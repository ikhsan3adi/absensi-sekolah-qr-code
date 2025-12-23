<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title"><b>Form Edit Kelas</b></h4>
          </div>
          <div class="card-body mx-5 my-3">

            <form action="<?= base_url('admin/kelas/editKelasPost'); ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= esc($kelas->id_kelas); ?>">
              <input type="hidden" name="back_url" value="<?= currentFullURL(); ?>">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mt-4">
                    <label for="tingkat">Tingkat</label>
                    <input type="text" id="tingkat"
                      class="form-control <?= invalidFeedback('tingkat') ? 'is-invalid' : ''; ?>" name="tingkat"
                      placeholder="'X', 'XI', 'XII'" value="<?= old('tingkat') ?? $kelas->tingkat ?? '' ?>" required>
                    <div class="invalid-feedback">
                      <?= invalidFeedback('tingkat'); ?>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mt-4">
                    <label for="index_kelas">Index Kelas</label>
                    <input type="text" id="index_kelas"
                      class="form-control <?= invalidFeedback('index_kelas') ? 'is-invalid' : ''; ?>" name="index_kelas"
                      placeholder="'1', '2', 'A'" value="<?= old('index_kelas') ?? $kelas->index_kelas ?? '' ?>"
                      required>
                    <div class="invalid-feedback">
                      <?= invalidFeedback('index_kelas'); ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label for="id_jurusan">Jurusan</label>
                <select class="custom-select <?= invalidFeedback('id_jurusan') ? 'is-invalid' : ''; ?>" id="id_jurusan"
                  name="id_jurusan">
                  <option value="">--Pilih Jurusan--</option>
                  <?php foreach ($jurusan as $value): ?>
                    <option value="<?= $value['id']; ?>" <?= $kelas->id_jurusan == $value['id'] ? 'selected' : ''; ?>>
                      <?= $value['jurusan']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">
                  <?= invalidFeedback('id_jurusan'); ?>
                </div>
              </div>
              <div class="col-md-6">
                <label for="id_wali_kelas">Wali Kelas</label>
                <select class="custom-select <?= invalidFeedback('id_wali_kelas') ? 'is-invalid' : ''; ?>"
                  id="id_wali_kelas" name="id_wali_kelas">
                  <option value="">--Pilih Wali Kelas--</option>
                  <?php foreach ($guru as $value): ?>
                    <option value="<?= $value['id_guru']; ?>" <?= $kelas->id_wali_kelas == $value['id_guru'] ? 'selected' : ''; ?>>
                      <?= $value['nama_guru']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">
                  <?= invalidFeedback('id_wali_kelas'); ?>
                </div>
              </div>
          </div>
          <button type="submit" class="btn btn-primary mt-4">Simpan</button>
          </form>

          <hr>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?= $this->endSection() ?>