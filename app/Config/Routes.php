<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

service('auth')->routes($routes);

// ── Home route (role-based redirect) ──
$routes->get('/', function () {
   helper('user');
   $user = auth()->user();

   // Superadmin always goes to admin dashboard, regardless of other groups
   if ($user && $user->inGroup('superadmin')) {
      return redirect()->to(base_url('admin'));
   }

   if (is_guru()) {
      return redirect()->to(base_url('teacher/dashboard'));
   }

   $role = user_role();
   if ($role === 'scanner') {
      return redirect()->to(base_url('scan'));
   }

   return redirect()->to(base_url('admin'));
});

// ── Scan (public after login) ──
$routes->group('scan', function (RouteCollection $routes) {
   $routes->get('', 'Scan::index');
   $routes->get('masuk', 'Scan::index/Masuk');
   $routes->get('pulang', 'Scan::index/Pulang');
   $routes->post('cek', 'Scan::cekKode');
});

// Perizinan Publik
$routes->group('izin', function (RouteCollection $routes) {
   $routes->get('', 'Perizinan::index');
   $routes->post('submit', 'Perizinan::submit');
   $routes->post('get-siswa', 'Perizinan::getSiswaByNis');
});

// Portal Cek Kehadiran Mandiri
$routes->group('cek-kehadiran', function (RouteCollection $routes) {
   $routes->get('', 'CekKehadiran::index');
   $routes->post('view', 'CekKehadiran::view');
});

