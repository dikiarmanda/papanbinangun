<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../admin/config_admin.php';

// Admin membalas pesan user. Body JSON: { password, no_hp, pesan }

$input = getJsonInput();
$password = trim($input['password'] ?? '');
$noHp = trim($input['no_hp'] ?? '');
$pesan = trim($input['pesan'] ?? '');

if (!hash_equals((string)ADMIN_PASSWORD, $password)) {
    jsonResponse(false, null, 'Password admin salah', 401);
}
if ($noHp === '' || $pesan === '') {
    jsonResponse(false, null, 'Parameter no_hp dan pesan wajib diisi', 400);
}

simpanPesanChat($noHp, null, 'admin', $pesan);
jsonResponse(true, null, 'Balasan terkirim');
