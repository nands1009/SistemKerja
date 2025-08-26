<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UnansweredQuestionModel;
use App\Models\AnsweredQuestionsModel;
use Exception;

class Chatbot extends Controller
{
    protected $dataset = [];
    protected $vocab = [];
    protected $tagCounts = [];
    protected $wordTagCounts = [];
    protected $tagAnswers = [];
    protected $geminiApiKey;

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

    // Daftar informasi default untuk pertanyaan yang tidak terjawab
    protected $defaultInformation = [
        'Pertanyaan Anda telah kami terima dan akan segera ditangani oleh tim kami. Terima kasih atas kesabarannya.',
        'Tim customer service kami akan segera merespons pertanyaan Anda. Mohon tunggu sebentar.',
        'Pertanyaan Anda sedang diproses. Kami akan memberikan jawaban secepatnya.'
    ];

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    public function __construct()
    {
        // Inisialisasi API Key Gemini dari environment atau config
        $this->geminiApiKey = getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE';
        
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

        // Cek jika ada kecocokan dengan kata kunci tertentu
        foreach ($this->keywordResponses as $keyword => $response) {
            if ($input === $keyword || strpos($input, $keyword) !== false) {
                // Simpan juga respons kata kunci ke tabel answered_questions
                $this->saveToAnsweredQuestions($input, $response, 'greeting');
                return $this->response->setJSON(['message' => $response]);
            }
        }

        // LANGKAH 1: Cek apakah pertanyaan ini sudah pernah dijawab oleh teknisi/admin
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
            $this->saveToAnsweredQuestions($input, $exactMatch, $tag);
            return $this->response->setJSON(['message' => $exactMatch]);
        }

        // LANGKAH 3: Cek kecocokan berdasarkan similar_text dengan threshold yang lebih ketat
        $similarMatch = $this->findSimilarMatch($input);
        if ($similarMatch && $similarMatch['score'] > 85) { // Threshold dinaikkan menjadi 85%
            $tag = $similarMatch['tag'];
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $tag);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // LANGKAH 4: Gunakan Naive Bayes untuk prediksi tag
        $predictedTag = $this->predictTag($input);
        $tagConfidence = $this->calculateTagConfidence($input, $predictedTag);

        // Jika confidence cukup tinggi (> 0.4), gunakan jawaban dari dataset
        if ($tagConfidence > 0.4) {
            $answer = $this->getAnswerByTag($predictedTag, $input);
            
            // Pastikan jawaban bukan default/error message
            if ($answer !== "Maaf, saya belum mengerti maksud kamu." && !empty($answer)) {
                $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
                return $this->response->setJSON(['message' => $answer]);
            }
        }

        // LANGKAH 5: Jika confidence rendah atau similarity rendah, gunakan Gemini AI dengan konteks dataset
        if ($tagConfidence < 0.4 || (isset($similarMatch) && $similarMatch['score'] < 85)) {
            $geminiAnswer = $this->getGeminiResponseFromDataset($input);
            if ($geminiAnswer && $geminiAnswer !== 'error' && !empty(trim($geminiAnswer))) {
                $this->saveToAnsweredQuestions($input, $geminiAnswer, 'ai_generated');
                return $this->response->setJSON(['message' => $geminiAnswer]);
            }
        }

