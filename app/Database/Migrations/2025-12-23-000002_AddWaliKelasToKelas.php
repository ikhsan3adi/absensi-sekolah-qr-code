<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaliKelasToKelas extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Add id_wali_kelas column to tb_kelas
        if (!$db->fieldExists('id_wali_kelas', 'tb_kelas')) {
            $this->forge->addColumn('tb_kelas', [
                'id_wali_kelas' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => false,
                    'null'       => true,
                    'after'      => 'index_kelas',
                ],
            ]);

            // Add foreign key for id_wali_kelas
            $this->forge->addForeignKey(
                'id_wali_kelas',
                'tb_guru',
                'id_guru',
                'SET NULL',
                'CASCADE',
                'tb_kelas_id_wali_kelas_foreign'
            );
        }

        // Add id_guru column to users table (Myth\Auth)
        if (!$db->fieldExists('id_guru', 'users')) {
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
            $this->forge->addForeignKey(
                'id_guru',
                'tb_guru',
                'id_guru',
                'CASCADE',
                'CASCADE',
                'users_id_guru_foreign'
            );
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Drop foreign keys first
        if ($db->fieldExists('id_guru', 'users')) {
            $this->forge->dropForeignKey('users', 'users_id_guru_foreign');
            $this->forge->dropColumn('users', 'id_guru');
        }

        if ($db->fieldExists('id_wali_kelas', 'tb_kelas')) {
            $this->forge->dropForeignKey('tb_kelas', 'tb_kelas_id_wali_kelas_foreign');
            $this->forge->dropColumn('tb_kelas', 'id_wali_kelas');
        }
    }
}
