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

        // Check if superadmin already exists
        $existingSuperadmin = $userProvider->where('username', $username)
            ->orWhere('email', $email)
            ->first();

        if (!$existingSuperadmin) {
            // Create user entity
            $user = new User([
                'username' => $username,
                'email'    => $email,
                'password' => $password,
            ]);

            // Custom columns from myth auth / previous DB
            $user->is_superadmin = 1;
            $user->active = 1;

            // Save user
            $userProvider->save($user);

            // Get the user again to add to group
            $user = $userProvider->findById($userProvider->getInsertID());
            $user->addGroup('superadmin');
            
            echo "\nSuperadmin created successfully!\n";
            echo "Username: {$username}\n";
            echo "Password: {$password}\n";
            echo "Email: {$email}\n";
        } else {
            echo "Superadmin already exists. Skipping...\n";
        }
    }
}
