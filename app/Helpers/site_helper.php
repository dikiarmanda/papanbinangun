<?php

use App\Models\PengaturanModel;

if (!function_exists('pengaturan')) {
    function pengaturan(): array
    {
        static $data = null;

        if ($data === null) {
            $data = (new PengaturanModel())->get();
        }

        return $data;
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);

        return trim($text, '-') ?: 'item';
    }
}

if (!function_exists('unsplash_dummy')) {
    /**
     * Gambar dummy dari Unsplash (tema alam / desa / budaya).
     */
    function unsplash_dummy(string $type = 'default', int $index = 0): string
    {
        $pool = [
            'wisata' => [
                'https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=800&q=80',
            ],
            'artikel' => [
                'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1528183429752-a97d0bf99b5a?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80',
            ],
            'galeri' => [
                'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1433086966358-54859d0ed716?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=800&q=80',
            ],
            'default' => [
                'https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        $images = $pool[$type] ?? $pool['default'];

        return $images[$index % count($images)];
    }
}

if (!function_exists('upload_url')) {
    function upload_url(?string $path, string $dummyType = 'default', int $dummyIndex = 0): string
    {
        if (!$path) {
            return unsplash_dummy($dummyType, $dummyIndex);
        }

        return base_url($path);
    }
}

if (!function_exists('banner_image_url')) {
    function banner_image_url(?string $path): string
    {
        if (!$path) {
            return unsplash_dummy('wisata', 0);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return base_url($path);
    }
}


if (!function_exists('vendor_url')) {
    function vendor_url(string $path): string
    {
        return base_url('assets/vendor/' . ltrim($path, '/'));
    }
}

if (!function_exists('wa_link')) {
    function wa_link(?string $number, string $message = ''): string
    {
        $number = preg_replace('/[^0-9]/', '', $number ?? '');
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        $url = 'https://wa.me/' . $number;
        if ($message !== '') {
            $url .= '?text=' . rawurlencode($message);
        }

        return $url;
    }
}

if (!function_exists('brand_favicon')) {
    function brand_favicon(string $type = 'svg'): string
    {
        return match ($type) {
            'png' => base_url('assets/images/favicon-32.png'),
            'apple' => base_url('assets/images/apple-touch-icon.png'),
            default => base_url('assets/images/favicon.svg'),
        };
    }
}

if (!function_exists('brand_og_image')) {
    function brand_og_image(): string
    {
        return base_url('assets/images/og-image.png');
    }
}

if (!function_exists('artikel_ringkasan_from_konten')) {
    function artikel_ringkasan_from_konten(?string $konten, int $max = 300): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($konten ?? '')));
        if ($text === '') {
            return '';
        }

        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $max - 1)) . '…';
    }
}

if (!function_exists('wisata_fasilitas_icons')) {
    /** Daftar ikon fasilitas wisata untuk form admin. */
    function wisata_fasilitas_icons(): array
    {
        return [
            'fa-square-parking' => 'Parkir',
            'fa-car' => 'Parkir Mobil',
            'fa-motorcycle' => 'Parkir Motor',
            'fa-restroom' => 'Toilet',
            'fa-faucet-drip' => 'Air / Keran',
            'fa-utensils' => 'Warung / Makan',
            'fa-mug-hot' => 'Kafe / Minuman',
            'fa-store' => 'Toko / Kios',
            'fa-shop' => 'Souvenir',
            'fa-wifi' => 'WiFi',
            'fa-plug' => 'Colokan Listrik',
            'fa-bolt' => 'Listrik',
            'fa-charging-station' => 'Pengisian Kendaraan',
            'fa-mosque' => 'Musholla',
            'fa-place-of-worship' => 'Tempat Ibadah',
            'fa-bed' => 'Penginapan / Homestay',
            'fa-house-chimney' => 'Homestay',
            'fa-tent' => 'Area Camping',
            'fa-campground' => 'Perkemahan',
            'fa-umbrella' => 'Shelter / Tempat Berteduh',
            'fa-table-picnic' => 'Area Piknik',
            'fa-camera' => 'Spot Foto',
            'fa-tree' => 'Area Hijau',
            'fa-fish' => 'Spot Memancing',
            'fa-water' => 'Sungai',
            'fa-swimming-pool' => 'Kolam Renang',
            'fa-volleyball' => 'Olahraga / Lapangan',
            'fa-masks-theater' => 'Pertunjukan Budaya',
            'fa-drum' => 'Kesenian Lokal',
            'fa-graduation-cap' => 'Edukasi / Wisata Edukasi',
            'fa-money-bill' => 'ATM / Uang Tunai',
            'fa-credit-card' => 'Pembayaran Digital',
            'fa-first-aid' => 'P3K',
            'fa-fire-extinguisher' => 'APAR',
            'fa-life-ring' => 'Pelampung / Keselamatan Air',
            'fa-shield-heart' => 'Keamanan',
            'fa-phone' => 'Telepon Darurat',
            'fa-user-tie' => 'Tour Guide',
            'fa-child' => 'Ramah Anak',
            'fa-paw' => 'Pet Friendly',
            'fa-wheelchair' => 'Akses Difabel',
            'fa-smoking-ban' => 'Area Bebas Rokok',
            'fa-recycle' => 'Tempat Sampah / Ramah Lingkungan',
            'fa-bus' => 'Shuttle / Transport',
            'fa-van-shuttle' => 'Antar-Jemput',
        ];
    }
}

if (!function_exists('parse_wisata_fasilitas')) {
    /**
     * @return array<int, array{nama: string, icon: string}>
     */
    function parse_wisata_fasilitas(null|string|array $value): array
    {
        return \App\Models\WisataModel::decodeFasilitas($value);
    }
}

if (!function_exists('is_superadmin')) {
    function is_superadmin(): bool
    {
        return session()->get('admin_role') === 'superadmin';
    }
}

if (!function_exists('format_tanggal')) {
    function format_tanggal(?string $datetime): string
    {
        if (!$datetime) {
            return '-';
        }

        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        $ts = strtotime($datetime);
        $d = (int) date('j', $ts);
        $m = (int) date('n', $ts);
        $y = date('Y', $ts);

        return "{$d} {$bulan[$m]} {$y}";
    }
}
