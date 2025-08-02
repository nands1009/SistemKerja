<?php

namespace App\Controllers;

use App\Models\PenghargaanModel;
use App\Models\SPModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Pengajuan extends Controller
{
    protected $penghargaanModel;
    protected $spModel;
    protected $userModel;

    public function index()
    {
        // Menampilkan form upload
        return view('pengajuan/riwayat_pengajuan');
    }

    public function __construct()
    {
        $this->penghargaanModel = new PenghargaanModel();
        $this->spModel = new SpModel();
        $this->userModel = new UserModel();
    }

    // Menampilkan halaman untuk pengajuan penghargaan atau SP
    public function ajukan()
    {
        $divisi_id = session()->get('divisi_id');  // Mengambil divisi_id dari session manager
        $pegawais = $this->userModel->where('divisi_id', $divisi_id)->where('role', 'pegawai')->findAll();
        //var_dump($pegawais);

        return view('dashboard/manager/pengajuan_manager', ['pegawais' => $pegawais]);
    }

    public function ajukanHRD()
    {
        $divisi_id = session()->get('divisi_id');  // Mengambil divisi_id dari session manager
        $pegawais = $this->userModel->where('divisi_id', $divisi_id)->where('role', 'pegawai')->findAll();
        var_dump($pegawais);

        return view('dashboard/hrd/pengajuan', ['pegawais' => $pegawais]);
    }

    // Menyimpan pengajuan penghargaan
    public function submitPenghargaan()
    {
        $data = [
            'manajer_id' => session()->get('user_id'), // Mendapatkan id manajer dari session
            'pegawai_id' => $this->request->getPost('pegawai_id'),
            'jenis_penghargaan' => $this->request->getPost('jenis_penghargaan'),
            'alasan' => $this->request->getPost('alasan'),
            'status' => 'Pending',

            
        ];

        // Menyimpan data pengajuan penghargaan
        $this->penghargaanModel->insert($data);
        return redirect()->to('/pengajuan/ajukan')->with('success', 'Pengajuan penghargaan berhasil!');
    }

    public function submitPenghargaanHrd()
    {
        $data = [
            'manajer_id' => session()->get('user_id'), // Mendapatkan id manajer dari session
            'pegawai_id' => $this->request->getPost('pegawai_id'),
            'jenis_penghargaan' => $this->request->getPost('jenis_penghargaan'),
            'alasan' => $this->request->getPost('alasan'),
            'status' => 'Pending',

            
        ];

        // Menyimpan data pengajuan penghargaan
        $this->penghargaanModel->insert($data);
        return redirect()->to('pengajuan-hrd/ajukan')->with('success', 'Pengajuan penghargaan berhasil!');
    }

    // Menyimpan pengajuan Surat Peringatan (SP)
    public function submitSP()
    {
        $data = [
            'manajer_id' => session()->get('user_id'), // Mendapatkan id manajer dari session
            'pegawai_id' => $this->request->getPost('pegawai_id'),
            'alasan' => $this->request->getPost('alasan'),
            'status' => 'Pending',

        ];

        // Menyimpan data pengajuan SP
        $this->spModel->insert($data);
        return redirect()->to('/pengajuan/ajukan')->with('success', 'Pengajuan SP berhasil!');
    }
        public function submitSPHrd()
    {
        $data = [
            'manajer_id' => session()->get('user_id'), // Mendapatkan id manajer dari session
            'pegawai_id' => $this->request->getPost('pegawai_id'),
            'alasan' => $this->request->getPost('alasan'),
            'status' => 'Pending',

        ];

        // Menyimpan data pengajuan SP
        $this->spModel->insert($data);
        return redirect()->to('pengajuan-hrd/ajukan')->with('success', 'Pengajuan SP berhasil!');
    }


    public function uploadsp($id)  // Ganti $pegawaiId dengan $id
    {
        $pdfFile = $this->request->getFile('pdf_file');
        $spModel = new \App\Models\SPModel();
    
        // Cari data SP berdasarkan ID
        $sp = $spModel->find($id);  
    
        // Jika data SP tidak ditemukan
        if (!$sp) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
    
        // Validasi status dan file
        if ($sp['status'] !== 'Approved DIREKSI') {
            return redirect()->back()->with('error', 'Hanya data dengan status "Approved DIREKSI" yang bisa diunggah.');
        }
    
        if ($pdfFile && $pdfFile->isValid() && $pdfFile->getClientMimeType() === 'application/pdf') {
            $newFileName = $pdfFile->getRandomName();
            $pdfFile->move(WRITEPATH . 'uploads/pdf', $newFileName);
    
            $data = [
                'file_name' => $newFileName,
                'file_path' => 'uploads/pdf/' . $newFileName,
            ];
    
            // Update data berdasarkan ID
            $spModel->update($id, $data);  // Gunakan update() untuk memperbarui berdasarkan ID
    
            return redirect()->back()->with('success', 'PDF berhasil diunggah.');
        } else {
            return redirect()->back()->with('error', 'File tidak valid. Pastikan itu adalah PDF.');
        }
    }

    public function uploadpenghargaan($id)
{
    // Ambil file PDF yang diupload
    $pdfFile = $this->request->getFile('pdf_file');
    $penghargaanModel = new \App\Models\PenghargaanModel();

    // Cari data penghargaan berdasarkan ID penghargaan
    $penghargaan = $penghargaanModel->find($id); // Cari berdasarkan $id yang diterima dari URL
    
    // Jika data penghargaan tidak ditemukan
    if (!$penghargaan) {
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    // Pastikan hanya data dengan status "Approved DIREKSI" yang bisa diunggah
    if ($penghargaan['status'] !== 'Approved DIREKSI') {
        return redirect()->back()->with('error', 'Hanya data dengan status "Approved DIREKSI" yang bisa diunggah.');
    }

    // Validasi file PDF
    if ($pdfFile && $pdfFile->isValid() && $pdfFile->getClientMimeType() === 'application/pdf') {
        // Buat nama file baru secara acak untuk mencegah bentrok
        $newFileName = $pdfFile->getRandomName();
        
        // Tentukan lokasi penyimpanan file
        $pdfFile->move(WRITEPATH . 'uploads/pdf', $newFileName);

        // Siapkan data untuk diperbarui
        $data = [
            'file_name' => $newFileName,
            'file_path' => 'uploads/pdf/' . $newFileName,
        ];

        // Update data penghargaan berdasarkan ID
        $penghargaanModel->update($id, $data); // Gunakan update() untuk memperbarui berdasarkan $id

        // Kembalikan response dengan pesan sukses
        return redirect()->back()->with('success', 'PDF berhasil diunggah.');
    } else {
        // Jika file tidak valid, kembalikan error
        return redirect()->back()->with('error', 'File tidak valid. Pastikan itu adalah PDF.');
    }
}
    
    
    
    // Fungsi untuk approve pengajuan dari HRD
    public function approveHrd($id, $jenis)
    {
        if ($jenis == 'penghargaan') {
            $this->penghargaanModel->update($id, ['status' => 'Approved HRD']);
        } else {
            $this->spModel->update($id, ['status' => 'Approved HRD']);
        }

        return redirect()->to('/pengajuan/riwayat_hrd')->with('success', 'Pengajuan diteruskan ke Direksi!');
    }

    // Fungsi untuk reject pengajuan dari HRD
    public function rejectHrd($id, $jenis)
    {
        $catatan_penolakan = $this->request->getPost('catatan_penolakan');

        if ($jenis == 'penghargaan') {
            $this->penghargaanModel->update($id, [
                'status' => 'Rejected HRD',
                'catatan_penolakan' => $catatan_penolakan
            ]);
        } else {
            $this->spModel->update($id, [
                'status' => 'Rejected HRD',
                'catatan_penolakan' => $catatan_penolakan
            ]);
        }

        return redirect()->to('/pengajuan/riwayat_hrd')->with('error', 'Pengajuan ditolak oleh HRD!');
    }

    public function rejectDireksi($id, $jenis)
    {
        $catatan_penolakan = $this->request->getPost('catatan_penolakan');

        if ($jenis == 'penghargaan') {
            $this->penghargaanModel->update($id, [
                'status' => 'Rejected DIREKSI',
                'catatan_penolakan' => $catatan_penolakan
            ]);
        } else {
            $this->spModel->update($id, [
                'status' => 'Rejected DIREKSI',
                'catatan_penolakan' => $catatan_penolakan
            ]);
        }

        return redirect()->to('/pengajuan/riwayat_direksi')->with('error', 'Pengajuan ditolak oleh HRD!');
    }

    // Menampilkan riwayat pengajuan penghargaan atau SP
    public function riwayatPengajuan()
    {
        $riwayatPenghargaan = $this->penghargaanModel
        ->select('users.username,penghargaan.id,penghargaan.file_path,penghargaan.file_name, penghargaan.pegawai_id, penghargaan.jenis_penghargaan, penghargaan.status, penghargaan.alasan, penghargaan.catatan_penolakan, penghargaan.created_at, penghargaan.updated_at')
        ->join('users', 'users.id = penghargaan.pegawai_id', 'left')
        ->where('manajer_id', session()->get('user_id'))
        ->orderBy('penghargaan.updated_at', 'DESC')
        ->findAll();

        $riwayatSP = $this->spModel
        //->select('pegawai_id,alasan, status,catatan_penolakan ')->where('manajer_id', session()->get('user_id'))->findAll();
        ->select('users.username,sp.id,sp.file_path,sp.file_name, sp.pegawai_id, sp.alasan, sp.status, sp.catatan_penolakan, sp.created_at, sp.updated_at')
        ->join('users', 'users.id = sp.pegawai_id', 'left')
        ->orderBy('sp.updated_at', 'DESC')
        ->where('manajer_id', session()->get('user_id'))
        ->findAll();
        
       //var_dump($riwayatPenghargaan);
        return view('dashboard/manager/riwayat_pengajuan', [
            'riwayat_penghargaan' => $riwayatPenghargaan,
            'riwayat_sp' => $riwayatSP
        ]);
    }
    public function riwayatPengajuanHRD()
    {
        $riwayatPenghargaan = $this->penghargaanModel
        ->select('users.username,penghargaan.id,penghargaan.file_path,penghargaan.file_name, penghargaan.pegawai_id, penghargaan.jenis_penghargaan, penghargaan.status, penghargaan.alasan, penghargaan.catatan_penolakan, penghargaan.created_at, penghargaan.updated_at')
        ->join('users', 'users.id = penghargaan.pegawai_id', 'left')
        ->where('manajer_id', session()->get('user_id'))
        ->orderBy('penghargaan.updated_at','DESC')
        ->findAll();

        $riwayatSP = $this->spModel
        //->select('pegawai_id,alasan, status,catatan_penolakan ')->where('manajer_id', session()->get('user_id'))->findAll();
        ->select('users.username,sp.id,sp.file_path,sp.file_name, sp.pegawai_id, sp.alasan, sp.status, sp.catatan_penolakan, sp.created_at, sp.updated_at')
        ->join('users', 'users.id = sp.pegawai_id', 'left')
        ->where('manajer_id', session()->get('user_id'))
        ->orderBy('sp.updated_at','DESC')
        ->findAll();
        
       //var_dump($riwayatPenghargaan);
        return view('dashboard/hrd/riwayat_pengajuan', [
            'riwayat_penghargaan' => $riwayatPenghargaan,
            'riwayat_sp' => $riwayatSP
        ]);
    }

    public function riwayatHrd()
    {
        // Correcting the where method to use whereIn for multiple values of status
        $riwayatPenghargaan = $this->penghargaanModel
       ->select('users.username, u1.username AS name, penghargaan.* ')
        ->join('users', 'users.id = penghargaan.pegawai_id', 'left')
        ->join('users AS u1', 'u1.id = penghargaan.manajer_id', 'left')
        ->whereIn('penghargaan.status', ['Pending', 'Approved HRD'])
        ->orderBy('created_at', 'desc')
        ->findAll(); 
        //$riwayatPenghargaan = $this->penghargaanModel->whereIn('status', ['Pending', 'Approved HRD'])->findAll();
        //$riwayatPenghargaan = $this->penghargaanModel->orderBy('created_at', 'desc')->findAll();
        $riwayatSP = $this->spModel
        ->select('users.username, u1.username AS name, sp.* ')
        ->join('users', 'users.id = sp.pegawai_id', 'left')
        ->join('users AS u1', 'u1.id = sp.manajer_id', 'left')
        ->whereIn('sp.status', ['Pending', 'Approved HRD'])
        ->orderBy('created_at', 'desc')
        ->findAll();
        
        
        
        return view('dashboard/hrd/approval_penghargaan', [
            'riwayat_penghargaan' => $riwayatPenghargaan,
            'riwayat_sp' => $riwayatSP
        ]);
    }

    // Menampilkan riwayat pengajuan ke Direksi
    public function riwayatDireksi()
    {
        $riwayatPenghargaan = $this->penghargaanModel
        ->select('users.username, u1.username AS name, penghargaan.* ')
        ->join('users', 'users.id = penghargaan.pegawai_id', 'left')
        ->join('users AS u1', 'u1.id = penghargaan.manajer_id', 'left')
        ->whereIn('penghargaan.status', ['Approved HRD'])
        ->orderBy('created_at', 'desc')
        ->findAll();
        $riwayatSP = $this->spModel
        ->select('users.username, u1.username AS name, sp.* ')
        ->join('users', 'users.id = sp.pegawai_id', 'left')
        ->join('users AS u1', 'u1.id = sp.manajer_id', 'left')
        ->whereIn('sp.status', ['Approved HRD'])
        ->orderBy('created_at', 'desc')
        ->findAll();

        return view('dashboard/direksi/approval_penghargaan', [
            'riwayat_penghargaan' => $riwayatPenghargaan,
            'riwayat_sp' => $riwayatSP
        ]);
    }

    // Fungsi untuk approve pengajuan dari Direksi
    public function approveDireksi($id, $jenis)
    {
        // Pastikan pengajuan sudah "Approved HRD" sebelum bisa diapprove oleh Direksi
        $pengajuan = $jenis == 'penghargaan'
            ? $this->penghargaanModel->find($id)
            : $this->spModel->find($id);
    
        // Check if pengajuan exists and if it's approved by HRD and not yet approved by Direksi
        if ($pengajuan && isset($pengajuan['status'])) {
            if ($pengajuan['status'] == 'Approved HRD') {
                // If the jenis is 'penghargaan', approve by Direksi
                if ($jenis == 'penghargaan') {
                    $this->penghargaanModel->update($id, [
                        'status' => 'Approved Direksi',
                        'updated_at' => date('Y-m-d H:i:s') // Memperbarui timestamp
                    ]);;
                    
                } else {
                    //$this->spModel->update($id, ['status' => 'Approved Direksi']);
                    $this->spModel->update($id, [
                        'status' => 'Approved Direksi',
                        'updated_at' => date('Y-m-d H:i:s') // Memperbarui timestamp
                    ]);;

                }
                return redirect()->to('/pengajuan/riwayat_direksi')->with('success', 'Pengajuan telah disetujui oleh Direksi!');
            } else {
                return redirect()->to('/pengajuan/riwayat_direksi')->with('error', 'Direksi tidak bisa menyetujui pengajuan sebelum HRD menyetujui!');
            }
        }
    
        return redirect()->to('/pengajuan/riwayat_direksi')->with('error', 'Pengajuan tidak ditemukan!');
    }

    // Fungsi hapus pengajuan
    public function hapus($id, $jenis)
    {
        if ($jenis == 'penghargaan') {
            $this->penghargaanModel->delete($id);
        } elseif ($jenis == 'sp') {
            $this->spModel->delete($id);
        }

        // Redirect setelah penghapusan selesai
        return redirect()->to('/pengajuan/riwayat_pengajuan')->with('success', 'Data berhasil dihapus!');
    }
}

