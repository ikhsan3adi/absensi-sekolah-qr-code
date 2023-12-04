<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDB extends Migration
{
    public function up()
    {
        $this->forge->getConnection()->query("CREATE TABLE tb_kehadiran (
            id_kehadiran int(11) NOT NULL,
            kehadiran enum('Hadir','Sakit','Izin','Tanpa keterangan') NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->forge->getConnection()->query("INSERT INTO tb_kehadiran (id_kehadiran, kehadiran) VALUES
            (1, 'Hadir'),
            (2, 'Sakit'),
            (3, 'Izin'),
            (4, 'Tanpa keterangan');");

        $this->forge->getConnection()->query("INSERT INTO tb_jurusan (jurusan) VALUES
            ('OTKP'),
            ('BDP'),
            ('AKL'),
            ('RPL');");

        $this->forge->getConnection()->query("INSERT INTO tb_kelas (kelas, id_jurusan) VALUES
            ('X', 1),
            ('X', 2),
            ('X', 3),
            ('X', 4),
            ('XI', 1),
            ('XI', 2),
            ('XI', 3),
            ('XI', 4),
            ('XII', 1),
            ('XII', 2),
            ('XII', 3),
            ('XII', 4);");

        $this->forge->getConnection()->query("CREATE TABLE tb_guru (
            id_guru int(11) NOT NULL,
            nuptk varchar(24) NOT NULL,
            nama_guru varchar(255) NOT NULL,
            jenis_kelamin enum('Laki-laki','Perempuan') NOT NULL,
            alamat text NOT NULL,
            no_hp varchar(32) NOT NULL,
            unique_code varchar(64) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->forge->getConnection()->query("CREATE TABLE tb_presensi_guru (
            id_presensi int(11) NOT NULL,
            id_guru int(11) DEFAULT NULL,
            tanggal date NOT NULL,
            jam_masuk time DEFAULT NULL,
            jam_keluar time DEFAULT NULL,
            id_kehadiran int(11) NOT NULL,
            keterangan varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                        ");

        $this->forge->getConnection()->query("CREATE TABLE tb_siswa (
            id_siswa int(11) NOT NULL,
            nis varchar(16) NOT NULL,
            nama_siswa varchar(255) NOT NULL,
            id_kelas int(11) UNSIGNED NOT NULL,
            jenis_kelamin enum('Laki-laki','Perempuan') NOT NULL,
            no_hp varchar(32) NOT NULL,
            unique_code varchar(64) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->forge->getConnection()->query("CREATE TABLE tb_presensi_siswa (
            id_presensi int(11) NOT NULL,
            id_siswa int(11) NOT NULL,
            id_kelas int(11) UNSIGNED DEFAULT NULL,
            tanggal date NOT NULL,
            jam_masuk time DEFAULT NULL,
            jam_keluar time DEFAULT NULL,
            id_kehadiran int(11) NOT NULL,
            keterangan varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->forge->getConnection()->query("ALTER TABLE tb_guru
            ADD PRIMARY KEY (id_guru),
            ADD UNIQUE KEY unique_code (unique_code);");

        $this->forge->getConnection()->query("ALTER TABLE tb_kehadiran
            ADD PRIMARY KEY (id_kehadiran);");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_guru
            ADD PRIMARY KEY (id_presensi),
            ADD KEY id_kehadiran (id_kehadiran),
            ADD KEY id_guru (id_guru);");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_siswa
            ADD PRIMARY KEY (id_presensi),
            ADD KEY id_siswa (id_siswa),
            ADD KEY id_kehadiran (id_kehadiran),
            ADD KEY id_kelas (id_kelas);");

        $this->forge->getConnection()->query("ALTER TABLE tb_siswa
            ADD PRIMARY KEY (id_siswa),
            ADD UNIQUE KEY unique_code (unique_code),
            ADD KEY id_kelas (id_kelas);");

        $this->forge->getConnection()->query("ALTER TABLE tb_guru
            MODIFY id_guru int(11) NOT NULL AUTO_INCREMENT;");

        $this->forge->getConnection()->query("ALTER TABLE tb_kehadiran
            MODIFY id_kehadiran int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_guru
            MODIFY id_presensi int(11) NOT NULL AUTO_INCREMENT;");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_siswa
            MODIFY id_presensi int(11) NOT NULL AUTO_INCREMENT;");

        $this->forge->getConnection()->query("ALTER TABLE tb_siswa
            MODIFY id_siswa int(11) NOT NULL AUTO_INCREMENT;");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_guru
            ADD CONSTRAINT tb_presensi_guru_ibfk_2 FOREIGN KEY (id_kehadiran) REFERENCES tb_kehadiran (id_kehadiran),
            ADD CONSTRAINT tb_presensi_guru_ibfk_3 FOREIGN KEY (id_guru) REFERENCES tb_guru (id_guru) ON DELETE SET NULL;");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_siswa
            ADD CONSTRAINT tb_presensi_siswa_ibfk_2 FOREIGN KEY (id_kehadiran) REFERENCES tb_kehadiran (id_kehadiran),
            ADD CONSTRAINT tb_presensi_siswa_ibfk_3 FOREIGN KEY (id_siswa) REFERENCES tb_siswa (id_siswa) ON DELETE CASCADE,
            ADD CONSTRAINT tb_presensi_siswa_ibfk_4 FOREIGN KEY (id_kelas) REFERENCES tb_kelas (id_kelas) ON DELETE SET NULL ON UPDATE CASCADE;");

        $this->forge->getConnection()->query("ALTER TABLE tb_siswa
            ADD CONSTRAINT tb_siswa_ibfk_1 FOREIGN KEY (id_kelas) REFERENCES tb_kelas (id_kelas);");
    }

    public function down()
    {
        $tables = [
            'tb_presensi_siswa',
            'tb_presensi_guru',
            'tb_siswa',
            'tb_guru',
            'tb_kehadiran',
        ];

        foreach ($tables as $table) {
            $this->forge->dropTable($table);
        }
    }
}
