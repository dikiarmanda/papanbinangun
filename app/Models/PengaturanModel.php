<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table = 'pengaturan_situs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nama_desa',
        'tagline',
        'deskripsi_singkat',
        'alamat',
        'no_whatsapp',
        'email_kontak',
        'instagram_url',
        'tiktok_url',
        'facebook_url',
        'google_maps_embed',
        'logo',
    ];
    protected $useTimestamps = true;
    protected $createdField = '';
    protected $updatedField = 'updated_at';

    public function get(): array
    {
        $row = $this->find(1);

        return $row ?? [
            'id' => 1,
            'nama_desa' => 'Wisata Binangun',
            'tagline' => 'Pesona Alam & Budaya yang Tak Lekang Waktu',
            'deskripsi_singkat' => 'Wisata Binangun menghadirkan pengalaman pedesaan yang otentik — alam asri, budaya hidup, dan keramahan warga.',

            'alamat' => '',
            'no_whatsapp' => '',
            'email_kontak' => '',
            'instagram_url' => '',
            'tiktok_url' => '',
            'facebook_url' => '',
            'google_maps_embed' => '',
            'logo' => '',
        ];
    }
}
