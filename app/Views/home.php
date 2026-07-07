<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero-split" aria-label="Beranda">
    <div class="hero-split-decor" aria-hidden="true">
        <span class="hero-decor hero-decor-pattern"></span>
        <span class="hero-decor hero-decor-medallion"></span>
        <span class="hero-decor hero-decor-corner hero-decor-corner--bl"></span>
        <span class="hero-decor hero-decor-corner hero-decor-corner--tr"></span>
    </div>
    <div class="container hero-split-inner">
        <div class="hero-text">
            <span class="stamp">Wisata Binangun</span>
            <h1><?= esc($pengaturan['nama_desa']) ?></h1>
            <p class="hero-tagline"><?= esc($pengaturan['tagline']) ?></p>
            <div class="hero-actions">
                <a href="<?= site_url('wisata') ?>" class="btn btn-primary"><i class="fa-solid fa-compass"></i> Jelajahi
                    Wisata</a>
                <a href="<?= wa_link($pengaturan['no_whatsapp'], 'Halo, saya ingin bertanya tentang ' . $pengaturan['nama_desa']) ?>"
                    class="btn btn-outline-light" target="_blank" rel="noopener"><i class="fa-solid fa-ticket"></i>
                    Reservasi</a>
            </div>
        </div>

        <div class="hero-banner" id="heroBanner" aria-label="Banner beranda" data-interval="5000">
            <div class="hero-banner-frame">
                <div class="hero-banner-track">
                    <?php foreach ($heroBanners as $i => $banner): ?>
                        <?php $imgUrl = banner_image_url($banner['gambar']); ?>
                        <div class="hero-banner-slide<?= $i === 0 ? ' is-active' : '' ?>" role="group"
                            aria-roledescription="slide"
                            aria-label="<?= ($i + 1) ?> dari <?= count($heroBanners) ?>">
                            <?php if (!empty($banner['link_url'])): ?>
                                <a href="<?= esc($banner['link_url'], 'attr') ?>" class="hero-banner-link" target="_blank"
                                    rel="noopener">
                                    <img src="<?= esc($imgUrl, 'attr') ?>"
                                        alt="<?= esc($banner['judul'] ?: $pengaturan['nama_desa'], 'attr') ?>"
                                        loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
                                </a>
                            <?php else: ?>
                                <img src="<?= esc($imgUrl, 'attr') ?>"
                                    alt="<?= esc($banner['judul'] ?: $pengaturan['nama_desa'], 'attr') ?>"
                                    loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
                            <?php endif; ?>
                            <?php if (!empty($banner['judul'])): ?>
                                <p class="hero-banner-caption"><?= esc($banner['judul']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($heroBanners) > 1): ?>
                    <button type="button" class="hero-banner-btn hero-banner-prev" aria-label="Banner sebelumnya"><i
                            class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="hero-banner-btn hero-banner-next" aria-label="Banner berikutnya"><i
                            class="fa-solid fa-chevron-right"></i></button>
                    <div class="hero-banner-dots" role="tablist" aria-label="Navigasi banner">
                        <?php foreach ($heroBanners as $i => $banner): ?>
                            <button type="button" class="hero-banner-dot<?= $i === 0 ? ' is-active' : '' ?>"
                                data-index="<?= $i ?>" aria-label="Banner <?= $i + 1 ?>"
                                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <a href="#sekilas" class="hero-scroll" aria-label="Gulir ke konten">
        <i class="fa-solid fa-chevron-down"></i>
    </a>
</section>

