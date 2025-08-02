<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UnansweredQuestionModel;
use App\Models\UserModel;

class AdminPanel extends Controller
{
    protected $unansweredQuestionModel;
    protected $userModel;

    public function __construct()
    {
        // Inisialisasi model
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Ambil data untuk dashboard admin
        $data = [
            'title' => 'Admin Panel Chatbot',
            'pendingQuestions' => $this->unansweredQuestionModel->getPendingQuestions(),
            'answeredQuestions' => $this->unansweredQuestionModel->getAnsweredQuestions()
        ];

        return view('', $data);
    }

    public function pendingQuestions()
    {
        $data = [
            'title' => 'Pertanyaan Belum Dijawab',
            'questions' => $this->unansweredQuestionModel->getPendingQuestions()
        ];

        return view('adminpanel/pending_questions', $data);
    }

    public function answeredQuestions()
    {
        $data = [
            'title' => 'Pertanyaan Sudah Dijawab',
            'questions' => $this->unansweredQuestionModel->getAnsweredQuestions()
        ];

        return view('adminpanel/answered_questions', $data);
    }

    public function answerQuestion($id = null)
    {
        if ($id === null) {
            return redirect()->to('/adminpanel/pending-questions')->with('error', 'ID Pertanyaan tidak valid');
        }

        $question = $this->unansweredQuestionModel->find($id);

        if (!$question) {
            return redirect()->to('/adminpanel/pending-questions')->with('error', 'Pertanyaan tidak ditemukan');
        }

        $data = [
            'title' => 'Jawab Pertanyaan',
            'question' => $question
        ];

        return view('adminpanel/answer_form', $data);
    }

    public function saveAnswer()
    {
        $id = $this->request->getPost('id');
        $answer = $this->request->getPost('answer');
        $tag = $this->request->getPost('tag');

        if (!$id || !$answer) {
            return redirect()->back()->with('error', 'ID atau jawaban tidak boleh kosong');
        }

        // Ambil user_id dari session
        $user_id = session()->get('user_id');

        // Cek apakah user_id ada di session
        if (!$user_id) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
        }

