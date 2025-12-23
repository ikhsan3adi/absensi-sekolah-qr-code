<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRfidToSiswaGuru extends Migration
{
    public function up()
    {
        $fields = [
            'rfid_code' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'unique_code',
                'default' => null,
            ],
        ];

        // Add column to tb_siswa
        $this->forge->addColumn('tb_siswa', $fields);
        // Add index for faster lookup
        $this->db->query('ALTER TABLE tb_siswa ADD INDEX(rfid_code)');

        // Add column to tb_guru
        $this->forge->addColumn('tb_guru', $fields);
        // Add index
        $this->db->query('ALTER TABLE tb_guru ADD INDEX(rfid_code)');
    }

    public function down()
    {
        $this->forge->dropColumn('tb_siswa', 'rfid_code');
        $this->forge->dropColumn('tb_guru', 'rfid_code');
    }
}
