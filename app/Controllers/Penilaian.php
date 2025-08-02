<?php

namespace App\Controllers;

use App\Models\PenilaianPegawaiModel;
use App\Models\UserModel;

class Penilaian extends BaseController
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

        return view('dashboard/manager/penilaian', ['pegawais' => $pegawais]);
    }

    // Menyimpan penilaian pegawai
    public function savePenilaian()
    {
        // Mendapatkan data yang dikirimkan dari form
        $pegawai_id = $this->request->getPost('pegawai_id');
        $nilai = $this->request->getPost('nilai');
        $catatan = $this->request->getPost('catatan');
        $user_id = session()->get('user_id');  // Mengambil user_id dari session
    
        // Cek apakah user_id ada di session dan valid
        $direksi_id = null;  // Default jika tidak ditemukan
        if ($user_id) {
            // Mendapatkan model UserModel
            $userModel = new \App\Models\UserModel();
    
            // Mencari user dengan role 'direksi'
            $direksi = $userModel->where('role', 'direksi')->first();  // Mengambil data dengan role 'direksi'
            
            // Jika data ditemukan, set direksi_id
            if ($direksi) {
                $direksi_id = $direksi['id'];
            }
        }
    
        // Debugging: Cek apakah direksi_id ditemukan
        if (!$direksi_id) {
            echo "Direksi ID tidak ditemukan!";
            return;
        }
    
        // Siapkan data untuk disimpan
        $data = [
            'manajer_id' => session()->get('user_id'),  // Mengambil ID Manajer dari session
            'pegawai_id' => $pegawai_id,
            'nilai' => $nilai,
            'catatan' => $catatan,
            'direksi_id' => $direksi_id,  // Menyimpan direksi_id yang diambil berdasarkan role
            'tanggal_penilaian' => date('Y-m-d H:i:s')  // Menyimpan tanggal penilaian saat ini
        ];
    
        // Simpan data penilaian ke database
        if ($this->penilaianModel->save($data)) {
            return redirect()->to('/penilaian')->with('message', 'Penilaian berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian');
        }
    }
    

    
}
