<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <?= view('admin/_messages'); ?>
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title"><b>Form Tambah Jurusan</b></h4>
          </div>
          <div class="card-body mx-5 my-3">

            <form action="<?= base_url('admin/jurusan/tambahJurusanPost'); ?>" method="post">
              <?= csrf_field() ?>
              <div class="form-group mt-4">
                <label for="jurusan">Nama jurusan</label>
                <input type="text" id="jurusan" class="form-control <?= invalidFeedback('jurusan') ? 'is-invalid' : ''; ?>" name="jurusan" placeholder="IPA, IPS" , value="<?= old('jurusan'); ?>" required>
                <div class="invalid-feedback">
                  <?= invalidFeedback('jurusan'); ?>
                </div>
              </div>
              <button type="submit" class="btn btn-primary mt-4">Simpan</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>