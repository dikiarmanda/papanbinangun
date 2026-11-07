<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogService;
use App\Libraries\TransaksiService;
use Exception;

class TransaksiAdminController extends BaseController
{
    public function index()
    {
        $tab = $this->request->getGet('tab') === 'belanja' ? 'belanja' : 'homestay';

        $statusBookingOptions = ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'];
        $statusBayarOptions   = ['pending', 'paid', 'failed', 'expired'];
        $statusOrderOptions   = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

        $bookings = [];
        $orders   = [];

        if ($tab === 'homestay') {
            $db = TransaksiService::getReservasiDb();
            $bookings = $db->table('bookings b')
                ->select('b.*, h.nama AS nama_homestay')
                ->join('homestays h', 'h.id = b.homestay_id', 'left')
                ->orderBy('b.id', 'DESC')
                ->limit(200)
                ->get()
                ->getResultArray();
        } else {
            $db = TransaksiService::getLayananDb();
            $orders = $db->query("SELECT o.*, p.nama AS nama_pemesan, p.no_hp AS no_hp_pemesan,
                    GROUP_CONCAT(CONCAT(oi.nama_produk, ' x', oi.jumlah) SEPARATOR ', ') AS ringkasan_item
                FROM `order` o
                LEFT JOIN pelanggan p ON p.id = o.pelanggan_id
                LEFT JOIN order_items oi ON oi.order_id = o.id
                GROUP BY o.id
                ORDER BY o.id DESC
                LIMIT 200")->getResultArray();
        }

        return view('admin/transaksi/index', [
            'title'                => 'Transaksi & Layanan',
            'tab'                  => $tab,
            'bookings'             => $bookings,
            'orders'               => $orders,
            'statusBookingOptions' => $statusBookingOptions,
            'statusBayarOptions'   => $statusBayarOptions,
            'statusOrderOptions'   => $statusOrderOptions,
        ]);
    }

    public function updateStatus()
    {
        $jenis = (string) $this->request->getPost('jenis');
        $id    = (int) $this->request->getPost('id');
        $tab   = $this->request->getPost('tab') === 'belanja' ? 'belanja' : 'homestay';

        if ($id <= 0) {
            return redirect()->to(site_url('admin/transaksi?tab=' . $tab))->with('error', 'ID data tidak valid.');
        }

        try {
            if ($jenis === 'booking') {
                $status  = trim((string) $this->request->getPost('status'));
                $allowed = ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'];
                if (!in_array($status, $allowed, true)) {
                    throw new Exception('Status booking tidak valid.');
                }

                $db = TransaksiService::getReservasiDb();
                $db->table('bookings')->where('id', $id)->update(['status' => $status]);
                ActivityLogService::log('mengubah status booking homestay ID ' . $id . ' menjadi ' . $status, 'bookings', $id);

            } elseif ($jenis === 'order') {
                $statusBayar = trim((string) $this->request->getPost('status_pembayaran'));
                $statusOrder = trim((string) $this->request->getPost('status_order'));

                $allowedBayar = ['pending', 'paid', 'failed', 'expired'];
                $allowedOrder = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

                if (!in_array($statusBayar, $allowedBayar, true) || !in_array($statusOrder, $allowedOrder, true)) {
                    throw new Exception('Status pembayaran atau order tidak valid.');
                }

                $db = TransaksiService::getLayananDb();
                $db->transBegin();

                // Finding 6.6: Rollback stok otomatis saat status_order dibatalkan
                if ($statusOrder === 'dibatalkan') {
                    TransaksiService::rollbackOrderStok($db, $id);
                }

                $db->table('`order`')->where('id', $id)->update([
                    'status_pembayaran' => $statusBayar,
                    'status_order'      => $statusOrder,
                ]);

                if ($db->transStatus() === false) {
                    $db->transRollback();
                    throw new Exception('Gagal menyimpan pembaruan status order.');
                }
                $db->transCommit();

                ActivityLogService::log('mengubah status order belanja ID ' . $id . ' (bayar: ' . $statusBayar . ', order: ' . $statusOrder . ')', 'order', $id);
            } else {
                throw new Exception('Jenis transaksi tidak dikenal.');
            }

            return redirect()->to(site_url('admin/transaksi?tab=' . $tab))->with('success', 'Status transaksi berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->to(site_url('admin/transaksi?tab=' . $tab))->with('error', $e->getMessage());
        }
    }
}
