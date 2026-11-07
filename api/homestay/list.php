<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method tidak diizinkan', 405);
}

$conn = getConnection('reservasi');

$result = $conn->query("SELECT id, nama, lokasi, deskripsi, harga, kapasitas, fasilitas, gambar, status
                         FROM homestays
                         WHERE status = 'aktif'
                         ORDER BY nama ASC");

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['id'] = (int)$row['id'];
    $row['harga'] = (float)$row['harga'];
    $row['kapasitas'] = (int)$row['kapasitas'];
    $data[] = $row;
}

jsonResponse(true, $data, 'Daftar homestay');
