<?php

use App\Models\TipeUser;

switch ($type) {
   case TipeUser::Siswa:
?>
      <h3 class="text-success">Absen <?= $waktu; ?> berhasil</h3>
      <div class="row w-100">
         <div class="col">
            <p><b>Nama : </b><?= $data['nama_siswa']; ?></p>
            <p><b>NIS : </b><?= $data['nis']; ?></p>
            <p><b>Kelas : </b><?= $data['kelas']  . ' ' . $data['jurusan']; ?></p>
         </div>
         <div class="col">
            <?= jam($presensi); ?>
         </div>
      </div>
   <?php
      break;

   case TipeUser::Guru:
   ?>
      <h3 class="text-success">Absen <?= $waktu; ?> berhasil</h3>
      <p><b>Nama : </b><?= $data['nama_guru']; ?></p>
   <?php
      jam($presensi);
      break;

   default:
   ?>
      <h3 class="text-danger">Tipe tidak valid</h3>
   <?php
      break;
}

function jam($presensi)
{
   ?>
   <p>Jam masuk : <b class="text-info"><?= $presensi['jam_masuk'] ?? '-'; ?></b></p>
   <p>Jam pulang : <b class="text-info"><?= $presensi['jam_keluar'] ?? '-'; ?></b></p>
<?php
}

?>