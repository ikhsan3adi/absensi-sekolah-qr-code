<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('styles') ?>
<style>
    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Custom colors for Chartist lines */
    /* Hadir - Green */
    .ct-chart .ct-series-a .ct-line,
    .ct-chart .ct-series-a .ct-point {
        stroke: #4caf50 !important;
    }

    /* Sakit - Orange */
    .ct-chart .ct-series-b .ct-line,
    .ct-chart .ct-series-b .ct-point {
        stroke: #ff9800 !important;
    }

    /* Izin - Cyan */
    .ct-chart .ct-series-c .ct-line,
    .ct-chart .ct-series-c .ct-point {
        stroke: #00bcd4 !important;
    }

    /* Alfa - Red */
    .ct-chart .ct-series-d .ct-line,
    .ct-chart .ct-series-d .ct-point {
        stroke: #f44336 !important;
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
                                        <option value="<?= $k['id_kelas'] ?>"><?= $k['kelas'] ?> (<?= $k['jumlah_siswa'] ?? 0 ?> siswa)</option>
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
                            'total' => $totalSiswa
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header card-header-success">
                        <h4 class="card-title"><b>Absensi Guru Hari Ini</b></h4>
                        <p class="card-category">
                            <?= $dateNow; ?>
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-2">
                                <h5 class="text-success text-nowrap"><b>Hadir</b></h5>
                                <h4 class="text-nowrap">
                                    <?= $jumlahKehadiranGuru['hadir']; ?>
                                </h4>
                            </div>
                            <div class="col-2">
                                <h5 class="text-warning text-nowrap"><b>Sakit</b></h5>
                                <h4 class="text-nowrap">
                                    <?= $jumlahKehadiranGuru['sakit']; ?>
                                </h4>
                            </div>
                            <div class="col-2">
                                <h5 class="text-info text-nowrap"><b>Izin</b></h5>
                                <h4 class="text-nowrap">
                                    <?= $jumlahKehadiranGuru['izin']; ?>
                                </h4>
                            </div>
                            <div class="col-2">
                                <h5 class="text-danger text-nowrap"><b>Alfa</b></h5>
                                <h4 class="text-nowrap">
                                    <?= $jumlahKehadiranGuru['alfa']; ?>
                                </h4>
                            </div>
                            <div class="col-1">
                                <div class="border-right mx-auto h-100" style="width: 0;"></div>
                            </div>
                            <div class="col-2 col-sm-3">
                                <h5 class="text-primary text-nowrap"><b>Total</b></h5>
                                <h4 class="text-nowrap">
                                    <?= $totalSiswa; ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART SISWA -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-chart">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title" id="titleSiswaChart">Tingkat Kehadiran Siswa</h4>
                        <p class="card-category">
                            Statistik kehadiran 7 hari terakhir | <?= $dateNow; ?>
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="ct-chart" id="kehadiranSiswa"></div>
                        <!-- CHART LEGEND -->
                        <div class="chart-legend mb-3">
                            <span class="legend-item"><span class="legend-dot bg-success"></span> Hadir</span>
                            <span class="legend-item"><span class="legend-dot bg-warning"></span> Sakit</span>
                            <span class="legend-item"><span class="legend-dot bg-info"></span> Izin</span>
                            <span class="legend-item"><span class="legend-dot bg-danger"></span> Alfa</span>
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
                <div class="card card-chart">
                    <div class="card-header card-header-success">
                        <h4 class="card-title">Tingkat Kehadiran Guru</h4>
                        <p class="card-category">
                            Statistik kehadiran 7 hari terakhir | <?= $dateNow; ?>
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="ct-chart" id="kehadiranGuru"></div>
                        <!-- CHART LEGEND -->
                        <div class="chart-legend mb-3">
                            <span class="legend-item"><span class="legend-dot bg-success"></span> Hadir</span>
                            <span class="legend-item"><span class="legend-dot bg-warning"></span> Sakit</span>
                            <span class="legend-item"><span class="legend-dot bg-info"></span> Izin</span>
                            <span class="legend-item"><span class="legend-dot bg-danger"></span> Alfa</span>
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
<!-- Chartist JS -->
<script src="<?= base_url('assets/js/plugins/chartist.min.js') ?>"></script>
<script>
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
                        const className = $('#filterKelas option:selected').text();
                        if (idKelas == "") {
                            $('#titleSiswaChart').text("Tingkat Kehadiran Siswa");
                        } else {
                            const kelasName = className.split(' (')[0];
                            $('#titleSiswaChart').text("Tingkat Kehadiran Siswa " + kelasName);
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

    let kehadiranSiswaChart;
    let kehadiranGuruChart;

    const optionsBase = {
        lineSmooth: Chartist.Interpolation.cardinal({
            tension: 0
        }),
        low: 0,
        height: '360px',
        chartPadding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        },
        plugins: [
            //! THIS DOESNT WORK!
            // TODO: CHARTIST WILL BE REPLACED BY SOME BETTER LIB
            // Chartist.plugins.tooltip(),
            // Chartist.plugins.ctPointLabels({
            //     textAnchor: 'middle',
            //     labelInterpolationFnc: function (value) { return value ? value : 0 }
            // })
        ]
    };

    const chartLabels = [
        <?php foreach ($dateRange as $value): ?>
                                '<?= $value ?>',
        <?php endforeach; ?>
    ];

    function updateSiswaChart(newData) {
        if (kehadiranSiswaChart) {
            const data = {
                labels: chartLabels,
                series: [newData.hadir, newData.sakit, newData.izin, newData.alfa]
            };

            const highestData = Math.max(...data.series.flat(), 0);

            const options = {
                ...optionsBase,
                high: highestData + Math.max(highestData / 4, 5),
            };

            kehadiranSiswaChart.update(data, options);
            md.startAnimationForLineChart(kehadiranSiswaChart);
        }
    }

    function updateGuruChart(newData) {
        if (kehadiranGuruChart) {
            const data = {
                labels: chartLabels,
                series: [newData.hadir, newData.sakit, newData.izin, newData.alfa]
            };

            const highestData = Math.max(...data.series.flat(), 0);

            const options = {
                ...optionsBase,
                high: highestData + Math.max(highestData / 4, 5),
            };

            kehadiranGuruChart.update(data, options);
            md.startAnimationForLineChart(kehadiranGuruChart);
        }
    }

    function initDashboardPageCharts() {
        if ($('#kehadiranSiswa').length != 0) {
            const dataSiswa = {
                hadir: [<?php echo implode(',', $grafikKehadiranSiswa['hadir']); ?>],
                sakit: [<?php echo implode(',', $grafikKehadiranSiswa['sakit']); ?>],
                izin: [<?php echo implode(',', $grafikKehadiranSiswa['izin']); ?>],
                alfa: [<?php echo implode(',', $grafikKehadiranSiswa['alfa']); ?>]
            };

            kehadiranSiswaChart = new Chartist.Line('#kehadiranSiswa', {}, optionsBase);
            updateSiswaChart(dataSiswa);
        }

        if ($('#kehadiranGuru').length != 0) {
            const dataGuru = {
                hadir: [<?php echo implode(',', $grafikKehadiranGuru['hadir']); ?>],
                sakit: [<?php echo implode(',', $grafikKehadiranGuru['sakit']); ?>],
                izin: [<?php echo implode(',', $grafikKehadiranGuru['izin']); ?>],
                alfa: [<?php echo implode(',', $grafikKehadiranGuru['alfa']); ?>]
            };

            kehadiranGuruChart = new Chartist.Line('#kehadiranGuru', {}, optionsBase);
            updateGuruChart(dataGuru);
        }
    }
</script>
<?= $this->endSection() ?>