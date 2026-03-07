<?= $this->extend('templates/starting_page_layout') ?>

<?= $this->section('content') ?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center pt-5">
                <div class="col-md-5 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Portal Cek Kehadiran</h4>
                            <p class="card-category">Masukkan NIS dan Nomor HP orang tua untuk melihat riwayat</p>
                        </div>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('cek-kehadiran/view') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="form-group mt-3">
                                    <label class="bmd-label-floating">NIS Siswa</label>
                                    <input type="text" name="nis" class="form-control" required value="<?= old('nis') ?>">
                                </div>
                                <div class="form-group mt-4">
                                    <label class="bmd-label-floating">Nomor HP Orang Tua (Terdaftar)</label>
                                    <input type="text" name="no_hp" class="form-control" required value="<?= old('no_hp') ?>">
                                    <small class="text-muted">Gunakan nomor yang sama dengan yang menerima notifikasi WA.</small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-4">Lihat Riwayat</button>
                                <a href="<?= base_url() ?>" class="btn btn-default btn-block">Kembali</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
