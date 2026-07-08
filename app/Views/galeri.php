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
            <?php foreach ($kategori_list as $kat): ?>
                <a href="<?= site_url('galeri?kategori=' . esc($kat['slug'], 'url')) ?>"
                    class="<?= $kategori_aktif === $kat['slug'] ? 'active' : '' ?>">
                    <?= esc($kat['nama']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($galeri)): ?>
            <p class="text-center text-muted">Belum ada foto di galeri.</p>
        <?php else: ?>
            <div class="gallery-grid" id="galleryGrid"
                data-initial="<?= (int) $galeri_limit_initial ?>"
                data-step="<?= (int) $galeri_limit_step ?>">
                <?php foreach ($galeri as $index => $g): ?>
                    <a href="<?= upload_url($g['gambar']) ?>"
                        class="gallery-item<?= $index >= $galeri_limit_initial ? ' is-gallery-hidden' : '' ?>"
                        data-lightbox title="<?= esc($g['judul'] ?? '') ?>">
                        <img src="<?= upload_url($g['gambar']) ?>" alt="<?= esc($g['judul'] ?? 'Galeri') ?>" loading="lazy">
                        <?php if (! empty($g['judul'])): ?>
                            <span class="gallery-caption"><?= esc($g['judul']) ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($galeri_total > $galeri_limit_initial): ?>
                <div class="gallery-load-more-wrap">
                    <button type="button" class="btn btn-outline gallery-load-more" id="galleryLoadMore"
                        aria-controls="galleryGrid">
                        <i class="fa-solid fa-images"></i>
                        Muat Lebih
                        <span class="gallery-load-more-count">
                            (<?= (int) ($galeri_total - $galeri_limit_initial) ?> foto)
                        </span>
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<div id="lightbox" class="lightbox" hidden>
    <button type="button" class="lightbox-close" aria-label="Tutup">&times;</button>
    <img src="" alt="">
</div>

<?= $this->endSection() ?>
