<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-header card-header-danger">
                  <h4 class="card-title"><b>Generate QR Code</b></h4>
                  <p class="card-category">Generate QR berdasarkan kode unik data siswa/guru</p>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-body">
                              <h4 class="text-primary"><b>Data Siswa</b></h4>
                              <p>Total jumlah siswa : <b><?= count($siswa); ?></b>
                                 <br>
                                 <a href="<?= base_url('admin/siswa'); ?>">Lihat data</a>
                              </p>
                              <a href="#" class="btn btn-primary pl-3 py-4">
                                 <i class="material-icons mr-2 pb-2" style="font-size: 64px;">qr_code</i>
                                 <h3 class="d-inline">Generate All</h3>
                              </a>
                              <hr>
                              <br>
                              <h4 class="text-primary"><b>Generate per kelas</b></h4>
                              <select name="id_kelas" id="kelas" class="custom-select mb-3">
                                 <option value="">--Pilih kelas--</option>
                                 <?php foreach ($kelas as $value) : ?>
                                    <option value="<?= $value['id_kelas']; ?>">
                                       <?= $value['kelas'] . ' ' . $value['jurusan']; ?>
                                    </option>
                                 <?php endforeach; ?>
                              </select>
                              <a href="#" class="btn btn-primary pl-3">
                                 <i class="material-icons mr-2 pb-2" style="font-size: 32px;">qr_code</i>
                                 <h4 class="d-inline">Generate</h4>
                              </a>
                              <br>
                              <br>
                              <p>Untuk generate qr code per masing-masing siswa kunjungi <a href="<?= base_url('admin/siswa'); ?>">data siswa</a></p>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-body">
                              <h4 class="text-success"><b>Data Guru</b></h4>
                              <p>Total jumlah guru : <b><?= count($guru); ?></b>
                                 <br>
                                 <a href="<?= base_url('admin/guru'); ?>">Lihat data</a>
                              </p>
                              <a href="#" class="btn btn-success pl-3 py-4">
                                 <i class="material-icons mr-2 pb-2" style="font-size: 64px;">qr_code</i>
                                 <h3 class="d-inline">Generate All</h3>
                              </a>
                              <br>
                              <br>
                              <p>Untuk generate qr code per masing-masing guru kunjungi <a href="<?= base_url('admin/guru'); ?>">data guru</a></p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>

</script>
<?= $this->endSection() ?>