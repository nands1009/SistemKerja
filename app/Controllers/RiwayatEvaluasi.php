<?php

namespace App\Controllers;

use App\Models\EvaluasiKinerjaModel;
use App\Models\SPModel;
use CodeIgniter\Controller;
use App\Models\PenilaianPegawaiModel;
use App\Models\PenghargaanModel; 

class RiwayatEvaluasi extends Controller
{



    public function penilaian()
    {

        $penilaianpegawaiModel = new PenilaianPegawaiModel();
        $role = session()->get('role');
        $data['penilaian_pegawai'] = $penilaianpegawaiModel->getPenilaianWithUser(session()->get('user_id'), $role);
        //var_dump($penilaianpegawaiModel);

        return view('dashboard/layouts/riwayat_penilaian', $data);
    }


    public function riwayatsp()
    {
        // Membuat objek model SPModel
        $spModel = new SPModel();
        $role = session()->get('role');
        $data['sppegawai'] = $spModel->getSpByUser(session()->get('user_id'), $role);
        //var_dump($data);

        return view('dashboard/layouts/riwayat_sp', $data);; // Tidak perlu menambah key array karena data sudah ada
    }

    public function riwayatpenghargaan()
    {

        $penghargaanModel = new PenghargaanModel();
        $role = session()->get('role');
        $data['penghargaan'] = $penghargaanModel->getPenghargaanByUser(session()->get('user_id'), $role);
        //var_dump($data);

        return view('dashboard/layouts/riwayat_penghargaan', $data);; // Tidak perlu menambah key array karena data sudah ada
    }

    public function download_pdf($file_name)
    {
        // Path ke folder PDF
        $pdfPath = WRITEPATH . 'uploads/pdf/' . $file_name;

        // Cek jika file ada
        if (file_exists($pdfPath)) {
            // Set header untuk download file
            return $this->response->download($pdfPath, null)->setFileName($file_name);
        } else {
            // Jika file tidak ditemukan
            return redirect()->to('/riwayat_evaluasi/sp')->with('error', 'File tidak ditemukan.');
        }
    }

    public function download_pdfpenghargaan($file_name)
    {
        // Path ke folder PDF
        $pdfPath = WRITEPATH . 'uploads/pdf/' . $file_name;

        // Cek jika file ada
        if (file_exists($pdfPath)) {
            // Set header untuk download file
            return $this->response->download($pdfPath, null)->setFileName($file_name);
        } else {
            // Jika file tidak ditemukan
            return redirect()->to('/riwayat_evaluasi/penghargaan')->with('error', 'File tidak ditemukan.');
        }
    }
}
