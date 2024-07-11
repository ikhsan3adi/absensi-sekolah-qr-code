<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-body">
                  <div class="row justify-content-between">
                     <div class="col">
                        <div class="pt-3 pl-3">
                           <h4><b>Daftar Kelas</b></h4>
                           <p>Silakan pilih kelas</p>
                        </div>
                     </div>
                  </div>

                  <div class="card-body pt-1 px-3">
                     <div class="row">
                        <?php foreach ($kelas as $value) : ?>
                           <?php
                           $idKelas = $value['id_kelas'];
                           $namaKelas =  $value['kelas'] . ' ' . $value['jurusan'];
                           ?>
                           <div class="col-md-3">
                              <button id="kelas-<?= $idKelas; ?>" onclick="getSiswa(<?= $idKelas; ?>, '<?= $namaKelas; ?>')" class="btn btn-primary w-100">
                                 <?= $namaKelas; ?>
                              </button>
                           </div>
                        <?php endforeach; ?>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-3">
                        <div class="pt-3 pl-3 pb-2">
                           <h4><b>Tanggal</b></h4>
                           <input class="form-control" type="date" name="tangal" id="tanggal" value="<?= date('Y-m-d'); ?>" onchange="onDateChange()">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card" id="dataSiswa">
         <div class="card-body">
            <div class="row justify-content-between">
               <div class="col-auto me-auto">
                  <div class="pt-3 pl-3">
                     <h4><b>Absen Siswa</b></h4>
                     <p>Daftar siswa muncul disini</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal ubah kehadiran -->
   <div class="modal fade" id="ubahModal" tabindex="-1" aria-labelledby="modalUbahKehadiran" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modalUbahKehadiran">Ubah kehadiran</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div id="modalFormUbahSiswa"></div>
         </div>
      </div>
   </div>
</div>
<script>
   var lastIdKelas;
   var lastKelas;

   function onDateChange() {
      if (lastIdKelas != null && lastKelas != null) getSiswa(lastIdKelas, lastKelas);
   }

   function getSiswa(idKelas, kelas) {
      var tanggal = $('#tanggal').val();

      updateBtn(idKelas);

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-siswa'); ?>",
         type: 'post',
         data: {
            'kelas': kelas,
            'id_kelas': idKelas,
            'tanggal': tanggal
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#dataSiswa').html(response);

            $('html, body').animate({
               scrollTop: $("#dataSiswa").offset().top
            }, 500);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#dataSiswa').html(thrown);
         }
      });

      lastIdKelas = idKelas;
      lastKelas = kelas;
   }

   function updateBtn(id_btn) {
      for (let index = 1; index <= <?= count($kelas); ?>; index++) {
         if (index != id_btn) {
            $('#kelas-' + index).removeClass('btn-success');
            $('#kelas-' + index).addClass('btn-primary');
         } else {
            $('#kelas-' + index).removeClass('btn-primary');
            $('#kelas-' + index).addClass('btn-success');
         }
      }
   }

   function getDataKehadiran(idPresensi, idSiswa) {
      jQuery.ajax({
         url: "<?= base_url('/admin/absen-siswa/kehadiran'); ?>",
         type: 'post',
         data: {
            'id_presensi': idPresensi,
            'id_siswa': idSiswa
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#modalFormUbahSiswa').html(response);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#modalFormUbahSiswa').html(thrown);
         }
      });
   }

   function ubahKehadiran() {
      var tanggal = $('#tanggal').val();

      var form = $('#formUbah').serializeArray();

      form.push({
         name: 'tanggal',
         value: tanggal
      });

      console.log(form);

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-siswa/edit'); ?>",
         type: 'post',
         data: form,
         success: function(response, status, xhr) {
            // console.log(status);

            if (response['status']) {
               getSiswa(lastIdKelas, lastKelas);
               alert('Berhasil ubah kehadiran : ' + response['nama_siswa']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['nama_siswa']);
            }
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal ubah kehadiran\n' + thrown);
         }
      });
   }
</script>
<?= $this->endSection() ?>