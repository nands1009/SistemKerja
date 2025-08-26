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

    // Threshold untuk menentukan apakah jawaban cukup relevan
    protected $MINIMUM_CONFIDENCE_SCORE = 75; // Naikkan dari 70 ke 75
    protected $GEMINI_MINIMUM_SCORE = 60;     // Threshold untuk menggunakan Gemini
    protected $EXACT_MATCH_THRESHOLD = 85;    // Threshold untuk exact match
    
    // Kata-kata yang mengindikasikan pertanyaan tidak jelas
    protected $unclearIndicators = [
        'apa', 'gimana', 'bagaimana', 'kenapa', 'mengapa', 'kapan', 'dimana', 
        'siapa', 'berapa', 'test', 'coba', 'tes', '123', 'abc'
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
        $this->loadDataset();
    }

    private function loadDataset()
    {
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
        $input = trim($this->request->getPost('message'));

        if (!$input) {
            return $this->response->setJSON(['message' => 'Pertanyaan kosong.']);
        }

        // Validasi input untuk menghindari spam atau input tidak valid
        if (!$this->isValidQuestion($input)) {
            $this->saveUnansweredQuestion($input, 'invalid_input');
            return $this->response->setJSON([
                'message' => 'Maaf, pertanyaan Anda tidak jelas. Silakan tulis pertanyaan yang lebih spesifik atau hubungi admin untuk bantuan lebih lanjut.'
            ]);
        }

        $inputLower = strtolower($input);

        // LANGKAH 1: Cek kata kunci sapaan
        $greetingResponse = $this->checkGreetingKeywords($inputLower);
        if ($greetingResponse) {
            $this->saveToAnsweredQuestions($input, $greetingResponse, 'greeting');
            return $this->response->setJSON(['message' => $greetingResponse]);
        }

        // LANGKAH 2: Cek jawaban teknis dari admin
        $technicalAnswer = $this->checkTechnicalAnswer($inputLower);
        if ($technicalAnswer) {
            $this->saveToAnsweredQuestions($input, $technicalAnswer, 'technical');
            return $this->response->setJSON(['message' => $technicalAnswer]);
        }

        // LANGKAH 3: Cek kecocokan persis di dataset
        $exactMatch = $this->findExactMatch($inputLower);
        if ($exactMatch) {
            $tag = $this->findTagFromExactMatch($inputLower);
            $this->saveToAnsweredQuestions($input, $exactMatch, $tag);
            return $this->response->setJSON(['message' => $exactMatch]);
        }

        // LANGKAH 4: Cek kecocokan similar dengan threshold ketat
        $similarMatch = $this->findSimilarMatch($inputLower);
        
        if ($similarMatch && $similarMatch['score'] >= $this->MINIMUM_CONFIDENCE_SCORE) {
            // Validasi tambahan untuk memastikan jawaban relevan
            if ($this->isAnswerRelevant($input, $similarMatch['answer'], $similarMatch['question'])) {
                $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $similarMatch['tag']);
                return $this->response->setJSON(['message' => $similarMatch['answer']]);
            }
        }

        // LANGKAH 5: Coba Gemini AI untuk pertanyaan dengan skor menengah
        if ($similarMatch && $similarMatch['score'] >= $this->GEMINI_MINIMUM_SCORE) {
            $geminiAnswer = $this->getGeminiResponse($input, $similarMatch);
            if ($geminiAnswer && $geminiAnswer !== 'error') {
                // Validasi jawaban Gemini sebelum mengirim
                if ($this->validateGeminiAnswer($geminiAnswer, $input)) {
                    $this->saveToAnsweredQuestions($input, $geminiAnswer, 'ai_generated');
                    return $this->response->setJSON(['message' => $geminiAnswer]);
                }
            }
        }

        // LANGKAH 6: Naive Bayes sebagai fallback terakhir
        $predictedTag = $this->predictTag($inputLower);
        $tagConfidence = $this->calculateTagConfidence($inputLower, $predictedTag);

        if ($tagConfidence >= 0.4) { // Naikkan threshold confidence
            $answer = $this->getAnswerByTag($predictedTag, $inputLower);
            
            // Validasi jawaban dari Naive Bayes
            if ($this->isAnswerRelevant($input, $answer, '')) {
                $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
                return $this->response->setJSON(['message' => $answer]);
            }
        }

        // LANGKAH 7: Jika semua gagal, kirim ke admin
        $this->saveUnansweredQuestion($input, 'no_suitable_answer');
        return $this->response->setJSON([
            'message' => 'Maaf, saya belum bisa memahami pertanyaan Anda dengan baik. Pertanyaan Anda telah diteruskan ke tim support kami untuk mendapatkan jawaban yang lebih akurat. Terima kasih atas kesabaran Anda.'
        ]);
    }

    // Fungsi untuk memvalidasi apakah pertanyaan valid
    private function isValidQuestion($input)
    {
        $input = trim($input);
        $inputLower = strtolower($input);
        
        // Cek panjang minimum
        if (strlen($input) < 3) {
            return false;
        }
        
        // Cek apakah hanya karakter berulang
        if (preg_match('/^(.)\1+$/', $input)) {
            return false;
        }
        
        // Cek apakah hanya angka atau karakter khusus
        if (preg_match('/^[0-9\W]+$/', $input)) {
            return false;
        }
        
        // Cek kata-kata tidak jelas yang berdiri sendiri
        $words = explode(' ', $inputLower);
        if (count($words) == 1 && in_array($words[0], $this->unclearIndicators)) {
            return false;
        }
        
        return true;
    }

    // Fungsi untuk memvalidasi relevansi jawaban
    private function isAnswerRelevant($question, $answer, $originalQuestion = '')
    {
        $questionLower = strtolower($question);
        $answerLower = strtolower($answer);
        
        // Cek apakah jawaban terlalu generik
        $genericAnswers = [
            'maaf, saya belum mengerti',
            'mohon maaf',
            'terima kasih',
            'selamat'
        ];
        
        foreach ($genericAnswers as $generic) {
            if (strpos($answerLower, $generic) !== false && strlen($answer) < 50) {
                return false;
            }
        }
        
        // Cek konteks pertanyaan dan jawaban
        if ($originalQuestion) {
            $questionWords = array_filter(explode(' ', $questionLower));
            $originalWords = array_filter(explode(' ', strtolower($originalQuestion)));
            $answerWords = array_filter(explode(' ', $answerLower));
            
            // Hitung kecocokan kata kunci
            $commonWords = array_intersect($questionWords, $originalWords);
            $answerRelevance = array_intersect($questionWords, $answerWords);
            
            // Jika tidak ada kata yang cocok dan jawaban terlalu pendek
            if (empty($commonWords) && empty($answerRelevance) && strlen($answer) < 30) {
                return false;
            }
        }
        
        return true;
    }

    // Fungsi untuk memvalidasi jawaban dari Gemini
    private function validateGeminiAnswer($answer, $question)
    {
        $answerLower = strtolower(trim($answer));
        $questionLower = strtolower(trim($question));
        
        // Cek apakah jawaban terlalu pendek atau generik
        if (strlen($answer) < 20) {
            return false;
        }
        
        // Cek apakah jawaban mengandung informasi yang relevan
        $questionWords = array_filter(explode(' ', $questionLower), function($word) {
            return strlen($word) > 3 && !in_array($word, ['yang', 'dengan', 'untuk', 'dari', 'pada']);
        });
        
        $relevantWords = 0;
        foreach ($questionWords as $word) {
            if (strpos($answerLower, $word) !== false) {
                $relevantWords++;
            }
        }
        
        // Minimal 1 kata relevan untuk jawaban pendek, atau minimal 30% untuk jawaban panjang
        $minRelevance = count($questionWords) > 3 ? ceil(count($questionWords) * 0.3) : 1;
        
        return $relevantWords >= $minRelevance;
    }

    // Fungsi untuk cek kata kunci sapaan
    private function checkGreetingKeywords($input)
    {
        foreach ($this->keywordResponses as $keyword => $response) {
            if ($input === $keyword || strpos($input, $keyword) !== false) {
                return $response;
            }
        }
        return null;
    }

    // Fungsi yang sudah diperbaiki untuk Gemini AI
    private function getGeminiResponse($question, $context = null)
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=" . $this->geminiApiKey;
            
            // Buat prompt yang lebih spesifik dengan konteks
            $contextInfo = "";
            if ($context && isset($context['question'])) {
                $contextInfo = " Pertanyaan serupa yang pernah ditanyakan: '" . $context['question'] . "' dengan jawaban: '" . $context['answer'] . "'. ";
            }
            
            $prompt = "Anda adalah asisten customer service yang profesional dan membantu. " . $contextInfo . 
                     "Berikan jawaban yang spesifik, informatif, dan dalam bahasa Indonesia untuk pertanyaan: '" . $question . 
                     "'. Jawaban harus relevan dengan pertanyaan, tidak lebih dari 150 kata, dan hindari jawaban yang terlalu umum. " .
                     "Jika tidak bisa menjawab dengan pasti, katakan 'Saya perlu informasi lebih lanjut untuk menjawab pertanyaan Anda.'";
            
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
                    'temperature' => 0.3,  // Kurangi temperature untuk jawaban lebih konsisten
                    'maxOutputTokens' => 150,
                    'topP' => 0.7,
                    'topK' => 20
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
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $answer = trim($result['candidates'][0]['content']['parts'][0]['text']);
                log_message('info', 'Gemini AI response: ' . $answer);
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

    // Fungsi untuk memeriksa jawaban teknis dari admin
    private function checkTechnicalAnswer($input)
    {
        $result = $this->unansweredQuestionModel->findSimilarQuestion($input);
        
        if ($result['match'] && $result['score'] > $this->EXACT_MATCH_THRESHOLD) {
            return $result['match']['answer'];
        }
        
        return null;
    }

    // Fungsi yang sudah diperbaiki untuk similar match
    private function findSimilarMatch($input)
    {
        $bestScore = 0;
        $bestAnswer = null;
        $bestTag = 'unknown';
        $bestQuestion = '';
        
        $inputWords = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', strtolower($input)));
        $inputWords = array_filter($inputWords, function($word) {
            return strlen($word) > 2; // Filter kata yang terlalu pendek
        });

        foreach ($this->dataset as $data) {
            $questionWords = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', strtolower($data['question'])));
            $questionWords = array_filter($questionWords, function($word) {
                return strlen($word) > 2;
            });
            
            // Hitung similar_text score
            similar_text(strtolower($input), strtolower($data['question']), $percent);
            
            // Hitung keyword matching dengan bobot
            $keywordScore = $this->calculateKeywordScore($inputWords, $questionWords);
            
            // Hitung semantic similarity
            $semanticScore = $this->calculateSemanticSimilarity($inputWords, $questionWords);
            
            // Gabungkan semua skor dengan bobot
            $finalScore = ($percent * 0.4) + ($keywordScore * 0.4) + ($semanticScore * 0.2);
            
            // Penalty untuk jawaban yang terlalu berbeda konteks
            $contextPenalty = $this->calculateContextPenalty($input, $data['question']);
            $finalScore -= $contextPenalty;
            
            if ($finalScore > $bestScore) {
                $bestScore = $finalScore;
                $bestAnswer = $data['answer'];
                $bestTag = $data['tag'];
                $bestQuestion = $data['question'];
            }
        }

        return [
            'score' => $bestScore,
            'answer' => $bestAnswer,
            'tag' => $bestTag,
            'question' => $bestQuestion
        ];
    }

    // Fungsi untuk menghitung skor kata kunci
    private function calculateKeywordScore($inputWords, $questionWords)
    {
        if (empty($inputWords) || empty($questionWords)) {
            return 0;
        }
        
        $commonWords = array_intersect($inputWords, $questionWords);
        $importantWords = array_filter($commonWords, function($word) {
            return strlen($word) > 3 && !in_array($word, ['yang', 'dengan', 'untuk', 'dari', 'pada', 'akan', 'dapat', 'juga', 'atau', 'dan']);
        });
        
        $totalWords = max(count($inputWords), count($questionWords));
        $score = (count($importantWords) / $totalWords) * 100;
        
        return min($score, 50); // Maksimal 50 poin dari keyword matching
    }

    // Fungsi untuk menghitung semantic similarity
    private function calculateSemanticSimilarity($inputWords, $questionWords)
    {
        // Kelompokkan kata berdasarkan konteks
        $contextGroups = [
            'login' => ['masuk', 'login', 'akses', 'account'],
            'laporan' => ['laporan', 'report', 'data', 'informasi'],
            'rencana' => ['rencana', 'plan', 'planning', 'jadwal'],
            'edit' => ['edit', 'ubah', 'update', 'modifikasi'],
            'buat' => ['buat', 'create', 'tambah', 'new'],
        ];
        
        $inputContext = [];
        $questionContext = [];
        
        // Identifikasi konteks dari input dan pertanyaan
        foreach ($contextGroups as $context => $words) {
            if (array_intersect($inputWords, $words)) {
                $inputContext[] = $context;
            }
            if (array_intersect($questionWords, $words)) {
                $questionContext[] = $context;
            }
        }
        
        // Hitung similarity berdasarkan konteks
        $contextSimilarity = count(array_intersect($inputContext, $questionContext));
        return $contextSimilarity * 10; // Maksimal 10 poin per konteks yang cocok
    }

    // Fungsi untuk menghitung penalty konteks
    private function calculateContextPenalty($input, $question)
    {
        // Kelompok kata yang bertentangan
        $conflictingGroups = [
            'login' => ['logout', 'keluar'],
            'masuk' => ['keluar', 'logout'],
            'buat' => ['hapus', 'delete'],
            'edit' => ['create', 'buat'],
            'laporan' => ['rencana'],
            'rencana' => ['laporan']
        ];
        
        $inputLower = strtolower($input);
        $questionLower = strtolower($question);
        
        $penalty = 0;
        
        foreach ($conflictingGroups as $word => $conflicts) {
            if (strpos($inputLower, $word) !== false) {
                foreach ($conflicts as $conflict) {
                    if (strpos($questionLower, $conflict) !== false) {
                        $penalty += 25; // Heavy penalty untuk konteks yang bertentangan
                    }
                }
            }
        }
        
        return $penalty;
    }

    // Fungsi yang sudah ada sebelumnya (tidak diubah)
    private function findExactMatch($input)
    {
        foreach ($this->dataset as $data) {
            if (strtolower(trim($data['question'])) === $input) {
                return $data['answer'];
            }
        }
        return null;
    }

    private function findTagFromExactMatch($input)
    {
        foreach ($this->dataset as $data) {
            if (strtolower(trim($data['question'])) === $input) {
                return $data['tag'];
            }
        }
        return 'unknown';
    }

    private function calculateTagConfidence($input, $predictedTag)
    {
        $inputWords = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $input));
        $totalDocs = array_sum($this->tagCounts);
        $vocabSize = count($this->vocab);

        $scores = [];
        $totalScore = 0;

        foreach ($this->tagCounts as $tag => $tagTotal) {
            $tagProb = log($tagTotal / $totalDocs);
            $wordProb = 0;
            
            foreach ($inputWords as $word) {
                if ($word === '') continue;

                $wordCount = $this->wordTagCounts[$tag][$word] ?? 0;
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag] ?? []);

                $wordProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
            }

            $scores[$tag] = $tagProb + $wordProb;
            $totalScore += exp($scores[$tag]);
        }

        return isset($scores[$predictedTag]) ? exp($scores[$predictedTag]) / $totalScore : 0;
    }

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

                $logProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
            }

            $scores[$tag] = $logProb;
        }

        arsort($scores);
        return array_key_first($scores);
    }

    private function getAnswerByTag($tag, $input)
    {
        if (!isset($this->tagAnswers[$tag])) {
            return "Maaf, saya belum mengerti maksud kamu.";
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
            return "Maaf, saya belum mengerti maksud kamu.";
        }

        return $answer;
    }

    // Fungsi yang diperbaiki untuk menyimpan pertanyaan yang tidak terjawab
    private function saveUnansweredQuestion($question, $reason = 'unknown')
    {
        $existingQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->first();
            
        if (!$existingQuestion) {
            $this->unansweredQuestionModel->save([
                'question' => $question,
                'status' => 'pending',
                'reason' => $reason, // Tambahan: alasan kenapa tidak terjawab
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            log_message('info', 'Pertanyaan baru disimpan ke database dengan alasan: ' . $reason . ' - ' . $question);
        } else {
            // Update frequency jika pertanyaan sudah ada
            $this->unansweredQuestionModel->update($existingQuestion['id'], [
                'frequency' => ($existingQuestion['frequency'] ?? 1) + 1,
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Fungsi lainnya tetap sama seperti sebelumnya
    private function saveToAnsweredQuestions($question, $answer, $tag)
    {
        $unansweredQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->first();
            
        if ($unansweredQuestion && !empty($unansweredQuestion['tag'])) {
            $tag = $unansweredQuestion['tag'];
        }
        
        if (empty($tag)) {
            $tag = 'general';
        }
        
        $existingQuestion = $this->answeredQuestionModel
            ->where('question', $question)
            ->first();
            
        if ($existingQuestion) {
            $this->answeredQuestionModel->update($existingQuestion['id'], [
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => $existingQuestion['frequency'] + 1,
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->answeredQuestionModel->save([
                'question' => $question,
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        if ($unansweredQuestion) {
            $this->unansweredQuestionModel->update($unansweredQuestion['id'], [
                'status' => 'answered',
                'answer' => $answer
            ]);
        }
    }

    public function moveAnsweredQuestionToAnotherTable($question, $answer, $tag)
    {
        return $this->saveToAnsweredQuestions($question, $answer, $tag);
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
