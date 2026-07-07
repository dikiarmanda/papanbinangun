<?php

namespace App\Models;

use CodeIgniter\Model;

class HeroBannerModel extends Model
{
    protected $table            = 'hero_banners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul',
        'gambar',
        'link_url',
        'urutan',
        'status',
        'admin_id',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPublished(): array
    {
        return $this->where('status', 'publish')
            ->orderBy('urutan', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getAllForAdmin(): array
    {
        return $this->select('hero_banners.*, admin_users.nama as uploader')
            ->join('admin_users', 'admin_users.id = hero_banners.admin_id', 'left')
            ->orderBy('urutan', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
