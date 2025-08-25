// Fungsi untuk menampilkan pertanyaan yang sering ditanya<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UnansweredQuestionModel;
use App\Models\AnsweredQuestionsModel;

class Chatbot extends Controller
{
    protected $dataset = [];
    protected $vocab = [];
    protected $tagCounts = [];
    protected $wordTagCounts = [];
    protected $tagAnswers = [];

    // Daftar kata kunci untuk respons khusus
    protected $keywordResponses = [
        'pagi' => 'Selamat pagi! Ada yang bisa saya bantu?',
        'siang' => 'Selamat siang! Ada yang bisa saya bantu?',
        'sore' => 'Selamat sore! Ada yang bisa saya bantu?',
        'malam' => 'Selamat malam! Ada yang bisa saya bantu?',
        'halo' => 'Halo! Ada yang bisa saya bantu?',
        'hai' => 'Hai! Ada yang bisa saya bantu hari ini?',
        'hi' => 'Hi! Ada yang bisa saya bantu?'
    ];

    // Respons default untuk pertanyaan yang tidak terjawab - DIPERKETAT
    protected $defaultResponse = 'Mohon maaf, Saya tidak bisa menjawab pertanyaan anda, tetapi pertanyaan ini akan saya kirim ke admin. Mohon ditunggu admin akan menjawab segera mungkin.';

