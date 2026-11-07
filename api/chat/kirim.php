<?php
require_once __DIR__ . '/../config.php';

// Kirim pesan chat dari USER (tanpa login) ke admin.
// Body JSON: { no_hp, nama, pesan }

$input = getJsonInput();
$noHp = trim($input['no_hp'] ?? '');
$nama = trim($input['nama'] ?? '');
$pesan = trim($input['pesan'] ?? '');

if ($noHp === '' || $nama === '' || $pesan === '') {
    jsonResponse(false, null, 'Nama, no HP, dan pesan wajib diisi', 400);
}

simpanPesanChat($noHp, $nama, 'user', $pesan);
jsonResponse(true, null, 'Pesan terkirim');
