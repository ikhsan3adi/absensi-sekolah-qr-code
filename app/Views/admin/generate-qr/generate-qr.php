<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<style>
   .progress-siswa {
      height: 5px;
      border-radius: 0px;
      background-color: rgb(186, 124, 222);
   }

   .progress-guru {
      height: 5px;
      border-radius: 0px;
      background-color: rgb(58, 192, 85);
   }

   .my-progress-bar {
      height: 5px;
      border-radius: 0px;
   }
</style>
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
                              <button onclick="generateAllQrSiswa()" class="btn btn-primary pl-3 py-4">
                                 <div class="row align-items-center">
                                    <div class="col">
                                       <i class="material-icons" style="font-size: 64px;">qr_code</i>
                                    </div>
                                    <div class="col">
                                       <h3 class="d-inline">Generate All</h3>
                                       <div id="progressSiswa" class="d-none">
                                          <span id="progressTextSiswa"></span>
                                          <i id="progressSelesaiSiswa" class="material-icons d-none" class="d-none">check</i>
                                          <div class="progress progress-siswa">
                                             <div id="progressBarSiswa" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </button>
                              <hr>
                              <br>
                              <h4 class="text-primary"><b>Generate per kelas</b></h4>
                              <select name="id_kelas" id="kelasSelect" class="custom-select mb-3">
                                 <option value="">--Pilih kelas--</option>
                                 <?php foreach ($kelas as $value) : ?>
                                    <option id="idKelas<?= $value['id_kelas']; ?>" value="<?= $value['id_kelas']; ?>">
                                       <?= $value['kelas'] . ' ' . $value['jurusan']; ?>
                                    </option>
                                 <?php endforeach; ?>
                              </select>
                              <button onclick="generateQrSiswaByKelas()" class="btn btn-primary pl-3">
                                 <div class="row align-items-center">
                                    <div class="col">
                                       <i class="material-icons" style="font-size: 32px;">qr_code</i>
                                    </div>
                                    <div class="col">
                                       <div class="text-start">
                                          <h4 class="d-inline">Generate per kelas</h4>
                                       </div>
                                       <div id="progressKelas" class="d-none">
                                          <span id="progressTextKelas"></span>
                                          <i id="progressSelesaiKelas" class="material-icons d-none" class="d-none">check</i>
                                          <div class="progress progress-siswa d-none" id="progressBarBgKelas">
                                             <div id="progressBarKelas" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </button>
                              <br>
                              <div class="text-danger mt-2" id="textErrorKelas"><b></b></div>
                              <!-- <br>
                              <p>Untuk generate qr code per masing-masing siswa kunjungi <a href="<?= base_url('admin/siswa'); ?>">data siswa</a></p> -->
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
                              <button onclick="generateAllQrGuru()" class="btn btn-success pl-3 py-4">
                                 <div class="row align-items-center">
                                    <div class="col">
                                       <i class="material-icons" style="font-size: 64px;">qr_code</i>
                                    </div>
                                    <div class="col">
                                       <h3 class="d-inline">Generate All</h3>
                                       <div>
                                          <div id="progressGuru" class="d-none">
                                             <span id="progressTextGuru"></span>
                                             <i id="progressSelesaiGuru" class="material-icons d-none" class="d-none">check</i>
                                             <div class="progress progress-guru">
                                                <div id="progressBarGuru" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </button>
                              <br>
                              <br>
                              <!-- <p>Untuk generate qr code per masing-masing guru kunjungi <a href="<?= base_url('admin/guru'); ?>">data guru</a></p> -->
                           </div>
                        </div>
                        <p class="text-danger"><i class="material-icons" style="font-size: 16px;">warning</i> File image QR Code tersimpan di [folder website]/public/uploads/</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   const dataGuru = [
      <?php foreach ($guru as $value) {
         echo "{
                  'nama' : '$value[nama_guru]',
                  'unique_code' : '$value[unique_code]',
                  'nomor' :'$value[nuptk]'
               },";
      }; ?>
   ];

   const dataSiswa = [
      <?php foreach ($siswa as $value) {
         echo "{
                  'nama' : '$value[nama_siswa]',
                  'unique_code' : '$value[unique_code]',
                  'kelas' : '$value[kelas] $value[jurusan]',
                  'nomor' :'$value[nis]'
               },";
      }; ?>
   ];

   var dataSiswaPerKelas = [];

   function generateAllQrSiswa() {
      var i = 1;
      $('#progressSiswa').removeClass('d-none');
      $('#progressBarSiswa')
         .attr('aria-valuenow', '0')
         .attr('aria-valuemin', '0')
         .attr('aria-valuemax', dataSiswa.length)
         .attr('style', 'width: 0%;');

      dataSiswa.forEach(element => {
         jQuery.ajax({
            url: "<?= base_url('admin/generate/siswa'); ?>",
            type: 'post',
            data: {
               nama: element['nama'],
               unique_code: element['unique_code'],
               kelas: element['kelas'],
               nomor: element['nomor']
            },
            success: function(response) {
               if (i != dataSiswa.length) {
                  $('#progressTextSiswa').html('Progres: ' + i + '/' + dataSiswa.length);
               } else {
                  $('#progressTextSiswa').html('Progres: ' + i + '/' + dataSiswa.length + ' selesai');
                  $('#progressSelesaiSiswa').removeClass('d-none');
               }

               $('#progressBarSiswa')
                  .attr('aria-valuenow', i)
                  .attr('style', 'width: ' + (i / dataSiswa.length) * 100 + '%;');
               i++;
            }
         });
      });
   }

   function generateQrSiswaByKelas() {
      var i = 1;

      idKelas = $('#kelasSelect').val();

      if (idKelas == '') {
         $('#progressKelas').addClass('d-none');
         $('#textErrorKelas').html('Pilih kelas terlebih dahulu');
         return;
      }

      kelas = $('#idKelas' + idKelas).html();

      jQuery.ajax({
         url: "<?= base_url('admin/generate/siswa-by-kelas'); ?>",
         type: 'post',
         data: {
            idKelas: idKelas
         },
         success: function(response) {
            dataSiswaPerKelas = response;

            if (dataSiswaPerKelas.length < 1) {
               $('#progressKelas').addClass('d-none');
               $('#textErrorKelas').html('Data siswa kelas ' + kelas + ' tidak ditemukan');
               return;
            }

            $('#textErrorKelas').html('')

            $('#progressKelas').removeClass('d-none');
            $('#progressBarBgKelas')
               .removeClass('d-none');
            $('#progressBarKelas')
               .removeClass('d-none')
               .attr('aria-valuenow', '0')
               .attr('aria-valuemin', '0')
               .attr('aria-valuemax', dataSiswaPerKelas.length)
               .attr('style', 'width: 0%;');

            dataSiswaPerKelas.forEach(element => {
               jQuery.ajax({
                  url: "<?= base_url('admin/generate/siswa'); ?>",
                  type: 'post',
                  data: {
                     nama: element['nama_siswa'],
                     unique_code: element['unique_code'],
                     kelas: element['kelas'] + ' ' + element['jurusan'],
                     nomor: element['nis']
                  },
                  success: function(response) {
                     if (i != dataSiswaPerKelas.length) {
                        $('#progressTextKelas').html('Progres: ' + i + '/' + dataSiswaPerKelas.length);
                     } else {
                        $('#progressTextKelas').html('Progres: ' + i + '/' + dataSiswaPerKelas.length + ' selesai');
                        $('#progressSelesaiKelas').removeClass('d-none');
                     }

                     $('#progressBarKelas')
                        .attr('aria-valuenow', i)
                        .attr('style', 'width: ' + (i / dataSiswaPerKelas.length) * 100 + '%;');
                     i++;
                  },
                  error: function(xhr, status, thrown) {
                     console.log(xhr + status + thrown);
                  }
               });
            });
         }
      });
   }

   function generateAllQrGuru() {
      var i = 1;
      $('#progressGuru').removeClass('d-none');
      $('#progressBarGuru')
         .attr('aria-valuenow', '0')
         .attr('aria-valuemin', '0')
         .attr('aria-valuemax', dataGuru.length)
         .attr('style', 'width: 0%;');

      dataGuru.forEach(element => {
         jQuery.ajax({
            url: "<?= base_url('admin/generate/guru'); ?>",
            type: 'post',
            data: {
               nama: element['nama'],
               unique_code: element['unique_code'],
               nomor: element['nomor']
            },
            success: function(response) {
               if (i != dataGuru.length) {
                  $('#progressTextGuru').html('Progres: ' + i + '/' + dataGuru.length);
               } else {
                  $('#progressTextGuru').html('Progres: ' + i + '/' + dataGuru.length + ' selesai');
                  $('#progressSelesaiGuru').removeClass('d-none');
               }

               $('#progressBarGuru')
                  .attr('aria-valuenow', i)
                  .attr('style', 'width: ' + (i / dataGuru.length) * 100 + '%;');
               i++;
            }
         });
      });
   }
</script>
<?= $this->endSection() ?>