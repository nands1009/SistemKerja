<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatbotModel extends Model
{
    protected $table = 'chatbot_data';
    protected $primaryKey = 'id';
    protected $allowedFields = ['question', 'answer', 'status', 'question_id'];
    protected $useTimestamps = false;
}