// ═══════════════════════════════════════════
// ADMIN AREA — each resource has its own
// permission filter via Shield PermissionFilter.
// ═══════════════════════════════════════════
$routes->group('admin', function (RouteCollection $routes) {

   // ── Dashboard (dashboard.view-admin) ──
   $routes->get('', 'Admin\Dashboard::index', ['filter' => 'permission:dashboard.view-admin']);
   $routes->get('dashboard', 'Admin\Dashboard::index', ['filter' => 'permission:dashboard.view-admin']);
   $routes->post('dashboard/filter-data', 'Admin\Dashboard::filterData', ['filter' => 'permission:dashboard.view-admin']);

   // ── Perizinan ──
   $routes->group('perizinan', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->get('/', 'Perizinan::index');
      $routes->post('list', 'Perizinan::list');
      $routes->post('konfirmasi', 'Perizinan::konfirmasi');
      $routes->delete('delete/(:any)', 'Perizinan::delete/$1');
   });

   // ── Hari Libur ──
   $routes->group('holiday', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->get('/', 'Holiday::index');
      $routes->get('generate-weekend', 'Holiday::generateWeekend');
      $routes->post('save', 'Holiday::save');
      $routes->post('bulk-delete', 'Holiday::bulkDelete');
      $routes->delete('delete/(:any)', 'Holiday::delete/$1');
   });

   // ── Audit Log ──
   $routes->get('audit-log', 'Admin\Dashboard::auditLog');

   // ── Absensi Siswa (attendance.edit) ──
   $routes->group('absen-siswa', ['filter' => 'permission:attendance.edit'], function ($routes) {
      $routes->get('/', 'Admin\DataAbsenSiswa::index');
      $routes->post('/', 'Admin\DataAbsenSiswa::ambilDataSiswa');
      $routes->post('kehadiran', 'Admin\DataAbsenSiswa::ambilKehadiran');
      $routes->post('edit', 'Admin\DataAbsenSiswa::ubahKehadiran');
   });

   // ── Absensi Guru (attendance.edit) ──
   $routes->group('absen-guru', ['filter' => 'permission:attendance.edit'], function ($routes) {
      $routes->get('/', 'Admin\DataAbsenGuru::index');
      $routes->post('/', 'Admin\DataAbsenGuru::ambilDataGuru');
      $routes->post('kehadiran', 'Admin\DataAbsenGuru::ambilKehadiran');
      $routes->post('edit', 'Admin\DataAbsenGuru::ubahKehadiran');
   });

   // ── Data Siswa (students.manage) ──
   $routes->group('siswa', ['filter' => 'permission:students.manage'], function ($routes) {
      $routes->get('/', 'Admin\DataSiswa::index');
      $routes->post('/', 'Admin\DataSiswa::ambilDataSiswa');
      $routes->get('create', 'Admin\DataSiswa::formTambahSiswa');
      $routes->post('create', 'Admin\DataSiswa::saveSiswa');
      $routes->get('edit/(:any)', 'Admin\DataSiswa::formEditSiswa/$1');
      $routes->post('edit', 'Admin\DataSiswa::updateSiswa');
      $routes->delete('delete/(:any)', 'Admin\DataSiswa::delete/$1');
      $routes->get('bulk', 'Admin\DataSiswa::bulkPostSiswa');
      $routes->post('downloadCSVFilePost', 'Admin\DataSiswa::downloadCSVFilePost');
      $routes->post('generateCSVObjectPost', 'Admin\DataSiswa::generateCSVObjectPost');
      $routes->post('importCSVItemPost', 'Admin\DataSiswa::importCSVItemPost');
      $routes->post('deleteSelectedSiswa', 'Admin\DataSiswa::deleteSelectedSiswa');
   });

   // ── Data Guru (teachers.manage) ──
   $routes->group('guru', ['filter' => 'permission:teachers.manage'], function ($routes) {
      $routes->get('/', 'Admin\DataGuru::index');
      $routes->post('/', 'Admin\DataGuru::ambilDataGuru');
      $routes->get('create', 'Admin\DataGuru::formTambahGuru');
      $routes->post('create', 'Admin\DataGuru::saveGuru');
      $routes->get('edit/(:any)', 'Admin\DataGuru::formEditGuru/$1');
      $routes->post('edit', 'Admin\DataGuru::updateGuru');
      $routes->delete('delete/(:any)', 'Admin\DataGuru::delete/$1');
      $routes->get('bulk', 'Admin\DataGuru::bulkPost');
      $routes->post('downloadCSVFilePost', 'Admin\DataGuru::downloadCSVFilePost');
      $routes->post('generateCSVObjectPost', 'Admin\DataGuru::generateCSVObjectPost');
      $routes->post('importCSVItemPost', 'Admin\DataGuru::importCSVItemPost');
   });

   // ── Kelas & Jurusan (classes.manage) ──
   $routes->group('kelas', ['filter' => 'permission:classes.manage'], function ($routes) {
      $routes->get('/', 'Admin\KelasController::index');
      $routes->get('tambah', 'Admin\KelasController::tambahKelas');
      $routes->post('tambahKelasPost', 'Admin\KelasController::tambahKelasPost');
      $routes->get('edit/(:any)', 'Admin\KelasController::editKelas/$1');
      $routes->post('editKelasPost', 'Admin\KelasController::editKelasPost');
      $routes->post('deleteKelasPost', 'Admin\KelasController::deleteKelasPost');
      $routes->post('list-data', 'Admin\KelasController::listData');
      $routes->get('bulk', 'Admin\KelasController::bulkPost');
      $routes->post('downloadCSVFilePost', 'Admin\KelasController::downloadCSVFilePost');
      $routes->post('generateCSVObjectPost', 'Admin\KelasController::generateCSVObjectPost');
      $routes->post('importCSVItemPost', 'Admin\KelasController::importCSVItemPost');
   });

   $routes->group('jurusan', ['filter' => 'permission:classes.manage'], function ($routes) {
      $routes->get('/', 'Admin\JurusanController::index');
      $routes->get('tambah', 'Admin\JurusanController::tambahJurusan');
      $routes->post('tambahJurusanPost', 'Admin\JurusanController::tambahJurusanPost');
      $routes->get('edit/(:any)', 'Admin\JurusanController::editJurusan/$1');
      $routes->post('editJurusanPost', 'Admin\JurusanController::editJurusanPost');
      $routes->post('deleteJurusanPost', 'Admin\JurusanController::deleteJurusanPost');
      $routes->post('list-data', 'Admin\JurusanController::listData');
      $routes->get('bulk', 'Admin\JurusanController::bulkPost');
      $routes->post('downloadCSVFilePost', 'Admin\JurusanController::downloadCSVFilePost');
      $routes->post('generateCSVObjectPost', 'Admin\JurusanController::generateCSVObjectPost');
      $routes->post('importCSVItemPost', 'Admin\JurusanController::importCSVItemPost');
   });

   // ── Generate QR (qr.generate) ──
   $routes->group('generate', ['filter' => 'permission:qr.generate'], function ($routes) {
      $routes->get('/', 'Admin\GenerateQR::index');
      $routes->post('siswa-by-kelas', 'Admin\GenerateQR::getSiswaByKelas');
      $routes->post('siswa', 'Admin\QRGenerator::generateQrSiswa');
      $routes->post('guru', 'Admin\QRGenerator::generateQrGuru');
   });

    // ── QR Download & Print (qr.generate) ──
    $routes->group('qr', ['filter' => 'permission:qr.generate'], function ($routes) {
       $routes->get('siswa/download', 'Admin\QRGenerator::downloadAllQrSiswa');
       $routes->get('siswa/(:any)/download', 'Admin\QRGenerator::downloadQrSiswa/$1');
       $routes->get('siswa/(:any)/view', 'Admin\QRGenerator::viewQrSiswa/$1');
       $routes->get('siswa/print', 'Admin\QRGenerator::printQrSiswa');
       $routes->get('siswa/print/(:any)', 'Admin\QRGenerator::printQrSiswa/$1');
       $routes->get('siswa/print-single/(:any)', 'Admin\QRGenerator::printQrSiswaSingle/$1');
       $routes->get('guru/download', 'Admin\QRGenerator::downloadAllQrGuru');
       $routes->get('guru/(:any)/download', 'Admin\QRGenerator::downloadQrGuru/$1');
       $routes->get('guru/(:any)/view', 'Admin\QRGenerator::viewQrGuru/$1');
       $routes->get('guru/print', 'Admin\QRGenerator::printQrGuru');
       $routes->get('guru/print-single/(:any)', 'Admin\QRGenerator::printQrGuruSingle/$1');
    });

   // ── Laporan (attendance.view) ──
   $routes->group('laporan', ['filter' => 'permission:attendance.view'], function ($routes) {
      $routes->get('/', 'Admin\GenerateLaporan::index');
      $routes->post('siswa', 'Admin\GenerateLaporan::generateLaporanSiswa');
      $routes->post('guru', 'Admin\GenerateLaporan::generateLaporanGuru');
   });

   // ── Data Petugas (petugas.manage) ──
   $routes->group('petugas', ['filter' => 'permission:petugas.manage'], function ($routes) {
      $routes->get('/', 'Admin\DataPetugas::index');
      $routes->post('/', 'Admin\DataPetugas::ambilDataPetugas');
      $routes->get('register', 'Admin\DataPetugas::registerPetugas');
      $routes->post('register', 'Admin\DataPetugas::registerPetugasPost');
      $routes->get('edit/(:any)', 'Admin\DataPetugas::formEditPetugas/$1');
      $routes->post('edit', 'Admin\DataPetugas::updatePetugas');
      $routes->delete('delete/(:any)', 'Admin\DataPetugas::delete/$1');
      $routes->get('activate/(:any)', 'Admin\DataPetugas::toggleActivation/$1');
      $routes->get('bulk', 'Admin\DataPetugas::bulkPost');
      $routes->post('downloadCSVFilePost', 'Admin\DataPetugas::downloadCSVFilePost');
      $routes->post('generateCSVObjectPost', 'Admin\DataPetugas::generateCSVObjectPost');
      $routes->post('importCSVItemPost', 'Admin\DataPetugas::importCSVItemPost');
   });

   // ── General Settings (settings.manage) ──
   $routes->group('general-settings', ['filter' => 'permission:settings.manage'], function ($routes) {
      $routes->get('/', 'Admin\GeneralSettings::index');
      $routes->post('update', 'Admin\GeneralSettings::generalSettingsPost');
   });

   // ── Backup & Restore (backup.manage) ──
   $routes->group('backup', ['filter' => 'permission:backup.manage'], function ($routes) {
      $routes->get('', 'Admin\Backup::index');
      $routes->get('db/backup', 'Admin\Backup::dbBackup');
      $routes->post('db/restore', 'Admin\Backup::dbRestore');
      $routes->get('photos/backup', 'Admin\Backup::photosBackup');
      $routes->post('photos/restore', 'Admin\Backup::photosRestore');
   });
});

