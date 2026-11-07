<?php
require_once __DIR__ . '/../config.php';

$conn = getConnection();
$result = $conn->query("SELECT * FROM zona_antar_lokal WHERE status = 'aktif' ORDER BY nama");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
jsonResponse(true, $data, 'Daftar zona antar lokal');
