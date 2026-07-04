<?php $site = $pengaturan ?? pengaturan();
$uri = uri_string(); ?>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= site_url('/') ?>" class="brand">
            <?php if (!empty($site['logo'])): ?>
                <img src="<?= upload_url($site['logo']) ?>" alt="<?= esc($site['nama_desa']) ?>" class="brand-logo">
            <?php endif; ?>
            <span class="brand-text">
                <strong><?= esc($site['nama_desa']) ?></strong>
                <?php if (!empty($site['tagline'])): ?>
                    <small class="brand-tagline"><?= esc($site['tagline']) ?></small>
                <?php endif; ?>
            </span>
        </a>

        <button class="nav-toggle" type="button" aria-label="Menu" id="navToggle">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="site-nav" id="siteNav">
            <a href="<?= site_url('/') ?>" class="<?= $uri === '' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i>
                Beranda</a>
            <a href="<?= site_url('tentang') ?>" class="<?= $uri === 'tentang' ? 'active' : '' ?>"><i
                    class="fa-solid fa-info-circle"></i> Tentang</a>
            <a href="<?= site_url('wisata') ?>" class="<?= str_starts_with($uri, 'wisata') ? 'active' : '' ?>"><i
                    class="fa-solid fa-mountain-sun"></i> Wisata</a>
            <a href="<?= site_url('artikel') ?>" class="<?= str_starts_with($uri, 'artikel') ? 'active' : '' ?>"><i
                    class="fa-solid fa-newspaper"></i> Artikel</a>
            <a href="<?= site_url('galeri') ?>" class="<?= $uri === 'galeri' ? 'active' : '' ?>"><i
                    class="fa-solid fa-images"></i> Galeri</a>
            <a href="<?= site_url('kontak') ?>" class="<?= $uri === 'kontak' ? 'active' : '' ?>"><i
                    class="fa-solid fa-envelope"></i> Kontak</a>
        </nav>
    </div>
</header>