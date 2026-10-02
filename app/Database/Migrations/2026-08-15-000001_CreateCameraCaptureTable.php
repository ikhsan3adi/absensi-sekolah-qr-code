<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCameraCaptureTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'filepath' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'entity_type' => [
                'type'       => 'ENUM',
                'constraint' => ['siswa', 'guru', 'umum'],
                'default'    => 'umum',
            ],
            'entity_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'entity_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'default'    => null,
            ],
            'captured_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
                'null'       => true,
                'default'    => null,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('entity_type');
        $this->forge->addKey('entity_id');
        $this->forge->addKey('captured_by');
        $this->forge->createTable('tb_camera_capture', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_camera_capture');
    }
}
