<?php

namespace App\Libraries;

use DateTime;
use Exception;

class TransaksiService
{
    public static function getReservasiDb()
    {
        return \Config\Database::connect('reservasi');
    }

    public static function getLayananDb()
    {
        return \Config\Database::connect('layanan');
    }

    public static function generateKodeBooking(): string
    {
        return 'BK-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }

    public static function generateKodeOrder(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }

    public static function parseAlamatPembayaran(?string $raw): array
    {
        $alamat = $raw ?? '';
        $metode = null;
        if ($raw !== null && preg_match('/^(.*?)\n\[Pembayaran:\s*(.*?)\]\s*$/s', $raw, $m)) {
            $alamat = trim($m[1]);
            $metode = trim($m[2]);
        }
        return [$alamat, $metode];
    }

    public static function parseBookingCatatanLengkap(?string $raw): array
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

    public static function buktiBayarUrl(string $jenis, string $kode): ?string
    {
        $safeKode = preg_replace('/[^A-Za-z0-9_-]/', '', $kode);
        if ($safeKode === '') {
            return null;
        }

        $fcpath = defined('FCPATH') ? FCPATH : (dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
        $rootpath = defined('ROOTPATH') ? ROOTPATH : (dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);

        $dirs = [
            $fcpath . 'uploads/bukti_bayar/' . $jenis,
            $rootpath . 'uploads/bukti_bayar/' . $jenis,
        ];

        foreach ($dirs as $dir) {
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                if (is_file($dir . '/' . $safeKode . '.' . $ext)) {
                    return function_exists('base_url') ? base_url('uploads/bukti_bayar/' . $jenis . '/' . $safeKode . '.' . $ext) : ('/uploads/bukti_bayar/' . $jenis . '/' . $safeKode . '.' . $ext);
                }
            }
        }
        return null;
    }

    public static function simpanBuktiBayar(string $jenis, string $kode, string $fotoBase64): array
    {
        $ext = 'jpg';
        if (preg_match('#^data:image/(png|jpe?g|webp);base64,#i', $fotoBase64, $m)) {
            $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
            $fotoBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $fotoBase64);
        }

        $data = base64_decode($fotoBase64, true);
        if ($data === false || strlen($data) < 100) {
            return [false, 'Data foto tidak valid'];
        }
        if (strlen($data) > 6 * 1024 * 1024) {
            return [false, 'Ukuran foto terlalu besar (maks 6MB)'];
        }

        $safeKode = preg_replace('/[^A-Za-z0-9_-]/', '', $kode);
        if ($safeKode === '') {
            return [false, 'Kode tidak valid'];
        }

        $fcpath = defined('FCPATH') ? FCPATH : (dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
        $dir = $fcpath . 'uploads/bukti_bayar/' . $jenis;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $safeKode . '.' . $ext;
        file_put_contents($dir . '/' . $filename, $data);

        $url = function_exists('base_url') ? base_url('uploads/bukti_bayar/' . $jenis . '/' . $filename) : ('/uploads/bukti_bayar/' . $jenis . '/' . $filename);
        return [true, $url];
    }

    public static function findOrCreateGuestPelanggan($db, string $nama, string $noHp): int
    {
        $nama = trim($nama);
        $noHp = trim($noHp);

        $row = $db->table('pelanggan')
            ->select('id, nama')
            ->where('no_hp', $noHp)
            ->get()
            ->getRowArray();

        if ($row) {
            if ($nama !== '' && $nama !== $row['nama']) {
                $db->table('pelanggan')
                    ->where('id', (int) $row['id'])
                    ->update(['nama' => $nama]);
            }
            return (int) $row['id'];
        }

        $emailGuest = 'tamu_' . preg_replace('/[^0-9]/', '', $noHp) . '_' . substr(bin2hex(random_bytes(3)), 0, 6) . '@tamu.local';
        $db->table('pelanggan')->insert([
            'nama'     => $nama,
            'email'    => $emailGuest,
            'no_hp'    => $noHp,
            'password' => null,
        ]);

        return (int) $db->insertID();
    }

    public static function rollbackOrderStok($db, int $orderId): bool
    {
        $order = $db->table('`order`')->where('id', $orderId)->get()->getRowArray();
        if (!$order || (int) ($order['stok_locked'] ?? 0) === 0) {
            return false;
        }

        $items = $db->table('order_items')->where('order_id', $orderId)->get()->getResultArray();
        foreach ($items as $item) {
            $produkId = (int) ($item['produk_id'] ?? 0);
            $qty = (int) ($item['jumlah'] ?? 0);
            if ($produkId > 0 && $qty > 0) {
                $db->table('produk')
                    ->where('id', $produkId)
                    ->set('stok', 'stok + ' . $qty, false)
                    ->update();
            }
        }

        $db->table('`order`')->where('id', $orderId)->update(['stok_locked' => 0]);
        return true;
    }

    public static function getChatDir(): string
    {
        $fcpath = defined('FCPATH') ? FCPATH : (dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
        $dir = rtrim($fcpath, '/\\') . DIRECTORY_SEPARATOR . 'chat';
        self::ensureChatDir($dir);
        return $dir;
    }

    public static function ensureChatDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $htaccess = $dir . '/.htaccess';
        if (!is_file($htaccess)) {
            $content = "# Proteksi privasi percakapan chat\n"
                     . "<FilesMatch \"\\.json$\">\n"
                     . "    Order Deny,Allow\n"
                     . "    Deny from all\n"
                     . "</FilesMatch>\n"
                     . "Options -Indexes\n";
            file_put_contents($htaccess, $content);
        }

        $index = $dir . '/index.html';
        if (!is_file($index)) {
            file_put_contents($index, '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden.</h1></body></html>');
        }
    }

    public static function chatSafeNoHp(string $noHp): string
    {
        return preg_replace('/[^0-9]/', '', $noHp);
    }

    public static function chatFilePath(string $noHp): string
    {
        $safe = self::chatSafeNoHp($noHp);
        return self::getChatDir() . '/' . $safe . '.json';
    }

    public static function bacaChat(string $noHp): ?array
    {
        $safe = self::chatSafeNoHp($noHp);
        $path = self::chatFilePath($safe);

        if (!is_file($path)) {
            $rootpath = defined('ROOTPATH') ? ROOTPATH : (dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
            $legacyPath = rtrim($rootpath, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $safe . '.json';
            if (is_file($legacyPath)) {
                $path = $legacyPath;
            } else {
                return null;
            }
        }

        $fp = fopen($path, 'r');
        if (!$fp) {
            return null;
        }

        flock($fp, LOCK_SH);
        $contents = stream_get_contents($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        $data = json_decode($contents, true);
        return is_array($data) ? $data : null;
    }

    public static function simpanPesanChat(string $noHp, ?string $nama, string $dari, string $pesan): bool
    {
        $safe = self::chatSafeNoHp($noHp);
        if ($safe === '') {
            return false;
        }

        $path = self::chatFilePath($safe);

        $fp = fopen($path, 'c+');
        if (!$fp) {
            return false;
        }

        if (flock($fp, LOCK_EX)) {
            $raw = stream_get_contents($fp);
            $data = $raw ? json_decode($raw, true) : null;

            if (!is_array($data)) {
                $data = ['no_hp' => $safe, 'nama' => $nama ?? '', 'pesan' => []];
            }

            if ($nama !== null && trim($nama) !== '') {
                $data['nama'] = trim($nama);
            }

            $data['pesan'][] = [
                'dari'  => $dari,
                'teks'  => trim($pesan),
                'waktu' => date('Y-m-d H:i:s'),
            ];

            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            return true;
        }

        fclose($fp);
        return false;
    }

    public static function daftarPercakapanChat(): array
    {
        $dir = self::getChatDir();
        $files = glob($dir . '/*.json') ?: [];

        $rootpath = defined('ROOTPATH') ? ROOTPATH : (dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
        $legacyDir = rtrim($rootpath, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'chat';
        if (is_dir($legacyDir)) {
            $legacyFiles = glob($legacyDir . '/*.json') ?: [];
            $files = array_unique(array_merge($files, $legacyFiles));
        }

        if (empty($files)) {
            return [];
        }

        $result = [];
        foreach ($files as $file) {
            $raw = file_get_contents($file);
            $data = json_decode($raw, true);
            if (!is_array($data) || empty($data['pesan'])) {
                continue;
            }
            $last = end($data['pesan']);
            $noHp = $data['no_hp'] ?? basename($file, '.json');
            $result[$noHp] = [
                'no_hp'          => $noHp,
                'nama'           => $data['nama'] ?? '-',
                'pesan_terakhir' => $last['teks'] ?? '',
                'dari_terakhir'  => $last['dari'] ?? '',
                'waktu_terakhir' => $last['waktu'] ?? '',
                'jumlah_pesan'   => count($data['pesan']),
            ];
        }

        $list = array_values($result);
        usort($list, fn($a, $b) => strcmp($b['waktu_terakhir'], $a['waktu_terakhir']));
        return $list;
    }
}
