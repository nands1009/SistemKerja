<?php

namespace App\Controllers;

use App\Models\RencanaKerjamModel;
use App\Models\UserModel;
use App\Models\DivisiModel;

class RencanaKerjam extends BaseController
{
    protected $rencanaKerjaModel;
    protected $usersModel;
    protected $divisiModel;

    public function __construct()
    {
        $this->rencanaKerjaModel = new RencanaKerjamModel();
        $this->usersModel = new UserModel();
        $this->divisiModel = new DivisiModel();
    }

    // Halaman untuk input rencana kerja
    public function input()
    {
        return view('dashboard/manager/input_rencana_kerja');
    }

    // Proses input rencana kerja
    public function saveRencanaKerja()
    {
        //$manager_id = session()->get('name_role');  // Asumsikan user_id sudah ada di session
        $divisi_id = session()->get('divisi_id'); // Asumsikan divisi_id ada di session

        $data = [
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'status' => $this->request->getPost('status'), // Default status
            'user_id' => session()->get('user_id'),
            'divisi_id' => $divisi_id,
        ];

        $this->rencanaKerjaModel->addRencanaKerja($data);
        return redirect()->to('/rencana-kerja/input')->with('message', 'Rencana Kerja berhasil ditambahkan');
    }

    // Halaman untuk melihat riwayat rencana kerja
    public function riwayat()
    {
        // Ambil riwayat rencana kerja dari manager dan staff dalam divisi
        $rencana_kerja = $this->rencanaKerjaModel
        ->select('rencana_kerja.*, users.username AS user_name, u1.username AS manager_name')
        ->join('users ', 'users.id = rencana_kerja.user_id', 'left')
        ->join('users AS u1', 'u1.id = rencana_kerja.divisi_id', 'left')
        ->where('users.divisi_id', session()->get('divisi_id'))
        ->orderBy('rencana_kerja.tanggal', 'DESC')
        ->findAll();
         //var_dump($rencana_kerja);
        return view('dashboard/manager/riwayat_rencana_kerja', [
            'rencana_kerja' => $rencana_kerja
        ]);
    }
    public function edit($id)
    {
        $rencana = $this->rencanaKerjaModel->find($id);
        if (!$rencana) {
            return redirect()->to('/rencana-kerja/riwayat')->with('error', 'Rencana kerja tidak ditemukan');
        }

        return view('manager/edit_rencana_kerja', ['rencana' => $rencana]);
    }
    // Metode untuk update rencana kerja
    public function update($id)
    {
        // Mengambil data dari form
        $data = [
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'status' => $this->request->getPost('status'),
        ];

        // Mengupdate data berdasarkan ID
        $this->rencanaKerjaModel->update($id, $data);

        // Redirect ke halaman riwayat setelah update berhasil
        return redirect()->to('/rencana-kerja/riwayat')->with('message', 'Rencana Kerja berhasil diperbarui');
    }
}
