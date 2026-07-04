<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="page-toolbar">
    <p>Upload dan kelola foto galeri desa wisata.</p>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-cloud-arrow-up"></i> Upload Foto</h2>
        </div>
        <form method="post" action="<?= esc($form['action']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="gambar">Gambar *</label>
                <input type="file" id="gambar" name="gambar" accept="image/*" class="dropify" required data-height="220"
                    data-max-file-size="5M" data-allowed-file-extensions="jpg jpeg png webp gif">
                <small class="form-hint">Format JPG, PNG, WEBP, atau GIF. Maks. 5 MB.</small>
            </div>
            <div class="form-group">
                <label for="judul">Judul</label>
                <input type="text" id="judul" name="judul" placeholder="Judul foto (opsional)">
            </div>
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori">
                    <?php foreach ($form['kategoriOptions'] as $opt): ?>
                        <option value="<?= esc($opt['value']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                            <?= esc($opt['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="wisata_id">Terkait Destinasi (opsional)</label>
                <select id="wisata_id" name="wisata_id" class="select2-field" data-placeholder="Pilih destinasi">
                    <?php foreach ($form['wisataOptions'] as $opt): ?>
                        <option value="<?= esc($opt['value']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                            <?= esc($opt['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-upload"></i> Upload
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-images"></i> Daftar Foto (<?= (int) $itemCount ?>)</h2>
        </div>
        <div class="gallery-admin-grid">
            <?php if ($items === []): ?>
                <p class="empty-state" style="grid-column: 1 / -1;">
                    <i class="fa-solid fa-inbox"></i>
                    Belum ada foto.
                </p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="gallery-admin-item">
                        <img src="<?= esc($item['gambarUrl']) ?>" alt="<?= esc($item['judul']) ?>">
                        <div class="gallery-admin-meta">
                            <strong><?= esc($item['judul']) ?></strong>
                            <span class="badge"><?= esc($item['kategori']) ?></span>
                            <form method="post" action="<?= esc($item['deleteUrl']) ?>"
                                onsubmit="return confirm('Hapus foto ini?')">
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