<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="page-toolbar">
    <p>Kelola banner carousel pada hero beranda. Banner ditampilkan di sisi kanan halaman utama.</p>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-cloud-arrow-up"></i> Tambah Banner</h2>
        </div>
        <form method="post" action="<?= esc($form['action']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="gambar">Gambar *</label>
                <input type="file" id="gambar" name="gambar" accept="image/*" class="dropify" required data-height="220"
                    data-max-file-size="5M" data-allowed-file-extensions="jpg jpeg png webp gif">
                <small class="form-hint">Format JPG, PNG, WEBP, atau GIF. Maks. 5 MB. Disarankan rasio landscape.</small>
            </div>
            <div class="form-group">
                <label for="judul">Judul / Caption</label>
                <input type="text" id="judul" name="judul" placeholder="Teks singkat di bawah banner (opsional)">
            </div>
            <div class="form-group">
                <label for="link_url">Link (opsional)</label>
                <input type="url" id="link_url" name="link_url" placeholder="https://...">
                <small class="form-hint">Jika diisi, banner dapat diklik pengunjung.</small>
            </div>
            <div class="form-group">
                <label for="urutan">Urutan</label>
                <input type="number" id="urutan" name="urutan" value="0" min="0" step="1">
                <small class="form-hint">Angka lebih kecil tampil lebih dulu.</small>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <?php foreach ($form['statusOptions'] as $opt): ?>
                        <option value="<?= esc($opt['value']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                            <?= esc($opt['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-upload"></i> Tambah Banner
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-panorama"></i> Daftar Banner (<?= (int) $itemCount ?>)</h2>
        </div>
        <div class="gallery-admin-grid">
            <?php if ($items === []): ?>
                <p class="empty-state" style="grid-column: 1 / -1;">
                    <i class="fa-solid fa-inbox"></i>
                    Belum ada banner. Tambahkan banner agar tampil di beranda.
                </p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="gallery-admin-item banner-admin-item">
                        <img src="<?= esc($item['gambarUrl']) ?>" alt="<?= esc($item['judul']) ?>">
                        <div class="gallery-admin-meta">
                            <form method="post" action="<?= esc($item['updateUrl']) ?>" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label for="judul-<?= (int) $item['id'] ?>">Judul</label>
                                    <input type="text" id="judul-<?= (int) $item['id'] ?>" name="judul"
                                        value="<?= esc($item['judul'] === 'Tanpa judul' ? '' : $item['judul']) ?>"
                                        placeholder="Caption banner">
                                </div>
                                <div class="form-group">
                                    <label for="link-<?= (int) $item['id'] ?>">Link</label>
                                    <input type="url" id="link-<?= (int) $item['id'] ?>" name="link_url"
                                        value="<?= esc($item['linkUrl']) ?>" placeholder="https://...">
                                </div>
                                <div class="form-group">
                                    <label for="urutan-<?= (int) $item['id'] ?>">Urutan</label>
                                    <input type="number" id="urutan-<?= (int) $item['id'] ?>" name="urutan"
                                        value="<?= (int) $item['urutan'] ?>" min="0" step="1">
                                </div>
                                <div class="form-group">
                                    <label for="status-<?= (int) $item['id'] ?>">Status</label>
                                    <select id="status-<?= (int) $item['id'] ?>" name="status">
                                        <option value="publish" <?= $item['status'] === 'publish' ? 'selected' : '' ?>>Tampilkan</option>
                                        <option value="draft" <?= $item['status'] === 'draft' ? 'selected' : '' ?>>Sembunyikan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="gambar-<?= (int) $item['id'] ?>">Ganti Gambar</label>
                                    <input type="file" id="gambar-<?= (int) $item['id'] ?>" name="gambar" accept="image/*">
                                </div>
                                <div class="banner-admin-actions">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                                    </button>
                                    <span class="badge badge-<?= esc($item['statusBadge']) ?>">
                                        <?= $item['status'] === 'publish' ? 'Tampil' : 'Draft' ?>
                                    </span>
                                </div>
                            </form>
                            <form method="post" action="<?= esc($item['deleteUrl']) ?>" class="js-swal-confirm"
                                data-swal-title="Hapus banner ini?"
                                data-swal-text="Banner yang dihapus tidak dapat dikembalikan."
                                data-swal-icon="warning"
                                data-swal-confirm="Ya, Hapus">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
