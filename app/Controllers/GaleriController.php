<?php

namespace App\Controllers;

use App\Models\GaleriModel;
use App\Models\KategoriModel;

class GaleriController extends BaseController
{
    public function index()
    {
        $kategoriSlug = $this->request->getGet('kategori');
        $galeri = (new GaleriModel())->getPublicGallery($kategoriSlug);

        return view('galeri', [
            'title'              => 'Galeri Foto',
            'meta_description'   => 'Galeri foto destinasi dan kegiatan desa wisata',
            'galeri'             => $galeri,
            'galeri_total'       => count($galeri),
            'galeri_limit_initial' => GaleriModel::PUBLIC_INITIAL_LIMIT,
            'galeri_limit_step'    => GaleriModel::PUBLIC_LOAD_MORE_STEP,
            'kategori_list'      => (new KategoriModel())->orderBy('nama')->findAll(),
            'kategori_aktif'     => $kategoriSlug,
            'pengaturan'         => pengaturan(),
        ]);
    }
}
