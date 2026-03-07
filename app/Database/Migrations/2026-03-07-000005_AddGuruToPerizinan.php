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
        $this->db->query('ALTER TABLE tb_perizinan DROP FOREIGN KEY tb_perizinan_id_guru_foreign');
        $this->forge->dropColumn('tb_perizinan', 'id_guru');
        
        $this->forge->modifyColumn('tb_perizinan', [
            'id_siswa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);
    }
}