// ═══════════════════════════════════════════
// TEACHER AREA
// ═══════════════════════════════════════════
$routes->group('teacher', ['filter' => 'permission:teacher.access'], function (RouteCollection $routes) {
   $routes->get('/', 'Teacher\Dashboard::index');
   $routes->get('dashboard', 'Teacher\Dashboard::index');
   $routes->get('dashboard/live-stats', 'Teacher\Dashboard::getLiveStats');
   $routes->get('laporan', 'Teacher\Reports::index');
   $routes->post('laporan/generate', 'Teacher\Reports::generate');
    $routes->get('qr', 'Teacher\QRCode::index');
    $routes->get('qr/download', 'Teacher\QRCode::download');
    $routes->get('qr/print', 'Teacher\QRCode::print');
   $routes->get('attendance', 'Teacher\Dashboard::attendance');
   $routes->get('attendance/(:any)', 'Teacher\Dashboard::attendance/$1');
   $routes->post('attendance/get-list', 'Teacher\Dashboard::getAttendanceList');
   $routes->post('attendance/get-edit-modal', 'Teacher\Dashboard::getEditModal');
   $routes->post('attendance/update-single', 'Teacher\Dashboard::updateSingleAttendance');

   // Perizinan
   $routes->get('perizinan', 'Teacher\Perizinan::index');
   $routes->post('perizinan/konfirmasi', 'Teacher\Perizinan::konfirmasi');
});

// ── Environment-specific routes ──
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
   require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
