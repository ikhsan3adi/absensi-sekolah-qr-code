<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <?php if (isset($no_class) && $no_class): ?>
            <div class="alert alert-warning">
                Anda belum ditugaskan sebagai Wali Kelas di kelas manapun.
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header card-header-primary d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="card-title"><?= $title ?></h4>
                                                <p class="card-category">Kelola pengajuan izin dan sakit siswa di kelas Anda</p>
                                            </div>
                                            <button type="button" class="btn btn-white btn-round btn-just-icon" onclick="location.reload()" title="Refresh Data">
                                                <i class="material-icons text-primary">refresh</i>
                                            </button>
                                        </div>
                                        <div class="card-body">                            <div class="table-responsive">
                                <table id="tablePerizinan" class="table table-hover">
                                    <thead class="text-primary">
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Alasan</th>
                                        <th>Bukti</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($perizinan)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada data pengajuan.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($perizinan as $index => $p): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <b><?= $p['nama_siswa'] ?></b><br>
                                                    <small><?= $p['nis'] ?></small>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($p['tanggal_mulai'])) ?> 
                                                    <?php if($p['tanggal_mulai'] != $p['tanggal_selesai']): ?>
                                                        - <?= date('d/m/Y', strtotime($p['tanggal_selesai'])) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $p['tipe_izin'] == 'Sakit' ? 'warning' : 'info' ?>">
                                                        <?= $p['tipe_izin'] ?>
                                                    </span>
                                                </td>
                                                <td><?= $p['alasan'] ?></td>
                                                <td>
                                                    <a href="<?= base_url('uploads/perizinan/' . $p['bukti']) ?>" target="_blank">
                                                        <img src="<?= base_url('uploads/perizinan/' . $p['bukti']) ?>" width="50" class="img-thumbnail">
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if ($p['status'] == 'Pending'): ?>
                                                        <span class="badge badge-default">Pending</span>
                                                    <?php elseif ($p['status'] == 'Disetujui'): ?>
                                                        <span class="badge badge-success">Disetujui</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="td-actions">
                                                    <?php if ($p['status'] == 'Pending'): ?>
                                                        <button type="button" class="btn btn-success btn-round btn-sm btn-konfirmasi" data-id="<?= $p['id_perizinan'] ?>" data-status="Disetujui">
                                                            <i class="material-icons">check</i> Setujui
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-round btn-sm btn-konfirmasi" data-id="<?= $p['id_perizinan'] ?>" data-status="Ditolak">
                                                            <i class="material-icons">close</i> Tolak
                                                        </button>
                                                    <?php else: ?>
                                                        <small class="text-muted">Sudah diproses</small>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tablePerizinan').DataTable({
            columnDefs: [{ orderable: false, targets: [-1] }],
            order: [[2, 'desc']]
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const btns = document.querySelectorAll('.btn-konfirmasi');
        btns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const status = this.getAttribute('data-status');

                swal({
                    title: "Konfirmasi",
                    text: `Apakah Anda yakin ingin mengubah status menjadi ${status}?`,
                    icon: "warning",
                    buttons: ["Batal", "Ya, Lanjutkan"],
                    dangerMode: status === 'Ditolak',
                }).then((willProcess) => {
                    if (willProcess) {
                        fetch('<?= base_url('teacher/perizinan/konfirmasi') ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: `id_perizinan=${id}&status=${status}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.status === 'success') {
                                    swal("Berhasil", result.message, "success").then(() => {
                                        location.reload();
                                    });
                                } else {
                                    swal("Gagal", result.message, "error");
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                swal("Error", "Terjadi kesalahan saat memproses data.", "error");
                            });
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>
