<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DivisiModel;
use App\Models\ManagerModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        return view('auth/register');
    }

    public function doRegister()
    {
        helper(['form', 'url']);

        $validation = \Config\Services::validation();
        if (!$this->validate([
            'username' => 'required|min_length[3]|max_length[20]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required',
            'divisi' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        $DivisiModel = new DivisiModel();
        $divisi = $DivisiModel->getDivisiByName(strtolower($this->request->getVar('divisi')));
        $ManagerModel = new ManagerModel();
        $role = $ManagerModel->getDivisiByNameManager(strtolower($this->request->getVar('role')));
        $userModel = new UserModel();
        $data = [
            'username' => $this->request->getVar('username'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getVar('role'),
            'divisi' => $this->request->getVar('divisi'),
            'divisi_id' => $divisi['id'],
            'role_id' => $role['id']
        ];
        //var_dump($data);

        $userModel->save($data);

        return redirect()->to('/login');
    }

    public function login()
    {
        return view('auth/login');
    }

    public function doLogin()
    {
        helper(['form', 'url']);

        $validation = \Config\Services::validation();
        if (!$this->validate([
            'email' => 'required|valid_email|min_length[3]',
            'password' => 'required|min_length[1]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($this->request->getVar('email'));

        if (!$user || !password_verify($this->request->getVar('password'), $user['password'])) {
            return redirect()->to('/login')->with('error', 'Email atau Password salah!');
        }
        if ($user['approved'] != 'Approved') {
    return redirect()->to('/login')->with('error', 'Akun Anda belum disetujui!');
}

        // Set session
        session()->set('user_id', $user['id']);
        session()->set('role', $user['role']);
        session()->set('role_id', $user['role_id']);
        session()->set('divisi_id', $user['divisi_id']);
        return redirect()->to('/dashboard');
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
