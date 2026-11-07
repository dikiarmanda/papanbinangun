<?php
session_start();
require_once __DIR__ . '/config_admin.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../api/config.php';
// config.php di atas set header JSON untuk kebutuhan API — timpa lagi
// supaya halaman admin ini dikirim sebagai HTML biasa.
header('Content-Type: text/html; charset=UTF-8');

$tab = ($_GET['tab'] ?? 'homestay') === 'belanja' ? 'belanja' : 'homestay';

function parseAlamatCatatan($raw) {
    $alamat = '';
    $catatan = null;
    if ($raw !== null && preg_match('/^Alamat:\s*(.*?)(?:\nCatatan:\s*(.*))?$/s', $raw, $m)) {
        $alamat = trim($m[1]);
        $catatan = isset($m[2]) && trim($m[2]) !== '' ? trim($m[2]) : null;
    } else {
        $catatan = $raw;
    }
    return [$alamat, $catatan];
}

function badge($status) {
    $cls = strtolower(str_replace(' ', '_', (string)$status));
    return '<span class="badge ' . htmlspecialchars($cls) . '">' . htmlspecialchars(str_replace('_', ' ', (string)$status)) . '</span>';
}

function buktiBayarLink($jenis, $kode) {
    $url = buktiBayarUrl($jenis, $kode);
    if (!$url) {
        return '<span class="muted">-</span>';
    }
    return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener">'
        . '<img src="' . htmlspecialchars($url) . '" alt="Bukti bayar" class="bukti-thumb"></a>';
}

function selectOptions($options, $current) {
    $html = '';
    foreach ($options as $opt) {
        $sel = ($opt === $current) ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars($opt) . '"' . $sel . '>' . htmlspecialchars(str_replace('_', ' ', $opt)) . '</option>';
    }
    return $html;
}

$statusBookingOptions = ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'];
$statusBayarOptions = ['pending', 'lunas', 'dibatalkan'];
$statusOrderOptions = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

$bookings = [];
$orders = [];

if ($tab === 'homestay') {
    $conn = getConnection('reservasi');
    $res = $conn->query('SELECT b.*, h.nama AS nama_homestay
        FROM bookings b
        LEFT JOIN homestays h ON h.id = b.homestay_id
        ORDER BY b.id DESC
        LIMIT 200');
    while ($row = $res->fetch_assoc()) {
        $bookings[] = $row;
    }
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
    while ($row = $res->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin - Papan Binangun</title>
<?php include __DIR__ . '/_style.php'; ?>
</head>
<body>
  <header class="topbar">
    <h1>Admin Papan Binangun</h1>
    <a href="logout.php">Keluar</a>
  </header>
  <div class="container">
    <div class="tabs">
      <a href="?tab=homestay" class="<?= $tab === 'homestay' ? 'active' : '' ?>">Booking Homestay</a>
      <a href="?tab=belanja" class="<?= $tab === 'belanja' ? 'active' : '' ?>">Order Belanja</a>
    </div>

    <?php if (!empty($_GET['berhasil'])): ?>
      <div class="flash flash-success">Status berhasil diperbarui.</div>
    <?php elseif (!empty($_GET['gagal'])): ?>
      <div class="flash flash-error">Gagal memperbarui status: <?= htmlspecialchars($_GET['gagal']) ?></div>
    <?php endif; ?>

    <?php if ($tab === 'homestay'): ?>
      <?php if (empty($bookings)): ?>
        <div class="empty">Belum ada booking homestay.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Kode</th>
              <th>Homestay</th>
              <th>Tamu</th>
              <th>WhatsApp</th>
              <th>Alamat</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Tamu</th>
              <th>Total</th>
              <th>Pembayaran</th>
              <th>Bukti Bayar</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): [$alamat, $catatanOnly, $metodeBayarBooking] = parseBookingCatatanLengkap($b['catatan']); ?>
              <tr>
                <td><strong><?= htmlspecialchars($b['kode_booking']) ?></strong></td>
                <td><?= htmlspecialchars($b['nama_homestay'] ?? '-') ?></td>
                <td><?= htmlspecialchars($b['nama_tamu']) ?></td>
                <td><?= htmlspecialchars($b['whatsapp']) ?></td>
                <td><?= htmlspecialchars($alamat ?: '-') ?><?php if ($catatanOnly): ?><div class="muted">Catatan: <?= htmlspecialchars($catatanOnly) ?></div><?php endif; ?></td>
                <td><?= htmlspecialchars($b['checkin']) ?></td>
                <td><?= htmlspecialchars($b['checkout']) ?></td>
                <td><?= (int)$b['jumlah_tamu'] ?> orang / <?= (int)$b['malam'] ?> malam</td>
                <td>Rp <?= number_format((float)$b['total'], 0, ',', '.') ?></td>
                <td><?= $metodeBayarBooking ? htmlspecialchars($metodeBayarBooking) : '<span class="muted">-</span>' ?></td>
                <td><?= buktiBayarLink('booking', $b['kode_booking']) ?></td>
                <td><?= badge($b['status']) ?></td>
                <td>
                  <form method="post" action="update_status.php" class="aksi-form">
                    <input type="hidden" name="jenis" value="booking">
                    <input type="hidden" name="tab" value="homestay">
                    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                    <select name="status">
                      <?= selectOptions($statusBookingOptions, $b['status']) ?>
                    </select>
                    <button type="submit">Simpan</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    <?php else: ?>
      <?php if (empty($orders)): ?>
        <div class="empty">Belum ada order belanja.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Kode</th>
              <th>Pemesan</th>
              <th>No. HP</th>
              <th>Alamat Kirim</th>
              <th>Item</th>
              <th>Pengiriman</th>
              <th>Pembayaran</th>
              <th>Bukti Bayar</th>
              <th>Total</th>
              <th>Bayar</th>
              <th>Order</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): [$alamatOrder, $metodeBayarOrder] = parseAlamatPembayaran($o['alamat_kirim']); ?>
              <tr>
                <td><strong><?= htmlspecialchars($o['kode_order']) ?></strong></td>
                <td><?= htmlspecialchars($o['nama_pemesan'] ?? '-') ?></td>
                <td><?= htmlspecialchars($o['no_hp_pemesan'] ?? '-') ?></td>
                <td><?= htmlspecialchars($alamatOrder) ?></td>
                <td><?= htmlspecialchars($o['ringkasan_item'] ?? '-') ?></td>
                <td><?= htmlspecialchars(str_replace('_', ' ', $o['metode_pengiriman'])) ?></td>
                <td><?= $metodeBayarOrder ? htmlspecialchars($metodeBayarOrder) : '<span class="muted">-</span>' ?></td>
                <td><?= buktiBayarLink('order', $o['kode_order']) ?></td>
                <td>Rp <?= number_format((float)$o['total_harga'], 0, ',', '.') ?></td>
                <td><?= badge($o['status_pembayaran']) ?></td>
                <td><?= badge($o['status_order']) ?></td>
                <td>
                  <form method="post" action="update_status.php" class="aksi-form">
                    <input type="hidden" name="jenis" value="order">
                    <input type="hidden" name="tab" value="belanja">
                    <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                    <select name="status_pembayaran">
                      <?= selectOptions($statusBayarOptions, $o['status_pembayaran']) ?>
                    </select>
                    <select name="status_order">
                      <?= selectOptions($statusOrderOptions, $o['status_order']) ?>
                    </select>
                    <button type="submit">Simpan</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</body>
</html>
