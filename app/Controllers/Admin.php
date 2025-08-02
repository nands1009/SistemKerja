<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LaporanKerjaModel;
use App\Models\UserModel;

class Admin extends BaseController
{

    protected $laporanKerjaModel;

    public function __construct()
    {
        $this->laporanKerjaModel = new LaporanKerjaModel();
    }

public function index()
{
    // Mengambil nilai year dari request
    $year = $this->request->getVar('yearFilter');

    // Ambil semua data laporan kerja dengan join
    $builder = $this->laporanKerjaModel
        ->select('users.username, u1.username AS name, laporan_kerja.*, users.*')
        ->join('users', 'users.id = laporan_kerja.user_id', 'left')
        ->join('users AS u1', 'u1.id = laporan_kerja.manager_id', 'left')
        ->orderBy('laporan_kerja.tanggal', 'desc');

    // Menambahkan filter untuk tahun jika ada
    if ($year) {
        $builder->like('laporan_kerja.tanggal', $year, 'after'); // Mengambil laporan yang tahunannya sesuai
    }

    // Eksekusi query dan ambil hasilnya
    $laporan = $builder->get()->getResultArray();

    // Mengambil daftar tahun yang unik dari laporan
    $years = array_unique(array_map(function ($laporanItem) {
        return date('Y', strtotime($laporanItem['tanggal']));
    }, $laporan));

    // Mengurutkan tahun secara ascending
    sort($years);

    // Kirim data ke view
    return view('dashboard/admin/rekap_laporan', ['laporan' => $laporan, 'years' => $years]);
}
    public function indexDIREKSI()
    {
        // Ambil semua data laporan kerja
        $laporan = $this->laporanKerjaModel
        ->select('users.username, u1.username AS name, laporan_kerja.*,users.* ')
        ->join('users', 'users.id = laporan_kerja.user_id', 'left')
        ->join('users AS u1', 'u1.id = laporan_kerja.manager_id', 'left')
        ->where('laporan_kerja.status_approval !=','Rejected')
        ->orderBy('laporan_kerja.tanggal', 'desc')
        ->findAll();
     

        // Kirim data ke view
        return view('dashboard/direksi/rekap_laporan', ['laporan' => $laporan] );
    }
     public function deleteAllData($id)
    {
        $userModel = new UserModel();
        $laporanModel = new LaporanKerjaModel();

        // Cek apakah user ada
        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin')->with('error', 'User tidak ditemukan');
        }

        // Hapus laporan terkait user
        $laporanModel->where('user_id', $id)->delete();
        $userModel->where('id', $id)->delete();

        // Hapus penilaian terkait user
 // Hapus penilaian berdasarkan laporan

        // Hapus user
        $userModel->delete($id);

        // Redirect ke halaman admin dengan pesan sukses
        return redirect()->to('dashboard/admin/rekap_laporan')->with('success', 'User dan data terkait berhasil dihapus');
    }
}
