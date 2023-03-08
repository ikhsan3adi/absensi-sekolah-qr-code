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
   public function cekAbsen(string|int $id, string|Time $date);
   public function absenMasuk(string $id, $date, $time);
   public function absenKeluar(string $id, $time);
}

class PresensiBaseModel extends Model
{
   // protected $useTimestamps = true;

   protected $primaryKey = 'id_presensi';
}
