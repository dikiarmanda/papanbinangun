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
            <label for="nama">Nama Destinasi *</label>
            <input type="text" id="nama" name="nama" value="<?= $form['nama'] ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug (opsional)</label>
            <div class="input-with-action">
                <input type="text" id="slug" name="slug" value="<?= $form['slug'] ?>" placeholder="otomatis dari nama">
                <button type="button" class="btn btn-slug" data-slug-from="nama" data-slug-to="slug"
                    title="Generate slug dari nama destinasi">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                </button>
            </div>
            <small class="form-hint">Klik Generate untuk membuat slug dari nama, atau kosongkan agar otomatis saat
                simpan.</small>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi *</label>
            <textarea id="deskripsi" name="deskripsi" class="lexical-field" rows="8" required
                data-toolbar="full" data-min-height="320"
                data-placeholder="Tulis deskripsi destinasi…"><?= $form['deskripsi'] ?></textarea>
        </div>

        <div class="form-group">
            <label>Fasilitas</label>
            <p class="form-hint">Isi fasilitas yang tersedia di destinasi ini.</p>
            <div class="table-wrap fasilitas-table-wrap">
                <table class="fasilitas-table">
                    <thead>
                        <tr>
                            <th>Nama Fasilitas</th>
                            <th>Ikon</th>
                            <th class="col-preview">Preview</th>
                            <th class="col-action">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="fasilitasEditor" data-icons='<?= $form['fasilitasIconsJson'] ?>'>
                        <?php foreach ($form['fasilitasItems'] as $item): ?>
                            <tr class="fasilitas-row">
                                <td>
                                    <input type="text" name="fasilitas_nama[]" value="<?= esc($item['nama']) ?>"
                                        placeholder="Mis. Parkir">
                                </td>
                                <td>
                                    <select name="fasilitas_icon[]" class="select2-field" data-placeholder="Pilih ikon">
                                        <option value="">— Pilih ikon —</option>
                                        <?php foreach ($item['iconOptions'] as $opt): ?>
                                            <option value="<?= esc($opt['value']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                                                <?= esc($opt['label']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="col-preview">
                                    <span class="fasilitas-preview" aria-hidden="true">
                                        <i class="fa-solid <?= esc($item['previewIcon']) ?>"></i>
                                    </span>
                                </td>
                                <td class="col-action">
                                    <button type="button" class="btn btn-sm btn-danger fasilitas-remove" title="Hapus baris">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm fasilitas-add" id="fasilitasAdd">
                <i class="fa-solid fa-plus"></i> Tambah Fasilitas
            </button>
        </div>

        <div class="form-group">
            <label for="google_maps_embed">Google Maps Embed</label>
            <textarea id="google_maps_embed" name="google_maps_embed" rows="4"
                placeholder='<iframe src="https://www.google.com/maps/embed?..." ...></iframe>'><?= $form['googleMapsEmbed'] ?></textarea>
            <small class="form-hint">Salin kode embed dari Google Maps → Bagikan → Sematkan peta. Kosongkan jika tidak perlu peta.</small>
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
