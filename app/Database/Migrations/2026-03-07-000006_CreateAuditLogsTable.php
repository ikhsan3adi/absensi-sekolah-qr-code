<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'aksi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'tabel' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'id_record' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'data_lama' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_baru' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tb_audit_logs');
    }

    public function down()
    {
        $this->forge->dropTable('tb_audit_logs');
    }
}
