<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../admin/config_admin.php';

// Update status booking homestay / order belanja dari app Flutter (mode
// admin dalam-aplikasi). Tidak pakai session PHP (app tidak login via
// browser) — password admin diverifikasi ulang di setiap request.

$input = getJsonInput();
$password = trim($input['password'] ?? '');
$jenis = trim($input['jenis'] ?? ''); // 'homestay' | 'belanja'
$id = (int)($input['id'] ?? 0);

if (!hash_equals((string)ADMIN_PASSWORD, $password)) {
    jsonResponse(false, null, 'Password admin salah', 401);
}

if ($id <= 0) {
    jsonResponse(false, null, 'ID tidak valid', 400);
}

if ($jenis === 'homestay') {
    $status = trim($input['status'] ?? '');
    $allowed = ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'];
    if (!in_array($status, $allowed, true)) {
        jsonResponse(false, null, 'Status booking tidak valid', 400);
    }
    $conn = getConnection('reservasi');
    $stmt = $conn->prepare('UPDATE bookings SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    jsonResponse(true, null, 'Status booking diperbarui');
} elseif ($jenis === 'belanja') {
    $statusPembayaran = isset($input['status_pembayaran']) && $input['status_pembayaran'] !== null
        ? trim($input['status_pembayaran']) : null;
    $statusOrder = isset($input['status_order']) && $input['status_order'] !== null
        ? trim($input['status_order']) : null;

    // Nilai ENUM asli kolom `order`.status_pembayaran: pending/paid/failed/expired
    $allowedBayar = ['pending', 'paid', 'failed', 'expired'];
    // Nilai ENUM asli kolom `order`.status_order:
    $allowedOrder = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

    $conn = getConnection('layanan');

    if ($statusPembayaran !== null) {
        if (!in_array($statusPembayaran, $allowedBayar, true)) {
            jsonResponse(false, null, 'Status pembayaran tidak valid', 400);
        }
        $stmt = $conn->prepare('UPDATE `order` SET status_pembayaran = ? WHERE id = ?');
        $stmt->bind_param('si', $statusPembayaran, $id);
        $stmt->execute();
    }

    if ($statusOrder !== null) {
        if (!in_array($statusOrder, $allowedOrder, true)) {
            jsonResponse(false, null, 'Status order tidak valid', 400);
        }
        $stmt = $conn->prepare('UPDATE `order` SET status_order = ? WHERE id = ?');
        $stmt->bind_param('si', $statusOrder, $id);
        $stmt->execute();
    }

    jsonResponse(true, null, 'Status order diperbarui');
} else {
    jsonResponse(false, null, 'Jenis tidak dikenal', 400);
}
