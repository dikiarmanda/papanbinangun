<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$heroImage = unsplash_dummy('wisata', 2);
$storyImage = unsplash_dummy('wisata', 0);
$features = [
    [
        'icon' => 'fa-mountain-sun',
        'title' => 'Wisata Alam',
        'desc' => 'Pemandangan hijau, trekking ringan, dan spot foto yang memukau sepanjang hari.',
    ],
    [
        'icon' => 'fa-masks-theater',
        'title' => 'Budaya & Tradisi',
        'desc' => 'Upacara adat, kesenian lokal, dan festival yang masih hidup di tengah masyarakat.',
    ],
    [
        'icon' => 'fa-utensils',
        'title' => 'Kuliner',
        'desc' => 'Hidangan khas desa dari bahan lokal — autentik, hangat, dan penuh cita rasa.',
    ],
    [
        'icon' => 'fa-store',
        'title' => 'Kerajinan & UMKM',
        'desc' => 'Produk tangan warga yang unik, berkualitas, dan siap dibawa pulang sebagai oleh-oleh.',
    ],
];
?>

<section class="page-hero page-hero-image about-hero" style="--hero-img: url('<?= $heroImage ?>')">
    <div class="container">
        <span class="stamp"><i class="fa-solid fa-leaf"></i> Profil Desa</span>
        <h1>Tentang <?= esc($pengaturan['nama_desa']) ?></h1>
        <p><?= esc($pengaturan['tagline']) ?></p>
    </div>
</section>

<section class="section about-intro">
    <div class="container">
        <div class="about-intro-card">
            <div class="about-intro-icon" aria-hidden="true">
                <i class="fa-solid fa-compass"></i>
            </div>
            <div class="about-intro-text content-html"><?= $pengaturan['deskripsi_singkat'] ?></div>
            <div class="about-highlights">
                <span><i class="fa-solid fa-tree"></i> Alam Asri</span>
                <span><i class="fa-solid fa-people-group"></i> Keramahan Warga</span>
                <span><i class="fa-solid fa-hand-holding-heart"></i> Budaya Hidup</span>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="about-story">
            <figure class="about-story-media">
                <div class="about-photo-frame">
                    <img src="<?= $storyImage ?>" alt="Suasana pedesaan Wisata Binangun" loading="lazy">
                </div>
                <figcaption class="about-photo-caption">
                    <i class="fa-solid fa-camera"></i> Keindahan alam yang masih asri
                </figcaption>
            </figure>
            <div class="about-story-content">
                <span class="ornament"><i class="fa-solid fa-book-open"></i></span>
                <h2>Sejarah Singkat</h2>
                <p>
                    <?= esc($pengaturan['nama_desa']) ?> adalah desa yang kaya akan warisan alam dan budaya.
                    Dari generasi ke generasi, masyarakat menjaga tradisi, keramahan, dan kearifan lokal
                    yang menjadi daya tarik utama bagi para wisatawan yang mencari pengalaman otentik.
                </p>
                <p>
                    Nama Binangun identik dengan keindahan alam yang masih asri, kerajinan tangan warga,
                    serta kuliner tradisional yang diwariskan turun-temurun. Setiap sudut desa menyimpan
                    cerita — dari sawah yang menghijau, sungai yang jernih, hingga rumah-rumah adat
                    yang masih berdiri kokoh.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <blockquote class="about-vision">
            <span class="about-vision-icon" aria-hidden="true"><i class="fa-solid fa-quote-left"></i></span>
            <h2>Visi <?= esc($pengaturan['nama_desa']) ?></h2>
            <p>
                Menjadi destinasi wisata pedesaan yang berkelanjutan, memberdayakan masyarakat lokal,
                dan memperkenalkan kekayaan budaya Indonesia kepada dunia — tanpa mengorbankan
                keaslian dan kelestarian lingkungan.
            </p>
        </blockquote>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <span class="ornament"><i class="fa-solid fa-star"></i></span>
            <h2>Potensi Unggulan</h2>
            <p>Keunggulan yang membuat kunjungan Anda ke Binangun tak terlupakan</p>
        </div>
        <div class="about-features">
            <?php foreach ($features as $f): ?>
                <article class="about-feature-card">
                    <span class="about-feature-icon"><i class="fa-solid <?= esc($f['icon']) ?>"></i></span>
                    <h3><?= esc($f['title']) ?></h3>
                    <p><?= esc($f['desc']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section about-cta">
    <div class="container">
        <div class="about-cta-box">
            <h2>Siap Menjelajahi Binangun?</h2>
            <p>Temukan destinasi wisata, atau hubungi kami untuk informasi kunjungan dan paket perjalanan.</p>
            <div class="about-cta-actions">
                <a href="<?= site_url('wisata') ?>" class="btn btn-primary">
                    <i class="fa-solid fa-compass"></i> Jelajahi Destinasi
                </a>
                <a href="<?= site_url('kontak') ?>" class="btn btn-outline">
                    <i class="fa-solid fa-envelope"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>