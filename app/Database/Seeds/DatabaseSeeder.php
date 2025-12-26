<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Run seeders in order
        $this->call('KehadiranSeeder');
        $this->call('JurusanSeeder');
        $this->call('KelasSeeder');
        $this->call('SuperadminSeeder');
        $this->call('GeneralSettingsSeeder');
        
        // Optional: Uncomment if you want to seed sample data
        // $this->call('GuruSeeder');
        // $this->call('SiswaSeeder');
    }
}