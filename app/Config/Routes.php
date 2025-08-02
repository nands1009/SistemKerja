<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/chatbot', 'Chatbot::index');  // Menampilkan form chatbot
//$routes->post('/chatbot/chat', 'Chatbot::chat'); 
//$routes->post('chat', 'ChatbotController::chat');
$routes->get('/chatbot', 'Chatbot::index');
$routes->post('/chatbot/getResponse', 'Chatbot::getResponse');
$routes->get('/chatbot/evaluateModel', 'Chatbot::evaluateModel');
  // Update jawaban admin
$routes->get('adminpanel/pending_questions', 'AdminPanel::pendingQuestions');
$routes->get('adminpanel/answeredquestions', 'AdminPanel::answeredQuestions');
$routes->get('adminpanel/answerquestion/(:num)', 'AdminPanel::answerQuestion/$1');
$routes->post('adminpanel/saveanswer', 'AdminPanel::saveAnswer');
$routes->get('adminpanel/editanswer/(:num)', 'AdminPanel::editAnswer/$1');
$routes->post('adminpanel/updateanswer', 'AdminPanel::updateAnswer');
$routes->get('adminpanel/deletequestion/(:num)', 'AdminPanel::deleteQuestion/$1');
$routes->get('chatbot-analytics', 'Chatbot::showTagChart');
$routes->get('chatbot/frequent-questions', 'Chatbot::showFrequentQuestions');
$routes->post('move-answered-question', 'Chatbot::moveAnsweredQuestionToAnotherTable');
$routes->get('chatbot/frequent-questions/(:segment)', 'Chatbot::showFrequentQuestions/$1');
$routes->get('questions/frequent', 'FrequentQuestionsController::index');
$routes->get('/pengaturan_asisten_virtual', 'AdminPanel::showAnswered');
$routes->post('questions/save', 'AdminPanel::save');
$routes->get('questions/edit/', 'AdminPanel::edit');
$routes->post('questions/update/(:num)', 'AdminPanel::update/$1');
$routes->post('questions/delete/(:num)', 'AdminPanel::delete/$1');
  // Update jawaban admin
$routes->get('/', 'Auth::login');
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
});
$routes->get('/login', 'Auth::login');
$routes->post('/auth/doLogin', 'Auth::doLogin');
$routes->get('/register', 'Auth::register');
$routes->post('/auth/doRegister', 'Auth::doRegister');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/logout', 'Auth::logout');

$routes->get('/pegawai', 'Pegawai::index'); // Menampilkan data pegawai
$routes->get('/pegawai/add', 'Pegawai::add'); // Menampilkan form tambah pegawai
$routes->post('/pegawai/save', 'Pegawai::save'); // Menyimpan data pegawai baru
$routes->get('/pegawai/edit/(:num)', 'Pegawai::edit/$1'); // Menampilkan form edit pegawai
$routes->post('/pegawai/update/(:num)', 'Pegawai::update/$1'); // Menyimpan perubahan data pegawai
$routes->get('/pegawai/delete/(:num)', 'Pegawai::delete/$1'); // Menghapus data pegawai
$routes->get('pegawai/approve/(:num)', 'Pegawai::approve/$1');

$routes->get('/waktu_penilaian', 'WaktuPenilaian::index'); // Halaman pengaturan waktu penilaian
$routes->get('/waktu_penilaian/add', 'WaktuPenilaian::add'); // Halaman tambah waktu penilaian
$routes->post('/waktu_penilaian/save', 'WaktuPenilaian::save'); // Menyimpan waktu penilaian baru
$routes->get('/waktu_penilaian/edit/(:num)', 'WaktuPenilaian::edit/$1'); // Menampilkan form edit
$routes->post('/waktu_penilaian/update/(:num)', 'WaktuPenilaian::update/$1'); // Menyimpan perubahan waktu penilaian
$routes->get('/waktu_penilaian/delete/(:num)', 'WaktuPenilaian::delete/$1'); // Menghapus waktu penilaian

//pegawai

//$routes->get('/dashboard/nama', 'DashboardPegawai::nama');



