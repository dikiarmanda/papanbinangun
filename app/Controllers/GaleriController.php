<?php

namespace App\Controllers;

use App\Models\GaleriModel;

class GaleriController extends BaseController
{
    public function index()
    {
        $kategori = $this->request->getGet('kategori');

        return view('galeri', [
            'title'            => 'Galeri Foto',
            'meta_description' => 'Galeri foto destinasi dan kegiatan desa wisata',
            'galeri'           => (new GaleriModel())->getAllWithRelations($kategori),
            'kategori_aktif'   => $kategori,
            'pengaturan'       => pengaturan(),
        ]);
    }
}
