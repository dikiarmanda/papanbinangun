<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GaleriModel;
use App\Models\WisataModel;
use App\Libraries\ActivityLogService;
use App\Libraries\ImageUploader;

class GaleriAdminController extends BaseController
{
    protected GaleriModel $model;

    public function __construct()
    {
        $this->model = new GaleriModel();
    }

    public function index()
    {
        $galeri = $this->model->getAllWithRelations();
        $wisata = (new WisataModel())->orderBy('nama')->findAll();

        return view('admin/galeri/index', [
            'title' => 'Kelola Galeri',
            'form' => [
                'action' => site_url('admin/galeri/store'),
                'kategoriOptions' => admin_galeri_kategori_options(),
                'wisataOptions' => admin_wisata_select_options($wisata),
            ],
            'items' => admin_galeri_index_items($galeri),
            'itemCount' => count($galeri),
        ]);
    }

    public function store()
    {
        $gambar = ImageUploader::upload('gambar', 'uploads/galeri');
        if (!$gambar) {
            return redirect()->back()->with('error', 'Gagal mengunggah gambar. Pastikan format valid (JPG/PNG/WebP).');
        }

        $data = [
            'judul' => $this->request->getPost('judul'),
            'gambar' => $gambar,
            'kategori' => $this->request->getPost('kategori') ?: 'lainnya',
            'wisata_id' => $this->request->getPost('wisata_id') ?: null,
            'admin_id' => session()->get('admin_id'),
        ];

        $id = $this->model->insert($data);
        ActivityLogService::log('menambah foto galeri', 'galeri', (int) $id);

        return redirect()->to(site_url('admin/galeri'))->with('success', 'Foto berhasil ditambahkan.');
    }

    public function delete(int $id)
    {
        $item = $this->model->find($id);
        if ($item) {
            ImageUploader::delete($item['gambar']);
            $this->model->delete($id);
            ActivityLogService::log('menghapus foto galeri', 'galeri', $id);
        }

        return redirect()->to(site_url('admin/galeri'))->with('success', 'Foto berhasil dihapus.');
    }
}
