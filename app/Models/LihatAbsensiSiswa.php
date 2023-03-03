<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class LihatAbsensiSiswa extends Model
{
    protected $allowedFields = [
        'id_siswa',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'id_kehadiran',
        'keterangan'
    ];

    protected $table = 'tb_siswa';

    public function get_presensi_byKelasTanggal($id_kelas, $tanggal)
    {
        return $this->select('*')
            ->join(
                "(SELECT * FROM tb_presensi_siswa WHERE tb_presensi_siswa.tanggal = '$tanggal' LIMIT 1) tb_presensi_siswa",
                "{$this->table}.id_siswa = tb_presensi_siswa.id_siswa",
                'left'
            )
            ->join(
                'tb_kehadiran',
                'tb_presensi_siswa.id_kehadiran = tb_kehadiran.id_kehadiran',
                'left'
            )
            ->where("{$this->table}.id_kelas = $id_kelas;")->findAll();
    }
}
