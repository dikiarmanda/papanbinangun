<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method tidak diizinkan', 405);
}

$input = getJsonInput();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    jsonResponse(false, null, 'Email dan password wajib diisi', 400);
}

$conn = getConnection();
$stmt = $conn->prepare('SELECT id, nama, email, no_hp, password FROM pelanggan WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, null, 'Email atau password salah', 401);
}

$user = $result->fetch_assoc();
if (!password_verify($password, $user['password'])) {
    jsonResponse(false, null, 'Email atau password salah', 401);
}

unset($user['password']);
jsonResponse(true, $user, 'Login berhasil');