$routes->get('/laporan_kerja', 'LaporanKerja::create'); // Halaman untuk membuat laporan
$routes->post('/laporan_kerja/store', 'LaporanKerja::store'); // Menyimpan laporan
$routes->get('/laporan_kerja/edit/(:num)', 'LaporanKerja::edit/$1');
//$routes->post('/laporan_kerja/edit/(:segment)', 'LaporanKerja::edit/$1'); // Perbaikan route untuk edit laporan
$routes->get('/laporan_kerja/riwayat', 'LaporanKerja::riwayat');
$routes->get('/laporan_kerja/details/(:num)', 'LaporanKerja::details/$1'); // Menampilkan riwayat laporan
$routes->post('/laporan_kerja/update/(:num)', 'LaporanKerja::update/$1'); // Mengupdate rencana kerja berdasarkan ID
$routes->get('/laporan_kerja/feedback/(:num)', 'LaporanKerja::feedback/$1'); // Menangani feedback laporan



$routes->get('rencana_kerja', 'RencanaKerja::index'); // Halaman utama rencana kerja
$routes->get('rencana_kerja/add', 'RencanaKerja::add'); // Menambahkan rencana kerja baru
$routes->post('rencana_kerja/save', 'RencanaKerja::save'); // Menyimpan data rencana kerja
$routes->get('rencana_kerja/edit/(:num)', 'RencanaKerja::edit/$1'); // Halaman edit berdasarkan ID
$routes->post('rencana_kerja/update/(:num)', 'RencanaKerja::update/$1'); // Mengupdate rencana kerja berdasarkan ID
$routes->get('rencana_kerja/view/(:num)', 'RencanaKerja::view/$1');
$routes->get('rencana_kerja/detail/(:num)', 'RencanaKerja::detailpegawai/$1'); // Melihat rencana kerja berdasarkan ID


$routes->get('evaluasi_kinerja', 'EvaluasiKinerja::index');
$routes->get('evaluasi_kinerja/add/(:num)', 'EvaluasiKinerja::add/$1');
$routes->post('evaluasi_kinerja/save', 'EvaluasiKinerja::save');



$routes->get('/riwayat_evaluasi/penilaian', 'RiwayatEvaluasi::penilaian');
$routes->get('/riwayat_evaluasi/sp', 'RiwayatEvaluasi::riwayatsp');
$routes->get('/riwayat_evaluasi/penghargaan', 'RiwayatEvaluasi::riwayatpenghargaan');
$routes->get('sp/download_pdf/(:segment)', 'RiwayatEvaluasi::download_pdf/$1');
$routes->get('penghargaan/download_pdf/(:segment)', 'RiwayatEvaluasi::download_pdfpenghargaan/$1');

// Manager

// rencana kerja manager
$routes->get('/rencana-kerja/input', 'RencanaKerjam::input');
$routes->post('/rencana-kerja/save', 'RencanaKerjam::saveRencanaKerja');
$routes->get('/rencana-kerja/riwayat', 'RencanaKerjam::riwayat');
$routes->get('/rencana-kerja/edit/(:num)', 'RencanaKerjam::edit/$1');
$routes->post('/rencana-kerja/update/(:num)', 'RencanaKerjam::update/$1');

$routes->get('approval', 'Approval::index');
$routes->get('approval/approvemanager/(:num)', 'Approval::approve/$1');
$routes->post('approval/reject/(:num)', 'Approval::reject/$1');

$routes->group('laporan_kerja_manager', ['filter' => 'auth'], function ($routes) {
    // Menampilkan form input laporan kerja
    $routes->get('create', 'LaporanKerjaManager::create');

    // Menyimpan laporan kerja
    $routes->post('store', 'LaporanKerjaManager::store');

    // Menampilkan riwayat laporan kerja
    $routes->get('riwayat', 'LaporanKerjaManager::riwayat');
    $routes->get('details/(:num)', 'LaporanKerjaManager::detailManager/$1');
    $routes->get('edit/(:num)', 'LaporanKerjaManager::edit/$1');
    $routes->post('update/(:num)', 'LaporanKerjaManager::update/$1');
});

