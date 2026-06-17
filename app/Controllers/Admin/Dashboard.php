<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\GuruModel;
use App\Models\JurusanModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\PetugasModel;
use App\Models\PresensiGuruModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;

class Dashboard extends BaseController
{
   protected SiswaModel $siswaModel;
   protected GuruModel $guruModel;

   protected KelasModel $kelasModel;
   protected JurusanModel $jurusanModel;

   protected PresensiSiswaModel $presensiSiswaModel;
   protected PresensiGuruModel $presensiGuruModel;

   protected PetugasModel $petugasModel;

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->guruModel = new GuruModel();
      $this->kelasModel = new KelasModel();
      $this->jurusanModel = new JurusanModel();
      $this->presensiSiswaModel = new PresensiSiswaModel();
      $this->presensiGuruModel = new PresensiGuruModel();
      $this->petugasModel = new PetugasModel();
   }

   public function index()
   {
      $now = Time::now();

      $dateRange = [];
      $chartLabelColors = [];
      $holidayModel = new \App\Models\HariLiburModel();
      for ($i = 6; $i >= 0; $i--) {
         $date = $now->subDays($i)->toDateString();
         $isHoliday = $holidayModel->isHoliday($date);
         if ($i == 0) {
            $formattedDate = "Hari ini";
         } else {
            $t = $now->subDays($i);
            $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
         }
         array_push($dateRange, $formattedDate);
         array_push($chartLabelColors, $isHoliday ? '#f44336' : '#333');
      }

      $today = $now->toDateString();
      
      $jamPulangStandard = $this->generalSettings->jam_pulang_standard ?? '14:00:00';
      $isAfterSchool = $now->toTimeString() > $jamPulangStandard;

      // Get attendance trends using new methods
      $grafikKehadiranSiswa = $this->presensiSiswaModel->getAttendanceTrend();
      $grafikKehadiranGuru = $this->presensiGuruModel->getAttendanceTrend();

      // Prepare kelas data with student count
      $kelasData = $this->kelasModel->getDataKelas();
      foreach ($kelasData as &$k) {
         $k['jumlah_siswa'] = $this->siswaModel->getSiswaCountByKelas($k['id_kelas']);
      }

      $data = [
         'title' => 'Dashboard',
         'ctx' => 'admin-dashboard',

         'siswa' => $this->siswaModel->getAllSiswaWithKelas(),
         'guru' => $this->guruModel->getAllGuru(),

         'kelas' => $kelasData,
         'jurusan' => $this->jurusanModel->getDataJurusan(),

         'dateRange' => $dateRange,
         'chartLabelColors' => $chartLabelColors,
         'dateNow' => $now->toLocalizedString('d MMMM Y'),

         'grafikKehadiranSiswa' => $grafikKehadiranSiswa,
         'grafikKehadiranGuru' => $grafikKehadiranGuru,

         'jumlahKehadiranSiswa' => [
            'hadir' => count($this->presensiSiswaModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiSiswaModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiSiswaModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiSiswaModel->getPresensiByKehadiran('4', $today))
         ],

         'jumlahKehadiranGuru' => [
            'hadir' => count($this->presensiGuruModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiGuruModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiGuruModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiGuruModel->getPresensiByKehadiran('4', $today))
         ],

         'totalSiswa' => $this->siswaModel->getSiswaCountByKelas(),
         'totalGuru' => $this->guruModel->countAllResults(),

         'petugas' => $this->petugasModel->getAllPetugas(),
         
         'topLateStudents' => $this->siswaModel->select('tb_siswa.*, tb_kelas.tingkat, tb_kelas.index_kelas, tb_jurusan.jurusan')
            ->join('tb_kelas', 'tb_kelas.id_kelas = tb_siswa.id_kelas')
            ->join('tb_jurusan', 'tb_jurusan.id = tb_kelas.id_jurusan')
            ->where('poin_pelanggaran >', 0)
            ->orderBy('poin_pelanggaran', 'DESC')
            ->limit(5)
            ->get()->getResultArray(),

         'absenteeAlerts' => $this->presensiSiswaModel->getConsecutiveAbsences(3),
      ];

      return view('admin/dashboard', $data);
   }

   public function auditLog()
   {
      $auditLogModel = new \App\Models\AuditLogModel();
      $data = [
         'title' => 'Audit Log - Riwayat Perubahan',
         'ctx' => 'audit-log',
         'logs' => $auditLogModel->getLogs()
      ];
      return view('admin/audit_log', $data);
   }

   public function getLiveStats()
   {
      $now = Time::now();
      $today = $now->toDateString();
      $idKelas = $this->request->getGet('id_kelas');

      $jumlahKehadiranSiswa = [
         'hadir' => count($this->presensiSiswaModel->getPresensiByKehadiran('1', $today, $idKelas)),
         'sakit' => count($this->presensiSiswaModel->getPresensiByKehadiran('2', $today, $idKelas)),
         'izin' => count($this->presensiSiswaModel->getPresensiByKehadiran('3', $today, $idKelas)),
         'alfa' => count($this->presensiSiswaModel->getPresensiByKehadiran('4', $today, $idKelas))
      ];

      $totalSiswa = $this->siswaModel->getSiswaCountByKelas($idKelas);
      
      $jamPulangStandard = $this->generalSettings->jam_pulang_standard ?? '14:00:00';
      $isAfterSchool = $now->toTimeString() > $jamPulangStandard;

      return $this->response->setJSON([
         'stats' => $jumlahKehadiranSiswa,
         'totalSiswa' => $totalSiswa,
         'isAfterSchool' => $isAfterSchool,
         'lastUpdate' => $now->toTimeString()
      ]);
   }

   public function filterData()
   {
      $idKelas = $this->request->getPost('id_kelas');
      $now = Time::now();
      $today = $now->toDateString();

      // Statistik Siswa
      $jumlahKehadiranSiswa = [
         'hadir' => count($this->presensiSiswaModel->getPresensiByKehadiran('1', $today, $idKelas)),
         'sakit' => count($this->presensiSiswaModel->getPresensiByKehadiran('2', $today, $idKelas)),
         'izin' => count($this->presensiSiswaModel->getPresensiByKehadiran('3', $today, $idKelas)),
         'alfa' => count($this->presensiSiswaModel->getPresensiByKehadiran('4', $today, $idKelas))
      ];

      // Grafik Siswa (7 Hari) - using getAttendanceTrend
      $grafikKehadiranSiswa = $this->presensiSiswaModel->getAttendanceTrend(7, $idKelas ?: null);

      // Jumlah siswa per kelas
      $jumlahSiswa = $this->siswaModel->getSiswaCountByKelas($idKelas);

      $data = [
         'hadir' => $jumlahKehadiranSiswa['hadir'],
         'sakit' => $jumlahKehadiranSiswa['sakit'],
         'izin' => $jumlahKehadiranSiswa['izin'],
         'alfa' => $jumlahKehadiranSiswa['alfa'],
         'totalSiswa' => $jumlahSiswa,
      ];

      return json_encode([
         'result' => 1,
         'htmlContent' => view('admin/_dashboard_siswa_stats', $data),
         'chartData' => $grafikKehadiranSiswa,
         'totalSiswa' => $jumlahSiswa
      ]);
   }
}
