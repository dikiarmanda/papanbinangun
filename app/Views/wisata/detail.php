<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero page-hero-image"
    style="--hero-img: url('<?= upload_url($wisata['gambar_cover'], 'wisata', 0) ?>')">
    <div class="container">
        <span class="stamp">Destinasi</span>
        <h1><?= esc($wisata['nama']) ?></h1>
    </div>
</section>

<section class="section">
    <div class="container prose">
        <div class="detail-cover">
            <img src="<?= upload_url($wisata['gambar_cover'], 'wisata', 0) ?>" alt="<?= esc($wisata['nama']) ?>">
        </div>

        <div class="content-body content-html">
            <?= $wisata['deskripsi'] ?>
        </div>

        <?php if (!empty($fasilitas)): ?>
            <div class="wisata-fasilitas mt-lg">
                <h3><i class="fa-solid fa-list-check"></i> Fasilitas</h3>
                <ul class="wisata-fasilitas-grid">
                    <?php foreach ($fasilitas as $f): ?>
                        <li class="wisata-fasilitas-item">
                            <span class="wisata-fasilitas-icon">
                                <i class="fa-solid <?= esc($f['icon']) ?>" aria-hidden="true"></i>
                            </span>
                            <span><?= esc($f['nama']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($wisata['google_maps_embed'])): ?>
            <div class="map-box mt-lg">
                <h3><i class="fa-solid fa-map-location-dot"></i> Lokasi di Peta</h3>
                <div class="map-embed">
                    <?= $wisata['google_maps_embed'] ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($galeri)): ?>
            <h3 class="mt-lg">Galeri Destinasi</h3>
            <div class="gallery-grid">
                <?php foreach ($galeri as $g): ?>
                    <a href="<?= upload_url($g['gambar']) ?>" class="gallery-item" data-lightbox>
                        <img src="<?= upload_url($g['gambar']) ?>" alt="<?= esc($g['judul'] ?? $wisata['nama']) ?>"
                            loading="lazy">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-lg">
            <a href="<?= site_url('wisata') ?>" class="btn btn-outline">← Kembali ke Daftar Wisata</a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>