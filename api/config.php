<?php
// ======================================================
// KONFIGURASI DATABASE — 2 DATABASE TERPISAH
// ======================================================
// Database A: untuk HOMESTAY (website reservasi.papanbinangun.id /
//             booking.papanbinangun.id). Tabel: homestays, bookings.
// Database B: untuk UMKM/CATERING (website layanan.papanbinangun.id).
//             Tabel: produk, kategori_produk, order, order_items,
//             pelanggan, zona_antar_lokal.
//
// Isi username & password di bawah ini dengan milik kamu sendiri.
// Ambil dari hPanel Hostinger > Websites > (pilih website) > Databases
// > Management, atau dari file konfigurasi database yang sudah dipakai
// masing-masing website PHP/CodeIgniter kamu sekarang (biasanya di
// app/Config/Database.php untuk CodeIgniter, atau config.php/koneksi.php
// untuk PHP native).
//
// JANGAN commit file ini ke git publik / tempat yang bisa diakses umum.
// ======================================================

// --- Database HOMESTAY (u714729994_reservasi) ---
define('DB_RESERVASI_HOST', 'localhost');
define('DB_RESERVASI_USER', 'u714729994_reservasi');
define('DB_RESERVASI_PASS', 'Warisanrasa14');
define('DB_RESERVASI_NAME', 'u714729994_reservasi');

// --- Database UMKM/CATERING (u714729994_layanan) ---
define('DB_LAYANAN_HOST', 'localhost');
define('DB_LAYANAN_USER', 'u714729994_layanan');
define('DB_LAYANAN_PASS', 'Warisanrasa14');
define('DB_LAYANAN_NAME', 'u714729994_layanan');

// Opsional: API key sederhana untuk produksi (lihat README bagian keamanan)
// define('API_KEY', 'isi-dengan-key-rahasia-kamu');

/**
 * Ambil koneksi ke salah satu database.
 * @param string $which 'reservasi' (homestay) atau 'layanan' (UMKM/catering)
 */
function getConnection($which = 'layanan')
{
    static $connections = [];

    if (isset($connections[$which])) {
        return $connections[$which];
    }

    if ($which === 'reservasi') {
        $conn = new mysqli(DB_RESERVASI_HOST, DB_RESERVASI_USER, DB_RESERVASI_PASS, DB_RESERVASI_NAME);
    } elseif ($which === 'layanan') {
        $conn = new mysqli(DB_LAYANAN_HOST, DB_LAYANAN_USER, DB_LAYANAN_PASS, DB_LAYANAN_NAME);
    } else {
        jsonResponse(false, null, 'Nama database tidak dikenal: ' . $which, 500);
    }

    if ($conn->connect_error) {
        jsonResponse(false, null, 'Koneksi database (' . $which . ') gagal: ' . $conn->connect_error, 500);
    }
    $conn->set_charset('utf8mb4');
    $connections[$which] = $conn;
    return $conn;
}

