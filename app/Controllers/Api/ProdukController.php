<?php

namespace App\Controllers\Api;

use App\Libraries\TransaksiService;
use CodeIgniter\HTTP\ResponseInterface;

class ProdukController extends BaseApiController
{
    public function list(): ResponseInterface
    {
        $kategoriId = $this->request->getGet('kategori_id');
        $jenis      = $this->request->getGet('jenis'); // umkm / catering

        $db = TransaksiService::getLayananDb();
        $builder = $db->table('produk p')
            ->select('p.*, k.nama AS kategori_nama')
            ->join('kategori_produk k', 'k.id = p.kategori_id', 'left')
            ->where('p.status', 'publish');

        if (!empty($kategoriId)) {
            $builder->where('p.kategori_id', (int) $kategoriId);
        }
        if (!empty($jenis)) {
            $builder->where('p.jenis', trim($jenis));
        }

        $data = $builder->orderBy('p.created_at', 'DESC')->get()->getResultArray();
        return $this->respondJson(true, $data, 'Daftar produk');
    }

    public function detail(): ResponseInterface
    {
        $id = (int) $this->request->getGet('id');
        if ($id <= 0) {
            return $this->respondJson(false, null, 'Parameter id wajib diisi', 400);
        }

        $db = TransaksiService::getLayananDb();
        $row = $db->table('produk p')
            ->select('p.*, k.nama AS kategori_nama')
            ->join('kategori_produk k', 'k.id = p.kategori_id', 'left')
            ->where('p.id', $id)
            ->where('p.status', 'publish')
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->respondJson(false, null, 'Produk tidak ditemukan', 404);
        }

        return $this->respondJson(true, $row, 'Detail produk');
    }
}
