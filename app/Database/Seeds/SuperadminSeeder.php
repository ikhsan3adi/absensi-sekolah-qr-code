<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class SuperadminSeeder extends Seeder
{
    public function run()
    {
        // Default superadmin credentials
        $email = 'adminsuper@gmail.com';
        $username = 'superadmin';
        $password = 'superadmin';

        $userProvider = auth()->getProvider();

        // Check if superadmin already exists by email or username
        $existingSuperadmin = $userProvider->findByCredentials(['email' => $email]);

        if (!$existingSuperadmin) {
            // Also check by username
            $existingSuperadmin = $userProvider->where('username', $username)->first();
        }

        if (!$existingSuperadmin) {
            // Create user entity
            $user = new User([
                'username' => $username,
                'email'    => $email,
                'password' => $password,
            ]);

            $user->active = 1;

            // Save user
            $userProvider->save($user);

            // Get the user again to add to groups
            $user = $userProvider->findById($userProvider->getInsertID());

            // Superadmin gets: superadmin (primary), admin (convenience)
            $user->addGroup('superadmin', 'admin');

            echo "\nSuperadmin created successfully!\n";
            echo "Username: {$username}\n";
            echo "Password: {$password}\n";
            echo "Email: {$email}\n";
        } else {
            echo "Superadmin already exists. Skipping...\n";
        }
    }
}
