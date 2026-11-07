<?php
session_start();
require_once __DIR__ . '/config_admin.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../api/config.php';
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$jenis = $_POST['jenis'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$tab = ($_POST['tab'] ?? 'homestay') === 'belanja' ? 'belanja' : 'homestay';

if (!$id) {
    header('Location: index.php?tab=' . $tab . '&gagal=' . urlencode('ID tidak valid'));
    exit;
}

try {
    if ($jenis === 'booking') {
        // Kolom `bookings`.`status` sudah ada sejak awal (dipakai
        // booking_create.php dengan nilai default "pending"), jadi update
        // di bawah ini TIDAK mengubah struktur database, cuma mengisi
        // kolom yang sudah ada dengan nilai baru.
        $status = trim($_POST['status'] ?? '');
        $allowed = ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'];
        if (!in_array($status, $allowed, true)) {
            throw new Exception('Status booking tidak valid');
        }
        $conn = getConnection('reservasi');
        $stmt = $conn->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
    } elseif ($jenis === 'order') {
        // Kolom `order`.`status_pembayaran` & `status_order` juga sudah
        // ada sejak awal (dipakai order/create.php), update di bawah ini
        // hanya mengubah isinya, bukan struktur tabel.
        $statusBayar = trim($_POST['status_pembayaran'] ?? '');
        $statusOrder = trim($_POST['status_order'] ?? '');
        $allowedBayar = ['pending', 'lunas', 'dibatalkan'];
        $allowedOrder = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
        if (!in_array($statusBayar, $allowedBayar, true) || !in_array($statusOrder, $allowedOrder, true)) {
            throw new Exception('Status order tidak valid');
        }
        $conn = getConnection('layanan');
        $stmt = $conn->prepare('UPDATE `order` SET status_pembayaran = ?, status_order = ? WHERE id = ?');
        $stmt->bind_param('ssi', $statusBayar, $statusOrder, $id);
        $stmt->execute();
    } else {
        throw new Exception('Jenis data tidak dikenal');
    }

    header('Location: index.php?tab=' . $tab . '&berhasil=1');
    exit;
} catch (Exception $e) {
    header('Location: index.php?tab=' . $tab . '&gagal=' . urlencode($e->getMessage()));
    exit;
}
