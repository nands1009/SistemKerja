<?php

namespace App\Controllers;

use App\Models\LaporanKerjaModel;
use CodeIgniter\Controller;
use App\Models\UserModel;


class LaporanKerja extends Controller
{
    // Menampilkan halaman untuk membuat laporan kerja
    public function create()
    {
        return view('dashboard/layouts/in_laporankerja');
    }

    // Menyimpan laporan baru
    public function store()
    {
        $validation = \Config\Services::validation();
        $userModel = new UserModel();


        $manager = $userModel->where('divisi_id', session()->get('divisi_id'))->first();

        // Validasi form laporan
        if (!$this->validate([
            'judul' => 'required|min_length[3]',
            'deskripsi' => 'required',
            'foto_dokumen' => 'uploaded[foto_dokumen]|max_size[foto_dokumen,20480]|mime_in[foto_dokumen,image/jpg,image/jpeg,image/png,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Meng-upload file
        $fotoDokumen = $this->request->getFile('foto_dokumen');
        $fotoDokumenPath = $fotoDokumen->getName();

        // Memindahkan file ke folder yang tepat
        $fotoDokumen->move('uploads', $fotoDokumenPath);


        // Menyimpan data laporan
        $laporanModel = new LaporanKerjaModel();
        $data = [
            'divisi_id' => session()->get('divisi_id'),
            'user_id' => session()->get('user_id'),
            'judul' => $this->request->getVar('judul'),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'foto_dokumen' => $fotoDokumenPath,
            'status' => $this->request->getPost('status'), // Default status
            'manager_id' => $manager['id'] ?? null,
        ];

        // Menyimpan laporan ke database
        $laporanModel->simpanLaporan($data);

        return redirect()->to('/laporan_kerja')->with('success', 'Laporan kerja berhasil dibuat');
    }

    // Menampilkan riwayat laporan kerja
public function riwayat()
{
    $laporanModel = new LaporanKerjaModel();
    $role = session()->get('role');
    $userId = session()->get('user_id');
    
    // Ambil tahun dan status dari request
    $year = $this->request->getVar('yearFilter');
    $status = $this->request->getVar('statusFilter');
    
    // Filter riwayat laporan berdasarkan tahun dan status (jika ada)
    $riwayat = $laporanModel->getLaporanByUser($userId, $role, $year, $status);

    // Mengambil tahun unik yang ada di laporan untuk filter dropdown
    $years = array_unique(array_map(function($laporan) {
        return date('Y', strtotime($laporan['tanggal']));
    }, $riwayat));

    sort($years); // Urutkan tahun dari yang terbaru

    $data['laporan'] = $riwayat;
    $data['years'] = $years; // Kirim tahun ke view

    return view('dashboard/layouts/rwyt_laporankerja', $data);
}

    public function details($id)
    {
        $laporanModel = new LaporanKerjaModel();
        $role = session()->get('role');
        $riwayat = $laporanModel->getLaporanByUsershow(session()->get('user_id'), $role, $id);
        //var_dump($riwayat);
        $data['laporan'] = $riwayat;
        return view('/laporan_kerja/details', $data);
    }

    // Menangani feedback pada laporan kerja
    public function feedback($laporanId, $status)
    {
        $laporanModel = new LaporanKerjaModel();
        $laporanModel->ubahStatusLaporan($laporanId, $status);

        return redirect()->to('/laporan_kerja/riwayat')->with('success', 'Status laporan berhasil diperbarui');
    }

    // Menampilkan halaman untuk mengedit laporan kerja
    public function edit($id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM laporan_kerja WHERE id = ?", [$id]);
        // var_dump($query->getResult());
        $data['laporan'] = $query->getResult();
        return view('laporan_kerja/edit', $data);
    }

    // Menyimpan perubahan laporan
    public function update($id)
    {
        $validation = \Config\Services::validation();

        // Validasi form laporan
        if (!$this->validate([
            'judul' => 'required|min_length[1]',
            'deskripsi' => 'required',


        ])) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Update file foto/dokumen jika ada
        $fotoDokumen = $this->request->getFile('foto_dokumen');
        if ($fotoDokumen && $fotoDokumen->isValid()) {
            $fotoDokumenPath = $fotoDokumen->getName();
            $fotoDokumen->move('uploads', $fotoDokumenPath);
        } else {
            // Gunakan path lama jika tidak ada file baru
            $fotoDokumenPath = $this->request->getVar('existing_foto_dokumen');
        }

        // Menyimpan perubahan data laporan
        $data = [
            'judul' => $this->request->getVar('judul'),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'foto_dokumen' => $fotoDokumenPath,
            'status' => $this->request->getPost('status')
        ];

        // Memperbarui laporan berdasarkan ID
        $laporanModel = new LaporanKerjaModel();
        $laporanModel->updateData($data, $id);
        //dd($this->request->getVar());


        return redirect()->to('/laporan_kerja/riwayat');
    }

   
}
