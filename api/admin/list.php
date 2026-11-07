<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../admin/config_admin.php';

// Daftar booking/order untuk mode admin di dalam app Flutter user.
// Query sama persis dengan admin/index.php (web), hanya dikemas sebagai
// JSON supaya bisa dipakai di app, tanpa mengubah struktur database.

$input = $_SERVER['REQUEST_METHOD'] === 'POST' ? getJsonInput() : $_GET;
$password = (string)($input['password'] ?? '');
$tab = ($input['tab'] ?? 'homestay') === 'belanja' ? 'belanja' : 'homestay';

if ($password === '' || !hash_equals(ADMIN_PASSWORD, $password)) {
    jsonResponse(false, null, 'Password admin salah', 401);
}

if ($tab === 'homestay') {
    $conn = getConnection('reservasi');
    $res = $conn->query('SELECT b.*, h.nama AS nama_homestay
        FROM bookings b
        LEFT JOIN homestays h ON h.id = b.homestay_id
        ORDER BY b.id DESC
        LIMIT 200');
    $out = [];
    while ($row = $res->fetch_assoc()) {
        [$alamat, $catatan, $metodeBayar] = parseBookingCatatanLengkap($row['catatan']);
        $out[] = [
            'id'                => (int)$row['id'],
            'kode_booking'      => $row['kode_booking'],
            'nama_homestay'     => $row['nama_homestay'],
            'nama_tamu'         => $row['nama_tamu'],
            'whatsapp'          => $row['whatsapp'],
            'alamat'            => $alamat,
            'catatan'           => $catatan,
            'metode_pembayaran' => $metodeBayar,
            'bukti_bayar_url'   => buktiBayarUrl('booking', $row['kode_booking']),
            'checkin'           => $row['checkin'],
            'checkout'          => $row['checkout'],
            'jumlah_tamu'       => (int)$row['jumlah_tamu'],
            'malam'             => (int)$row['malam'],
            'total'             => (float)$row['total'],
            'status'            => $row['status'],
        ];
    }
    jsonResponse(true, $out, 'Daftar booking homestay');
} else {
    $conn = getConnection('layanan');
    $res = $conn->query("SELECT o.*, p.nama AS nama_pemesan, p.no_hp AS no_hp_pemesan,
            GROUP_CONCAT(CONCAT(oi.nama_produk, ' x', oi.jumlah) SEPARATOR ', ') AS ringkasan_item
        FROM `order` o
        LEFT JOIN pelanggan p ON p.id = o.pelanggan_id
        LEFT JOIN order_items oi ON oi.order_id = o.id
        GROUP BY o.id
        ORDER BY o.id DESC
        LIMIT 200");
    $out = [];
    while ($row = $res->fetch_assoc()) {
        [$alamatBersih, $metodeBayar] = parseAlamatPembayaran($row['alamat_kirim']);
        $out[] = [
            'id'                 => (int)$row['id'],
            'kode_order'         => $row['kode_order'],
            'nama_pemesan'       => $row['nama_pemesan'],
            'no_hp_pemesan'      => $row['no_hp_pemesan'],
            'alamat_kirim'       => $alamatBersih,
            'metode_pembayaran'  => $metodeBayar,
            'bukti_bayar_url'    => buktiBayarUrl('order', $row['kode_order']),
            'ringkasan_item'     => $row['ringkasan_item'],
            'metode_pengiriman'  => $row['metode_pengiriman'],
            'total_harga'        => (float)$row['total_harga'],
            'status_pembayaran'  => $row['status_pembayaran'],
            'status_order'       => $row['status_order'],
        ];
    }
    jsonResponse(true, $out, 'Daftar order belanja');
}
