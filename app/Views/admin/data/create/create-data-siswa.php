<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-header card-header-primary">
                  <h4 class="card-title"><b>Form Tambah Siswa</b></h4>

               </div>
               <div class="card-body mx-5 my-3">

                  <form action="<?= base_url('admin/siswa/create'); ?>" method="post">
                     <?= csrf_field() ?>
                     <?php $validation = \Config\Services::validation(); ?>

                     <?php if (session()->getFlashdata('msg')) : ?>
                        <div class="pb-2">
                           <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success'  ?> ">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                 <i class="material-icons">close</i>
                              </button>
                              <?= session()->getFlashdata('msg') ?>
                           </div>
                        </div>
                     <?php endif; ?>

                     <div class="form-group mt-4">
                        <label for="nis">NIS</label>
                        <input type="text" id="nis" class="form-control <?= $validation->getError('nis') ? 'is-invalid' : ''; ?>" name="nis" placeholder="1234" value="<?= old('nis') ?? $oldInput['nis']  ?? '' ?>">
                        <div class="invalid-feedback">
                           <?= $validation->getError('nis'); ?>
                        </div>
                     </div>

                     <div class="form-group mt-4">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" class="form-control <?= $validation->getError('nama') ? 'is-invalid' : ''; ?>" name="nama" placeholder="Your Name" value="<?= old('nama') ?? $oldInput['nama']  ?? '' ?>" required>
                        <div class="invalid-feedback">
                           <?= $validation->getError('nama'); ?>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <label for="kelas">Kelas</label>
                           <select class="custom-select <?= $validation->getError('id_kelas') ? 'is-invalid' : ''; ?>" id="kelas" name="id_kelas">
                              <option value="">--Pilih kelas--</option>
                              <?php foreach ($kelas as $value) : ?>
                                 <option value="<?= $value['id_kelas']; ?>" <?= old('id_kelas') ?? $oldInput['id_kelas'] ?? '' == $value['id_kelas'] ? 'selected' : ''; ?>>
                                    <?= $value['kelas'] . ' ' . $value['jurusan']; ?>
                                 </option>
                              <?php endforeach; ?>
                           </select>
                           <div class="invalid-feedback">
                              <?= $validation->getError('id_kelas'); ?>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <label for="jk">Jenis Kelamin</label>
                           <?php
                           if (old('jk')) {
                              $l = (old('jk') ?? $oldInput['jk']) == '1' ? 'checked' : '';
                              $p = (old('jk') ?? $oldInput['jk']) == '2' ? 'checked' : '';
                           }
                           ?>
                           <div class="form-check form-control pt-0 mb-1 <?= $validation->getError('jk') ? 'is-invalid' : ''; ?>" id="jk">
                              <div class="row">
                                 <div class="col-auto">
                                    <div class="row">
                                       <div class="col-auto pr-1">
                                          <input class="form-check" type="radio" name="jk" id="laki" value="1" <?= $l ?? ''; ?>>
                                       </div>
                                       <div class="col">
                                          <label class="form-check-label pl-0 pt-1" for="laki">
                                             <h6 class="text-dark">Laki-laki</h6>
                                          </label>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col">
                                    <div class="row">
                                       <div class="col-auto pr-1">
                                          <input class="form-check" type="radio" name="jk" id="perempuan" value="2" <?= $p ?? ''; ?>>
                                       </div>
                                       <div class="col">
                                          <label class="form-check-label pl-0 pt-1" for="perempuan">
                                             <h6 class="text-dark">Perempuan</h6>
                                          </label>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="invalid-feedback">
                              <?= $validation->getError('jk'); ?>
                           </div>
                        </div>
                     </div>

                     <div class="form-group mt-5">
                        <label for="hp">No HP</label>
                        <input type="number" id="hp" name="no_hp" class="form-control <?= $validation->getError('no_hp') ? 'is-invalid' : ''; ?>" value="<?= old('no_hp') ?? $oldInput['no_hp'] ?? '' ?>">
                        <div class="invalid-feedback">
                           <?= $validation->getError('no_hp'); ?>
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