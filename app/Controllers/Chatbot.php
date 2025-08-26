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
            // LAPORAN KERJA
            'laporan_kerja' => [
                'description' => 'Sistem manajemen laporan kerja harian pegawai',
                'functions' => [
                    'buat_laporan' => 'Pegawai dapat membuat laporan kerja harian melalui form laporan kerja di sistem',
                    'cara_edit_laporan_kerja' => 'Untuk mengedit laporan kerja, silakan buka menu Riwayat Laporan Kerja di sistem, kemudian pilih laporan yang ingin diedit',
                    'kirim_laporan' => 'Pegawai dapat mengirimkan laporan yang telah dibuat ke manajer untuk ditinjau',
                    'edit_laporan' => 'Pegawai dapat mengedit laporan yang ditolak oleh manajer, namun tidak dapat mengedit laporan yang sudah disetujui',
                    'riwayat_laporan' => 'Menu Riwayat Laporan Kerja untuk melihat seluruh laporan yang telah dibuat sebelumnya',
                    'status_laporan' => 'Sistem menampilkan status: Pending, Approved, atau Rejected',
                    'notifikasi' => 'Pegawai menerima notifikasi ketika laporan disetujui atau ditolak',
                    'approval_manajer' => 'Manajer dapat melakukan persetujuan atau penolakan laporan kerja pegawai dalam divisi mereka',
                    'revisi_laporan' => 'Untuk revisi laporan yang sudah disetujui, buatlah laporan baru dengan menambahkan kata "Revisi" pada judul'
                ],
                'required_info' => 'Nama pegawai, tanggal, rincian pekerjaan, hasil pekerjaan, kendala yang dihadapi, serta solusi yang diterapkan',
                'access_level' => [
                    'pegawai' => 'Hanya dapat mengakses laporan kerja miliknya sendiri',
                    'manajer' => 'Dapat melihat laporan kerja pegawai dalam divisi mereka',
                    'admin' => 'Dapat melihat semua laporan kerja pegawai',
                    'direksi' => 'Dapat melihat riwayat laporan kerja dari seluruh pegawai'
                ]
            ],

            // RENCANA KERJA
            'rencana_kerja' => [
                'description' => 'Sistem perencanaan dan manajemen target kerja',
                'functions' => [
                    'buat_rencana' => 'Pegawai dapat mengakses fitur rencana kerja, memasukkan target pekerjaan, dan menyimpannya',
                    'edit_rencana' => 'Pegawai dapat mengedit atau menghapus rencana kerja selama belum digunakan sebagai referensi laporan',
                    'filter_tanggal' => 'Dapat memfilter rencana kerja berdasarkan rentang tanggal tertentu',
                    'atur_jadwal' => 'Mengatur ulang jadwal melalui menu Edit pada rencana kerja yang ingin diubah'
                ],
                'access_level' => [
                    'pegawai' => 'Dapat membuat rencana kerja dan mencari rencana kerja yang sudah dibuat',
                    'manajer' => 'Dapat membuat rencana kerja dan melihat rencana kerja pegawai dalam divisi mereka, tidak dapat melihat rencana kerja pegawai di divisi lain'
                ]
            ],

            // EVALUASI KINERJA & PENILAIAN
            'evaluasi_kinerja' => [
                'description' => 'Sistem penilaian dan evaluasi kinerja pegawai',
                'functions' => [
                    'penilaian_pegawai' => 'Manajer dapat menilai pegawai dalam divisi mereka menggunakan fitur evaluasi',
                    'penilaian_manajer' => 'Direksi dapat menilai kinerja manajer dalam organisasi',
                    'riwayat_penilaian' => 'Pegawai dapat mengakses halaman riwayat penilaian untuk melihat lampiran penilaian milik sendiri',
                    'periode_penilaian' => 'Admin dapat membuka periode penilaian melalui menu pengaturan waktu',
                    'hasil_evaluasi' => 'Hasil evaluasi kinerja dapat diakses setelah semua tahap review oleh manajer dan direksi selesai',
                    'laporan_evaluasi' => 'Direksi dapat melihat laporan rangkuman evaluasi kinerja pegawai dan manajer yang dikompilasi oleh HRD'
                ],
                'kriteria_penilaian' => [
                    'pegawai' => 'Produktivitas, keterampilan teknis, kerja sama tim, dan inisiatif dalam menyelesaikan tugas',
                    'manajer' => 'Efektivitas kepemimpinan, kualitas keputusan strategis, serta kemampuan dalam mengelola tim dan proyek'
                ],
                'access_level' => [
                    'pegawai' => 'Hanya dapat menerima hasil penilaian, tidak dapat menilai rekan kerja dalam divisi',
                    'manajer' => 'Dapat menilai pegawai dalam divisi mereka, tidak dapat menilai pegawai di luar divisi',
                    'direksi' => 'Dapat menilai kinerja manajer'
                ]
            ],

            // PENGAJUAN PENGHARGAAN & SP
            'pengajuan_penghargaan' => [
                'description' => 'Sistem pengajuan penghargaan dan surat peringatan (SP)',
                'functions' => [
                    'ajukan_penghargaan' => 'Manajer dapat mengisi form pengajuan penghargaan di halaman evaluasi kinerja',
                    'ajukan_sp' => 'Manajer dapat mengajukan SP kepada HRD untuk pegawai yang berkinerja buruk',
                    'riwayat_pengajuan' => 'Menu Riwayat Penghargaan untuk melihat daftar lengkap pengajuan yang telah diajukan dan diterima',
                    'status_pengajuan' => 'Manajer akan menerima pemberitahuan mengenai jawaban persetujuan yang diajukan ke HRD',
                    'notifikasi_hasil' => 'Sistem mengirimkan pemberitahuan resmi ketika pegawai menerima penghargaan atau SP'
                ],
                'rules' => [
                    'edit_sp' => 'Setelah pengajuan SP dikirim, tidak dapat melakukan perubahan. Jika revisi diperlukan, harus mengajukan ulang',
                    'approval_hrd' => 'Semua pengajuan penghargaan dan SP harus disetujui oleh HRD'
                ]
            ],

            // SISTEM NLP (Natural Language Processing)
            'pengaturan_nlp' => [
                'description' => 'Sistem NLP untuk membantu menyelesaikan masalah teknis',
                'functions' => [
                    'analisis_masalah' => 'NLP dapat menganalisis deskripsi masalah teknis dan memberikan solusi berdasarkan data historis',
                    'rekomendasi_evaluasi' => 'NLP menganalisis pola kerja pegawai dan memberikan rekomendasi berbasis data laporan serta hasil penilaian sebelumnya',
                    'deteksi_anomali' => 'Manajer dapat menggunakan NLP untuk mendeteksi anomali dalam laporan dan mengidentifikasi area yang perlu ditingkatkan',
                    'konfigurasi_admin' => 'Admin dapat mengonfigurasi sistem NLP melalui halaman manajemen NLP'
                ],
                'access_level' => [
                    'pegawai' => 'Dapat menggunakan sistem NLP untuk membantu menyelesaikan masalah teknis',
                    'manajer' => 'Dapat menggunakan NLP untuk analisis laporan dan deteksi anomali',
                    'admin' => 'Dapat mengonfigurasi dan mengatur parameter sistem NLP',
                    'direksi' => 'Dapat menggunakan sistem NLP untuk menyelesaikan masalah teknis'
                ]
            ],

            // MANAJEMEN WAKTU & PERIODE
            'waktu_penilaian' => [
                'description' => 'Sistem pengaturan periode dan waktu penilaian',
                'functions' => [
                    'buka_periode' => 'Admin dapat membuka periode penilaian melalui menu pengaturan waktu',
                    'atur_waktu' => 'Admin mengisi form dengan tanggal dan waktu mulai serta akhir penilaian',
                    'notifikasi_periode' => 'Pegawai menerima notifikasi ketika waktu penilaian sudah dimulai'
                ],
                'rules' => [
                    'akses_penilaian' => 'Pegawai hanya dapat melakukan penilaian setelah menerima notifikasi bahwa periode penilaian sudah dimulai'
                ]
            ]
        ],

        // COMMON PROCESSES yang diperbaiki
        'common_processes' => [
            'cara_input_laporan' => 'Login → Menu Laporan Kerja → Isi Form (nama, tanggal, rincian pekerjaan, hasil, kendala, solusi) → Kirim ke Manajer',
            'cara_cek_status_laporan' => 'Menu Riwayat Laporan Kerja → Pilih laporan → Lihat status (Pending/Approved/Rejected)',
            'cara_edit_laporan_ditolak' => 'Menu Riwayat → Pilih laporan yang ditolak → Edit sesuai catatan → Kirim ulang',
            'cara_buat_rencana_kerja' => 'Menu Rencana Kerja → Buat Baru → Masukkan target pekerjaan → Simpan',
            'cara_lihat_hasil_penilaian' => 'Menu Riwayat Penilaian → Lihat lampiran penilaian milik sendiri',
            'cara_ajukan_penghargaan' => 'Halaman Evaluasi Kinerja → Isi Form Pengajuan Penghargaan → Kirim ke HRD',
            'cara_gunakan_nlp' => 'Input deskripsi masalah teknis → Sistem NLP mencari solusi berdasarkan data historis'
        ],

        // USER ROLES yang lebih detail
        'user_roles' => [
            'pegawai' => [
                'dapat' => ['buat laporan kerja', 'buat rencana kerja', 'lihat riwayat penilaian sendiri', 'gunakan NLP'],
                'tidak_dapat' => ['edit laporan yang approved', 'hapus laporan yang sudah dikirim', 'lihat laporan orang lain', 'menilai rekan kerja']
            ],
            'manajer' => [
                'dapat' => ['approve/reject laporan pegawai divisi', 'lihat rencana kerja pegawai divisi', 'nilai pegawai divisi', 'ajukan penghargaan/SP', 'gunakan NLP untuk analisis'],
                'tidak_dapat' => ['edit laporan pegawai', 'lihat data pegawai divisi lain', 'nilai pegawai luar divisi']
            ],
            'admin' => [
                'dapat' => ['lihat semua laporan kerja', 'atur periode penilaian', 'konfigurasi sistem NLP'],
                'fokus' => 'manajemen sistem dan pengaturan teknis'
            ],
            'direksi' => [
                'dapat' => ['lihat riwayat laporan semua pegawai', 'nilai kinerja manajer', 'lihat laporan evaluasi komprehensif', 'gunakan NLP'],
                'fokus' => 'oversight dan evaluasi strategis'
            ]
        ]
    ];

    protected $keywordResponses = [
        'pagi' => 'Selamat pagi! Ada yang dapat saya bantu?',
        'siang' => 'Selamat siang! Ada yang bisa dibantu?',
        'sore' => 'Selamat sore! Bagaimana saya dapat membantu Anda?',
        'malam' => 'Selamat malam! Ada yang dapat saya bantu?',
        'halo' => 'Halo! Ada yang dapat saya bantu?',
        'hai' => 'Hai! Silakan sampaikan pertanyaan Anda.',
        'hi' => 'Hi! Ada yang ingin ditanyakan?'
    ];

    // Responses yang sopan dan profesional
    protected $professionalResponses = [
        'confusion' => [
            'Mohon maaf, saya kurang memahami maksud pertanyaan Anda. Bisakah dijelaskan lebih detail?',
            'Saya belum sepenuhnya memahami pertanyaan tersebut. Mohon dapat diperjelas lagi.',
            'Maaf, saya memerlukan penjelasan lebih lanjut untuk dapat memberikan jawaban yang tepat.',
            'Mohon maaf, bisakah Anda menjelaskan pertanyaan dengan lebih spesifik?'
        ],
        'thinking' => [
            'Mohon tunggu sebentar, saya sedang mencari informasi yang tepat.',
            'Izinkan saya memeriksa informasi tersebut.',
            'Sebentar, saya sedang mengecek data yang relevan.',
            'Mohon bersabar, saya sedang mencari jawaban yang sesuai.'
        ],
        'closing' => [
            'Semoga informasi ini membantu.',
            'Apakah ada pertanyaan lain yang dapat saya bantu?',
            'Silakan bertanya jika masih ada yang perlu diklarifikasi.',
            'Apakah penjelasan ini sudah cukup jelas?'
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
            return $this->response->setJSON(['message' => 'Mohon sampaikan pertanyaan yang ingin Anda tanyakan.']);
        }

        // Cek kata kunci sapaan
        foreach ($this->keywordResponses as $keyword => $response) {
            if ($input === $keyword || strpos($input, $keyword) !== false) {
                $this->saveToAnsweredQuestions($input, $response, 'greeting');
                return $this->response->setJSON(['message' => $response]);
            }
        }

        // PRIORITAS 1: Cek jawaban dari database answered questions dulu (pertanyaan yang sering ditanya)
        $dbAnswer = $this->findAnswerFromDatabase($input);
        if ($dbAnswer) {
            $this->incrementQuestionFrequency($input);
            return $this->response->setJSON(['message' => $dbAnswer]);
        }

        // PRIORITAS 2: Cek kecocokan persis di dataset CSV
        $exactMatch = $this->findExactMatch($input);
        if ($exactMatch) {
            $tag = $this->findTagFromExactMatch($input);
            $this->saveToAnsweredQuestions($input, $exactMatch, $tag);
            return $this->response->setJSON(['message' => $exactMatch]);
        }

        // PRIORITAS 3: Cek kecocokan similar di dataset CSV
        $similarMatch = $this->findSimilarMatchInDataset($input);
        if ($similarMatch && $similarMatch['score'] > 70) {
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $similarMatch['tag']);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // PRIORITAS 4: Menggunakan Naive Bayes untuk prediksi
        $predictedTag = $this->predictTag($input);
        $tagConfidence = $this->calculateTagConfidence($input, $predictedTag);

        if ($tagConfidence >= 0.4) {
            $answer = $this->getAnswerByTag($predictedTag, $input);
            if (!$this->isGenericErrorResponse($answer)) {
                $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
                return $this->response->setJSON(['message' => $answer]);
            }
        }

        // PRIORITAS 5: Coba Gemini AI sebagai backup
        $contextualAnswer = $this->getProfessionalAIResponse($input);
        if ($contextualAnswer && $contextualAnswer !== 'error' && !empty(trim($contextualAnswer))) {
            $this->saveToAnsweredQuestions($input, $contextualAnswer, 'contextual');
            return $this->response->setJSON(['message' => $contextualAnswer]);
        }

        // LANGKAH TERAKHIR: Kirim ke admin jika semua metode gagal
        $this->saveUnansweredQuestion($input);
        $adminNotificationResponse = $this->getAdminNotificationResponse($input);
        
        return $this->response->setJSON(['message' => $adminNotificationResponse]);
    }

    // Method baru untuk mencari jawaban dari database answered questions
    private function findAnswerFromDatabase($input)
    {
        // Cari exact match dulu
        $exactResult = $this->answeredQuestionModel->where('LOWER(question)', strtolower($input))->first();
        if ($exactResult) {
            return $exactResult['answer'];
        }

        // Cari similar match dengan threshold lebih ketat
        $allQuestions = $this->answeredQuestionModel->findAll();
        $bestScore = 0;
        $bestAnswer = null;

        foreach ($allQuestions as $questionData) {
            similar_text(strtolower($input), strtolower($questionData['question']), $percent);
            if ($percent > $bestScore && $percent >= 85) { // Threshold tinggi untuk akurasi
                $bestScore = $percent;
                $bestAnswer = $questionData['answer'];
            }
        }

        return $bestAnswer;
    }

    // Method untuk increment frequency pada pertanyaan yang sudah dijawab
    private function incrementQuestionFrequency($question)
    {
        $existingQuestion = $this->answeredQuestionModel->where('LOWER(question)', strtolower($question))->first();
        if ($existingQuestion) {
            $this->answeredQuestionModel->update($existingQuestion['id'], [
                'frequency' => $existingQuestion['frequency'] + 1,
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Improved similar match untuk dataset CSV dengan threshold yang lebih baik
    private function findSimilarMatchInDataset($input)
    {
        $bestScore = 0;
        $bestAnswer = null;
        $bestTag = 'unknown';

        foreach ($this->dataset as $data) {
            // Gunakan kombinasi similar_text dan word matching
            similar_text(strtolower($input), strtolower($data['question']), $percentText);
            
            // Tambahan: hitung kesamaan kata-kata kunci
            $inputWords = array_filter(preg_split('/\s+/', strtolower($input)));
            $questionWords = array_filter(preg_split('/\s+/', strtolower($data['question'])));
            $commonWords = array_intersect($inputWords, $questionWords);
            $wordMatchPercent = (count($commonWords) / max(count($inputWords), count($questionWords))) * 100;
            
            // Kombinasi score
            $combinedScore = ($percentText * 0.7) + ($wordMatchPercent * 0.3);
            
            if ($combinedScore > $bestScore) {
                $bestScore = $combinedScore;
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

    // Method untuk cek apakah response adalah error generic
    private function isGenericErrorResponse($response)
    {
        $errorPatterns = [
            'kurang memahami',
            'belum memahami',
            'tidak dapat menjawab',
            'maaf, saya tidak',
            'mohon maaf, saya kurang',
            'memerlukan penjelasan lebih'
        ];
        
        $lowerResponse = strtolower($response);
        foreach ($errorPatterns as $pattern) {
            if (strpos($lowerResponse, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    // Method baru untuk response notifikasi admin yang sopan
    private function getAdminNotificationResponse($input)
    {
        $adminResponses = [
            'Mohon maaf, saya belum dapat memberikan jawaban yang tepat untuk pertanyaan tersebut. Pertanyaan Anda telah diteruskan kepada tim teknis untuk ditinjau lebih lanjut.',
            'Terima kasih atas pertanyaan Anda. Saat ini saya belum memiliki informasi yang cukup untuk menjawab. Tim kami akan menindaklanjuti pertanyaan ini segera.',
            'Saya mohon maaf belum dapat memberikan jawaban yang memuaskan. Pertanyaan Anda telah dicatat dan akan dijawab oleh tim yang lebih kompeten.',
            'Maaf atas ketidaknyamanannya. Pertanyaan Anda memerlukan penanganan khusus dan telah diteruskan kepada divisi terkait.',
            'Terima kasih atas kesabaran Anda. Pertanyaan ini telah diteruskan kepada tim ahli untuk mendapatkan jawaban yang akurat.'
        ];
        
        return $adminResponses[array_rand($adminResponses)];
    }

    // AI Response yang lebih professional
    private function getProfessionalAIResponse($question)
    {
        try {
            if (empty($this->geminiApiKey) || $this->geminiApiKey === 'YOUR_GEMINI_API_KEY_HERE') {
                return 'error';
            }

            $systemInfo = $this->getSystemContext($question);
            $relevantRefs = $this->findRelevantReferences($question);
            $prompt = $this->buildProfessionalPrompt($question, $systemInfo, $relevantRefs);
            
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
                    'temperature' => 0.3, // Lebih konsisten dan formal
                    'maxOutputTokens' => 250,
                    'topP' => 0.8,
                    'topK' => 20
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

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
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $rawAnswer = trim($result['candidates'][0]['content']['parts'][0]['text']);
                
                if (empty($rawAnswer) || strlen($rawAnswer) < 10) {
                    log_message('info', 'Gemini returned too short or empty answer');
                    return 'error';
                }
                
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
                
                return $this->finalizeProfessionalResponse($rawAnswer);
            }
            
            log_message('error', 'Gemini API unexpected response structure: ' . print_r($result, true));
            return 'error';

        } catch (Exception $e) {
            log_message('error', 'Gemini API Exception: ' . $e->getMessage());
            return 'error';
        }
    }

    // Build prompt yang professional
    private function buildProfessionalPrompt($question, $systemInfo, $relevantRefs)
    {
        $basePrompt = "Anda adalah asisten customer service profesional untuk sistem manajemen kinerja PT. ARMINDO. ";
        $basePrompt .= "Gunakan bahasa Indonesia yang sopan, formal, dan profesional dalam setiap respons. ";
        $basePrompt .= "PENTING: Jika tidak yakin dengan jawaban, lebih baik mengakui ketidaktahuan daripada memberikan informasi yang tidak akurat. ";
        
        $contextPrompt = "\nAnda memiliki pengetahuan mendalam tentang:\n";
        $contextPrompt .= "- Sistem laporan kerja harian pegawai\n";
        $contextPrompt .= "- Proses evaluasi kinerja dan penilaian\n";
        $contextPrompt .= "- Manajemen rencana kerja dan target\n";
        $contextPrompt .= "- Prosedur pengajuan penghargaan atau surat peringatan\n";
        
        $referencePrompt = "";
        if (!empty($relevantRefs)) {
            $referencePrompt = "\nInformasi referensi yang relevan:\n";
            foreach (array_slice($relevantRefs, 0, 2) as $ref) {
                $referencePrompt .= "• {$ref['answer']}\n";
            }
        }
        
        $stylePrompt = "\nPedoman komunikasi:\n";
        $stylePrompt .= "- Gunakan bahasa yang sopan dan profesional\n";
        $stylePrompt .= "- Berikan informasi yang akurat dan jelas\n";
        $stylePrompt .= "- Jika tidak yakin, katakan 'Mohon maaf, saya perlu informasi lebih lanjut untuk hal tersebut'\n";
        $stylePrompt .= "- Jawaban singkat namun informatif (2-3 kalimat)\n";
        $stylePrompt .= "- Gunakan kata sapaan seperti 'Mohon maaf', 'Silakan', 'Terima kasih'\n";
        $stylePrompt .= "- Hindari bahasa gaul atau tidak formal\n\n";
        
        $questionPrompt = "Pertanyaan dari pengguna: \"{$question}\"\n\nBerikan jawaban yang profesional dan sopan:";
        
        return $basePrompt . $contextPrompt . $referencePrompt . $stylePrompt . $questionPrompt;
    }

    // Finalisasi response agar tetap professional
    private function finalizeProfessionalResponse($answer)
    {
        // Perbaiki kata-kata yang kurang formal
        $informalToFormal = [
            'aku' => 'saya',
            'kamu' => 'Anda',
            'kita' => 'kami',
            'gimana' => 'bagaimana',
            'udah' => 'sudah',
            'belum tau' => 'belum mengetahui',
            'ga bisa' => 'tidak dapat',
            'ga tau' => 'tidak mengetahui',
            'bisa ga' => 'apakah bisa',
            'kayak' => 'seperti',
            'banget' => 'sekali',
            'emang' => 'memang',
            'kalo' => 'jika',
            'nih' => '',
            'sih' => '',
            'deh' => '',
            'dong' => '',
            'lagi' => 'sedang'
        ];
        
        foreach ($informalToFormal as $informal => $formal) {
            $answer = str_ireplace($informal, $formal, $answer);
        }
        
        // Pastikan diawali dengan sapaan sopan jika belum ada
        $politeStarters = ['mohon maaf', 'silakan', 'terima kasih', 'selamat', 'untuk'];
        $hasPoliteStarter = false;
        
        foreach ($politeStarters as $starter) {
            if (stripos(trim($answer), $starter) === 0) {
                $hasPoliteStarter = true;
                break;
            }
        }
        
        // 30% chance tambahkan starter sopan jika belum ada
        if (!$hasPoliteStarter && rand(1, 10) <= 3) {
            $starters = ['Untuk hal tersebut, ', 'Mengenai pertanyaan Anda, ', 'Berdasarkan sistem kami, '];
            $starter = $starters[array_rand($starters)];
            $answer = $starter . lcfirst(trim($answer));
        }
        
        // Pastikan diakhiri dengan tanda baca yang benar
        $answer = rtrim($answer);
        if (!in_array(substr($answer, -1), ['.', '!', '?'])) {
            $answer .= '.';
        }
        
        // 25% chance tambahkan closing sopan
        if (rand(1, 10) <= 2) {
            $endings = [' Semoga informasi ini membantu.', ' Apakah ada yang perlu dijelaskan lebih lanjut?'];
            $ending = $endings[array_rand($endings)];
            $answer = rtrim($answer, '.') . $ending;
        }
        
        return trim($answer);
    }

    // Method untuk mencari referensi yang relevan
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
        
        return array_slice($relevantData, 0, 3);
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

    private function getSystemContext($question)
    {
        $context = [];
        $question = strtolower($question);
        
        if (strpos($question, 'laporan') !== false || strpos($question, 'report') !== false) {
            $context['feature'] = 'laporan_kerja';
        } elseif (strpos($question, 'kinerja') !== false || strpos($question, 'evaluasi') !== false) {
            $context['feature'] = 'evaluasi_kinerja';
        } elseif (strpos($question, 'rencana') !== false || strpos($question, 'target') !== false) {
            $context['feature'] = 'rencana_kerja';
        } elseif (strpos($question, 'penghargaan') !== false || strpos($question, 'sp') !== false) {
            $context['feature'] = 'pengajuan_penghargaan';
        } elseif (strpos($question, 'penilaian') !== false || strpos($question, 'periode') !== false) {
            $context['feature'] = 'waktu_penilaian';
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

    private function getAnswerByTag($tag, $input)
    {
        if (!isset($this->tagAnswers[$tag])) {
            return "Mohon maaf, saya memerlukan informasi lebih lanjut untuk pertanyaan tersebut.";
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

        $answer = ($bestScore > 40) ? $bestAnswer : $defaultAnswer;

        if (empty($answer)) {
            return "Mohon maaf, saya belum dapat memberikan jawaban yang tepat untuk hal tersebut.";
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

    // Method tambahan untuk menambah pertanyaan manual dari admin
    public function addManualAnswer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $question = trim($this->request->getPost('question'));
        $answer = trim($this->request->getPost('answer'));
        $tag = trim($this->request->getPost('tag')) ?: 'general';

        if (empty($question) || empty($answer)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pertanyaan dan jawaban harus diisi.']);
        }

        try {
            // Simpan ke answered questions
            $this->answeredQuestionModel->save([
                'question' => $question,
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);

            // Update status jika ada di unanswered questions
            $unansweredQuestion = $this->unansweredQuestionModel->where('question', $question)->first();
            if ($unansweredQuestion) {
                $this->unansweredQuestionModel->update($unansweredQuestion['id'], [
                    'status' => 'answered',
                    'answer' => $answer,
                    'tag' => $tag
                ]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Pertanyaan dan jawaban berhasil ditambahkan.']);

        } catch (Exception $e) {
            log_message('error', 'Error adding manual answer: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.']);
        }
    }

    // Method untuk update jawaban yang sudah ada
    public function updateAnswer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $id = $this->request->getPost('id');
        $answer = trim($this->request->getPost('answer'));
        $tag = trim($this->request->getPost('tag'));

        if (empty($id) || empty($answer)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID dan jawaban harus diisi.']);
        }

        try {
            $this->answeredQuestionModel->update($id, [
                'answer' => $answer,
                'tag' => $tag ?: 'general'
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Jawaban berhasil diperbarui.']);

        } catch (Exception $e) {
            log_message('error', 'Error updating answer: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui data.']);
        }
    }

    // Method untuk menghapus pertanyaan dan jawaban
    public function deleteAnswer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $id = $this->request->getPost('id');

        if (empty($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID harus diisi.']);
        }

        try {
            $this->answeredQuestionModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Pertanyaan dan jawaban berhasil dihapus.']);

        } catch (Exception $e) {
            log_message('error', 'Error deleting answer: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }
}
