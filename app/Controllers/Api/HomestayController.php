<?php

namespace App\Controllers\Api;

use App\Libraries\TransaksiService;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;
use Exception;

class HomestayController extends BaseApiController
{
    public function list(): ResponseInterface
    {
        $db = TransaksiService::getReservasiDb();
        $rows = $db->table('homestays')
            ->select('id, nama, lokasi, deskripsi, harga, kapasitas, fasilitas, gambar, status')
            ->where('status', 'aktif')
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();

        $data = [];
        foreach ($rows as $row) {
            $row['id']        = (int) $row['id'];
            $row['harga']     = (float) $row['harga'];
            $row['kapasitas'] = (int) $row['kapasitas'];
            $data[]           = $row;
        }

        return $this->respondJson(true, $data, 'Daftar homestay');
    }

    public function detail(): ResponseInterface
    {
        $id = (int) $this->request->getGet('id');
        if ($id <= 0) {
            return $this->respondJson(false, null, 'id wajib diisi', 400);
        }

        $db = TransaksiService::getReservasiDb();
        $row = $db->table('homestays')
            ->select('id, nama, lokasi, deskripsi, harga, kapasitas, fasilitas, gambar, status')
            ->where('id', $id)
            ->where('status', 'aktif')
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->respondJson(false, null, 'Homestay tidak ditemukan', 404);
        }

        $row['id']        = (int) $row['id'];
        $row['harga']     = (float) $row['harga'];
        $row['kapasitas'] = (int) $row['kapasitas'];

