<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaliKelasToKelas extends Migration
{
    public function up()
    {
        // Add id_wali_kelas to tb_kelas
        $this->forge->addColumn('tb_kelas', [
            'id_wali_kelas' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'index_kelas',
            ],
        ]);

        // Add foreign key for id_wali_kelas
        $this->db->query('ALTER TABLE tb_kelas ADD CONSTRAINT tb_kelas_id_wali_kelas_foreign FOREIGN KEY (id_wali_kelas) REFERENCES tb_guru(id_guru) ON DELETE SET NULL ON UPDATE CASCADE');

        // Add id_guru to users (Myth\Auth table)
        // Note: The table name used by Myth\Auth is usually 'users'
        $this->forge->addColumn('users', [
            'id_guru' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'id',
            ],
        ]);

        // Add foreign key for id_guru in users table
        $this->db->query('ALTER TABLE users ADD CONSTRAINT users_id_guru_foreign FOREIGN KEY (id_guru) REFERENCES tb_guru(id_guru) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign keys first
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY users_id_guru_foreign');
        $this->db->query('ALTER TABLE tb_kelas DROP FOREIGN KEY tb_kelas_id_wali_kelas_foreign');

        // Drop columns
        $this->forge->dropColumn('tb_kelas', 'id_wali_kelas');
        $this->forge->dropColumn('users', 'id_guru');
    }
}
