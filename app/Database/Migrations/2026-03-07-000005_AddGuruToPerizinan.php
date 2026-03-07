<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGuruToPerizinan extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('tb_perizinan', [
            'id_siswa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);

        $this->forge->addColumn('tb_perizinan', [
            'id_guru' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_siswa'
            ],
        ]);

        $this->forge->addForeignKey('id_guru', 'tb_guru', 'id_guru', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Check if the foreign key exists before dropping
        $db = \Config\Database::connect();
        $result = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = '" . $db->getDatabase() . "' AND TABLE_NAME = 'tb_perizinan' AND CONSTRAINT_NAME = 'tb_perizinan_id_guru_foreign'")->getRow();

        if ($result) {
            $this->db->query('ALTER TABLE tb_perizinan DROP FOREIGN KEY tb_perizinan_id_guru_foreign');
        }

        if ($this->db->fieldExists('id_guru', 'tb_perizinan')) {
            $this->forge->dropColumn('tb_perizinan', 'id_guru');
        }
        
        $this->forge->modifyColumn('tb_perizinan', [
            'id_siswa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);
    }
}
