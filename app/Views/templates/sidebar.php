<?php
$context = $ctx ?? 'dashboard';
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

if (is_wali_kelas()) {
   $menuItems = [
      ['title' => 'Dashboard Wali Kelas', 'url' => 'teacher/dashboard', 'icon' => 'dashboard', 'context' => 'dashboard', 'visible' => true],
      ['title' => 'Laporan Kelas', 'url' => 'teacher/laporan', 'icon' => 'print', 'context' => 'laporan-kelas', 'visible' => true],
      ['title' => 'QR Code Siswa', 'url' => 'teacher/qr', 'icon' => 'qr_code', 'context' => 'qr', 'visible' => true],
      ['title' => 'Manajemen Kehadiran', 'url' => 'teacher/attendance', 'icon' => 'event_note', 'context' => 'attendance', 'visible' => true],
   ];
} else {
   $menuItems = [
      ['title' => 'Dashboard', 'url' => 'admin/dashboard', 'icon' => 'dashboard', 'context' => 'dashboard', 'visible' => true],
      ['title' => 'Absensi Siswa', 'url' => 'admin/absen-siswa', 'icon' => 'checklist', 'context' => 'absen-siswa', 'visible' => true],
      ['title' => 'Absensi Guru', 'url' => 'admin/absen-guru', 'icon' => 'checklist', 'context' => 'absen-guru', 'visible' => true],
      ['title' => 'Data Siswa', 'url' => 'admin/siswa', 'icon' => 'person', 'context' => 'siswa', 'visible' => is_superadmin()],
      ['title' => 'Data Guru', 'url' => 'admin/guru', 'icon' => 'person_4', 'context' => 'guru', 'visible' => is_superadmin()],
      ['title' => 'Data Kelas & Jurusan', 'url' => 'admin/kelas', 'icon' => 'school', 'context' => 'kelas', 'visible' => is_superadmin()],
      ['title' => 'Generate QR Code', 'url' => 'admin/generate', 'icon' => 'qr_code', 'context' => 'qr', 'visible' => can_generate_qr()],
      ['title' => 'Generate Laporan', 'url' => 'admin/laporan', 'icon' => 'print', 'context' => 'laporan', 'visible' => can_view_report()],
      ['title' => 'Data Petugas', 'url' => 'admin/petugas', 'icon' => 'computer', 'context' => 'petugas', 'visible' => is_superadmin()],
      ['title' => 'Pengaturan', 'url' => 'admin/general-settings', 'icon' => 'settings', 'context' => 'general_settings', 'visible' => is_superadmin() || is_kepsek()],
      ['title' => 'Backup & Restore', 'url' => 'admin/backup', 'icon' => 'backup', 'context' => 'backup', 'visible' => is_superadmin()],
   ];
}
?>
<div class="sidebar" data-color="<?= $sidebarColor; ?>" data-image="<?= base_url('assets/img/sidebar/sidebar-3.jpg'); ?>">
   <!-- data-background-color="black/red" -->
   <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
   <div class="logo">
      <a class="simple-text logo-normal">
         <b>Operator<br>Petugas Absensi</b>
         <br>
         <small><?= $generalSettings->school_name; ?></small>
      </a>
   </div>
   <div class="sidebar-wrapper">
      <ul class="nav">
         <?php
         foreach ($menuItems as $item):
            if (!$item['visible'])
               continue;
            ?>
            <li class="nav-item <?= $context == $item['context'] ? 'active' : ''; ?>">
               <a class="nav-link font-weight-bold" href="<?= base_url($item['url']); ?>">
                  <i class="material-icons"><?= $item['icon']; ?></i>
                  <p><?= $item['title']; ?></p>
               </a>
            </li>
         <?php endforeach; ?>
      </ul>
   </div>
</div>