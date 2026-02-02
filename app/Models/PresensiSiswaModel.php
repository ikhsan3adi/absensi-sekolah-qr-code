<?php

namespace App\Models;

use App\Models\PresensiInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use App\Libraries\enums\Kehadiran;

class PresensiSiswaModel extends Model implements PresensiInterface
{
   protected $primaryKey = 'id_presensi';

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

   public function cekAbsen(string|int $id, string|Time $date)
   {
      $result = $this->where(['id_siswa' => $id, 'tanggal' => $date])->first();

      if (empty($result))
         return false;

      return $result[$this->primaryKey];
   }

   public function absenMasuk(string $id, $date, $time, $idKelas = '')
   {
      $this->save([
         'id_siswa' => $id,
         'id_kelas' => $idKelas,
         'tanggal' => $date,
         'jam_masuk' => $time,
         // 'jam_keluar' => '',
         'id_kehadiran' => Kehadiran::Hadir->value,
         'keterangan' => ''
      ]);
   }

   public function absenKeluar(string $id, $time)
   {
      $this->update($id, [
         'jam_keluar' => $time,
         'keterangan' => ''
      ]);
   }

   public function getPresensiByIdSiswaTanggal($idSiswa, $date)
   {
      return $this->where(['id_siswa' => $idSiswa, 'tanggal' => $date])->first();
   }

   public function getPresensiById(string $idPresensi)
   {
      return $this->where([$this->primaryKey => $idPresensi])->first();
   }

   public function getPresensiByKelasTanggal($idKelas, $tanggal): array
   {
      return $this->db->table('tb_siswa')
         ->select('*')
         ->join(
            "(SELECT id_presensi, id_siswa AS id_siswa_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_siswa)tb_presensi_siswa",
            "tb_siswa.id_siswa = tb_presensi_siswa.id_siswa_presensi AND tb_presensi_siswa.tanggal = '$tanggal'",
            'left'
         )
         ->join(
            'tb_kehadiran',
            'tb_presensi_siswa.id_kehadiran = tb_kehadiran.id_kehadiran',
            'left'
         )
         ->where("tb_siswa.id_kelas", $idKelas)
         ->orderBy("nama_siswa")
         ->get()
         ->getResultArray();
   }

   public function getPresensiByKehadiran(string $idKehadiran, $tanggal, $idKelas = null)
   {
      $this->join(
         'tb_siswa',
         "tb_presensi_siswa.id_siswa = tb_siswa.id_siswa AND tb_presensi_siswa.tanggal = '$tanggal'",
         'right'
      );

      if ($idKelas) {
         $this->where('tb_siswa.id_kelas', $idKelas);
      }

      if ($idKehadiran == '4') {
         $result = $this->findAll();

         $filteredResult = [];

         foreach ($result as $value) {
            if (!in_array($value['id_kehadiran'], ['1', '2', '3'])) {
               array_push($filteredResult, $value);
            }
         }

         return $filteredResult;
      } else {
         $this->where(['tb_presensi_siswa.id_kehadiran' => $idKehadiran]);
         return $this->findAll();
      }
   }

   /**
    * Get attendance trend for last N days
    * @return array ['hadir' => [], 'sakit' => [], 'izin' => [], 'alfa' => []]
    */
   public function getAttendanceTrend(int $days = 7, $idKelas = null): array
   {
      $now = Time::now();
      $result = ['hadir' => [], 'sakit' => [], 'izin' => [], 'alfa' => []];

      for ($i = $days - 1; $i >= 0; $i--) {
         $date = $now->subDays($i)->toDateString();

         $result['hadir'][] = count($this->getPresensiByKehadiran('1', $date, $idKelas));
         $result['sakit'][] = count($this->getPresensiByKehadiran('2', $date, $idKelas));
         $result['izin'][] = count($this->getPresensiByKehadiran('3', $date, $idKelas));
         $result['alfa'][] = count($this->getPresensiByKehadiran('4', $date, $idKelas));
      }

      return $result;
   }

   public function updatePresensi(
      $idPresensi,
      $idSiswa,
      $idKelas,
      $tanggal,
      $idKehadiran,
      $jamMasuk,
      $jamKeluar,
      $keterangan
   ) {
      $presensi = $this->getPresensiByIdSiswaTanggal($idSiswa, $tanggal);

      $data = [
         'id_siswa' => $idSiswa,
         'id_kelas' => $idKelas,
         'tanggal' => $tanggal,
         'id_kehadiran' => $idKehadiran,
         'keterangan' => $keterangan ?? $presensi['keterangan'] ?? ''
      ];

      if ($idPresensi != null) {
         $data[$this->primaryKey] = $idPresensi;
      }

      if ($jamMasuk != null) {
         $data['jam_masuk'] = $jamMasuk;
      }

      if ($jamKeluar != null) {
         $data['jam_keluar'] = $jamKeluar;
      }

      return $this->save($data);
   }
}
