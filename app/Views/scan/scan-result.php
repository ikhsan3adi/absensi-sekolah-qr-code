<?php

use App\Models\TipeUser;

switch ($type) {
   case TipeUser::Siswa:
?>
      <h3 class="text-success">Absen <?= $waktu; ?> berhasil</h3>
      <p><b>Nama : </b><?= $data['nama_siswa']; ?></p>
      <p><b>NIS : </b><?= $data['nis']; ?></p>
      <p><b>Kelas : </b><?= $data['kelas']; ?></p>
      <p><b>Jurusan : </b><?= $data['jurusan']; ?></p>
   <?php
      jam($presensi);
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
   <p><b>Jam masuk : </b><?= $presensi['jam_masuk']; ?></p>
   <p><b>Jam pulang : </b><?= $presensi['jam_keluar']; ?></p>
<?php
}

?>