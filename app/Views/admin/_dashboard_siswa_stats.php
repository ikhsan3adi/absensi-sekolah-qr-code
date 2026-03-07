<div class="row text-center flex-nowrap">
  <div class="col-2">
    <h5 class="text-success text-nowrap"><b>Hadir</b></h5>
    <h4 class="text-nowrap" id="hadirCount"><?= $hadir; ?></h4>
  </div>
  <div class="col-2">
    <h5 class="text-warning text-nowrap"><b>Sakit</b></h5>
    <h4 class="text-nowrap" id="sakitCount"><?= $sakit; ?></h4>
  </div>
  <div class="col-2">
    <h5 class="text-info text-nowrap"><b>Izin</b></h5>
    <h4 class="text-nowrap" id="izinCount"><?= $izin; ?></h4>
  </div>
  <div class="col-2">
    <?php if (isset($isAfterSchool) && $isAfterSchool): ?>
        <h5 class="text-danger text-nowrap" id="alfaLabel"><b>Alfa</b></h5>
        <h4 class="text-nowrap" id="alfaCount"><?= $alfa; ?></h4>
    <?php else: ?>
        <h5 class="text-muted text-nowrap" id="alfaLabel"><b>Belum Absen</b></h5>
        <h4 class="text-nowrap" id="alfaCount"><?= $alfa; ?></h4>
    <?php endif; ?>
  </div>
  <div class="col-1">
    <div class="border-right mx-auto h-100" style="width: 0;"></div>
  </div>
  <div class="col-3">
    <h5 class="text-primary text-nowrap"><b>Total Siswa</b></h5>
    <h4 class="text-nowrap" id="totalSiswaCount"><?= $totalSiswa; ?></h4>
  </div>
</div>