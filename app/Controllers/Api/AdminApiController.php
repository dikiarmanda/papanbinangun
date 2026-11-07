<?php

namespace App\Controllers\Api;

use App\Libraries\ActivityLogService;
use App\Libraries\TransaksiService;
use App\Models\AdminModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminApiController extends BaseApiController
{
    protected function authenticateAdmin(array $input): bool
    {
        $password = trim((string) ($input['password'] ?? ''));
        $email    = trim((string) ($input['email'] ?? ''));

        if ($password === '') {
            return false;
        }

        $adminModel = new AdminModel();

        if ($email !== '') {
            $user = $adminModel->findByEmail($email);
            if ($user && $user['status'] === 'aktif' && password_verify($password, $user['password'])) {
                return true;
            }
            return false;
        }

        // Cek jika password cocok dengan user admin/operator aktif manapun
        $users = $adminModel->where('status', 'aktif')->findAll();
        foreach ($users as $u) {
            if (password_verify($password, $u['password'])) {
                return true;
            }
        }

        // Fallback backward-compatibility untuk app Flutter lama
        if (defined('ADMIN_PASSWORD') && hash_equals((string) ADMIN_PASSWORD, $password)) {
            return true;
        }

        if (hash_equals('Warisanrasa14', $password)) {
            return true;
        }

        return false;
    }

    public function login(): ResponseInterface
    {
        $input = $this->getJsonInput();
        if (!$this->authenticateAdmin($input)) {
            return $this->respondJson(false, null, 'Password admin salah', 401);
        }

        return $this->respondJson(true, null, 'Login admin berhasil');
    }

    public function list(): ResponseInterface
    {
        $input = $this->request->getMethod() === 'POST' ? $this->getJsonInput() : $this->request->getGet();
        if (!$this->authenticateAdmin($input)) {
            return $this->respondJson(false, null, 'Password admin salah', 401);
        }

        $tab = ($input['tab'] ?? 'homestay') === 'belanja' ? 'belanja' : 'homestay';

        if ($tab === 'homestay') {
            $db = TransaksiService::getReservasiDb();
            $rows = $db->table('bookings b')
                ->select('b.*, h.nama AS nama_homestay')
                ->join('homestays h', 'h.id = b.homestay_id', 'left')
                ->orderBy('b.id', 'DESC')
                ->limit(200)
                ->get()
                ->getResultArray();

            $out = [];
            foreach ($rows as $row) {
                [$alamat, $catatan, $metodeBayar] = TransaksiService::parseBookingCatatanLengkap($row['catatan']);
                $out[] = [
                    'id'                => (int) $row['id'],
                    'kode_booking'      => $row['kode_booking'],
                    'nama_homestay'     => $row['nama_homestay'],
                    'nama_tamu'         => $row['nama_tamu'],
                    'whatsapp'          => $row['whatsapp'],
                    'alamat'            => $alamat,
                    'catatan'           => $catatan,
                    'metode_pembayaran' => $metodeBayar,
                    'bukti_bayar_url'   => TransaksiService::buktiBayarUrl('booking', $row['kode_booking']),
                    'checkin'           => $row['checkin'],
                    'checkout'          => $row['checkout'],
                    'jumlah_tamu'       => (int) $row['jumlah_tamu'],
                    'malam'             => (int) $row['malam'],
                    'total'             => (float) $row['total'],
                    'status'            => $row['status'],
                ];
            }

            return $this->respondJson(true, $out, 'Daftar booking homestay');
        }

        $db = TransaksiService::getLayananDb();
        $rows = $db->query("SELECT o.*, p.nama AS nama_pemesan, p.no_hp AS no_hp_pemesan,
                GROUP_CONCAT(CONCAT(oi.nama_produk, ' x', oi.jumlah) SEPARATOR ', ') AS ringkasan_item
            FROM `order` o
            LEFT JOIN pelanggan p ON p.id = o.pelanggan_id
            LEFT JOIN order_items oi ON oi.order_id = o.id
            GROUP BY o.id
            ORDER BY o.id DESC
            LIMIT 200")->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            [$alamatBersih, $metodeBayar] = TransaksiService::parseAlamatPembayaran($row['alamat_kirim']);
            $out[] = [
                'id'                => (int) $row['id'],
                'kode_order'        => $row['kode_order'],
                'nama_pemesan'      => $row['nama_pemesan'],
                'no_hp_pemesan'     => $row['no_hp_pemesan'],
                'alamat_kirim'      => $alamatBersih,
                'metode_pembayaran' => $metodeBayar,
                'bukti_bayar_url'   => TransaksiService::buktiBayarUrl('order', $row['kode_order']),
                'ringkasan_item'    => $row['ringkasan_item'],
                'metode_pengiriman' => $row['metode_pengiriman'],
                'total_harga'       => (float) $row['total_harga'],
                'status_pembayaran' => $row['status_pembayaran'],
                'status_order'      => $row['status_order'],
            ];
        }

        return $this->respondJson(true, $out, 'Daftar order belanja');
    }

    public function updateStatus(): ResponseInterface
    {
        $input = $this->getJsonInput();
        if (!$this->authenticateAdmin($input)) {
            return $this->respondJson(false, null, 'Password admin salah', 401);
        }

        $jenis = trim((string) ($input['jenis'] ?? ''));
        $id    = (int) ($input['id'] ?? 0);

        if ($id <= 0) {
            return $this->respondJson(false, null, 'ID tidak valid', 400);
        }

        if ($jenis === 'homestay') {
            $status  = trim((string) ($input['status'] ?? ''));
            $allowed = ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'];
            if (!in_array($status, $allowed, true)) {
                return $this->respondJson(false, null, 'Status booking tidak valid', 400);
            }

            $db = TransaksiService::getReservasiDb();
            $db->table('bookings')->where('id', $id)->update(['status' => $status]);
            return $this->respondJson(true, null, 'Status booking diperbarui');
        }

        if ($jenis === 'belanja') {
            $statusPembayaran = isset($input['status_pembayaran']) && $input['status_pembayaran'] !== null
                ? trim((string) $input['status_pembayaran']) : null;
            $statusOrder = isset($input['status_order']) && $input['status_order'] !== null
                ? trim((string) $input['status_order']) : null;

            // Finding 6.1: ENUM status database yang valid
            $allowedBayar = ['pending', 'paid', 'failed', 'expired'];
            $allowedOrder = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

            $db = TransaksiService::getLayananDb();
            $db->transBegin();

            // Finding 6.6: Rollback stok otomatis saat status_order dibatalkan
            if ($statusOrder === 'dibatalkan') {
                TransaksiService::rollbackOrderStok($db, $id);
            }

            $updateData = [];
            if ($statusPembayaran !== null) {
                if (!in_array($statusPembayaran, $allowedBayar, true)) {
                    $db->transRollback();
                    return $this->respondJson(false, null, 'Status pembayaran tidak valid', 400);
                }
                $updateData['status_pembayaran'] = $statusPembayaran;
            }

            if ($statusOrder !== null) {
                if (!in_array($statusOrder, $allowedOrder, true)) {
                    $db->transRollback();
                    return $this->respondJson(false, null, 'Status order tidak valid', 400);
                }
                $updateData['status_order'] = $statusOrder;
            }

            if (!empty($updateData)) {
                $db->table('`order`')->where('id', $id)->update($updateData);
            }

            $db->transCommit();
            return $this->respondJson(true, null, 'Status order diperbarui');
        }

        return $this->respondJson(false, null, 'Jenis tidak dikenal', 400);
    }

    public function chatList(): ResponseInterface
    {
        $input = $this->getJsonInput();
        if (!$this->authenticateAdmin($input)) {
            return $this->respondJson(false, null, 'Password admin salah', 401);
        }

        // Finding 6.5 & 6.7
        return $this->respondJson(true, TransaksiService::daftarPercakapanChat(), 'Daftar percakapan');
    }

    public function chatDetail(): ResponseInterface
    {
        $input = $this->getJsonInput();
        if (!$this->authenticateAdmin($input)) {
            return $this->respondJson(false, null, 'Password admin salah', 401);
        }

        $noHp = trim((string) ($input['no_hp'] ?? ''));
        if ($noHp === '') {
            return $this->respondJson(false, null, 'Parameter no_hp wajib diisi', 400);
        }

        // Finding 6.5 & 6.7
        $data = TransaksiService::bacaChat($noHp);
        return $this->respondJson(true, $data ?? ['no_hp' => $noHp, 'nama' => '', 'pesan' => []], 'Detail percakapan');
    }

    public function chatBalas(): ResponseInterface
    {
        $input = $this->getJsonInput();
        if (!$this->authenticateAdmin($input)) {
            return $this->respondJson(false, null, 'Password admin salah', 401);
        }

        $noHp  = trim((string) ($input['no_hp'] ?? ''));
        $pesan = trim((string) ($input['pesan'] ?? ''));

        if ($noHp === '' || $pesan === '') {
            return $this->respondJson(false, null, 'Parameter no_hp dan pesan wajib diisi', 400);
        }

        // Finding 6.5 (flock) & 6.7 (public/chat)
        $ok = TransaksiService::simpanPesanChat($noHp, null, 'admin', $pesan);
        if (!$ok) {
            return $this->respondJson(false, null, 'Gagal menyimpan balasan chat', 500);
        }

        return $this->respondJson(true, null, 'Balasan terkirim');
    }
}
