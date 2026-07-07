<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — <?= esc(pengaturan()['nama_desa'] ?? 'Wisata Binangun') ?></title>
    <link rel="icon" href="<?= brand_favicon() ?>" type="image/svg+xml">
    <link rel="icon" href="<?= brand_favicon('png') ?>" type="image/png" sizes="32x32">
    <link rel="stylesheet" href="<?= vendor_url('fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/css/admin.css') ?>">
</head>

<body class="login-body">
    <div class="login-card">
        <div class="login-brand">
            <span class="login-brand-icon"><i class="fa-solid fa-mountain-sun"></i></span>
            <h1>Login Admin</h1>
            <p class="login-sub"><?= esc(pengaturan()['nama_desa'] ?? 'Wisata Binangun') ?></p>
        </div>

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
</body>

</html>