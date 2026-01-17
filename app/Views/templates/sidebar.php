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
      $sidebarColor = 'danger';
      break;

   default:
      $sidebarColor = 'azure';
      break;
}
?>
<div class="sidebar" data-color="<?= $sidebarColor; ?>" data-background-color="black"
   data-image="<?= base_url('assets/img/sidebar/sidebar-1.jpg'); ?>">
   <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
   <div class="logo">
      <a class="simple-text logo-normal">
         <b>Operator<br>Petugas Absensi</b>
      </a>
   </div>
   <div class="sidebar-wrapper">
      <ul class="nav">
         <?php if (empty(user()->id_guru)): ?>
            <li class="nav-item <?= $context == 'dashboard' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/dashboard'); ?>">
                  <i class="material-icons">dashboard</i>
                  <p>Dashboard</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'absen-siswa' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/absen-siswa'); ?>">
                  <i class="material-icons">checklist</i>
                  <p>Absensi Siswa</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'absen-guru' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/absen-guru'); ?>">
                  <i class="material-icons">checklist</i>
                  <p>Absensi Guru</p>
               </a>
            </li>
         <?php endif; ?>

         <?php if (user()->toArray()['is_superadmin'] != 0): ?>
            <li class="nav-item <?= $context == 'laporan' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/laporan'); ?>">
                  <i class="material-icons">print</i>
                  <p>Generate Laporan</p>
               </a>
            </li>
         <?php endif; ?>


         <?php if (!empty(user()->id_guru)): ?>
            <li class="nav-item <?= $context == 'dashboard' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('teacher/dashboard'); ?>">
                  <i class="material-icons">dashboard</i>
                  <p>Dashboard Wali Kelas</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'laporan' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('teacher/laporan'); ?>">
                  <i class="material-icons">print</i>
                  <p>Laporan Kelas</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'qr' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('teacher/qr'); ?>">
                  <i class="material-icons">qr_code</i>
                  <p>QR Code Siswa</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'attendance' ? 'active' : '' ?>">
               <a class="nav-link" href="<?= base_url('teacher/attendance'); ?>">
                  <i class="material-icons">event_note</i>
                  <p>Manajemen Kehadiran</p>
               </a>
            </li>
         <?php endif; ?>

         <?php if (user()->toArray()['is_superadmin'] == 1): ?>
            <li class="nav-item <?= $context == 'siswa' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/siswa'); ?>">
                  <i class="material-icons">person</i>
                  <p>Data Siswa</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'guru' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/guru'); ?>">
                  <i class="material-icons">person_4</i>
                  <p>Data Guru</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'kelas' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/kelas'); ?>">
                  <i class="material-icons">school</i>
                  <p>Data Kelas & Jurusan</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'qr' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/generate'); ?>">
                  <i class="material-icons">qr_code</i>
                  <p>Generate QR Code</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'petugas' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/petugas'); ?>">
                  <i class="material-icons">computer</i>
                  <p>Data Petugas</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'general_settings' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/general-settings'); ?>">
                  <i class="material-icons">settings</i>
                  <p>Pengaturan</p>
               </a>
            </li>
            <li class="nav-item <?= $context == 'backup' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/backup'); ?>">
                  <i class="material-icons">backup</i>
                  <p>Backup & Restore</p>
               </a>
            </li>
         <?php endif; ?>
         <!-- <li class="nav-item active-pro mb-3">
            <a class="nav-link" href="./upgrade.html">
               <i class="material-icons">unarchive</i>
               <p>Bottom sidebar</p>
            </a>
         </li> -->
      </ul>
   </div>
</div>