function jsonResponse($success, $data = null, $message = '', $httpCode = 200)
{
    http_response_code($httpCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonInput()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// ======================================================
// GUEST CHECKOUT (tanpa login) — UNTUK UMKM/CATERING SAJA
// Dipakai oleh order/create.php (DB layanan) supaya user Flutter tidak
// perlu register/login dulu. Cukup kirim nama_pemesan + no_hp_pemesan,
// lalu fungsi ini akan mencari pelanggan yang sudah pernah pakai no HP
// itu, atau membuat baris `pelanggan` baru ("akun tamu") secara
// otomatis agar relasi pelanggan_id di order tetap konsisten untuk
// admin. (Homestay TIDAK memakai tabel pelanggan — booking langsung
// menyimpan nama_tamu & whatsapp, lihat api/homestay/booking_create.php)
// ======================================================
function findOrCreateGuestPelanggan($conn, $nama, $no_hp)
{
    $nama = trim($nama);
    $no_hp = trim($no_hp);

    $stmt = $conn->prepare('SELECT id, nama FROM pelanggan WHERE no_hp = ? LIMIT 1');
    $stmt->bind_param('s', $no_hp);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        // Nomor HP sudah pernah dipakai pesan sebelumnya -> pakai akun yang sama.
        // Perbarui nama jika pemesan mengetik nama berbeda kali ini.
        if ($nama !== '' && $nama !== $row['nama']) {
            $upd = $conn->prepare('UPDATE pelanggan SET nama = ? WHERE id = ?');
            $upd->bind_param('si', $nama, $row['id']);
            $upd->execute();
        }
        return (int) $row['id'];
    }

    // Belum pernah pesan -> buat akun pelanggan "tamu" otomatis.
    // Email diisi placeholder karena tamu tidak pernah login. Password
    // dikosongkan (NULL) karena kolom pelanggan.password memang nullable.
    $emailGuest = 'tamu_' . preg_replace('/[^0-9]/', '', $no_hp) . '_' . substr(bin2hex(random_bytes(3)), 0, 6) . '@tamu.local';

    $ins = $conn->prepare('INSERT INTO pelanggan (nama, email, no_hp, password) VALUES (?,?,?,NULL)');
    $ins->bind_param('sss', $nama, $emailGuest, $no_hp);
    $ins->execute();
    return $ins->insert_id;
}

// ======================================================
// Helper kode unik untuk booking homestay, misal: BK-20261106-A1B2C9
// ======================================================
function generateKodeBooking()
{
    return 'BK-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
}

// ======================================================
// Helper untuk memisahkan metode pembayaran yang "dititipkan" oleh app
// Flutter di akhir kolom alamat_kirim pada tabel `order` (tabel `order`
// tidak punya kolom metode_pembayaran sendiri, dan tidak ingin mengubah
// struktur database). Formatnya:
//   "<alamat asli>\n[Pembayaran: <label metode>]"
// Dipakai oleh api/order/track.php, api/order/detail.php, dan
// admin/index.php supaya alamat & metode pembayaran tetap bisa
// ditampilkan terpisah seperti field biasa.
// ======================================================
function parseAlamatPembayaran($raw)
{
    $alamat = $raw;
    $metode = null;
    if ($raw !== null && preg_match('/^(.*?)\n\[Pembayaran:\s*(.*?)\]\s*$/s', $raw, $m)) {
        $alamat = trim($m[1]);
        $metode = trim($m[2]);
    }
    return [$alamat, $metode];
}

// ======================================================
// Pecah catatan booking homestay menjadi alamat, catatan, dan metode
// pembayaran. Format baru (3 baris): "Alamat: ...\nCatatan: ...\nPembayaran: ..."
// Tetap kompatibel dengan booking lama (format 2 baris / tanpa format).
// ======================================================
function parseBookingCatatanLengkap($raw)
{
    $alamat = '';
    $catatan = null;
    $metode = null;
    if ($raw !== null && preg_match('/^Alamat:\s*(.*?)\nCatatan:\s*(.*?)\nPembayaran:\s*(.*)$/s', $raw, $m)) {
        $alamat = trim($m[1]);
        $catatan = trim($m[2]) !== '' ? trim($m[2]) : null;
        $metode = trim($m[3]) !== '' ? trim($m[3]) : null;
    } elseif ($raw !== null && preg_match('/^Alamat:\s*(.*?)(?:\nCatatan:\s*(.*))?$/s', $raw, $m)) {
        $alamat = trim($m[1]);
        $catatan = isset($m[2]) && trim($m[2]) !== '' ? trim($m[2]) : null;
    } else {
        $catatan = $raw;
    }
    return [$alamat, $catatan, $metode];
}

// ======================================================
// Cari URL bukti bayar (foto) yang sudah pernah diupload untuk
// order/booking tertentu, berdasarkan kode unik yang sudah ada
// (kode_order / kode_booking) — tanpa kolom DB baru.
// ======================================================
function buktiBayarUrl($jenis, $kode)
{
    $safeKode = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $kode);
    if ($safeKode === '')
        return null;
    $dir = __DIR__ . '/../uploads/bukti_bayar/' . $jenis;
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        if (is_file($dir . '/' . $safeKode . '.' . $ext)) {
            return '/uploads/bukti_bayar/' . $jenis . '/' . $safeKode . '.' . $ext;
        }
    }
    return null;
}

