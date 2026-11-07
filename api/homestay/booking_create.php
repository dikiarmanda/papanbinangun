<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method tidak diizinkan', 405);
}

$input = getJsonInput();

$homestay_id = isset($input['homestay_id']) ? (int)$input['homestay_id'] : 0;
$checkin     = $input['checkin'] ?? null;   // format: YYYY-MM-DD
$checkout    = $input['checkout'] ?? null;  // format: YYYY-MM-DD
$jumlah_tamu = isset($input['jumlah_tamu']) ? (int)$input['jumlah_tamu'] : 1;
$catatan     = $input['catatan'] ?? null;
$metode_pembayaran = trim($input['metode_pembayaran'] ?? 'Tunai (Cash)');

// ----- Data tamu (WAJIB diisi dari form Flutter, tanpa perlu login) -----
$nama_tamu = trim($input['nama_tamu'] ?? '');
$whatsapp  = trim($input['whatsapp'] ?? '');
$alamat    = trim($input['alamat'] ?? '');

if (!$homestay_id) {
    jsonResponse(false, null, 'homestay_id wajib diisi', 400);
}
if ($nama_tamu === '' || $whatsapp === '' || $alamat === '') {
    jsonResponse(false, null, 'Nama, nomor WhatsApp, dan alamat wajib diisi', 400);
}
if (!$checkin || !$checkout) {
    jsonResponse(false, null, 'Tanggal check-in dan check-out wajib diisi', 400);
}

$d1 = DateTime::createFromFormat('Y-m-d', $checkin);
$d2 = DateTime::createFromFormat('Y-m-d', $checkout);
if (!$d1 || !$d2) {
    jsonResponse(false, null, 'Format tanggal tidak valid, gunakan YYYY-MM-DD', 400);
}
$malam = (int)$d2->diff($d1)->days;
if ($malam < 1) {
    jsonResponse(false, null, 'Tanggal check-out harus setelah check-in', 400);
}
if ($jumlah_tamu < 1) {
    $jumlah_tamu = 1;
}

$conn = getConnection('reservasi');
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("SELECT * FROM homestays WHERE id = ? AND status = 'aktif' FOR UPDATE");
    $stmt->bind_param('i', $homestay_id);
    $stmt->execute();
    $homestay = $stmt->get_result()->fetch_assoc();
    if (!$homestay) {
        throw new Exception('Homestay tidak ditemukan atau tidak tersedia');
    }

    if ($jumlah_tamu > (int)$homestay['kapasitas']) {
        throw new Exception('Jumlah tamu melebihi kapasitas homestay (maks ' . $homestay['kapasitas'] . ' orang)');
    }

    $harga_per_malam = (float)$homestay['harga'];
    $total = $harga_per_malam * $malam;
    $kode_booking = generateKodeBooking();

    // Tabel `bookings` tidak punya kolom `alamat`/`metode_pembayaran`
    // sendiri (tidak ingin mengubah struktur database), jadi alamat &
    // metode pembayaran dititipkan di kolom `catatan` yang sudah ada,
    // dengan format baku supaya mudah dibaca admin maupun diparsing
    // kembali oleh booking_track.php & parseBookingCatatanLengkap().
    $catatanGabungan = 'Alamat: ' . $alamat
        . "\nCatatan: " . ($catatan !== null && trim($catatan) !== '' ? trim($catatan) : '')
        . "\nPembayaran: " . $metode_pembayaran;

    $ins = $conn->prepare('INSERT INTO bookings
        (kode_booking, homestay_id, nama_tamu, whatsapp, checkin, checkout, jumlah_tamu, malam, harga_per_malam, total, catatan, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,"pending")');
    $ins->bind_param(
        'sissssiidds',
        $kode_booking, $homestay_id, $nama_tamu, $whatsapp,
        $checkin, $checkout, $jumlah_tamu, $malam, $harga_per_malam, $total, $catatanGabungan
    );
    $ins->execute();

    $conn->commit();

    jsonResponse(true, [
        'id'              => $ins->insert_id,
        'kode_booking'    => $kode_booking,
        'malam'           => $malam,
        'harga_per_malam' => $harga_per_malam,
        'total'           => $total,
        'status'          => 'pending',
    ], 'Booking berhasil dibuat, menunggu konfirmasi admin');

} catch (Exception $e) {
    $conn->rollback();
    jsonResponse(false, null, $e->getMessage(), 400);
}
