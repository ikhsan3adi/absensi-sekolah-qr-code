<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <div class="row">
            <!-- Form Tambah -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Tambah Hari Libur</h4>
                        <p class="card-category">Tandai tanggal libur sekolah</p>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('admin/holiday/save') ?>" method="post" class="mt-3">
                            <?= csrf_field() ?>
                            <div class="form-group mb-4">
                                <label class="label-control">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control" required value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group mb-4">
                                <label class="label-control">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" required value="<?= date('Y-m-d') ?>">
                                <small class="text-muted">Gunakan tanggal yang sama jika hanya libur 1 hari.</small>
                            </div>
                            <div class="form-group mb-4">
                                <label class="bmd-label-floating">Keterangan (Contoh: Libur Semester Ganjil)</label>
                                <input type="text" name="keterangan" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header card-header-info d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Daftar Hari Libur</h4>
                            <p class="card-category">Bulan: <?= date('F', mktime(0, 0, 0, $selectedMonth, 10)) ?> <?= $selectedYear ?></p>
                        </div>
                        <div>
                            <a href="<?= base_url('admin/holiday/generate-weekend') ?>" class="btn btn-white btn-sm text-info mr-2">
                                <i class="material-icons">calendar_today</i> Generate Hari Non-Kerja
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Bulan -->
                        <form action="<?= base_url('admin/holiday') ?>" method="get" class="row mb-4">
                            <div class="col-md-5">
                                <select name="month" class="custom-select">
                                    <?php for($m=1; $m<=12; $m++): ?>
                                        <option value="<?= sprintf('%02d', $m) ?>" <?= $selectedMonth == $m ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="year" class="custom-select">
                                    <?php for($y=date('Y')-1; $y<=date('Y')+2; $y++): ?>
                                        <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info btn-block mt-0">Filter</button>
                            </div>
                        </form>

                        <form id="formBulkDelete" action="<?= base_url('admin/holiday/bulk-delete') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm" disabled>
                                    <i class="material-icons">delete_sweep</i> Hapus Terpilih
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table id="tableHoliday" class="table table-hover">
                                    <thead class="text-info">
                                        <th width="40">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" id="checkAll">
                                                    <span class="form-check-sign"><span class="check"></span></span>
                                                </label>
                                            </div>
                                        </th>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($holidays)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada hari libur di bulan ini.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($holidays as $index => $h): ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input holiday-checkbox" type="checkbox" name="holiday_ids[]" value="<?= $h['id'] ?>">
                                                            <span class="form-check-sign"><span class="check"></span></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td><?= $index + 1 ?></td>
                                                <td><b><?= date('d/m/Y', strtotime($h['tanggal'])) ?></b></td>
                                                <td><?= $h['keterangan'] ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-link btn-sm btn-delete-single" data-id="<?= $h['id'] ?>">
                                                        <i class="material-icons">delete</i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableHoliday').DataTable({
            columnDefs: [{ orderable: false, targets: [0, -1] }],
            order: [[2, 'desc']]
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.holiday-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const formBulkDelete = document.getElementById('formBulkDelete');

        // Check All functionality
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            toggleBulkButton();
        });

        // Individual checkbox functionality
        checkboxes.forEach(cb => {
            cb.addEventListener('change', toggleBulkButton);
        });

        function toggleBulkButton() {
            const checkedCount = document.querySelectorAll('.holiday-checkbox:checked').length;
            btnBulkDelete.disabled = checkedCount === 0;
            if (checkedCount > 0) {
                btnBulkDelete.innerHTML = `<i class="material-icons">delete_sweep</i> Hapus Terpilih (${checkedCount})`;
            } else {
                btnBulkDelete.innerHTML = `<i class="material-icons">delete_sweep</i> Hapus Terpilih`;
            }
        }

        // Bulk Delete Action
        btnBulkDelete.addEventListener('click', function() {
            swal({
                title: "Hapus Masal?",
                text: "Hari libur yang dipilih akan dihapus dan presensi akan kembali aktif pada tanggal tersebut.",
                icon: "warning",
                buttons: ["Batal", "Hapus Semua"],
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    formBulkDelete.submit();
                }
            });
        });

        // Single Delete Action
        const deleteSingleBtns = document.querySelectorAll('.btn-delete-single');
        deleteSingleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                swal({
                    title: "Hapus Hari Libur?",
                    text: "Sistem akan kembali mengaktifkan presensi di tanggal ini.",
                    icon: "warning",
                    buttons: ["Batal", "Hapus"],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        const form = document.createElement('form');
                        form.action = `<?= base_url('admin/holiday/delete') ?>/${id}`;
                        form.method = 'POST';
                        form.innerHTML = `<?= csrf_field() ?><input type="hidden" name="_method" value="DELETE">`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>
