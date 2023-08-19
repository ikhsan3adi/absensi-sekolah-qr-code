<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuperadminField extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'is_superadmin' => [
                'type'           => 'TINYINT',
                'constraint'     => 1,
                'null'           => false,
                'default'        => 0,
                'after'          => 'username'
            ]
        ]);

        $this->forge->getConnection()->query("INSERT INTO users (email, username, is_superadmin, password_hash, active) VALUES (
            'adminsuper@gmail.com',
            'superadmin',
            1,
            '$2y\$10\$Ke39CFuF9n1qQCkkOxUSz.SctsyzMS1dL2qt85GviA1HJ2jLNRwhS',
            1)");
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'is_superadmin');
    }
}
