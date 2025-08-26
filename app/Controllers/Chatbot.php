<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UnansweredQuestionModel;
use App\Models\AnsweredQuestionsModel;
use App\Models\EmployeeModel; // Tambahkan model yang diperlukan
use App\Models\PerformanceModel;
use App\Models\ReportModel;
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
            'laporan_kinerja' => 'Sistem laporan kinerja harian, mingguan, dan bulanan',
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

    // Daftar kata kunci untuk respons khusus
    protected $keywordResponses = [
        'pagi' => 'Selamat pagi! Saya di sini untuk membantu Anda dengan sistem kinerja perusahaan. Ada yang bisa dibantu?',
        'siang' => 'Selamat siang! Apakah ada kendala dengan laporan kerja atau sistem kinerja yang perlu bantuan?',
        'sore' => 'Selamat sore! Saya siap membantu Anda dengan pertanyaan seputar sistem kinerja dan laporan.',
        'malam' => 'Selamat malam! Meski sudah malam, saya tetap siap membantu dengan sistem kinerja perusahaan.',
        'halo' => 'Halo! Saya asisten virtual untuk sistem kinerja perusahaan. Ada yang bisa saya bantu?',
        'hai' => 'Hai! Saya di sini untuk membantu Anda dengan segala hal terkait laporan kerja dan evaluasi kinerja.',
        'hi' => 'Hi! Ada kendala dengan sistem kinerja atau butuh panduan penggunaan fitur tertentu?'
    ];

    protected $defaultInformation = [
        'Tim support sedang memproses pertanyaan Anda. Sementara itu, Anda bisa cek panduan di menu Help atau hubungi supervisor langsung.',
        'Pertanyaan Anda sedang dianalisis oleh sistem. Coba periksa FAQ di dashboard atau konsultasi dengan HR.',
        'Saya akan mencarikan informasi yang tepat untuk Anda. Sambil menunggu, silakan cek dokumentasi sistem di menu Bantuan.'
    ];

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;
    protected $employeeModel;
    protected $performanceModel;
    protected $reportModel;

    public function __construct()
    {
        $this->geminiApiKey = getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE';
        
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel = new AnsweredQuestionsModel();
        $this->employeeModel = new EmployeeModel(); // Inisialisasi model yang diperlukan
        $this->performanceModel = new PerformanceModel();
        $this->reportModel = new ReportModel();
        
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

        // Jika skor rendah atau tidak ada kecocokan, gunakan Context-Aware Gemini
        $contextualAnswer = $this->getContextualGeminiResponse($input);
        if ($contextualAnswer && $contextualAnswer !== 'error') {
            $this->saveToAnsweredQuestions($input, $contextualAnswer, 'ai_contextual');
            return $this->response->setJSON(['message' => $contextualAnswer]);
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

    // Fungsi utama: Context-Aware Gemini Response
    private function getContextualGeminiResponse($question)
    {
        try {
            // Dapatkan context dari sistem
            $systemInfo = $this->getSystemContext($question);
            $userContext = $this->getUserContext();
            
            // Buat prompt yang natural dan kontekstual
            $prompt = $this->buildContextualPrompt($question, $systemInfo, $userContext);
            
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
                    'temperature' => 0.4, // Lebih konsisten
                    'maxOutputTokens' => 300,
                    'topP' => 0.9,
                    'topK' => 40
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
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

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                log_message('error', 'Gemini API HTTP ' . $httpCode . ': ' . $response);
                return 'error';
            }

            $result = json_decode($response, true);
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $rawAnswer = trim($result['candidates'][0]['content']['parts'][0]['text']);
                
                // Post-process jawaban agar lebih natural
                $naturalAnswer = $this->naturalizeAnswer($rawAnswer, $question);
                
                log_message('info', 'Contextual Gemini response: ' . $naturalAnswer);
                return $naturalAnswer;
            } else {
                log_message('error', 'Unexpected Gemini response: ' . $response);
                return 'error';
            }

        } catch (Exception $e) {
            log_message('error', 'Gemini API error: ' . $e->getMessage());
            return 'error';
        }
    }

    // Dapatkan konteks sistem berdasarkan pertanyaan
    private function getSystemContext($question)
    {
        $context = [];
        $question = strtolower($question);
        
        // Analisis kata kunci untuk menentukan fitur yang relevan
        if (strpos($question, 'laporan') !== false || strpos($question, 'report') !== false) {
            $context['feature'] = 'laporan_kinerja';
            $context['process'] = $this->systemContext['common_processes']['cara_input_laporan'];
        } elseif (strpos($question, 'kinerja') !== false || strpos($question, 'evaluasi') !== false) {
            $context['feature'] = 'evaluasi_pegawai';
            $context['process'] = $this->systemContext['common_processes']['cara_cek_kinerja'];
        } elseif (strpos($question, 'target') !== false || strpos($question, 'kpi') !== false) {
            $context['feature'] = 'target_kpi';
            $context['process'] = $this->systemContext['common_processes']['cara_update_target'];
        } elseif (strpos($question, 'cuti') !== false || strpos($question, 'absen') !== false) {
            $context['feature'] = 'absensi';
            $context['process'] = $this->systemContext['common_processes']['cara_request_cuti'];
        } else {
            $context['feature'] = 'general';
            $context['process'] = 'Silakan navigasi melalui dashboard utama untuk mengakses fitur yang diperlukan';
        }

        // Tambahkan informasi fitur
        if (isset($this->systemContext['system_features'][$context['feature']])) {
            $context['description'] = $this->systemContext['system_features'][$context['feature']];
        }

        return $context;
    }

    // Dapatkan konteks user (bisa dikembangkan lebih lanjut)
    private function getUserContext()
    {
        // Placeholder - bisa diisi dengan data session user aktif
        return [
            'role' => 'employee', // Bisa didapat dari session
            'department' => 'general', // Bisa didapat dari database user
            'access_level' => 'standard'
        ];
    }

    // Build prompt yang kontekstual dan natural
    private function buildContextualPrompt($question, $systemInfo, $userContext)
    {
        $basePrompt = "Anda adalah asisten virtual internal " . $this->systemContext['company_name'] . " yang membantu karyawan dengan sistem manajemen kinerja. ";
        
        $rolePrompt = "Berikan jawaban yang praktis, friendly, dan seolah-olah Anda adalah staff IT support yang berpengalaman dengan sistem perusahaan. ";
        
        $contextPrompt = "";
        if (isset($systemInfo['feature']) && $systemInfo['feature'] !== 'general') {
            $contextPrompt = "Pertanyaan ini terkait dengan " . ($systemInfo['description'] ?? $systemInfo['feature']) . ". ";
            if (isset($systemInfo['process'])) {
                $contextPrompt .= "Proses umumnya: " . $systemInfo['process'] . ". ";
            }
        }
        
        $instructionPrompt = "Jawab dalam bahasa Indonesia yang natural dan jangan menyebutkan bahwa Anda adalah AI. ";
        $instructionPrompt .= "Gunakan istilah seperti 'saya akan bantu', 'berdasarkan pengalaman', 'biasanya', dll. ";
        $instructionPrompt .= "Jika tidak yakin, sarankan untuk menghubungi supervisor atau cek dokumentasi sistem. ";
        
        $questionPrompt = "\n\nPertanyaan: " . $question . "\n\nJawaban:";
        
        return $basePrompt . $rolePrompt . $contextPrompt . $instructionPrompt . $questionPrompt;
    }

    // Naturalisasi jawaban agar tidak terkesan AI
    private function naturalizeAnswer($answer, $question)
    {
        // Hapus indikasi AI yang terlalu jelas
        $replacements = [
            'sebagai AI' => 'berdasarkan pengalaman dengan sistem',
            'saya adalah AI' => 'saya dari tim support sistem',
            'sistem AI' => 'sistem bantuan',
            'artificial intelligence' => 'sistem otomatis',
            'machine learning' => 'algoritma sistem'
        ];
        
        $naturalAnswer = str_ireplace(array_keys($replacements), array_values($replacements), $answer);
        
        // Tambahkan variasi natural di awal jawaban
        $naturalStarters = [
            'Baik, saya bantu. ',
            'Untuk masalah ini, ',
            'Berdasarkan pengalaman, ',
            'Biasanya untuk kasus seperti ini, ',
            'Saya coba jelaskan. '
        ];
        
        // Jika jawaban belum dimulai dengan starter natural, tambahkan
        if (!preg_match('/^(baik|untuk|berdasarkan|biasanya|saya)/i', trim($naturalAnswer))) {
            $starter = $naturalStarters[array_rand($naturalStarters)];
            $naturalAnswer = $starter . lcfirst(trim($naturalAnswer));
        }
        
        return trim($naturalAnswer);
    }

    // Method lainnya tetap sama seperti sebelumnya...
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
