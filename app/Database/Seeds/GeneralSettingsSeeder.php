<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GeneralSettingsSeeder extends Seeder
{
    public function run()
    {
        // Default general settings
        $data = [
            'school_name' => 'SMK 1 Indonesia',
            'school_year' => '2024/2025',
            'copyright'   => '© 2025 All rights reserved.',
            'logo'        => null,
        ];

        // Check if settings already exist
        $existingSettings = $this->db->table('general_settings')
            ->get()
            ->getRow();

        if (!$existingSettings) {
            // Insert default settings
            $this->db->table('general_settings')->insert($data);
            
            echo "\nGeneral settings created successfully!\n";
            echo "School Name: {$data['school_name']}\n";
            echo "School Year: {$data['school_year']}\n";
        } else {
            echo "General settings already exist. Skipping...\n";
        }
    }
}
