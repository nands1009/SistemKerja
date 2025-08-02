<?php

namespace App\Controllers;

use App\Models\RencanaKerjaModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminRencanaKerja extends BaseController
{
    protected $rencanaKerjaModel;

    public function __construct()
    {
        $this->rencanaKerjaModel = new RencanaKerjaModel();
    }

    public function index()
{
    // Ambil semua data rencana kerja
    $rencanaKerja = $this->rencanaKerjaModel
        ->select('users.username, u1.username AS name, rencana_kerja.*,users.*')
        ->join('users', 'users.id = rencana_kerja.user_id', 'left') // join user pembuat rencana
        ->join('users AS u1', 'u1.id = rencana_kerja.manager_id' , 'left') // join user manager
        ->orderBy('rencana_kerja.tanggal', 'desc')
        ->findAll();

    // Debug: Periksa apakah lebih dari dua hasil yang diambil
    ////var_dump($rencanaKerja); // Baris debug sementara
 // Pastikan data ditampilkan lalu berhenti sementara eksekusi

    // Kirim data ke view
    return view('dashboard/admin/rekap_rencana_kerja', ['rencanaKerja' => $rencanaKerja]);
}

}
