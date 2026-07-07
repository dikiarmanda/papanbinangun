<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HeroBannerModel;
use App\Libraries\ActivityLogService;
use App\Libraries\ImageUploader;

class BannerAdminController extends BaseController
{
    protected HeroBannerModel $model;

    public function __construct()
    {
        $this->model = new HeroBannerModel();
    }

    public function index()
    {
        $banners = $this->model->getAllForAdmin();

        return view('admin/banner/index', [
            'title' => 'Kelola Banner Beranda',
            'form' => [
                'action' => site_url('admin/banner/store'),
                'statusOptions' => admin_select_options([
                    'publish' => 'Tampilkan',
                    'draft' => 'Sembunyikan',
                ], 'publish'),
            ],
            'items' => admin_banner_index_items($banners),
            'itemCount' => count($banners),
        ]);
    }

    public function store()
    {
        $gambar = ImageUploader::upload('gambar', 'uploads/banner', 1400);
        if (!$gambar) {
            return redirect()->back()->with('error', 'Gagal mengunggah gambar. Pastikan format valid (JPG/PNG/WebP).');
        }

        $data = [
            'judul' => $this->request->getPost('judul'),
            'gambar' => $gambar,
            'link_url' => $this->request->getPost('link_url') ?: null,
            'urutan' => (int) ($this->request->getPost('urutan') ?: 0),
            'status' => $this->request->getPost('status') === 'draft' ? 'draft' : 'publish',
            'admin_id' => session()->get('admin_id'),
        ];

        $id = $this->model->insert($data);
        ActivityLogService::log('menambah banner beranda', 'hero_banner', (int) $id);

        return redirect()->to(site_url('admin/banner'))->with('success', 'Banner berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $banner = $this->model->find($id);
        if (!$banner) {
            return redirect()->to(site_url('admin/banner'))->with('error', 'Banner tidak ditemukan.');
        }

        $data = [
            'judul' => $this->request->getPost('judul'),
            'link_url' => $this->request->getPost('link_url') ?: null,
            'urutan' => (int) ($this->request->getPost('urutan') ?: 0),
            'status' => $this->request->getPost('status') === 'draft' ? 'draft' : 'publish',
        ];

        $newGambar = ImageUploader::upload('gambar', 'uploads/banner', 1400);
        if ($newGambar) {
            ImageUploader::delete($banner['gambar']);
            $data['gambar'] = $newGambar;
        }

        $this->model->update($id, $data);
        ActivityLogService::log('mengubah banner beranda: ' . ($data['judul'] ?: '#' . $id), 'hero_banner', $id);

        return redirect()->to(site_url('admin/banner'))->with('success', 'Banner berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $banner = $this->model->find($id);
        if ($banner) {
            ImageUploader::delete($banner['gambar']);
            $this->model->delete($id);
            ActivityLogService::log('menghapus banner beranda', 'hero_banner', $id);
        }

        return redirect()->to(site_url('admin/banner'))->with('success', 'Banner berhasil dihapus.');
    }
}
