<?php

namespace App\Models;

use CodeIgniter\Model;

class GaleriModel extends Model
{
    protected $table            = 'galeri';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul', 'gambar', 'kategori', 'wisata_id', 'admin_id',
    ];
    protected $useTimestamps = false;

    public function getAllWithRelations(?string $kategori = null): array
    {
        $builder = $this->select('galeri.*, wisata.nama as wisata_nama, admin_users.nama as uploader')
            ->join('wisata', 'wisata.id = galeri.wisata_id', 'left')
            ->join('admin_users', 'admin_users.id = galeri.admin_id', 'left')
            ->orderBy('galeri.created_at', 'DESC');

        if ($kategori !== null && $kategori !== '') {
            $builder->where('galeri.kategori', $kategori);
        }

        return $builder->findAll();
    }
}
