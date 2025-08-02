<?php

namespace App\Models;

use CodeIgniter\Model;

class AnsweredQuestionsModel extends Model
{
    protected $table         = 'answered_questions';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['question', 'answer', 'tag', 'frequency', 'created_at', 'last_asked_at'];
    protected $useTimestamps = false;

    public function countFrequentQuestions($limit = 10)
    {
        return $this->select('question, answer, tag, frequency, created_at, last_asked_at')
                    ->orderBy('frequency', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    
                    ->findAll();
    }

    public function getQuestionsByTag($tag = null, $limit = 10)
    {
        $query = $this->select('question, answer, tag, frequency, created_at, last_asked_at');
        
        if ($tag) {
            $query = $query->where('tag', $tag);
        }
        
        return $query->orderBy('frequency', 'DESC')
                     ->limit($limit)
                     ->orderBy('created_at', 'DESC')
                     ->findAll();
    }
}