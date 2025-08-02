<?php

namespace App\Controllers;

use App\Models\PenilaianPegawaiModel;
use App\Models\UserModel;

class PenilaianHRD extends BaseController
{
    protected $penilaianModel;
    protected $userModel;

    public function __construct()
    {
        $this->penilaianModel = new PenilaianPegawaiModel();
        $this->userModel = new UserModel();
    }

    // Menampilkan halaman penilaian pegawai
    public function index()
    {
        $divisi_id = session()->get('divisi_id');  // Mengambil divisi_id dari session manager
        $pegawais = $this->userModel->where('divisi_id', $divisi_id)->where('role', 'pegawai')->findAll();

        return view('dashboard/hrd/penilaian', ['pegawais' => $pegawais]);
    }

    // Menyimpan penilaian pegawai
    public function savePenilaian()
    {
        $pegawai_id = $this->request->getPost('pegawai_id');
        $nilai = $this->request->getPost('nilai');
        $catatan = $this->request->getPost('catatan');
        $manajer_id = session()->get('user_id'); // Mendapatkan id manajer dari session

        // Simpan penilaian ke database
        $this->penilaianModel->save([
            'pegawai_id' => $pegawai_id,
            'manajer_id' => $manajer_id,
            'nilai' => $nilai,
            'catatan' => $catatan,
        ]);

        return redirect()->to('/penilaian-hrd')->with('message', 'Penilaian berhasil disimpan');
    }
}
