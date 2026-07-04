<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="stamp">Kenangan</span>
        <h1>Galeri Foto</h1>
        <p>Potret alam, budaya, dan kehidupan desa</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="filter-bar">
            <a href="<?= site_url('galeri') ?>" class="<?= empty($kategori_aktif) ? 'active' : '' ?>">Semua</a>
            <?php foreach (['alam', 'budaya', 'kuliner', 'kerajinan', 'lainnya'] as $kat): ?>
                <a href="<?= site_url('galeri?kategori=' . $kat) ?>" class="<?= $kategori_aktif === $kat ? 'active' : '' ?>"><?= ucfirst($kat) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($galeri)): ?>
            <p class="text-center text-muted">Belum ada foto di galeri.</p>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($galeri as $g): ?>
                <a href="<?= upload_url($g['gambar']) ?>" class="gallery-item" data-lightbox title="<?= esc($g['judul'] ?? '') ?>">
                    <img src="<?= upload_url($g['gambar']) ?>" alt="<?= esc($g['judul'] ?? 'Galeri') ?>" loading="lazy">
                    <?php if (! empty($g['judul'])): ?>
                        <span class="gallery-caption"><?= esc($g['judul']) ?></span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<div id="lightbox" class="lightbox" hidden>
    <button type="button" class="lightbox-close" aria-label="Tutup">&times;</button>
    <img src="" alt="">
</div>

<?= $this->endSection() ?>
