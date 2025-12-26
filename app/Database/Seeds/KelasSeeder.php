<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Kelas X (4 jurusan: OTKP, BDP, AKL, RPL)
            ['tingkat' => 'X', 'id_jurusan' => 1, 'index_kelas' => 'A'],
            ['tingkat' => 'X', 'id_jurusan' => 2, 'index_kelas' => 'A'],
            ['tingkat' => 'X', 'id_jurusan' => 3, 'index_kelas' => 'A'],
            ['tingkat' => 'X', 'id_jurusan' => 4, 'index_kelas' => 'A'],
            
            // Kelas XI (4 jurusan: OTKP, BDP, AKL, RPL)
            ['tingkat' => 'XI', 'id_jurusan' => 1, 'index_kelas' => 'A'],
            ['tingkat' => 'XI', 'id_jurusan' => 2, 'index_kelas' => 'A'],
            ['tingkat' => 'XI', 'id_jurusan' => 3, 'index_kelas' => 'A'],
            ['tingkat' => 'XI', 'id_jurusan' => 4, 'index_kelas' => 'A'],
            
            // Kelas XII (4 jurusan: OTKP, BDP, AKL, RPL)
            ['tingkat' => 'XII', 'id_jurusan' => 1, 'index_kelas' => 'A'],
            ['tingkat' => 'XII', 'id_jurusan' => 2, 'index_kelas' => 'A'],
            ['tingkat' => 'XII', 'id_jurusan' => 3, 'index_kelas' => 'A'],
            ['tingkat' => 'XII', 'id_jurusan' => 4, 'index_kelas' => 'A'],
        ];

        // Using Query Builder for batch insert
        $this->db->table('tb_kelas')->insertBatch($data);
    }
}