<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWorkingDays extends Migration
{
    public function up()
    {
        $this->forge->addColumn('general_settings', [
            'hari_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'default'    => '1,2,3,4,5',
                'after'      => 'jam_pulang_standard'
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('hari_kerja', 'general_settings')) {
            $this->forge->dropColumn('general_settings', 'hari_kerja');
        }
    }
}
