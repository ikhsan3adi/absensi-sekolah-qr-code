<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

enum Kehadiran: int
{
    case Hadir = 1;
    case Sakit = 2;
    case Izin = 3;
    case TanpaKeterangan = 4;
}

enum TipeUser: string
{
    case Siswa = 'id_siswa';
    case Guru = 'id_guru';
}

interface PresensiInterface
{
    public function cek_absen(string|int $id, string|Time $date): bool;
    public function absen_masuk(string $id);
    public function absen_keluar(string $id);
}

class PresensiBaseModel extends Model
{
    // protected $useTimestamps = true;

    protected $primaryKey = 'id_presensi';
}
