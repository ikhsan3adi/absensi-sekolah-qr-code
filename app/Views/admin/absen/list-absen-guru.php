<div id="dataSiswa" class="card-body table-responsive pb-5">
   <?php if (!empty($data)) : ?>
      <table class="table table-hover">
         <thead class="text-success">
            <th><b>No.</b></th>
            <th><b>NUPTK</b></th>
            <th><b>Nama Guru</b></th>
            <th><b>Kehadiran</b></th>
            <th><b>Jam masuk</b></th>
            <th><b>Jam pulang</b></th>
            <th><b>Keterangan</b></th>
            <th><b>Aksi</b></th>
         </thead>
         <tbody>
            <?php $no = 1; ?>
            <?php foreach ($data as $value) : ?>
               <?php
               $idKehadiran = intval($value['id_kehadiran'] ?? ($lewat ? 5 : 4));
               $kehadiran = kehadiran($idKehadiran);
               ?>
               <tr>
                  <td><?= $no; ?></td>
                  <td><?= $value['nuptk']; ?></td>
                  <td><b><?= $value['nama_guru']; ?></b></td>
                  <td>
                     <p class="p-2 w-100 btn btn-<?= $kehadiran['color']; ?> text-center">
                        <b><?= $kehadiran['text']; ?></b>
                     </p>
                  </td>
                  <td><b><?= $value['jam_masuk'] ?? '-'; ?></b></td>
                  <td><b><?= $value['jam_keluar'] ?? '-'; ?></b></td>
                  <td><?= $value['keterangan'] ?? '-'; ?></td>
                  <td>
                     <?php if (!$lewat) : ?>
                        <div class="dropstart">
                           <button type="button" class="btn btn-info p-2 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="<?= $value['id_guru']; ?>">
                              <i class="material-icons">edit</i>
                              Edit
                           </button>
                           <div class="dropdown-menu" aria-labelledby="<?= $value['id_guru']; ?>">
                              <form id="formUbah<?= $value['id_guru']; ?>">
                                 <div class="row">
                                    <div class="col">
                                       <h5 class="dropdown-header">Ubah kehadiran</h5>
                                       <input type="hidden" name="id_guru" value="<?= $value['id_guru']; ?>">
                                       <?php foreach ($listKehadiran as $value2) : ?>
                                          <div class="px-3">
                                             <div class="form-control">
                                                <?php $color = kehadiran($value2['id_kehadiran'])['color'] ?>
                                                <?php if ($value2['id_kehadiran'] == $idKehadiran) : ?>
                                                   <input type="radio" name="id_kehadiran" id="<?= $value2['id_kehadiran']; ?>" value="<?= $value2['id_kehadiran']; ?>" checked>
                                                   <label class="form-check-label text-<?= $color; ?>" for="<?= $value2['id_kehadiran']; ?>">
                                                      <?= $value2['kehadiran']; ?>
                                                   </label>
                                                <?php elseif ($value2['id_kehadiran'] == 1) : ?>
                                                   <input type="radio" name="id_kehadiran" id="<?= $value2['id_kehadiran']; ?>" value="<?= $value2['id_kehadiran']; ?>">
                                                   <label class="form-check-label text-<?= $color; ?>" for="<?= $value2['id_kehadiran']; ?>">
                                                      <?= $value2['kehadiran']; ?>
                                                   </label>
                                                <?php else : ?>
                                                   <input type="radio" name="id_kehadiran" id="<?= $value2['id_kehadiran']; ?>" value="<?= $value2['id_kehadiran']; ?>">
                                                   <label class="form-check-label text-<?= $color; ?>" for="<?= $value2['id_kehadiran']; ?>">
                                                      <?= $value2['kehadiran']; ?>
                                                   </label>
                                                <?php endif; ?>
                                             </div>
                                          </div>
                                       <?php endforeach; ?>
                                    </div>
                                    <div class="col">
                                       <h5 class="dropdown-header pb-0 pl-0">Keterangan</h5>
                                       <div class="pr-3 py-3">
                                          <textarea name="keterangan"><?= trim($value['keterangan']); ?></textarea>
                                       </div>
                                    </div>
                                 </div>
                              </form>
                              <div class="p-3">
                                 <button id="tombolUbah<?= $value['id_guru']; ?>" class="btn btn-success w-100">Ubah</button>
                              </div>
                              <script>
                                 $('#tombolUbah' + <?= $value['id_guru']; ?>).click(function(e) {
                                    ubahKehadiran(<?= $value['id_guru']; ?>);
                                 });
                              </script>
                           </div>
                        </div>
                     <?php else : ?>
                        <button class="btn btn-disabled p-2">No Action</button>
                     <?php endif; ?>
                  </td>
               </tr>
            <?php $no++;
            endforeach ?>
         </tbody>
      </table>
   <?php
   else :
   ?>
      <div class="row">
         <div class="col">
            <h4 class="text-center text-danger">Data tidak ditemukan</h4>
         </div>
      </div>
   <?php
   endif; ?>
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