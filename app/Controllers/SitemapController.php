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

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . esc($url['loc']) . "</loc>\n";

            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>' . esc($url['lastmod']) . "</lastmod>\n";
            }

            $xml .= '    <priority>' . esc($url['priority'] ?? '0.5') . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/xml')
            ->setBody($xml);
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin/\n\nSitemap: " . site_url('sitemap.xml') . "\n";

        return $this->response
            ->setStatusCode(200)
            ->setContentType('text/plain')
            ->setBody($content);
    }
}
