<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-pen-to-square"></i> <?= esc($title) ?></h2>
        <a href="<?= esc($form['backUrl']) ?>" class="btn btn-sm btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    <form method="post" action="<?= esc($form['action']) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="judul">Judul *</label>
            <input type="text" id="judul" name="judul" value="<?= $form['judul'] ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug (opsional)</label>
            <div class="input-with-action">
                <input type="text" id="slug" name="slug" value="<?= $form['slug'] ?>" placeholder="otomatis dari judul">
                <button type="button" class="btn btn-slug" data-slug-from="judul" data-slug-to="slug"
                    title="Generate slug dari judul artikel">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                </button>
            </div>
            <small class="form-hint">Klik Generate untuk membuat slug dari judul, atau kosongkan agar otomatis saat simpan.</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id" class="select2-field" data-placeholder="Pilih kategori">
                    <?php foreach ($form['kategoriOptions'] as $opt): ?>
                        <option value="<?= esc($opt['value']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                            <?= esc($opt['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
        </div>

        <div class="form-group">
            <label for="konten">Konten *</label>
            <textarea id="konten" name="konten" class="lexical-field" rows="12" required data-toolbar="full"
                data-min-height="400"
                data-placeholder="Tulis konten artikel…"><?= $form['konten'] ?></textarea>
            <small class="form-hint">Ringkasan artikel otomatis diambil dari 300 karakter pertama konten saat disimpan.</small>
        </div>

        <div class="form-group">
            <label for="gambar_cover">Gambar Cover</label>
            <input type="file" id="gambar_cover" name="gambar_cover" accept="image/*" class="dropify"
                data-height="220" data-max-file-size="5M"
                data-allowed-file-extensions="jpg jpeg png webp gif"
                <?php if ($form['coverUrl']): ?>data-default-file="<?= esc($form['coverUrl']) ?>"<?php endif; ?>>
            <small class="form-hint">Format JPG, PNG, WEBP, atau GIF. Maks. 5 MB.</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            <a href="<?= esc($form['backUrl']) ?>" class="btn">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
