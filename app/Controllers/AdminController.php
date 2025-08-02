<?php

namespace App\Controllers;

use App\Models\AdminModel;

class AdminController extends BaseController
{
    // Menampilkan daftar pertanyaan yang belum dijawab
    public function index()
    {
        $model = new AdminModel();
        $data['chatbot_data'] = $model->where('answer', 'Belum dijawab')->findAll(); // Ambil data pertanyaan

        return view('admin_panel', $data);  // Menampilkan tampilan admin panel
    }

    // Menampilkan halaman edit untuk pertanyaan berdasarkan ID
    public function edit($id)
    {
        $model = new AdminModel();
        $data['chatbot'] = $model->find($id);  // Mengambil data berdasarkan ID

        return view('admin_edit', $data);  // Menampilkan halaman edit
    }

    // Memperbarui jawaban admin dan mengirimkan response JSON
   
    public function updateAnswer($questionId)
    {
        $answer = $this->request->getPost('answer'); // Mendapatkan jawaban dari admin

        // Pastikan jawaban tidak kosong
        if (empty($answer)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Jawaban tidak boleh kosong!'
            ]);
        }

        $model = new AdminModel();

        // Memperbarui jawaban dan status pertanyaan di database
        $model->update($questionId, [
            'admin_answer' => $answer, // Menyimpan jawaban admin
            'status' => 'answered'  // Ubah status menjadi "answered"
        ]);

        // Mengambil data yang sudah diperbarui
        $updatedChat = $model->find($questionId);

        // Mengirimkan jawaban yang baru diperbarui sebagai respons JSON
        return $this->response->setJSON([
            'status' => 'success',
            'answer' => $updatedChat['admin_answer'],
            'questionId' => $updatedChat['id']
        ]);
    }
}
