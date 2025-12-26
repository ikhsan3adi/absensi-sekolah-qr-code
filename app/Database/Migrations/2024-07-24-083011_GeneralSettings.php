<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GeneralSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 225,
                'null'       => true,
            ],
            'school_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 225,
                'null'       => true,
                'default'    => 'SMK 1 Indonesia',
            ],
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 225,
                'null'       => true,
                'default'    => '2024/2025',
            ],
            'copyright' => [
                'type'       => 'VARCHAR',
                'constraint' => 225,
                'null'       => true,
                'default'    => '© 2025 All rights reserved.',
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        $this->forge->createTable('general_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('general_settings', true);
    }
}