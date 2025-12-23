<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><b>Generate Laporan Kelas
                                <?= $kelas['kelas']; ?></b></h4>
                        <p class="card-category">Pilih bulan untuk mendownload laporan presensi</p>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('teacher/laporan/generate'); ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group is-filled">
                                        <label class="bmd-label-static">Pilih Bulan</label>
                                        <input type="month" id="bulan" name="bulan" class="form-control"
                                            value="<?= date('Y-m'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group is-filled">
                                        <label class="bmd-label-static">Format Laporan</label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="pdf">PDF</option>
                                            <option value="doc">Word (.doc)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-4">Generate & Download</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>