<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KehadiranSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_kehadiran' => 1,
                'kehadiran'    => 'Hadir',
            ],
            [
                'id_kehadiran' => 2,
                'kehadiran'    => 'Sakit',
            ],
            [
                'id_kehadiran' => 3,
                'kehadiran'    => 'Izin',
            ],
            [
                'id_kehadiran' => 4,
                'kehadiran'    => 'Tanpa keterangan',
            ],
        ];

        // Check if data already exists (prevent duplicates on re-seed)
        $existing = $this->db->table('tb_kehadiran')->countAllResults();

        if ($existing === 0) {
            // Using Query Builder for batch insert
            $this->db->table('tb_kehadiran')->insertBatch($data);

            // Reset AUTO_INCREMENT to 5 for next entries
            $this->db->query('ALTER TABLE tb_kehadiran AUTO_INCREMENT = 5');
        }
    }
}