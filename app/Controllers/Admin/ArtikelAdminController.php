<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArtikelModel;
use App\Models\KategoriModel;
use App\Libraries\ActivityLogService;
use App\Libraries\ImageUploader;

class ArtikelAdminController extends BaseController
{
    protected ArtikelModel $artikelModel;
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->artikelModel = new ArtikelModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $artikel = $this->artikelModel->getWithKategori();

        return view('admin/artikel/index', [
            'title' => 'Kelola Artikel',
            'rows' => admin_artikel_index_rows($artikel),
        ]);
    }

    public function create()
    {
        return view('admin/artikel/form', $this->formViewData(null));
    }

    public function store()
    {
        $judul = $this->request->getPost('judul');
        $slug = $this->request->getPost('slug') ?: slugify($judul);
        $status = $this->request->getPost('status') ?: 'draft';

        $slug = $this->uniqueSlug($slug);

        $konten = $this->request->getPost('konten');

        $data = [
            'judul' => $judul,
            'slug' => $slug,
            'ringkasan' => artikel_ringkasan_from_konten($konten),
            'konten' => $konten,
            'kategori_id' => $this->request->getPost('kategori_id') ?: null,
            'admin_id' => session()->get('admin_id'),
            'status' => $status,
            'published_at' => $status === 'publish' ? date('Y-m-d H:i:s') : null,
        ];

        $cover = ImageUploader::upload('gambar_cover', 'uploads/artikel');
        if ($cover) {
            $data['gambar_cover'] = $cover;
        }

        $id = $this->artikelModel->insert($data);
        ActivityLogService::log('membuat artikel: ' . $judul, 'artikel', (int) $id);

        return redirect()->to(site_url('admin/artikel'))->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $artikel = $this->artikelModel->find($id);
        if (!$artikel) {
            return redirect()->to(site_url('admin/artikel'))->with('error', 'Artikel tidak ditemukan.');
        }

        return view('admin/artikel/form', $this->formViewData($artikel));
    }

    public function update(int $id)
    {
        $artikel = $this->artikelModel->find($id);
        if (!$artikel) {
            return redirect()->to(site_url('admin/artikel'))->with('error', 'Artikel tidak ditemukan.');
        }

        $judul = $this->request->getPost('judul');
        $slug = $this->request->getPost('slug') ?: slugify($judul);
        $status = $this->request->getPost('status') ?: 'draft';

        if ($slug !== $artikel['slug']) {
            $slug = $this->uniqueSlug($slug, $id);
        }

        $konten = $this->request->getPost('konten');

        $data = [
            'judul' => $judul,
            'slug' => $slug,
            'ringkasan' => artikel_ringkasan_from_konten($konten),
            'konten' => $konten,
            'kategori_id' => $this->request->getPost('kategori_id') ?: null,
            'status' => $status,
        ];

        if ($status === 'publish' && empty($artikel['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $cover = ImageUploader::upload('gambar_cover', 'uploads/artikel');
        if ($cover) {
            ImageUploader::delete($artikel['gambar_cover']);
            $data['gambar_cover'] = $cover;
        }

        $this->artikelModel->update($id, $data);
        ActivityLogService::log('mengubah artikel: ' . $judul, 'artikel', $id);

        return redirect()->to(site_url('admin/artikel'))->with('success', 'Artikel berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $artikel = $this->artikelModel->find($id);
        if ($artikel) {
            ImageUploader::delete($artikel['gambar_cover']);
            $this->artikelModel->delete($id);
            ActivityLogService::log('menghapus artikel: ' . $artikel['judul'], 'artikel', $id);
        }

        return redirect()->to(site_url('admin/artikel'))->with('success', 'Artikel berhasil dihapus.');
    }

    private function formViewData(?array $artikel): array
    {
        $kategori = $this->kategoriModel->orderBy('nama')->findAll();
        $id = isset($artikel['id']) ? (int) $artikel['id'] : null;

        return [
            'title' => $artikel ? 'Edit Artikel' : 'Tambah Artikel',
            'form' => [
                'action' => admin_crud_action('admin/artikel', $id),
                'backUrl' => site_url('admin/artikel'),
                'judul' => admin_form_value('judul', $artikel['judul'] ?? ''),
                'slug' => admin_form_value('slug', $artikel['slug'] ?? ''),
                'konten' => admin_rich_text('konten', $artikel['konten'] ?? ''),
                'coverUrl' => !empty($artikel['gambar_cover']) ? upload_url($artikel['gambar_cover']) : '',
                'kategoriOptions' => admin_kategori_select_options(
                    $kategori,
                    admin_form_raw('kategori_id', $artikel['kategori_id'] ?? ''),
                ),
                'statusOptions' => admin_select_options([
                    'draft' => 'Draft',
                    'publish' => 'Publish',
                ], admin_form_raw('status', $artikel['status'] ?? 'draft')),
            ],
        ];
    }

    private function uniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $base = $slug;
        $count = 1;

        while (true) {
            $builder = $this->artikelModel->where('slug', $slug);
            if ($excludeId) {
                $builder->where('id !=', $excludeId);
            }
            if (!$builder->first()) {
                return $slug;
            }
            $slug = $base . '-' . $count++;
        }
    }
}
