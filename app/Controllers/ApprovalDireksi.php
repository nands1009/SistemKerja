<?php

namespace App\Controllers;

use App\Models\PengajuanPenghargaanModel;

class ApprovalDireksi extends BaseController
{
    protected $PengajuanPenghargaanModel;

    public function __construct()
    {
        $this->PengajuanPenghargaanModel = new PengajuanPenghargaanModel();
    }

    // Menampilkan pengajuan penghargaan yang perlu disetujui oleh Direksi
    public function index()
    {
        $direksi_id = session()->get('user_id');
        
        // Ambil pengajuan penghargaan yang statusnya sudah disetujui oleh HRD dan menunggu approval Direksi
        $pengajuan = $this->PengajuanPenghargaanModel->where('status', 'Approved by HRD')
            ->where('direksi_id', $direksi_id)  // Hanya untuk Direksi yang login
            ->findAll();

        return view('dashboard/direksi/approval_penghargaan', ['pengajuan' => $pengajuan]);
    }

    // Approve oleh Direksi
    public function approve($id)
    {
        $data = [
            'status' => 'Approved by Direksi',
            'direksi_id' => session()->get('user_id'),
        ];

        // Update status menjadi "Approved by Direksi"
        $this->PengajuanPenghargaanModel->update($id, $data);

        return redirect()->to('/approval-direksi')->with('message', 'Pengajuan Penghargaan disetujui oleh Direksi.');
    }

    // Reject oleh Direksi
    public function reject($id)
    {
        $data = [
            'status' => 'Rejected by Direksi',
            'direksi_id' => session()->get('user_id'),
        ];

        // Update status menjadi "Rejected by Direksi"
        $this->PengajuanPenghargaanModel->update($id, $data);

        return redirect()->to('/approval-direksi')->with('message', 'Pengajuan Penghargaan ditolak oleh Direksi.');
    }
}
