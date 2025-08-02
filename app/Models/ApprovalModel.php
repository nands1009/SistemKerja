<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalModel extends Model

{public function simpanLaporan($data)
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

    public function getLaporanByUser($userId, $roleId)
    {
        return $this->db->table('laporan_kerja')
            ->select('laporan_kerja.*, users.*')
            ->join('users', 'users.id = laporan_kerja.user_id')
            ->where('users.id', $userId)
            ->where('users.role', $roleId)
            ->get()
            ->getResultArray();
    }
}