<?php

namespace App\Controllers;

use App\Models\LaporanKerjamModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Models\LaporanKerjaModel;

class LaporanKerjaManager extends Controller
{
    public function create()
    {
        return view('dashboard/manager/in_laporankerja');
    }

    public function store()
    {
        $validation =  \Config\Services::validation();

        // Validasi file foto/dokumen
        if (!$this->validate([
            'judul' => 'required|min_length[3]',
            'deskripsi' => 'required',
            'foto_dokumen' => 'uploaded[foto_dokumen]|max_size[foto_dokumen,204800]|mime_in[foto_dokumen,image/jpg,image/jpeg,image/png,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Upload file
        $fotoDokumen = $this->request->getFile('foto_dokumen');
        if ($fotoDokumen && $fotoDokumen->isValid()) {
            $fotoDokumenPath = $fotoDokumen->getName();
            $fotoDokumen->move('uploads', $fotoDokumenPath);
        } else {
            // Gunakan path lama jika tidak ada file baru
            $fotoDokumenPath = $this->request->getVar('existing_foto_dokumen');
        }

        // Simpan data laporan
        $laporanModel = new LaporanKerjamModel();
        $data = [
            'divisi' => session()->get('divisi'),
            'user_id' => session()->get('user_id'),
            'judul' => $this->request->getVar('judul'),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'foto_dokumen' => $fotoDokumenPath,
            'divisi_id' => session()->get('divisi_id'),
            'status_approval' => 'Pending',
        ];

        // Simpan laporan ke database
        $laporanModel->simpanLaporan($data);

        return redirect()->to('/laporan_kerja_manager/create')->with('success', 'Laporan kerja berhasil dibuat');
    }

public function riwayat()
{
    // Mendapatkan filter tahun dan status terlebih dahulu
    $year = $this->request->getVar('yearFilter');


    // Membuat objek model LaporanKerja
    $laporanModel = new LaporanKerjamModel();

    // Mendapatkan role dari session
    $role = session()->get('role');

    // Mendapatkan riwayat laporan berdasarkan user_id dan role
    $riwayat = $laporanModel->getLaporanByUser(session()->get('user_id'), $role, $year);

    // Mendapatkan laporan yang sudah disetujui berdasarkan divisi
    $approved = $laporanModel->getLaporanByDivisiID(session()->get('divisi_id'), 'Approved');

    // Mengambil daftar tahun yang unik dari laporan
    $years = array_unique(array_map(function ($laporan) {
        return date('Y', strtotime($laporan['tanggal']));
    }, $riwayat));

    // Mengurutkan tahun secara ascending
    sort($years);


    // Menyusun data untuk tampilan
    $data['laporan'] = $riwayat;
    $data['laporan_approved'] = $approved;
    $data['years'] = $years;

    // Menampilkan view dengan data yang telah disiapkan
    return view('dashboard/manager/rwyt_laporankerja', $data);
}
    public function detailManager($id)
    {
        $laporanModel = new LaporanKerjamModel();
        $userId = session()->get('user_id');
        $roleId = session()->get('role');

        // Panggil model untuk mendapatkan laporan berdasarkan userId, roleId, dan id
        $laporan = $laporanModel->getLaporanByUserManagerShow($userId, $roleId, $id);

        // Cek jika laporan tidak ditemukan
        if (empty($laporan)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak ditemukan');
        }

        // Kirim data laporan ke view
        return view('laporan_kerja_manager/details', ['laporan' => $laporan[0]]);
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM laporan_kerja WHERE id = ?", [$id]);
        // var_dump($query->getResult());
        $data['laporan'] = $query->getResult();
        return view('laporan_kerja_manager/edit', $data);
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
        $laporanModel = new LaporanKerjamModel();
        $laporanModel->updateData($data, $id);
        //dd($this->request->getVar());


        return redirect()->to('/laporan_kerja_manager/riwayat');
    }
    

    

    public function feedback($laporanId, $status)
    {
        $laporanModel = new LaporanKerjamModel();
        $laporanModel->ubahStatusLaporan($laporanId, $status);

        return redirect()->to('laporan_kerja_manager/create
        ')->with('success', 'Status laporan berhasil diperbarui');
    }
    // Persetujuan laporan kerja
    public function approve($id)
    {
        $laporanModel = new LaporanKerjamModel();
        $laporanModel->updateStatusApproval($id, 'Disetujui');

        return redirect()->to('/laporan_kerja')->with('success', 'Laporan Kerja disetujui');
    }

    // Penolakan laporan kerja
    public function reject($id)
    {
        $catatan = $this->request->getPost('catatan');
        $laporanModel = new LaporanKerjamModel();
        $laporanModel->updateStatusApproval($id, 'Ditolak', $catatan);

        return redirect()->to('/laporan_kerja/approval')->with('success', 'Laporan Kerja ditolak');
    }
}
