<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run()
    {
        // Check if data already exists (prevent duplicates on re-seed)
        $existing = $this->db->table('tb_jurusan')->countAllResults();

        if ($existing === 0) {
            $data = [
                ['jurusan' => 'OTKP'],
                ['jurusan' => 'BDP'],
                ['jurusan' => 'AKL'],
                ['jurusan' => 'RPL'],
            ];

            // Using Query Builder for batch insert
            $this->db->table('tb_jurusan')->insertBatch($data);
        }
    }
}