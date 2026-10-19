<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSewuDataTables extends Migration
{
    public function up()
    {
        // 1. Tabel Users
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'password'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama_lengkap'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'role'          => ['type' => 'ENUM', 'constraint' => ['admin', 'manajer'], 'default' => 'manajer'],
            'aktif'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // 2. Tabel Pedagang
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'aktif'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pedagang', true);

        // 3. Tabel Penjualan
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pedagang_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'bulan'         => ['type' => 'VARCHAR', 'constraint' => 20],
            'minggu_ke'     => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'keping'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'omzet'         => ['type' => 'BIGINT', 'constraint' => 20, 'default' => 0],
            'ppn'           => ['type' => 'BIGINT', 'constraint' => 20, 'default' => 0],
            'hasil_bersih'  => ['type' => 'BIGINT', 'constraint' => 20, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pedagang_id', 'pedagang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penjualan', true);
    }

    public function down()
    {
        $this->forge->dropTable('penjualan', true);
        $this->forge->dropTable('pedagang', true);
        $this->forge->dropTable('users', true);
    }
}