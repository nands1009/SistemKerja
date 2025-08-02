<?php

namespace App\Controllers;

use App\Models\EvaluasiKinerjaModel;
use CodeIgniter\Controller;

class EvaluasiKinerja extends Controller
{
    // Fungsi untuk menampilkan rekap evaluasi kinerja berdasarkan role
    public function index()
    {
        $model = new EvaluasiKinerjaModel();
        $data = [];

        // Admin dan Direksi: Melihat semua evaluasi kinerja pegawai
        if (session()->get('role') == 'admin' || session()->get('role') == 'direksi') {
            // Retrieve all evaluasi kinerja
            $data['evaluasi_kinerja'] = $model->findAll();
            return view('evaluasi_kinerja/rekap', $data);
        }

        // Manager: Melihat evaluasi kinerja pegawai di bawahnya
        elseif (session()->get('role') == 'manager') {
            $divisi = session()->get('divisi');
            // Retrieve evaluasi kinerja berdasarkan divisi
            $data['evaluasi_kinerja'] = $model->where('divisi', $divisi)->findAll();
            return view('evaluasi_kinerja/rekap', $data);
        }

        return redirect()->to('/dashboard');
    }

    // Fungsi untuk menampilkan form evaluasi kinerja
    public function add($user_id)
    {
        return view('evaluasi_kinerja/add', ['user_id' => $user_id]);
    }

    // Fungsi untuk menyimpan evaluasi kinerja
    public function save()
    {
        $model = new EvaluasiKinerjaModel();

        // Menyimpan evaluasi kinerja
        $model->save([
            'user_id' => $this->request->getPost('user_id'),
            'divisi' => $this->request->getPost('divisi'),
            'penilaian' => $this->request->getPost('penilaian'),
            'skor' => $this->request->getPost('skor'),
            'evaluasi_dari' => session()->get('username'),  // Nama evaluator dari session
        ]);

        return redirect()->to('/evaluasi_kinerja');
    }
}
