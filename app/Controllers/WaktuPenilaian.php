<?php

// app/Controllers/WaktuPenilaian.php
namespace App\Controllers;

use App\Models\WaktuPenilaianModel;
use CodeIgniter\Controller;

class WaktuPenilaian extends Controller
{
    // Menampilkan daftar waktu penilaian
    public function index()
    {
        $model = new WaktuPenilaianModel();
        $data['waktu_penilaian'] = $model->findAll(); // Mengambil semua data waktu penilaian
        return view('dashboard/admin/waktu_penilaian', $data);
    }

    // Menampilkan form tambah waktu penilaian
    public function add()
    {
        return view('waktu_penilaian/add');
    }

    // Menyimpan waktu penilaian baru
    public function save()
    {
       
        // Validasi input
        if (!$this->validate([
            'tanggal_mulai' => 'required|valid_date[Y-m-d\TH:i]',
            'tanggal_selesai' => 'required|valid_date[Y-m-d\TH:i]',
        ])) {
            return redirect()->to('/waktu_penilaian')->withInput()->with('validation', $this->validator);
        }

        // Ambil data input
        $data = [
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
        ];

        // Simpan data
        (new WaktuPenilaianModel())->save($data);

        // Kirim notifikasi
        $this->sendNotification();

        return redirect()->to('/waktu_penilaian');
    }

    // Menampilkan form edit waktu penilaian
    public function edit($id)
    {
        $model = new WaktuPenilaianModel();
        $data['waktu_penilaian'] = $model->find($id); // Menampilkan data waktu penilaian berdasarkan ID
        return view('waktu_penilaian/edit', $data);
    }

    // Menyimpan perubahan waktu penilaian
    public function update($id)
    {
        $model = new WaktuPenilaianModel();
        $model->update($id, [
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
        ]);

        return redirect()->to('/waktu_penilaian');
    }

    // Menghapus waktu penilaian
    public function delete($id)
    {
        $model = new WaktuPenilaianModel();
        $model->delete($id);
        return redirect()->to('/waktu_penilaian');
    }

    // Mengirim notifikasi ke Manager, HRD, dan Direksi
    private function sendNotification()
    {
        // Ini hanya contoh, Anda bisa mengintegrasikan dengan sistem notifikasi seperti email atau menggunakan session untuk notifikasi.
        // Simulasi notifikasi
        $users = ['manager', 'hrd', 'direksi']; // Misalnya, role yang menerima notifikasi
        foreach ($users as $user) {
            // Kirim notifikasi ke masing-masing user
            // Anda bisa menghubungkan sistem ini dengan email atau sistem notifikasi lainnya
            session()->setFlashdata('message', "Notifikasi: Waktu penilaian telah diatur.");
        }
    }
}
