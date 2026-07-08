<?php

namespace App\Models;

use CodeIgniter\Model;

class GaleriModel extends Model
{
    protected $table            = 'galeri';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul', 'gambar', 'wisata_id', 'admin_id',
    ];
    protected $useTimestamps = false;

    public function getAllWithRelations(?string $kategoriSlug = null): array
    {
        $builder = $this->select(
            'galeri.*, wisata.nama as wisata_nama, admin_users.nama as uploader',
        )
            ->join('wisata', 'wisata.id = galeri.wisata_id', 'left')
            ->join('admin_users', 'admin_users.id = galeri.admin_id', 'left')
            ->orderBy('galeri.created_at', 'DESC');

        if ($kategoriSlug !== null && $kategoriSlug !== '') {
            $sub = $this->db->table('galeri_kategori gk')
                ->select('gk.galeri_id')
                ->join('kategori_artikel ka', 'ka.id = gk.kategori_id')
                ->where('ka.slug', $kategoriSlug);

            $builder->whereIn('galeri.id', $sub);
        }

        $rows = $builder->findAll();
        $kategoriMap = $this->getKategoriByGaleriIds(array_column($rows, 'id'));

        foreach ($rows as &$row) {
            $list = $kategoriMap[(int) $row['id']] ?? [];
            $row['kategori_list'] = $list;
            $row['kategori_ids'] = array_column($list, 'id');
            $row['kategori_nama'] = $list !== []
                ? implode(', ', array_column($list, 'nama'))
                : '-';
        }

        return $rows;
    }

    /**
     * @param list<int> $galeriIds
     *
     * @return array<int, list<array{id: int, nama: string, slug: string}>>
     */
    public function getKategoriByGaleriIds(array $galeriIds): array
    {
        $galeriIds = array_values(array_unique(array_filter(array_map('intval', $galeriIds))));
        if ($galeriIds === []) {
            return [];
        }

        $rows = $this->db->table('galeri_kategori gk')
            ->select('gk.galeri_id, kategori_artikel.id, kategori_artikel.nama, kategori_artikel.slug')
            ->join('kategori_artikel', 'kategori_artikel.id = gk.kategori_id')
            ->whereIn('gk.galeri_id', $galeriIds)
            ->orderBy('kategori_artikel.nama', 'ASC')
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $galeriId = (int) $row['galeri_id'];
            $map[$galeriId][] = [
                'id'   => (int) $row['id'],
                'nama' => $row['nama'],
                'slug' => $row['slug'],
            ];
        }

        return $map;
    }

    /**
     * @param list<int|string> $kategoriIds
     */
    public function syncKategori(int $galeriId, array $kategoriIds): void
    {
        $kategoriIds = array_values(array_unique(array_filter(array_map('intval', $kategoriIds))));

        $this->db->table('galeri_kategori')->where('galeri_id', $galeriId)->delete();

        if ($kategoriIds === []) {
            return;
        }

        $batch = array_map(static fn (int $kategoriId) => [
            'galeri_id'   => $galeriId,
            'kategori_id' => $kategoriId,
        ], $kategoriIds);

        $this->db->table('galeri_kategori')->insertBatch($batch);
    }

    /**
     * @return list<int>
     */
    public function parseKategoriIdsFromRequest(mixed $input): array
    {
        if ($input === null || $input === '') {
            return [];
        }

        if (! is_array($input)) {
            $input = [$input];
        }

        return array_values(array_unique(array_filter(array_map('intval', $input))));
    }
}
