<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanKerjaModel extends Model
{
    protected $table            = 'laporan_kerja';
    protected $primaryKey       = ['id'];
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'judul', 'deskripsi', 'foto_dokumen', 'status', 'username', 'divisi_id', 'status_approval', 'manager_id', 'catatan_penolakan'];

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


    public function simpanLaporan($data)
    {
        return $this->insert($data);
    }

    public function ubahStatusLaporan($laporanId, $status)
    {
        $this->update($laporanId, ['status' => $status]);

        // Simpan riwayat status laporan
        $riwayatModel = new RiwayatLaporanModel();
        $riwayatModel->simpanRiwayat($laporanId, $status);
    }

public function getLaporanByUser($userId, $roleId, $year = null)
{
    $builder = $this->db->table('laporan_kerja')
        ->select('laporan_kerja.*, users.username, users.role, users.divisi')
        ->join('users', 'users.id = laporan_kerja.user_id')
        ->where('users.id', $userId)
        ->where('users.role', $roleId)
        ->orderBy('laporan_kerja.tanggal', 'DESC');

    // Jika ada tahun yang dipilih, filter berdasarkan tahun
    if ($year) {
        $builder->like('laporan_kerja.tanggal', $year, 'after'); // Mengambil laporan yang tahunannya sesuai
    }

    return $builder->get()->getResultArray();
}


    public function updateData($data, $id)
    {
        return $this->db->table('laporan_kerja')
            ->update($data, ['id' => $id]);  // Mengganti 'judul' menjadi 'id' jika itu adalah kolom yang sesuai untuk pencarian
    }

    public function getLaporanByUsershow($userId, $roleId, $id)
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

    public function getLaporanJumlah()
{
    //return $this->db->table('laporan_kerja')->num_rows();
}

    public function pdf($userId, $roleId)
{
    return $this->db->table('laporan_kerja')
        ->select('laporan_kerja.*, users.username, users.role, users.divisi')
        ->join('users', 'users.id = laporan_kerja.user_id')
        ->where('users.id', $userId)
        ->where('users.role', $roleId)
        ->orderBy('laporan_kerja.tanggal', 'DESC') // Urutkan berdasarkan tanggal terbaru
        ->get()
        ->getResultArray();
}

}


class RiwayatLaporanModel extends Model
{
    protected $table = 'riwayat_laporan';
    protected $primaryKey = ['id'];
    protected $allowedFields = ['laporan_id', 'status_sebelumnya', 'status_baru'];

    public function simpanRiwayat($laporanId, $status)
    {
        // Ambil status sebelumnya
        $laporanModel = new LaporanKerjaModel();
        $laporan = $laporanModel->find($laporanId);
        $statusSebelumnya = $laporan['status'];

        // Simpan riwayat status
        $this->insert([
            'laporan_id' => $laporanId,
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $status
        ]);
    }
    // Mendapatkan laporan berdasarkan divisi dan status approval
    public function getLaporanByDivisiAndStatus($divisi_id, $status = 'Pending')
    {
        return $this->where('divisi_id', $divisi_id)
            ->where('status_approval', $status)
            ->findAll();
    }

    // Mengupdate status approval laporan
    public function updateStatusApproval($id, $status, $catatan_penolakan = null)
    {
        $data = ['status_approval' => $status];
        if ($status == 'Ditolak') {
            $data['catatan_penolakan'] = $catatan_penolakan;
        }
        return $this->update($id, $data);
    }
}
