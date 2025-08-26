<?php

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
        'hi' => 'Hi! Ada yang bisa saya bantu?',
        'selamat pagi' => 'Selamat pagi! Ada yang bisa saya bantu?',
        'selamat siang' => 'Selamat siang! Ada yang bisa saya bantu?',
        'selamat sore' => 'Selamat sore! Ada yang bisa saya bantu?',
        'selamat malam' => 'Selamat malam! Ada yang bisa saya bantu?'
    ];

    // Pesan default untuk pertanyaan yang tidak dapat dijawab
    protected $defaultMessage = 'Maaf, saya tidak dapat menemukan jawaban untuk pertanyaan Anda. Pertanyaan ini akan diteruskan ke admin untuk dijawab. Mohon ditunggu, admin akan merespons segera.';

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    public function __construct()
    {
        // Inisialisasi model
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel = new AnsweredQuestionsModel();
        
        // Memuat dataset CSV
        $this->loadDataset();
    }

    private function loadDataset()
    {
        $csvPath = FCPATH . 'dataset/chatbot_dataset.csv';

        if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
            fgetcsv($handle); // Lewati header

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 3) {
                    $question = strtolower(trim($data[0]));
                    $answer   = trim($data[1]);
                    $tag      = trim($data[2]);

                    // Skip jika ada data kosong
                    if (empty($question) || empty($answer) || empty($tag)) {
                        continue;
                    }

                    $this->dataset[] = compact('question', 'answer', 'tag');

                    // Tokenisasi kata dari pertanyaan
                    $words = $this->tokenizeText($question);
                    $this->tagCounts[$tag] = ($this->tagCounts[$tag] ?? 0) + 1;

                    foreach ($words as $word) {
                        if (strlen($word) > 2) { // Hanya ambil kata dengan panjang > 2 karakter
                            $this->vocab[$word] = true;
                            $this->wordTagCounts[$tag][$word] = ($this->wordTagCounts[$tag][$word] ?? 0) + 1;
                        }
                    }

                    // Simpan jawaban berdasarkan tag
                    $this->tagAnswers[$tag][] = $answer;
                }
            }
            fclose($handle);
        }
    }

    private function tokenizeText($text)
    {
        // Bersihkan teks dan tokenisasi
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9 ]/', ' ', $text);
        $words = preg_split('/\s+/', $text);
        return array_filter($words, function($word) {
            return !empty($word) && strlen($word) > 1;
        });
    }

    public function index()
    {
        return view('chatbot');
    }

    public function getResponse()
    {
        $input = trim($this->request->getPost('message'));

        if (!$input) {
            return $this->response->setJSON(['message' => 'Mohon masukkan pertanyaan Anda.']);
        }

        $inputLower = strtolower($input);

        // LANGKAH 1: Cek greeting/keyword responses
        $keywordResponse = $this->checkKeywordResponse($inputLower);
        if ($keywordResponse) {
            $this->saveToAnsweredQuestions($input, $keywordResponse, 'greeting');
            return $this->response->setJSON(['message' => $keywordResponse]);
        }

        // LANGKAH 2: Cek jawaban teknis dari admin (database unanswered_questions yang sudah dijawab)
        $technicalAnswer = $this->checkTechnicalAnswer($inputLower);
        if ($technicalAnswer) {
            $this->saveToAnsweredQuestions($input, $technicalAnswer, 'technical');
            return $this->response->setJSON(['message' => $technicalAnswer]);
        }

        // LANGKAH 3: Cek kecocokan persis di dataset
        $exactMatch = $this->findExactMatch($inputLower);
        if ($exactMatch) {
            $this->saveToAnsweredQuestions($input, $exactMatch['answer'], $exactMatch['tag']);
            return $this->response->setJSON(['message' => $exactMatch['answer']]);
        }

        // LANGKAH 4: Cek kecocokan dengan similarity tinggi (>85%)
        $similarMatch = $this->findSimilarMatch($inputLower);
        if ($similarMatch && $similarMatch['score'] > 85) {
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $similarMatch['tag']);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // LANGKAH 5: Cek dengan Naive Bayes jika similarity moderat (50-85%)
        if ($similarMatch && $similarMatch['score'] >= 50 && $similarMatch['score'] <= 85) {
            $predictedTag = $this->predictTag($inputLower);
            $tagConfidence = $this->calculateTagConfidence($inputLower, $predictedTag);
            
            // Jika confidence cukup tinggi, gunakan jawaban dari tag
            if ($tagConfidence > 0.4) {
                $answer = $this->getAnswerByTag($predictedTag, $inputLower);
                if ($answer !== $this->defaultMessage) {
                    $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
                    return $this->response->setJSON(['message' => $answer]);
                }
            }
        }

        // LANGKAH 6: Jika semua metode gagal, kirim ke admin
        $this->saveUnansweredQuestion($input);
        return $this->response->setJSON(['message' => $this->defaultMessage]);
    }

    private function checkKeywordResponse($input)
    {
        // Cek exact match dulu
        if (isset($this->keywordResponses[$input])) {
            return $this->keywordResponses[$input];
        }

        // Cek partial match
        foreach ($this->keywordResponses as $keyword => $response) {
            if (strpos($input, $keyword) !== false) {
                return $response;
            }
        }

        return null;
    }

    private function checkTechnicalAnswer($input)
    {
        $result = $this->unansweredQuestionModel->findSimilarQuestion($input);
        
        if ($result['match'] && $result['score'] > 80 && !empty($result['match']['answer'])) {
            return $result['match']['answer'];
        }
        
        return null;
    }

    private function findExactMatch($input)
    {
        foreach ($this->dataset as $data) {
            if ($data['question'] === $input) {
                return [
                    'answer' => $data['answer'],
                    'tag' => $data['tag']
                ];
            }
        }
        return null;
    }

    private function findSimilarMatch($input)
    {
        $bestScore = 0;
        $bestAnswer = null;
        $bestTag = null;

        foreach ($this->dataset as $data) {
            similar_text($input, $data['question'], $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $bestAnswer = $data['answer'];
                $bestTag = $data['tag'];
            }
        }

        if ($bestScore > 0) {
            return [
                'score' => $bestScore,
                'answer' => $bestAnswer,
                'tag' => $bestTag
            ];
        }

        return null;
    }

    private function predictTag($input)
    {
        if (empty($this->tagCounts)) {
            return 'general';
        }

        $inputWords = $this->tokenizeText($input);
        if (empty($inputWords)) {
            return 'general';
        }

        $totalDocs = array_sum($this->tagCounts);
        $vocabSize = count($this->vocab);

        $scores = [];

        foreach ($this->tagCounts as $tag => $tagTotal) {
            // Prior probability
            $logProb = log($tagTotal / $totalDocs);

            // Likelihood untuk setiap kata
            foreach ($inputWords as $word) {
                $wordCount = $this->wordTagCounts[$tag][$word] ?? 0;
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag] ?? []);

                // Laplace smoothing
                if ($totalWordsInTag > 0) {
                    $logProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
                }
            }

            $scores[$tag] = $logProb;
        }

        if (empty($scores)) {
            return 'general';
        }

        arsort($scores);
        return array_key_first($scores);
    }

    private function calculateTagConfidence($input, $predictedTag)
    {
        if (empty($this->tagCounts) || !isset($this->tagCounts[$predictedTag])) {
            return 0;
        }

        $inputWords = $this->tokenizeText($input);
        if (empty($inputWords)) {
            return 0;
        }

        $totalDocs = array_sum($this->tagCounts);
        $vocabSize = count($this->vocab);

        $scores = [];

        foreach ($this->tagCounts as $tag => $tagTotal) {
            $logProb = log($tagTotal / $totalDocs);

            foreach ($inputWords as $word) {
                $wordCount = $this->wordTagCounts[$tag][$word] ?? 0;
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag] ?? []);

                if ($totalWordsInTag > 0) {
                    $logProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
                }
            }

            $scores[$tag] = $logProb;
        }

        // Convert log probabilities to actual probabilities
        $maxScore = max($scores);
        $expScores = [];
        $totalExp = 0;

        foreach ($scores as $tag => $score) {
            $expScores[$tag] = exp($score - $maxScore);
            $totalExp += $expScores[$tag];
        }

        return isset($expScores[$predictedTag]) ? $expScores[$predictedTag] / $totalExp : 0;
    }

    private function getAnswerByTag($tag, $input)
    {
        if (!isset($this->tagAnswers[$tag]) || empty($this->tagAnswers[$tag])) {
            return $this->defaultMessage;
        }

        // Cari jawaban terbaik dalam tag berdasarkan similarity
        $bestScore = 0;
        $bestAnswer = null;

        foreach ($this->dataset as $data) {
            if ($data['tag'] === $tag) {
                similar_text($input, $data['question'], $percent);
                if ($percent > $bestScore) {
                    $bestScore = $percent;
                    $bestAnswer = $data['answer'];
                }
            }
        }

        // Jika similarity rendah, ambil jawaban random dari tag
        if ($bestScore < 30) {
            return $this->tagAnswers[$tag][array_rand($this->tagAnswers[$tag])];
        }

        return $bestAnswer ?? $this->defaultMessage;
    }

    private function saveUnansweredQuestion($question)
    {
        // Cek apakah pertanyaan sudah ada di database
        $existingQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->where('status', 'pending') // Hanya cek yang pending
            ->first();
            
        // Jika pertanyaan belum ada, simpan
        if (!$existingQuestion) {
            $data = [
                'question' => $question,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->unansweredQuestionModel->save($data);
            log_message('info', 'Pertanyaan baru disimpan ke database: ' . $question);
        } else {
            // Update frequency jika sudah ada
            $this->unansweredQuestionModel->update($existingQuestion['id'], [
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private function saveToAnsweredQuestions($question, $answer, $tag)
    {
        // Validasi input
        if (empty($question) || empty($answer)) {
            return;
        }

        // Pastikan tag tidak kosong
        if (empty($tag)) {
            $tag = 'general';
        }

        // Cek apakah sudah ada di answered_questions
        $existingQuestion = $this->answeredQuestionModel
            ->where('question', $question)
            ->first();
            
        if ($existingQuestion) {
            // Update yang sudah ada
            $this->answeredQuestionModel->update($existingQuestion['id'], [
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => $existingQuestion['frequency'] + 1,
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Simpan baru
            $this->answeredQuestionModel->save([
                'question' => $question,
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Update status di unanswered_questions jika ada
        $unansweredQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->where('status', 'pending')
            ->first();
            
        if ($unansweredQuestion) {
            $this->unansweredQuestionModel->update($unansweredQuestion['id'], [
                'status' => 'answered',
                'answer' => $answer,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function showFrequentQuestions($tag = null)
    {
        $frequentQuestions = $this->answeredQuestionModel->getQuestionsByTag($tag);

        return view('frequent_questions', [
            'frequentQuestions' => $frequentQuestions,
            'selectedTag' => $tag
        ]);
    }
}
