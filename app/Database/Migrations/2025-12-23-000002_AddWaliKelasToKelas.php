<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaliKelasToKelas extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Add id_wali_kelas column to tb_kelas
        $this->forge->addColumn('tb_kelas', [
            'id_wali_kelas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,
                'after'      => 'index_kelas',
            ],
        ]);

        // Add foreign key for id_wali_kelas to tb_kelas
        $this->db->query('ALTER TABLE tb_kelas ADD CONSTRAINT fk_tb_kelas_id_wali_kelas FOREIGN KEY (id_wali_kelas) REFERENCES tb_guru(id_guru) ON UPDATE NO ACTION ON DELETE RESTRICT');

        // Add id_guru column to users table
        $this->forge->addColumn('users', [
            'id_guru' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        // Add foreign key for id_guru in users table
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_id_guru FOREIGN KEY (id_guru) REFERENCES tb_guru(id_guru) ON UPDATE NO ACTION ON DELETE RESTRICT');
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Drop foreign key for users table if exists
        $checkUserFK = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = '" . $db->getDatabase() . "' AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'fk_users_id_guru'")->getRow();
        if ($checkUserFK) {
            $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_id_guru');
        }

        if ($this->db->fieldExists('id_guru', 'users')) {
            $this->forge->dropColumn('users', 'id_guru');
        }

        // Drop foreign key for tb_kelas table if exists
        $checkKelasFK = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = '" . $db->getDatabase() . "' AND TABLE_NAME = 'tb_kelas' AND CONSTRAINT_NAME = 'fk_tb_kelas_id_wali_kelas'")->getRow();
        if ($checkKelasFK) {
            $this->db->query('ALTER TABLE tb_kelas DROP FOREIGN KEY fk_tb_kelas_id_wali_kelas');
        }

        if ($this->db->fieldExists('id_wali_kelas', 'tb_kelas')) {
            $this->forge->dropColumn('tb_kelas', 'id_wali_kelas');
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
