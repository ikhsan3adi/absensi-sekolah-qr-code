<?= $this->extend('templates/starting_page_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
<style>
    @media print {
        .navbar, .sidebar, .btn, .footer, .main-panel::after, .btn-print-area, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info {
            display: none !important;
        }
        .main-panel {
            width: 100% !important;
            float: none !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        body {
            background-color: white !important;
        }
        .table-responsive {
            overflow: visible !important;
        }
    }
    /* Style adjustment for DataTables */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #00bcd4 !important;
        color: white !important;
        border: none !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 m-auto">
                    <div class="card mt-4">
                        <div class="card-header card-header-info d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Laporan Kehadiran Mandiri</h4>
                                <p class="card-category">Bulan: <?= $monthName ?></p>
                            </div>
                            <button type="button" class="btn btn-white btn-sm btn-print-area" onclick="window.print()">
                                <i class="material-icons text-info">print</i> Cetak / Simpan PDF
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Informasi Siswa -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr><td>NIS</td><td>: <b><?= $siswa['nis'] ?></b></td></tr>
                                        <tr><td>Nama</td><td>: <b><?= $siswa['nama_siswa'] ?></b></td></tr>
                                        <tr><td>Poin Pelanggaran</td><td>: <span class="badge badge-warning"><?= $siswa['poin_pelanggaran'] ?> Poin</span></td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="row text-center">
                                        <div class="col-3"><h5 class="text-success mb-0">Hadir</h5><h3><?= $stats['hadir'] ?></h3></div>
                                        <div class="col-3"><h5 class="text-warning mb-0">Sakit</h5><h3><?= $stats['sakit'] ?></h3></div>
                                        <div class="col-3"><h5 class="text-info mb-0">Izin</h5><h3><?= $stats['izin'] ?></h3></div>
                                        <div class="col-3"><h5 class="text-danger mb-0">Alfa</h5><h3><?= $stats['alfa'] ?></h3></div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Riwayat Tabel -->
                            <div class="table-responsive">
                                <table class="table table-hover" id="tableRiwayat">
                                    <thead class="text-info">
                                        <th>Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($history)): ?>
                                            <tr><td colspan="5" class="text-center">Belum ada data kehadiran.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($history as $h): ?>
                                            <tr>
                                                <td><span style="display:none"><?= $h['tanggal'] ?></span><?= date('d/m/Y', strtotime($h['tanggal'])) ?></td>
                                                <td><?= $h['jam_masuk'] ?? '-' ?></td>
                                                <td><?= $h['jam_keluar'] ?? '-' ?></td>
                                                <td>
                                                    <?php 
                                                        $status = 'Hadir'; $cls = 'success';
                                                        if($h['id_kehadiran'] == 2) { $status = 'Sakit'; $cls = 'warning'; }
                                                        elseif($h['id_kehadiran'] == 3) { $status = 'Izin'; $cls = 'info'; }
                                                        elseif($h['id_kehadiran'] == 4) { $status = 'Alfa'; $cls = 'danger'; }
                                                    ?>
                                                    <span class="badge badge-<?= $cls ?>"><?= $status ?></span>
                                                </td>
                                                <td>
                                                    <?php if($h['menit_keterlambatan'] > 0): ?>
                                                        <small class="text-danger">Terlambat <?= $h['menit_keterlambatan'] ?> mnt</small>
                                                    <?php else: ?>
                                                        <?= $h['keterangan'] ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center mt-4">
                                <a href="<?= base_url('cek-kehadiran') ?>" class="btn btn-default">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableRiwayat').DataTable({
            "pageLength": 50,
            "order": [[ 0, "desc" ]],
            "language": {
                "search": "Cari Riwayat:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada data yang cocok",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": {
                    "next": "Lanjut",
                    "previous": "Kembali"
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
