<?php
require_once __DIR__ . '/../config.php';

$conn = getConnection();
$result = $conn->query('SELECT * FROM kategori_produk ORDER BY nama');
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
jsonResponse(true, $data, 'Daftar kategori');
