<?php

namespace App\Controllers;


use App\Models\DashboardPegawaiModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class DashboardPegawai extends Controller

{


    public function nama()
    {
        $namaModel = new DashboardPegawaiModel();
        $role = session()->get('role');
        
        // Ambil nama berdasarkan user_id dan role dari session
        $nama = $namaModel->getName(session()->get('user_id'), $role);
        
        // Simpan nama ke dalam data array
        $data['nama'] = $nama;
        
        // Ambil waktu saat ini
        $time = new Time('now', 'Asia/Jakarta');
        setlocale(LC_TIME, 'id_ID.UTF-8');
        
        // Simpan waktu saat ini ke dalam data array
        $data['currentTime'] = $time->toLocalizedString(' HH:mm:ss , EEEE, d MMMM yyyy');
        
        // Mengirim data ke view
        return view('/dashboard', $data);

    }

}    