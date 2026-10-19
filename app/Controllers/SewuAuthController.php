<?php

namespace App\Controllers;

use App\Models\UsersModel;

class SewuAuthController extends BaseController
{
    public function login()
    {
        if (session()->get('sewu_logged_in')) {
            return redirect()->to(base_url('sewu-data/admin'));
        }
        return view('sewu_data/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $usersModel = new UsersModel();
        $user = $usersModel->where('username', $username)->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $session->set([
                    'sewu_id'        => $user['id'],
                    'sewu_username'  => $user['username'],
                    'sewu_nama'      => $user['nama_lengkap'],
                    'sewu_role'      => $user['role'],
                    'sewu_logged_in' => true,
                ]);

                return redirect()->to(base_url('sewu-data/admin'));
            }
        }

        $session->setFlashdata('error', 'Username atau password salah.');
        return redirect()->to(base_url('sewu-data/login'));
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('sewu-data/login'));
    }
}