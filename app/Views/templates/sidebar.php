<?php
$user     = auth()->user();
$context  = $ctx ?? 'dashboard';
$menuItems = [];

// ── Sidebar color based on context ──
switch ($context) {
   case 'absen-siswa':
   case 'siswa':
   case 'kelas':
      $sidebarColor = 'purple';
      break;
   case 'absen-guru':
   case 'guru':
      $sidebarColor = 'green';
      break;
   case 'qr':
   case 'backup':
      $sidebarColor = 'danger';
      break;
   default:
      $sidebarColor = 'azure';
      break;
}

// ── Role-based logo label ──
$roleLabel = 'Operator Petugas Absensi';
if ($user) {
   if ($user->inGroup('superadmin')) {
      $roleLabel = 'Super Administrator';
   } elseif ($user->inGroup('kepsek')) {
      $roleLabel = 'Kepala Sekolah';
   } elseif ($user->inGroup('scanner') && !$user->can('admin.access')) {
      $roleLabel = 'Petugas Scanner';
   } elseif ($user->inGroup('guru') && !$user->inGroup('admin') && !$user->inGroup('superadmin')) {
      $roleLabel = 'Guru / Wali Kelas';
   }
}

// ── Collect menu sections ──
// Returns false when no items added to that section
$hasTeacherSection = false;
$hasAdminSection   = false;

// ── Teacher / Wali Kelas menus ──
// Only show for users with a teacher profile
if ($user && is_guru()) {
   $hasTeacherSection = true;
   $menuItems = array_merge($menuItems, [
      ['title' => 'Dashboard Wali Kelas', 'url' => 'teacher/dashboard', 'icon' => 'dashboard', 'context' => 'teacher-dashboard'],
      ['title' => 'Pengajuan Izin',       'url' => 'teacher/perizinan',  'icon' => 'mail', 'context' => 'teacher-perizinan'],
      ['title' => 'Laporan Kelas',         'url' => 'teacher/laporan',   'icon' => 'print',      'context' => 'teacher-laporan'],
      ['title' => 'QR Code Siswa',         'url' => 'teacher/qr',         'icon' => 'qr_code',    'context' => 'teacher-qr'],
      ['title' => 'Manajemen Kehadiran',   'url' => 'teacher/attendance', 'icon' => 'event_note', 'context' => 'teacher-attendance'],
   ]);
}

// ── Admin-area menus, filtered by permission ──
$adminMenus = [
   ['title' => 'Dashboard',            'url' => 'admin/dashboard',         'icon' => 'dashboard',  'context' => 'admin-dashboard',    'perm' => 'admin.access'],
   ['title' => 'Absensi Siswa',        'url' => 'admin/absen-siswa',       'icon' => 'checklist',  'context' => 'absen-siswa',        'perm' => 'attendance.edit'],
   ['title' => 'Absensi Guru',         'url' => 'admin/absen-guru',        'icon' => 'checklist',  'context' => 'absen-guru',         'perm' => 'attendance.edit'],
   ['title' => 'Data Perizinan',       'url' => 'admin/perizinan',          'icon' => 'mail', 'context' => 'perizinan',     'perm' => 'attendance.edit'],
   ['title' => 'Hari Libur',           'url' => 'admin/holiday',            'icon' => 'event_busy', 'context' => 'holiday',            'perm' => 'settings.manage'],
   ['title' => 'Data Siswa',           'url' => 'admin/siswa',             'icon' => 'person',     'context' => 'siswa',             'perm' => 'students.manage'],
   ['title' => 'Data Guru',            'url' => 'admin/guru',              'icon' => 'person_4',   'context' => 'guru',              'perm' => 'teachers.manage'],
   ['title' => 'Data Kelas & Jurusan', 'url' => 'admin/kelas',             'icon' => 'school',     'context' => 'kelas',             'perm' => 'classes.manage'],
   ['title' => 'Generate QR Code',      'url' => 'admin/generate',          'icon' => 'qr_code',    'context' => 'admin-qr',           'perm' => 'qr.generate'],
   ['title' => 'Generate Laporan',      'url' => 'admin/laporan',           'icon' => 'print',      'context' => 'laporan',           'perm' => 'attendance.view'],
   ['title' => 'Data Petugas',          'url' => 'admin/petugas',           'icon' => 'computer',   'context' => 'petugas',           'perm' => 'petugas.manage'],
   ['title' => 'Pengaturan',            'url' => 'admin/general-settings',  'icon' => 'settings',   'context' => 'general_settings',  'perm' => 'settings.manage'],
   ['title' => 'Audit Log',             'url' => 'admin/audit-log',         'icon' => 'history',    'context' => 'audit-log',         'perm' => 'admin.access'],
   ['title' => 'Backup & Restore',      'url' => 'admin/backup',            'icon' => 'backup',     'context' => 'backup',            'perm' => 'backup.manage'],
];

