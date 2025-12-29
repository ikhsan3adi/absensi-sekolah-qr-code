<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <?php if (isset($no_class)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="text-center">Anda belum ditugaskan sebagai Wali Kelas di kelas manapun.</h3>
                            <p class="text-center">Silahkan hubungi administrator untuk penugasan kelas.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-stats">
                        <div class="card-header card-header-info card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">school</i>
                            </div>
                            <p class="card-category">Kelas Anda</p>
                            <h3 class="card-title">
                                <?= $kelas['tingkat'] . ' ' . $kelas['jurusan'] . ' ' . $kelas['index_kelas']; ?>
                            </h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons">person</i> Total Siswa: <?= $summary['total_siswa']; ?>
                                <span class="mx-2">|</span>
                                <i class="material-icons text-primary">qr_code</i> <a class="text-primary"
                                    href="<?= base_url('teacher/qr'); ?>">Download QR Code Siswa</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title"><b>Statistik Kehadiran Kelas Hari Ini</b></h4>
                            <p class="card-category"><?= date('d F Y'); ?></p>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h4 class="text-success"><b>Hadir</b></h4>
                                    <h3><?= $summary['hadir_hari_ini']; ?></h3>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-warning"><b>Sakit</b></h4>
                                    <h3><?= $summary['sakit_hari_ini']; ?></h3>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-info"><b>Izin</b></h4>
                                    <h3><?= $summary['izin_hari_ini']; ?></h3>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-danger"><b>Alfa</b></h4>
                                    <h3><?= $summary['alfa_hari_ini']; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-chart">
                        <div class="card-header card-header-info">
                            <div class="ct-chart" id="kehadiranSiswaKelas"></div>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">Tingkat Kehadiran Kelas (7 Hari Terakhir)</h4>
                            <p class="card-category">Jumlah siswa hadir per hari</p>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons text-info">assessment</i> <a class="text-info"
                                    href="<?= base_url('teacher/laporan'); ?>">Download Laporan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!isset($no_class)): ?>
    <!-- Chartist JS -->
    <script src="<?= base_url('assets/js/plugins/chartist.min.js') ?>"></script>
    <script>
        $(document).ready(function () {
            initTeacherCharts();
        });

        function initTeacherCharts() {
            if ($('#kehadiranSiswaKelas').length != 0) {
                const dataKehadiran = [<?php foreach ($kehadiranArray as $v)
                    echo "$v,"; ?>];
                const chartData = {
                    labels: [<?php foreach ($dateRange as $d)
                        echo "'$d',"; ?>],
                    series: [dataKehadiran]
                };

                var max = Math.max(...dataKehadiran);
                const options = {
                    lineSmooth: Chartist.Interpolation.cardinal({ tension: 0 }),
                    low: 0,
                    high: max + (max / 4) + 5,
                    chartPadding: { top: 0, right: 0, bottom: 0, left: 0 }
                };

                var chart = new Chartist.Line('#kehadiranSiswaKelas', chartData, options);
                md.startAnimationForLineChart(chart);
            }
        }
    </script>
<?php endif; ?>
<?= $this->endSection() ?>
