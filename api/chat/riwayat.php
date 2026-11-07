<?php
require_once __DIR__ . '/../config.php';

// Ambil riwayat chat milik USER (tanpa login), dipanggil berkala (polling)
// oleh app. GET /chat/riwayat.php?no_hp=...

$noHp = trim($_GET['no_hp'] ?? '');
if ($noHp === '') {
    jsonResponse(false, null, 'Parameter no_hp wajib diisi', 400);
}

$data = bacaChat($noHp);
jsonResponse(true, $data ?? ['no_hp' => $noHp, 'nama' => '', 'pesan' => []], 'Riwayat chat');
