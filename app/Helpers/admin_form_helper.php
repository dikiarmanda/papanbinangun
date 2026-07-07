<?php

if (!function_exists('admin_form_raw')) {
    function admin_form_raw(string $key, mixed $default = ''): string
    {
        $value = old($key);

        return $value !== null ? (string) $value : (string) ($default ?? '');
    }
}

if (!function_exists('admin_form_value')) {
    function admin_form_value(string $key, mixed $default = ''): string
    {
        return esc(admin_form_raw($key, $default));
    }
}

if (!function_exists('admin_rich_text')) {
    function admin_rich_text(string $key, mixed $default = ''): string
    {
        $value = admin_form_raw($key, $default);

        return str_replace('</textarea>', '&lt;/textarea&gt;', $value);
    }
}

if (!function_exists('admin_crud_action')) {
    function admin_crud_action(string $route, ?int $id = null): string
    {
        return $id
            ? site_url($route . '/update/' . $id)
            : site_url($route . '/store');
    }
}

if (!function_exists('admin_select_options')) {
    /**
     * @param array<string, string> $labelsByValue
     *
     * @return list<array{value: string, label: string, selected: bool}>
     */
    function admin_select_options(array $labelsByValue, string $selected): array
    {
        $options = [];

        foreach ($labelsByValue as $value => $label) {
            $options[] = [
                'value' => (string) $value,
                'label' => (string) $label,
                'selected' => (string) $value === $selected,
            ];
        }

        return $options;
    }
}

if (!function_exists('admin_wisata_fasilitas_items')) {
    /**
     * @return list<array{nama: string, icon: string, previewIcon: string, iconOptions: list<array{value: string, label: string, selected: bool}>}>
     */
    function admin_wisata_fasilitas_items(array $saved = []): array
    {
        $items = [];

        if (old('fasilitas_nama') !== null) {
            foreach ((array) old('fasilitas_nama') as $i => $nama) {
                $items[] = [
                    'nama' => (string) $nama,
                    'icon' => (string) (old('fasilitas_icon')[$i] ?? ''),
                ];
            }
        } elseif ($saved !== []) {
            foreach ($saved as $item) {
                $items[] = [
                    'nama' => (string) ($item['nama'] ?? ''),
                    'icon' => (string) ($item['icon'] ?? ''),
                ];
            }
        }

        if ($items === []) {
            $items[] = ['nama' => '', 'icon' => ''];
        }

        $iconLabels = wisata_fasilitas_icons();

        return array_map(static function (array $item) use ($iconLabels) {
            $icon = $item['icon'];

            return [
                'nama' => $item['nama'],
                'icon' => $icon,
                'previewIcon' => $icon !== '' ? $icon : 'fa-circle-question',
                'iconOptions' => admin_select_options($iconLabels, $icon),
            ];
        }, $items);
    }
}

if (!function_exists('admin_wisata_index_rows')) {
    function admin_wisata_index_rows(array $wisata): array
    {
        return array_map(static function (array $row) {
            return [
                'id' => (int) $row['id'],
                'nama' => $row['nama'],
                'fasilitasCount' => count(parse_wisata_fasilitas($row['fasilitas'] ?? null)),
                'hasMap' => !empty($row['google_maps_embed']),
                'status' => $row['status'],
                'penulis' => $row['penulis'] ?? '-',
                'editUrl' => site_url('admin/wisata/edit/' . $row['id']),
                'deleteUrl' => site_url('admin/wisata/delete/' . $row['id']),
            ];
        }, $wisata);
    }
}

if (!function_exists('admin_artikel_index_rows')) {
    function admin_artikel_index_rows(array $artikel): array
    {
        return array_map(static function (array $row) {
            return [
                'id' => (int) $row['id'],
                'judul' => $row['judul'],
                'kategoriNama' => $row['kategori_nama'] ?? '-',
                'penulis' => $row['penulis'] ?? '-',
                'status' => $row['status'],
                'views' => (int) $row['views'],
                'editUrl' => site_url('admin/artikel/edit/' . $row['id']),
                'deleteUrl' => site_url('admin/artikel/delete/' . $row['id']),
            ];
        }, $artikel);
    }
}

if (!function_exists('admin_kategori_index_rows')) {
    function admin_kategori_index_rows(array $kategori): array
    {
        return array_map(static function (array $row) {
            return [
                'id' => (int) $row['id'],
                'nama' => $row['nama'],
                'slug' => $row['slug'],
                'updateUrl' => site_url('admin/kategori/update/' . $row['id']),
                'deleteUrl' => site_url('admin/kategori/delete/' . $row['id']),
            ];
        }, $kategori);
    }
}

if (!function_exists('admin_user_index_rows')) {
    function admin_user_index_rows(array $users, int $currentAdminId): array
    {
        return array_map(static function (array $row) use ($currentAdminId) {
            return [
                'id' => (int) $row['id'],
                'nama' => $row['nama'],
                'email' => $row['email'],
                'status' => $row['status'],
                'statusBadge' => $row['status'] === 'aktif' ? 'publish' : 'draft',
                'lastLogin' => $row['last_login_at'] ? format_tanggal($row['last_login_at']) : '—',
                'editUrl' => site_url('admin/users/edit/' . $row['id']),
                'deleteUrl' => site_url('admin/users/delete/' . $row['id']),
                'canDelete' => (int) $row['id'] !== $currentAdminId,
            ];
        }, $users);
    }
}

