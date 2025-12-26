<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Password;

class SuperadminSeeder extends Seeder
{
    public function run()
    {
        // Default superadmin credentials
        $email = 'adminsuper@gmail.com';
        $username = 'superadmin';
        $password = 'superadmin';

        // Hash the password
        $encryptedPassword = Password::hash($password);

        // Prepare data
        $data = [
            'email'         => $email,
            'username'      => $username,
            'is_superadmin' => 1,
            'password_hash' => $encryptedPassword,
            'active'        => 1,
        ];

        // Check if superadmin already exists
        $existingSuperadmin = $this->db->table('users')
            ->where('username', $username)
            ->orWhere('email', $email)
            ->get()
            ->getRow();

        if (!$existingSuperadmin) {
            // Insert superadmin
            $this->db->table('users')->insert($data);
            
            echo "Superadmin created successfully!\n";
            echo "Username: {$username}\n";
            echo "Password: {$password}\n";
            echo "Email: {$email}\n";
        } else {
            echo "Superadmin already exists. Skipping...\n";
        }
    }
}