<?php

namespace App\Controllers;

use App\Models\PengajuanPenghargaanModel;
use App\Models\UserModel;

class ApprovalPenghargaan extends BaseController
{
    protected $PengajuanPenghargaanModel;
    protected $UserModel;

    public function __construct()
    {
        $this->PengajuanPenghargaanModel = new PengajuanPenghargaanModel();
        $this->UserModel = new UserModel();
    }

    // Menampilkan pengajuan penghargaan yang perlu disetujui oleh HRD
    public function index()
    {
        $hrd_id = session()->get('user_id');

        // Ambil pengajuan penghargaan yang statusnya Pending
        $pengajuan = $this->PengajuanPenghargaanModel->where('status', 'Pending')
            ->where('hrd_id', $hrd_id)  // Hanya untuk HRD yang login
            ->findAll();

        return view('dashboard/hrd/approval_penghargaan', ['pengajuan' => $pengajuan]);
    }

    // Approve oleh HRD
    public function approve($id)
    {
        $data = [
            'status' => 'Approved by HRD',
            'hrd_id' => session()->get('user_id'),
        ];

        // Update status menjadi "Approved by HRD"
        $this->PengajuanPenghargaanModel->update($id, $data);

        // Kirim ke Direksi untuk persetujuan lebih lanjut
        return redirect()->to('/approval-direksi')->with('message', 'Pengajuan Penghargaan disetujui dan dikirim ke Direksi.');
    }

    // Reject oleh HRD
    public function reject($id)
    {
        $data = [
            'status' => 'Rejected by HRD',
            'hrd_id' => session()->get('user_id'),
        ];

        // Update status menjadi "Rejected by HRD"
        $this->PengajuanPenghargaanModel->update($id, $data);

        return redirect()->to('/approval-hrd')->with('message', 'Pengajuan Penghargaan ditolak.');
    }
}
