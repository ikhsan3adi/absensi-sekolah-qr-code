<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJamPulangStandard extends Migration
{
    public function up()
    {
        $this->forge->addColumn('general_settings', [
            'jam_pulang_standard' => [
                'type'    => 'TIME',
                'null'    => true,
                'default' => '14:00:00',
                'after'   => 'jam_masuk_limit'
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('jam_pulang_standard', 'general_settings')) {
            $this->forge->dropColumn('general_settings', 'jam_pulang_standard');
        }
    }
}
