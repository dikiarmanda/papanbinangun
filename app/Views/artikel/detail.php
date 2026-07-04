<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<article class="section">
    <div class="container prose article-detail">
        <header class="article-header">
            <?php if (!empty($artikel['kategori_nama'])): ?>
                <span class="badge-gold"><?= esc($artikel['kategori_nama']) ?></span>
            <?php endif; ?>
            <h1><?= esc($artikel['judul']) ?></h1>
            <div class="article-meta">
                <span><?= format_tanggal($artikel['published_at']) ?></span>
                <?php if (!empty($artikel['penulis'])): ?>
                    <span>· <?= esc($artikel['penulis']) ?></span>
                <?php endif; ?>
                <span>· <?= (int) $artikel['views'] ?> views</span>
            </div>
        </header>

        <?php if (!empty($artikel['gambar_cover'])): ?>
            <div class="detail-cover">
                <img src="<?= upload_url($artikel['gambar_cover'], 'artikel', 0) ?>" alt="<?= esc($artikel['judul']) ?>">
            </div>
        <?php endif; ?>

        <div class="content-body content-html">
            <?= $artikel['konten'] ?>
        </div>

        <div class="text-center mt-lg">
            <a href="<?= site_url('artikel') ?>" class="btn btn-outline">← Kembali ke Artikel</a>
        </div>

        <?php if (!empty($terkait)): ?>
            <aside class="related-articles mt-lg">
                <h3>Artikel Terkait</h3>
                <div class="article-grid">
                    <?php foreach ($terkait as $i => $t): ?>
                        <article class="article-card">
                            <a href="<?= site_url('artikel/' . $t['slug']) ?>" class="card-media">
                                <div class="article-thumb">
                                    <img src="<?= upload_url($t['gambar_cover'], 'artikel', $i) ?>"
                                        alt="<?= esc($t['judul']) ?>" loading="lazy">
                                </div>
                            </a>
                            <div class="article-body">
                                <h3><a href="<?= site_url('artikel/' . $t['slug']) ?>"><?= esc($t['judul']) ?></a></h3>
                                <time><i class="fa-regular fa-calendar"></i> <?= format_tanggal($t['published_at']) ?></time>
                                <a href="<?= site_url('artikel/' . $t['slug']) ?>" class="btn btn-outline btn-card">
                                    <i class="fa-solid fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </aside>
        <?php endif; ?>
    </div>
</article>

<?= $this->endSection() ?>