//$routes->get('laporan_kerja_manager/details/(:num)', 'LaporanKerjaManager::detailManager/$1');
//$routes->get('/laporan_kerja_hrd', 'LaporanKerjaHRD::create');


$routes->get('/penilaian', 'Penilaian::index');
$routes->post('/penilaian/savePenilaian', 'Penilaian::savePenilaian');

//HRD
$routes->group('laporan_kerja_hrd', ['filter' => 'auth'], function ($routes) {
    // Menampilkan form input laporan kerja
    $routes->get('create', 'LaporanKerjaHRD::create');

    // Menyimpan laporan kerja
    $routes->post('store', 'LaporanKerjaHRD::store');

    // Menampilkan riwayat laporan kerja
    $routes->get('riwayat', 'LaporanKerjaHRD::riwayat');
});
$routes->get('/rencana-kerja-hrd/input', 'RencanaKerjaHRD::inputhrd');
$routes->post('/rencana-kerja-hrd/save', 'RencanaKerjaHRD::saveRencanaKerjahrd');
$routes->get('/rencana-kerja-hrd/riwayat', 'RencanaKerjaHRD::riwayat');
$routes->get('/rencana-kerja-hrd/edit/(:num)', 'RencanaKerjaHRD::edit/$1');
$routes->post('/rencana-kerja-hrd/update/(:num)', 'RencanaKerjaHRD::update/$1');

$routes->get('/approval-hrd', 'ApprovalHRD::index');
$routes->get('approval/approvehrd/(:num)', 'ApprovalHRD::approve/$1');
$routes->post('approval/rejecthrd/(:num)', 'ApprovalHRD::reject/$1');

$routes->group('pengajuan-hrd', function ($routes) {
    $routes->get('ajukan', 'Pengajuan::ajukanHRD');
   $routes->post('pengajuan/submitPenghargaanhrd', 'Pengajuan::submitPenghargaanHrd');
   $routes->post('pengajuan/submitSPhrd', 'Pengajuan::submitSPHrd');
    $routes->get('riwayat-pengajuan', 'Pengajuan::riwayatPengajuan');
    $routes->get('riwayat-pengajuan-hrd', 'Pengajuan::riwayatPengajuanHRD');
    $routes->get('riwayat_hrd', 'Pengajuan::riwayatHrd');
    $routes->get('riwayat_direksi', 'PengajuanHRD::riwayatDireksi');

    //$routes->get('approve_hrd/(:num)/(:any)', 'Pengajuan::approveHrd/$1/$2');
    //$routes->post('approve_hrd/(:num)/(:any)', 'PengajuanHRD::approveHrd/$1/$2');
    //$routes->get('approve_direksi/(:num)/(:any)', 'PengajuanHRD::approveHrd/$1/$2');
    //$routes->post('/pengajuan/reject/(:num)/(:any)', 'PengajuanHRD::rejectHrd/$1/$2');
   // $routes->post('approve_direksi/(:num)/(:any)', 'Pengajuan::approveDireksi/$1/$2');
    //$routes->get('riwayat_pengajuan-hrd/(:num)/(:any)', 'PengajuanHRD::approveDireksi/$1/$2');
    //$routes->get('hapus/(:num)/(:any)', 'PengajuanHRD::hapus/$1/$2');
}); 
 $routes->post('/pengajuan/submitPenghargaanhrd', 'Pengajuan::submitPenghargaanHrd');
  $routes->post('/pengajuan/submitSPHRD', 'Pengajuan::submitSPHrd');

$routes->get('/penilaian-hrd', 'PenilaianHRD::index');
$routes->post('/penilaian-hrd/savePenilaian', 'PenilaianHRD::savePenilaian');
// RIWAYAT PENGAJUAN HRD SP & PENGHARGAAN 
//$routes->get('/pengajuan', 'Pengajuan::index');
//$routes->post('/pengajuan/submitPenghargaan', 'Pengajuan::submitPenghargaan');
//$routes->post('/pengajuan/submitSP', 'Pengajuan::submitSP');
$routes->get('/riwayat-pengajuan', 'Pengajuan::riwayatPengajuan');

