<?php

namespace App\Controllers\Api;

use App\Libraries\TransaksiService;
use CodeIgniter\HTTP\ResponseInterface;

class ZonaController extends BaseApiController
{
    public function list(): ResponseInterface
    {
        $db = TransaksiService::getLayananDb();
        $data = $db->table('zona_antar_lokal')
            ->where('status', 'aktif')
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();

        return $this->respondJson(true, $data, 'Daftar zona antar lokal');
    }
}
