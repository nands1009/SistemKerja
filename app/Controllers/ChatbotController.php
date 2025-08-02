<?php

namespace App\Controllers;

use App\Models\ChatbotModel;
use CodeIgniter\Controller;
use App\Models\AdminModel;

class ChatbotController extends Controller
{
    protected $chatbotModel;

    public function __construct()
    {
        $this->chatbotModel = new ChatbotModel();
    }

    public function index()
    {
        return view('chatbot-view');
    }
    
    // Menangani pertanyaan dari user
    public function chat()
    {
        $question = $this->request->getPost('question');
        $questionId = $this->request->getPost('id');

        if (empty($question)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Pertanyaan tidak boleh kosong!'
            ]);
        }

        // STEP 1: Cari jawaban yang sudah ada di database
        $existingAnswer = $this->searchAnswer($question);
        
        if ($existingAnswer) {
            // Jika menemukan jawaban, langsung return
            return $this->response->setJSON([
                'status' => 'answered',
                'answer' => $existingAnswer['admin_answer'],
                'question_id' => $questionId
            ]);
        }

        // STEP 2: Jika tidak ada jawaban, cek apakah pertanyaan ini sudah pernah ditanyakan
        $existingQuestion = $this->chatbotModel->where('question_id', $questionId)->first();

        if (!$existingQuestion) {
            // Simpan pertanyaan baru
            $this->chatbotModel->save([
                'question' => $question,
                'status' => 'waiting_for_admin',
                'question_id' => $questionId,
                'answer' => 'Belum dijawab'
            ]);
        }

        // Return status menunggu admin
        return $this->response->setJSON([
            'status' => 'waiting_for_admin',
            'answer' => 'Pertanyaan Anda sedang diproses oleh admin...',
            'question_id' => $questionId
        ]);
    }

    // Fungsi untuk mencari jawaban di database
    private function searchAnswer($userQuestion)
    {
        // Normalize pertanyaan user
        $normalizedQuestion = $this->normalizeText($userQuestion);
        
        // Method 1: Exact Match (persis sama)
        $exactMatch = $this->findExactMatch($normalizedQuestion);
        if ($exactMatch) {
            return $exactMatch;
        }
        
        // Method 2: Similar Match (mirip)
        $similarMatch = $this->findSimilarMatch($normalizedQuestion);
        if ($similarMatch) {
            return $similarMatch;
        }
        
        // Method 3: Keyword Match (kata kunci)
        $keywordMatch = $this->findKeywordMatch($normalizedQuestion);
        if ($keywordMatch) {
            return $keywordMatch;
        }
        
        return null;
    }

    // Cari exact match
    private function findExactMatch($normalizedQuestion)
    {
        $query = $this->chatbotModel
            ->where('LOWER(TRIM(question))', $normalizedQuestion)
            ->where('status', 'answered')
            ->where('admin_answer !=', '')
            ->first();
            
        return $query;
    }

    // Cari similar match (toleransi perbedaan kecil)
    private function findSimilarMatch($normalizedQuestion)
    {
        // Ambil semua pertanyaan yang sudah dijawab
        $answeredQuestions = $this->chatbotModel
            ->where('status', 'answered')
            ->where('admin_answer !=', '')
            ->findAll();
        
        foreach ($answeredQuestions as $q) {
            $dbQuestion = $this->normalizeText($q['question']);
            
            // Hitung similarity score
            $similarity = $this->calculateSimilarity($normalizedQuestion, $dbQuestion);
            
            // Jika similarity > 80%, anggap match
            if ($similarity > 0.8) {
                return $q;
            }
        }
        
        return null;
    }

    // Cari berdasarkan keyword
    private function findKeywordMatch($normalizedQuestion)
    {
        // Ekstrak keywords dari pertanyaan
        $keywords = $this->extractKeywords($normalizedQuestion);
        
        if (empty($keywords)) {
            return null;
        }
        
        // Buat query dengan LIKE untuk setiap keyword
        $builder = $this->chatbotModel->builder();
        
        $builder->where('status', 'answered')
                ->where('admin_answer !=', '');
        
        // Cari pertanyaan yang mengandung semua keywords
        foreach ($keywords as $keyword) {
            $builder->like('LOWER(question)', $keyword);
        }
        
        $result = $builder->get()->getRowArray();
        
        return $result;
    }

    // Normalize text (hapus special char, lowercase, trim)
    private function normalizeText($text)
    {
        $text = strtolower($text);                // Lowercase
        $text = preg_replace('/[^a-z0-9\s]/u', '', $text);  // Hapus special char
        $text = trim($text);                      // Trim whitespace
        $text = preg_replace('/\s+/', ' ', $text); // Normalize spaces
        return $text;
    }

    // Hitung similarity score sederhana
    private function calculateSimilarity($str1, $str2)
    {
        // Jika salah satu string kosong
        if (empty($str1) || empty($str2)) {
            return 0;
        }
        
        // Jika sama persis
        if ($str1 === $str2) {
            return 1;
        }
        
        // Tokenize
        $tokens1 = explode(' ', $str1);
        $tokens2 = explode(' ', $str2);
        
        // Hitung berapa banyak token yang sama
        $commonTokens = array_intersect($tokens1, $tokens2);
        $totalTokens = array_unique(array_merge($tokens1, $tokens2));
        
        if (count($totalTokens) == 0) {
            return 0;
        }
        
        // Return ratio of common tokens
        return count($commonTokens) / count($totalTokens);
    }

    // Ekstrak keywords penting dari text
    private function extractKeywords($text)
    {
        // Daftar stopwords bahasa Indonesia yang akan diabaikan
        $stopwords = ['yang', 'dan', 'di', 'ke', 'dari', 'atau', 'dengan', 'untuk', 'pada', 'adalah', 'ini', 'itu'];
        
        // Tokenize
        $words = explode(' ', $text);
        
        // Filter stopwords dan kata pendek
        $keywords = array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });
        
        return array_values($keywords);
    }

    // Mendapatkan jawaban terbaru
    public function getLatestAnswer()
    {
        $questionId = $this->request->getGet('questionId');

        if (!$questionId) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Question ID tidak ditemukan.'
            ]);
        }

        $question = $this->chatbotModel->where('question_id', $questionId)->first();

        if (!$question) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Pertanyaan tidak ditemukan.'
            ]);
        }

        if ($question['status'] == 'answered' && !empty($question['admin_answer'])) {
            return $this->response->setJSON([
                'status' => 'answered',
                'answer' => $question['admin_answer'],
                'questionId' => $questionId
            ]);
        }

        return $this->response->setJSON([
            'status' => 'waiting_for_admin',
            'answer' => 'Jawaban sedang ditunggu dari admin...',
            'questionId' => $questionId
        ]);
    }

    // Update jawaban dari admin
    public function updateAnswer($id)
    {
        $model = new AdminModel();
        $answer = $this->request->getPost('answer');

        $model->update($id, [
            'admin_answer' => $answer,
            'status' => 'answered'
        ]);

        $updatedChat = $model->find($id);

        return $this->response->setJSON([
            'answer' => $updatedChat['admin_answer'],
            'questionId' => $updatedChat['id'],
            'status' => 'answered'
        ]);
    }
}