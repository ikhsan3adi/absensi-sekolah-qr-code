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

            <form action="<?= base_url('admin/kelas/' . $data['id_kelas']); ?>" method="post">
              <input type="hidden" name="_method" value="PATCH">
              <?= csrf_field() ?>
              <?php $validation = \Config\Services::validation(); ?>

              <?php if (session()->getFlashdata('msg')) : ?>
                <div class="pb-2">
                  <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <i class="material-icons">close</i>
                    </button>
                    <?= session()->getFlashdata('msg') ?>
                  </div>
                </div>
              <?php endif; ?>

              <div class="form-group mt-4">
                <label for="kelas">Kelas / Tingkat</label>
                <input type="text" id="kelas" class="form-control <?= $validation->getError('kelas') ? 'is-invalid' : ''; ?>" name="kelas" placeholder="'X', 'XI', '11'" value="<?= old('kelas') ?? $oldInput['kelas'] ?? $data['kelas'] ?>">
                <div class="invalid-feedback">
                  <?= $validation->getError('kelas'); ?>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <label for="idJurusan">Jurusan</label>
                  <select class="custom-select <?= $validation->getError('idJurusan') ? 'is-invalid' : ''; ?>" id="idJurusan" name="idJurusan">
                    <option value="">--Pilih idJurusan--</option>
                    <?php foreach ($jurusan as $value) : ?>
                      <option value="<?= $value['id']; ?>" <?= old('idJurusan') ?? $oldInput['idJurusan'] ?? $value['id'] == $data['id_jurusan'] ? 'selected' : ''; ?>>
                        <?= $value['jurusan']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">
                    <?= $validation->getError('idJurusan'); ?>
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