<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../admin/config_admin.php';

// Endpoint khusus untuk mode admin DI DALAM app Flutter user (lewat
// drawer kiri), BEDA dari admin panel web yang pakai session PHP biasa.
// Di sini stateless: password admin dikirim ulang tiap request, cocok
// untuk app yang memang tanpa login/session. Tidak ada tabel/kolom baru
// di database untuk ini.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method tidak diizinkan', 405);
}

$input = getJsonInput();
$password = (string)($input['password'] ?? '');

if ($password === '' || !hash_equals(ADMIN_PASSWORD, $password)) {
    jsonResponse(false, null, 'Password admin salah', 401);
}

jsonResponse(true, null, 'Login admin berhasil');
