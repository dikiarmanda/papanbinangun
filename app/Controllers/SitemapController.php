<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\WisataModel;

class SitemapController extends BaseController
{
    public function index()
    {
        $artikel = (new ArtikelModel())->where('status', 'publish')->findAll();
        $wisata  = (new WisataModel())->where('status', 'publish')->findAll();

        $urls = [
            ['loc' => site_url('/'), 'priority' => '1.0'],
            ['loc' => site_url('tentang'), 'priority' => '0.8'],
            ['loc' => site_url('wisata'), 'priority' => '0.9'],
            ['loc' => site_url('artikel'), 'priority' => '0.9'],
            ['loc' => site_url('galeri'), 'priority' => '0.7'],
            ['loc' => site_url('kontak'), 'priority' => '0.6'],
        ];

        foreach ($wisata as $w) {
            $urls[] = [
                'loc'     => site_url('wisata/' . $w['slug']),
                'lastmod' => date('Y-m-d', strtotime($w['updated_at'])),
                'priority'=> '0.8',
            ];
        }

        foreach ($artikel as $a) {
            $urls[] = [
                'loc'     => site_url('artikel/' . $a['slug']),
                'lastmod' => date('Y-m-d', strtotime($a['updated_at'])),
                'priority'=> '0.7',
            ];
        }

        return $this->response
            ->setHeader('Content-Type', 'application/xml')
            ->setBody(view('sitemap', ['urls' => $urls]));
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin/\n\nSitemap: " . site_url('sitemap.xml') . "\n";

        return $this->response
            ->setHeader('Content-Type', 'text/plain')
            ->setBody($content);
    }
}
