<?php
session_start();
require_once __DIR__ . '/config_admin.php';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw = $_POST['password'] ?? '';
    if (hash_equals(ADMIN_PASSWORD, $pw)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Password salah. Coba lagi.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin - Papan Binangun</title>
<?php include __DIR__ . '/_style.php'; ?>
</head>
<body>
  <div class="login-wrap">
    <form method="post" class="login-box">
      <h1>Admin Papan Binangun</h1>
      <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
      <input type="password" name="password" placeholder="Password admin" required autofocus>
      <button type="submit">Masuk</button>
    </form>
  </div>
</body>
</html>
