<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('styles') ?>
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
<?= $this->endSection() ?>
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
                <div class="col-md-6">
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
                                <i class="material-icons text-primary">qr_code</i> <a class="text-primary" href="<?= base_url('teacher/qr'); ?>">Download QR Code Siswa</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title"><b>Statistik Kehadiran Kelas Hari Ini</b></h4>
                            <p class="card-category"><?= date('d F Y'); ?></p>
                        </div>
                        <div class="card-body">
                            <div class="row text-center flex-nowrap">
                                <div class="col-2">
                                    <h5 class="text-success text-nowrap"><b>Hadir</b></h5>
                                    <h4 class="text-nowrap"><?= $summary['hadir_hari_ini']; ?></h4>
                                </div>
                                <div class="col-2">
                                    <h5 class="text-warning text-nowrap"><b>Sakit</b></h5>
                                    <h4 class="text-nowrap"><?= $summary['sakit_hari_ini']; ?></h4>
                                </div>
                                <div class="col-2">
                                    <h5 class="text-info text-nowrap"><b>Izin</b></h5>
                                    <h4 class="text-nowrap"><?= $summary['izin_hari_ini']; ?></h4>
                                </div>
                                <div class="col-2">
                                    <h5 class="text-danger text-nowrap"><b>Alfa</b></h5>
                                    <h4 class="text-nowrap"><?= $summary['alfa_hari_ini']; ?></h4>
                                </div>
                                <div class="col-1">
                                    <div class="border-right mx-auto h-100" style="width: 0;"></div>
                                </div>
                                <div class="col-2 col-sm-3">
                                    <h5 class="text-primary text-nowrap"><b>Total</b></h5>
                                    <h4 class="text-nowrap"><?= $summary['total_siswa']; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-info">
                            <h4 class="card-title">Tingkat Kehadiran Kelas (7 Hari Terakhir)</h4>
                            <p class="card-category">Statistik kehadiran siswa per status</p>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="kehadiranSiswaKelas"></canvas>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons text-info">assessment</i> <a class="text-info" href="<?= base_url('teacher/laporan'); ?>">Download Laporan</a>
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
    <!-- Chart.js -->
    <script src="<?= base_url('assets/js/plugins/chartjs/chart.umd.min.js') ?>"></script>
    <script>
        const chartLabels = <?= json_encode($dateRange) ?>;

        const chartColors = {
            hadir: { border: '#4caf50', bg: 'rgba(76, 175, 80, 1)' },
            sakit: { border: '#ff9800', bg: 'rgba(255, 152, 0, 1)' },
            izin: { border: '#00bcd4', bg: 'rgba(0, 188, 212, 1)' },
            alfa: { border: '#f44336', bg: 'rgba(244, 67, 54, 1)' }
        };

        function initTeacherCharts() {
            const ctx = document.getElementById('kehadiranSiswaKelas');
            if (ctx) {
                const data = {
                    hadir: <?= json_encode($grafikKehadiran['hadir']) ?>,
                    sakit: <?= json_encode($grafikKehadiran['sakit']) ?>,
                    izin: <?= json_encode($grafikKehadiran['izin']) ?>,
                    alfa: <?= json_encode($grafikKehadiran['alfa']) ?>
                };

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            {
                                label: 'Hadir',
                                data: data.hadir,
                                borderColor: chartColors.hadir.border,
                                backgroundColor: chartColors.hadir.bg
                            },
                            {
                                label: 'Sakit',
                                data: data.sakit,
                                borderColor: chartColors.sakit.border,
                                backgroundColor: chartColors.sakit.bg
                            },
                            {
                                label: 'Izin',
                                data: data.izin,
                                borderColor: chartColors.izin.border,
                                backgroundColor: chartColors.izin.bg
                            },
                            {
                                label: 'Alfa',
                                data: data.alfa,
                                borderColor: chartColors.alfa.border,
                                backgroundColor: chartColors.alfa.bg
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleFont: { size: 14 },
                                bodyFont: { size: 13 },
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function (context) {
                                        return context.dataset.label + ': ' + context.parsed.y + ' siswa';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                stacked: false,
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function (value) {
                                        if (Number.isInteger(value)) return value;
                                    }
                                },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: {
                                stacked: false,
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        }

        $(document).ready(function () {
            initTeacherCharts();
        });
    </script>
<?php endif; ?>
<?= $this->endSection() ?>