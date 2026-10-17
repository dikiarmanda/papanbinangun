<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · SEWU DATA</title>
    <link rel="stylesheet" href="<?= base_url('sewu-data/css/style.css') ?>">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #f4e9d8;
            font-family: 'Lora', serif;
        }
        .login-box {
            background: #fffcf7;
            padding: 40px;
            border-radius: 12px;
            border: 1px solid #c9a66b;
            box-shadow: 0 8px 30px rgba(74,50,34,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-box h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            color: #4a3222;
            text-align: center;
            margin-bottom: 8px;
        }
        .login-box .sub {
            text-align: center;
            color: #8b5e3c;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            color: #4a3222;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #c9a66b;
            border-radius: 6px;
            background: #f4e9d8;
            font-size: 14px;
            font-family: 'Lora', serif;
        }
        .form-group input:focus {
            outline: none;
            border-color: #c1502e;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #c1502e;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Lora', serif;
        }
        .btn-login:hover {
            background: #a34226;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h1>SEWU DATA</h1>
    <div class="sub">Dashboard Pasar Pring Sewu · Binangun</div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('sewu-data/login') ?>">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn-login">Masuk</button>
    </form>
</div>

</body>
</html>