<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriIdToGaleri extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('kategori_id', 'galeri')) {
            $this->forge->addColumn('galeri', [
                'kategori_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'gambar',
                ],
            ]);
        }

        $slugMap = [
            'alam'      => 'wisata-alam',
            'budaya'    => 'budaya-tradisi',
            'kuliner'   => 'kuliner',
            'kerajinan' => 'kerajinan-umkm',
        ];

        if ($this->db->fieldExists('kategori', 'galeri')) {
            $kategoriRows = $this->db->table('kategori_artikel')->get()->getResultArray();
            $idBySlug = [];

            foreach ($kategoriRows as $row) {
                $idBySlug[$row['slug']] = (int) $row['id'];
            }

            $galeriRows = $this->db->table('galeri')->get()->getResultArray();

            foreach ($galeriRows as $row) {
                $legacy = $row['kategori'] ?? '';
                $slug = $slugMap[$legacy] ?? null;
                $kategoriId = $slug && isset($idBySlug[$slug]) ? $idBySlug[$slug] : null;

                $this->db->table('galeri')
                    ->where('id', $row['id'])
                    ->update(['kategori_id' => $kategoriId]);
            }

            $this->forge->dropColumn('galeri', 'kategori');
        }

        $this->forge->addForeignKey('kategori_id', 'kategori_artikel', 'id', 'SET NULL', 'CASCADE', 'galeri_kategori_fk');
        $this->forge->processIndexes('galeri');
    }

    public function down()
    {
        $this->forge->dropForeignKey('galeri', 'galeri_kategori_fk');

        if (! $this->db->fieldExists('kategori', 'galeri')) {
            $this->forge->addColumn('galeri', [
                'kategori' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'lainnya',
                    'after'      => 'gambar',
                ],
            ]);
        }

        $reverseMap = [
            'wisata-alam'    => 'alam',
            'budaya-tradisi' => 'budaya',
            'kuliner'        => 'kuliner',
            'kerajinan-umkm' => 'kerajinan',
        ];

        $rows = $this->db->table('galeri')
            ->select('galeri.id, kategori_artikel.slug')
            ->join('kategori_artikel', 'kategori_artikel.id = galeri.kategori_id', 'left')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $legacy = $reverseMap[$row['slug'] ?? ''] ?? 'lainnya';

            $this->db->table('galeri')
                ->where('id', $row['id'])
                ->update(['kategori' => $legacy]);
        }

        if ($this->db->fieldExists('kategori_id', 'galeri')) {
            $this->forge->dropColumn('galeri', 'kategori_id');
        }
    }
}
