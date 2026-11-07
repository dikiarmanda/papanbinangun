<?php

namespace App\Controllers\Api;

use App\Libraries\TransaksiService;
use CodeIgniter\HTTP\ResponseInterface;

class KategoriController extends BaseApiController
{
    public function list(): ResponseInterface
    {
        $db = TransaksiService::getLayananDb();
        $data = $db->table('kategori_produk')->orderBy('nama', 'ASC')->get()->getResultArray();
        return $this->respondJson(true, $data, 'Daftar kategori');
    }
}
