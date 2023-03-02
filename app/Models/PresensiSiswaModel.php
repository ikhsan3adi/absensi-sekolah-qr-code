<?php

namespace App\Models;

use App\Models\PresensiBaseModel;

use CodeIgniter\I18n\Time;

class PresensiSiswaModel extends PresensiBaseModel implements PresensiInterface
{
    protected $allowedFields = [
        'id_siswa',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'id_kehadiran',
        'keterangan'
    ];

    protected $table = 'tb_presensi_siswa';

    public function cek_absen(string|int $id, string|Time $date)
    {
        $result = $this->where(['id_siswa' => $id, 'tanggal' => $date])->first();

        if (empty($result)) return false;

        return $result[$this->primaryKey];
    }

    public function absen_masuk(string $id, $date, $time)
    {
        $this->save([
            'id_siswa' => $id,
            'tanggal' => $date,
            'jam_masuk' => $time,
            // 'jam_keluar' => '',
            'id_kehadiran' => Kehadiran::Hadir->value,
            'keterangan' => ''
        ]);
    }

    public function absen_keluar(string $id, $time)
    {
        $this->update($id, [
            'jam_keluar' => $time,
            'keterangan' => ''
        ]);
    }

    public function get_presensi($id, $date)
    {
        return $this->where(['id_siswa' => $id, 'tanggal' => $date])->first();
    }

    public function get_presensi_byId($id_presensi)
    {
        return $this->where([$this->primaryKey => $id_presensi])->first();
    }
}
