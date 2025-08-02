<?php

namespace App\Models;

use CodeIgniter\Model;

class PenghargaanModel extends Model
{
    protected $table            = 'penghargaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    =  ['manajer_id', 'pegawai_id', 'jenis_penghargaan', 'alasan', 'status', 'catatan_penolakan', 'file_name', 'file_path'];


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


    public function updateDatapenghargaan($data, $pegawaiId, $id)
    {
        // Menggunakan dua kondisi untuk memastikan data yang tepat diupdate
        return $this->db->table('penghargaan')
            ->where('pegawai_id', $pegawaiId)  // Kondisi pertama berdasarkan pegawai_id
            ->where('id', $id)                 // Kondisi kedua berdasarkan id
            ->update($data);                   // Update data
    }

    public function getPenghargaanByUser($userId , $roleId)
    {
        return $this->select('penghargaan.*, u1.*, u2.username AS manager_id')
            ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
            ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
            ->where('u1.role', $roleId)
            ->where('u1.id', $userId)
            ->where('penghargaan.file_name !=', '')  // Menggunakan alias u1 untuk filter id pegawai
            ->findAll();
    }
}
