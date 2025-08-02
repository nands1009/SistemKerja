<?php

namespace App\Controllers;

use App\Models\PenilaianPegawaiModel;
use App\Models\UserModel;

class PenilaianManagerController extends BaseController
{
    protected $penilaianPegawaiModel;
    protected $userModel;

    public function __construct()
    {
        $this->penilaianPegawaiModel = new PenilaianPegawaiModel();
        $this->userModel = new UserModel();
    }

    // Menampilkan daftar pegawai yang dinilai oleh direksi
    public function index()
{
    // Mendapatkan divisi_id dari session manager
     // Mengambil divisi_id dari session manager

    // Mengambil data pengguna dengan role 'manager' berdasarkan divisi_id
    $users = $this->userModel->whereIn('role', ['manager', 'hrd'])
    ->orderBy('username', 'asc')
    ->findAll();

    // Mengirim data pengguna ke view
    return view('dashboard/direksi/penilaian', ['users' => $users]);
}
    // Menyimpan penilaian pegawai
    public function savePenilaian()
    {
        // Mendapatkan data yang dikirimkan dari form
        $pegawai_id = $this->request->getPost('pegawai_id');
        $nilai = $this->request->getPost('nilai');
        $catatan = $this->request->getPost('catatan');
        $direksi_id = session()->get('user_id'); // Mendapatkan id direksi dari session
        $manajer_id = session()->get('manajer_id'); // Mendapatkan ID Manajer dari form

        // Simpan penilaian ke database
        $this->penilaianPegawaiModel->save([
            'pegawai_id' => $pegawai_id,
            'manajer_id' => $manajer_id,
            'direksi_id' => $direksi_id,  // Menambahkan direksi_id
            'nilai' => $nilai,
            'catatan' => $catatan,
            'tanggal_penilaian' => date('Y-m-d H:i:s')  // Menyimpan tanggal penilaian saat ini
        ]);

        // Redirect setelah data disimpan
        return redirect()->to('/direksi/penilaian_manager')->with('message', 'Penilaian manager berhasil disimpan');
    }
}
