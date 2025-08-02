<?php

namespace App\Controllers;

use App\Models\LaporanKerjamModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class LaporanKerjaHRD extends Controller
{
    public function create()
    {
        return view('dashboard/hrd/laporan_kerja_create');
    }

    public function store()
    {
        $validation =  \Config\Services::validation();

        // Validasi file foto/dokumen
        if (!$this->validate([
            'judul' => 'required|min_length[3]',
            'deskripsi' => 'required',
            'foto_dokumen' => 'uploaded[foto_dokumen]|max_size[foto_dokumen,2048]|mime_in[foto_dokumen,image/jpg,image/jpeg,image/png,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]'
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

        return redirect()->to('/laporan_kerja_create')->with('success', 'Laporan kerja berhasil dibuat');
    }

    public function riwayat()
    {
        $laporanModel = new LaporanKerjamModel();
        $role = session()->get('role');
        $riwayat = $laporanModel->getLaporanByUser(session()->get('user_id'), $role);
        $approved = $laporanModel->getLaporanByDivisiID(session()->get('divisi_id'), 'Approved');

        $data['laporan'] = $riwayat;
        $data['laporan_approved'] = $approved;
        //var_dump(session()->get('role'));
        return view('dashboard/hrd/rwyt_laporankerja' , $data);
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
