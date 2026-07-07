<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHeroBannersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'gambar' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'link_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'urutan' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['publish', 'draft'],
                'default'    => 'publish',
            ],
            'admin_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('admin_id', 'admin_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('hero_banners');
    }

    public function down()
    {
        $this->forge->dropTable('hero_banners');
    }
}
