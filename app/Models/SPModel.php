<?php

namespace App\Models;

use CodeIgniter\Model;

class SPModel extends Model
{
    protected $table            = 'sp';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    =  ['manajer_id', 'pegawai_id', 'alasan', 'status', 'catatan_penolakan', 'file_name', 'file_path'];


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


    public function getRiwayatSP()
    {
        return $this->findAll();
    }

    // Fungsi untuk memperbarui status dan menyimpan nama file PDF
    public function saveDocument($data)
    {
        return $this->insert($data);
    }

    public function updateData($data, $pegawaiId)
    {
        return $this->db->table('sp')
            ->where('pegawai_id', $pegawaiId)
            ->update($data);
    }

    public function getSpByUser($userId , $roleId)
    {
        return $this->select('sp.*, u1.username , u2.username AS manager_id')
            ->join('users AS u1', 'u1.id = sp.pegawai_id')
            ->join('users AS u2', 'u2.id = sp.manajer_id')
            ->where('u1.role', $roleId)
            ->where('u1.id', $userId)  
            ->where('sp.file_name !=', '')  
            ->findAll();
    }
}
