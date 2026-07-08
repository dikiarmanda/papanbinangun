<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="page-toolbar">
    <p>Kelola artikel dan berita desa wisata.</p>
    <a href="<?= site_url('admin/artikel/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Artikel
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []): ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="fa-solid fa-inbox"></i>
                            Belum ada artikel.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= esc($row['judul']) ?></td>
                            <td><?= esc($row['kategoriNama']) ?></td>
                            <td><?= esc($row['penulis']) ?></td>
                            <td><span class="badge badge-<?= esc($row['status']) ?>"><?= esc($row['status']) ?></span></td>
                            <td><?= (int) $row['views'] ?></td>
                            <td class="actions">
                                <a href="<?= esc($row['editUrl']) ?>" class="btn btn-sm">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form method="post" action="<?= esc($row['deleteUrl']) ?>" class="inline-form js-swal-confirm"
                                    data-swal-title="Hapus artikel ini?"
                                    data-swal-text="Artikel yang dihapus tidak dapat dikembalikan."
                                    data-swal-icon="warning"
                                    data-swal-confirm="Ya, Hapus">
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