        // LANGKAH 6: Jika semua gagal, simpan ke unanswered questions dan kirim ke admin
        $this->saveUnansweredQuestion($input);
        $answer = $this->getInformationForUnknownQuery($input);
        return $this->response->setJSON(['message' => $answer]);
    }

    // Fungsi baru untuk mendapatkan respons dari Gemini AI berdasarkan dataset internal
    private function getGeminiResponseFromDataset($question)
    {
        try {
            // Buat konteks dari dataset internal
            $datasetContext = $this->buildDatasetContext();
            
            // URL API Gemini
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=" . $this->geminiApiKey;
            
            // Buat prompt yang membatasi Gemini untuk hanya menggunakan dataset internal
            $prompt = "Anda adalah asisten customer service. PENTING: Anda HANYA boleh menjawab berdasarkan informasi yang tersedia dalam dataset berikut ini. Jika pertanyaan tidak dapat dijawab berdasarkan dataset ini, jawab dengan 'DATA_NOT_FOUND'.

Dataset yang tersedia:
" . $datasetContext . "

Pertanyaan: " . $question . "

Jawaban (gunakan HANYA informasi dari dataset di atas, dalam bahasa Indonesia yang sopan dan informatif):";
            
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3, // Lebih rendah untuk jawaban yang lebih konsisten
                    'maxOutputTokens' => 150,
                    'topP' => 0.8,
                    'topK' => 40
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                log_message('error', 'Gemini API returned HTTP ' . $httpCode . ': ' . $response);
                return 'error';
            }

            $result = json_decode($response, true);
            
            // Ekstrak jawaban dari respons Gemini
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $answer = trim($result['candidates'][0]['content']['parts'][0]['text']);
                
                // Jika Gemini mengatakan tidak dapat menemukan data, return error
                if (strpos($answer, 'DATA_NOT_FOUND') !== false || 
                    strpos(strtolower($answer), 'tidak dapat menjawab') !== false ||
                    strpos(strtolower($answer), 'informasi tidak tersedia') !== false) {
                    return 'error';
                }
                
                log_message('info', 'Gemini AI response from dataset: ' . $answer);
                return $answer;
            } else {
                log_message('error', 'Unexpected Gemini API response format: ' . $response);
                return 'error';
            }

        } catch (Exception $e) {
            log_message('error', 'Error calling Gemini API: ' . $e->getMessage());
            return 'error';
        }
    }

    // Fungsi untuk membangun konteks dataset
    private function buildDatasetContext()
    {
        $context = "";
        $addedQuestions = []; // Untuk menghindari duplikasi
        
        // Ambil sample dari dataset untuk konteks (maksimal 50 entri untuk menghindari token limit)
        $sampleCount = 0;
        foreach ($this->dataset as $data) {
            if ($sampleCount >= 50) break;
            
            $questionKey = strtolower(trim($data['question']));
            if (!in_array($questionKey, $addedQuestions)) {
                $context .= "Q: " . $data['question'] . "\n";
                $context .= "A: " . $data['answer'] . "\n";
                $context .= "Tag: " . $data['tag'] . "\n\n";
                $addedQuestions[] = $questionKey;
                $sampleCount++;
            }
        }
        
        return $context;
    }

    // Fungsi untuk memeriksa apakah pertanyaan sudah pernah dijawab oleh teknisi/admin
    private function checkTechnicalAnswer($input)
    {
        // Cek di answered_questions terlebih dahulu
        $answeredQuestion = $this->answeredQuestionModel->findSimilarQuestion($input);
        if ($answeredQuestion['match'] && $answeredQuestion['score'] > 80) {
            return $answeredQuestion['match']['answer'];
        }

        // Cek di unanswered_questions yang sudah dijawab admin
        $result = $this->unansweredQuestionModel->findSimilarQuestion($input);
        if ($result['match'] && $result['score'] > 80 && $result['match']['status'] === 'answered') {
            return $result['match']['answer'];
        }
        
        return null;
    }

    private function calculateTagConfidence($input, $predictedTag)
    {
        $inputWords = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $input));
        $totalDocs = array_sum($this->tagCounts);
        $vocabSize = count($this->vocab);

        $scores = [];
        $totalScore = 0;

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
            $totalScore += exp($scores[$tag]);
        }

        // Confidence adalah probabilitas relatif dari tag yang diprediksi
        return isset($scores[$predictedTag]) ? exp($scores[$predictedTag]) / $totalScore : 0;
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

    // Fungsi untuk memprediksi tag dari pertanyaan
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
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag]);

                // Laplace smoothing
                $logProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
            }

            $scores[$tag] = $logProb;
        }

        arsort($scores);
        return array_key_first($scores);
    }

    // Fungsi untuk mendapatkan jawaban berdasarkan tag dengan similarity yang lebih ketat
    private function getAnswerByTag($tag, $input)
    {
        if (!isset($this->tagAnswers[$tag])) {
            return "Maaf, saya belum mengerti maksud kamu.";
        }

        $bestScore = 0;
        $bestAnswer = null;
        $threshold = 60; // Threshold yang lebih tinggi

        // Cari jawaban yang paling mirip dalam tag yang sama
        foreach ($this->dataset as $data) {
            if ($data['tag'] === $tag) {
                similar_text($input, $data['question'], $percent);
                if ($percent > $bestScore && $percent > $threshold) {
                    $bestScore = $percent;
                    $bestAnswer = $data['answer'];
                }
            }
        }

        // Jika tidak ada yang memenuhi threshold, kembalikan null agar bisa dicoba dengan Gemini
        if ($bestScore < $threshold) {
            return null;
        }

        return $bestAnswer ?: "Maaf, saya belum mengerti maksud kamu.";
    }

    // Fungsi untuk menangani pertanyaan yang tidak ada dalam database
    private function getInformationForUnknownQuery($input)
    {
        $randomInfo = $this->defaultInformation[array_rand($this->defaultInformation)];
        return $randomInfo . " Pertanyaan Anda: \"" . $input . "\" telah diteruskan ke tim customer service kami.";
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
                'priority' => 'normal', // Tambahkan priority jika ada field ini
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            log_message('info', 'Pertanyaan baru disimpan ke database: ' . $question);
            
            // Optional: Kirim notifikasi ke admin (bisa ditambahkan sesuai kebutuhan)
            $this->notifyAdmin($question);
        } else {
            // Update frequency jika pertanyaan sudah ada
            $this->unansweredQuestionModel->update($existingQuestion['id'], [
                'frequency' => ($existingQuestion['frequency'] ?? 1) + 1,
                'last_asked_at' => date('Y-m-d H:i:s')
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
            // Update pertanyaan yang sudah ada
            $this->answeredQuestionModel->update($existingQuestion['id'], [
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => $existingQuestion['frequency'] + 1,
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Simpan pertanyaan baru
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
                'answer' => $answer,
                'answered_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Fungsi untuk mengirim notifikasi ke admin (optional)
    private function notifyAdmin($question)
    {
        // Implementasi notifikasi admin bisa berupa:
        // - Email
        // - Push notification
        // - Webhook ke sistem admin
        // - Log khusus untuk admin dashboard
        
        log_message('info', 'ADMIN NOTIFICATION: New unanswered question - ' . $question);
        
        // Contoh: Bisa ditambahkan email notification
        // $this->sendEmailToAdmin($question);
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
