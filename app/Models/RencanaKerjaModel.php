<?php

namespace App\Models;

use CodeIgniter\Model;

class RencanaKerjaModel extends Model
{
    protected $table            = 'rencana_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','usernmae', 'judul', 'deskripsi', 'status', 'divisi' , 'divisi_id' , 'manager_id'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getrencanakerjaByUsershow($userId, $roleId, $id)
    {
        return $this->db->table('rencana_kerja')
            ->select('rencana_kerja.*, users.username, users.role, users.divisi')
            ->join('users', 'users.id = rencana_kerja.user_id')
            ->where('users.id', $userId)
            ->where('rencana_kerja.id', $id) // Menambahkan kondisi untuk laporan_kerja.id
            ->where('users.role', $roleId)
            ->get()
            ->getResultArray();
    }
}
