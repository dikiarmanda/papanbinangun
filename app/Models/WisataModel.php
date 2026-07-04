<?php

namespace App\Models;

use CodeIgniter\Model;

class WisataModel extends Model
{
    protected $table = 'wisata';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nama',
        'slug',
        'deskripsi',
        'gambar_cover',
        'fasilitas',
        'google_maps_embed',
        'status',
        'admin_id',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPublished(int $limit = 0): array
    {
        $builder = $this->where('status', 'publish')->orderBy('created_at', 'DESC');

        if ($limit > 0) {
            return $builder->findAll($limit);
        }

        return $builder->findAll();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->where('status', 'publish')->first();
    }

    public function getWithAdmin(): array
    {
        return $this->select('wisata.*, admin_users.nama as penulis')
            ->join('admin_users', 'admin_users.id = wisata.admin_id', 'left')
            ->orderBy('wisata.created_at', 'DESC')
            ->findAll();
    }

    /**
     * @param array<int, array{nama: string, icon: string}> $items
     */
    public static function encodeFasilitas(array $items): ?string
    {
        $clean = [];

        foreach ($items as $item) {
            $nama = trim($item['nama'] ?? '');
            $icon = trim($item['icon'] ?? '');

            if ($nama === '' || $icon === '') {
                continue;
            }

            $clean[] = [
                'nama' => $nama,
                'icon' => preg_replace('/[^a-z0-9-]/', '', $icon) ?: 'fa-circle-info',
            ];
        }

        if ($clean === []) {
            return null;
        }

        return json_encode($clean, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array<int, array{nama: string, icon: string}>
     */
    public static function decodeFasilitas(null|string|array $value): array
    {
        if (is_array($value)) {
            return self::normalizeFasilitasList($value);
        }

        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? self::normalizeFasilitasList($decoded) : [];
    }

    /**
     * @param array<int, mixed> $items
     * @return array<int, array{nama: string, icon: string}>
     */
    private static function normalizeFasilitasList(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $nama = trim($item['nama'] ?? '');
            $icon = trim($item['icon'] ?? '');

            if ($nama === '' || $icon === '') {
                continue;
            }

            $result[] = [
                'nama' => $nama,
                'icon' => preg_replace('/[^a-z0-9-]/', '', $icon) ?: 'fa-circle-info',
            ];
        }

        return $result;
    }
}