    // Threshold yang diseimbangkan untuk menentukan apakah jawaban valid
    protected $EXACT_MATCH_THRESHOLD = 100;        // Harus persis sama
    protected $SIMILAR_MATCH_THRESHOLD = 80;       // Disesuaikan untuk pertanyaan mirip
    protected $TECHNICAL_MATCH_THRESHOLD =80;     // Threshold untuk jawaban teknis
    protected $CONFIDENCE_THRESHOLD = 0.3;         // Threshold confidence Naive Bayes
    protected $BEST_SCORE_THRESHOLD = 50;          // Threshold untuk similarity dalam tag

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    public function __construct()
    {
        // Inisialisasi model pertanyaan yang belum dijawab
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel = new AnsweredQuestionsModel();
        
        // Memuat dataset CSV
        $csvPath = FCPATH . 'dataset/chatbot_dataset.csv';

        if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
            fgetcsv($handle); // Lewati header

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 3) {
                    $question = strtolower(trim($data[0])); // 0: question
                    $answer   = trim($data[1]);             // 1: answer
                    $tag      = trim($data[2]);             // 2: tag

                    $this->dataset[] = compact('question', 'answer', 'tag');

                    // Tokenisasi kata dari pertanyaan
                    $words = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $question));
                    $this->tagCounts[$tag] = ($this->tagCounts[$tag] ?? 0) + 1;

                    foreach ($words as $word) {
                        if ($word === '') continue;
                        $this->vocab[$word] = true;
                        $this->wordTagCounts[$tag][$word] = ($this->wordTagCounts[$tag][$word] ?? 0) + 1;
                    }

                    // Simpan jawaban berdasarkan tag
                    $this->tagAnswers[$tag][] = $answer;
                }
            }
            fclose($handle);
        }
    }

    public function index()
    {
        return view('chatbot');
    }

    public function getResponse()
    {
        $input = strtolower(trim($this->request->getPost('message')));

        if (!$input) {
            return $this->response->setJSON(['message' => 'Pertanyaan kosong.']);
        }

        // Cek jika ada kecocokan dengan kata kunci tertentu (greeting)
        foreach ($this->keywordResponses as $keyword => $response) {
            if ($input === $keyword || strpos($input, $keyword) !== false) {
                // Simpan juga respons kata kunci ke tabel answered_questions
                $this->saveToAnsweredQuestions($input, $response, 'greeting');
                return $this->response->setJSON(['message' => $response]);
            }
        }

        // LANGKAH 1: Cek apakah pertanyaan ini sudah pernah dijawab oleh teknisi
        $technicalAnswer = $this->checkTechnicalAnswer($input);
        if ($technicalAnswer) {
            // Simpan pertanyaan teknis yang sudah dijawab
            $this->saveToAnsweredQuestions($input, $technicalAnswer, 'technical');
            return $this->response->setJSON(['message' => $technicalAnswer]);
        }

        // LANGKAH 2: Cek kecocokan persis di dataset
        $exactMatch = $this->findExactMatch($input);
        if ($exactMatch) {
            $tag = $this->findTagFromExactMatch($input);
            // Simpan pertanyaan yang cocok persis
            $this->saveToAnsweredQuestions($input, $exactMatch, $tag);
            return $this->response->setJSON(['message' => $exactMatch]);
        }

        // LANGKAH 3: Cek kecocokan berdasarkan similar_text dengan threshold lebih ketat
        $similarMatch = $this->findSimilarMatch($input);
        if ($similarMatch && $similarMatch['score'] >= $this->SIMILAR_MATCH_THRESHOLD) {
            $tag = $similarMatch['tag'];
            // Simpan pertanyaan yang mirip
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $tag);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // LANGKAH 4: Untuk pertanyaan yang tidak memenuhi threshold similarity tinggi,
        // gunakan Naive Bayes dengan confidence threshold yang wajar
        $predictedTag = $this->predictTag($input);
        $tagConfidence = $this->calculateTagConfidence($input, $predictedTag);

        // Jika confidence memadai, berikan jawaban
        if ($tagConfidence >= $this->CONFIDENCE_THRESHOLD) {
            $answer = $this->getAnswerByTag($predictedTag, $input);
            
            // Jika jawaban valid (bukan default), simpan dan return
            if ($answer !== $this->defaultResponse) {
                $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
                return $this->response->setJSON(['message' => $answer]);
            }
        }

        // LANGKAH 5: Jika semua langkah gagal, gunakan respons default
        $this->saveUnansweredQuestion($input);
        return $this->response->setJSON(['message' => $this->defaultResponse]);
    }

    // Fungsi untuk memeriksa apakah pertanyaan sudah pernah dijawab oleh teknisi
    private function checkTechnicalAnswer($input)
    {
        $result = $this->unansweredQuestionModel->findSimilarQuestion($input);
        
        // Threshold disesuaikan untuk lebih fleksibel
        if ($result['match'] && $result['score'] >= $this->TECHNICAL_MATCH_THRESHOLD) {
            return $result['match']['answer'];
        }
        
        return null;
    }

    // Fungsi mencari kecocokan persis di dataset
    private function findExactMatch($input)
    {
        foreach ($this->dataset as $data) {
            if (strtolower(trim($data['question'])) === $input) {
                return $data['answer'];
            }
        }
        return null;
    }

    // Fungsi untuk menemukan tag dari pertanyaan yang cocok persis
    private function findTagFromExactMatch($input)
    {
        foreach ($this->dataset as $data) {
            if (strtolower(trim($data['question'])) === $input) {
                return $data['tag'];
            }
        }
        return 'unknown';
    }

    // Fungsi mencari kecocokan mirip dengan menggunakan similar_text
    private function findSimilarMatch($input)
    {
        $bestScore = 0;
        $bestAnswer = null;
        $bestTag = 'unknown';

        foreach ($this->dataset as $data) {
            similar_text(strtolower($input), strtolower($data['question']), $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $bestAnswer = $data['answer'];
                $bestTag = $data['tag'];
            }
        }

        return [
            'score' => $bestScore,
            'answer' => $bestAnswer,
            'tag' => $bestTag
        ];
    }

    // Fungsi untuk menyimpan pertanyaan yang tidak terjawab ke database
    private function saveUnansweredQuestion($question)
    {
        // Cek apakah pertanyaan sudah ada di database
        $existingQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->first();
            
        // Jika pertanyaan belum ada di database, simpan
        if (!$existingQuestion) {
            $this->unansweredQuestionModel->save([
                'question' => $question,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            log_message('info', 'Pertanyaan baru disimpan ke database: ' . $question);
        } else {
            // Jika sudah ada, update waktu terakhir ditanya
            $this->unansweredQuestionModel->update($existingQuestion['id'], [
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Fungsi untuk menyimpan pertanyaan dan jawaban ke tabel answered_questions
    private function saveToAnsweredQuestions($question, $answer, $tag)
    {
        // Cek apakah pertanyaan sudah ada di unanswered_questions
        $unansweredQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->first();
            
        // Prioritaskan tag dari unanswered_questions jika sudah ditentukan oleh admin
        if ($unansweredQuestion && !empty($unansweredQuestion['tag'])) {
            // Gunakan tag yang sudah ditetapkan admin
            $tag = $unansweredQuestion['tag'];
        }
        
        // Pastikan tag tidak kosong
        if (empty($tag)) {
            $tag = 'general';
        }
        
        // Cek apakah pertanyaan sudah ada di answered_questions
        $existingQuestion = $this->answeredQuestionModel
            ->where('question', $question)
            ->first();
            
        if ($existingQuestion) {
            // Update pertanyaan yang sudah ada, termasuk tag
            $this->answeredQuestionModel->update($existingQuestion['id'], [
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => $existingQuestion['frequency'] + 1,
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Simpan pertanyaan baru dengan tag dari unanswered_questions
            $this->answeredQuestionModel->save([
                'question' => $question,
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Update status pertanyaan di unanswered_questions
        if ($unansweredQuestion) {
            $this->unansweredQuestionModel->update($unansweredQuestion['id'], [
                'status' => 'answered',
                'answer' => $answer
            ]);
        }
    }

    // Fungsi untuk memprediksi tag dari pertanyaan menggunakan Naive Bayes
    private function predictTag($input)
    {
        $inputWords = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $input));
        $totalDocs = array_sum($this->tagCounts);
        $vocabSize = count($this->vocab);

        $scores = [];

        foreach ($this->tagCounts as $tag => $tagTotal) {
            $logProb = log($tagTotal / $totalDocs);

            foreach ($inputWords as $word) {
                if ($word === '') continue;

                $wordCount = $this->wordTagCounts[$tag][$word] ?? 0;
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag] ?? []);

                // Laplace smoothing
                $logProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
            }

            $scores[$tag] = $logProb;
        }

        arsort($scores);
        return array_key_first($scores);
    }

    // Fungsi untuk menghitung confidence dari prediksi tag
    private function calculateTagConfidence($input, $predictedTag)
    {
        $inputWords = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $input));
        $totalDocs = array_sum($this->tagCounts);
        $vocabSize = count($this->vocab);

        $scores = [];

        foreach ($this->tagCounts as $tag => $tagTotal) {
            // Probabilitas awal P(tag)
            $tagProb = log($tagTotal / $totalDocs);

            // Likelihood untuk setiap kata
            $wordProb = 0;
            foreach ($inputWords as $word) {
                if ($word === '') continue;

                $wordCount = $this->wordTagCounts[$tag][$word] ?? 0;
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag] ?? []);

                // Likelihood dengan Laplace smoothing
                $wordProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
            }

            $scores[$tag] = $tagProb + $wordProb;
        }

        // Normalisasi menggunakan softmax untuk mendapatkan probabilitas
        $maxScore = max($scores);
        $expScores = [];
        $totalExp = 0;

        foreach ($scores as $tag => $score) {
            $expScores[$tag] = exp($score - $maxScore);
            $totalExp += $expScores[$tag];
        }

        // Confidence adalah probabilitas dari tag yang diprediksi
        return isset($expScores[$predictedTag]) ? $expScores[$predictedTag] / $totalExp : 0;
    }

    // Fungsi untuk mendapatkan jawaban berdasarkan tag tanpa random
    private function getAnswerByTag($tag, $input)
    {
        if (!isset($this->tagAnswers[$tag]) || empty($this->tagAnswers[$tag])) {
            return $this->defaultResponse;
        }

        // Cari jawaban dengan similarity terbaik dalam tag yang sama
        $bestScore = 0;
        $bestAnswer = null;

        foreach ($this->dataset as $data) {
            if ($data['tag'] === $tag) {
                similar_text(strtolower($input), strtolower($data['question']), $percent);
                if ($percent > $bestScore) {
                    $bestScore = $percent;
                    $bestAnswer = $data['answer'];
                }
            }
        }

        // Hanya gunakan jawaban jika similarity cukup tinggi
        if ($bestScore >= $this->BEST_SCORE_THRESHOLD) {
            return $bestAnswer;
        }

        // Jika similarity tidak memadai, gunakan respons default
        return $this->defaultResponse;
    }
    public function showFrequentQuestions($tag = null)
    {
        $frequentQuestions = $this->answeredQuestionModel->getQuestionsByTag($tag);

        // Kirim data ke view
        return view('frequent_questions', [
            'frequentQuestions' => $frequentQuestions,
            'selectedTag' => $tag
        ]);
    }
}
