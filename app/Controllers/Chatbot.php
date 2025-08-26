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
        'hi' => 'Hi! Ada yang bisa saya bantu?'
    ];

    // Daftar informasi default untuk pertanyaan yang tidak terjawab
    protected $defaultInformation = [
        'Saya tidak bisa menjawab pertanyaan anda, tetapi pertanyaan ini akan saya kirim ke admin. Mohon ditunggu admin akan menjawab segera mungkin.',
    ];

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    // Konfigurasi Gemini AI
    protected $geminiApiKey;
    protected $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';

    public function __construct()
    {
        // Inisialisasi model pertanyaan yang belum dijawab
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel = new AnsweredQuestionsModel();
        
        // Ambil API key dari environment variable atau config
        $this->geminiApiKey = getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE';
        
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

        // LANGKAH 3: Cek kecocokan berdasarkan similar_text
        $similarMatch = $this->findSimilarMatch($input);
        if ($similarMatch && $similarMatch['score'] > 80) {
            $tag = $similarMatch['tag']; // Tambahkan tag ke hasil similar_text
            // Simpan pertanyaan yang mirip
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $tag);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // LANGKAH 4: Jika skor kemiripan rendah, coba Gemini AI
        if (isset($similarMatch) && $similarMatch['score'] < 50) {
            // Coba gunakan Gemini AI sebagai fallback
            $geminiAnswer = $this->getGeminiAnswer($input);
            if ($geminiAnswer) {
                // Simpan jawaban dari Gemini AI
                $this->saveToAnsweredQuestions($input, $geminiAnswer, 'gemini_ai');
                return $this->response->setJSON(['message' => $geminiAnswer]);
            } else {
                // Jika Gemini AI gagal, simpan ke unanswered questions
                $this->saveUnansweredQuestion($input);
                $answer = $this->getInformationForUnknownQuery($input);
                return $this->response->setJSON(['message' => $answer]);
            }
        }

        // LANGKAH 5: Gunakan Naive Bayes sebagai fallback terakhir jika kemiripan moderat
        $predictedTag = $this->predictTag($input);
        $tagConfidence = $this->calculateTagConfidence($input, $predictedTag);

        // Jika confidence rendah, coba Gemini AI dulu sebelum default
        if ($tagConfidence < 0.3) {
            // Coba gunakan Gemini AI sebagai fallback
            $geminiAnswer = $this->getGeminiAnswer($input);
            if ($geminiAnswer) {
                // Simpan jawaban dari Gemini AI
                $this->saveToAnsweredQuestions($input, $geminiAnswer, 'gemini_ai');
                $answer = $geminiAnswer;
            } else {
                // Jika Gemini AI gagal, simpan ke unanswered questions
                $this->saveUnansweredQuestion($input);
                $answer = $this->getInformationForUnknownQuery($input);
            }
        } else {
            $answer = $this->getAnswerByTag($predictedTag, $input);

            // Tambahkan log untuk debugging
            log_message('info', 'Jawaban ditemukan: ' . $answer);

            // Setelah mendapatkan jawaban, simpan ke answered_questions
            $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
        }

        // Jika jawaban adalah default, coba Gemini AI sebagai last resort
        if ($answer === "Maaf, saya belum mengerti maksud kamu.") {
            $geminiAnswer = $this->getGeminiAnswer($input);
            if ($geminiAnswer) {
                // Simpan jawaban dari Gemini AI
                $this->saveToAnsweredQuestions($input, $geminiAnswer, 'gemini_ai');
                $answer = $geminiAnswer;
            } else {
                $this->saveUnansweredQuestion($input);
                $answer = $this->getInformationForUnknownQuery($input);
            }
        }

        return $this->response->setJSON(['message' => $answer]);
    }

    // Fungsi baru untuk memanggil Gemini AI
    private function getGeminiAnswer($question)
    {
        try {
            // Pastikan API key tersedia
            if (empty($this->geminiApiKey) || $this->geminiApiKey === 'YOUR_GEMINI_API_KEY_HERE') {
                log_message('error', 'Gemini API key tidak tersedia');
                return null;
            }

            // Siapkan prompt untuk Gemini AI
            $prompt = "Jawab pertanyaan berikut dengan bahasa Indonesia yang baik dan sopan. Berikan jawaban yang informatif dan membantu: " . $question;

            // Data untuk request ke Gemini AI
            $requestData = [
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
                    'temperature' => 0.7,
                    'topP' => 0.8,
                    'topK' => 40,
                    'maxOutputTokens' => 1024
                ]
            ];

            // Inisialisasi cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->geminiApiUrl . '?key=' . $this->geminiApiKey,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($requestData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($response === false || $httpCode !== 200) {
                log_message('error', 'Gemini API request gagal. HTTP Code: ' . $httpCode);
                return null;
            }

            $responseData = json_decode($response, true);
            
            // Ekstrak jawaban dari response Gemini AI
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $answer = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
                
                // Tambahkan prefix untuk menandai bahwa ini jawaban dari AI
                $answer = "🤖 " . $answer;
                
                log_message('info', 'Gemini AI berhasil menjawab pertanyaan: ' . $question);
                return $answer;
            } else {
                log_message('error', 'Format response Gemini AI tidak sesuai: ' . $response);
                return null;
            }

        } catch (Exception $e) {
            log_message('error', 'Error saat memanggil Gemini AI: ' . $e->getMessage());
            return null;
        }
    }

    // Fungsi untuk memeriksa apakah pertanyaan sudah pernah dijawab oleh teknisi
    private function checkTechnicalAnswer($input)
    {
        $result = $this->unansweredQuestionModel->findSimilarQuestion($input);
        
        if ($result['match'] && $result['score'] > 70) {
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
            'tag' => $bestTag // Tambahkan tag ke hasil
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

    // Fungsi untuk mendapatkan jawaban berdasarkan tag
    private function getAnswerByTag($tag, $input)
    {
        if (!isset($this->tagAnswers[$tag])) {
            return "Maaf, saya belum mengerti maksud kamu.";
        }

        // Untuk pertanyaan pendek, pilih jawaban default
        if (str_word_count($input) <= 1) {
            return $this->tagAnswers[$tag][array_rand($this->tagAnswers[$tag])];
        }

        $bestScore = 0;
        $bestAnswer = null;
        $defaultAnswer = $this->tagAnswers[$tag][array_rand($this->tagAnswers[$tag])];

        foreach ($this->dataset as $data) {
            if ($data['tag'] === $tag) {
                similar_text($input, $data['question'], $percent);
                if ($percent > $bestScore) {
                    $bestScore = $percent;
                    $bestAnswer = $data['answer'];
                }
            }
        }

        $answer = ($bestScore > 30) ? $bestAnswer : $defaultAnswer;

        if (empty($answer)) {
            return $this->getInformationForUnknownQuery($input);
        }

        return $answer;
    }

    // Fungsi untuk menangani pertanyaan yang tidak ada dalam database
    private function getInformationForUnknownQuery($input)
    {
        $randomInfo = $this->defaultInformation[array_rand($this->defaultInformation)];
        return "Mohon maaf, " . $randomInfo;
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
                'tag' => $tag, // Update tag berdasarkan unanswered_questions
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

    // Fungsi lama yang tidak digunakan lagi - bisa dihapus atau dipertahankan untuk kompatibilitas
    public function moveAnsweredQuestionToAnotherTable($question, $answer, $tag)
    {
        // Arahkan ke fungsi baru
        return $this->saveToAnsweredQuestions($question, $answer, $tag);
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
