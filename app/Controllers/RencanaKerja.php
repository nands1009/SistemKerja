<?php

namespace App\Controllers;

use App\Models\RencanaKerjaModel;
use App\Models\UserModel;


class RencanaKerja extends BaseController
{
    // Menampilkan daftar rencana kerja pegawai
    public function index()
    {
        $model = new RencanaKerjaModel();
        $data = [];

        // Ambil rencana kerja berdasarkan users_id dari session
        $user_id = session()->get('user_id');
        $rencanaKerja = $model->where('user_id', $user_id)->orderBy('rencana_kerja.tanggal', 'DESC')->findAll();

        $data['rencana_kerja'] = $rencanaKerja;

        // Tampilkan view dengan data
        return view('dashboard/layouts/rencanakerja', $data);
    }

    public function detail($id)
    {
        $model = new RencanaKerjaModel();
        $data = [];

        // Ambil rencana kerja berdasarkan users_id dari session
        $user_id = session()->get('user_id');
        $rencanaKerja = $model->getLaporanByUsershow(session()->get('user_id'), $user_id, $id);

        $data['rencana_kerja'] = $rencanaKerja;

        // Tampilkan view dengan data
        return view('dashboard/layouts/rencanakerja', $data);
    }

    // Menampilkan form untuk menambah rencana kerja
    public function add()
    {
        return view('rencana_kerja/add');
    }

    // Menyimpan rencana kerja baru
    public function save()
    {
        $model = new RencanaKerjaModel();
        $userModel = new UserModel();


        $manager = $userModel->where('divisi_id', session()->get('divisi_id'))->first();
        // Validasi input
        $rules = [
            'judul' => 'required|min_length[3]|max_length[255]',
            'deskripsi' => 'required',
            'tanggal' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/rencana_kerja/add')->withInput();
        }

        // Simpan rencana kerja
        $model->save([
            'user_id' => session()->get('user_id'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'status' => 'Pending',
            'divisi_id' => session()->get('divisi_id'),  
            'manager_id' => $manager['id'] ?? null, 
        ]);



        return redirect()->to('/rencana_kerja');
    }

    // Fungsi untuk mengedit rencana kerja
    public function edit($id)
    {
        // Ambil data rencana kerja dari model berdasarkan ID
        $rencanaModel = new RencanaKerjaModel();
        $rencana = $rencanaModel->find($id);
    
        // Kirim data ke view
        return view('rencana_kerja/edit', ['rencana' => $rencana]);
    }

    public function detailpegawai($id)
    {
        // Ambil data rencana kerja dari model berdasarkan ID
        $rencanaModel = new RencanaKerjaModel();
        $rencana = $rencanaModel->find($id);
        //var_dump($rencana);
    
        // Kirim data ke view
        return view('rencana_kerja/detail', ['rencana' => $rencana]);
    }

    // Fungsi untuk update rencana kerja
    public function update($id)
    {
        $model = new RencanaKerjaModel();

        // Validasi input
        $rules = [
            'judul' => 'required|min_length[3]|max_length[255]',
            'deskripsi' => 'required',
            'tanggal' => 'required|valid_date',
        ];



        // Simpan perubahan
        $model->update($id, [
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'status' => $this->request->getPost('status'),
        ]);

        return redirect()->to('/rencana_kerja');
    }
}
