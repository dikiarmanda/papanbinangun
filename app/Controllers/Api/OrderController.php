<?php

namespace App\Controllers\Api;

use App\Libraries\TransaksiService;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class OrderController extends BaseApiController
{
    public function create(): ResponseInterface
    {
        $input = $this->getJsonInput();

        $namaPemesan      = trim((string) ($input['nama_pemesan'] ?? ''));
        $noHpPemesan      = trim((string) ($input['no_hp_pemesan'] ?? ''));
        $alamatKirim      = trim((string) ($input['alamat_kirim'] ?? ''));
        $metodePengiriman = trim((string) ($input['metode_pengiriman'] ?? 'ambil_di_tempat'));
        $zonaAntarId      = (isset($input['zona_antar_id']) && $input['zona_antar_id'] !== '' && $input['zona_antar_id'] !== null)
            ? (int) $input['zona_antar_id'] : null;
        $items            = $input['items'] ?? [];
        $ongkosKirim      = isset($input['ongkos_kirim']) ? (float) $input['ongkos_kirim'] : 0.0;

        if ($namaPemesan === '' || $noHpPemesan === '' || $alamatKirim === '') {
            return $this->respondJson(false, null, 'Nama, no HP, dan alamat kirim wajib diisi', 400);
        }

        $metodeValid = ['ekspedisi', 'ambil_di_tempat', 'antar_lokal'];
        if (!in_array($metodePengiriman, $metodeValid, true)) {
            return $this->respondJson(false, null, 'Metode pengiriman tidak valid', 400);
        }

        if (!is_array($items) || count($items) === 0) {
            return $this->respondJson(false, null, 'Keranjang belanja kosong', 400);
        }

        $db = TransaksiService::getLayananDb();
        $db->transBegin();

        try {
            $pelangganId = TransaksiService::findOrCreateGuestPelanggan($db, $namaPemesan, $noHpPemesan);

            $itemsToInsert = [];
            $totalHarga    = 0.0;

            foreach ($items as $it) {
                $produkId = (int) ($it['produk_id'] ?? 0);
                $jumlah   = (int) ($it['jumlah'] ?? 0);
                if ($produkId <= 0 || $jumlah <= 0) {
                    throw new Exception('Data item keranjang tidak valid');
                }

                $produk = $db->query('SELECT id, nama, harga, stok FROM produk WHERE id = ? AND status = "publish" FOR UPDATE', [$produkId])->getRowArray();
                if (!$produk) {
                    throw new Exception('Produk tidak ditemukan (id: ' . $produkId . ')');
                }
                if ((int) $produk['stok'] < $jumlah) {
                    throw new Exception('Stok produk "' . $produk['nama'] . '" tidak mencukupi');
                }

                $hargaSatuan = (float) $produk['harga'];
                $subtotal    = $hargaSatuan * $jumlah;
                $totalHarga += $subtotal;

                $itemsToInsert[] = [
                    'produk_id'    => $produkId,
                    'nama_produk'  => $produk['nama'],
                    'harga_satuan' => $hargaSatuan,
                    'jumlah'       => $jumlah,
                    'subtotal'     => $subtotal,
                ];

                $db->table('produk')
                    ->where('id', $produkId)
                    ->set('stok', 'stok - ' . $jumlah, false)
                    ->update();
            }

            $totalHarga += $ongkosKirim;
            $kodeOrder   = TransaksiService::generateKodeOrder();

            $db->table('`order`')->insert([
                'kode_order'        => $kodeOrder,
                'pelanggan_id'      => $pelangganId,
                'alamat_kirim'      => $alamatKirim,
                'metode_pengiriman' => $metodePengiriman,
                'zona_antar_id'     => $zonaAntarId,
                'ongkos_kirim'      => $ongkosKirim,
                'total_harga'       => $totalHarga,
                'status_pembayaran' => 'pending',
                'status_order'      => 'menunggu_pembayaran',
                'stok_locked'       => 1,
            ]);

            $orderId = (int) $db->insertID();

            foreach ($itemsToInsert as $it) {
                $db->table('order_items')->insert([
                    'order_id'     => $orderId,
                    'produk_id'    => $it['produk_id'],
                    'nama_produk'  => $it['nama_produk'],
                    'harga_satuan' => $it['harga_satuan'],
                    'jumlah'       => $it['jumlah'],
                    'subtotal'     => $it['subtotal'],
                ]);
            }

            $db->transCommit();

            return $this->respondJson(true, [
                'id'                => $orderId,
                'kode_order'        => $kodeOrder,
                'total_harga'       => $totalHarga,
                'status_pembayaran' => 'pending',
                'status_order'      => 'menunggu_pembayaran',
            ], 'Order berhasil dibuat');
        } catch (Exception $e) {
            $db->transRollback();
            return $this->respondJson(false, null, $e->getMessage(), 400);
        }
    }

    public function detail(): ResponseInterface
    {
        $id = (int) $this->request->getGet('id');
        if ($id <= 0) {
            return $this->respondJson(false, null, 'Parameter id wajib diisi', 400);
        }

        $db = TransaksiService::getLayananDb();
        $order = $db->table('`order` o')
            ->select('o.*, p.nama AS nama_pemesan, p.no_hp AS no_hp_pemesan')
            ->join('pelanggan p', 'p.id = o.pelanggan_id', 'left')
            ->where('o.id', $id)
            ->get()
            ->getRowArray();

        if (!$order) {
            return $this->respondJson(false, null, 'Order tidak ditemukan', 404);
        }

        [$order['alamat_kirim'], $order['metode_pembayaran']] = TransaksiService::parseAlamatPembayaran($order['alamat_kirim']);
        $order['bukti_bayar_url'] = TransaksiService::buktiBayarUrl('order', $order['kode_order']);

        $items = $db->table('order_items')->where('order_id', $id)->get()->getResultArray();
        $order['items'] = $items;

        return $this->respondJson(true, $order, 'Detail order');
    }

    public function track(): ResponseInterface
    {
        $kode = trim((string) $this->request->getGet('kode'));
        $noHp = trim((string) $this->request->getGet('no_hp'));

        if ($kode === '' || $noHp === '') {
            return $this->respondJson(false, null, 'Parameter kode dan no_hp wajib diisi', 400);
        }

        $db = TransaksiService::getLayananDb();
        $order = $db->table('`order` o')
            ->select('o.*, p.nama AS nama_pemesan, p.no_hp AS no_hp_pemesan')
            ->join('pelanggan p', 'p.id = o.pelanggan_id')
            ->where('o.kode_order', $kode)
            ->where('p.no_hp', $noHp)
            ->get()
            ->getRowArray();

        if (!$order) {
            return $this->respondJson(false, null, 'Order tidak ditemukan. Periksa kembali kode dan nomor HP.', 404);
        }

        [$order['alamat_kirim'], $order['metode_pembayaran']] = TransaksiService::parseAlamatPembayaran($order['alamat_kirim']);
        $order['bukti_bayar_url'] = TransaksiService::buktiBayarUrl('order', $order['kode_order']);

        $items = $db->table('order_items')->where('order_id', (int) $order['id'])->get()->getResultArray();
        $order['items'] = $items;

        return $this->respondJson(true, $order, 'Detail order');
    }

    public function buktiBayar(): ResponseInterface
    {
        $input = $this->getJsonInput();
        $kode       = trim((string) ($input['kode'] ?? ''));
        $noHp       = trim((string) ($input['no_hp'] ?? ''));
        $fotoBase64 = (string) ($input['foto_base64'] ?? '');

        if ($kode === '' || $noHp === '' || $fotoBase64 === '') {
            return $this->respondJson(false, null, 'Parameter kode, no_hp, dan foto_base64 wajib diisi', 400);
        }

        $db = TransaksiService::getLayananDb();
        $order = $db->table('`order` o')
            ->select('o.id, o.status_pembayaran')
            ->join('pelanggan p', 'p.id = o.pelanggan_id')
            ->where('o.kode_order', $kode)
            ->where('p.no_hp', $noHp)
            ->get()
            ->getRowArray();

        if (!$order) {
            return $this->respondJson(false, null, 'Order tidak ditemukan. Periksa kembali kode dan nomor HP.', 404);
        }

        [$ok, $result] = TransaksiService::simpanBuktiBayar('order', $kode, $fotoBase64);
        if (!$ok) {
            return $this->respondJson(false, null, $result, 400);
        }

        // Finding 6.1: status_pembayaran ENUM: 'pending', 'paid', 'failed', 'expired'
        if ($order['status_pembayaran'] === 'pending') {
            $db->table('`order`')->where('id', (int) $order['id'])->update([
                'status_pembayaran' => 'paid',
                'paid_at'           => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->respondJson(true, ['url' => $result], 'Bukti bayar berhasil diupload');
    }
}
