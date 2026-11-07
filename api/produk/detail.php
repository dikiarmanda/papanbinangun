<?php
require_once __DIR__ . '/../config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    jsonResponse(false, null, 'Parameter id wajib diisi', 400);
}

$conn = getConnection();
$stmt = $conn->prepare("SELECT p.*, k.nama AS kategori_nama FROM produk p
    LEFT JOIN kategori_produk k ON k.id = p.kategori_id
    WHERE p.id = ? AND p.status = 'publish'");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, null, 'Produk tidak ditemukan', 404);
}
jsonResponse(true, $result->fetch_assoc(), 'Detail produk');
