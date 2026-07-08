<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="galeri-admin-page">
    <div class="card galeri-upload-card">
        <div class="card-header">
            <h2><i class="fa-solid fa-cloud-arrow-up"></i> Upload Foto Baru</h2>
        </div>
        <form method="post" action="<?= esc($form['action']) ?>" enctype="multipart/form-data"
            class="galeri-upload-form">
            <?= csrf_field() ?>
            <div class="galeri-upload-grid">
                <div class="form-group galeri-upload-file">
                    <label for="gambar">Gambar *</label>
                    <input type="file" id="gambar" name="gambar" accept="image/*" class="dropify" required
                        data-height="200" data-max-file-size="5M" data-allowed-file-extensions="jpg jpeg png webp gif">
                    <small class="form-hint">JPG, PNG, WEBP, atau GIF. Maks. 5 MB.</small>
                </div>
                <div class="galeri-upload-fields">
                    <div class="form-group">
                        <label for="judul">Judul</label>
                        <input type="text" id="judul" name="judul" placeholder="Judul foto (opsional)">
                    </div>
                    <div class="form-group">
                        <label for="kategori_id">Kategori</label>
                        <select id="kategori_id" name="kategori_id[]" multiple class="select2-field select2-multiple"
                            data-placeholder="Pilih satu atau lebih kategori">
                            <?php foreach ($form['kategoriOptions'] as $opt): ?>
                                <option value="<?= esc($opt['value']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                                    <?= esc($opt['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-hint">Bisa memilih lebih dari satu kategori.</small>
                    </div>
                    <div class="form-group">
                        <label for="wisata_id">Terkait Destinasi</label>
                        <select id="wisata_id" name="wisata_id" class="select2-field"
                            data-placeholder="Pilih destinasi (opsional)">
                            <?php foreach ($form['wisataOptions'] as $opt): ?>
                                <option value="<?= esc($opt['value']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                                    <?= esc($opt['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-upload"></i> Upload Foto
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card galeri-list-card">
        <div class="card-header galeri-list-header">
            <h2><i class="fa-solid fa-images"></i> Daftar Foto</h2>
            <span class="galeri-count-badge"><?= (int) $itemCount ?> foto</span>
        </div>

        <?php if ($items === []): ?>
            <p class="empty-state">
                <i class="fa-solid fa-inbox"></i>
                Belum ada foto. Upload foto pertama Anda di atas.
            </p>
        <?php else: ?>
            <div class="galeri-admin-grid">
                <?php foreach ($items as $item): ?>
                    <article class="galeri-card">
                        <div class="galeri-card-media">
                            <img src="<?= esc($item['gambarUrl']) ?>" alt="<?= esc($item['judul']) ?>" loading="lazy">
                            <div class="galeri-card-overlay">
                                <button type="button" class="galeri-card-btn galeri-edit-btn" title="Edit foto"
                                    data-id="<?= (int) $item['id'] ?>"
                                    data-judul="<?= esc($item['judul'] === 'Tanpa judul' ? '' : $item['judul'], 'attr') ?>"
                                    data-kategori-ids="<?= esc(json_encode($item['kategoriIds']), 'attr') ?>"
                                    data-wisata-id="<?= esc((string) $item['wisataId'], 'attr') ?>"
                                    data-gambar-url="<?= esc($item['gambarUrl'], 'attr') ?>"
                                    data-update-url="<?= esc($item['updateUrl'], 'attr') ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="galeri-card-btn galeri-delete-btn" title="Hapus foto"
                                    data-id="<?= (int) $item['id'] ?>" data-judul="<?= esc($item['judul'], 'attr') ?>"
                                    data-gambar-url="<?= esc($item['gambarUrl'], 'attr') ?>"
                                    data-delete-url="<?= esc($item['deleteUrl'], 'attr') ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="galeri-card-body">
                            <strong class="galeri-card-title"><?= esc($item['judul']) ?></strong>
                            <?php if ($item['kategoriNames'] !== []): ?>
                                <div class="galeri-card-tags">
                                    <?php foreach ($item['kategoriNames'] as $nama): ?>
                                        <span class="badge badge-soft"><?= esc($nama) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-modal" id="galeriEditModal" hidden aria-hidden="true">
    <div class="admin-modal-backdrop" data-modal-close></div>
    <div class="admin-modal-dialog admin-modal-lg" role="dialog" aria-modal="true" aria-labelledby="galeriEditTitle">
        <div class="admin-modal-header">
            <h3 id="galeriEditTitle"><i class="fa-solid fa-pen"></i> Edit Foto</h3>
            <button type="button" class="admin-modal-close" data-modal-close aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="post" id="galeriEditForm" action="">
            <?= csrf_field() ?>
            <div class="admin-modal-body galeri-edit-body">
                <div class="galeri-edit-preview">
                    <img id="galeriEditPreview" src="" alt="Preview foto">
                </div>
                <div class="galeri-edit-fields">
                    <div class="form-group">
                        <label for="galeriEditJudul">Judul</label>
                        <input type="text" id="galeriEditJudul" name="judul" placeholder="Judul foto">
                    </div>
                    <div class="form-group">
                        <label for="galeriEditKategori">Kategori</label>
                        <select id="galeriEditKategori" name="kategori_id[]" multiple class="select2-field select2-multiple"
                            data-placeholder="Pilih satu atau lebih kategori">
                            <?php foreach ($form['kategoriOptions'] as $opt): ?>
                                <option value="<?= esc($opt['value']) ?>"><?= esc($opt['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="galeriEditWisata">Destinasi</label>
                        <select id="galeriEditWisata" name="wisata_id" class="select2-field"
                            data-placeholder="Pilih destinasi">
                            <?php foreach ($form['wisataOptions'] as $opt): ?>
                                <option value="<?= esc($opt['value']) ?>"><?= esc($opt['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="admin-modal" id="galeriDeleteModal" hidden aria-hidden="true">
    <div class="admin-modal-backdrop" data-modal-close></div>
    <div class="admin-modal-dialog admin-modal-sm" role="dialog" aria-modal="true" aria-labelledby="galeriDeleteTitle">
        <div class="admin-modal-header">
            <h3 id="galeriDeleteTitle"><i class="fa-solid fa-trash"></i> Hapus Foto</h3>
            <button type="button" class="admin-modal-close" data-modal-close aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="post" id="galeriDeleteForm" action="">
            <?= csrf_field() ?>
            <div class="admin-modal-body galeri-delete-body">
                <div class="galeri-delete-preview">
                    <img id="galeriDeletePreview" src="" alt="">
                </div>
                <p>Anda yakin ingin menghapus foto <strong id="galeriDeleteLabel"></strong>? Tindakan ini tidak dapat
                    dibatalkan.</p>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>