if (!function_exists('admin_galeri_kategori_options')) {
    function admin_galeri_kategori_options(): array
    {
        return admin_select_options([
            'alam' => 'Alam',
            'budaya' => 'Budaya',
            'kuliner' => 'Kuliner',
            'kerajinan' => 'Kerajinan',
            'lainnya' => 'Lainnya',
        ], 'alam');
    }
}

if (!function_exists('admin_wisata_select_options')) {
    function admin_wisata_select_options(array $wisata, string $selected = ''): array
    {
        $options = [['value' => '', 'label' => '— Tidak ada —', 'selected' => $selected === '']];

        foreach ($wisata as $row) {
            $value = (string) $row['id'];
            $options[] = [
                'value' => $value,
                'label' => (string) $row['nama'],
                'selected' => $value === $selected,
            ];
        }

        return $options;
    }
}

if (!function_exists('admin_kategori_select_options')) {
    function admin_kategori_select_options(array $kategori, string $selected = ''): array
    {
        $options = [['value' => '', 'label' => '— Pilih —', 'selected' => $selected === '']];

        foreach ($kategori as $row) {
            $value = (string) $row['id'];
            $options[] = [
                'value' => $value,
                'label' => (string) $row['nama'],
                'selected' => $value === $selected,
            ];
        }

        return $options;
    }
}

if (!function_exists('admin_galeri_index_items')) {
    function admin_galeri_index_items(array $galeri): array
    {
        return array_map(static function (array $row) {
            return [
                'id' => (int) $row['id'],
                'gambarUrl' => upload_url($row['gambar']),
                'judul' => $row['judul'] ?: 'Tanpa judul',
                'kategori' => $row['kategori'],
                'deleteUrl' => site_url('admin/galeri/delete/' . $row['id']),
            ];
        }, $galeri);
    }
}

if (!function_exists('admin_pengaturan_form')) {
    function admin_pengaturan_form(array $pengaturan): array
    {
        return [
            'action' => site_url('admin/pengaturan/update'),
            'logoUrl' => !empty($pengaturan['logo']) ? upload_url($pengaturan['logo']) : '',
            'fields' => [
                'nama_desa' => esc($pengaturan['nama_desa'] ?? ''),
                'tagline' => esc($pengaturan['tagline'] ?? ''),
                'deskripsi_singkat' => admin_rich_text('deskripsi_singkat', $pengaturan['deskripsi_singkat'] ?? ''),
                'alamat' => esc($pengaturan['alamat'] ?? ''),
                'no_whatsapp' => esc($pengaturan['no_whatsapp'] ?? ''),
                'email_kontak' => esc($pengaturan['email_kontak'] ?? ''),
                'instagram_url' => esc($pengaturan['instagram_url'] ?? ''),
                'tiktok_url' => esc($pengaturan['tiktok_url'] ?? ''),
                'facebook_url' => esc($pengaturan['facebook_url'] ?? ''),
                'google_maps_embed' => esc($pengaturan['google_maps_embed'] ?? ''),
            ],
        ];
    }
}

if (!function_exists('admin_dashboard_artikel_rows')) {
    function admin_dashboard_artikel_rows(array $artikel): array
    {
        return array_map(static function (array $row) {
            return [
                'id' => (int) $row['id'],
                'judul' => $row['judul'],
                'status' => $row['status'],
                'editUrl' => site_url('admin/artikel/edit/' . $row['id']),
                'createdAt' => format_tanggal($row['created_at']),
            ];
        }, $artikel);
    }
}

if (!function_exists('admin_dashboard_log_rows')) {
    function admin_dashboard_log_rows(array $logs): array
    {
        return array_map(static function (array $row) {
            return [
                'adminNama' => $row['admin_nama'] ?? 'Admin',
                'aksi' => $row['aksi'],
                'tanggal' => format_tanggal($row['created_at']),
                'jam' => date('H:i', strtotime($row['created_at'])),
            ];
        }, $logs);
    }
}

if (!function_exists('admin_banner_index_items')) {
    function admin_banner_index_items(array $banners): array
    {
        return array_map(static function (array $row) {
            return [
                'id' => (int) $row['id'],
                'gambarUrl' => banner_image_url($row['gambar']),
                'judul' => $row['judul'] ?: 'Tanpa judul',
                'linkUrl' => $row['link_url'] ?? '',
                'urutan' => (int) ($row['urutan'] ?? 0),
                'status' => $row['status'],
                'statusBadge' => $row['status'] === 'publish' ? 'publish' : 'draft',
                'updateUrl' => site_url('admin/banner/update/' . $row['id']),
                'deleteUrl' => site_url('admin/banner/delete/' . $row['id']),
            ];
        }, $banners);
    }
}
