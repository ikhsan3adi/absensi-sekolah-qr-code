<div class="card-body table-responsive">
   <?php if (!$empty) : ?>
      <table class="table table-hover">
         <thead class="text-primary">
            <th><b>No</b></th>
            <th><b>NIS</b></th>
            <th><b>Nama Siswa</b></th>
            <th><b>Jenis Kelamin</b></th>
            <th><b>Kelas</b></th>
            <th><b>Jurusan</b></th>
            <th><b>No HP</b></th>
            <th><b>Aksi</b></th>
         </thead>
         <tbody>
            <?php $i = 1;
            foreach ($data as $value) : ?>
               <tr>
                  <td><?= $i; ?></td>
                  <td><?= $value['nis']; ?></td>
                  <td><b><?= $value['nama_siswa']; ?></b></td>
                  <td><?= $value['jenis_kelamin']; ?></td>
                  <td><?= $value['kelas']; ?></td>
                  <td><?= $value['jurusan']; ?></td>
                  <td><?= $value['no_hp']; ?></td>
                  <td>
                     <a href="<?= base_url('admin/siswa/edit/' . $value['id_siswa']); ?>" type="button" class="btn btn-primary p-2" id="<?= $value['nis']; ?>">
                        <i class="material-icons">edit</i>
                        Edit
                     </a>
                     <form action="<?= base_url('admin/siswa/delete/' . $value['id_siswa']); ?>" method="post" class="d-inline">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button onclick="return confirm('Konfirmasi untuk menghapus data');" type="submit" class="btn btn-danger p-2" id="<?= $value['nis']; ?>">
                           <i class="material-icons">delete_forever</i>
                           Delete
                        </button>
                     </form>
                  </td>
               </tr>
            <?php $i++;
            endforeach; ?>
         </tbody>
      </table>
   <?php else : ?>
      <div class="row">
         <div class="col">
            <h4 class="text-center text-danger">Data tidak ditemukan</h4>
         </div>
      </div>
   <?php endif; ?>
</div>