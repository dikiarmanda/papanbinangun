<?php
require_once __DIR__ . '/../config.php';

// ======================================================
// Buat order produk UMKM/catering — tanpa login (guest checkout).
// Dipanggil dari Flutter: ApiService.buatOrder().
//
// Body JSON yang dikirim app:
//   nama_pemesan, no_hp_pemesan, alamat_kirim (sudah termasuk
//   "[Pembayaran: ...]" yang disisipkan Flutter — lihat
//   metode_pembayaran.dart / parseAlamatPembayaran() di config.php),
//   metode_pengiriman ('ekspedisi'|'ambil_di_tempat'|'antar_lokal'),
//   zona_antar_id (opsional), items: [{produk_id, jumlah}, ...],
//   ongkos_kirim (opsional)
// ======================================================

function generateKodeOrder() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
}

$input = getJsonInput();

$namaPemesan = trim($input['nama_pemesan'] ?? '');
$noHpPemesan = trim($input['no_hp_pemesan'] ?? '');
$alamatKirim = trim($input['alamat_kirim'] ?? '');
$metodePengiriman = trim($input['metode_pengiriman'] ?? 'ambil_di_tempat');
$zonaAntarId = (isset($input['zona_antar_id']) && $input['zona_antar_id'] !== null && $input['zona_antar_id'] !== '')
    ? (int)$input['zona_antar_id'] : null;
$items = $input['items'] ?? [];
$ongkosKirim = isset($input['ongkos_kirim']) ? (float)$input['ongkos_kirim'] : 0.0;

if ($namaPemesan === '' || $noHpPemesan === '' || $alamatKirim === '') {
    jsonResponse(false, null, 'Nama, no HP, dan alamat kirim wajib diisi', 400);
}

$metodeValid = ['ekspedisi', 'ambil_di_tempat', 'antar_lokal'];
if (!in_array($metodePengiriman, $metodeValid, true)) {
    jsonResponse(false, null, 'Metode pengiriman tidak valid', 400);
}

if (!is_array($items) || count($items) === 0) {
    jsonResponse(false, null, 'Keranjang belanja kosong', 400);
}

$conn = getConnection();
$conn->begin_transaction();

try {
    $pelangganId = findOrCreateGuestPelanggan($conn, $namaPemesan, $noHpPemesan);

    // Hitung total dari harga produk yang tersimpan di DB saat ini
    // (bukan dari input app) supaya tidak bisa dimanipulasi client.
    $itemsToInsert = [];
    $totalHarga = 0.0;

    foreach ($items as $it) {
        $produkId = (int)($it['produk_id'] ?? 0);
        $jumlah = (int)($it['jumlah'] ?? 0);
        if ($produkId <= 0 || $jumlah <= 0) {
            throw new Exception('Data item keranjang tidak valid');
        }

        $stmt = $conn->prepare('SELECT id, nama, harga, stok FROM produk WHERE id = ? AND status = "publish" FOR UPDATE');
        $stmt->bind_param('i', $produkId);
        $stmt->execute();
        $produk = $stmt->get_result()->fetch_assoc();

        if (!$produk) {
            throw new Exception('Produk tidak ditemukan (id: ' . $produkId . ')');
        }
        if ((int)$produk['stok'] < $jumlah) {
            throw new Exception('Stok produk "' . $produk['nama'] . '" tidak mencukupi');
        }

        $hargaSatuan = (float)$produk['harga'];
        $subtotal = $hargaSatuan * $jumlah;
        $totalHarga += $subtotal;

        $itemsToInsert[] = [
            'produk_id'     => $produkId,
            'nama_produk'   => $produk['nama'],
            'harga_satuan'  => $hargaSatuan,
            'jumlah'        => $jumlah,
            'subtotal'      => $subtotal,
        ];

        $upd = $conn->prepare('UPDATE produk SET stok = stok - ? WHERE id = ?');
        $upd->bind_param('ii', $jumlah, $produkId);
        $upd->execute();
    }

    $totalHarga += $ongkosKirim;
    $kodeOrder = generateKodeOrder();

    $stmt = $conn->prepare('INSERT INTO `order`
        (kode_order, pelanggan_id, alamat_kirim, metode_pengiriman, zona_antar_id, ongkos_kirim, total_harga, status_pembayaran, status_order, stok_locked)
        VALUES (?, ?, ?, ?, ?, ?, ?, "pending", "menunggu_pembayaran", 1)');
    $stmt->bind_param('sissidd', $kodeOrder, $pelangganId, $alamatKirim, $metodePengiriman, $zonaAntarId, $ongkosKirim, $totalHarga);
    $stmt->execute();
    $orderId = $stmt->insert_id;

    $itemStmt = $conn->prepare('INSERT INTO order_items (order_id, produk_id, nama_produk, harga_satuan, jumlah, subtotal) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($itemsToInsert as $it) {
        $itemStmt->bind_param(
            'iisdid',
            $orderId,
            $it['produk_id'],
            $it['nama_produk'],
            $it['harga_satuan'],
            $it['jumlah'],
            $it['subtotal']
        );
        $itemStmt->execute();
    }

    $conn->commit();

    jsonResponse(true, [
        'id'                => $orderId,
        'kode_order'        => $kodeOrder,
        'total_harga'       => $totalHarga,
        'status_pembayaran' => 'pending',
        'status_order'      => 'menunggu_pembayaran',
    ], 'Order berhasil dibuat');
} catch (Exception $e) {
    $conn->rollback();
    jsonResponse(false, null, $e->getMessage(), 400);
}
