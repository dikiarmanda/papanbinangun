<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin') ?> — Panel Admin</title>
    <link rel="icon" href="<?= brand_favicon() ?>" type="image/svg+xml">
    <link rel="icon" href="<?= brand_favicon('png') ?>" type="image/png" sizes="32x32">
    <link rel="stylesheet" href="<?= vendor_url('fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor_url('select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor_url('dropify/css/dropify.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
    <div class="admin-overlay" id="adminOverlay" hidden></div>

    <div class="admin-wrap">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <strong><i class="fa-solid fa-compass"></i> Panel Admin</strong>
                <small><?= esc(pengaturan()['nama_desa'] ?? 'Wisata Binangun') ?></small>
            </div>
            <nav class="sidebar-nav" id="adminNav">
                <a href="<?= site_url('admin/dashboard') ?>"
                    class="<?= uri_string() === 'admin/dashboard' || uri_string() === 'admin' ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="<?= site_url('admin/artikel') ?>"
                    class="<?= str_starts_with(uri_string(), 'admin/artikel') ? 'active' : '' ?>">
                    <i class="fa-solid fa-newspaper"></i> Artikel
                </a>
                <a href="<?= site_url('admin/kategori') ?>"
                    class="<?= str_starts_with(uri_string(), 'admin/kategori') ? 'active' : '' ?>">
                    <i class="fa-solid fa-tags"></i> Kategori
                </a>
                <a href="<?= site_url('admin/wisata') ?>"
                    class="<?= str_starts_with(uri_string(), 'admin/wisata') ? 'active' : '' ?>">
                    <i class="fa-solid fa-mountain-sun"></i> Wisata
                </a>
                <a href="<?= site_url('admin/galeri') ?>"
                    class="<?= str_starts_with(uri_string(), 'admin/galeri') ? 'active' : '' ?>">
                    <i class="fa-solid fa-images"></i> Galeri
                </a>
                <a href="<?= site_url('admin/banner') ?>"
                    class="<?= str_starts_with(uri_string(), 'admin/banner') ? 'active' : '' ?>">
                    <i class="fa-solid fa-panorama"></i> Banner Beranda
                </a>
                <a href="<?= site_url('admin/users') ?>"
                    class="<?= str_starts_with(uri_string(), 'admin/users') ? 'active' : '' ?>">
                    <i class="fa-solid fa-users-gear"></i> Akun Admin
                </a>
                <a href="<?= site_url('admin/pengaturan') ?>"
                    class="<?= str_starts_with(uri_string(), 'admin/pengaturan') ? 'active' : '' ?>">
                    <i class="fa-solid fa-gear"></i> Pengaturan
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="admin-user"><i class="fa-solid fa-user"></i> <?= esc(session('admin_nama')) ?></div>
                <div class="admin-role"><?= esc(session('admin_role')) ?></div>
                <a href="<?= site_url('admin/logout') ?>" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
                <a href="<?= site_url('/') ?>" class="btn-site" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Situs
                </a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-topbar">
                <button type="button" class="admin-nav-toggle" id="adminNavToggle" aria-label="Buka menu"
                    aria-expanded="false" aria-controls="adminSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <strong><?= esc(pengaturan()['nama_desa'] ?? 'Wisata Binangun') ?></strong>
            </div>

            <header class="admin-header">
                <div>
                    <p class="admin-breadcrumb">Panel Admin</p>
                    <h1><?= esc($title ?? 'Dashboard') ?></h1>
                </div>
                <div class="admin-header-actions">
                    <a href="<?= site_url('/') ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Situs
                    </a>
                </div>
            </header>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
    <script src="<?= vendor_url('jquery/jquery.min.js') ?>"></script>
    <script src="<?= vendor_url('select2/js/select2.min.js') ?>"></script>
    <script src="<?= vendor_url('dropify/js/dropify.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/lexical-editor.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>