$routes->get('/riwayat-penilaian', 'RiwayatPenilaian::index');
$routes->get('/riwayat-penilaian-hrd', 'RiwayatPenilaian::indexHRD');

$routes->get('/approval-hrd', 'ApprovalPenghargaan::index');
$routes->get('/approval/approve/(:num)', 'ApprovalPenghargaan::approve/$1');
$routes->get('/approval/reject/(:num)', 'ApprovalPenghargaan::reject/$1');

$routes->get('/approval-direksi', 'ApprovalDireksi::index');
$routes->get('/approval-direksi/approve/(:num)', 'ApprovalDireksi::approve/$1');
$routes->get('/approval-direksi/reject/(:num)', 'ApprovalDireksi::reject/$1');

$routes->get('manager-evaluation', 'ManagerEvaluation::index');
$routes->post('manager-evaluation/submitEvaluation', 'ManagerEvaluation::submitEvaluation');

//coba



$routes->group('pengajuan', function ($routes) {
    $routes->get('ajukan', 'Pengajuan::ajukan');
    $routes->post('submitPenghargaan', 'Pengajuan::submitPenghargaan');
    $routes->post('submitSP', 'Pengajuan::submitSP');
    $routes->get('riwayat-pengajuan', 'Pengajuan::riwayatPengajuan');
    $routes->get('riwayat_hrd', 'Pengajuan::riwayatHrd');
    $routes->get('riwayat_direksi', 'Pengajuan::riwayatDireksi');

    $routes->get('approve_hrd/(:num)/(:any)', 'Pengajuan::approveHrd/$1/$2');
    $routes->post('approve_hrd/(:num)/(:any)', 'Pengajuan::approveHrd/$1/$2');
    $routes->get('approve_direksi/(:num)/(:any)', 'Pengajuan::approveHrd/$1/$2');
    $routes->post('/pengajuan/reject-hrd/(:num)/(:any)', 'Pengajuan::rejectHrd/$1/$2');
   // $routes->post('approve_direksi/(:num)/(:any)', 'Pengajuan::approveDireksi/$1/$2');
    $routes->get('riwayat_pengajuan/(:num)/(:any)', 'Pengajuan::approveDireksi/$1/$2');
    $routes->get('hapus/(:num)/(:any)', 'Pengajuan::hapus/$1/$2');
});


//$routes->post('/pengajuan/ajukan', 'Pengajuan::ajukan');

// routes reject HRD & DIREKSI ( sp & penghargaan )
$routes->post('/pengajuan/reject-hrd/(:num)/(:any)', 'Pengajuan::rejectHrd/$1/$2');
$routes->post('/pengajuan/reject_direksi/(:num)/(:any)', 'Pengajuan::rejectDireksi/$1/$2');


$routes->post('/pdf/upload/penghargaan/(:num)', 'Pengajuan::uploadPenghargaan/$1');

$routes->post('/pdf/upload/(:num)', 'Pengajuan::uploadsp/$1');
$routes->post('/pengajuan/reject/(:num)/(:any)', 'Pengajuan::rejectHrd/$1/$2');

$routes->get('/pengajuan/riwayat_pengajuan', 'Pengajuan::index');

// admin
$routes->get('/admin/rekap_laporan', 'Admin::index');
$routes->get('/admin/delete/(:num)', 'Admin::delete/$1');
$routes->post('admin/rekap_laporan','Admin::Delete');
$routes->get('admin/rekap_rencana_kerja', 'AdminRencanaKerja::index');
$routes->get('/admin/rekap_penilaian', 'AdminPenilaian::index');
$routes->get('admin/deleteAllData/(:num)', 'Admin::deleteAllData/$1');

// direksi

$routes->get('/direksi/penilaian_manager', 'PenilaianManagerController::index');
$routes->post('/direksi/savePenilaian', 'PenilaianManagerController::savePenilaian');
$routes->get('/direksi/rekap_laporan', 'Admin::indexDIREKSI');
$routes->get('/direksi/rekap_penilaian', 'AdminPenilaian::indexDIREKSI');
