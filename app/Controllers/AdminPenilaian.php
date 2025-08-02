<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PenilaianPegawaiModel;

class AdminPenilaian extends BaseController
{
    protected $penilaianModel;

    public function __construct()
    {
        $this->penilaianModel = new PenilaianPegawaiModel();
    }

    public function index()
    {
        // Ambil semua data penilaian
        $penilaian = $this->penilaianModel
        ->select('users.username, u1.username AS name, penilaian_pegawai.*,users.* ')
        ->join('users', 'users.id = penilaian_pegawai.pegawai_id', 'left')
        ->join('users AS u1', 'u1.id = penilaian_pegawai.manajer_id ', 'left')
        
        ->orderBy('penilaian_pegawai.tanggal_penilaian', 'desc')
        ->findAll();

        // Kirim data ke view
        return view('dashboard/admin/rekap_penilaian', ['penilaian' => $penilaian]);
    }
    public function indexDIREKSI()
    {
        // Ambil semua data penilaian
        $penilaian = $this->penilaianModel
        ->select('users.username, u1.username AS name, penilaian_pegawai.*,users.* ')
        ->join('users', 'users.id = penilaian_pegawai.pegawai_id', 'left')
        ->join('users AS u1', 'u1.id = penilaian_pegawai.manajer_id ', 'left')
        
        ->orderBy('penilaian_pegawai.tanggal_penilaian', 'desc')
        ->findAll();
        // Kirim data ke view
        return view('dashboard/direksi/rekap_penilaian', ['penilaian' => $penilaian]);
    }
}
