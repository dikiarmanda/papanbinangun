<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="stamp">Berita Desa</span>
        <h1>Artikel & Berita</h1>
        <p>Cerita, tips, dan kabar terkini dari desa</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($artikel)): ?>
            <p class="text-center text-muted">Belum ada artikel yang dipublikasikan.</p>
        <?php else: ?>
            <div class="article-grid">
                <?php foreach ($artikel as $i => $a): ?>
                    <article class="article-card">
                        <a href="<?= site_url('artikel/' . $a['slug']) ?>" class="card-media">
                            <div class="article-thumb">
                                <img src="<?= upload_url($a['gambar_cover'], 'artikel', $i) ?>" alt="<?= esc($a['judul']) ?>"
                                    loading="lazy">
                            </div>
                        </a>
                        <div class="article-body">
                            <?php if (!empty($a['kategori_nama'])): ?>
                                <span class="badge-gold"><?= esc($a['kategori_nama']) ?></span>
                            <?php endif; ?>
                            <h3><a href="<?= site_url('artikel/' . $a['slug']) ?>"><?= esc($a['judul']) ?></a></h3>
                            <p><?= esc($a['ringkasan'] ?: mb_substr(strip_tags($a['konten']), 0, 120) . '...') ?></p>
                            <time><i class="fa-regular fa-calendar"></i> <?= format_tanggal($a['published_at']) ?></time>
                            <a href="<?= site_url('artikel/' . $a['slug']) ?>" class="btn btn-outline btn-card">
                                <i class="fa-solid fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?= site_url('artikel?page=' . $i) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>