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
    <link rel="icon" href="<?= brand_favicon() ?>" type="image/svg+xml">
    <link rel="icon" href="<?= brand_favicon('png') ?>" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="<?= brand_favicon('apple') ?>" sizes="180x180">
    <meta property="og:title" content="<?= esc($pageTitle) ?>">
    <meta property="og:description" content="<?= esc($desc) ?>">
    <meta property="og:image" content="<?= esc($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= esc($ogImage) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
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

    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>