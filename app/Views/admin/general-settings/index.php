<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?= view('admin/_messages'); ?>
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><b>Pengaturan</b></h4>
                    </div>
                    <div class="card-body mx-5 my-3">

                        <form action="<?= base_url('admin/general-settings/update'); ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="form-group mt-4">
                                <label for="school_name">Nama Sekolah</label>
                                <input type="text" id="school_name" class="form-control <?= invalidFeedback('school_name') ? 'is-invalid' : ''; ?>" name="school_name" placeholder="SMK 1 Indonesia" value="<?= $generalSettings->school_name; ?>" required>
                                <div class="invalid-feedback">
                                    <?= invalidFeedback('school_name'); ?>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label for="school_year">Tahun Ajaran</label>
                                <input type="text" id="school_year" class="form-control <?= invalidFeedback('school_year') ? 'is-invalid' : ''; ?>" name="school_year" placeholder="2024/2025" value="<?= $generalSettings->school_year; ?>" required>
                                <div class="invalid-feedback">
                                    <?= invalidFeedback('school_year'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mt-4">
                                        <label for="copyright">Copyright</label>
                                        <input type="text" id="copyright" class="form-control <?= invalidFeedback('copyright') ? 'is-invalid' : ''; ?>" name="copyright" placeholder="@ 2024 All" value="<?= $generalSettings->copyright; ?>" required>
                                        <div class="invalid-feedback">
                                            <?= invalidFeedback('copyright'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="logo">Logo</label>
                                        <div style="margin-bottom: 10px; border: 1px solid #eee; padding: 10px; width: auto;">
                                            <img id="logo" src="<?= getLogo(); ?>" alt="logo" style="max-width: 250px; max-height: 250px;">
                                        </div>
                                        <div class="display-block">
                                            <button type="button" onclick="$('#logo-upload').trigger('click');" class="btn btn-primary btn-sm btn-file-upload">
                                                Ganti
                                            </button>
                                            <input type="file" id="logo-upload" name="logo" size="40" accept="image/jpg,image/jpeg,image/png,image/gif,image/svg+xml" onchange="$('#upload-file-info1').html($(this).val().replace(/.*[\/\\]/, ''));">
                                            <span class="text-sm text-secondary">(.png, .jpg, .jpeg, .gif, .svg)</span>
                                        </div>
                                        <span class='label label-info' id="upload-file-info1"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                        </form>

                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>