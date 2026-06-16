<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHariLiburTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'unique' => true,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->createTable('tb_hari_libur');
    }

    public function down()
    {
        $this->forge->dropTable('tb_hari_libur');
    }
}