$adminItems = [];
foreach ($adminMenus as $m) {
   if ($user && $user->can($m['perm'])) {
      $adminItems[] = $m;
   }
}

if ($adminItems !== []) {
   $hasAdminSection = true;
}

// ── Decide whether to show section headers ──
// If both sections are present, group them with headers
$showSectionHeaders = ($hasTeacherSection && $hasAdminSection);
?>
<div class="sidebar" data-color="<?= $sidebarColor; ?>" data-image="<?= base_url('assets/img/sidebar/sidebar-3.jpg'); ?>">
   <div class="logo">
      <a class="simple-text logo-normal">
         <b><?= $roleLabel; ?></b>
         <br>
         <small><?= $generalSettings->school_name; ?></small>
      </a>
   </div>
   <div class="sidebar-wrapper">
      <ul class="nav">

<?php if ($showSectionHeaders): ?>

         <!--Wali Kelas section-->
         <li class="nav-item"><p class="nav-link py-1">Wali Kelas</p></li>
         <?php foreach ($menuItems as $item): ?>
            <li class="nav-item <?= $context === $item['context'] ? 'active' : ''; ?>">
               <a class="nav-link font-weight-bold" href="<?= base_url($item['url']); ?>">
                  <i class="material-icons"><?= $item['icon']; ?></i>
                  <p><?= $item['title']; ?></p>
               </a>
            </li>
         <?php endforeach; ?>

         <!--Admin section-->
         <li class="nav-item"><p class="nav-link py-1">Admin</p></li>
         <?php foreach ($adminItems as $item): ?>
            <li class="nav-item <?= $context === $item['context'] ? 'active' : ''; ?>">
               <a class="nav-link font-weight-bold" href="<?= base_url($item['url']); ?>">
                  <i class="material-icons"><?= $item['icon']; ?></i>
                  <p><?= $item['title']; ?></p>
               </a>
            </li>
         <?php endforeach; ?>

<?php else: ?>
         <!--Single section (no grouping needed)-->
         <?php foreach (array_merge($menuItems, $adminItems) as $item): ?>
            <li class="nav-item <?= $context === $item['context'] ? 'active' : ''; ?>">
               <a class="nav-link font-weight-bold" href="<?= base_url($item['url']); ?>">
                  <i class="material-icons"><?= $item['icon']; ?></i>
                  <p><?= $item['title']; ?></p>
               </a>
            </li>
         <?php endforeach; ?>
<?php endif; ?>
        <!-- Fallback: scanner-only -->
         <?php if (empty($menuItems) && empty($adminItems) && $user && $user->inGroup('scanner')): ?>
            <li class="nav-item <?= $context === 'scan' ? 'active' : ''; ?>">
               <a class="nav-link font-weight-bold" href="<?= base_url('scan'); ?>">
                  <i class="material-icons">qr_code</i>
                  <p>Scan QR</p>
               </a>
            </li>
         <?php endif; ?>
      </ul>
   </div>
</div>
