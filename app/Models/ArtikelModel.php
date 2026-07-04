<?php

namespace App\Models;

use CodeIgniter\Model;

class ArtikelModel extends Model
{
    protected $table            = 'artikel';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul', 'slug', 'ringkasan', 'konten', 'gambar_cover',
        'kategori_id', 'admin_id', 'status', 'views', 'published_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPublished(int $limit = 10, int $offset = 0): array
    {
        return $this->select('artikel.*, kategori_artikel.nama as kategori_nama, kategori_artikel.slug as kategori_slug')
            ->join('kategori_artikel', 'kategori_artikel.id = artikel.kategori_id', 'left')
            ->where('artikel.status', 'publish')
            ->orderBy('artikel.published_at', 'DESC')
            ->findAll($limit, $offset);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->select('artikel.*, kategori_artikel.nama as kategori_nama, kategori_artikel.slug as kategori_slug, admin_users.nama as penulis')
            ->join('kategori_artikel', 'kategori_artikel.id = artikel.kategori_id', 'left')
            ->join('admin_users', 'admin_users.id = artikel.admin_id', 'left')
            ->where('artikel.slug', $slug)
            ->where('artikel.status', 'publish')
            ->first();
    }

    public function getWithKategori(?string $status = null): array
    {
        $builder = $this->select('artikel.*, kategori_artikel.nama as kategori_nama, admin_users.nama as penulis')
            ->join('kategori_artikel', 'kategori_artikel.id = artikel.kategori_id', 'left')
            ->join('admin_users', 'admin_users.id = artikel.admin_id', 'left')
            ->orderBy('artikel.created_at', 'DESC');

        if ($status !== null) {
            $builder->where('artikel.status', $status);
        }

        return $builder->findAll();
    }

    public function incrementViews(int $id): void
    {
        $this->set('views', 'views + 1', false)->where('id', $id)->update();
    }
}
