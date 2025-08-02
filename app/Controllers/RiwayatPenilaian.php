<?php

namespace App\Controllers;

use App\Models\PenilaianPegawaiModel;
use App\Models\UserModel;

class RiwayatPenilaian extends BaseController
{
    protected $PenilaianPegawaiModel;
    protected $UserModel;

    public function __construct()
    {
        $this->PenilaianPegawaiModel = new PenilaianPegawaiModel();
        $this->UserModel = new UserModel();
    }

    // Menampilkan riwayat penilaian
    public function index()
    {
        // Ambil id manajer dan divisi_id dari session
        $manajer_id = session()->get('user_id');
        $divisi_id = (int) session()->get('divisi_id'); // casting untuk keamanan

        // Ambil riwayat penilaian manajer dan pegawai dalam divisinya
        $riwayat = $this->PenilaianPegawaiModel
    ->select('users.username, penilaian_pegawai.*, u1.username AS name_manager')
    ->join('users', 'users.id = penilaian_pegawai.pegawai_id', 'left')  // Penyesuaian dengan penilaian_pegawai
    ->join('users AS u1', 'u1.id = penilaian_pegawai.manajer_id', 'left') // Penyesuaian dengan manager_id
    ->where('penilaian_pegawai.manajer_id', session()->get('user_id'))
    ->orderBy('penilaian_pegawai.tanggal_penilaian', 'DESC') 
    ->findAll();
        return view('dashboard/manager/riwayat_penilaian', ['riwayat' => $riwayat]);
    }
    public function indexHRD()
    {
        // Ambil id manajer dan divisi_id dari session
        $manajer_id = session()->get('user_id');
        $divisi_id = (int) session()->get('divisi_id'); // casting untuk keamanan

        // Ambil riwayat penilaian manajer dan pegawai dalam divisinya
        $riwayat = $this->PenilaianPegawaiModel
    ->select('users.username, penilaian_pegawai.*, u1.username AS name_manager')
    ->join('users', 'users.id = penilaian_pegawai.pegawai_id', 'left')  // Penyesuaian dengan penilaian_pegawai
    ->join('users AS u1', 'u1.id = penilaian_pegawai.manajer_id', 'left') // Penyesuaian dengan manager_id
    ->where('penilaian_pegawai.manajer_id', session()->get('user_id'))
    ->orderBy('penilaian_pegawai.tanggal_penilaian', 'DESC') 
    ->findAll();
        return view('dashboard/hrd/riwayat_penilaian', ['riwayat' => $riwayat]);
    }
}