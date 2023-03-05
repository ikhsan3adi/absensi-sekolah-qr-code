<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="card">
         <div class="card-body">
            <div class="pt-3 pl-3 w-25 pb-2">
               <h4><b>Tanggal</b></h4>
               <input class="form-control" type="date" name="tangal" id="tanggal" value="<?= date('Y-m-d'); ?>" onchange="get_guru()">
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-body">
            <div class="row">
               <div class="col">
                  <div class="pt-3 pl-3">
                     <h4><b>Absen Guru</b></h4>
                     <p>Daftar guru muncul disini</p>
                  </div>
               </div>
            </div>
            <div id="dataGuru">

            </div>
         </div>
      </div>
   </div>
</div>
<script src="<?= base_url('public/assets/js/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
<script>
   get_guru();

   function get_guru() {
      var tanggal = $('#tanggal').val();

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-guru'); ?>",
         type: 'post',
         data: {
            'tanggal': tanggal
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#dataGuru').html(response);

            $('html, body').animate({
               scrollTop: $("#dataGuru").offset().top
            }, 500);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#dataGuru').html(thrown);
         }
      });
   }

   function ubahKehadiran(idKehadiran, idGuru, jam_masuk = '') {
      if (!confirm("Apakah yakin untuk mengubah kehadiran?")) {
         return;
      }

      var tanggal = $('#tanggal').val();

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-guru/edit'); ?>",
         type: 'post',
         data: {
            'id_kehadiran': idKehadiran,
            'id_guru': idGuru,
            'tanggal': tanggal,
            'jam_masuk': jam_masuk
         },
         success: function(response, status, xhr) {
            // console.log(status);

            if (response['status']) {
               alert('Berhasil ubah kehadiran : ' + response['nama_guru']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['nama_guru']);
            }

            get_siswa(lastIdKelas, lastKelas);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal ubah kehadiran\n' + thrown);
         }
      });
   }
</script>
<?= $this->endSection() ?>