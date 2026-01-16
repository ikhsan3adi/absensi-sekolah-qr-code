<?= $this->extend('templates/admin_page_layout'); ?>

<?= $this->section('content'); ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                        <span>
                            <?= session()->getFlashdata('success'); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                        <span>
                            <?= session()->getFlashdata('error'); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Database Backup/Restore -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Database</h4>
                        <p class="card-category">Backup & Restore Database</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Backup Database</h5>
                            <p>Download file .sql database saat ini.</p>
                            <a href="<?= base_url('admin/backup/db/backup'); ?>" class="btn btn-primary">
                                <i class="material-icons">cloud_download</i> Download Backup
                            </a>
                        </div>
                        <hr>
                        <div>
                            <h5>Restore Database</h5>
                            <p class="text-warning">Peringatan: Tindakan ini akan menimpa database saat ini.</p>
                            <form action="<?= base_url('admin/backup/db/restore'); ?>" method="post"
                                enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Pilih file</label>
                                    <input type="file" name="file_backup_db" class="form-control" accept=".sql" required
                                        style="opacity: 1 !important; position: relative !important; z-index: 1 !important;">
                                </div>
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Apakah Anda yakin ingin merestore database? Data saat ini akan ditimpa!')">
                                    <i class="material-icons">cloud_upload</i> Restore Database
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photos Backup/Restore -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-info">
                        <h4 class="card-title">Foto (QR Code)</h4>
                        <p class="card-category">Backup & Restore folder uploads</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Backup Foto</h5>
                            <p>Download file .zip berisi semua foto QR code.</p>
                            <a href="<?= base_url('admin/backup/photos/backup'); ?>" class="btn btn-info">
                                <i class="material-icons">cloud_download</i> Download Backup
                            </a>
                        </div>
                        <hr>
                        <div>
                            <h5>Restore Foto</h5>
                            <p class="text-warning">Peringatan: Foto yang ada akan ditimpa jika nama file sama.</p>
                            <form action="<?= base_url('admin/backup/photos/restore'); ?>" method="post"
                                enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Pilih file</label>
                                    <input type="file" name="file_backup_photos" class="form-control" accept=".zip"
                                        required
                                        style="opacity: 1 !important; position: relative !important; z-index: 1 !important;">
                                </div>
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Apakah Anda yakin ingin merestore foto?')">
                                    <i class="material-icons">cloud_upload</i> Restore Foto
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection(); ?>