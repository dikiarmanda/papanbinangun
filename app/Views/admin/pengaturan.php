<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="page-toolbar">
    <p>Identitas, kontak, dan media sosial situs desa wisata.</p>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-gear"></i> Pengaturan Situs</h2>
    </div>

    <form method="post" action="<?= esc($form['action']) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <h3 class="form-section-title">Identitas</h3>
        <div class="form-group">
            <label for="nama_desa">Nama Desa *</label>
            <input type="text" id="nama_desa" name="nama_desa" value="<?= $form['fields']['nama_desa'] ?>" required>
        </div>
        <div class="form-group">
            <label for="tagline">Tagline</label>
            <input type="text" id="tagline" name="tagline" value="<?= $form['fields']['tagline'] ?>">
        </div>
        <div class="form-group">
            <label for="deskripsi_singkat">Deskripsi Singkat</label>
            <textarea id="deskripsi_singkat" name="deskripsi_singkat" class="lexical-field" rows="6"
                data-toolbar="simple" data-min-height="200"
                data-placeholder="Tulis deskripsi singkat situs…"><?= $form['fields']['deskripsi_singkat'] ?></textarea>
        </div>
        <div class="form-group">
            <label for="logo">Logo</label>
            <input type="file" id="logo" name="logo" accept="image/*" class="dropify"
                data-height="180" data-max-file-size="2M"
                data-allowed-file-extensions="jpg jpeg png webp gif"
                <?php if ($form['logoUrl']): ?>data-default-file="<?= esc($form['logoUrl']) ?>"<?php endif; ?>>
            <small class="form-hint">Format JPG, PNG, WEBP, atau GIF. Maks. 2 MB.</small>
        </div>

        <h3 class="form-section-title">Kontak</h3>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" value="<?= $form['fields']['alamat'] ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="no_whatsapp">No. WhatsApp</label>
                <input type="text" id="no_whatsapp" name="no_whatsapp" value="<?= $form['fields']['no_whatsapp'] ?>"
                    placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group">
                <label for="email_kontak">Email Kontak</label>
                <input type="email" id="email_kontak" name="email_kontak"
                    value="<?= $form['fields']['email_kontak'] ?>">
            </div>
        </div>

        <h3 class="form-section-title">Media Sosial</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="instagram_url">Instagram URL</label>
                <input type="url" id="instagram_url" name="instagram_url"
                    value="<?= $form['fields']['instagram_url'] ?>">
            </div>
            <div class="form-group">
                <label for="tiktok_url">Tiktok URL</label>
                <input type="url" id="tiktok_url" name="tiktok_url" value="<?= $form['fields']['tiktok_url'] ?>">
            </div>
            <div class="form-group">
                <label for="facebook_url">Facebook URL</label>
                <input type="url" id="facebook_url" name="facebook_url" value="<?= $form['fields']['facebook_url'] ?>">
            </div>
        </div>

        <h3 class="form-section-title">Peta</h3>
        <div class="form-group">
            <label for="google_maps_embed">Google Maps Embed (iframe HTML)</label>
            <textarea id="google_maps_embed" name="google_maps_embed" rows="3"
                placeholder='<iframe src="..." ...></iframe>'><?= $form['fields']['google_maps_embed'] ?></textarea>
            <small class="form-hint">Salin kode embed dari Google Maps → Bagikan → Sematkan peta.</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>