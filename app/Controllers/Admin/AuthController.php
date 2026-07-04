<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Libraries\ActivityLogService;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('admin_id')) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return view('admin/login');
    }

    public function attemptLogin()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Rate limit sederhana
        $throttleKey = 'login_attempts_' . $this->request->getIPAddress();
        $attempts    = (int) (session()->get($throttleKey) ?? 0);
        $lockedUntil = session()->get($throttleKey . '_locked');

        if ($lockedUntil && time() < $lockedUntil) {
            return redirect()->back()->with('error', 'Terlalu banyak percobaan. Coba lagi dalam beberapa menit.');
        }

        $adminModel = new AdminModel();
        $admin      = $adminModel->findByEmail($email);

        if (! $admin || ! password_verify($password, $admin['password'])) {
            $attempts++;
            session()->set($throttleKey, $attempts);
            if ($attempts >= 5) {
                session()->set($throttleKey . '_locked', time() + 300);
                session()->set($throttleKey, 0);
            }

            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        if ($admin['status'] !== 'aktif') {
            return redirect()->back()->with('error', 'Akun Anda dinonaktifkan.');
        }

        session()->remove($throttleKey);
        session()->remove($throttleKey . '_locked');

        session()->set([
            'admin_id'     => $admin['id'],
            'admin_nama'   => $admin['nama'],
            'admin_email'  => $admin['email'],
            'admin_role'   => $admin['role'],
            'admin_status' => $admin['status'],
        ]);

        $adminModel->update($admin['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        ActivityLogService::log('login ke panel admin');

        return redirect()->to(site_url('admin/dashboard'))->with('success', 'Selamat datang, ' . $admin['nama']);
    }

    public function logout()
    {
        ActivityLogService::log('logout dari panel admin');
        session()->destroy();

        return redirect()->to(site_url('admin/login'))->with('success', 'Anda telah logout.');
    }
}
