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
        'pagi' => 'Pagi juga! Ada yang bisa dibantu?',
        'siang' => 'Siang! Ada apa nih?',
        'sore' => 'Sore juga! Gimana ada yang bisa dibantu?',
        'malam' => 'Malam! Masih kerja ya? Ada masalah?',
        'halo' => 'Halo! Iya ada apa?',
        'hai' => 'Hai! Gimana?',
        'hi' => 'Hi! Ada yang mau ditanya?'
    ];

    // Responses yang lebih natural dan spontan
    protected $casualResponses = [
        'confusion' => [
            'Agak bingung nih maksudnya gimana ya?',
            'Kurang ngerti, bisa jelasin lagi ga?',
            'Hmm yang mana ya maksudnya?',
            'Waduh kurang paham nih, coba tanya yang lebih jelas deh.'
        ],
        'thinking' => [
            'Bentar ya, lagi inget-inget.',
            'Sebentar dulu, aku cek.',
            'Tunggu ya, lagi mikir nih.',
            'Hmm bentar, kayaknya pernah deh.'
        ],
        'closing' => [
            'Gitu aja sih.',
            'Udah jelas belum?',
            'Semoga bener ya.',
            'Kalo masih bingung tanya lagi aja.',
            'Ada lagi ga?',
            ''
        ]
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
            return $this->response->setJSON(['message' => 'Eh, ga ada yang ditanya nih. Ada masalah apa?']);
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
            $naturalAnswer = $this->makeMoreCasual($technicalAnswer, $input);
            $this->saveToAnsweredQuestions($input, $naturalAnswer, 'technical');
            return $this->response->setJSON(['message' => $naturalAnswer]);
        }

        // Cek kecocokan persis di dataset
        $exactMatch = $this->findExactMatch($input);
        if ($exactMatch) {
            $tag = $this->findTagFromExactMatch($input);
            $casualAnswer = $this->makeMoreCasual($exactMatch, $input);
            $this->saveToAnsweredQuestions($input, $casualAnswer, $tag);
            return $this->response->setJSON(['message' => $casualAnswer]);
        }

        // Cek kecocokan similar text
        $similarMatch = $this->findSimilarMatch($input);
        if ($similarMatch && $similarMatch['score'] > 80) {
            $casualAnswer = $this->makeMoreCasual($similarMatch['answer'], $input);
            $this->saveToAnsweredQuestions($input, $casualAnswer, $similarMatch['tag']);
            return $this->response->setJSON(['message' => $casualAnswer]);
        }

        // **PERBAIKAN UTAMA**: Coba Gemini dulu, jika gagal maka kirim ke admin
        $contextualAnswer = $this->getHumanLikeResponse($input);
        
        // Jika Gemini berhasil memberikan jawaban yang valid
        if ($contextualAnswer && $contextualAnswer !== 'error' && !empty(trim($contextualAnswer))) {
            $this->saveToAnsweredQuestions($input, $contextualAnswer, 'contextual');
            return $this->response->setJSON(['message' => $contextualAnswer]);
        }

        // **JIKA GEMINI GAGAL**: Coba Naive Bayes sebagai fallback
        $predictedTag = $this->predictTag($input);
        $tagConfidence = $this->calculateTagConfidence($input, $predictedTag);

        if ($tagConfidence >= 0.3) {
            $answer = $this->getAnswerByTag($predictedTag, $input);
            $answer = $this->makeMoreCasual($answer, $input);
            
            // Cek apakah jawaban valid (bukan error response)
            if (!$this->isGenericErrorResponse($answer)) {
                $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
                return $this->response->setJSON(['message' => $answer]);
            }
        }

        // **LANGKAH TERAKHIR**: Semua metode gagal, kirim ke admin
        $this->saveUnansweredQuestion($input);
        $adminNotificationResponse = $this->getAdminNotificationResponse($input);
        
        return $this->response->setJSON(['message' => $adminNotificationResponse]);
    }

    // **Method baru untuk cek apakah response adalah error generic**
    private function isGenericErrorResponse($response)
    {
        $errorPatterns = [
            'kurang tau nih',
            'belum pernah dapet',
            'bingung juga sih',
            'kurang ngerti',
            'waduh kurang paham',
            'ga tau aku',
            'maaf, saya belum mengerti'
        ];
        
        $lowerResponse = strtolower($response);
        foreach ($errorPatterns as $pattern) {
            if (strpos($lowerResponse, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    // **Method baru untuk response notifikasi admin**
    private function getAdminNotificationResponse($input)
    {
        $adminResponses = [
            'Waduh, ini pertanyaan baru nih. Udah aku kirim ke admin ya, nanti mereka jawab langsung.',
            'Hmm belum tau jawabannya. Udah aku forward ke tim yang lebih tau, tunggu sebentar ya.',
            'Pertanyaan bagus nih, tapi aku belum bisa jawab. Udah aku teruskan ke atasan deh.',
            'Wah ini di luar kemampuan aku. Admin udah aku kabarin, mereka bakal jawab segera kok.',
            'Maaf ya belum bisa bantu. Pertanyaannya udah aku kirim ke yang lebih ahli.',
        ];
        
        return $adminResponses[array_rand($adminResponses)];
    }

    // Method untuk mencari referensi yang relevan tanpa menyebutkan "dataset" atau "FAQ"
    private function findRelevantReferences($question)
    {
        $relevantData = [];
        $questionWords = preg_split('/\s+/', strtolower($question));
        
        foreach ($this->dataset as $data) {
            $dataWords = preg_split('/\s+/', strtolower($data['question']));
            $commonWords = array_intersect($questionWords, $dataWords);
            
            if (count($commonWords) >= 1) {
                similar_text(strtolower($question), strtolower($data['question']), $percent);
                $relevantData[] = [
                    'question' => $data['question'],
                    'answer' => $data['answer'],
                    'tag' => $data['tag'],
                    'similarity' => $percent
                ];
            }
        }
        
        usort($relevantData, function($a, $b) {
            return $b['similarity'] - $a['similarity'];
        });
        
        return array_slice($relevantData, 0, 2); // Hanya 2 referensi teratas
    }

    // **PERBAIKAN**: Response yang lebih manusiawi dan natural dengan error handling yang lebih baik
    private function getHumanLikeResponse($question)
    {
        try {
            // Jika API key tidak valid, langsung return error
            if (empty($this->geminiApiKey) || $this->geminiApiKey === 'YOUR_GEMINI_API_KEY_HERE') {
                return 'error';
            }

            $systemInfo = $this->getSystemContext($question);
            $relevantRefs = $this->findRelevantReferences($question);
            $prompt = $this->buildCasualPrompt($question, $systemInfo, $relevantRefs);
            
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
                    'temperature' => 0.8, // Lebih kreatif dan natural
                    'maxOutputTokens' => 200,
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
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout lebih singkat
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // **PERBAIKAN**: Lebih detail error handling
            if ($curlError) {
                log_message('error', 'Gemini API Curl Error: ' . $curlError);
                return 'error';
            }

            if ($httpCode !== 200) {
                log_message('error', 'Gemini API HTTP Error: ' . $httpCode . ', Response: ' . $response);
                return 'error';
            }

            if (empty($response)) {
                log_message('error', 'Gemini API returned empty response');
                return 'error';
            }

            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'Gemini API JSON decode error: ' . json_last_error_msg());
                return 'error';
            }
            
            // **PERBAIKAN**: Validasi response structure yang lebih ketat
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $rawAnswer = trim($result['candidates'][0]['content']['parts'][0]['text']);
                
                // Validasi apakah jawaban tidak kosong dan bermakna
                if (empty($rawAnswer) || strlen($rawAnswer) < 10) {
                    log_message('info', 'Gemini returned too short or empty answer');
                    return 'error';
                }
                
                // Cek apakah Gemini memberikan jawaban yang menunjukkan ketidaktahuan
                $uncertaintyPatterns = [
                    'saya tidak tahu',
                    'tidak dapat menjawab',
                    'maaf, saya tidak',
                    'kurang informasi',
                    'tidak memiliki informasi'
                ];
                
                $lowerAnswer = strtolower($rawAnswer);
                foreach ($uncertaintyPatterns as $pattern) {
                    if (strpos($lowerAnswer, $pattern) !== false) {
                        log_message('info', 'Gemini expressed uncertainty: ' . $rawAnswer);
                        return 'error';
                    }
                }
                
                return $this->finalizeHumanResponse($rawAnswer);
            }
            
            // Jika struktur response tidak sesuai ekspektasi
            log_message('error', 'Gemini API unexpected response structure: ' . print_r($result, true));
            return 'error';

        } catch (Exception $e) {
            log_message('error', 'Gemini API Exception: ' . $e->getMessage());
            return 'error';
        }
    }

    // Build prompt yang lebih casual dan spontan
    private function buildCasualPrompt($question, $systemInfo, $relevantRefs)
    {
        $basePrompt = "Kamu adalah staff IT di PT. ARMINDO yang ngebantu dengan sistem kinerja pegawai. ";
        $basePrompt .= "Ngomongnya santai, ga formal, kayak ngobrol sama temen. ";
        $basePrompt .= "PENTING: Kalo emang ga tau atau ga yakin jawabannya, bilang aja 'ga tau' atau 'kurang paham' jangan asal jawab. ";
        
        $contextPrompt = "\nKamu ngerti banget soal:\n";
        $contextPrompt .= "- Laporan kerja harian pegawai\n";
        $contextPrompt .= "- Evaluasi kinerja dan penilaian\n";
        $contextPrompt .= "- Rencana kerja dan target\n";
        $contextPrompt .= "- Pengajuan penghargaan atau SP\n";
        
        $referencePrompt = "";
        if (!empty($relevantRefs)) {
            $referencePrompt = "\nPernah jawab yang mirip-mirip:\n";
            foreach (array_slice($relevantRefs, 0, 2) as $ref) {
                $referencePrompt .= "• {$ref['answer']}\n";
            }
        }
        
        $stylePrompt = "\nCara ngomong kamu:\n";
        $stylePrompt .= "- Santai aja, pake 'nih', 'sih', 'deh', 'ya' kayak orang Indonesia\n";
        $stylePrompt .= "- Jangan sebut AI, asisten, atau sistem otomatis\n";
        $stylePrompt .= "- Langsung to the point, ga usah bertele-tele\n";
        $stylePrompt .= "- KALO EMANG GA TAU atau GA YAKIN, bilang aja 'kurang tau nih' atau 'coba tanya atasan'\n";
        $stylePrompt .= "- Jawab pendek aja, 1-2 kalimat cukup\n";
        $stylePrompt .= "- Jangan pake kata formal kayak 'Silakan', 'Mohon', 'Terima kasih'\n\n";
        
        $questionPrompt = "Temen kantor tanya: \"{$question}\"\n\nJawab singkat (kalo ga tau bilang ga tau):";
        
        return $basePrompt . $contextPrompt . $referencePrompt . $stylePrompt . $questionPrompt;
    }

    // Finalisasi response agar spontan kayak orang biasa
    private function finalizeHumanResponse($answer)
    {
        // Hapus kata-kata yang kedengeran AI/robot
        $robotWords = [
            'saya' => 'aku',
            'kami' => 'kita',
            'sistem otomatis' => 'aplikasi',
            'artificial intelligence' => 'program komputer',
            'machine learning' => 'sistem belajar',
            'database' => 'data',
            'silakan' => 'coba',
            'mohon' => 'tolong',
            'terima kasih' => 'makasih',
            'sebagai asisten' => 'sebagai staff IT',
            'dengan hormat' => '',
            'demikian' => 'gitu aja',
            'apabila' => 'kalo',
            'harap' => 'tolong'
        ];
        
        $answer = str_ireplace(array_keys($robotWords), array_values($robotWords), $answer);
        
        // Kasih feeling spontan dengan random starter (cuma kadang-kadang)
        $spontaneousStarters = [
            'Oh iya, ', 'Nah itu, ', 'Wah kalo itu, ', 'Hmm gini, ', 'Oke jadi, '
        ];
        
        $spontaneousMiddle = [
            ' nih, ', ' sih ', ' deh ', ' ya ', ' kan '
        ];
        
        // 40% chance kasih starter spontan
        if (rand(1, 10) <= 4 && !preg_match('/^(oh|nah|wah|hmm|oke|iya|untuk)/i', trim($answer))) {
            $starter = $spontaneousStarters[array_rand($spontaneousStarters)];
            $answer = $starter . lcfirst(trim($answer));
        }
        
        // 30% chance sisip kata spontan di tengah
        if (rand(1, 10) <= 3) {
            $middle = $spontaneousMiddle[array_rand($spontaneousMiddle)];
            $answer = preg_replace('/\. /', $middle . '. ', $answer, 1);
        }
        
        // Ending yang spontan dan variatif (cuma kadang-kadang biar ga monoton)
        $spontaneousEndings = [
            ' Udah gitu aja.', ' Gampang kan?', ' Paham ga?', ' Coba aja dulu.', 
            ' Semoga bener ya.', ' Ada lagi?', ''
        ];
        
        // 35% chance kasih ending
        if (rand(1, 10) <= 3) {
            $ending = $spontaneousEndings[array_rand($spontaneousEndings)];
            $answer = rtrim($answer, '.') . $ending;
        }
        
        return trim($answer);
    }

    // Method untuk membuat jawaban existing jadi lebih spontan
    private function makeMoreCasual($answer, $originalQuestion)
    {
        // Ganti kata formal jadi bahasa sehari-hari
        $formalToCasual = [
            'Silakan pilih' => 'Coba pilih',
            'Silakan' => 'Coba',
            'Mohon' => 'Tolong',
            'Harap bersabar' => 'Sabar ya',
            'Terima kasih' => 'Makasih',
            'Pastikan untuk memeriksa' => 'Cek aja',
            'Anda akan menerima' => 'Nanti ada',
            'begitu laporan Anda disetujui' => 'kalo laporannya udah di-approve',
            'dapat memeriksa status' => 'bisa cek status',
            'tidak bisa diedit lagi' => 'ga bisa diedit lagi',
            'Pastikan laporan Anda' => 'Pastiin laporannya',
            'sebelum disetujui' => 'sebelum di-approve',
            'yang diperlukan' => 'yang perlu',
            'mengirimkannya kembali' => 'kirim lagi',
            'untuk disetujui' => 'buat di-approve'
        ];
        
        foreach ($formalToCasual as $formal => $casual) {
            $answer = str_ireplace($formal, $casual, $answer);
        }
        
        // Tambahin feeling natural random
        $naturalWords = ['nih', 'sih', 'deh', 'ya'];
        
        // 25% chance sisip kata natural
        if (rand(1, 10) <= 2) {
            $word = $naturalWords[array_rand($naturalWords)];
            // Sisip di tengah kalimat
            $answer = preg_replace('/(\w+),/', "$1 $word,", $answer, 1);
        }
        
        return trim($answer);
    }

    // **Method lainnya tetap sama seperti sebelumnya**
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

    private function getSystemContext($question)
    {
        $context = [];
        $question = strtolower($question);
        
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
            return "Kurang tau nih soal itu.";
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
            return "Kurang paham maksudnya.";
        }

        return $answer;
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
            
            log_message('info', 'Pertanyaan baru disimpan ke database: ' . $question);
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
