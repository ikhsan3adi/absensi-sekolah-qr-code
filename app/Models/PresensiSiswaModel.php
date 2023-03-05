<?php

namespace App\Models;

use App\Models\PresensiBaseModel;

use CodeIgniter\I18n\Time;

class PresensiSiswaModel extends PresensiBaseModel implements PresensiInterface
{
    protected $allowedFields = [
        'id_siswa',
        'id_kelas',
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

    public function absen_masuk(string $id,  $date, $time, $id_kelas = '')
    {
        $this->save([
            'id_siswa' => $id,
            'id_kelas' => $id_kelas,
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

    public function get_presensi($id_siswa, $date)
    {
        return $this->where(['id_siswa' => $id_siswa, 'tanggal' => $date])->first();
    }

    public function get_presensi_byId($id_presensi)
    {
        return $this->where([$this->primaryKey => $id_presensi])->first();
    }

    public function get_presensi_byKelasTanggal($id_kelas, $tanggal)
    {
        return $this->setTable('tb_siswa')
            ->select('*')
            ->join(
                "(SELECT id_presensi, id_siswa AS id_siswa_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_siswa)tb_presensi_siswa",
                "{$this->table}.id_siswa = tb_presensi_siswa.id_siswa_presensi AND tb_presensi_siswa.tanggal = '$tanggal'",
                'left'
            )
            ->join(
                'tb_kehadiran',
                'tb_presensi_siswa.id_kehadiran = tb_kehadiran.id_kehadiran',
                'left'
            )
            ->where("{$this->table}.id_kelas = $id_kelas")
            ->orderBy("nama_siswa")
            ->findAll();
    }

    public function update_presensi($id_presensi = NULL, $id_siswa, $id_kelas, $tanggal, $id_kehadiran, $jam_masuk = NULL, $keterangan = NULL)
    {
        $presensi = $this->get_presensi($id_siswa, $tanggal);

        $data = [
            'id_siswa' => $id_siswa,
            'id_kelas' => $id_kelas,
            'tanggal' => $tanggal,
            'id_kehadiran' => $id_kehadiran,
            'keterangan' => $keterangan ?? $presensi['keterangan']
        ];

        if ($id_presensi != null) {
            $data[$this->primaryKey] = $id_presensi;
        }

        if ($jam_masuk != null) {
            $data['jam_masuk'] = $jam_masuk;
        }

        return $this->save($data);
    }
}
