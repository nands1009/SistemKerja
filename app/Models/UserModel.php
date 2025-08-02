<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = ['users'];
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'password', 'email', 'role', 'divisi','divisi_id' , 'role_id', 'approved'];

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
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
    public function getStaffByDivisi($divisi_id)
    {
        return $this->where('divisi', $divisi_id) // Filter by divisi_id
            ->findAll(); // Get all staff in that divisi
    }

    public function getName($userId, $roleId)
    {
        return $this->db->table('users')  // Mengambil data dari tabel 'users'
            ->select('users.username, users.role, users.divisi')  // Pilih kolom yang dibutuhkan
            ->where('users.id', $userId)  // Filter berdasarkan userId
            ->where('users.role', $roleId)  // Filter berdasarkan roleId
            ->get()  // Menjalankan query
            ->getResultArray();  // Mengembalikan hasil sebagai array
    }

    public function getlaporan($userId, $roleId)
    {
        return $this->db->table('laporan_kerja')
        ->select('laporan_kerja.*, users.username, users.role, users.divisi')
        ->join('users', 'users.id = laporan_kerja.user_id')  // Pilih kolom yang dibutuhkan

            ->where('status_approval', 'Rejected')
            ->where('users.id', $userId)
            ->where('users.role', $roleId)
            ->countAllResults();
    }

    public function getSpByUser($userId , $roleId)
    {
        return $this->db->table('sp')
        ->select('sp.*, u1.username , u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = sp.pegawai_id')
            ->join('users AS u2', 'u2.id = sp.manajer_id')
            ->where('u1.role', $roleId)
            ->where('u1.id', $userId)  
            ->where('sp.file_name !=', '')  
            ->countAllResults();
    }

    public function getPenghargaanByUser($userId , $roleId)
    {
        return $this->db->table('penghargaan')
        ->select('penghargaan.*, u1.*, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
            ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
            ->where('u1.role', $roleId)
            ->where('u1.id', $userId)
            ->where('penghargaan.file_name !=', '')  // Menggunakan alias u1 untuk filter id pegawai
            ->countAllResults();
    }

    /*manager notifikasi*/

    public function getSpByManager($userId, $roleId)
{
    // Menghitung jumlah SP yang sudah disetujui tetapi belum diupload PDF
    $beforeUpload = $this->db->table('sp')
        ->select('sp.*, u1.username, u2.role AS manager_id')
        ->join('users AS u1', 'u1.id = sp.pegawai_id')
        ->join('users AS u2', 'u2.id = sp.manajer_id')
        ->whereIn('sp.status', ['Approved Direksi', 'Approved Hrd'])  // Status yang disetujui
        // Memastikan file_name kosong (belum diupload)
        ->groupStart() 
            ->where('sp.pegawai_id', $userId)  // Pegawai sesuai dengan userId
            ->orWhere('sp.manajer_id', $userId)  // Atau sebagai manajer dengan userId
        ->groupEnd() 
        ->countAllResults(); // Menghitung jumlah hasil

    // Menghitung jumlah SP yang sudah disetujui dan file PDF sudah diupload
    $afterUpload = $this->db->table('sp')
        ->select('sp.*, u1.username, u2.role AS manager_id')
        ->join('users AS u1', 'u1.id = sp.pegawai_id')
        ->join('users AS u2', 'u2.id = sp.manajer_id')
        ->whereIn('sp.status', ['Approved Direksi', 'Approved Hrd'])  // Status yang disetujui
        ->where('sp.file_name !=', '')  // Memastikan file_name sudah ada (sudah diupload)
        ->groupStart()
            ->where('sp.pegawai_id', $userId)  // Pegawai sesuai dengan userId
            ->orWhere('sp.manajer_id', $userId)  // Atau sebagai manajer dengan userId
        ->groupEnd()
        ->countAllResults(); // Menghitung jumlah hasil

    // Mengembalikan hasil: SP yang belum diupload dan sudah diupload
    return $beforeUpload - $afterUpload; // Jumlah yang berkurang setelah upload PDF
}

    public function getSpRejectByManager($userId, $roleId)
    {
        return $this->db->table('sp')
            ->select('sp.*, u1.username, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = sp.pegawai_id')
            ->join('users AS u2', 'u2.id = sp.manajer_id')
            ->whereIn('sp.status', ['Rejected Direksi', 'Rejected Hrd'])  // Status yang disetujui
            ->groupStart() // Mulai kondisi OR
                ->where('sp.pegawai_id', $userId) // Pegawai sesuai dengan userId
                ->orWhere('sp.manajer_id', $userId) // Atau sebagai manajer dengan userId
            ->groupEnd() // Akhiri kondisi OR
            ->countAllResults(); // Menghitung jumlah hasil
    }

    public function getPenghargaanByManager($userId, $roleId)
    {
        // Menghitung jumlah SP yang sudah disetujui tetapi belum diupload PDF
        $beforeUpload = $this->db->table('penghargaan')
            ->select('penghargaan.*, u1.username, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
            ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
            ->whereIn('penghargaan.status', ['Approved Direksi', 'Approved Hrd'])  // Status yang disetujui
            // Memastikan file_name kosong (belum diupload)
            ->groupStart() 
                ->where('penghargaan.pegawai_id', $userId)  // Pegawai sesuai dengan userId
                ->orWhere('penghargaan.manajer_id', $userId)  // Atau sebagai manajer dengan userId
            ->groupEnd() 
            ->countAllResults(); // Menghitung jumlah hasil
    
        // Menghitung jumlah SP yang sudah disetujui dan file PDF sudah diupload
        $afterUpload = $this->db->table('penghargaan')
            ->select('penghargaan.*, u1.username, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
            ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
            ->whereIn('penghargaan.status', ['Approved Direksi', 'Approved Hrd'])  // Status yang disetujui
            ->where('penghargaan.file_name !=', '')  // Memastikan file_name sudah ada (sudah diupload)
            ->groupStart()
                ->where('penghargaan.pegawai_id', $userId)  // Pegawai sesuai dengan userId
                ->orWhere('penghargaan.manajer_id', $userId)  // Atau sebagai manajer dengan userId
            ->groupEnd()
            ->countAllResults(); // Menghitung jumlah hasil
    
        // Mengembalikan hasil: SP yang belum diupload dan sudah diupload
        return $beforeUpload - $afterUpload; // Jumlah yang berkurang setelah upload PDF
    }

    public function getPenghargaanRejectByManager($userId, $roleId)
    {
        return $this->db->table('penghargaan')
            ->select('penghargaan.*, u1.username, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
            ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
            ->whereIn('penghargaan.status', ['Rejected Direksi', 'Rejected Hrd'])  // Status yang disetujui
            ->groupStart() // Mulai kondisi OR
                ->where('penghargaan.pegawai_id', $userId) // Pegawai sesuai dengan userId
                ->orWhere('penghargaan.manajer_id', $userId) // Atau sebagai manajer dengan userId
            ->groupEnd() // Akhiri kondisi OR
            ->countAllResults(); // Menghitung jumlah hasil
    }

     /*direksi notifikasi*/

     public function getSpByDireksi()
     {
        return $this->db->table('sp')
        ->select('sp.*, u1.username, u2.role AS manager_id')
        ->join('users AS u1', 'u1.id = sp.pegawai_id')
        ->join('users AS u2', 'u2.id = sp.manajer_id')
        ->whereIn('sp.status', ['Approved Hrd'])  // Status yang disetujui
        ->countAllResults(); // 
     }

     public function getPenghargaanByDireksi()
     {
        return $this->db->table('penghargaan')
        ->select('penghargaan.*, u1.username, u2.role AS manager_id')
        ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
        ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
        ->whereIn('penghargaan.status', ['Approved Hrd'])  // Status yang disetujui
        ->countAllResults(); // 
     }
    
     /*HRD notifikasi*/

     public function getSPbyHrd()
     {
         // Menghitung jumlah SP dengan status 'Pending'
         $beforeUpload = $this->db->table('sp')
             ->select('sp.*')
             ->where('sp.status', 'Pending')  // Status yang masih Pending
             ->countAllResults();  // Menghitung jumlah SP Pending
     
         // Menghitung jumlah SP dengan status 'Approved HRD' yang sudah diproses
         

        
         return $beforeUpload;
     }

     public function getPernghargaanbyHrd()
     {
         // Menghitung jumlah SP dengan status 'Pending'
         $beforeUpload = $this->db->table('penghargaan')
             ->select('penghargaan.*')
             ->where('penghargaan.status', 'Pending')  // Status yang masih Pending
             ->countAllResults();  // Menghitung jumlah SP Pending
     
         // Menghitung jumlah SP dengan status 'Approved HRD' yang sudah diproses
         

        
         return $beforeUpload;
     }

     //USER hrd 
     public function getSpByUserHrd($userId, $roleId)
{
    // Menghitung jumlah SP yang sudah disetujui tetapi belum diupload PDF
    $beforeUpload = $this->db->table('sp')
        ->select('sp.*, u1.username, u2.role AS manager_id')
        ->join('users AS u1', 'u1.id = sp.pegawai_id')
        ->join('users AS u2', 'u2.id = sp.manajer_id')
        ->whereIn('sp.status', ['Approved Direksi'])  // Status yang disetujui
        // Memastikan file_name kosong (belum diupload)
        ->groupStart() 
            ->where('sp.pegawai_id', $userId)  // Pegawai sesuai dengan userId
            ->orWhere('sp.manajer_id', $userId)  // Atau sebagai manajer dengan userId
        ->groupEnd() 
        ->countAllResults(); // Menghitung jumlah hasil

    // Menghitung jumlah SP yang sudah disetujui dan file PDF sudah diupload
    $afterUpload = $this->db->table('sp')
        ->select('sp.*, u1.username, u2.role AS manager_id')
        ->join('users AS u1', 'u1.id = sp.pegawai_id')
        ->join('users AS u2', 'u2.id = sp.manajer_id')
        ->whereIn('sp.status', ['Approved Direksi'])  // Status yang disetujui
        ->where('sp.file_name !=', '')  // Memastikan file_name sudah ada (sudah diupload)
        ->groupStart()
            ->where('sp.pegawai_id', $userId)  // Pegawai sesuai dengan userId
            ->orWhere('sp.manajer_id', $userId)  // Atau sebagai manajer dengan userId
        ->groupEnd()
        ->countAllResults(); // Menghitung jumlah hasil

    // Mengembalikan hasil: SP yang belum diupload dan sudah diupload
    return $beforeUpload - $afterUpload; // Jumlah yang berkurang setelah upload PDF
}

 public function getSpRejectByuserHRD($userId, $roleId)
    {
        return $this->db->table('sp')
            ->select('sp.*, u1.username, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = sp.pegawai_id')
            ->join('users AS u2', 'u2.id = sp.manajer_id')
            ->whereIn('sp.status', ['Rejected Direksi'])  // Status yang disetujui
            ->groupStart() // Mulai kondisi OR
                ->where('sp.pegawai_id', $userId) // Pegawai sesuai dengan userId
                ->orWhere('sp.manajer_id', $userId) // Atau sebagai manajer dengan userId
            ->groupEnd() // Akhiri kondisi OR
            ->countAllResults(); // Menghitung jumlah hasil
    }


    public function getPenghargaanByUserHRD($userId, $roleId)
    {
        // Menghitung jumlah SP yang sudah disetujui tetapi belum diupload PDF
        $beforeUpload = $this->db->table('penghargaan')
            ->select('penghargaan.*, u1.username, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
            ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
            ->whereIn('penghargaan.status', ['Approved Direksi', 'Approved Hrd'])  // Status yang disetujui
            // Memastikan file_name kosong (belum diupload)
            ->groupStart() 
                ->where('penghargaan.pegawai_id', $userId)  // Pegawai sesuai dengan userId
                ->orWhere('penghargaan.manajer_id', $userId)  // Atau sebagai manajer dengan userId
            ->groupEnd() 
            ->countAllResults(); // Menghitung jumlah hasil
    
        // Menghitung jumlah SP yang sudah disetujui dan file PDF sudah diupload
        $afterUpload = $this->db->table('penghargaan')
            ->select('penghargaan.*, u1.username, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
            ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
            ->whereIn('penghargaan.status', ['Approved Direksi'])  // Status yang disetujui
            ->where('penghargaan.file_name !=', '')  // Memastikan file_name sudah ada (sudah diupload)
            ->groupStart()
                ->where('penghargaan.pegawai_id', $userId)  // Pegawai sesuai dengan userId
                ->orWhere('penghargaan.manajer_id', $userId)  // Atau sebagai manajer dengan userId
            ->groupEnd()
            ->countAllResults(); // Menghitung jumlah hasil
    
        // Mengembalikan hasil: SP yang belum diupload dan sudah diupload
        return $beforeUpload - $afterUpload; // Jumlah yang berkurang setelah upload PDF
    }

    public function getPenghargaanRejectByUserHRD($userId, $roleId)
    {
        return $this->db->table('penghargaan')
            ->select('penghargaan.*, u1.username, u2.role AS manager_id')
            ->join('users AS u1', 'u1.id = penghargaan.pegawai_id')
            ->join('users AS u2', 'u2.id = penghargaan.manajer_id')
            ->whereIn('penghargaan.status', ['Rejected Direksi'])  // Status yang disetujui
            ->groupStart() // Mulai kondisi OR
                ->where('penghargaan.pegawai_id', $userId) // Pegawai sesuai dengan userId
                ->orWhere('penghargaan.manajer_id', $userId) // Atau sebagai manajer dengan userId
            ->groupEnd() // Akhiri kondisi OR
            ->countAllResults(); // Menghitung jumlah hasil
    }


}
