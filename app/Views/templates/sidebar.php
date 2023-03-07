<div class="sidebar" data-color="azure" data-background-color="black" data-image="<?= base_url('public/assets/img/sidebar/sidebar-1.jpg'); ?>">
   <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
   <div class="logo">
      <a class="simple-text logo-normal">
         Operator<br>Petugas Absensi
      </a>
   </div>
   <div class="sidebar-wrapper">
      <ul class="nav">
         <li class="nav-item <?= isset($ctx) && $ctx == 'dashboard' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/dashboard'); ?>">
               <i class="material-icons">dashboard</i>
               <p>Dashboard</p>
            </a>
         </li>
         <li class="nav-item <?= isset($ctx) && $ctx == 'absen-siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/absen-siswa'); ?>">
               <i class="material-icons">checklist</i>
               <p>Absensi Siswa</p>
            </a>
         </li>
         <li class="nav-item <?= isset($ctx) && $ctx == 'absen-guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/absen-guru'); ?>">
               <i class="material-icons">checklist</i>
               <p>Absensi Guru</p>
            </a>
         </li>
         <li class="nav-item <?= isset($ctx) && $ctx == 'siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/data-siswa'); ?>">
               <i class="material-icons">person</i>
               <p>Data Siswa</p>
            </a>
         </li>
         <li class="nav-item <?= isset($ctx) && $ctx == 'guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/data-guru'); ?>">
               <i class="material-icons">person</i>
               <p>Data Guru</p>
            </a>
         </li>
         <!-- <li class="nav-item active-pro ">
            <a class="nav-link" href="./upgrade.html">
               <i class="material-icons">unarchive</i>
               <p>Upgrade to PRO</p>
            </a>
         </li> -->
      </ul>
   </div>
</div>