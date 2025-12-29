<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="card">
         <div class="card-body">
            <div class="row">
               <div class="col-md-3">
                  <div class="pt-3 pl-3 pb-2">
                     <h4><b>Tanggal</b></h4>
                     <input class="form-control" type="date" name="tanggal" id="tanggal"
                        value="<?= $date; ?>" onchange="getSiswa(<?= $kelas['id_kelas']; ?>, '<?= $kelas['kelas']; ?>')">
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card" id="dataSiswa">
         <div class="card-body">
             <div class="text-center p-5">
                 <div class="spinner-border text-primary" role="status">
                     <span class="sr-only">Loading...</span>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    var lastIdKelas = <?= $kelas['id_kelas']; ?>;
    var lastKelas = '<?= $kelas['kelas']; ?>';

   $(document).ready(function() {
       getSiswa(lastIdKelas, lastKelas);
   });

   function getSiswa(idKelas, kelas) {
      var tanggal = $('#tanggal').val();

      jQuery.ajax({
         url: "<?= base_url('/teacher/attendance/get-list'); ?>",
         type: 'post',
         data: {
            'kelas': kelas,
            'id_kelas': idKelas,
            'tanggal': tanggal
         },
         success: function (response, status, xhr) {
            $('#dataSiswa').html(response);
         },
         error: function (xhr, status, thrown) {
            console.log(thrown);
            $('#dataSiswa').html(thrown);
         }
      });
      lastIdKelas = idKelas;
      lastKelas = kelas;
   }

   function getDataKehadiran(idPresensi, idSiswa) {
      jQuery.ajax({
         url: "<?= base_url('/teacher/attendance/get-edit-modal'); ?>",
         type: 'post',
         data: {
            'id_presensi': idPresensi,
            'id_siswa': idSiswa
         },
         success: function (response, status, xhr) {
            $('#modalFormUbahSiswa').html(response);
         },
         error: function (xhr, status, thrown) {
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

      jQuery.ajax({
         url: "<?= base_url('/teacher/attendance/update-single'); ?>",
         type: 'post',
         data: form,
         success: function (response, status, xhr) {
            if (response['status']) {
               getSiswa(lastIdKelas, lastKelas);
               alert('Berhasil ubah kehadiran : ' + response['nama_siswa']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['nama_siswa']);
            }
         },
         error: function (xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal ubah kehadiran\n' + thrown);
         }
      });
   }
</script>
<?= $this->endSection() ?>
