<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel;

class ArtikelController extends BaseController
{
    public function index()
    {
        $model    = new ArtikelModel();
        $perPage  = 9;
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset   = ($page - 1) * $perPage;
        $total    = $model->where('status', 'publish')->countAllResults(true);
        $artikel  = $model->getPublished($perPage, $offset);

        return view('artikel/index', [
            'title'            => 'Artikel & Berita',
            'meta_description' => 'Artikel dan berita terbaru dari desa wisata',
            'artikel'          => $artikel,
            'kategori'         => (new KategoriModel())->orderBy('nama')->findAll(),
            'pengaturan'       => pengaturan(),
            'page'             => $page,
            'totalPages'       => (int) ceil($total / $perPage),
        ]);
    }

    public function detail(string $slug)
    {
        $model   = new ArtikelModel();
        $artikel = $model->findBySlug($slug);

        if (! $artikel) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $model->incrementViews((int) $artikel['id']);
        $artikel['views'] = (int) $artikel['views'] + 1;

        return view('artikel/detail', [
            'title'            => $artikel['judul'],
            'meta_description' => $artikel['ringkasan'] ?: mb_substr(strip_tags($artikel['konten']), 0, 160),
            'meta_image'       => $artikel['gambar_cover'] ? base_url($artikel['gambar_cover']) : null,
            'artikel'          => $artikel,
            'terkait'          => $model->where('status', 'publish')
                ->where('id !=', $artikel['id'])
                ->orderBy('published_at', 'DESC')
                ->findAll(3),
            'pengaturan'       => pengaturan(),
        ]);
    }
}
