<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="page-toolbar kategori-toolbar">
    <div>
        <p>Kelola kategori artikel agar konten lebih terstruktur dan mudah ditemukan.</p>
    </div>
    <span class="kategori-count-badge">
        <i class="fa-solid fa-tags"></i>
        <?= (int) $itemCount ?> kategori
    </span>
</div>

<div class="kategori-layout">
    <div class="card kategori-add-card">
        <div class="card-header">
            <h2><i class="fa-solid fa-plus"></i> Tambah Kategori</h2>
        </div>
        <form method="post" action="<?= esc($form['action']) ?>" class="kategori-add-form">
            <?= csrf_field() ?>
            <div class="kategori-add-fields">
                <div class="form-group">
                    <label for="nama">Nama Kategori *</label>
                    <input type="text" id="nama" name="nama" required placeholder="Mis. Wisata Alam">
                </div>
                <div class="form-group">
                    <label for="slug">Slug (opsional)</label>
                    <div class="input-with-action">
                        <input type="text" id="slug" name="slug" placeholder="otomatis dari nama">
                        <button type="button" class="btn btn-slug" data-slug-from="nama" data-slug-to="slug"
                            title="Generate slug dari nama kategori">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                        </button>
                    </div>
                </div>
            </div>
            <div class="kategori-add-action">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Tambah Kategori
                </button>
            </div>
        </form>
    </div>

    <div class="card kategori-list-card">
        <div class="card-header">
            <h2><i class="fa-solid fa-list"></i> Daftar Kategori</h2>
        </div>
        <div class="table-wrap kategori-table-wrap">
            <table class="kategori-table">
                <thead>
                    <tr>
                        <th class="col-nama">Nama</th>
                        <th class="col-slug">Slug</th>
                        <th class="col-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rows === []): ?>
                        <tr>
                            <td colspan="3" class="empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                Belum ada kategori.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td class="col-nama">
                                    <input type="text" id="kategori-nama-<?= (int) $row['id'] ?>" name="nama"
                                        form="kategori-form-<?= (int) $row['id'] ?>" value="<?= esc($row['nama']) ?>" required
                                        class="kategori-table-input">
                                </td>
                                <td class="col-slug">
                                    <div class="kategori-slug-field">
                                        <input type="text" id="kategori-slug-<?= (int) $row['id'] ?>" name="slug"
                                            form="kategori-form-<?= (int) $row['id'] ?>" value="<?= esc($row['slug']) ?>"
                                            placeholder="slug-kategori" class="kategori-table-input">
                                        <button type="button" class="btn btn-slug"
                                            data-slug-from="kategori-nama-<?= (int) $row['id'] ?>"
                                            data-slug-to="kategori-slug-<?= (int) $row['id'] ?>"
                                            title="Generate slug dari nama">
                                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="col-actions">
                                    <div class="kategori-actions-inner">
                                        <form method="post" action="<?= esc($row['updateUrl']) ?>"
                                            id="kategori-form-<?= (int) $row['id'] ?>" class="kategori-row-form">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-floppy-disk"></i> Simpan
                                            </button>
                                        </form>
                                        <form method="post" action="<?= esc($row['deleteUrl']) ?>" class="kategori-delete-form js-swal-confirm"
                                            data-swal-title="Hapus kategori ini?"
                                            data-swal-text="Kategori yang masih dipakai tidak dapat dihapus."
                                            data-swal-icon="warning"
                                            data-swal-confirm="Ya, Hapus">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>