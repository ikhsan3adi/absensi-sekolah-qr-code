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

    public function cek_absen(string|int $id, string|Time $date)
    {
        $result = $this->where(['id_guru' => $id, 'tanggal' => $date])->first();

        if (empty($result)) return false;

        return $result[$this->primaryKey];
    }

    public function absen_masuk(string $id, $date, $time)
    {
        $this->save([
            'id_guru' => $id,
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

    public function get_presensi($id_guru, $date)
    {
        return $this->where(['id_guru' => $id_guru, 'tanggal' => $date])->first();
    }

    public function get_presensi_byId($id_presensi)
    {
        return $this->where([$this->primaryKey => $id_presensi])->first();
    }

    public function get_presensi_byTanggal($tanggal)
    {
        return $this->setTable('tb_guru')
            ->select('*')
            ->join(
                "(SELECT id_presensi, id_guru AS id_guru_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_guru) tb_presensi_guru",
                "{$this->table}.id_guru = tb_presensi_guru.id_guru_presensi AND tb_presensi_guru.tanggal = '$tanggal'",
                'left'
            )
            ->join(
                'tb_kehadiran',
                'tb_presensi_guru.id_kehadiran = tb_kehadiran.id_kehadiran',
                'left'
            )
            ->orderBy("nama_guru")
            ->findAll();
    }
}
