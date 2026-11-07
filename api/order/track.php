<?php
require_once __DIR__ . '/../config.php';

// Cek status order tanpa login, cukup pakai kode_order + no HP
// yang dipakai saat pesan. Contoh:
// GET /order/track.php?kode=ORD-261106-ABC123&no_hp=081234567890

$kode = trim($_GET['kode'] ?? '');
$no_hp = trim($_GET['no_hp'] ?? '');

if ($kode === '' || $no_hp === '') {
    jsonResponse(false, null, 'Parameter kode dan no_hp wajib diisi', 400);
}

// Nama & no HP pemesan diambil dari tabel `pelanggan` (lihat catatan di
// order/create.php — tabel `order` tidak punya kolom nama_pemesan/no_hp_pemesan
// sendiri supaya tidak perlu mengubah struktur database).
$conn = getConnection();
$stmt = $conn->prepare('SELECT o.*, p.nama AS nama_pemesan, p.no_hp AS no_hp_pemesan
    FROM `order` o
    JOIN pelanggan p ON p.id = o.pelanggan_id
    WHERE o.kode_order = ? AND p.no_hp = ?');
$stmt->bind_param('ss', $kode, $no_hp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, null, 'Order tidak ditemukan. Periksa kembali kode dan nomor HP.', 404);
}

$order = $result->fetch_assoc();

// Lihat catatan di order/create.php & config.php — metode pembayaran
// (tunai/qris/transfer) dititipkan di akhir alamat_kirim oleh app
// Flutter, dipecah lagi di sini supaya response tetap punya field
// alamat_kirim (bersih) & metode_pembayaran terpisah.
[$order['alamat_kirim'], $order['metode_pembayaran']] = parseAlamatPembayaran($order['alamat_kirim']);
$order['bukti_bayar_url'] = buktiBayarUrl('order', $order['kode_order']);

$itemStmt = $conn->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->bind_param('i', $order['id']);
$itemStmt->execute();
$items = [];
$res = $itemStmt->get_result();
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}
$order['items'] = $items;

jsonResponse(true, $order, 'Detail order');
