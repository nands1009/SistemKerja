<?php

namespace App\Controllers;

use App\Models\AnsweredQuestionsModel;

class FrequentQuestionsController extends BaseController
{
    public function index()
    {
        $model = new AnsweredQuestionsModel();
        
        // Ambil parameter dari URL
        $limit = $this->request->getGet('limit') ?? 10;
        
        // Validasi limit
        $limit = (int)$limit;
        if ($limit <= 0 || $limit > 100) {
            $limit = 10; // Default limit jika tidak valid
        }
        
        // Ambil data pertanyaan yang sering ditanyakan
        $frequentQuestions = $model->countFrequentQuestions(100); // Ambil lebih banyak data untuk perhitungan tag
        
        // Hitung frekuensi berdasarkan tag
        $tagFrequencies = [];
        
        foreach ($frequentQuestions as $question) {
            $tag = $question['tag'];
            
            // Inisialisasi jika tag belum ada dalam array
            if (!isset($tagFrequencies[$tag])) {
                $tagFrequencies[$tag] = [
                    'total_frequency' => 0,
                    'question_count' => 0
                ];
            }
            
            // Tambahkan frekuensi pertanyaan ini ke total tag
            $tagFrequencies[$tag]['total_frequency'] += $question['frequency'];
            
            // Tambahkan 1 ke penghitungan pertanyaan unik
            $tagFrequencies[$tag]['question_count']++;
        }
        
        // Urutkan berdasarkan total frekuensi (dari tertinggi ke terendah)
        uasort($tagFrequencies, function($a, $b) {
            return $b['total_frequency'] - $a['total_frequency'];
        });
        
        // Terapkan batasan jumlah data untuk tag
        $tagFrequencies = array_slice($tagFrequencies, 0, $limit, true);
        
        // Filter pertanyaan berdasarkan tag yang dipilih setelah pembatasan
        $selectedTags = array_keys($tagFrequencies);
        $filteredQuestions = array_filter($frequentQuestions, function($question) use ($selectedTags) {
            return in_array($question['tag'], $selectedTags);
        });
        
        // Ambil semua tag unik untuk dropdown filter (disimpan untuk kompatibilitas)
        $allQuestions = $model->findAll();
        $uniqueTags = array_unique(array_column($allQuestions, 'tag'));
        
        // Kirim data ke view
        return view('most_frequent_questions_chart', [
            'frequentQuestions' => array_values($filteredQuestions), // Reset indeks array
            'tagFrequencies' => $tagFrequencies,
            'uniqueTags' => $uniqueTags
        ]);
    }
}