// ======================================================
// Simpan foto bukti bayar (base64 dari app Flutter) ke disk,
// nama file = kode_order/kode_booking supaya tidak perlu kolom DB baru.
// ======================================================
function simpanBuktiBayar($jenis, $kode, $fotoBase64)
{
    $ext = 'jpg';
    if (preg_match('#^data:image/(png|jpe?g|webp);base64,#i', $fotoBase64, $m)) {
        $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
        $fotoBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $fotoBase64);
    }
    $data = base64_decode($fotoBase64, true);
    if ($data === false || strlen($data) < 100)
        return [false, 'Data foto tidak valid'];
    if (strlen($data) > 6 * 1024 * 1024)
        return [false, 'Ukuran foto terlalu besar (maks 6MB)'];
    $safeKode = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $kode);
    if ($safeKode === '')
        return [false, 'Kode tidak valid'];
    $dir = __DIR__ . '/../uploads/bukti_bayar/' . $jenis;
    if (!is_dir($dir))
        mkdir($dir, 0755, true);
    $filename = $safeKode . '.' . $ext;
    file_put_contents($dir . '/' . $filename, $data);
    return [true, '/uploads/bukti_bayar/' . $jenis . '/' . $filename];
}

// ======================================================
// Fitur Chat Admin (tanpa tabel DB baru) — setiap percakapan disimpan
// sebagai 1 file JSON di uploads/chat/<no_hp_aman>.json, formatnya:
//   {"no_hp":"...", "nama":"...", "pesan":[{"dari":"user|admin","teks":"...","waktu":"..."}]}
// ======================================================
function chatSafeNoHp($noHp)
{
    return preg_replace('/[^0-9]/', '', (string) $noHp);
}

function chatFilePath($noHp)
{
    $safe = chatSafeNoHp($noHp);
    return __DIR__ . '/../uploads/chat/' . $safe . '.json';
}

function bacaChat($noHp)
{
    $path = chatFilePath($noHp);
    if (!is_file($path)) {
        return null;
    }
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : null;
}

function simpanPesanChat($noHp, $nama, $dari, $pesan)
{
    $safe = chatSafeNoHp($noHp);
    if ($safe === '')
        return false;
    $dir = __DIR__ . '/../uploads/chat';
    if (!is_dir($dir))
        mkdir($dir, 0755, true);
    $path = $dir . '/' . $safe . '.json';

    $data = bacaChat($noHp);
    if ($data === null) {
        $data = ['no_hp' => $safe, 'nama' => $nama, 'pesan' => []];
    }
    if ($nama !== null && trim((string) $nama) !== '') {
        $data['nama'] = trim($nama);
    }
    $data['pesan'][] = [
        'dari' => $dari, // 'user' | 'admin'
        'teks' => trim((string) $pesan),
        'waktu' => date('Y-m-d H:i:s'),
    ];
    file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    return true;
}

function daftarPercakapanChat()
{
    $dir = __DIR__ . '/../uploads/chat';
    if (!is_dir($dir))
        return [];
    $result = [];
    foreach (glob($dir . '/*.json') as $file) {
        $data = json_decode(file_get_contents($file), true);
        if (!is_array($data) || empty($data['pesan']))
            continue;
        $last = end($data['pesan']);
        $result[] = [
            'no_hp' => $data['no_hp'] ?? basename($file, '.json'),
            'nama' => $data['nama'] ?? '-',
            'pesan_terakhir' => $last['teks'] ?? '',
            'dari_terakhir' => $last['dari'] ?? '',
            'waktu_terakhir' => $last['waktu'] ?? '',
            'jumlah_pesan' => count($data['pesan']),
        ];
    }
    usort($result, fn($a, $b) => strcmp($b['waktu_terakhir'], $a['waktu_terakhir']));
    return $result;
}


// CORS: izinkan diakses dari app lain (termasuk Flutter mobile/web)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
