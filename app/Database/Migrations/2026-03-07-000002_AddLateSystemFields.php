<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLateSystemFields extends Migration
{
    public function up()
    {
        // Add jam_masuk_limit to general_settings
        $this->forge->addColumn('general_settings', [
            'jam_masuk_limit' => [
                'type'    => 'TIME',
                'null'    => true,
                'default' => '07:00:00',
                'after'   => 'school_year'
            ],
        ]);

        // Add poin_pelanggaran to tb_siswa
        $this->forge->addColumn('tb_siswa', [
            'poin_pelanggaran' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'no_hp'
            ],
        ]);

        // Add menit_keterlambatan to tb_presensi_siswa
        $this->forge->addColumn('tb_presensi_siswa', [
            'menit_keterlambatan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'id_kehadiran'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('general_settings', 'jam_masuk_limit');
        $this->forge->dropColumn('tb_siswa', 'poin_pelanggaran');
        $this->forge->dropColumn('tb_presensi_siswa', 'menit_keterlambatan');
    }
}
