<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianPegawaiModel extends Model
{
    protected $table            = 'penilaian_pegawai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pegawai_id', 'manajer_id', 'nilai', 'catatan', 'tanggal_penilaian' , 'direksi_id'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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

    public function getPenilaianWithUser($userId, $roleId)
    {
        return $this->select('penilaian_pegawai.*, u1.username AS nama_pegawai, u2.username AS nama_evaluator')
        ->join('users AS u1', 'u1.id = penilaian_pegawai.pegawai_id')
        ->join('users AS u2', 'u2.id = penilaian_pegawai.manajer_id')
        ->where('u1.id', $userId)  // Menggunakan alias u1 untuk filter id pegawai
        ->where('u1.role', $roleId)  // Menggunakan alias u1 untuk filter role pegawai
        ->orderBy('penilaian_pegawai.tanggal_penilaian', 'DESC')
        ->findAll();
            //return $this->db->table('penilaian_pegawai')
           // ->select('penilaian_pegawai.*, users.username, users.role, users.divisi')
            //->join('users', 'users.id = penilaian_pegawai.id')
            //->where('users.id', $userId)
            //->where('users.role', $roleId)
           // ->orderBy('penilaian_pegawai.tanggal_penilaian', 'DESC') // Urutkan berdasarkan tanggal terbaru
           // ->get()
           // ->getResultArray();
    }
}
