<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Myth\Auth\Password;

class AddSuperadmin extends Migration
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

        // INSERT INITIAL SUPERADMIN
        $email = 'adminsuper@gmail.com';
        $username = 'superadmin';
        $password = 'superadmin';

        $encryptedPassword = Password::hash($password);

        $this->forge->getConnection()->query(
            "INSERT INTO users (email, username, is_superadmin, password_hash, active) 
            VALUES ('$email', '$username', 1, '$encryptedPassword', 1)"
        );
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'is_superadmin');
    }
}
