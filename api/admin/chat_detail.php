<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../admin/config_admin.php';

// Detail 1 percakapan untuk admin. Body JSON: { password, no_hp }

$input = getJsonInput();
$password = trim($input['password'] ?? '');
$noHp = trim($input['no_hp'] ?? '');

if (!hash_equals((string)ADMIN_PASSWORD, $password)) {
    jsonResponse(false, null, 'Password admin salah', 401);
}
if ($noHp === '') {
    jsonResponse(false, null, 'Parameter no_hp wajib diisi', 400);
}

$data = bacaChat($noHp);
jsonResponse(true, $data ?? ['no_hp' => $noHp, 'nama' => '', 'pesan' => []], 'Detail percakapan');
