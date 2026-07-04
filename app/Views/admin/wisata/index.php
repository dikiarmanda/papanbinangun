<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="page-toolbar">
    <p>Kelola destinasi wisata desa.</p>
    <a href="<?= site_url('admin/wisata/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Destinasi
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Fasilitas</th>
                    <th>Peta</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []): ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="fa-solid fa-inbox"></i>
                            Belum ada destinasi.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= esc($row['nama']) ?></td>
                            <td><?= (int) $row['fasilitasCount'] ?> item</td>
                            <td><?= $row['hasMap'] ? 'Ya' : '-' ?></td>
                            <td><span class="badge badge-<?= esc($row['status']) ?>"><?= esc($row['status']) ?></span></td>
                            <td class="actions">
                                <a href="<?= esc($row['editUrl']) ?>" class="btn btn-sm">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form method="post" action="<?= esc($row['deleteUrl']) ?>" class="inline-form"
                                    onsubmit="return confirm('Hapus destinasi ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>