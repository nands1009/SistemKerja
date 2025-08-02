<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatbotModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'chatbot_dataset';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['question', 'tag', 'answer'];

    // NLP dan Model properties
    private $salam_variations = [
        'hai', 'halo', 'hello', 'helo', 'hallo', 'hi', 'hey', 
        'selamat', 'slamat', 'selmat', 'slmat', 'pagi', 'siang', 'sore', 'malam',
        'assalamualaikum', 'asalamualaikum', 'assalamu', 'asalamu', 'salam'
    ];

    // Jawaban khusus untuk konteks umum
    private $special_answers = [
        "laporan_kerja" => [
            "status" => "Status laporan kerja dapat Anda lihat di menu 'Riwayat Laporan Kerja'. Status akan berubah dari 'Pending' menjadi 'Approved' atau 'Rejected' setelah diproses oleh atasan.",
            "edit" => "Anda tidak dapat mengedit laporan kerja yang sudah mendapatkan approval. Namun, Anda dapat mengedit laporan yang masih berstatus 'Pending'.",
            "hapus" => "Untuk menghapus laporan kerja, buka menu 'Riwayat Laporan Kerja', pilih laporan yang ingin dihapus, dan klik tombol 'Hapus'. Perhatikan bahwa laporan yang sudah diapprove tidak dapat dihapus.",
            "tambah" => "Untuk menambahkan laporan kerja baru, silakan akses menu 'Buat Laporan Kerja' dan isi formulir yang tersedia.",
            "lihat" => "Silakan pilih menu 'Riwayat Laporan Kerja', di sana Anda akan menemukan seluruh laporan kerja yang telah Anda buat sebelumnya."
        ],
        "evaluasi_kinerja" => [
            "status" => "Status evaluasi kinerja dapat Anda lihat di menu 'Evaluasi Kinerja'. Status akan berubah setelah ditinjau oleh atasan.",
            "edit" => "Anda dapat mengedit pengajuan evaluasi kinerja yang masih dalam status 'Draft', tetapi tidak dapat mengedit yang sudah dikirim.",
            "lihat" => "Untuk melihat hasil evaluasi kinerja, silakan akses menu 'Evaluasi Kinerja' dan pilih periode evaluasi yang ingin Anda lihat."
        ],
        "rencana_kerja" => [
            "status" => "Status rencana kerja dapat dilihat di menu 'Riwayat Rencana Kerja'.",
            "edit" => "Anda dapat mengedit rencana kerja yang masih dalam status 'Draft', tetapi tidak dapat mengedit yang sudah disetujui.",
            "tambah" => "Untuk membuat rencana kerja baru, silakan akses menu 'Buat Rencana Kerja' dan ikuti instruksi yang tersedia.",
            "lihat" => "Untuk melihat rencana kerja yang telah dibuat, silakan akses menu 'Riwayat Rencana Kerja'."
        ]
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        // Inisialisasi library NLP jika diperlukan
        $this->initializePython();
    }

    /**
     * Inisialisasi Python dan library NLP
     */
    private function initializePython()
    {
        // Jika menggunakan Python dan library NLP via exec atau API
        // Contoh: PythonBridge, exec, atau panggilan API eksternal
    }

    /**
     * Memproses pertanyaan user
     * @param string $question
     * @return array
     */
    public function processQuestion($question)
    {
        // Preprocessing teks
        $processedQuestion = $this->preprocessText($question);
        
        // Cek apakah ini salam
        if ($this->isSalam($question)) {
            return [
                'tag' => 'Salam',
                'answer' => $this->getSalamResponse($question)
            ];
        }
        
        // Deteksi tag/intent dari pertanyaan
        $tag = $this->detectTag($processedQuestion);
        
        // Dapatkan jawaban berdasarkan tag
        $answer = $this->getAnswer($tag, $question);
        
        return [
            'tag' => $tag,
            'answer' => $answer
        ];
    }

    /**
     * Mendeteksi apakah text adalah salam
     * @param string $text
     * @return boolean
     */
    private function isSalam($text)
    {
        $text = strtolower($text);
        $tokens = explode(' ', $text);
        
        foreach ($tokens as $token) {
            if (in_array($token, $this->salam_variations)) {
                return true;
            }
        }
        
        // Cek untuk frasa dengan salam
        foreach ($this->salam_variations as $salam) {
            if (strpos($text, $salam) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Mendapatkan respons salam yang sesuai
     * @param string $text
     * @return string
     */
    private function getSalamResponse($text)
    {
        $text = strtolower($text);
        $tokens = explode(' ', $text);
        
        if (strpos($text, 'pagi') !== false) {
            return "Selamat pagi! Ada yang bisa saya bantu?";
        } elseif (strpos($text, 'siang') !== false) {
            return "Selamat siang! Ada yang bisa saya bantu?";
        } elseif (strpos($text, 'sore') !== false) {
            return "Selamat sore! Ada yang bisa saya bantu?";
        } elseif (strpos($text, 'malam') !== false) {
            return "Selamat malam! Ada yang bisa saya bantu?";
        } else {
            return "Halo! Ada yang bisa saya bantu?";
        }
    }

    /**
     * Preprocessing text
     * @param string $text
     * @return string
     */
    private function preprocessText($text)
    {
        // Case folding
        $text = strtolower($text);
        
        // Cleaning: hapus tanda baca dan angka
        $text = preg_replace('/[^\w\s]/', ' ', $text);
        $text = preg_replace('/\d+/', ' ', $text);
        
        // Hapus extra whitespace
        $text = preg_replace('/\s+/', ' ', trim($text));
        
        return $text;
    }

    /**
     * Mendeteksi tag/intent dari pertanyaan
     * @param string $processedQuestion
     * @return string
     */
    private function detectTag($processedQuestion)
    {
        // Di sini kita bisa mengimplementasikan logika ML atau rule-based
        // Contoh sederhana dengan rule-based:
        $text = strtolower($processedQuestion);
        
        // Deteksi tag berdasarkan keyword
        if (strpos($text, 'laporan kerja') !== false || 
            strpos($text, 'laporan') !== false) {
            return "laporan_kerja";
        } elseif (strpos($text, 'evaluasi kinerja') !== false || 
                 strpos($text, 'evaluasi') !== false || 
                 strpos($text, 'kinerja') !== false) {
            return "evaluasi_kinerja";
        } elseif (strpos($text, 'rencana kerja') !== false || 
                 strpos($text, 'rencana') !== false) {
            return "rencana_kerja";
        }
        
        // Untuk implementasi sebenarnya, disarankan menggunakan model ML yang dilatih
        // di file Python atau layanan eksternal
        
        // Fallback ke database untuk mencari kecocokan
        return $this->findTagFromDatabase($processedQuestion);
    }

    /**
     * Mencari tag dari database
     * @param string $processedQuestion
     * @return string
     */
    private function findTagFromDatabase($processedQuestion)
    {
        // Query database untuk mencari pertanyaan serupa
        $query = $this->db->table($this->table)
                        ->like('question', $processedQuestion)
                        ->limit(1)
                        ->get();
        
        if ($query->getNumRows() > 0) {
            $row = $query->getRow();
            return $row->tag;
        }
        
        // Fallback jika tidak ditemukan
        return "unknown";
    }

    /**
     * Mendapatkan jawaban berdasarkan tag
     * @param string $tag
     * @param string $question
     * @return string
     */
    private function getAnswer($tag, $question)
    {
        // Cek jawaban khusus untuk konteks umum
        if (isset($this->special_answers[$tag])) {
            $question_lower = strtolower($question);
            
            // Cek kata kunci dalam pertanyaan
            foreach ($this->special_answers[$tag] as $keyword => $answer) {
                if (strpos($question_lower, $keyword) !== false) {
                    return $answer;
                }
            }
            
            // Cek konteks spesifik berdasarkan kata kunci
            if ($tag == "laporan_kerja") {
                $statusKeywords = ["status", "pending", "approve", "diapprove", "diterima", "ditolak", "rejected"];
                $editKeywords = ["edit", "ubah", "perbarui", "update", "modifikasi"];
                $deleteKeywords = ["hapus", "delete", "remove"];
                $addKeywords = ["tambah", "buat", "add", "new"];
                $viewKeywords = ["lihat", "cari", "temukan", "view", "history", "riwayat"];
                
                foreach ($statusKeywords as $keyword) {
                    if (strpos($question_lower, $keyword) !== false) {
                        return $this->special_answers[$tag]["status"];
                    }
                }
                
                foreach ($editKeywords as $keyword) {
                    if (strpos($question_lower, $keyword) !== false) {
                        return $this->special_answers[$tag]["edit"];
                    }
                }
                
                foreach ($deleteKeywords as $keyword) {
                    if (strpos($question_lower, $keyword) !== false) {
                        return $this->special_answers[$tag]["hapus"];
                    }
                }
                
                foreach ($addKeywords as $keyword) {
                    if (strpos($question_lower, $keyword) !== false) {
                        return $this->special_answers[$tag]["tambah"];
                    }
                }
                
                foreach ($viewKeywords as $keyword) {
                    if (strpos($question_lower, $keyword) !== false) {
                        return $this->special_answers[$tag]["lihat"];
                    }
                }
            }
            
            // Default: return yang pertama
            $keys = array_keys($this->special_answers[$tag]);
            return $this->special_answers[$tag][$keys[0]];
        }
        
        // Cari dari database
        $query = $this->db->table($this->table)
                        ->where('tag', $tag)
                        ->limit(1)
                        ->get();
        
        if ($query->getNumRows() > 0) {
            $row = $query->getRow();
            return $row->answer;
        }
        
        // Fallback jika tidak ditemukan
        return "Maaf, saya tidak memahami pertanyaan Anda. Silakan coba pertanyaan lain.";
    }
}