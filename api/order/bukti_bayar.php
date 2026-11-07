<?php
require_once __DIR__ . '/../config.php';

// Upload screenshot bukti transfer/QRIS untuk order belanja (tanpa login,
// cukup kode_order + no HP seperti saat tracking). Status pembayaran
// otomatis diperbarui ke 'paid' (nilai ENUM asli kolom status_pembayaran
// di tabel `order`: pending/paid/failed/expired) kalau sebelumnya masih
// 'pending'.

$input = getJsonInput();
$kode = trim($input['kode'] ?? '');
$noHp = trim($input['no_hp'] ?? '');
$fotoBase64 = $input['foto_base64'] ?? '';

if ($kode === '' || $noHp === '' || $fotoBase64 === '') {
    jsonResponse(false, null, 'Parameter kode, no_hp, dan foto_base64 wajib diisi', 400);
}

$conn = getConnection('layanan');
$stmt = $conn->prepare('SELECT o.id, o.status_pembayaran
    FROM `order` o
    JOIN pelanggan p ON p.id = o.pelanggan_id
    WHERE o.kode_order = ? AND p.no_hp = ?');
$stmt->bind_param('ss', $kode, $noHp);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    jsonResponse(false, null, 'Order tidak ditemukan. Periksa kembali kode dan nomor HP.', 404);
}

[$ok, $result] = simpanBuktiBayar('order', $kode, $fotoBase64);
if (!$ok) {
    jsonResponse(false, null, $result, 400);
}

if ($row['status_pembayaran'] === 'pending') {
    $upd = $conn->prepare("UPDATE `order` SET status_pembayaran = 'paid', paid_at = NOW() WHERE id = ?");
    $upd->bind_param('i', $row['id']);
    $upd->execute();
}

jsonResponse(true, ['url' => $result], 'Bukti bayar berhasil diupload');
