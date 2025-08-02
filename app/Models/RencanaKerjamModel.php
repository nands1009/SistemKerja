<?php

namespace App\Models;

use CodeIgniter\Model;

class RencanaKerjamModel extends Model
{
    protected $table            = 'rencana_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['judul', 'deskripsi', 'tanggal', 'status', 'manager_id', 'divisi_id', 'divisi','user_id'];

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
    // Mendapatkan rencana kerja berdasarkan divisi dan manager
    public function getRencanaKerjaByManagerAndDivisi($manager_id, $divisi_id)
    {
        return $this

        ->where('manager_id', $manager_id)
            ->orWhere('divisi_id', $divisi_id)
            ->findAll();
    }

    // Menambahkan rencana kerja baru
    public function addRencanaKerja($data)
    {
        return $this->insert($data);
    }
}
