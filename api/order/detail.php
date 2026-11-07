<?php
require_once __DIR__ . '/../config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    jsonResponse(false, null, 'Parameter id wajib diisi', 400);
}

$conn = getConnection();
$stmt = $conn->prepare('SELECT o.*, p.nama AS nama_pemesan, p.no_hp AS no_hp_pemesan
    FROM `order` o LEFT JOIN pelanggan p ON p.id = o.pelanggan_id
    WHERE o.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    jsonResponse(false, null, 'Order tidak ditemukan', 404);
}

// Pisahkan metode pembayaran yang dititipkan di akhir alamat_kirim
// (lihat catatan di order/create.php & config.php).
[$order['alamat_kirim'], $order['metode_pembayaran']] = parseAlamatPembayaran($order['alamat_kirim']);
$order['bukti_bayar_url'] = buktiBayarUrl('order', $order['kode_order']);

$itemStmt = $conn->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->bind_param('i', $id);
$itemStmt->execute();
$items = [];
$res = $itemStmt->get_result();
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}
$order['items'] = $items;

jsonResponse(true, $order, 'Detail order');
