<?php

namespace App\Models;

use CodeIgniter\Model;

class UnansweredQuestionModel extends Model
{
    protected $table      = 'unanswered_questions';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    
    protected $allowedFields = ['question', 'answer', 'status', 'tag', 'answered_by', 'created_at', 'updated_at'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules = [
        'question' => 'required|min_length[3]',
    ];
    
    // Mendapatkan semua pertanyaan yang belum dijawab
    public function getPendingQuestions()
    {
        return $this->where('status', 'pending')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    // Mendapatkan semua pertanyaan yang sudah dijawab
    public function getAnsweredQuestions()
    {
        return $this->where('status', 'answered')
                    ->orderBy('updated_at', 'DESC')
                    ->findAll();
    }
    
    // Mencari jawaban untuk pertanyaan yang mirip
    public function findSimilarQuestion($question)
    {
        $answeredQuestions = $this->where('status', 'answered')->findAll();
        $bestScore = 0;
        $bestMatch = null;
        
        foreach ($answeredQuestions as $item) {
            similar_text(strtolower($question), strtolower($item['question']), $percent);
            if ($percent > $bestScore && $percent > 70) { // Minimal 70% kecocokan
                $bestScore = $percent;
                $bestMatch = $item;
            }
        }
        
        return [
            'match' => $bestMatch,
            'score' => $bestScore
        ];
    }

}