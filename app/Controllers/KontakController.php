<?php

namespace App\Controllers;

class KontakController extends BaseController
{
    public function index()
    {
        $settings = pengaturan();
        $mapQuery = 'Desa+Binangun+Pandaan+Pasuruan+Jawa+Timur';

        $mapEmbed = $settings['google_maps_embed'] ?? '';
        if ($mapEmbed === '' || $mapEmbed === null) {
            $mapEmbed = '<iframe src="https://maps.google.com/maps?q=' . $mapQuery . '&t=&z=14&ie=UTF8&iwloc=&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen title="Peta Wisata Binangun"></iframe>';
        }

        return view('kontak', [
            'title' => 'Kontak',
            'meta_description' => 'Hubungi kami — ' . $settings['nama_desa'],
            'pengaturan' => $settings,
            'mapEmbed' => $mapEmbed,
            'mapsUrl' => 'https://www.google.com/maps/search/?api=1&query=' . $mapQuery,
            'alamat' => $settings['alamat'] ?: 'Desa Binangun, Kec. Pandaan, Kab. Pasuruan, Jawa Timur',
        ]);
    }
}
