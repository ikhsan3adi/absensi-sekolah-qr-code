<div class="modal-body">
   <div class="container-fluid">
      <form id="formUbah">

         <input type="hidden" name="id_siswa" value="<?= $data['id_siswa'] ?? $data['id_guru'] ?? ''; ?>">
         <input type="hidden" name="id_kelas" value="<?= $data['id_kelas'] ?? ''; ?>">

         <label for="kehadiran">Ubah kehadiran</label>
         <div class="form-check" id="kehadiran">
            <?php foreach ($listKehadiran as $value2) : ?>
               <?php $kehadiran = kehadiran($value2['id_kehadiran']); ?>
               <div class="row">
                  <div class="col-auto pr-1 pt-1">
                     <input class="form-check" type="radio" name="id_kehadiran" id="k<?= $kehadiran['text']; ?>" value="<?= $value2['id_kehadiran']; ?>" <?= $value2['id_kehadiran'] == ($data['id_kehadiran'] ?? '4') ? 'checked' : ''; ?>>
                  </div>
                  <div class="col">
                     <label class="form-check-label pl-0" for="k<?= $kehadiran['text']; ?>">
                        <h6 class="text-<?= $kehadiran['color']; ?>"><?= $kehadiran['text']; ?></h6>
                     </label>
                  </div>
               </div>
            <?php endforeach; ?>
         </div>
         <div class="row mb-2">
            <div class="col">
               <label for="jamMasuk">Jam masuk</label>
               <input class="form-control" type="time" name="jam_masuk" id="jamMasuk" value="<?= $data['jam_masuk'] ?? ''; ?>">
            </div>
            <div class="col">
               <label for="jamKeluar">Jam keluar</label>
               <input class="form-control" type="time" name="jam_keluar" id="jamKeluar" value="<?= $data['jam_keluar'] ?? ''; ?>">
            </div>
         </div>
         <label for="keterangan">Keterangan</label>
         <textarea id="keterangan" name="keterangan" class="custom-select"><?= trim($data['keterangan'] ?? ''); ?></textarea>
      </form>
   </div>
</div>
<div class="modal-footer">
   <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
   <button type="button" onclick="ubahKehadiran()" class="btn btn-primary" data-dismiss="modal">Ubah</button>
</div>
<script>
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

<?php
function kehadiran($kehadiran): array
{
   $text = '';
   $color = '';
   switch ($kehadiran) {
      case 1:
         $color = 'success';
         $text = 'Hadir';
         break;
      case 2:
         $color = 'warning';
         $text = 'Sakit';
         break;
      case 3:
         $color = 'info';
         $text = 'Izin';
         break;
      case 4:
         $color = 'danger';
         $text = 'Tanpa keterangan';
         break;
      case 5:
      default:
         $color = 'disabled';
         $text = 'Belum tersedia';
         break;
   }

   return ['color' => $color, 'text' => $text];
}
?>