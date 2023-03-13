<?= $this->extend('templates/laporan') ?>

<?= $this->section('content') ?>
<h2 align="center">DAFTAR HADIR GURU</h2>
<h4 align="center">TAHUN PELAJARAN 2022/2023</h4>
<span>Bulan : <?= $bulan; ?></span>
<table align="center" border="1">
   <thead>
      <td></td>
      <td></td>
      <th colspan="<?= count($tanggal); ?>">Hari/Tanggal</th>
   </thead>
   <thead>
      <td></td>
      <td></td>
      <?php foreach ($tanggal as $value) : ?>
         <th align="center"><?= $value->format('D'); ?></th>
      <?php endforeach; ?>
   </thead>
   <tr>
      <th id="rowSpan3" align="center">No</th>
      <th id="rowSpan3" width="250px">Nama</th>
      <?php foreach ($tanggal as $value) : ?>
         <th align="center"><?= $value->format('d'); ?></th>
      <?php endforeach; ?>
   </tr>

   <?php $i = 0; ?>

   <?php foreach ($listGuru as $guru) : ?>
      <tr>
         <td align="center"><?= $i + 1; ?></td>
         <td><?= $guru['nama_guru']; ?></td>
         <?php foreach ($listAbsen as $absen) : ?>
            <?= kehadiran($absen[$i]['id_kehadiran'] ?? ($absen['lewat'] ? 5 : 4)); ?>
         <?php endforeach; ?>
      </tr>
   <?php
      $i++;
   endforeach; ?>

</table>
<br>
<table>
   <tr>
      <td>Jumlah guru</td>
      <td>: <?= count($listGuru); ?></td>
   </tr>
   <tr>
      <td>Laki-laki</td>
      <td>: <?= $jumlahGuru['laki']; ?></td>
   </tr>
   <tr>
      <td>Perempuan</td>
      <td>: <?= $jumlahGuru['perempuan']; ?></td>
   </tr>
</table>
<script>
   window.print();
</script>
<?php
function kehadiran($kehadiran)
{
   $text = '';
   switch ($kehadiran) {
      case 1:
         $text = "<td align='center' style='background-color:lightgreen;'>H</td>";
         break;
      case 2:
         $text = "<td align='center' style='background-color:yellow;'>S</td>";
         break;
      case 3:
         $text = "<td align='center' style='background-color:yellow;'>I</td>";
         break;
      case 4:
         $text = "<td align='center' style='background-color:red;'>A</td>";
         break;
      case 5:
      default:
         $text = "<td></td>";
         break;
   }

   return $text;
}
?>
<?= $this->endSection() ?>