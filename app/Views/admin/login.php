<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — <?= esc(pengaturan()['nama_desa'] ?? 'Wisata Binangun') ?></title>
    <?= $this->include('partials/favicon') ?>
    <link rel="stylesheet" href="<?= vendor_url('fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor_url('sweetalert2/css/sweetalert2.min.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/css/admin.css') ?>">
</head>

<body class="login-body">
    <div class="login-card">
        <div class="login-brand">
            <span class="login-brand-icon"><i class="fa-solid fa-mountain-sun"></i></span>
            <h1>Login Admin</h1>
            <p class="login-sub"><?= esc(pengaturan()['nama_desa'] ?? 'Wisata Binangun') ?></p>
        </div>

        <?= view('partials/swal_flash') ?>

        <form method="post" action="<?= site_url('admin/login') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required autofocus
                        placeholder="admin@example.com">
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>
    </div>
    <script src="<?= vendor_url('sweetalert2/js/sweetalert2.all.min.js') ?>"></script>
    <script src="<?= asset_url('assets/js/swal-helper.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initSwalFlash();
        });
    </script>
</body>

</html>