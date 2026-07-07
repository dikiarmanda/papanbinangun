<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\WisataModel;
use App\Models\GaleriModel;
use App\Models\HeroBannerModel;

class Home extends BaseController
{
    public function index()
    {
        $settings = pengaturan();
        $mapQuery = 'Desa+Binangun+Pandaan+Pasuruan+Jawa+Timur';

        return view('home', [
            'title' => $settings['nama_desa'],
            'meta_description' => strip_tags($settings['deskripsi_singkat'] ?? $settings['tagline']),
            'pengaturan' => $settings,
            'wisata' => (new WisataModel())->getPublished(6),
            'artikel' => (new ArtikelModel())->getPublished(3),
            'galeri' => (new GaleriModel())->orderBy('created_at', 'DESC')->findAll(6),
            'heroBanners' => $this->heroBanners(),
            'youtubeId' => 'nUT9GVqArXU',
            'supporters' => $this->supporters(),
            'testimonials' => $this->testimonials(),
            'mapEmbed' => $this->mapEmbed($settings, $mapQuery),
            'mapsUrl' => 'https://www.google.com/maps/search/?api=1&query=' . $mapQuery,
            'alamatKunjungan' => $settings['alamat'] ?: 'Desa Binangun, Kec. Pandaan, Kab. Pasuruan, Jawa Timur',
        ]);
    }

    private function heroBanners(): array
    {
        $banners = (new HeroBannerModel())->getPublished();

        if ($banners !== []) {
            return $banners;
        }

        return [
            [
                'id' => 0,
                'judul' => 'Sawah terasering yang menghijau',
                'gambar' => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=1400&q=80',
                'link_url' => null,
            ],
            [
                'id' => 0,
                'judul' => 'Warisan budaya yang hidup',
                'gambar' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1400&q=80',
                'link_url' => null,
            ],
            [
                'id' => 0,
                'judul' => 'Pemandangan alam yang menenangkan',
                'gambar' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1400&q=80',
                'link_url' => null,
            ],
        ];
    }

    private function supporters(): array
    {
        return [
            [
                'nama' => 'Pemerintah Kabupaten Pasuruan',
                'logo' => 'img/support/pasuruan.png',
                'url' => 'https://www.pasuruankab.go.id/',
            ],
            [
                'nama' => 'Diktisaintek Berdampak',
                'logo' => 'img/support/diktisaintek.png',
                'url' => 'https://kemdiktisaintek.go.id/library/book/diktisaintek-berdampak',
            ],
            [
                'nama' => 'Universitas Muhammadiyah Sidoarjo',
                'logo' => 'img/support/umsida-brand.png',
                'url' => 'https://umsida.ac.id/',
            ],
        ];
    }

    private function testimonials(): array
    {
        return [
            [
                'nama' => 'Maulidia Zahro',
                'teks' => 'Wisata yg recomended. Tiket @5 k per orang, renang @5 k per orang. Minuman 5-10rb an, makanan ada indomie. Tracking kids friendly ke air terjun kurleb sekitar 10-15 mnt. Byk kamar mandi. Byk lesehan.',
                'foto' => base_url('img/testimonials/1.png'),
                'rating' => 5,
            ],
            [
                'nama' => 'Ema Rahma',
                'teks' => 'Tempat wisata yg sangat murah meriah HTM cuma 5k aja udah bisa menikmati air terjun yg indah udah bisa healing di sini selai air terjun di pintu masuk juga ada kolam renangnya. AQ kesini udah di jam 4 sorean udah mau tutup jadi pengunjungnya udah tinggal beberap aja.',
                'foto' => base_url('img/testimonials/2.png'),
                'rating' => 5,
            ],
            [
                'nama' => 'Indah Bonita',
                'teks' => 'Masyaallah. Nemuin tempat hidden gemm yang murceee sejukk nyamannn. Semogaaa makinn rameeee pengunjung 🍃😍',
                'foto' => base_url('img/testimonials/3.png'),
                'rating' => 5,
            ],
            [
                'nama' => 'Selly Rizkiyah',
                'teks' => 'Wisata yg underrated kayaknya, krn masih sepi, yg jual di dalem jg cuma satu. Airnya jernih, dingin, banyak muaranya, ada mushollanya. Cuma mungkin bs dikembangkan lagi. Tiketnya 5 rb, wajar aja. Overall okee lah.',
                'foto' => base_url('img/testimonials/4.png'),
                'rating' => 5,
            ],
            [
                'nama' => 'Fany Wardana',
                'teks' => 'Tempat nya bagus asri... Cocok untuk yang ingin menikmati suasana pedesaan dengan bermain air sungai. Sistem jual beli disini yang menarik, karena transaksinya menggunakan kepingan bambu, satu keping senilai 2rb rupiah, makanan tradisional, murah meriah. Disediakan tikar bambu untuk tempat duduk, disarankan membawa sendiri alas tikar untuk jaga-jaga apabila tikar yang disediakan habis. Ada beberapa spot air terjun.',
                'foto' => base_url('img/testimonials/5.png'),
                'rating' => 5,
            ],
            [
                'nama' => 'Santynov19',
                'teks' => 'Banyak di jual makanan tradisional, alat jual beli pake koin, tempat nya sejuk segar rindang, ada sungai.',
                'foto' => base_url('img/testimonials/6.png'),
                'rating' => 5,
            ],
        ];
    }

    private function mapEmbed(array $settings, string $mapQuery): string
    {
        $embed = $settings['google_maps_embed'] ?? '';

        if ($embed !== '' && $embed !== null) {
            return $embed;
        }

        return '<iframe src="https://maps.google.com/maps?q=' . $mapQuery . '&t=&z=14&ie=UTF8&iwloc=&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen title="Peta Wisata Binangun"></iframe>';
    }
}
