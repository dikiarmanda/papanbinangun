<?php

namespace App\Controllers\Api;

use App\Libraries\TransaksiService;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseApiController
{
    public function register(): ResponseInterface
    {
        $input = $this->getJsonInput();
        $nama     = trim((string) ($input['nama'] ?? ''));
        $email    = trim((string) ($input['email'] ?? ''));
        $noHp     = trim((string) ($input['no_hp'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if ($nama === '' || $email === '' || $noHp === '' || $password === '') {
            return $this->respondJson(false, null, 'Semua field wajib diisi', 400);
        }

        if (strlen($password) < 6) {
            return $this->respondJson(false, null, 'Password minimal 6 karakter', 400);
        }

        $db = TransaksiService::getLayananDb();
        $existing = $db->table('pelanggan')->where('email', $email)->get()->getRowArray();
        if ($existing) {
            return $this->respondJson(false, null, 'Email sudah terdaftar', 409);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db->table('pelanggan')->insert([
            'nama'     => $nama,
            'email'    => $email,
            'no_hp'    => $noHp,
            'password' => $hash,
        ]);

        $id = (int) $db->insertID();
        return $this->respondJson(true, ['id' => $id, 'nama' => $nama, 'email' => $email], 'Registrasi berhasil');
    }

    public function login(): ResponseInterface
    {
        $input = $this->getJsonInput();
        $email    = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if ($email === '' || $password === '') {
            return $this->respondJson(false, null, 'Email dan password wajib diisi', 400);
        }

        $db = TransaksiService::getLayananDb();
        $user = $db->table('pelanggan')->where('email', $email)->get()->getRowArray();

        if (!$user || empty($user['password']) || !password_verify($password, $user['password'])) {
            return $this->respondJson(false, null, 'Email atau password salah', 401);
        }

        unset($user['password']);
        return $this->respondJson(true, $user, 'Login berhasil');
    }
}
