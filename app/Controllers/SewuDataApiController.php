<?php

namespace App\Controllers;

use App\Models\PedagangModel;
use App\Models\PenjualanModel;

class SewuDataApiController extends BaseController
{
    // GET /sewu-data/api/data
    // Return semua data penjualan (format sama dengan Apps Script)
    public function data()
    {
        $penjualanModel = new PenjualanModel();

        $data = $penjualanModel
            ->select('penjualan.bulan, pedagang.nama, penjualan.keping, penjualan.omzet')
            ->join('pedagang', 'pedagang.id = penjualan.pedagang_id')
            ->orderBy('penjualan.bulan', 'ASC')
            ->findAll();

        // Ubah format: {b, n, k, o}
        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'b' => $row['bulan'],
                'n' => $row['nama'],
                'k' => (int) $row['keping'],
                'o' => (int) $row['omzet'],
            ];
        }

        return $this->response->setJSON($result);
    }

    // GET /sewu-data/api/statistik
    // Return statistik ringkasan
    public function statistik()
    {
        $penjualanModel = new PenjualanModel();

        $total = $penjualanModel
            ->select('SUM(keping) as total_keping, SUM(omzet) as total_omzet, COUNT(DISTINCT pedagang_id) as total_pedagang')
            ->first();

        return $this->response->setJSON([
            'total_keping' => (int) $total['total_keping'],
            'total_omzet' => (int) $total['total_omzet'],
            'total_pedagang' => (int) $total['total_pedagang'],
        ]);
    }

    // GET /sewu-data/api/pedagang
    // Return daftar pedagang
    public function pedagang()
    {
        $pedagangModel = new PedagangModel();
        $data = $pedagangModel->orderBy('nama', 'ASC')->findAll();

        return $this->response->setJSON($data);
    }
}