        return $this->respondJson(true, $row, 'Detail homestay');
    }

    public function bookingCreate(): ResponseInterface
    {
        $input = $this->getJsonInput();

        $homestayId       = isset($input['homestay_id']) ? (int) $input['homestay_id'] : 0;
        $checkin          = $input['checkin'] ?? null;
        $checkout         = $input['checkout'] ?? null;
        $jumlahTamu       = isset($input['jumlah_tamu']) ? (int) $input['jumlah_tamu'] : 1;
        $catatan          = $input['catatan'] ?? null;
        $metodePembayaran = trim((string) ($input['metode_pembayaran'] ?? 'Tunai (Cash)'));

        $namaTamu = trim((string) ($input['nama_tamu'] ?? ''));
        $whatsapp = trim((string) ($input['whatsapp'] ?? ''));
        $alamat   = trim((string) ($input['alamat'] ?? ''));

        if ($homestayId <= 0) {
            return $this->respondJson(false, null, 'homestay_id wajib diisi', 400);
        }
        if ($namaTamu === '' || $whatsapp === '' || $alamat === '') {
            return $this->respondJson(false, null, 'Nama, nomor WhatsApp, dan alamat wajib diisi', 400);
        }
        if (!$checkin || !$checkout) {
            return $this->respondJson(false, null, 'Tanggal check-in dan check-out wajib diisi', 400);
        }

        $d1 = DateTime::createFromFormat('Y-m-d', $checkin);
        $d2 = DateTime::createFromFormat('Y-m-d', $checkout);
        if (!$d1 || !$d2) {
            return $this->respondJson(false, null, 'Format tanggal tidak valid, gunakan YYYY-MM-DD', 400);
        }
        $malam = (int) $d2->diff($d1)->days;
        if ($malam < 1) {
            return $this->respondJson(false, null, 'Tanggal check-out harus setelah check-in', 400);
        }
        if ($jumlahTamu < 1) {
            $jumlahTamu = 1;
        }

        $db = TransaksiService::getReservasiDb();
        $db->transBegin();

        try {
            $homestay = $db->query("SELECT * FROM homestays WHERE id = ? AND status = 'aktif' FOR UPDATE", [$homestayId])->getRowArray();
            if (!$homestay) {
                throw new Exception('Homestay tidak ditemukan atau tidak tersedia');
            }

            if ($jumlahTamu > (int) $homestay['kapasitas']) {
                throw new Exception('Jumlah tamu melebihi kapasitas homestay (maks ' . $homestay['kapasitas'] . ' orang)');
            }

            $hargaPerMalam = (float) $homestay['harga'];
            $total         = $hargaPerMalam * $malam;
            $kodeBooking   = TransaksiService::generateKodeBooking();

            $catatanGabungan = 'Alamat: ' . $alamat
                . "\nCatatan: " . ($catatan !== null && trim($catatan) !== '' ? trim($catatan) : '')
                . "\nPembayaran: " . $metodePembayaran;

            $db->table('bookings')->insert([
                'kode_booking'    => $kodeBooking,
                'homestay_id'     => $homestayId,
                'nama_tamu'       => $namaTamu,
                'whatsapp'        => $whatsapp,
                'checkin'         => $checkin,
                'checkout'        => $checkout,
                'jumlah_tamu'     => $jumlahTamu,
                'malam'           => $malam,
                'harga_per_malam' => $hargaPerMalam,
                'total'           => $total,
                'catatan'         => $catatanGabungan,
                'status'          => 'pending',
            ]);

            $insertId = (int) $db->insertID();
            $db->transCommit();

            return $this->respondJson(true, [
                'id'              => $insertId,
                'kode_booking'    => $kodeBooking,
                'malam'           => $malam,
                'harga_per_malam' => $hargaPerMalam,
                'total'           => $total,
                'status'          => 'pending',
            ], 'Booking berhasil dibuat, menunggu konfirmasi admin');
        } catch (Exception $e) {
            $db->transRollback();
            return $this->respondJson(false, null, $e->getMessage(), 400);
        }
    }

    public function bookingTrack(): ResponseInterface
    {
        $kodeBooking = trim((string) $this->request->getGet('kode_booking'));
        $whatsapp    = trim((string) $this->request->getGet('whatsapp'));

        if ($kodeBooking === '' || $whatsapp === '') {
            return $this->respondJson(false, null, 'kode_booking dan whatsapp wajib diisi', 400);
        }

        $db = TransaksiService::getReservasiDb();
        $row = $db->table('bookings b')
            ->select('b.id, b.kode_booking, b.homestay_id, h.nama AS nama_homestay, h.lokasi,
                      b.nama_tamu, b.whatsapp, b.checkin, b.checkout, b.jumlah_tamu,
                      b.malam, b.harga_per_malam, b.total, b.catatan, b.status, b.created_at')
            ->join('homestays h', 'h.id = b.homestay_id')
            ->where('b.kode_booking', $kodeBooking)
            ->where('b.whatsapp', $whatsapp)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->respondJson(false, null, 'Booking tidak ditemukan. Periksa kembali kode booking dan nomor WhatsApp.', 404);
        }

        [$alamat, $catatanOnly, $metodePembayaran] = TransaksiService::parseBookingCatatanLengkap($row['catatan']);
        $row['alamat']            = $alamat;
        $row['catatan']           = $catatanOnly;
        $row['metode_pembayaran'] = $metodePembayaran;
        $row['bukti_bayar_url']   = TransaksiService::buktiBayarUrl('booking', $row['kode_booking']);

        $row['id']              = (int) $row['id'];
        $row['homestay_id']     = (int) $row['homestay_id'];
        $row['jumlah_tamu']     = (int) $row['jumlah_tamu'];
        $row['malam']           = (int) $row['malam'];
        $row['harga_per_malam'] = (float) $row['harga_per_malam'];
        $row['total']           = (float) $row['total'];

        return $this->respondJson(true, $row, 'Detail booking');
    }

    public function buktiBayar(): ResponseInterface
    {
        $input = $this->getJsonInput();
        $kodeBooking = trim((string) ($input['kode_booking'] ?? ''));
        $whatsapp    = trim((string) ($input['whatsapp'] ?? ''));
        $foto        = (string) ($input['foto_base64'] ?? '');

        if ($kodeBooking === '' || $whatsapp === '' || $foto === '') {
            return $this->respondJson(false, null, 'Kode booking, whatsapp, dan foto wajib diisi', 400);
        }

        $db = TransaksiService::getReservasiDb();
        $row = $db->table('bookings')
            ->select('id, status')
            ->where('kode_booking', $kodeBooking)
            ->where('whatsapp', $whatsapp)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->respondJson(false, null, 'Booking tidak ditemukan. Periksa kembali kode booking dan nomor WhatsApp.', 404);
        }

        [$ok, $result] = TransaksiService::simpanBuktiBayar('booking', $kodeBooking, $foto);
        if (!$ok) {
            return $this->respondJson(false, null, $result, 400);
        }

        if ($row['status'] === 'pending') {
            $db->table('bookings')->where('id', (int) $row['id'])->update(['status' => 'dikonfirmasi']);
        }

        return $this->respondJson(true, ['url' => $result], 'Bukti pembayaran diterima, booking otomatis dikonfirmasi');
    }
}
