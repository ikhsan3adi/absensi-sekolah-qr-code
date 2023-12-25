<h3 class="text-danger"><?= $msg; ?></h3>

<?php

use App\Libraries\enums\TipeUser;

if (empty($type)) {
   return;
} else {
   switch ($type) {
      case TipeUser::Siswa: ?>
         <div class="row w-100">
            <div class="col">
               <p>Nama : <b><?= $data['nama_siswa']; ?></b></p>
               <p>NIS : <b><?= $data['nis']; ?></b></p>
               <p>Kelas : <b><?= $data['kelas'] . ' ' . $data['jurusan']; ?></b></p>
            </div>
            <div class="col">
               <?= jam($presensi ?? []); ?>
            </div>
         </div>
      <?php break;

      case TipeUser::Guru: ?>
         <div class="row w-100">
            <div class="col">
               <p>Nama : <b><?= $data['nama_guru']; ?></b></p>
               <p>NUPTK : <b><?= $data['nuptk']; ?></b></p>
               <p>No HP : <b><?= $data['no_hp']; ?></b></p>
            </div>
            <div class="col">
               <?= jam($presensi ?? []); ?>
            </div>
         </div>
      <?php break;

      default: ?>
         <p class="text-danger">Tipe tidak valid</p>
   <?php break;
   }
}

function jam($presensi)
{
   ?>
   <p>Jam masuk : <b class="text-info"><?= $presensi['jam_masuk'] ?? '-'; ?></b></p>
   <p>Jam pulang : <b class="text-info"><?= $presensi['jam_keluar'] ?? '-'; ?></b></p>
<?php
}

?>