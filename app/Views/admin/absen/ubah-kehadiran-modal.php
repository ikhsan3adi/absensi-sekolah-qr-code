<div class="modal-body">
   <div class="container-fluid">
      <form id="formUbah">

         <input type="hidden" name="id_siswa" value="<?= $data['id_siswa'] ?? ''; ?>">
         <input type="hidden" name="id_guru" value="<?= $data['id_guru'] ?? ''; ?>">
         <input type="hidden" name="id_kelas" value="<?= $data['id_kelas'] ?? ''; ?>">

         <label for="kehadiran">Kehadiran</label>
         <div class="form-check" id="kehadiran">
            <?php foreach ($listKehadiran as $value2) : ?>
               <?php $kehadiran = kehadiran($value2['id_kehadiran']); ?>
               <div class="row">
                  <div class="col-auto pr-1 pt-1">
                     <input class="form-check" type="radio" name="id_kehadiran" id="k<?= $kehadiran['text']; ?>" value="<?= $value2['id_kehadiran']; ?>" <?= $value2['id_kehadiran'] == ($presensi['id_kehadiran'] ?? '4') ? 'checked' : ''; ?>>
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
               <input class="form-control" type="time" name="jam_masuk" id="jamMasuk" value="<?= $presensi['jam_masuk'] ?? ''; ?>">
            </div>
            <div class="col">
               <label for="jamKeluar">Jam keluar</label>
               <input class="form-control" type="time" name="jam_keluar" id="jamKeluar" value="<?= $presensi['jam_keluar'] ?? ''; ?>">
            </div>
         </div>
         <label for="keterangan">Keterangan</label>
         <textarea id="keterangan" name="keterangan" class="custom-select"><?= trim($presensi['keterangan'] ?? ''); ?></textarea>
      </form>
   </div>
</div>
<div class="modal-footer">
   <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
   <button type="button" onclick="ubahKehadiran()" class="btn btn-primary" data-dismiss="modal">Ubah</button>
</div>

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