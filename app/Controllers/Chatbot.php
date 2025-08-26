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
    protected $geminiApiKey;
    protected $useGemini = true;

    // Tambahkan mapping sinonim untuk meningkatkan pengenalan
    protected $synonyms = [
        'lihat' => ['melihat', 'cek', 'check', 'periksa', 'buka'],
        'cara' => ['bagaimana', 'gimana', 'metode', 'langkah'],
        'penilaian' => ['nilai', 'rating', 'score', 'evaluasi', 'assessment'],
        'bot' => ['chatbot', 'robot', 'ai', 'assistant', 'asisten'],
        'bantuan' => ['help', 'tolong', 'bantu'],
        'masalah' => ['problem', 'trouble', 'kendala', 'issue']
    ];

    // Daftar kata yang akan dihapus (stop words)
    protected $stopWords = [
        'adalah', 'dengan', 'untuk', 'pada', 'dalam', 'dari', 'ke', 'di', 'yang', 
        'ini', 'itu', 'dan', 'atau', 'juga', 'sudah', 'akan', 'dapat', 'bisa',
        'apa', 'siapa', 'dimana', 'kapan', 'mengapa', 'bagaimana', 'apakah',
        'saya', 'kamu', 'dia', 'kita', 'mereka', 'anda'
    ];

    // Keyword responses yang sudah ada
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

    protected $defaultMessage = 'Maaf, saya tidak dapat menemukan jawaban untuk pertanyaan Anda. Pertanyaan ini akan diteruskan ke admin untuk dijawab. Mohon ditunggu, admin akan merespons segera.';

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    public function __construct()
    {
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel = new AnsweredQuestionsModel();
        
        $this->geminiApiKey = getenv('GEMINI_API_KEY') ?: '';
        
        if (empty($this->geminiApiKey)) {
            $this->useGemini = false;
            log_message('warning', 'Gemini API Key tidak ditemukan. Gemini AI dinonaktifkan.');
        }
        
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

                    if (empty($question) || empty($answer) || empty($tag)) {
                        continue;
                    }

                    $this->dataset[] = compact('question', 'answer', 'tag');

                    // Gunakan tokenisasi yang lebih baik
                    $words = $this->improvedTokenize($question);
                    $this->tagCounts[$tag] = ($this->tagCounts[$tag] ?? 0) + 1;

                    foreach ($words as $word) {
                        if (strlen($word) > 1) { // Ubah dari 2 ke 1
                            $this->vocab[$word] = true;
                            $this->wordTagCounts[$tag][$word] = ($this->wordTagCounts[$tag][$word] ?? 0) + 1;
                        }
                    }

                    $this->tagAnswers[$tag][] = $answer;
                }
            }
            fclose($handle);
        }
    }

    // Perbaikan tokenisasi dengan penanganan sinonim
    private function improvedTokenize($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9 ]/', ' ', $text);
        $words = preg_split('/\s+/', trim($text));
        
        $processedWords = [];
        
        foreach ($words as $word) {
            if (empty($word) || strlen($word) <= 1) continue;
            
            // Skip stop words
            if (in_array($word, $this->stopWords)) continue;
            
            // Cek apakah ada sinonim
            $normalizedWord = $this->normalizeWord($word);
            $processedWords[] = $normalizedWord;
            
            // Tambahkan sinonim jika ada
            if (isset($this->synonyms[$normalizedWord])) {
                $processedWords = array_merge($processedWords, $this->synonyms[$normalizedWord]);
            }
        }
        
        return array_unique($processedWords);
    }

    // Fungsi untuk normalisasi kata (mengatasi variasi kata)
    private function normalizeWord($word)
    {
        // Cari kata dasar dari sinonim
        foreach ($this->synonyms as $baseWord => $synonymList) {
            if (in_array($word, $synonymList) || $word === $baseWord) {
                return $baseWord;
            }
        }
        return $word;
    }

    // Tokenisasi sederhana untuk kompatibilitas
    private function tokenizeText($text)
    {
        return $this->improvedTokenize($text);
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

        // LANGKAH 2: Cek jawaban teknis dari admin
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

        // LANGKAH 4: Improved similarity matching
        $similarMatch = $this->findImprovedSimilarMatch($inputLower);
        if ($similarMatch && $similarMatch['score'] > 70) { // Turunkan threshold
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $similarMatch['tag']);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // LANGKAH 5: Enhanced Naive Bayes dengan threshold yang lebih rendah
        if ($similarMatch && $similarMatch['score'] >= 40) { // Turunkan threshold
            $predictedTag = $this->predictTag($inputLower);
            $tagConfidence = $this->calculateTagConfidence($inputLower, $predictedTag);
            
            // Turunkan confidence threshold
            if ($tagConfidence > 0.2) {
                $answer = $this->getAnswerByTag($predictedTag, $inputLower);
                if ($answer !== $this->defaultMessage) {
                    $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
                    return $this->response->setJSON(['message' => $answer]);
                }
            }
        }

        // LANGKAH 6: Enhanced Gemini AI dengan konteks yang lebih baik
        if ($this->useGemini) {
            $geminiResponse = $this->askGeminiAI($input);
            if ($geminiResponse && !empty($geminiResponse)) {
                $this->saveToAnsweredQuestions($input, $geminiResponse, 'gemini_ai');
                $this->saveUnansweredQuestion($input, $geminiResponse);
                
                return $this->response->setJSON([
                    'message' => $geminiResponse,
                    'source' => 'AI Assistant'
                ]);
            }
        }

        // LANGKAH 7: Fallback ke admin
        $this->saveUnansweredQuestion($input);
        return $this->response->setJSON(['message' => $this->defaultMessage]);
    }

    // Perbaikan similarity matching
    private function findImprovedSimilarMatch($input)
    {
        $bestScore = 0;
        $bestAnswer = null;
        $bestTag = null;

        // Tokenisasi input
        $inputTokens = $this->improvedTokenize($input);
        $inputText = implode(' ', $inputTokens);

        foreach ($this->dataset as $data) {
            // Tokenisasi pertanyaan dataset
            $datasetTokens = $this->improvedTokenize($data['question']);
            $datasetText = implode(' ', $datasetTokens);

            // Hitung similarity dengan beberapa metode
            $score1 = $this->calculateJaccardSimilarity($inputTokens, $datasetTokens);
            $score2 = $this->calculateCosineSimilarity($inputTokens, $datasetTokens);
            
            // Similarity teks asli
            similar_text($inputText, $datasetText, $score3);
            
            // Gabungkan skor dengan bobot
            $finalScore = ($score1 * 0.4) + ($score2 * 0.4) + ($score3 * 0.2);

            if ($finalScore > $bestScore) {
                $bestScore = $finalScore;
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

    // Hitung Jaccard Similarity
    private function calculateJaccardSimilarity($tokens1, $tokens2)
    {
        $set1 = array_unique($tokens1);
        $set2 = array_unique($tokens2);
        
        $intersection = count(array_intersect($set1, $set2));
        $union = count(array_unique(array_merge($set1, $set2)));
        
        return $union > 0 ? ($intersection / $union) * 100 : 0;
    }

    // Hitung Cosine Similarity
    private function calculateCosineSimilarity($tokens1, $tokens2)
    {
        $vector1 = array_count_values($tokens1);
        $vector2 = array_count_values($tokens2);
        
        $allWords = array_unique(array_merge($tokens1, $tokens2));
        
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        foreach ($allWords as $word) {
            $count1 = $vector1[$word] ?? 0;
            $count2 = $vector2[$word] ?? 0;
            
            $dotProduct += $count1 * $count2;
            $magnitude1 += $count1 * $count1;
            $magnitude2 += $count2 * $count2;
        }
        
        $magnitude = sqrt($magnitude1) * sqrt($magnitude2);
        
        return $magnitude > 0 ? ($dotProduct / $magnitude) * 100 : 0;
    }

    // Perbaikan Gemini prompt
    private function buildGeminiPrompt($question, $context)
    {
        return "Anda adalah asisten chatbot yang membantu menjawab pertanyaan seputar sistem atau aplikasi.

KONTEKS DATASET:
{$context}

ATURAN PENTING:
1. Prioritaskan jawaban yang konsisten dengan data yang ada di dataset
2. Jika pertanyaan mirip dengan yang ada di dataset, berikan jawaban yang serupa
3. Gunakan bahasa Indonesia yang natural dan membantu
4. Jawaban maksimal 150 kata
5. Jika tidak yakin, katakan akan menghubungkan dengan admin

PERTANYAAN USER: {$question}

Berikan jawaban yang tepat dan membantu:";
    }

    // Method lainnya tetap sama seperti kode asli...
    private function askGeminiAI($question)
    {
        if (!$this->useGemini) {
            return null;
        }

        try {
            $datasetContext = $this->buildDatasetContext();
            $prompt = $this->buildGeminiPrompt($question, $datasetContext);
            $response = $this->callGeminiAPI($prompt);
            
            if ($response && !empty($response)) {
                log_message('info', 'Gemini AI response untuk: "' . $question . '" - Response: ' . substr($response, 0, 100));
                return $response;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Gemini AI Error: ' . $e->getMessage());
        }

        return null;
    }

    private function buildDatasetContext()
    {
        $contexts = [];
        $tagsSample = array_slice(array_keys($this->tagCounts), 0, 5);
        
        foreach ($tagsSample as $tag) {
            if (isset($this->tagAnswers[$tag])) {
                $contexts[] = "Tag: {$tag} - Contoh jawaban: " . substr($this->tagAnswers[$tag][0], 0, 100);
            }
        }
        
        return implode("\n", array_slice($contexts, 0, 3));
    }

    private function callGeminiAPI($prompt)
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $this->geminiApiKey;
        
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
                'temperature' => 0.3, // Turunkan temperature untuk lebih konsisten
                'topK' => 20,
                'topP' => 0.8,
                'maxOutputTokens' => 512,
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            log_message('error', 'Gemini API cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }
        
        curl_close($ch);

        if ($httpCode !== 200) {
            log_message('error', 'Gemini API HTTP Error: ' . $httpCode . ' - Response: ' . $response);
            return null;
        }

        $responseData = json_decode($response, true);
        
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return trim($responseData['candidates'][0]['content']['parts'][0]['text']);
        }

        log_message('error', 'Gemini API Response tidak valid: ' . $response);
        return null;
    }

    // Method lainnya tetap sama seperti kode asli
    private function checkKeywordResponse($input)
    {
        if (isset($this->keywordResponses[$input])) {
            return $this->keywordResponses[$input];
        }

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
        
        if ($result['match'] && $result['score'] > 70 && !empty($result['match']['answer'])) { // Turunkan threshold
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

    private function predictTag($input)
    {
        if (empty($this->tagCounts)) {
            return 'general';
        }

        $inputWords = $this->improvedTokenize($input);
        if (empty($inputWords)) {
            return 'general';
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

        $inputWords = $this->improvedTokenize($input);
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

        $bestScore = 0;
        $bestAnswer = null;

        foreach ($this->dataset as $data) {
            if ($data['tag'] === $tag) {
                $inputTokens = $this->improvedTokenize($input);
                $dataTokens = $this->improvedTokenize($data['question']);
                
                $score = $this->calculateJaccardSimilarity($inputTokens, $dataTokens);
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestAnswer = $data['answer'];
                }
            }
        }

        if ($bestScore < 20) { // Turunkan threshold
            return $this->tagAnswers[$tag][array_rand($this->tagAnswers[$tag])];
        }

        return $bestAnswer ?? $this->defaultMessage;
    }

    private function saveUnansweredQuestion($question, $geminiAnswer = null)
    {
        $existingQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->where('status', 'pending')
            ->first();
            
        if (!$existingQuestion) {
            $data = [
                'question' => $question,
                'status' => 'pending',
                'ai_suggestion' => $geminiAnswer,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->unansweredQuestionModel->save($data);
            log_message('info', 'Pertanyaan baru disimpan ke database: ' . $question);
        } else {
            if ($geminiAnswer) {
                $this->unansweredQuestionModel->update($existingQuestion['id'], [
                    'ai_suggestion' => $geminiAnswer,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    private function saveToAnsweredQuestions($question, $answer, $tag)
    {
        if (empty($question) || empty($answer)) {
            return;
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
                'source' => ($tag === 'gemini_ai') ? 'AI' : 'dataset',
                'created_at' => date('Y-m-d H:i:s'),
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        }

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

    public function toggleGemini()
    {
        $this->useGemini = !$this->useGemini;
        
        return $this->response->setJSON([
            'status' => 'success',
            'gemini_status' => $this->useGemini ? 'enabled' : 'disabled'
        ]);
    }

    public function testGemini()
    {
        if (!$this->useGemini) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gemini AI tidak aktif'
            ]);
        }

        $testResponse = $this->askGeminiAI('Hello, test connection');
        
        return $this->response->setJSON([
            'status' => $testResponse ? 'success' : 'error',
            'message' => $testResponse ? 'Koneksi Gemini AI berhasil' : 'Koneksi Gemini AI gagal',
            'response' => $testResponse
        ]);
    }
}
