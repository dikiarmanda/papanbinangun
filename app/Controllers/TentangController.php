<?php

namespace App\Controllers;

class TentangController extends BaseController
{
    public function index()
    {
        if (ENVIRONMENT === 'production') {
            $this->cachePage(600);
        }

        $settings = pengaturan();

        return view('tentang', [
            'title'            => 'Tentang ' . $settings['nama_desa'],
            'meta_description' => 'Profil dan sejarah ' . $settings['nama_desa'],
            'pengaturan'       => $settings,
        ]);
    }
}
