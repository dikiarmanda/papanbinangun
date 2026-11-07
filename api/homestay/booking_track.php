<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method tidak diizinkan', 405);
}

$kode_booking = trim($_GET['kode_booking'] ?? '');
$whatsapp     = trim($_GET['whatsapp'] ?? '');

if ($kode_booking === '' || $whatsapp === '') {
    jsonResponse(false, null, 'kode_booking dan whatsapp wajib diisi', 400);
}

$conn = getConnection('reservasi');

// Catatan: tabel `bookings` tidak punya kolom `alamat` sendiri, jadi alamat
// sebelumnya dititipkan di awal kolom `catatan` oleh booking_create.php
// (format "Alamat: ...\nCatatan: ..."). Di sini dipecah kembali supaya
// response API tetap punya field `alamat` & `catatan` terpisah seperti biasa.
$stmt = $conn->prepare("SELECT b.id, b.kode_booking, b.homestay_id, h.nama AS nama_homestay, h.lokasi,
                                b.nama_tamu, b.whatsapp, b.checkin, b.checkout, b.jumlah_tamu,
                                b.malam, b.harga_per_malam, b.total, b.catatan, b.status, b.created_at
                         FROM bookings b
                         JOIN homestays h ON h.id = b.homestay_id
                         WHERE b.kode_booking = ? AND b.whatsapp = ?
                         LIMIT 1");
$stmt->bind_param('ss', $kode_booking, $whatsapp);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    jsonResponse(false, null, 'Booking tidak ditemukan. Periksa kembali kode booking dan nomor WhatsApp.', 404);
}

[$alamat, $catatanOnly, $metodePembayaran] = parseBookingCatatanLengkap($row['catatan']);
$row['alamat'] = $alamat;
$row['catatan'] = $catatanOnly;
$row['metode_pembayaran'] = $metodePembayaran;
$row['bukti_bayar_url'] = buktiBayarUrl('booking', $row['kode_booking']);

$row['id'] = (int)$row['id'];
$row['homestay_id'] = (int)$row['homestay_id'];
$row['jumlah_tamu'] = (int)$row['jumlah_tamu'];
$row['malam'] = (int)$row['malam'];
$row['harga_per_malam'] = (float)$row['harga_per_malam'];
$row['total'] = (float)$row['total'];

jsonResponse(true, $row, 'Detail booking');
