<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRfidToSiswaGuru extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $fields = [
            'rfid_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'unique_code',
            ],
        ];

        // Add rfid_code column to tb_siswa    
        $this->forge->addColumn('tb_siswa', $fields);
        $this->forge->addKey('rfid_code');
        $this->db->query('CREATE INDEX idx_tb_siswa_rfid_code ON tb_siswa(rfid_code)');

        // Add rfid_code column to tb_guru
        $this->forge->addColumn('tb_guru', $fields);
        $this->forge->addKey('rfid_code');
        $this->db->query('CREATE INDEX idx_tb_guru_rfid_code ON tb_guru(rfid_code)');
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Drop indexes first
        if ($db->fieldExists('rfid_code', 'tb_siswa')) {
            $this->forge->dropColumn('tb_siswa', 'rfid_code');
        }

        if ($db->fieldExists('rfid_code', 'tb_guru')) {
            $this->forge->dropColumn('tb_guru', 'rfid_code');
        }
    }
}
