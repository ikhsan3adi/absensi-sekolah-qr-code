<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class SuperadminSeeder extends Seeder
{
    // Default superadmin credentials
    public static string $email = 'adminsuper@test.com';
    public static string $username = 'testsuperadmin';
    public static string $password = 'superadmintest';

    public function run(): void
    {
        $userProvider = auth()->getProvider();

        // Check if superadmin already exists by email or username
        $existing = $userProvider->findByCredentials(['email' => self::$email]);

        if (!$existing) {
            $existing = $userProvider->where('username', self::$username)->first();
        }

        if (!$existing) {
            // Create user using Shield
            $user = new User([
                'username' => self::$username,
                'email'    => self::$email,
                'password' => self::$password,
            ]);
            $user->active = 1;

            $userProvider->save($user);

            // Add to groups
            $user = $userProvider->findById($userProvider->getInsertID());
            $user->addGroup('superadmin', 'admin');
        }
    }
}