        // Mengambil username berdasarkan user_id dengan query builder
        $user = $this->userModel->where('id', $user_id)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
        }

        // Ambil username dari data yang ditemukan, jika tidak ada gunakan 'admin' sebagai fallback
        $username = $user['username'] ?? 'admin';

        // Update jawaban di database
        $this->unansweredQuestionModel->update($id, [
            'answer' => $answer,
            'tag' => $tag,
            'status' => 'answered',
            'answered_by' => $username, // Menggunakan nama pengguna (username) di sini
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Jika ingin otomatis menambahkan ke dataset CSV
        $this->updateChatbotDataset($id);

        return redirect()->to('/dashboard')->with('success', 'Jawaban berhasil disimpan');
    }


    public function editAnswer($id = null)
    {
        if ($id === null) {
            return redirect()->to('/admin/answered_questions')->with('error', 'ID Pertanyaan tidak valid');
        }

        $question = $this->unansweredQuestionModel->find($id);

        if (!$question) {
            return redirect()->to('/admin/answered-questions')->with('error', 'Pertanyaan tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Jawaban',
            'question' => $question
        ];

        return view('admin/edit_answer_form', $data);
    }

    public function updateAnswer()
    {
        $id = $this->request->getPost('id');
        $answer = $this->request->getPost('answer');
        $tag = $this->request->getPost('tag');

        if (!$id || !$answer) {
            return redirect()->back()->with('error', 'ID atau jawaban tidak boleh kosong');
        }

        // Update jawaban di database
        $this->unansweredQuestionModel->update($id, [
            'answer' => $answer,
            'tag' => $tag,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Update dataset CSV jika perlu
        $this->updateChatbotDataset($id);

        return redirect()->to('/admin/answered-questions')->with('success', 'Jawaban berhasil diperbarui');
    }

    public function deleteQuestion($id = null)
    {
        if ($id === null) {
            return redirect()->back()->with('error', 'ID Pertanyaan tidak valid');
        }

        $this->unansweredQuestionModel->delete($id);

        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus');
    }

    // Fungsi untuk menambahkan jawaban ke dataset CSV
    private function updateChatbotDataset($questionId)
    {
        $question = $this->unansweredQuestionModel->find($questionId);

        if (!$question || $question['status'] !== 'answered') {
            return false;
        }

        $csvPath = FCPATH . 'dataset/chatbot_dataset.csv';

        if (!file_exists($csvPath)) {
            // Buat file jika belum ada
            $header = ["question", "answer", "tag"];
            $fp = fopen($csvPath, 'w');
            fputcsv($fp, $header);
            fclose($fp);
        }

        // Tambahkan data baru
        $fp = fopen($csvPath, 'a');
        fputcsv($fp, [
            $question['question'],
            $question['answer'],
            $question['tag'] ?? 'general'
        ]);
        fclose($fp);

        return true;
    }

    public function showFrequentQuestions()
    {
        // Memuat model
        $model = new UnansweredQuestionModel();

        // Mendapatkan data pertanyaan yang sering ditanyakan
        $data['frequent_questions'] = $model->getFrequentQuestions();

        // Menampilkan data ke view
        return view('frequent_questions_view', $data);
    }




    public function showAnswered()
    {
        // Membuat instance model
        $model = new UnansweredQuestionModel();

        // Mengambil data dengan status "answered"
        $data['questions'] = $model->where('status', 'answered')->findAll();

        // Menampilkan data ke view
        return view('dashboard/admin/pengaturan_asisten_virtual', $data);
    }

    public function save()
    {
        // Validasi input
        if (!$this->validate($this->unansweredQuestionModel->validationRules)) {
            return redirect()->to(base_url('questions/save'))->withInput()->with('validation', $this->validator);
        }
        $user_id = session()->get('user_id');

        // Cek apakah user_id ada di session
        if (!$user_id) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
        }

        // Mengambil username berdasarkan user_id dengan query builder
        $user = $this->userModel->where('id', $user_id)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
        }
        $username = $user['username'] ?? 'admin';
        //   
        // Data untuk disimpan
        $data = [
            'question' => $this->request->getVar('question'),
            'answer' => $this->request->getVar('answer'),
            'tag' => $this->request->getVar('tag'),
            'status' => $this->request->getVar('status') ?? 'pending',
            'answered_by' => $username,
        ];

        // Simpan ke database
        if ($this->unansweredQuestionModel->insert($data)) {
            session()->setFlashdata('message', 'Pertanyaan berhasil ditambahkan.');
            return redirect()->to(base_url('/pengaturan_asisten_virtual'));
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan pertanyaan.');
            return redirect()->to(base_url('/pengaturan_asisten_virtual'))->withInput();
        }
    }

    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->to(base_url('questions/edit/'));
        }

        $question = $this->unansweredQuestionModel->find($id);

        if (!$question) {
            session()->setFlashdata('error', 'Pertanyaan tidak ditemukan.');
            return redirect()->to(base_url('/pengaturan_asisten_virtual'));
        }

        $data = [
            'title' => 'Edit Pertanyaan',
            'validation' => \Config\Services::validation(),
            'question' => $question
        ];

        return view('/pengaturan_asisten_virtual', $data);
    }

    public function update($id)
    {
        $question = $this->unansweredQuestionModel->find($id);
        $user_id = session()->get('user_id');

        // Cek apakah user_id ada di session
        if (!$user_id) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
        }

        // Mengambil username berdasarkan user_id dengan query builder
        $user = $this->userModel->where('id', $user_id)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
        }
        $username = $user['username'] ?? 'admin';

        if (!$question) {
            // Jika data tidak ditemukan
            session()->setFlashdata('error', 'Pertanyaan tidak ditemukan.');
            return redirect()->to(base_url('/pengaturan_asisten_virtual'));
        }

        // Data untuk diupdate
        $data = [
            'question' => $this->request->getVar('question'),
            'answer'   => $this->request->getVar('answer'),
            'status'   => $this->request->getVar('status'),
            'answered_by' => $username,
            'tag' => $this->request->getVar('tag'),

        ];

        // Melakukan update dengan ID
        if ($this->unansweredQuestionModel->update($id, $data)) {
            session()->setFlashdata('message', 'Pertanyaan berhasil diperbarui.');
            return redirect()->to(base_url('/pengaturan_asisten_virtual'));
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui pertanyaan.');
            return redirect()->to(base_url('/pengaturan_asisten_virtual' . $id))->withInput();
        }
    }

  public function delete($id)
{
    log_message('debug', 'Memanggil method delete() dengan ID: ' . $id);

    $question = $this->unansweredQuestionModel->find($id);

    if (!$question) {
        session()->setFlashdata('error', 'Pertanyaan tidak ditemukan.');
        return redirect()->to('/pengaturan_asisten_virtual');
    }

    if ($this->unansweredQuestionModel->delete($id)) {
        session()->setFlashdata('message', 'Pertanyaan berhasil dihapus.');
    } else {
        session()->setFlashdata('error', 'Gagal menghapus pertanyaan.');
    }

    return redirect()->to('/pengaturan_asisten_virtual');
}
}
