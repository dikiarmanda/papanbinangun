<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GaleriKategoriPivot extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('galeri_kategori')) {
            $this->forge->addField([
                'galeri_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                ],
                'kategori_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                ],
            ]);
            $this->forge->addKey(['galeri_id', 'kategori_id'], true);
            $this->forge->addForeignKey('galeri_id', 'galeri', 'id', 'CASCADE', 'CASCADE', 'gk_galeri_fk');
            $this->forge->addForeignKey('kategori_id', 'kategori_artikel', 'id', 'CASCADE', 'CASCADE', 'gk_kategori_fk');
            $this->forge->createTable('galeri_kategori');
        }

        if ($this->db->fieldExists('kategori_id', 'galeri')) {
            $rows = $this->db->table('galeri')
                ->where('kategori_id IS NOT NULL', null, false)
                ->where('kategori_id >', 0)
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $exists = $this->db->table('galeri_kategori')
                    ->where('galeri_id', $row['id'])
                    ->where('kategori_id', $row['kategori_id'])
                    ->countAllResults();

                if ($exists === 0) {
                    $this->db->table('galeri_kategori')->insert([
                        'galeri_id'   => $row['id'],
                        'kategori_id' => $row['kategori_id'],
                    ]);
                }
            }

            if ($this->db->fieldExists('kategori_id', 'galeri')) {
                $this->forge->dropForeignKey('galeri', 'galeri_kategori_fk');
                $this->forge->dropColumn('galeri', 'kategori_id');
            }
        }
    }

    public function down()
    {
        if (! $this->db->fieldExists('kategori_id', 'galeri')) {
            $this->forge->addColumn('galeri', [
                'kategori_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'gambar',
                ],
            ]);
        }

        if ($this->db->tableExists('galeri_kategori')) {
            $pivotRows = $this->db->table('galeri_kategori')
                ->orderBy('kategori_id', 'ASC')
                ->get()
                ->getResultArray();

            $firstByGaleri = [];
            foreach ($pivotRows as $row) {
                $galeriId = (int) $row['galeri_id'];
                if (! isset($firstByGaleri[$galeriId])) {
                    $firstByGaleri[$galeriId] = (int) $row['kategori_id'];
                }
            }

            foreach ($firstByGaleri as $galeriId => $kategoriId) {
                $this->db->table('galeri')
                    ->where('id', $galeriId)
                    ->update(['kategori_id' => $kategoriId]);
            }

            $this->forge->dropTable('galeri_kategori', true);
        }

        if ($this->db->fieldExists('kategori_id', 'galeri')) {
            $this->forge->addForeignKey('kategori_id', 'kategori_artikel', 'id', 'SET NULL', 'CASCADE', 'galeri_kategori_fk');
            $this->forge->processIndexes('galeri');
        }
    }
}
