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
      'menit_keterlambatan',
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

   public function absenMasuk(string $id, $date, $time, $idKelas = '', $menitKeterlambatan = 0)
   {
      $this->save([
         'id_siswa' => $id,
         'id_kelas' => $idKelas,
         'tanggal' => $date,
         'jam_masuk' => $time,
         'id_kehadiran' => Kehadiran::Hadir->value,
         'menit_keterlambatan' => $menitKeterlambatan,
         'keterangan' => $menitKeterlambatan > 0 ? "Terlambat $menitKeterlambatan menit" : ''
      ]);

      // Jika terlambat, tambahkan poin pelanggaran ke tabel siswa
      if ($menitKeterlambatan > 0) {
         $db = \Config\Database::connect();
         $db->table('tb_siswa')
            ->where('id_siswa', $id)
            ->set('poin_pelanggaran', "poin_pelanggaran + $menitKeterlambatan", false)
            ->update();
      }
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

         $schoolConfigurations = new \Config\School();
         $generalSettings = $schoolConfigurations::$generalSettings;
         $jamPulangStandard = $generalSettings->jam_pulang_standard ?? '14:00:00';
         
         $now = Time::now();
         $nowTime = $now->toTimeString();
         $today = $now->toDateString();
         $isAfterSchool = ($today > $tanggal) || ($today == $tanggal && $nowTime > $jamPulangStandard);

         $filteredResult = [];

         foreach ($result as $value) {
            if (!in_array($value['id_kehadiran'], ['1', '2', '3'])) {
               // Tambahkan status virtual untuk membedakan di view jika perlu
               $value['is_alfa_final'] = $isAfterSchool;
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
    * @return array ['hadir' => [], 'sakit' => [], 'izin' => [], 'alfa' => [], 'belum_absen' => []]
    */
   public function getAttendanceTrend(int $days = 7, $idKelas = null): array
   {
      $now = Time::now();
      $result = ['hadir' => [], 'sakit' => [], 'izin' => [], 'alfa' => [], 'belum_absen' => []];

      $schoolConfigurations = new \Config\School();
      $generalSettings = $schoolConfigurations::$generalSettings;
      $jamPulangStandard = $generalSettings->jam_pulang_standard ?? '14:00:00';
      $holidayModel = new \App\Models\HariLiburModel();

      for ($i = $days - 1; $i >= 0; $i--) {
         $date = $now->subDays($i)->toDateString();

         if ($holidayModel->isHoliday($date)) {
            $result['hadir'][] = 0;
            $result['sakit'][] = 0;
            $result['izin'][] = 0;
            $result['alfa'][] = 0;
            $result['belum_absen'][] = 0;
            continue;
         }

         $isToday = ($date == $now->toDateString());
         $isAfterSchool = (date('Y-m-d') > $date) || ($isToday && $now->toTimeString() > $jamPulangStandard);

         $result['hadir'][] = count($this->getPresensiByKehadiran('1', $date, $idKelas));
         $result['sakit'][] = count($this->getPresensiByKehadiran('2', $date, $idKelas));
         $result['izin'][] = count($this->getPresensiByKehadiran('3', $date, $idKelas));
         
         $notPresentCount = count($this->getPresensiByKehadiran('4', $date, $idKelas));
         
         if ($isAfterSchool) {
            $result['alfa'][] = $notPresentCount;
            $result['belum_absen'][] = 0;
         } else {
            $result['alfa'][] = 0;
            $result['belum_absen'][] = $notPresentCount;
         }
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

   /**
    * Get students who have been absent (Alfa) for N consecutive days
    */
   public function getConsecutiveAbsences(int $consecutiveDays = 3, $idKelas = null): array
   {
      // Get the last N active attendance dates
      $dates = $this->select('tanggal')
         ->groupBy('tanggal')
         ->orderBy('tanggal', 'DESC')
         ->limit($consecutiveDays)
         ->get()
         ->getResultArray();

      if (count($dates) < $consecutiveDays) {
         return [];
      }

      $dateStrings = array_column($dates, 'tanggal');

      // Find students who are NOT in (Hadir, Sakit, Izin) for ALL these dates
      // In this system, Alfa means NO entry in tb_presensi_siswa for that date OR id_kehadiran = 4
      
      $builder = $this->db->table('tb_siswa')
         ->select('tb_siswa.*, tb_kelas.tingkat, tb_kelas.index_kelas, tb_jurusan.jurusan')
         ->join('tb_kelas', 'tb_kelas.id_kelas = tb_siswa.id_kelas')
         ->join('tb_jurusan', 'tb_jurusan.id = tb_kelas.id_jurusan');

      if ($idKelas) {
         $builder->where('tb_siswa.id_kelas', $idKelas);
      }

      $students = $builder->get()->getResultArray();
      $flaggedStudents = [];

      foreach ($students as $student) {
         // Skip siswa baru yang belum pernah absen sama sekali (misal via import CSV)
         $totalPresensi = $this->where('id_siswa', $student['id_siswa'])->countAllResults();
         if ($totalPresensi === 0) {
            continue;
         }

         $absentCount = 0;
         foreach ($dateStrings as $date) {
            $presensi = $this->where([
               'id_siswa' => $student['id_siswa'],
               'tanggal' => $date
            ])->whereIn('id_kehadiran', ['1', '2', '3'])->first();

            if (!$presensi) {
               $absentCount++;
            }
         }

         if ($absentCount >= $consecutiveDays) {
            $student['days_count'] = $absentCount;
            $flaggedStudents[] = $student;
         }
      }

      return $flaggedStudents;
   }
}
