<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Password;

class SuperadminSeeder extends Seeder
{
    // Default superadmin credentials
    public static string $email = 'adminsuper@test.com';
    public static string $username = 'testsuperadmin';
    public static string $password = 'superadmintest';

    public function run(): void
    {
        // Hash the password
        $encryptedPassword = Password::hash(self::$password);

        // Prepare data
        $data = [
            'email'         => self::$email,
            'username'      => self::$username,
            'is_superadmin' => 1,
            'password_hash' => $encryptedPassword,
            'active'        => 1,
        ];
        
        $this->db->table('users')->insert($data);
    }
}
