<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArtikelModel;
use App\Models\KategoriModel;
use App\Libraries\ActivityLogService;

class KategoriAdminController extends BaseController
{
    protected KategoriModel $model;

    public function __construct()
    {
        $this->model = new KategoriModel();
    }

    public function index()
    {
        $kategori = $this->model->orderBy('nama')->findAll();

        return view('admin/kategori/index', [
            'title' => 'Kelola Kategori',
            'itemCount' => count($kategori),
            'form' => [
                'action' => site_url('admin/kategori/store'),
            ],
            'rows' => admin_kategori_index_rows($kategori),
        ]);
    }

    public function store()
    {
        $nama = $this->request->getPost('nama');
        $slug = $this->request->getPost('slug') ?: slugify($nama);
        $slug = $this->uniqueSlug($slug);

        $id = $this->model->insert(['nama' => $nama, 'slug' => $slug]);
        ActivityLogService::log('membuat kategori: ' . $nama, 'kategori_artikel', (int) $id);

        return redirect()->to(site_url('admin/kategori'))->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $kategori = $this->model->find($id);
        if (!$kategori) {
            return redirect()->to(site_url('admin/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        $nama = $this->request->getPost('nama');
        $slug = $this->request->getPost('slug') ?: slugify($nama);

        if ($slug !== $kategori['slug']) {
            $slug = $this->uniqueSlug($slug, $id);
        }

        $this->model->update($id, ['nama' => $nama, 'slug' => $slug]);
        ActivityLogService::log('mengubah kategori: ' . $nama, 'kategori_artikel', $id);

        return redirect()->to(site_url('admin/kategori'))->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $kategori = $this->model->find($id);
        if (!$kategori) {
            return redirect()->to(site_url('admin/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        $galeriCount = db_connect()->table('galeri_kategori')->where('kategori_id', $id)->countAllResults();
        $artikelCount = (new ArtikelModel())->where('kategori_id', $id)->countAllResults();

        if ($galeriCount > 0 || $artikelCount > 0) {
            return redirect()->to(site_url('admin/kategori'))
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan di galeri atau artikel.');
        }

        $this->model->delete($id);
        ActivityLogService::log('menghapus kategori: ' . $kategori['nama'], 'kategori_artikel', $id);

        return redirect()->to(site_url('admin/kategori'))->with('success', 'Kategori berhasil dihapus.');
    }

    private function uniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $base = $slug;
        $count = 1;

        while (true) {
            $builder = $this->model->where('slug', $slug);
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
