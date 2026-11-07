<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method tidak diizinkan', 405);
}

$input = getJsonInput();
$nama = trim($input['nama'] ?? '');
$email = trim($input['email'] ?? '');
$no_hp = trim($input['no_hp'] ?? '');
$password = $input['password'] ?? '';

if ($nama === '' || $email === '' || $no_hp === '' || $password === '') {
    jsonResponse(false, null, 'Semua field wajib diisi', 400);
}
if (strlen($password) < 6) {
    jsonResponse(false, null, 'Password minimal 6 karakter', 400);
}

$conn = getConnection();

$check = $conn->prepare('SELECT id FROM pelanggan WHERE email = ?');
$check->bind_param('s', $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    jsonResponse(false, null, 'Email sudah terdaftar', 409);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO pelanggan (nama, email, no_hp, password) VALUES (?,?,?,?)');
$stmt->bind_param('ssss', $nama, $email, $no_hp, $hash);

if ($stmt->execute()) {
    jsonResponse(true, ['id' => $stmt->insert_id, 'nama' => $nama, 'email' => $email], 'Registrasi berhasil');
} else {
    jsonResponse(false, null, 'Gagal registrasi: ' . $stmt->error, 500);
}
