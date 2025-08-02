<?php

// app/Controllers/Pegawai.php
namespace App\Controllers;

use App\Models\PegaweiModel;
use CodeIgniter\Controller;
use App\Models\PegawaiModel;
use App\Models\UserModel;

class Pegawai extends Controller
{
    public function index()
    {
        // Ambil data pegawai dari model
        $pegawaiModel = new UserModel();
        $data['pegawai'] = $pegawaiModel
        ->orderBy('users.created_at' , 'DESC')
        ->findAll();  // Mengambil seluruh data pegawai

        // Kirim data ke view
        return view('dashboard/admin/pegawai', $data);
    }
    // app/Controllers/Pegawai.php
    public function add()
    {
        return view('pegawai/add');
    }
    public function save()
    {
        $model = new PegawaiModel();
        $model->save([
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
        ]);

        return redirect()->to('/pegawai');
    }
    public function edit($id)
    {
        $model = new PegawaiModel();

        // Ambil data pegawai berdasarkan ID
        $data['pegawai'] = $model->find($id);

        // Pastikan pegawai ditemukan
        if (!$data['pegawai']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Data pegawai tidak ditemukan.");
        }

        // Kirim data pegawai ke view edit
        return view('pegawai/edit', $data);
    }

    // Fungsi untuk menyimpan data pegawai yang sudah diedit
    public function update($id)
    {
        $model = new PegawaiModel();

        // Validasi input
        if (!$this->validate([
            'username' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'role' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Simpan perubahan data pegawai
        $model->update($id, [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
        ]);

        // Redirect ke halaman data pegawai setelah update
        return redirect()->to('/pegawai');
    }
    public function delete($id)
    {
        $model = new UserModel();
        $model->delete($id);
        

        return redirect()->to('/pegawai');
    }
    public function approve($id = null)
{
    $pegawaiModel = new UserModel();
    
    // Validasi parameter
    if ($id === null || !is_numeric($id)) {
        session()->setFlashdata('error', 'Parameter ID tidak valid!');
        return redirect()->to('/pegawai');
    }
    
    $id = intval($id);
    
    // Cek apakah data exists
    $existingData = $pegawaiModel->where('id', $id)->first();
    if (!$existingData) {
        session()->setFlashdata('error', 'Data pegawai tidak ditemukan!');
        return redirect()->to('/pegawai');
    }
    
    // Update menggunakan where condition
    $result = $pegawaiModel->where('id', $id)->set(['approved' => 'Approved'])->update();
    
    if ($result) {
        session()->setFlashdata('success', 'Pegawai berhasil disetujui!');
    } else {
        session()->setFlashdata('error', 'Gagal menyetujui pegawai!');
    }
    
    return redirect()->to('/pegawai');
}
}
