<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Audit Log</h4>
                            <p class="card-category">Riwayat perubahan data manual oleh Admin/Guru</p>
                        </div>
                        <button class="btn btn-white btn-just-icon btn-round" onclick="location.reload()">
                            <i class="material-icons text-primary">refresh</i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tableAudit">
                                <thead class="text-primary">
                                    <th>Waktu</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                    <th>Data Lama</th>
                                    <th>Data Baru</th>
                                    <th>IP Address</th>
                                </thead>
                                <tbody>
                                    <?php if (empty($logs)): ?>
                                        <tr><td colspan="6" class="text-center">Belum ada riwayat aktivitas.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($logs as $l): ?>
                                        <tr>
                                            <td><small><?= date('d/m/Y H:i', strtotime($l['created_at'])) ?></small></td>
                                            <td><b><?= $l['username'] ?? 'System' ?></b></td>
                                            <td><?= $l['aksi'] ?></td>
                                            <td><small class="text-muted"><?= $l['data_lama'] ?: '-' ?></small></td>
                                            <td><small class="text-info"><?= $l['data_baru'] ?: '-' ?></small></td>
                                            <td><small><?= $l['ip_address'] ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#tableAudit').DataTable({
            "order": [[ 0, "desc" ]],
            "pageLength": 50
        });
    });
</script>
<?= $this->endSection() ?>
