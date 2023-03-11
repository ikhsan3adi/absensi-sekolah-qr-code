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
                                          <div class="progress" style="height: 5px; border-radius: 0px; background-color: rgb(186, 124, 222);">
                                             <div id="progressBarSiswa" class="progress-bar bg-white" style="width: 0%; height: 10px;" role="progressbar" aria-valuenow="20" aria-valuemin="" aria-valuemax=""></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </button>
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
                                             <div class="progress" style="height: 5px; border-radius: 0px; background-color: rgb(58, 192, 85);">
                                                <div id="progressBarGuru" class="progress-bar bg-white" style="width: 0%; height: 10px;" role="progressbar" aria-valuenow="20" aria-valuemin="" aria-valuemax=""></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </button>
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
                  i++;
               } else {
                  $('#progressTextSiswa').html('Progres: ' + i + '/' + dataSiswa.length + ' selesai');
                  $('#progressSelesaiSiswa').removeClass('d-none');
               }

               $('#progressBarSiswa')
                  .attr('aria-valuenow', i)
                  .attr('style', 'width: ' + (i / dataSiswa.length) * 100 + '%;');
            }
         });
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
                  i++;
               } else {
                  $('#progressTextGuru').html('Progres: ' + i + '/' + dataGuru.length + ' selesai');
                  $('#progressSelesaiGuru').removeClass('d-none');
               }

               $('#progressBarGuru')
                  .attr('aria-valuenow', i)
                  .attr('style', 'width: ' + (i / dataGuru.length) * 100 + '%;');
            }
         });
      });
   }
</script>
<?= $this->endSection() ?>