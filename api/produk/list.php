<?php
require_once __DIR__ . '/../config.php';

$conn = getConnection();
$kategori_id = $_GET['kategori_id'] ?? null;
$jenis = $_GET['jenis'] ?? null; // umkm / catering

$sql = "SELECT p.*, k.nama AS kategori_nama FROM produk p
        LEFT JOIN kategori_produk k ON k.id = p.kategori_id
        WHERE p.status = 'publish'";
$params = [];
$types = '';

if ($kategori_id) {
    $sql .= ' AND p.kategori_id = ?';
    $params[] = $kategori_id;
    $types .= 'i';
}
if ($jenis) {
    $sql .= ' AND p.jenis = ?';
    $params[] = $jenis;
    $types .= 's';
}
$sql .= ' ORDER BY p.created_at DESC';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
jsonResponse(true, $data, 'Daftar produk');
