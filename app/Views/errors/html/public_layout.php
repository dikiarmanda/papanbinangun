<?php
/**
 * Layout halaman error publik — selaras dengan tema vintage situs.
 *
 * Variabel yang diharapkan:
 * - $statusCode (string)  Kode HTTP, mis. '404'
 * - $pageTitle (string)   Judul tab browser
 * - $headline (string)    Judul utama
 * - $description (string) Pesan (boleh berisi HTML aman)
 * - $icon (string)        Kelas Font Awesome, mis. 'fa-compass'
 * - $detailHtml (string|null) Detail tambahan (dev only)
 */

try {
    $pengaturan = pengaturan();
} catch (Throwable $e) {
    $pengaturan = [
        'nama_desa'          => 'Desa Wisata',
        'tagline'            => '',
        'logo'               => '',
        'deskripsi_singkat'  => '',
        'alamat'             => '',
        'email_kontak'       => '',
        'no_whatsapp'        => '',
        'instagram_url'      => '',
        'facebook_url'       => '',
    ];
}

$statusCode  = $statusCode ?? '404';
$pageTitle   = $pageTitle ?? 'Halaman Tidak Ditemukan';
$headline    = $headline ?? $pageTitle;
$description = $description ?? 'Maaf, terjadi kesalahan.';
$icon        = $icon ?? 'fa-compass';
$detailHtml  = $detailHtml ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($pageTitle) ?> — <?= esc($pengaturan['nama_desa']) ?></title>
    <meta name="description" content="<?= esc(strip_tags($description)) ?>">
    <?= view('partials/favicon') ?>
    <link rel="stylesheet" href="<?= vendor_url('fonts/fonts.css') ?>">
    <link rel="stylesheet" href="<?= vendor_url('fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/css/app.css') ?>">
</head>
<body>
    <?= view('partials/navbar', ['pengaturan' => $pengaturan]) ?>

    <main>
        <section class="page-hero page-hero-error" aria-labelledby="error-headline">
            <div class="container">
                <span class="stamp">Kesalahan <?= esc($statusCode) ?></span>
                <span class="error-code" aria-hidden="true"><?= esc($statusCode) ?></span>
                <h1 id="error-headline"><?= esc($headline) ?></h1>
            </div>
        </section>

        <section class="section error-page">
            <div class="container">
                <div class="error-panel">
                    <div class="error-panel-icon" aria-hidden="true">
                        <i class="fa-solid <?= esc($icon) ?>"></i>
                    </div>
                    <p class="error-description"><?= $description ?></p>

                    <?php if ($detailHtml): ?>
                        <div class="error-detail"><?= $detailHtml ?></div>
                    <?php endif; ?>

                    <div class="error-actions">
                        <a href="<?= site_url('/') ?>" class="btn btn-primary">
                            <i class="fa-solid fa-house"></i> Kembali ke Beranda
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline">
                            <i class="fa-solid fa-arrow-left"></i> Halaman Sebelumnya
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?= view('partials/footer', ['pengaturan' => $pengaturan]) ?>

    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
