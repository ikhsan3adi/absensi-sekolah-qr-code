<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerizinanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_perizinan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_siswa' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
            ],
            'tipe_izin' => [
                'type'       => 'ENUM',
                'constraint' => ['Sakit', 'Izin'],
                'default'    => 'Sakit',
            ],
            'alasan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Disetujui', 'Ditolak'],
                'default'    => 'Pending',
            ],
            'id_petugas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_perizinan', true);
        $this->forge->addForeignKey('id_siswa', 'tb_siswa', 'id_siswa', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_perizinan');
    }

    public function down()
    {
        $this->forge->dropTable('tb_perizinan');
    }
}
