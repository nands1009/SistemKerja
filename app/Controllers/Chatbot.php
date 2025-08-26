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

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    // Threshold untuk menentukan kapan menggunakan Gemini AI
    private $EXACT_MATCH_THRESHOLD = 95;     // Kecocokan hampir sempurna
    private $SIMILAR_MATCH_THRESHOLD = 75;   // Kecocokan cukup baik
    private $LOW_CONFIDENCE_THRESHOLD = 50;  // Confidence rendah, gunakan AI
    private $TAG_CONFIDENCE_THRESHOLD = 0.4; // Minimum confidence untuk prediksi tag

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

        log_message('info', 'Input pertanyaan: ' . $input);

        // Cek jika ada kecocokan dengan kata kunci tertentu
        foreach ($this->keywordResponses as $keyword => $response) {
            if ($input === $keyword || strpos($input, $keyword) !== false) {
                $this->saveToAnsweredQuestions($input, $response, 'greeting');
                return $this->response->setJSON(['message' => $response]);
            }
        }

        // LANGKAH 1: Cek apakah pertanyaan ini sudah pernah dijawab oleh teknisi
        $technicalAnswer = $this->checkTechnicalAnswer($input);
        if ($technicalAnswer) {
            log_message('info', 'Menggunakan jawaban teknis dari database');
            $this->saveToAnsweredQuestions($input, $technicalAnswer, 'technical');
            return $this->response->setJSON(['message' => $technicalAnswer]);
        }

        // LANGKAH 2: Cek kecocokan persis di dataset
        $exactMatch = $this->findExactMatch($input);
        if ($exactMatch) {
            log_message('info', 'Ditemukan kecocokan persis di dataset');
            $tag = $this->findTagFromExactMatch($input);
            $this->saveToAnsweredQuestions($input, $exactMatch, $tag);
            return $this->response->setJSON(['message' => $exactMatch]);
        }

        // LANGKAH 3: Cek kecocokan berdasarkan similarity dengan algoritma yang diperbaiki
        $similarMatch = $this->findSimilarMatch($input);
        
        log_message('info', 'Similarity score: ' . $similarMatch['score']);
        
        // Jika skor similarity tinggi, gunakan jawaban dari dataset
        if ($similarMatch && $similarMatch['score'] >= $this->SIMILAR_MATCH_THRESHOLD) {
            log_message('info', 'Menggunakan jawaban dari similarity matching');
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $similarMatch['tag']);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // LANGKAH 4: Jika similarity rendah, langsung gunakan Gemini AI
        if (!$similarMatch || $similarMatch['score'] < $this->LOW_CONFIDENCE_THRESHOLD) {
            log_message('info', 'Similarity score rendah, menggunakan Gemini AI');
            $geminiAnswer = $this->getGeminiResponse($input);
            
            if ($geminiAnswer && $geminiAnswer !== 'error' && !empty(trim($geminiAnswer))) {
                // Validasi jawaban Gemini sebelum disimpan
                if ($this->isValidGeminiResponse($geminiAnswer)) {
                    log_message('info', 'Berhasil mendapat jawaban dari Gemini AI');
                    $this->saveToAnsweredQuestions($input, $geminiAnswer, 'ai_generated');
                    return $this->response->setJSON(['message' => $geminiAnswer]);
                }
            }
            
            // Jika Gemini gagal, simpan ke unanswered questions
            log_message('info', 'Gemini AI gagal, menyimpan ke unanswered questions');
            $this->saveUnansweredQuestion($input);
            $defaultResponse = $this->getInformationForUnknownQuery($input);
            return $this->response->setJSON(['message' => $defaultResponse]);
        }

        // LANGKAH 5: Gunakan Naive Bayes sebagai fallback terakhir
        $predictedTag = $this->predictTag($input);
        $tagConfidence = $this->calculateTagConfidence($input, $predictedTag);

        log_message('info', 'Tag prediction: ' . $predictedTag . ', Confidence: ' . $tagConfidence);

        // Jika confidence tag rendah, gunakan Gemini AI
        if ($tagConfidence < $this->TAG_CONFIDENCE_THRESHOLD) {
            log_message('info', 'Tag confidence rendah, menggunakan Gemini AI');
            $geminiAnswer = $this->getGeminiResponse($input);
            
            if ($geminiAnswer && $geminiAnswer !== 'error' && !empty(trim($geminiAnswer))) {
                if ($this->isValidGeminiResponse($geminiAnswer)) {
                    log_message('info', 'Berhasil mendapat jawaban dari Gemini AI (fallback)');
                    $this->saveToAnsweredQuestions($input, $geminiAnswer, 'ai_generated');
                    return $this->response->setJSON(['message' => $geminiAnswer]);
                }
            }
            
            // Jika Gemini gagal, simpan ke unanswered questions
            $this->saveUnansweredQuestion($input);
            $answer = $this->getInformationForUnknownQuery($input);
        } else {
            // Gunakan jawaban berdasarkan prediksi tag
            $answer = $this->getAnswerByTag($predictedTag, $input);
            
            // Jika jawaban default, coba Gemini AI sekali lagi
            if ($answer === "Maaf, saya belum mengerti maksud kamu." || 
                strpos($answer, "Mohon maaf") === 0) {
                
                log_message('info', 'Jawaban default detected, mencoba Gemini AI');
                $geminiAnswer = $this->getGeminiResponse($input);
                
                if ($geminiAnswer && $geminiAnswer !== 'error' && !empty(trim($geminiAnswer))) {
                    if ($this->isValidGeminiResponse($geminiAnswer)) {
                        log_message('info', 'Berhasil mendapat jawaban dari Gemini AI (replacement)');
                        $this->saveToAnsweredQuestions($input, $geminiAnswer, 'ai_generated');
                        $answer = $geminiAnswer;
                    }
                } else {
                    // Jika Gemini gagal, simpan ke unanswered questions
                    $this->saveUnansweredQuestion($input);
                }
            } else {
                // Simpan jawaban dari prediksi tag
                $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
            }
        }

        return $this->response->setJSON(['message' => $answer]);
    }

    // Fungsi untuk memvalidasi respons dari Gemini AI
    private function isValidGeminiResponse($response)
    {
        // Cek apakah respons tidak kosong dan tidak terlalu pendek
        if (empty($response) || strlen(trim($response)) < 10) {
            return false;
        }

        // Cek apakah respons tidak mengandung error message umum
        $errorPatterns = [
            'I cannot',
            'I am unable',
            'I don\'t understand',
            'Sorry, I can\'t',
            'error',
            'failed',
            'invalid'
        ];

        $lowerResponse = strtolower($response);
        foreach ($errorPatterns as $pattern) {
            if (strpos($lowerResponse, strtolower($pattern)) !== false) {
                return false;
            }
        }

        return true;
    }

    // Fungsi yang diperbaiki untuk mendapatkan respons dari Gemini AI
    private function getGeminiResponse($question)
    {
        try {
            // Validasi API key
            if ($this->geminiApiKey === 'YOUR_GEMINI_API_KEY_HERE' || empty($this->geminiApiKey)) {
                log_message('error', 'Gemini API key tidak valid atau kosong');
                return 'error';
            }

            // URL API Gemini
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=" . $this->geminiApiKey;
            
            // Buat context yang lebih spesifik untuk meningkatkan akurasi
            $context = $this->buildContextFromDataset();
            
            // Buat prompt yang lebih komprehensif
            $prompt = "Anda adalah asisten customer service yang sangat membantu dan informatif. " .
                     "Konteks bisnis: $context " .
                     "Pertanyaan dari pengguna: \"$question\" " .
                     "Berikan jawaban yang: " .
                     "1. Akurat dan informatif dalam bahasa Indonesia " .
                     "2. Sopan dan ramah " .
                     "3. Langsung to the point " .
                     "4. Tidak lebih dari 200 kata " .
                     "5. Jika tidak yakin, berikan saran atau alternatif yang membantu " .
                     "Hindari menyebutkan bahwa Anda adalah AI atau tidak memiliki akses ke sistem tertentu.";
            
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
                    'temperature' => 0.3,        // Lebih deterministik
                    'maxOutputTokens' => 300,    // Lebih panjang untuk jawaban lengkap
                    'topP' => 0.8,
                    'topK' => 40,
                    'candidateCount' => 1
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
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
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Debugging curl errors
            if ($curlError) {
                log_message('error', 'Curl error: ' . $curlError);
                return 'error';
            }

            if ($httpCode !== 200) {
                log_message('error', 'Gemini API returned HTTP ' . $httpCode . ': ' . $response);
                return 'error';
            }

            $result = json_decode($response, true);
            
            // Debug response structure
            log_message('debug', 'Gemini API raw response: ' . $response);
            
            // Ekstrak jawaban dari respons Gemini dengan error handling yang lebih baik
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $answer = trim($result['candidates'][0]['content']['parts'][0]['text']);
                
                // Bersihkan jawaban dari karakter yang tidak diinginkan
                $answer = $this->cleanGeminiResponse($answer);
                
                log_message('info', 'Gemini AI response berhasil: ' . substr($answer, 0, 100) . '...');
                
                return $answer;
            } else {
                // Cek apakah ada error dalam response
                if (isset($result['error'])) {
                    log_message('error', 'Gemini API error: ' . json_encode($result['error']));
                } else {
                    log_message('error', 'Unexpected Gemini API response format: ' . $response);
                }
                return 'error';
            }

        } catch (Exception $e) {
            log_message('error', 'Exception calling Gemini API: ' . $e->getMessage());
            return 'error';
        }
    }

    // Fungsi untuk membersihkan respons Gemini AI
    private function cleanGeminiResponse($response)
    {
        // Hilangkan markdown formatting jika ada
        $response = preg_replace('/\*\*(.*?)\*\*/', '$1', $response);
        $response = preg_replace('/\*(.*?)\*/', '$1', $response);
        
        // Hilangkan line breaks berlebihan
        $response = preg_replace('/\n+/', ' ', $response);
        
        // Trim whitespace
        $response = trim($response);
        
        return $response;
    }

    // Fungsi untuk membangun context dari dataset yang ada
    private function buildContextFromDataset()
    {
        $tags = array_keys($this->tagCounts);
        $context = "Sistem ini menangani pertanyaan tentang: " . implode(', ', $tags);
        
        // Ambil beberapa contoh pertanyaan untuk memberikan context yang lebih baik
        $sampleQuestions = [];
        foreach (array_slice($this->dataset, 0, 5) as $data) {
            $sampleQuestions[] = $data['question'];
        }
        
        if (!empty($sampleQuestions)) {
            $context .= ". Contoh pertanyaan yang bisa dijawab: " . implode(', ', $sampleQuestions);
        }
        
        return $context;
    }

    // Fungsi untuk memeriksa apakah pertanyaan sudah pernah dijawab oleh teknisi
    private function checkTechnicalAnswer($input)
    {
        $result = $this->unansweredQuestionModel->findSimilarQuestion($input);
        
        if ($result['match'] && $result['score'] > 80) { // Naikkan threshold
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

    // Fungsi mencari kecocokan mirip dengan algoritma yang diperbaiki
    private function findSimilarMatch($input)
    {
        $bestScore = 0;
        $bestAnswer = null;
        $bestTag = 'unknown';
        
        // Extract key words from input
        $inputWords = array_filter(preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', strtolower($input))), function($word) {
            return strlen($word) > 2; // Filter kata pendek
        });

        foreach ($this->dataset as $data) {
            $questionWords = array_filter(preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', strtolower($data['question']))), function($word) {
                return strlen($word) > 2; // Filter kata pendek
            });
            
            // Calculate Levenshtein distance untuk similarity yang lebih baik
            $levenshtein = levenshtein(strtolower($input), strtolower($data['question']));
            $maxLength = max(strlen($input), strlen($data['question']));
            $levenshteinPercent = (1 - $levenshtein / $maxLength) * 100;
            
            // Calculate similar_text score
            similar_text(strtolower($input), strtolower($data['question']), $similarPercent);
            
            // Kombinasi kedua metode
            $combinedScore = ($levenshteinPercent + $similarPercent) / 2;
            
            // Bonus score for exact keyword matches
            $keywordBonus = 0;
            $commonWords = array_intersect($inputWords, $questionWords);
            
            foreach ($commonWords as $word) {
                if (strlen($word) > 3) {
                    $keywordBonus += 15; // Bonus per kata penting yang cocok
                }
            }
            
            // Context penalty untuk kata yang bertentangan
            $contextPenalty = $this->calculateContextPenalty($inputWords, $questionWords);
            
            // Final score calculation
            $finalScore = $combinedScore + $keywordBonus - $contextPenalty;
            
            if ($finalScore > $bestScore) {
                $bestScore = $finalScore;
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

    // Fungsi untuk menghitung penalty berdasarkan konteks yang bertentangan
    private function calculateContextPenalty($inputWords, $questionWords)
    {
        $penalty = 0;
        
        // Definisi kata-kata yang bertentangan
        $conflictWords = [
            'buat' => ['edit', 'ubah', 'update', 'hapus', 'delete'],
            'edit' => ['buat', 'create', 'new', 'tambah'],
            'hapus' => ['buat', 'create', 'tambah', 'edit'],
            'login' => ['logout', 'keluar', 'exit'],
            'masuk' => ['keluar', 'logout', 'exit'],
            'upload' => ['download', 'unduh'],
            'simpan' => ['hapus', 'delete', 'batalkan']
        ];
        
        foreach ($inputWords as $inputWord) {
            if (isset($conflictWords[$inputWord])) {
                foreach ($conflictWords[$inputWord] as $conflictWord) {
                    if (in_array($conflictWord, $questionWords)) {
                        $penalty += 25; // Penalty berat untuk konteks bertentangan
                    }
                }
            }
        }
        
        return $penalty;
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
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag] ?? []);

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

        $answer = ($bestScore > 40) ? $bestAnswer : $defaultAnswer; // Naikkan threshold

        if (empty($answer)) {
            return $this->getInformationForUnknownQuery($input);
        }

        return $answer;
    }

    // Fungsi untuk menangani pertanyaan yang tidak ada dalam database
    private function getInformationForUnknownQuery($input)
    {
        return "Mohon maaf, saya belum memiliki informasi yang tepat untuk pertanyaan Anda. Tim kami akan meninjau pertanyaan ini untuk memberikan jawaban yang lebih baik di masa depan. Apakah ada hal lain yang bisa saya bantu?";
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
                'answer' => $answer
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
