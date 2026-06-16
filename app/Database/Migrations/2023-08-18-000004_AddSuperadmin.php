<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * @@deprecated
 */
class AddSuperadmin extends Migration
{
    public function up()
    {
        // Add is_superadmin column to users table (legacy)
        $this->forge->addColumn('users', [
            'is_superadmin' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'username',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('is_superadmin', 'users')) {
            $this->forge->dropColumn('users', 'is_superadmin');
        }
    }
}
