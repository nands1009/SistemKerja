<?php

namespace App\Controllers;

use App\Models\LaporanKerjamModel; // Menggunakan nama model yang benar

class ApprovalHRD extends BaseController
{
    protected $laporanKerjamModel; // Menggunakan nama yang benar untuk properti model

    public function __construct()
    {
        // Menggunakan model yang benar
        $this->laporanKerjamModel = new LaporanKerjamModel(); // Pastikan model yang benar dipanggil
    }

    // Menampilkan laporan kerja yang perlu disetujui oleh manajer
    public function index()
    {
        $divisi_id = session()->get('divisi_id'); 
    $user_id = session()->get('user_id');  // Mengambil user_id (manager_id) dari session
    $status = 'Pending'; // Status yang ingin dicari, misalnya 'Pending'

    // Pastikan divisi_id dan user_id ada di session, jika tidak redirect ke halaman login
    if (!$divisi_id || !$user_id) {
        return redirect()->to('/login')->with('error', 'Divisi ID atau User ID tidak ditemukan di session');
    }

    // Mengambil laporan kerja berdasarkan divisi_id, user_id dan status 'Pending'
    $laporan_kerja = $this->laporanKerjamModel->getLaporanByDivisiAndStatusHrd($divisi_id, $user_id, $status);

    // Mengembalikan view dengan data laporan kerja

        return view('dashboard/hrd/approval', ['laporan_kerja' => $laporan_kerja]);
    }

    // Persetujuan laporan kerja
    public function approve($id)
    {
        $this->laporanKerjamModel->update($id, ['status_approval' => 'Approved']);
        return redirect()->to('/approval-hrd')->with('message', 'Laporan berhasil disetujui!');
    }

    public function reject($id)
    {
        $catatan_penolakan = $this->request->getPost('catatan_penolakan');
        $data = [
            'status_approval'   => 'Rejected',
            'catatan_penolakan' => $catatan_penolakan
        ];

        if ($this->laporanKerjamModel->update($id, $data)) {
            return redirect()->to('/approval-hrd')->with('message', 'Laporan ditolak dengan catatan!');
        } else {
            return redirect()->to('/approval-hrd')->with('error', 'Gagal menolak laporan!');
        }
    }
}
