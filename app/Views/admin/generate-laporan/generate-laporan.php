<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <?php if (session()->getFlashdata('msg')) : ?>
               <div class="pb-2 px-3">
                  <div class="alert alert-success">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                     </button>
                     <?= session()->getFlashdata('msg') ?>
                  </div>
               </div>
            <?php endif; ?>
            <div class="card">
               <div class="card-header card-header-tabs card-header-info">
                  <div class="nav-tabs-navigation">
                     <div class="row">
                        <div class="col">
                           <h4 class="card-title"><b>Generate Laporan</b></h4>
                           <p class="card-category">Laporan absen</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="card h-100">
                           <div class="card-body">
                              <h4 class="text-primary"><b>Laporan Absen Siswa</b></h4>
                              <div class="row align-items-center">
                                 <div class="col-auto">
                                    <p class="d-inline"><b>Bulan :</b></p>
                                 </div>
                                 <div class="col-5">
                                    <input type="month" name="tanggalSiswa" id="tanggalSiswa" class="form-control" value="<?= date('Y-m'); ?>">
                                 </div>
                              </div>
                              <select name="kelas" class="custom-select mt-3">
                                 <option value="">--Pilih kelas--</option>
                                 <?php foreach ($kelas as $value) : ?>
                                    <?php
                                    $idKelas = $value['id_kelas'] - 1;
                                    $jumlahSiswa = count($siswaPerKelas[$idKelas]);
                                    ?>
                                    <option value="<?= $idKelas; ?>">
                                       <?= "{$value['kelas']} {$value['jurusan']} - {$jumlahSiswa} siswa"; ?>
                                    </option>
                                 <?php endforeach; ?>
                              </select>
                              <button onclick="generateLaporanSiswa()" class="btn btn-primary pl-3 mt-3">
                                 <div class="row align-items-center">
                                    <div class="col">
                                       <i class="material-icons" style="font-size: 32px;">print</i>
                                    </div>
                                    <div class="col">
                                       <div class="text-start">
                                          <h4 class="d-inline"><b>Generate laporan</b></h4>
                                       </div>
                                    </div>
                                 </div>
                              </button>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="card h-100">
                           <div class="card-body">
                              <h4 class="text-success"><b>Laporan Absen Guru</b></h4>
                              <p>Total jumlah guru : <b><?= count($guru); ?></b></p>
                              <div class="row align-items-center">
                                 <div class="col-auto">
                                    <p class="d-inline"><b>Bulan :</b></p>
                                 </div>
                                 <div class="col-5">
                                    <input type="month" name="tanggalGuru" id="tanggalGuru" class="form-control" value="<?= date('Y-m'); ?>">
                                 </div>
                              </div>
                              <button onclick="generateLaporanGuru()" class="btn btn-success pl-3 mt-3">
                                 <div class="row align-items-center">
                                    <div class="col">
                                       <i class="material-icons" style="font-size: 32px;">print</i>
                                    </div>
                                    <div class="col">
                                       <div class="text-start">
                                          <h4 class="d-inline"><b>Generate laporan</b></h4>
                                       </div>
                                    </div>
                                 </div>
                              </button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <br><br>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   var kelas = null;
   var jurusan = null;

   getDataPetugas();

   function trig() {
      getDataPetugas();
   }

   function getDataPetugas() {
      jQuery.ajax({
         url: "<?= base_url('/admin/petugas'); ?>",
         type: 'post',
         data: {},
         success: function(response, status, xhr) {
            // console.log(status);
            $('#dataPetugas').html(response);

            $('html, body').animate({
               scrollTop: $("#dataPetugas").offset().top
            }, 500);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#dataPetugas').html(thrown);
         }
      });
   }

   function removeHover() {
      setTimeout(() => {
         $('#tabBtn').removeClass('active show');
      }, 250);
   }
</script>
<?= $this->endSection() ?>