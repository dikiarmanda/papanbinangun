<?php

namespace App\Controllers;

use App\Models\WisataModel;
use App\Models\GaleriModel;

class WisataController extends BaseController
{
    public function index()
    {
        return view('wisata/index', [
            'title' => 'Destinasi Wisata',
            'meta_description' => 'Jelajahi destinasi wisata desa',
            'wisata' => (new WisataModel())->getPublished(),
            'pengaturan' => pengaturan(),
        ]);
    }

    public function detail(string $slug)
    {
        $wisata = (new WisataModel())->findBySlug($slug);

        if (!$wisata) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $galeri = (new GaleriModel())
            ->where('wisata_id', $wisata['id'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $fasilitas = WisataModel::decodeFasilitas($wisata['fasilitas'] ?? null);
        $homestayKamar = $this->hasHomestayFasilitas($fasilitas) ? $this->dummyHomestayKamar() : [];

        return view('wisata/detail', [
            'title' => $wisata['nama'],
            'meta_description' => mb_substr(strip_tags($wisata['deskripsi']), 0, 160),
            'meta_image' => $wisata['gambar_cover'] ? base_url($wisata['gambar_cover']) : null,
            'wisata' => $wisata,
            'fasilitas' => $fasilitas,
            'galeri' => $galeri,
            'homestayKamar' => $homestayKamar,
            'pengaturan' => pengaturan(),
        ]);
    }

    /**
     * Data dummy kamar homestay.
     *
     * @return array<int, array>
     */
    private function dummyHomestayKamar(): array
    {
        return [
            [
                'nama_kamar' => 'Kamar Deluxe',
                'harga' => 150000,
                'kapasitas' => 2,
                'deskripsi' => 'Kamar nyaman dengan kasur double, AC, dan kamar mandi dalam.',
                'gambar' => null,
            ],
            [
                'nama_kamar' => 'Kamar Standard',
                'harga' => 100000,
                'kapasitas' => 2,
                'deskripsi' => 'Kamar sederhana dengan kipas angin, kasur single, kamar mandi luar.',
                'gambar' => null,
            ],
            [
                'nama_kamar' => 'Kamar VIP',
                'harga' => 250000,
                'kapasitas' => 4,
                'deskripsi' => 'Suite keluarga dengan 2 kamar tidur, AC, TV, kamar mandi dalam, dan ruang tamu.',
                'gambar' => null,
            ],
        ];
    }

    /**
     * Deteksi apakah wisata memiliki fasilitas homestay.
     *
     * @param array<int, array{nama: string, icon: string}> $fasilitas
     */
    private function hasHomestayFasilitas(array $fasilitas): bool
    {
        $homestayIcons = ['fa-bed', 'fa-house-chimney'];

        foreach ($fasilitas as $f) {
            if (in_array($f['icon'], $homestayIcons, true)) {
                return true;
            }
        }

        return false;
    }
}