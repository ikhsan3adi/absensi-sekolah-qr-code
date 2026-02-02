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
        <!-- REKAP JUMLAH DATA -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-primary card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/siswa'); ?>" class="text-white">
                                <i class="material-icons">person</i>
                            </a>
                        </div>
                        <p class="card-category">Jumlah siswa</p>
                        <h3 class="card-title"><?= count($siswa); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-primary">check</i>
                            Terdaftar
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/guru'); ?>" class="text-white">
                                <i class="material-icons">person_4</i>
                            </a>
                        </div>
                        <p class="card-category">Jumlah guru</p>
                        <h3 class="card-title"><?= count($guru); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-success">check</i>
                            Terdaftar
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/kelas'); ?>" class="text-white">
                                <i class="material-icons">grade</i>
                            </a>
                        </div>
                        <p class="card-category">Kelas / Jurusan</p>
                        <h3 class="card-title text-nowrap"><?= count($kelas) . ' / ' . count($jurusan); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">home</i>
                            <?= $generalSettings->school_name; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-danger card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/petugas'); ?>" class="text-white">
                                <i class="material-icons">settings</i>
                            </a>
                        </div>
                        <p class="card-category">Jumlah petugas</p>
                        <h3 class="card-title"><?= count($petugas); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">person</i>
                            Petugas dan Administrator
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATS HARI INI -->
        <div class="row">
            <div class="col-md-4">
                <!-- FILTER KELAS -->
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><b>Filter Kelas</b></h4>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <select name="id_kelas" id="filterKelas" class="custom-select">
                                    <option value="">-- Semua Kelas (<?= count($siswa) ?> siswa) --</option>
                                    <?php foreach ($kelas as $k): ?>
                                        <option value="<?= $k['id_kelas'] ?>" data-kelas="<?= $k['kelas'] ?>">
                                            <?= $k['kelas'] ?> (<?= $k['jumlah_siswa'] ?? 0 ?> siswa)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div id="filterLoader" class="col-2" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><b id="titleSiswaStats">Absensi Siswa Hari Ini</b></h4>
                        <p class="card-category"><?= $dateNow; ?></p>
                    </div>
                    <div class="card-body" id="siswaStatsContainer">
                        <?= view('admin/_dashboard_siswa_stats', [
                            'hadir' => $jumlahKehadiranSiswa['hadir'],
                            'sakit' => $jumlahKehadiranSiswa['sakit'],
                            'izin' => $jumlahKehadiranSiswa['izin'],
                            'alfa' => $jumlahKehadiranSiswa['alfa'],
                            'totalSiswa' => $totalSiswa
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header card-header-success">
                        <h4 class="card-title"><b>Absensi Guru Hari Ini</b></h4>
                        <p class="card-category"><?= $dateNow; ?></p>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-2">
                                <h5 class="text-success text-nowrap"><b>Hadir</b></h5>
                                <h4 class="text-nowrap"><?= $jumlahKehadiranGuru['hadir']; ?></h4>
                            </div>
                            <div class="col-2">
                                <h5 class="text-warning text-nowrap"><b>Sakit</b></h5>
                                <h4 class="text-nowrap"><?= $jumlahKehadiranGuru['sakit']; ?></h4>
                            </div>
                            <div class="col-2">
                                <h5 class="text-info text-nowrap"><b>Izin</b></h5>
                                <h4 class="text-nowrap"><?= $jumlahKehadiranGuru['izin']; ?></h4>
                            </div>
                            <div class="col-2">
                                <h5 class="text-danger text-nowrap"><b>Alfa</b></h5>
                                <h4 class="text-nowrap"><?= $jumlahKehadiranGuru['alfa']; ?></h4>
                            </div>
                            <div class="col-1">
                                <div class="border-right mx-auto h-100" style="width: 0;"></div>
                            </div>
                            <div class="col-2 col-sm-3">
                                <h5 class="text-primary text-nowrap"><b>Total</b></h5>
                                <h4 class="text-nowrap"><?= $totalGuru; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART SISWA -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title" id="titleSiswaChart">Tingkat Kehadiran Siswa</h4>
                        <p class="card-category">Statistik kehadiran 7 hari terakhir | <?= $dateNow; ?></p>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="kehadiranSiswa"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-primary">checklist</i> <a class="text-primary" href="<?= base_url('admin/absen-siswa'); ?>">Lihat data</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART GURU -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-header-success">
                        <h4 class="card-title">Tingkat Kehadiran Guru</h4>
                        <p class="card-category">Statistik kehadiran 7 hari terakhir | <?= $dateNow; ?></p>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="kehadiranGuru"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-success">checklist</i> <a class="text-success" href="<?= base_url('admin/absen-guru'); ?>">Lihat data</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js CDN -->
<script src="<?= base_url('assets/js/plugins/chartjs/chart.umd.min.js') ?>"></script>
<script>
    let kehadiranSiswaChart;
    let kehadiranGuruChart;

    const chartLabels = <?= json_encode($dateRange) ?>;

    const chartColors = {
        hadir: { border: '#4caf50', bg: 'rgba(76, 175, 80, 1)' },
        sakit: { border: '#ff9800', bg: 'rgba(255, 152, 0, 1)' },
        izin: { border: '#00bcd4', bg: 'rgba(0, 188, 212, 1)' },
        alfa: { border: '#f44336', bg: 'rgba(244, 67, 54, 1)' }
    };

    function createChartConfig(data) {
        return {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Hadir',
                        data: data.hadir,
                        borderColor: chartColors.hadir.border,
                        backgroundColor: chartColors.hadir.bg,
                        tension: 0.3,
                        fill: false,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Sakit',
                        data: data.sakit,
                        borderColor: chartColors.sakit.border,
                        backgroundColor: chartColors.sakit.bg,
                        tension: 0.3,
                        fill: false,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Izin',
                        data: data.izin,
                        borderColor: chartColors.izin.border,
                        backgroundColor: chartColors.izin.bg,
                        tension: 0.3,
                        fill: false,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Alfa',
                        data: data.alfa,
                        borderColor: chartColors.alfa.border,
                        backgroundColor: chartColors.alfa.bg,
                        tension: 0.3,
                        fill: false,
                        pointRadius: 4,
                        pointHoverRadius: 6
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
                                return context.dataset.label + ': ' + context.parsed.y + ' orang';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        stacked: true,
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
                        stacked: true,
                        grid: { display: false }
                    }
                }
            }
        };
    }

    function updateSiswaChart(newData) {
        if (kehadiranSiswaChart) {
            kehadiranSiswaChart.data.datasets[0].data = newData.hadir;
            kehadiranSiswaChart.data.datasets[1].data = newData.sakit;
            kehadiranSiswaChart.data.datasets[2].data = newData.izin;
            kehadiranSiswaChart.data.datasets[3].data = newData.alfa;
            kehadiranSiswaChart.update('active');
        }
    }

    function initDashboardPageCharts() {
        const siswaCtx = document.getElementById('kehadiranSiswa');
        if (siswaCtx) {
            const dataSiswa = {
                hadir: <?= json_encode($grafikKehadiranSiswa['hadir']) ?>,
                sakit: <?= json_encode($grafikKehadiranSiswa['sakit']) ?>,
                izin: <?= json_encode($grafikKehadiranSiswa['izin']) ?>,
                alfa: <?= json_encode($grafikKehadiranSiswa['alfa']) ?>
            };
            kehadiranSiswaChart = new Chart(siswaCtx, createChartConfig(dataSiswa));
        }

        const guruCtx = document.getElementById('kehadiranGuru');
        if (guruCtx) {
            const dataGuru = {
                hadir: <?= json_encode($grafikKehadiranGuru['hadir']) ?>,
                sakit: <?= json_encode($grafikKehadiranGuru['sakit']) ?>,
                izin: <?= json_encode($grafikKehadiranGuru['izin']) ?>,
                alfa: <?= json_encode($grafikKehadiranGuru['alfa']) ?>
            };
            kehadiranGuruChart = new Chart(guruCtx, createChartConfig(dataGuru));
        }
    }

    $(document).ready(function () {
        initDashboardPageCharts();

        $('#filterKelas').on('change', function () {
            const idKelas = $(this).val();
            const loader = $('#filterLoader');

            loader.show();

            $.ajax({
                url: '<?= base_url('admin/dashboard/filter-data') ?>',
                type: 'POST',
                data: setAjaxData({ id_kelas: idKelas }),
                success: function (response) {
                    const obj = JSON.parse(response);
                    if (obj.result == 1) {
                        $('#siswaStatsContainer').html(obj.htmlContent);
                        updateSiswaChart(obj.chartData);

                        // Update Titles
                        const className = $('#filterKelas option:selected').attr('data-kelas');
                        if (idKelas == "") {
                            $('#titleSiswaStats').text("Absensi Siswa Hari Ini");
                            $('#titleSiswaChart').text("Tingkat Kehadiran Siswa");
                        } else {
                            $('#titleSiswaStats').text("Absensi Siswa " + className + " Hari Ini");
                            $('#titleSiswaChart').text("Tingkat Kehadiran Siswa " + className);
                        }
                    }
                },
                error: function (xhr, status, thrown) {
                    console.error(thrown);
                },
                complete: function () {
                    loader.hide();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>