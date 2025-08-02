<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanKerjamModel extends Model
{
    protected $table            = 'laporan_kerja';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'judul', 'deskripsi', 'foto_dokumen', 'status', 'username', 'divisi_id', 'status_approval', 'manager_id', 'catatan_penolakan'];

    // Menyimpan laporan
    public function simpanLaporan($data)
    {
        return $this->insert($data);
    }

    // Mengubah status laporan
    public function ubahStatusLaporan($laporanId, $status)
    {
        $this->update($laporanId, ['status' => $status]);

        // Simpan riwayat status laporan
        $riwayatModel = new RiwayatLaporanModel();
        $riwayatModel->simpanRiwayat($laporanId, $status);
    }

    // Mendapatkan laporan berdasarkan user_id dan role
     public function getLaporanByUser($userId, $roleId, $year = null)
    {
        $builder = $this->db->table('laporan_kerja')
        ->select('laporan_kerja.*, users.username, users.role, users.divisi')
        ->join('users', 'users.id = laporan_kerja.user_id')
        ->where('users.id', $userId)
        ->where('users.role', $roleId)
        ->orderBy('laporan_kerja.tanggal', 'DESC'); // Urutkan berdasarkan tanggal terbaru

          if ($year) {
        $builder->like('laporan_kerja.tanggal', $year, 'after'); // Mengambil laporan yang tahunannya sesuai
    }
        return $builder->get()->getResultArray();
    }

    public function getLaporanByUserManagerShow($userId, $roleId , $id)
    {
        return $this->db->table('laporan_kerja')
            ->select('laporan_kerja.*, users.username, users.role, users.divisi')
            ->join('users', 'users.id = laporan_kerja.user_id')
            ->where('users.id', $userId)
            ->where('laporan_kerja.id', $id) // Menambahkan kondisi untuk laporan_kerja.id
            ->where('users.role', $roleId)
            ->get()
            ->getResultArray();
    }
    public function getLaporanByDivisiID($divisi_id, $status = "Approved")
    {
        return $this->db->table('laporan_kerja')
        ->select('laporan_kerja.*, users.*, users.id AS user_ID, laporan_kerja.id AS laporan_ID')
        ->join('users', 'users.id = laporan_kerja.user_id')
        ->where('users.divisi_id', $divisi_id)
        ->where('status_approval', $status)
        ->get()
        ->getResultArray();
    }

    public function updateData($data, $id)
    {
        return $this->db->table('laporan_kerja')
            ->update($data, ['id' => $id]);  // Mengganti 'judul' menjadi 'id' jika itu adalah kolom yang sesuai untuk pencarian
    }

       // Fungsi untuk mendapatkan laporan berdasarkan divisi_id dan status_approval
   public function getLaporanByDivisiAndStatus($divisi_id, $status)
{
    // Ambil ID user yang sedang login (asumsi Anda menggunakan session untuk menyimpan informasi pengguna)
    $user_id = session()->get('user_id');
    $user_role = session()->get('role');

    // Jika user yang sedang login adalah Manager, tambahkan pengecualian
    $query = $this->select('laporan_kerja.*, users.username, users.email, laporan_kerja.id AS laporan_ID, users.role, users.*') // Menambahkan kolom jabatan
                  ->join('users', 'users.id = laporan_kerja.user_id') // Join dengan tabel users untuk mendapatkan informasi karyawan
                  ->where('laporan_kerja.divisi_id', $divisi_id) // Filter berdasarkan divisi_id
                  ->groupStart() // Memulai grup kondisi
                      ->where('users.role !=', 'Manager') 
                      ->orderBy('laporan_kerja.tanggal', 'DESC') // Menghindari laporan yang dimiliki oleh Manager
                      ->orWhere('laporan_kerja.status', $status) // Atau jika status laporan sesuai
                  ->groupEnd(); // Mengakhiri grup kondisi

    // Jika role user adalah Manager, filter lebih lanjut untuk menghindari laporan manager
    if ($user_role != 'Manager') {
        $query->where('laporan_kerja.user_id !=', $user_id); // Mengecualikan laporan yang dimiliki oleh Manager yang sedang login
    }

    return $query->findAll(); // Mengambil semua laporan yang sesuai
}


    public function getLaporanByDivisiAndStatusHrd($divisi_id, $user_id, $status)
    {
        return $this->select('laporan_kerja.*, users.username, users.email, laporan_kerja.id AS laporan_ID ,users.*')
                    ->join('users', 'users.id = laporan_kerja.user_id') // Join dengan tabel users untuk mendapatkan informasi karyawan
                    ->where('laporan_kerja.divisi_id', $divisi_id) // Filter berdasarkan divisi_id
                    ->where('laporan_kerja.status_approval', $status) // Filter berdasarkan status approval
                    ->where('laporan_kerja.user_id !=', $user_id) // Jangan ambil laporan manager sendiri
                    ->findAll(); // Mengambil semua laporan yang sesuai
    }
    

    // Mengupdate status approval laporan
    public function updateStatusApproval($id, $status, $catatan_penolakan = null)
    {
        $data = ['status_approval' => $status];
        if ($status == 'Ditolak') {
            $data['catatan_penolakan'] = $catatan_penolakan; // Menambahkan catatan jika ditolak
        }
        return $this->update($id, $data);
    }
}
