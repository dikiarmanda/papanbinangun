<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $site = $pengaturan ?? pengaturan();
    $pageTitle = ($title ?? $site['nama_desa']) . ' — ' . $site['nama_desa'];
    $desc = $meta_description ?? strip_tags($site['deskripsi_singkat'] ?: $site['tagline']);
    $ogImage = $meta_image ?? (!empty($site['logo']) ? upload_url($site['logo']) : brand_og_image());
    ?>
    <title><?= esc($pageTitle) ?></title>
    <meta name="description" content="<?= esc($desc) ?>">
    <?= $this->include('partials/favicon') ?>
    <meta property="og:title" content="<?= esc($pageTitle) ?>">
    <meta property="og:description" content="<?= esc($desc) ?>">
    <meta property="og:image" content="<?= esc($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= esc($ogImage) ?>">
    <link rel="stylesheet" href="<?= vendor_url('fonts/fonts.css') ?>">
    <link rel="stylesheet" href="<?= vendor_url('fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/css/app.css') ?>">
</head>

<body>
    <?= $this->include('partials/navbar') ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <?php if (!empty($site['no_whatsapp'])): ?>
        <a href="<?= wa_link($site['no_whatsapp'], 'Halo, saya ingin bertanya tentang wisata ' . $site['nama_desa']) ?>"
            class="wa-float" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
        </a>
    <?php endif; ?>

    <div id="lightbox" class="lightbox" hidden>
        <button type="button" class="lightbox-close" aria-label="Tutup">&times;</button>
        <img src="" alt="">
    </div>

    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>