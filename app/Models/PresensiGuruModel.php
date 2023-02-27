<?php

namespace App\Models;

use App\Models\PresensiBaseModel;
use CodeIgniter\I18n\Time;

class PresensiGuruModel extends PresensiBaseModel implements PresensiInterface
{
    protected $allowedFields = [
        'id_guru',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'id_kehadiran',
        'keterangan'
    ];

    protected $table = 'tb_presensi_guru';

    public function cek_absen(string|int $id, string|Time $date): bool
    {
        $result = $this->where(['id_guru' => $id, 'tanggal' => $date])->first();

        if (empty($result)) return false;

        return true;
    }

    public function absen_masuk(string $id)
    {
        $this->save([
            'id_guru' => $id,
            'tanggal' => Time::today()->toDateString(),
            'jam_masuk' => Time::now()->toTimeString(),
            // 'jam_keluar' => '',
            'id_kehadiran' => Kehadiran::Hadir->value,
            'keterangan' => ''
        ]);
    }

    public function absen_keluar(string $id)
    {
    }
}
