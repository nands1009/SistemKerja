<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'chatbot_data';
    protected $primaryKey = 'id';
    protected $allowedFields = ['question', 'answer', 'status', 'admin_answer'];
    protected $useTimestamps = false;

   
}