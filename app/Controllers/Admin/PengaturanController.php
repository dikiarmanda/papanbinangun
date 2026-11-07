<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Libraries\ActivityLogService;
use App\Libraries\ImageUploader;

class PengaturanController extends BaseController
{
    protected PengaturanModel $model;

    public function __construct()
    {
        $this->model = new PengaturanModel();
    }

    public function index()
    {
        $pengaturan = $this->model->get();

        return view('admin/pengaturan', [
            'title' => 'Pengaturan Situs',
            'form' => admin_pengaturan_form($pengaturan),
        ]);
    }

    public function update()
    {
        $current = $this->model->get();

        $rawMaps = trim((string) $this->request->getPost('google_maps_embed'));
        $mapsEmbed = '';
        if ($rawMaps !== '') {
            $mapsEmbed = sanitize_google_maps_embed($rawMaps);
            if ($mapsEmbed === null) {
                return redirect()->back()->withInput()->with('error', 'Format Google Maps Embed tidak valid. Gunakan iframe atau URL resmi Google Maps.');
            }
        }

        $data = [
            'nama_desa' => $this->request->getPost('nama_desa'),
            'tagline' => $this->request->getPost('tagline'),
            'deskripsi_singkat' => $this->request->getPost('deskripsi_singkat'),
            'alamat' => $this->request->getPost('alamat'),
            'no_whatsapp' => $this->request->getPost('no_whatsapp'),
            'email_kontak' => $this->request->getPost('email_kontak'),
            'instagram_url' => $this->request->getPost('instagram_url'),
            'tiktok_url' => $this->request->getPost('tiktok_url'),
            'facebook_url' => $this->request->getPost('facebook_url'),
            'google_maps_embed' => $mapsEmbed,
        ];

        $logo = ImageUploader::upload('logo', 'uploads/logo', 400);
        if ($logo) {
            ImageUploader::delete($current['logo'] ?? null);
            $data['logo'] = $logo;
        }

        if ($this->model->find(1)) {
            $this->model->update(1, $data);
        } else {
            $data['id'] = 1;
            $this->model->insert($data);
        }

        cache()->delete('pengaturan_situs');
        ActivityLogService::log('mengubah pengaturan situs', 'pengaturan_situs', 1);

        return redirect()->to(site_url('admin/pengaturan'))->with('success', 'Pengaturan berhasil disimpan.');
    }
}
