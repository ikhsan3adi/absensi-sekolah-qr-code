<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDB extends Migration
{
    public function up()
    {
        // ====================================
        // Table: tb_kehadiran
        // ====================================
        $this->forge->addField([
            'id_kehadiran' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'kehadiran' => [
                'type'       => 'ENUM',
                'constraint' => ['Hadir', 'Sakit', 'Izin', 'Tanpa keterangan'],
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_kehadiran', true);
        $this->forge->createTable('tb_kehadiran', true);

        // ====================================
        // Table: tb_guru
        // ====================================
        $this->forge->addField([
            'id_guru' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'nuptk' => [
                'type'       => 'VARCHAR',
                'constraint' => 24,
                'null'       => false,
            ],
            'nama_guru' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['Laki-laki', 'Perempuan'],
                'null'       => false,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => false,
            ],
            'unique_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_guru', true);
        $this->forge->addUniqueKey('unique_code');
        $this->forge->createTable('tb_guru', true);

        // ====================================
        // Table: tb_siswa
        // ====================================
        $this->forge->addField([
            'id_siswa' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'nis' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => false,
            ],
            'nama_siswa' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'id_kelas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['Laki-laki', 'Perempuan'],
                'null'       => false,
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => false,
            ],
            'unique_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_siswa', true);
        $this->forge->addUniqueKey('unique_code');
        $this->forge->addKey('id_kelas');
        $this->forge->addForeignKey('id_kelas', 'tb_kelas', 'id_kelas', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('tb_siswa', true);

        // ====================================
        // Table: tb_presensi_guru
        // ====================================
        $this->forge->addField([
            'id_presensi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'id_guru' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'jam_masuk' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_keluar' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'id_kehadiran' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => false,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_presensi', true);
        $this->forge->addKey('id_guru');
        $this->forge->addKey('id_kehadiran');
        $this->forge->addForeignKey('id_kehadiran', 'tb_kehadiran', 'id_kehadiran', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('id_guru', 'tb_guru', 'id_guru', 'SET NULL', 'CASCADE');
        $this->forge->createTable('tb_presensi_guru', true);

        // ====================================
        // Table: tb_presensi_siswa
        // ====================================
        $this->forge->addField([
            'id_presensi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'id_siswa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => false,
            ],
            'id_kelas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'jam_masuk' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_keluar' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'id_kehadiran' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => false,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_presensi', true);
        $this->forge->addKey('id_siswa');
        $this->forge->addKey('id_kelas');
        $this->forge->addKey('id_kehadiran');
        $this->forge->addForeignKey('id_kehadiran', 'tb_kehadiran', 'id_kehadiran', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('id_siswa', 'tb_siswa', 'id_siswa', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kelas', 'tb_kelas', 'id_kelas', 'SET NULL', 'CASCADE');
        $this->forge->createTable('tb_presensi_siswa', true);
    }

    public function down()
    {
        // Drop tables in reverse order (respecting foreign key constraints)
        $this->forge->dropTable('tb_presensi_siswa', true);
        $this->forge->dropTable('tb_presensi_guru', true);
        $this->forge->dropTable('tb_siswa', true);
        $this->forge->dropTable('tb_guru', true);
        $this->forge->dropTable('tb_kehadiran', true);
    }
}