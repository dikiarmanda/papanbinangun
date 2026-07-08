<?php

namespace App\Controllers;

use App\Models\GaleriModel;
use App\Models\KategoriModel;

class GaleriController extends BaseController
{
    public function index()
    {
        $kategoriSlug = $this->request->getGet('kategori');

        return view('galeri', [
            'title'            => 'Galeri Foto',
            'meta_description' => 'Galeri foto destinasi dan kegiatan desa wisata',
            'galeri'           => (new GaleriModel())->getAllWithRelations($kategoriSlug),
            'kategori_list'    => (new KategoriModel())->orderBy('nama')->findAll(),
            'kategori_aktif'   => $kategoriSlug,
            'pengaturan'       => pengaturan(),
        ]);
    }
}
