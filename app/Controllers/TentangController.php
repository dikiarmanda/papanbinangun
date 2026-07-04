<?php

namespace App\Controllers;

class TentangController extends BaseController
{
    public function index()
    {
        $settings = pengaturan();

        return view('tentang', [
            'title'            => 'Tentang ' . $settings['nama_desa'],
            'meta_description' => 'Profil dan sejarah ' . $settings['nama_desa'],
            'pengaturan'       => $settings,
        ]);
    }
}
