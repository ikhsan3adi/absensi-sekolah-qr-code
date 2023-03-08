<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-header card-header-primary">
                  <h4 class="card-title">Form Edit Siswa</h4>
                  <!-- <p class="card-category">Angkatan 2022/2023</p> -->
               </div>
               <div class="card-body mx-5 my-3">

                  <form action="" method="post">
                     <?= csrf_field() ?>

                     <div class="form-group mt-4">
                        <label for="nis">NIS</label>
                        <input type="text" id="nis" class="form-control <?php if (session('errors.nis')) : ?>is-invalid<?php endif ?>" name="nis" placeholder="1234" value="<?= old('nis') ?>">
                        <div class="invalid-feedback">
                           <?= session('errors.nis') ?>
                        </div>
                     </div>

                     <div class="form-group mt-4">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" class="form-control <?php if (session('errors.nama')) : ?>is-invalid<?php endif ?>" name="nama" placeholder="Your Name" value="<?= old('nama') ?>">
                        <div class="invalid-feedback">
                           <?= session('errors.nama') ?>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <label for="kelas">Kelas</label>
                           <select class="form-control pt-0" id="kelas">
                              <option selected>--Pilih kelas--</option>
                              <?php foreach ($kelas as $value) : ?>
                                 <option value="<?= $value['id_kelas']; ?>"><?= $value['kelas'] . ' ' . $value['jurusan']; ?></option>
                              <?php endforeach; ?>
                           </select>
                           <div class="invalid-feedback">
                              <?= session('errors.kelas') ?>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <label for="jk">Jenis Kelamin</label>
                           <div class="form-check my-1" id="jk">
                              <div class="row">
                                 <div class="col-auto">
                                    <div class="row">
                                       <div class="col-auto pr-1">
                                          <input class="form-check" type="radio" name="jk" id="laki">
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
                                          <input class="form-check" type="radio" name="jk" id="perempuan">
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
                              <?= session('errors.jk') ?>
                           </div>
                        </div>
                     </div>

                     <div class="form-group mt-5">
                        <label for="hp">No HP</label>
                        <input type="number" id="hp" name="no_hp" class="form-control <?php if (session('errors.jk')) : ?>is-invalid<?php endif ?>">
                        <div class="invalid-feedback">
                           <?= session('errors.no_hp') ?>
                        </div>
                     </div>

                     <button type="submit" class="btn btn-success btn-block">Simpan</button>
                  </form>

                  <hr>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?= $this->endSection() ?>