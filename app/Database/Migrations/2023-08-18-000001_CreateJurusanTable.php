<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJurusanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'jurusan' => [
                'type'           => 'VARCHAR',
                'constraint'     => 32,
                'null'           => false,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        // Unique key
        $this->forge->addUniqueKey('jurusan');

        $this->forge->createTable('tb_jurusan', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_jurusan', true);
    }
}