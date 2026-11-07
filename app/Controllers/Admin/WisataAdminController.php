<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WisataModel;
use App\Libraries\ActivityLogService;
use App\Libraries\ImageUploader;
use App\Traits\HasUniqueSlug;

class WisataAdminController extends BaseController
{
    use HasUniqueSlug;

    protected WisataModel $model;

    public function __construct()
    {
        $this->model = new WisataModel();
    }

    public function index()
    {
        $wisata = $this->model->getWithAdmin();

        return view('admin/wisata/index', [
            'title' => 'Kelola Wisata',
            'rows' => admin_wisata_index_rows($wisata),
        ]);
    }

    public function create()
    {
        return view('admin/wisata/form', $this->formViewData(null, []));
    }

    public function store()
    {
        $nama = $this->request->getPost('nama');
        $slug = $this->request->getPost('slug') ?: slugify($nama);
        $status = $this->request->getPost('status') ?: 'draft';
        $slug = $this->uniqueSlug($slug);

        $rawMaps = trim((string) $this->request->getPost('google_maps_embed'));
        $mapsEmbed = null;
        if ($rawMaps !== '') {
            $mapsEmbed = sanitize_google_maps_embed($rawMaps);
            if ($mapsEmbed === null) {
                return redirect()->back()->withInput()->with('error', 'Format Google Maps Embed tidak valid. Gunakan iframe atau URL resmi Google Maps.');
            }
        }

        $data = [
            'nama' => $nama,
            'slug' => $slug,
            'deskripsi' => $this->request->getPost('deskripsi'),
            'fasilitas' => $this->collectFasilitas(),
            'google_maps_embed' => $mapsEmbed,
            'status' => $status,
            'admin_id' => session()->get('admin_id'),
        ];

        $cover = ImageUploader::upload('gambar_cover', 'uploads/wisata');
        if ($cover) {
            $data['gambar_cover'] = $cover;
        }

        $id = $this->model->insert($data);
        ActivityLogService::log('membuat destinasi wisata: ' . $nama, 'wisata', (int) $id);

        return redirect()->to(site_url('admin/wisata'))->with('success', 'Destinasi berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $wisata = $this->model->find($id);
        if (!$wisata) {
            return redirect()->to(site_url('admin/wisata'))->with('error', 'Destinasi tidak ditemukan.');
        }

        return view('admin/wisata/form', $this->formViewData(
            $wisata,
            WisataModel::decodeFasilitas($wisata['fasilitas'] ?? null),
        ));
    }

    public function update(int $id)
    {
        $wisata = $this->model->find($id);
        if (!$wisata) {
            return redirect()->to(site_url('admin/wisata'))->with('error', 'Destinasi tidak ditemukan.');
        }

        $nama = $this->request->getPost('nama');
        $slug = $this->request->getPost('slug') ?: slugify($nama);

        if ($slug !== $wisata['slug']) {
            $slug = $this->uniqueSlug($slug, $id);
        }

        $rawMaps = trim((string) $this->request->getPost('google_maps_embed'));
        $mapsEmbed = null;
        if ($rawMaps !== '') {
            $mapsEmbed = sanitize_google_maps_embed($rawMaps);
            if ($mapsEmbed === null) {
                return redirect()->back()->withInput()->with('error', 'Format Google Maps Embed tidak valid. Gunakan iframe atau URL resmi Google Maps.');
            }
        }

        $data = [
            'nama' => $nama,
            'slug' => $slug,
            'deskripsi' => $this->request->getPost('deskripsi'),
            'fasilitas' => $this->collectFasilitas(),
            'google_maps_embed' => $mapsEmbed,
            'status' => $this->request->getPost('status') ?: 'draft',
        ];

        $cover = ImageUploader::upload('gambar_cover', 'uploads/wisata');
        if ($cover) {
            ImageUploader::delete($wisata['gambar_cover']);
            $data['gambar_cover'] = $cover;
        }

        $this->model->update($id, $data);
        ActivityLogService::log('mengubah destinasi wisata: ' . $nama, 'wisata', $id);

        return redirect()->to(site_url('admin/wisata'))->with('success', 'Destinasi berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $wisata = $this->model->find($id);
        if ($wisata) {
            ImageUploader::delete($wisata['gambar_cover']);
            $this->model->delete($id);
            ActivityLogService::log('menghapus destinasi wisata: ' . $wisata['nama'], 'wisata', $id);
        }

        return redirect()->to(site_url('admin/wisata'))->with('success', 'Destinasi berhasil dihapus.');
    }

    private function formViewData(?array $wisata, array $fasilitas): array
    {
        $id = isset($wisata['id']) ? (int) $wisata['id'] : null;

        return [
            'title' => $wisata ? 'Edit Destinasi' : 'Tambah Destinasi',
            'form' => [
                'action' => admin_crud_action('admin/wisata', $id),
                'backUrl' => site_url('admin/wisata'),
                'nama' => admin_form_value('nama', $wisata['nama'] ?? ''),
                'slug' => admin_form_value('slug', $wisata['slug'] ?? ''),
                'deskripsi' => admin_rich_text('deskripsi', $wisata['deskripsi'] ?? ''),
                'googleMapsEmbed' => admin_form_value('google_maps_embed', $wisata['google_maps_embed'] ?? ''),
                'coverUrl' => !empty($wisata['gambar_cover']) ? upload_url($wisata['gambar_cover']) : '',
                'statusOptions' => admin_select_options([
                    'draft' => 'Draft',
                    'publish' => 'Publish',
                ], admin_form_raw('status', $wisata['status'] ?? 'draft')),
                'fasilitasItems' => admin_wisata_fasilitas_items($fasilitas),
                'fasilitasIconsJson' => esc(json_encode(wisata_fasilitas_icons(), JSON_UNESCAPED_UNICODE), 'attr'),
            ],
        ];
    }

    private function collectFasilitas(): ?string
    {
        $names = $this->request->getPost('fasilitas_nama') ?? [];
        $icons = $this->request->getPost('fasilitas_icon') ?? [];
        $items = [];

        if (!is_array($names) || !is_array($icons)) {
            return null;
        }

        foreach ($names as $i => $nama) {
            $items[] = [
                'nama' => (string) $nama,
                'icon' => (string) ($icons[$i] ?? ''),
            ];
        }

        return WisataModel::encodeFasilitas($items);
    }
}
