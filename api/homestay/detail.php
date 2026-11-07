<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method tidak diizinkan', 405);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    jsonResponse(false, null, 'id wajib diisi', 400);
}

$conn = getConnection('reservasi');

$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, harga, kapasitas, fasilitas, gambar, status
                         FROM homestays WHERE id = ? AND status = 'aktif' LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    jsonResponse(false, null, 'Homestay tidak ditemukan', 404);
}

$row['id'] = (int)$row['id'];
$row['harga'] = (float)$row['harga'];
$row['kapasitas'] = (int)$row['kapasitas'];

jsonResponse(true, $row, 'Detail homestay');
