<?php

namespace App\Controllers;

use App\Models\WisataModel;
use App\Models\GaleriModel;

class WisataController extends BaseController
{
    public function index()
    {
        return view('wisata/index', [
            'title' => 'Destinasi Wisata',
            'meta_description' => 'Jelajahi destinasi wisata desa',
            'wisata' => (new WisataModel())->getPublished(),
            'pengaturan' => pengaturan(),
        ]);
    }

    public function detail(string $slug)
    {
        $wisata = (new WisataModel())->findBySlug($slug);

        if (!$wisata) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $galeri = (new GaleriModel())
            ->where('wisata_id', $wisata['id'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('wisata/detail', [
            'title' => $wisata['nama'],
            'meta_description' => mb_substr(strip_tags($wisata['deskripsi']), 0, 160),
            'meta_image' => $wisata['gambar_cover'] ? base_url($wisata['gambar_cover']) : null,
            'wisata' => $wisata,
            'fasilitas' => WisataModel::decodeFasilitas($wisata['fasilitas'] ?? null),
            'galeri' => $galeri,
            'pengaturan' => pengaturan(),
        ]);
    }
}
