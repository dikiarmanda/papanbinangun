<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-newspaper"></i></div>
        <div class="stat-body">
            <span class="stat-label">Total Artikel</span>
            <span class="stat-value"><?= (int) $totalArtikel ?></span>
            <span class="stat-meta"><?= (int) $artikelPublish ?> publish · <?= (int) $artikelDraft ?> draft</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-mountain-sun"></i></div>
        <div class="stat-body">
            <span class="stat-label">Destinasi Wisata</span>
            <span class="stat-value"><?= (int) $totalWisata ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-images"></i></div>
        <div class="stat-body">
            <span class="stat-label">Foto Galeri</span>
            <span class="stat-value"><?= (int) $totalGaleri ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users-gear"></i></div>
        <div class="stat-body">
            <span class="stat-label">Akun Admin</span>
            <span class="stat-value"><?= (int) $totalAdmin ?></span>
        </div>
    </div>
</div>

<div class="grid-<?= $isSuperadmin ? '2' : '1' ?>">
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-clock"></i> Artikel Terbaru</h2>
            <a href="<?= site_url('admin/artikel/create') ?>" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($artikelTerbaru === []): ?>
                        <tr>
                            <td colspan="3" class="empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                Belum ada artikel.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($artikelTerbaru as $row): ?>
                            <tr>
                                <td><a href="<?= esc($row['editUrl']) ?>"><?= esc($row['judul']) ?></a></td>
                                <td><span class="badge badge-<?= esc($row['status']) ?>"><?= esc($row['status']) ?></span></td>
                                <td><?= esc($row['createdAt']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($isSuperadmin): ?>
        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-list-ul"></i> Log Aktivitas</h2>
            </div>
            <ul class="activity-list">
                <?php if ($logs === []): ?>
                    <li class="text-muted">Belum ada aktivitas.</li>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <li>
                            <strong><?= esc($log['adminNama']) ?></strong>
                            <?= esc($log['aksi']) ?>
                            <small><?= esc($log['tanggal']) ?> · <?= esc($log['jam']) ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>