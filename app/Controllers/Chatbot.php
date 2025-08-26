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
    
    // Context sistem kinerja perusahaan
    protected $systemContext = [
        'company_name' => 'PT. ARMINDO',
        'system_features' => [
            'laporan_kinerja' => 'Pegawai dapat membuat laporan kerja harian melalui form laporan kerja di sistem.',
            'evaluasi_pegawai' => 'Penilaian kinerja pegawai dengan berbagai metrik',
            'riwayat_pekerjaan' => 'Tracking riwayat tugas dan pencapaian pegawai',
            'absensi' => 'Sistem absensi digital dan monitoring kehadiran',
            'target_kpi' => 'Penetapan dan monitoring KPI individual dan tim',
            'feedback_360' => 'Sistem feedback dari atasan, rekan kerja, dan bawahan',
            'pelatihan_development' => 'Tracking program pelatihan dan pengembangan skill'
        ],
        'common_processes' => [
            'cara_input_laporan' => 'Login → Dashboard → Laporan Harian → Isi Form → Submit',
            'cara_cek_kinerja' => 'Menu Evaluasi → Pilih Periode → Lihat Grafik Performance',
            'cara_update_target' => 'Menu KPI → Edit Target → Konfirmasi dengan Supervisor',
            'cara_request_cuti' => 'Menu Absensi → Request Cuti → Tunggu Approval'
        ]
    ];

    protected $keywordResponses = [
        'pagi' => 'Selamat pagi! Ada yang bisa saya bantu?',
        'siang' => 'Selamat siang! Ada yang bisa saya bantu?',
        'sore' => 'Selamat sore! Ada yang bisa saya bantu?',
        'malam' => 'Selamat malam! Ada yang bisa saya bantu?',
        'halo' => 'Halo! Ada yang bisa saya bantu?',
        'hai' => 'Hai! Ada yang bisa saya bantu hari ini?',
        'hi' => 'Hi! Ada yang bisa saya bantu?'
    ];

    protected $defaultInformation = [
        'Saya akan mencarikan informasi yang tepat untuk Anda. Sambil menunggu'
    ];

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    public function __construct()
    {
        $this->geminiApiKey = getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE';
        
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel = new AnsweredQuestionsModel();
        
        $this->loadDataset();
    }

    private function loadDataset()
    {
        $csvPath = FCPATH . 'dataset/chatbot_dataset.csv';

        if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
            fgetcsv($handle);

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 3) {
                    $question = strtolower(trim($data[0]));
                    $answer   = trim($data[1]);
                    $tag      = trim($data[2]);

                    $this->dataset[] = compact('question', 'answer', 'tag');

                    $words = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $question));
                    $this->tagCounts[$tag] = ($this->tagCounts[$tag] ?? 0) + 1;

                    foreach ($words as $word) {
                        if ($word === '') continue;
                        $this->vocab[$word] = true;
                        $this->wordTagCounts[$tag][$word] = ($this->wordTagCounts[$tag][$word] ?? 0) + 1;
                    }

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
            return $this->response->setJSON(['message' => 'Mohon ketik pertanyaan Anda terkait sistem kinerja perusahaan.']);
        }

        // Cek kata kunci sapaan
        foreach ($this->keywordResponses as $keyword => $response) {
            if ($input === $keyword || strpos($input, $keyword) !== false) {
                $this->saveToAnsweredQuestions($input, $response, 'greeting');
                return $this->response->setJSON(['message' => $response]);
            }
        }

        // Cek jawaban teknis yang sudah ada
        $technicalAnswer = $this->checkTechnicalAnswer($input);
        if ($technicalAnswer) {
            $this->saveToAnsweredQuestions($input, $technicalAnswer, 'technical');
            return $this->response->setJSON(['message' => $technicalAnswer]);
        }

        // Cek kecocokan persis di dataset
        $exactMatch = $this->findExactMatch($input);
        if ($exactMatch) {
            $tag = $this->findTagFromExactMatch($input);
            $this->saveToAnsweredQuestions($input, $exactMatch, $tag);
            return $this->response->setJSON(['message' => $exactMatch]);
        }

        // Cek kecocokan similar text
        $similarMatch = $this->findSimilarMatch($input);
        if ($similarMatch && $similarMatch['score'] > 80) {
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $similarMatch['tag']);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // Gunakan Dataset-Aware Gemini untuk skor rendah atau tidak ada kecocokan
        $datasetAwareAnswer = $this->getDatasetAwareGeminiResponse($input);
        if ($datasetAwareAnswer && $datasetAwareAnswer !== 'error') {
            $this->saveToAnsweredQuestions($input, $datasetAwareAnswer, 'ai_contextual');
            return $this->response->setJSON(['message' => $datasetAwareAnswer]);
        }

        // Fallback ke Naive Bayes
        $predictedTag = $this->predictTag($input);
        $tagConfidence = $this->calculateTagConfidence($input, $predictedTag);

        if ($tagConfidence < 0.3) {
            $this->saveUnansweredQuestion($input);
            $answer = $this->getInformationForUnknownQuery($input);
        } else {
            $answer = $this->getAnswerByTag($predictedTag, $input);
            $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
        }

        return $this->response->setJSON(['message' => $answer]);
    }

    // Method untuk mencari referensi dataset yang relevan
    private function findRelevantDatasetReferences($question)
    {
        $relevantData = [];
        $questionWords = preg_split('/\s+/', strtolower($question));
        
        foreach ($this->dataset as $data) {
            $dataWords = preg_split('/\s+/', strtolower($data['question']));
            $commonWords = array_intersect($questionWords, $dataWords);
            
            if (count($commonWords) >= 1) { // Minimal 1 kata sama
                similar_text(strtolower($question), strtolower($data['question']), $percent);
                $relevantData[] = [
                    'question' => $data['question'],
                    'answer' => $data['answer'],
                    'tag' => $data['tag'],
                    'similarity' => $percent
                ];
            }
        }
        
        // Sort by similarity
        usort($relevantData, function($a, $b) {
            return $b['similarity'] - $a['similarity'];
        });
        
        return array_slice($relevantData, 0, 3); // Top 3 most relevant
    }

    // Method untuk membangun knowledge base summary
    private function buildKnowledgeBaseSummary()
    {
        $categories = [];
        
        foreach ($this->dataset as $data) {
            $tag = $data['tag'];
            if (!isset($categories[$tag])) {
                $categories[$tag] = [];
            }
            $categories[$tag][] = $data['question'];
        }
        
        $summary = [];
        foreach ($categories as $tag => $questions) {
            $summary[] = "{$tag}: " . count($questions) . " FAQ tersedia";
        }
        
        return implode(", ", $summary);
    }

    // Dataset-Aware Gemini Response
    private function getDatasetAwareGeminiResponse($question)
    {
        try {
            // Dapatkan context sistem
            $systemInfo = $this->getSystemContext($question);
            
            // Dapatkan referensi dataset yang relevan
            $relevantDataset = $this->findRelevantDatasetReferences($question);
            
            // Build prompt yang comprehensive
            $prompt = $this->buildDatasetAwarePrompt($question, $systemInfo, $relevantDataset);
            
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=" . $this->geminiApiKey;
            
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
                    'temperature' => 0.3, // Lebih konsisten dengan dataset
                    'maxOutputTokens' => 350,
                    'topP' => 0.9,
                    'topK' => 40
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                log_message('error', 'Dataset-aware Gemini API HTTP ' . $httpCode);
                return 'error';
            }

            $result = json_decode($response, true);
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $rawAnswer = trim($result['candidates'][0]['content']['parts'][0]['text']);
                return $this->naturalizeAnswer($rawAnswer, $question);
            }
            
            return 'error';

        } catch (Exception $e) {
            log_message('error', 'Dataset-aware Gemini error: ' . $e->getMessage());
            return 'error';
        }
    }

    // Build prompt dengan dataset context
    private function buildDatasetAwarePrompt($question, $systemInfo, $relevantDataset)
    {
        $basePrompt = "Anda adalah staff IT support berpengalaman di " . $this->systemContext['company_name'] . " yang menguasai sistem manajemen kinerja perusahaan. ";
        
        // Tambahkan knowledge base summary
        $knowledgePrompt = "\nSistem yang Anda kuasai:\n";
        foreach ($this->systemContext['system_features'] as $feature => $description) {
            $knowledgePrompt .= "- {$description}\n";
        }
        
        // Tambahkan referensi dataset yang relevan
        $referencePrompt = "";
        if (!empty($relevantDataset)) {
            $referencePrompt = "\nFAQ Terkait yang Sudah Ada di Sistem:\n";
            foreach ($relevantDataset as $ref) {
                $referencePrompt .= "• {$ref['question']} → {$ref['answer']}\n";
            }
        }
        
        // Context proses spesifik
        $processPrompt = "";
        if (isset($systemInfo['feature']) && isset($this->systemContext['common_processes']['cara_'.$systemInfo['feature']])) {
            $processPrompt = "\nProses standar untuk {$systemInfo['feature']}: " . $this->systemContext['common_processes']['cara_'.$systemInfo['feature']] . "\n";
        }
        
        $instructionPrompt = "\nCara menjawab:\n";
        $instructionPrompt .= "- Jawab seperti staff IT yang berpengalaman, gunakan bahasa Indonesia natural\n";
        $instructionPrompt .= "- Referensikan FAQ yang relevan di atas jika ada\n";
        $instructionPrompt .= "- Berikan langkah-langkah spesifik jika diperlukan\n";
        $instructionPrompt .= "- Jangan sebut diri sebagai AI, berperan sebagai staff support internal\n";
        $instructionPrompt .= "- Jika tidak yakin 100%, sarankan konsultasi supervisor atau cek dokumentasi\n\n";
        
        $questionPrompt = "Pertanyaan Karyawan: " . $question . "\n\nJawaban Support Staff:";
        
        return $basePrompt . $knowledgePrompt . $referencePrompt . $processPrompt . $instructionPrompt . $questionPrompt;
    }

    // Dapatkan konteks sistem berdasarkan pertanyaan
    private function getSystemContext($question)
    {
        $context = [];
        $question = strtolower($question);
        
        // Analisis kata kunci untuk menentukan fitur yang relevan
        if (strpos($question, 'laporan') !== false || strpos($question, 'report') !== false) {
            $context['feature'] = 'laporan_kinerja';
        } elseif (strpos($question, 'kinerja') !== false || strpos($question, 'evaluasi') !== false) {
            $context['feature'] = 'evaluasi_pegawai';
        } elseif (strpos($question, 'target') !== false || strpos($question, 'kpi') !== false) {
            $context['feature'] = 'target_kpi';
        } elseif (strpos($question, 'cuti') !== false || strpos($question, 'absen') !== false) {
            $context['feature'] = 'absensi';
        } elseif (strpos($question, 'riwayat') !== false || strpos($question, 'history') !== false) {
            $context['feature'] = 'riwayat_pekerjaan';
        } else {
            $context['feature'] = 'general';
        }

        return $context;
    }

    // Naturalisasi jawaban agar tidak terkesan AI
    private function naturalizeAnswer($answer, $question)
    {
        // Hapus indikasi AI yang terlalu jelas
        $replacements = [
            'saya adalah AI' => 'saya dari tim support',
            'artificial intelligence' => 'sistem otomatis',
            'machine learning' => 'algoritma sistem',
            'sebagai asisten AI' => 'sebagai staff support'
        ];
        
        $naturalAnswer = str_ireplace(array_keys($replacements), array_values($replacements), $answer);
        
        // Tambahkan variasi natural di awal jika belum ada
        $naturalStarters = [
            'Baik, saya bantu. ',
            'Untuk masalah ini, ',
            'Biasanya untuk kasus seperti ini, ',
            'Saya coba jelaskan step by step. ',
            'Oh iya, untuk hal ini '
        ];
        
        // Cek apakah jawaban sudah natural
        $isAlreadyNatural = preg_match('/^(baik|untuk|biasanya|saya|oh|kalau)/i', trim($naturalAnswer));
        
        if (!$isAlreadyNatural) {
            $starter = $naturalStarters[array_rand($naturalStarters)];
            $naturalAnswer = $starter . lcfirst(trim($naturalAnswer));
        }
        
        // Tambahkan closing yang supportive random
        $supportiveEndings = [
            ' Semoga membantu!',
            ' Kalau masih bingung, bisa langsung tanya saya lagi.',
            ' Mudah kan prosesnya?',
            ' Coba dulu ya, kalau ada kendala kabari saya.',
            ''
        ];
        
        // 30% chance untuk menambah ending
        if (rand(1, 10) <= 3) {
            $ending = $supportiveEndings[array_rand($supportiveEndings)];
            $naturalAnswer = rtrim($naturalAnswer, '.') . $ending;
        }
        
        return trim($naturalAnswer);
    }

    // Method existing lainnya...
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

        if ($totalDocs == 0 || $vocabSize == 0) return 0;

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

        return isset($scores[$predictedTag]) && $totalScore > 0 ? exp($scores[$predictedTag]) / $totalScore : 0;
    }

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

    private function predictTag($input)
    {
        if (empty($this->tagCounts)) return 'general';

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
                if ($totalWordsInTag > 0) {
                    $logProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
                }
            }

            $scores[$tag] = $logProb;
        }

        if (empty($scores)) return 'general';
        
        arsort($scores);
        return array_key_first($scores);
    }

    private function getAnswerByTag($tag, $input)
    {
        if (!isset($this->tagAnswers[$tag])) {
            return "Maaf, saya belum memiliki informasi spesifik untuk pertanyaan ini. Bisa coba hubungi supervisor atau cek dokumentasi sistem?";
        }

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

    private function getInformationForUnknownQuery($input)
    {
        $randomInfo = $this->defaultInformation[array_rand($this->defaultInformation)];
        return $randomInfo;
    }

    private function saveUnansweredQuestion($question)
    {
        $existingQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->first();
            
        if (!$existingQuestion) {
            $this->unansweredQuestionModel->save([
                'question' => $question,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            log_message('info', 'Pertanyaan baru disimpan: ' . $question);
        }
    }

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

    public function showFrequentQuestions($tag = null)
    {
        $frequentQuestions = $this->answeredQuestionModel->getQuestionsByTag($tag);

        return view('frequent_questions', [
            'frequentQuestions' => $frequentQuestions,
            'selectedTag' => $tag
        ]);
    }
}
