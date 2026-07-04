<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="stamp">Destinasi</span>
        <h1>Wisata Desa</h1>
        <p>Jelajahi pesona alam, budaya, dan keramahan warga</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($wisata)): ?>
            <p class="text-center text-muted">Belum ada destinasi yang dipublikasikan.</p>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($wisata as $i => $w): ?>
                    <article class="polaroid-card">
                        <a href="<?= site_url('wisata/' . $w['slug']) ?>" class="card-media">
                            <div class="polaroid-frame">
                                <img src="<?= upload_url($w['gambar_cover'], 'wisata', $i) ?>" alt="<?= esc($w['nama']) ?>"
                                    loading="lazy">
                            </div>
                        </a>
                        <div class="card-body">
                            <h3><a href="<?= site_url('wisata/' . $w['slug']) ?>"><?= esc($w['nama']) ?></a></h3>
                            <p class="card-excerpt"><?= esc(mb_substr(strip_tags($w['deskripsi']), 0, 100)) ?>...</p>
                            <a href="<?= site_url('wisata/' . $w['slug']) ?>" class="btn btn-outline btn-card">
                                <i class="fa-solid fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>