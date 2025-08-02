<?php

namespace App\Controllers;

use App\Models\DashboardPegawaiModel;
use App\Models\UserModel;
use App\Models\WaktuPenilaianModel;
use CodeIgniter\I18n\Time;
use App\Models\UnansweredQuestionModel;
use App\Models\AnsweredQuestionsModel; // Pastikan model ini ada

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        $data = []; // Inisialisasi array data

        switch ($role) {
            case 'admin':
                $data['nama'] = (new UserModel())->getName(session()->get('user_id'), $role);
                $data['currentTime'] = (new Time('now', 'Asia/Jakarta'))->toLocalizedString(' HH:mm:ss , EEEE, d MMMM yyyy');
                $data['pendingQuestions'] = (new UnansweredQuestionModel())->getPendingQuestions();
                $data['answeredQuestions'] = (new UnansweredQuestionModel())->getAnsweredQuestions();
                
                // Memanggil fungsi untuk mengambil data pertanyaan yang sering ditanyakan
                $this->loadFrequentQuestionsData($data);
                
                return view('dashboard-admin', $data);

            case 'manager':
                $data['nama'] = (new UserModel())->getName(session()->get('user_id'), $role);
                $data['currentTime'] = (new Time('now', 'Asia/Jakarta'))->toLocalizedString(' HH:mm:ss , EEEE, d MMMM yyyy');
                $data['spapproved'] = (new UserModel())->getSpByManager(session()->get('user_id'), $role);
                $data['sprejected'] = (new UserModel())->getSpRejectByManager(session()->get('user_id'), $role);
                $data['penghargaanapproved'] = (new UserModel())->getPenghargaanByManager(session()->get('user_id'), $role);
                $data['penghargaanrejected'] = (new UserModel())->getPenghargaanRejectByManager(session()->get('user_id'), $role);
                $data['waktupenilaian'] = (new WaktuPenilaianModel())->findAll();
                return view('dashboard-manager', $data);

            case 'hrd':
                $data['nama'] = (new UserModel())->getName(session()->get('user_id'), $role);
                $data['currentTime'] = (new Time('now', 'Asia/Jakarta'))->toLocalizedString(' HH:mm:ss , EEEE, d MMMM yyyy');
                $data['spapproved'] = (new UserModel())->getSPbyHrd();
                $data['penghargaanapproved'] = (new UserModel())->getPernghargaanbyHrd();
                $data['waktupenilaian'] = (new WaktuPenilaianModel())->findAll();
                $data['spapprovedSP'] = (new UserModel())->getSpByUserHrd(session()->get('user_id'), $role);
                $data['sprejectedSP'] = (new UserModel())->getSpRejectByuserHRD(session()->get('user_id'), $role);
                $data['penghargaanapprovedP'] = (new UserModel())->getPenghargaanByUserHRD(session()->get('user_id'), $role);
                $data['penghargaanrejectedP'] = (new UserModel())->getPenghargaanRejectByUserHRD(session()->get('user_id'), $role);
                return view('dashboard-hrd', $data);

            case 'direksi':
                $data['nama'] = (new UserModel())->getName(session()->get('user_id'), $role);
                $data['currentTime'] = (new Time('now', 'Asia/Jakarta'))->toLocalizedString(' HH:mm:ss , EEEE, d MMMM yyyy');
                $data['spapproved'] = (new UserModel())->getSpByDireksi(session()->get('user_id'), $role);
                $data['penghargaanapproved'] = (new UserModel())->getPenghargaanByDireksi(session()->get('user_id'), $role);
                $data['waktupenilaian'] = (new WaktuPenilaianModel())->findAll();
                return view('dashboard-direksi', $data);

            case 'pegawai':
                $data['nama'] = (new UserModel())->getName(session()->get('user_id'), $role);
                $data['currentTime'] = (new Time('now', 'Asia/Jakarta'))->toLocalizedString(' HH:mm:ss , EEEE, d MMMM yyyy');
                $data['rejectCount'] = (new UserModel())->getlaporan(session()->get('user_id'), $role);
                $data['sppegawai'] = (new UserModel())->getSpByUser(session()->get('user_id'), $role);
                $data['penghargaanpegawai'] = (new UserModel())->getPenghargaanByUser(session()->get('user_id'), $role);
                return view('dashboard-pegawai', $data);

            default:
                return view('dashboard-default', $data); // Ganti dengan view default jika role tidak ditemukan
        }
    }

    // Fungsi untuk memuat data pertanyaan yang sering ditanyakan
    private function loadFrequentQuestionsData(&$data)
    {
        $model = new AnsweredQuestionsModel();

        // Ambil parameter dari URL
        $limit = $this->request->getGet('limit') ?? 10;

        // Validasi limit
        $limit = (int)$limit;
        if ($limit <= 0 || $limit > 100) {
            $limit = 10; // Default limit jika tidak valid
        }

        // Ambil data pertanyaan yang sering ditanyakan
        $frequentQuestions = $model->countFrequentQuestions(100); // Ambil lebih banyak data untuk perhitungan tag

        // Hitung frekuensi berdasarkan tag
        $tagFrequencies = [];

        foreach ($frequentQuestions as $question) {
            $tag = $question['tag'];

            // Inisialisasi jika tag belum ada dalam array
            if (!isset($tagFrequencies[$tag])) {
                $tagFrequencies[$tag] = [
                    'total_frequency' => 0,
                    'question_count' => 0
                ];
            }

            // Tambahkan frekuensi pertanyaan ini ke total tag
            $tagFrequencies[$tag]['total_frequency'] += $question['frequency'];

            // Tambahkan 1 ke penghitungan pertanyaan unik
            $tagFrequencies[$tag]['question_count']++;
        }

        // Urutkan berdasarkan total frekuensi (dari tertinggi ke terendah)
        uasort($tagFrequencies, function ($a, $b) {
            return $b['total_frequency'] - $a['total_frequency'];
        });

        // Terapkan batasan jumlah data untuk tag
        $tagFrequencies = array_slice($tagFrequencies, 0, $limit, true);

        // Filter pertanyaan berdasarkan tag yang dipilih setelah pembatasan
        $selectedTags = array_keys($tagFrequencies);
        $filteredQuestions = array_filter($frequentQuestions, function ($question) use ($selectedTags) {
            return in_array($question['tag'], $selectedTags);
        });

        // Ambil semua tag unik untuk dropdown filter (disimpan untuk kompatibilitas)
        $allQuestions = $model->findAll();
        $uniqueTags = array_unique(array_column($allQuestions, 'tag'));

        // Perbarui data dengan pertanyaan yang difilter dan frekuensi tag
        $data['frequentQuestions'] = array_values($filteredQuestions); // Reset indeks array
        $data['tagFrequencies'] = $tagFrequencies;
        $data['uniqueTags'] = $uniqueTags;
    }
}