<section class="section section-intro" id="sekilas">
    <div class="container">
        <div class="intro-card">
            <div class="section-header">
                <span class="ornament"><i class="fa-solid fa-leaf"></i></span>
                <h2>Sekilas Wisata Binangun</h2>
                <div class="content-html"><?= $pengaturan['deskripsi_singkat'] ?></div>
                <a href="<?= site_url('tentang') ?>" class="link-more">Baca selengkapnya <i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <span class="ornament"><i class="fa-solid fa-play"></i></span>
            <h2>Video Profil Wisata Binangun</h2>
            <p>Saksikan potret kehidupan dan pesona alam Wisata Binangun</p>
        </div>
        <div class="video-frame">
            <div class="video-embed">
                <iframe src="https://www.youtube.com/embed/<?= esc($youtubeId, 'attr') ?>?rel=0&modestbranding=1"
                    title="Video profil <?= esc($pengaturan['nama_desa'], 'attr') ?>"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="ornament"><i class="fa-solid fa-mountain-sun"></i></span>
            <h2>Destinasi Unggulan</h2>
            <p>Temukan pesona alam dan budaya Binangun yang menanti untuk dijelajahi</p>
        </div>

        <?php if (empty($wisata)): ?>
            <p class="text-center text-muted">Destinasi wisata segera hadir.</p>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach (array_reverse($wisata) as $i => $w): ?>
                    <article class="polaroid-card">
                        <a href="<?= site_url('wisata/' . $w['slug']) ?>" class="card-media">
                            <div class="polaroid-frame">
                                <img src="<?= upload_url($w['gambar_cover'], 'wisata', $i) ?>" alt="<?= esc($w['nama']) ?>"
                                    loading="lazy">
                            </div>
                        </a>
                        <div class="card-body">
                            <h3><a href="<?= site_url('wisata/' . $w['slug']) ?>"><?= esc($w['nama']) ?></a></h3>
                            <a href="<?= wa_link($pengaturan['no_whatsapp'], 'hai saya mau reservasi untuk ' . $w['nama']) ?>"
                                class="btn btn-outline btn-card" target="_blank" rel="noopener">
                                <i class="fa-solid fa-ticket"></i> Reservasi
                            </a>
                            <a href="<?= site_url('wisata/' . $w['slug']) ?>" class="btn btn-outline btn-card btn-sm">
                                <i class="fa-solid fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-lg">
                <a href="<?= site_url('wisata') ?>" class="btn btn-outline"><i class="fa-solid fa-map"></i> Lihat Semua
                    Destinasi</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <span class="ornament"><i class="fa-solid fa-newspaper"></i></span>
            <h2>Artikel Terbaru</h2>
        </div>

        <?php if (empty($artikel)): ?>
            <p class="text-center text-muted">Belum ada artikel.</p>
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
                            <p><?= esc($a['ringkasan'] ?: mb_substr(strip_tags($a['konten']), 0, 100) . '...') ?></p>
                            <time><i class="fa-regular fa-calendar"></i> <?= format_tanggal($a['published_at']) ?></time>
                            <a href="<?= site_url('artikel/' . $a['slug']) ?>" class="btn btn-outline btn-card">
                                <i class="fa-solid fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-lg">
                <a href="<?= site_url('artikel') ?>" class="btn btn-outline"><i class="fa-solid fa-book-open"></i> Semua
                    Artikel</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="ornament"><i class="fa-solid fa-quote-left"></i></span>
            <h2>Testimoni Pengunjung</h2>
            <p>Cerita hangat dari mereka yang telah merasakan keramahan Binangun</p>
        </div>
        <div class="testimonial-carousel" id="testimonialCarousel" aria-label="Testimoni pengunjung"
            data-interval="6000">
            <div class="testimonial-carousel-viewport">
                <div class="testimonial-carousel-track">
                    <?php foreach ($testimonials as $i => $t): ?>
                        <blockquote class="testimonial-card" role="group" aria-roledescription="slide"
                            aria-label="<?= ($i + 1) ?> dari <?= count($testimonials) ?>">
                            <div class="testimonial-stars" aria-label="Rating <?= $t['rating'] ?> dari 5">
                                <?php for ($s = 0; $s < $t['rating']; $s++): ?>
                                    <i class="fa-solid fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="testimonial-text">“<?= esc($t['teks']) ?>”</p>
                            <footer class="testimonial-author">
                                <img src="<?= esc($t['foto'], 'attr') ?>" alt="<?= esc($t['nama']) ?>" loading="lazy" width="48"
                                    height="48">
                                <div>
                                    <strong><?= esc($t['nama']) ?></strong>
                                </div>
                            </footer>
                        </blockquote>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="button" class="testimonial-carousel-btn testimonial-carousel-prev"
                aria-label="Testimoni sebelumnya">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="testimonial-carousel-btn testimonial-carousel-next"
                aria-label="Testimoni berikutnya">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <div class="testimonial-carousel-dots" role="tablist" aria-label="Navigasi testimoni"></div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <span class="ornament"><i class="fa-solid fa-handshake"></i></span>
            <h2>Supported By</h2>
            <p>Didukung oleh mitra dan lembaga yang peduli kemajuan Wisata Binangun</p>
        </div>
        <div class="supporter-strip">
            <?php foreach ($supporters as $s): ?>
                <a href="<?= esc($s['url'], 'attr') ?>" class="supporter-link" title="<?= esc($s['nama']) ?>"
                    target="_blank" rel="noopener noreferrer">
                    <img src="<?= base_url($s['logo']) ?>" alt="<?= esc($s['nama']) ?>" loading="lazy">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="visit-section" aria-label="Kunjungi Kami">
    <div class="visit-map" aria-hidden="false">
        <?= $mapEmbed ?>
    </div>
    <div class="visit-panel">
        <h2>Kunjungi Kami</h2>
        <p class="visit-subtitle">Temukan keindahan Desa Binangun</p>
        <p class="visit-address">
            <i class="fa-solid fa-location-dot visit-pin" aria-hidden="true"></i>
            <span><?= esc($alamatKunjungan) ?></span>
        </p>
        <a href="<?= $mapsUrl ?>" class="visit-maps-btn" target="_blank" rel="noopener">
            <i class="fa-solid fa-map-location-dot"></i> Lihat di Google Maps
        </a>
    </div>
</section>

<?= $this->endSection() ?>