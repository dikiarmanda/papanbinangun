<?php
require_once __DIR__ . '/../config.php';

// Upload bukti pembayaran (screenshot transfer/QRIS) dari app untuk
// booking homestay. Tabel bookings tidak punya status pembayaran
// terpisah (cuma status umum: pending/dikonfirmasi/selesai/dibatalkan),
// jadi begitu bukti bayar diterima & status masih "pending", otomatis
// diubah jadi "dikonfirmasi". Tidak ada kolom/tabel baru - file foto
// disimpan di uploads/bukti_bayar/booking/, namanya = kode_booking.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method tidak diizinkan', 405);
}

$input = getJsonInput();
$kode_booking = trim($input['kode_booking'] ?? '');
$whatsapp = trim($input['whatsapp'] ?? '');
$foto = $input['foto_base64'] ?? '';

if ($kode_booking === '' || $whatsapp === '' || $foto === '') {
    jsonResponse(false, null, 'Kode booking, whatsapp, dan foto wajib diisi', 400);
}

$conn = getConnection('reservasi');
$stmt = $conn->prepare('SELECT id, status FROM bookings WHERE kode_booking = ? AND whatsapp = ?');
$stmt->bind_param('ss', $kode_booking, $whatsapp);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    jsonResponse(false, null, 'Booking tidak ditemukan. Periksa kembali kode booking dan nomor WhatsApp.', 404);
}

[$ok, $result] = simpanBuktiBayar('booking', $kode_booking, $foto);
if (!$ok) {
    jsonResponse(false, null, $result, 400);
}

if ($row['status'] === 'pending') {
    $upd = $conn->prepare("UPDATE bookings SET status = 'dikonfirmasi' WHERE id = ?");
    $upd->bind_param('i', $row['id']);
    $upd->execute();
}

jsonResponse(true, ['url' => $result], 'Bukti pembayaran diterima, booking otomatis dikonfirmasi');
