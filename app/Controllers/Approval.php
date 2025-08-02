<?php

namespace App\Controllers;

use App\Models\LaporanKerjamModel; // Menggunakan nama model yang benar

class Approval extends BaseController
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
    // Mengambil divisi_id dari session
    $divisi_id = session()->get('divisi_id'); // Mengambil divisi_id dari session manager

    // Mengambil status dari parameter, jika tidak ada, default ke 'Pending'
    $status = $this->request->getVar('status') ?? 'Pending'; // Menangkap status dari query parameter atau default ke 'Pending'

    // Pastikan divisi_id ada di session, jika tidak redirect ke halaman login
    if (!$divisi_id) {
        return redirect()->to('/login')->with('error', 'Divisi ID tidak ditemukan di session');
    }

    // Mengambil laporan kerja berdasarkan divisi_id dan status yang diinginkan
    $laporan_kerja = $this->laporanKerjamModel->getLaporanByDivisiAndStatus($divisi_id, $status);

    return view('dashboard/manager/approval', ['laporan_kerja' => $laporan_kerja]);
}

    // Persetujuan laporan kerja
    public function approve($id)
    {
        $this->laporanKerjamModel->update($id, ['status_approval' => 'Approved']);
        return redirect()->to('/approval')->with('message', 'Laporan berhasil disetujui!');
    }

    public function reject($id)
    {
        $catatan_penolakan = $this->request->getPost('catatan_penolakan');
        $data = [
            'status_approval'   => 'Rejected',
            'catatan_penolakan' => $catatan_penolakan
        ];

        if ($this->laporanKerjamModel->update($id, $data)) {
            return redirect()->to('/approval')->with('message', 'Laporan ditolak dengan catatan!');
        } else {
            return redirect()->to('/approval')->with('error', 'Gagal menolak laporan!');
        }
    }
}
