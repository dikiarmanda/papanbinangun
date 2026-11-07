<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
use App\Libraries\TransaksiService;

function formatBadge($status) {
    $slug = strtolower(str_replace([' ', '_'], '-', (string)$status));
    $label = ucwords(str_replace('_', ' ', (string)$status));
    return '<span class="badge badge-' . esc($slug) . '">' . esc($label) . '</span>';
}
?>

<style>
.tab-nav {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 8px;
}
.tab-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 14px;
    color: #64748b;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s;
}
.tab-link:hover {
    color: #1e293b;
    background: #f1f5f9;
}
.tab-link.active {
    color: #0f172a;
    background: #e2e8f0;
}
.bukti-thumb {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
}
.inline-aksi-form {
    display: flex;
    align-items: center;
    gap: 6px;
}
.inline-aksi-form select {
    padding: 4px 8px;
    font-size: 13px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
}
.muted-text {
    color: #94a3b8;
    font-size: 12px;
}
</style>

<div class="page-toolbar">
    <p>Kelola reservasi homestay dan pesanan belanja UMKM/catering desa wisata.</p>
</div>

<div class="tab-nav">
    <a href="<?= site_url('admin/transaksi?tab=homestay') ?>" class="tab-link <?= $tab === 'homestay' ? 'active' : '' ?>">
        <i class="fa-solid fa-hotel"></i> Booking Homestay
    </a>
    <a href="<?= site_url('admin/transaksi?tab=belanja') ?>" class="tab-link <?= $tab === 'belanja' ? 'active' : '' ?>">
        <i class="fa-solid fa-bag-shopping"></i> Order Belanja
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if ($tab === 'homestay'): ?>
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Homestay</th>
                        <th>Tamu & Kontak</th>
                        <th>Alamat & Catatan</th>
                        <th>Jadwal</th>
                        <th>Tamu/Malam</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Bukti Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="11" class="empty-state">
                                <i class="fa-solid fa-inbox"></i> Belum ada booking homestay.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <?php
                            [$alamat, $catatanOnly, $metodeBayar] = TransaksiService::parseBookingCatatanLengkap($b['catatan']);
                            $buktiUrl = TransaksiService::buktiBayarUrl('booking', $b['kode_booking']);
                            ?>
                            <tr>
                                <td><strong><?= esc($b['kode_booking']) ?></strong></td>
                                <td><?= esc($b['nama_homestay'] ?? '-') ?></td>
                                <td>
                                    <div><?= esc($b['nama_tamu']) ?></div>
                                    <div class="muted-text"><i class="fa-brands fa-whatsapp"></i> <?= esc($b['whatsapp']) ?></div>
                                </td>
                                <td>
                                    <div><?= esc($alamat ?: '-') ?></div>
                                    <?php if ($catatanOnly): ?>
                                        <div class="muted-text">Catatan: <?= esc($catatanOnly) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>In: <?= esc($b['checkin']) ?></div>
                                    <div>Out: <?= esc($b['checkout']) ?></div>
                                </td>
                                <td><?= (int)$b['jumlah_tamu'] ?> org / <?= (int)$b['malam'] ?> mlm</td>
                                <td><strong>Rp <?= number_format((float)$b['total'], 0, ',', '.') ?></strong></td>
                                <td><?= $metodeBayar ? esc($metodeBayar) : '<span class="muted-text">-</span>' ?></td>
                                <td>
                                    <?php if ($buktiUrl): ?>
                                        <a href="<?= esc($buktiUrl) ?>" target="_blank" rel="noopener">
                                            <img src="<?= esc($buktiUrl) ?>" alt="Bukti" class="bukti-thumb">
                                        </a>
                                    <?php else: ?>
                                        <span class="muted-text">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatBadge($b['status']) ?></td>
                                <td>
                                    <form method="post" action="<?= site_url('admin/transaksi/update-status') ?>" class="inline-aksi-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="jenis" value="booking">
                                        <input type="hidden" name="tab" value="homestay">
                                        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                                        <select name="status">
                                            <?php foreach ($statusBookingOptions as $opt): ?>
                                                <option value="<?= esc($opt) ?>" <?= $b['status'] === $opt ? 'selected' : '' ?>>
                                                    <?= esc(ucwords(str_replace('_', ' ', $opt))) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pemesan</th>
                        <th>Alamat Kirim</th>
                        <th>Item</th>
                        <th>Pengiriman</th>
                        <th>Total</th>
                        <th>Bukti Bayar</th>
                        <th>Status Bayar</th>
                        <th>Status Order</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="10" class="empty-state">
                                <i class="fa-solid fa-inbox"></i> Belum ada pesanan belanja.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <?php
                            [$alamatKirim, $metodeBayar] = TransaksiService::parseAlamatPembayaran($o['alamat_kirim']);
                            $buktiUrl = TransaksiService::buktiBayarUrl('order', $o['kode_order']);
                            ?>
                            <tr>
                                <td><strong><?= esc($o['kode_order']) ?></strong></td>
                                <td>
                                    <div><?= esc($o['nama_pemesan'] ?? '-') ?></div>
                                    <div class="muted-text"><i class="fa-solid fa-phone"></i> <?= esc($o['no_hp_pemesan'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <div><?= esc($alamatKirim) ?></div>
                                    <?php if ($metodeBayar): ?>
                                        <div class="muted-text">Metode: <?= esc($metodeBayar) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($o['ringkasan_item'] ?? '-') ?></td>
                                <td><?= esc(ucwords(str_replace('_', ' ', $o['metode_pengiriman'] ?? '-'))) ?></td>
                                <td><strong>Rp <?= number_format((float)$o['total_harga'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <?php if ($buktiUrl): ?>
                                        <a href="<?= esc($buktiUrl) ?>" target="_blank" rel="noopener">
                                            <img src="<?= esc($buktiUrl) ?>" alt="Bukti" class="bukti-thumb">
                                        </a>
                                    <?php else: ?>
                                        <span class="muted-text">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatBadge($o['status_pembayaran']) ?></td>
                                <td><?= formatBadge($o['status_order']) ?></td>
                                <td>
                                    <form method="post" action="<?= site_url('admin/transaksi/update-status') ?>" class="inline-aksi-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="jenis" value="order">
                                        <input type="hidden" name="tab" value="belanja">
                                        <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                                        <select name="status_pembayaran" title="Status Pembayaran">
                                            <?php foreach ($statusBayarOptions as $opt): ?>
                                                <option value="<?= esc($opt) ?>" <?= $o['status_pembayaran'] === $opt ? 'selected' : '' ?>>
                                                    <?= esc(ucwords(str_replace('_', ' ', $opt))) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select name="status_order" title="Status Order">
                                            <?php foreach ($statusOrderOptions as $opt): ?>
                                                <option value="<?= esc($opt) ?>" <?= $o['status_order'] === $opt ? 'selected' : '' ?>>
                                                    <?= esc(ucwords(str_replace('_', ' ', $opt))) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
