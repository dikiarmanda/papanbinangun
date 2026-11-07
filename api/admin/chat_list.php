<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../admin/config_admin.php';

// Daftar semua percakapan untuk admin (mode dalam-aplikasi, tanpa session,
// password diverifikasi ulang tiap request). Body JSON: { password }

$input = getJsonInput();
$password = trim($input['password'] ?? '');

if (!hash_equals((string)ADMIN_PASSWORD, $password)) {
    jsonResponse(false, null, 'Password admin salah', 401);
}

jsonResponse(true, daftarPercakapanChat(), 'Daftar percakapan');
