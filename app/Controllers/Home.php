<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\WisataModel;
use App\Models\GaleriModel;

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
            'carouselSlides' => $this->carouselSlides(),
            'youtubeId' => 'nUT9GVqArXU',
            'supporters' => $this->supporters(),
            'testimonials' => $this->testimonials(),
            'mapEmbed' => $this->mapEmbed($settings, $mapQuery),
            'mapsUrl' => 'https://www.google.com/maps/search/?api=1&query=' . $mapQuery,
            'alamatKunjungan' => $settings['alamat'] ?: 'Desa Binangun, Kec. Pandaan, Kab. Pasuruan, Jawa Timur',
        ]);
    }

    private function carouselSlides(): array
    {
        return [
            [
                'image' => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=1600&q=80',
                'caption' => 'Sawah terasering yang menghijau',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1600&q=80',
                'caption' => 'Warisan budaya yang hidup',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1600&q=80',
                'caption' => 'Pemandangan alam yang menenangkan',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1600&q=80',
                'caption' => 'Hutan dan udara segar pedesaan',
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
                'nama' => 'Rina Wulandari',
                'asal' => 'Yogyakarta',
                'teks' => 'Suasana Wisata Binangun sangat menenangkan. Warganya ramah, udaranya segar, dan pemandangannya indah. Pasti kembali lagi!',
                'foto' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
            ],
            [
                'nama' => 'Andi Pratama',
                'asal' => 'Jakarta',
                'teks' => 'Pengalaman wisata pedesaan yang otentik. Kuliner lokalnya enak, dan destinasi alamnya cocok untuk keluarga maupun fotografi.',
                'foto' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'asal' => 'Surabaya',
                'teks' => 'Budaya dan tradisinya masih terjaga. Anak-anak kami belajar banyak tentang kehidupan desa. Terima kasih Binangun!',
                'foto' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
            ],
            [
                'nama' => 'Budi Santoso',
                'asal' => 'Malang',
                'teks' => 'Perjalanan ke Binangun jadi liburan paling memorable tahun ini. Sawah teraseringnya instagramable, warga lokalnya sangat welcoming.',
                'foto' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
            ],
            [
                'nama' => 'Dewi Kartika',
                'asal' => 'Bandung',
                'teks' => 'Cocok untuk healing akhir pekan. Udara segar, suara alam, dan keramahan warga membuat kami betah berlama-lama di desa ini.',
                'foto' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
            ],
            [
                'nama' => 'Fajar Hidayat',
                'asal' => 'Sidoarjo',
                'teks' => 'Destinasi wisata yang belum terlalu ramai, justru itu kelebihannya. Bisa menikmati alam dengan tenang dan autentik.',
                'foto' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
                'rating' => 4,
            ],
            [
                'nama' => 'Maya Anggraini',
                'asal' => 'Semarang',
                'teks' => 'Kami ikut tur budaya dan belajar membatik serta memasak masakan tradisional. Pengalaman edukatif yang menyenangkan untuk seluruh keluarga.',
                'foto' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
            ],
            [
                'nama' => 'Rizky Aditya',
                'asal' => 'Denpasar',
                'teks' => 'Sebagai pecinta fotografi, Binangun punya banyak spot indah. Golden hour di persawahan adalah momen yang tak terlupakan.',
                'foto' => 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
            ],
            [
                'nama' => 'Lestari Putri',
                'asal' => 'Pasuruan',
                'teks' => 'Bangga ada desa wisata secantik ini di kabupaten kami. Sudah saya rekomendasikan ke teman-teman kantor dan semuanya puas.',
                'foto' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
            ],
            [
                'nama' => 'Hendra Wijaya',
                'asal' => 'Bekasi',
                'teks' => 'Akses jalan sudah bagus, fasilitas parkir cukup, dan pemandu wisata sangat informatif. Worth it untuk dikunjungi bareng teman-teman.',
                'foto' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=200&q=80',
                'rating' => 4,
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
