<div class="card-body table-responsive">
   <?php if (!$empty) : ?>
      <table class="table table-hover">
         <thead class="text-primary">
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Jenis Kelamin</th>
            <th>Kelas</th>
            <th>Jurusan</th>
            <th>No HP</th>
            <th>Aksi</th>
         </thead>
         <tbody>
            <?php $i = 1;
            foreach ($data as $value) : ?>
               <tr>
                  <td><?= $i; ?></td>
                  <td><?= $value['nis']; ?></td>
                  <td><?= $value['nama_siswa']; ?></td>
                  <td><?= $value['jenis_kelamin']; ?></td>
                  <td><?= $value['kelas']; ?></td>
                  <td><?= $value['jurusan']; ?></td>
                  <td><?= $value['no_hp']; ?></td>
                  <td>
                     <a href="<?= base_url('admin/data-siswa/edit/' . $value['id_siswa']); ?>" type="button" class="btn btn-primary p-2" id="<?= $value['nis']; ?>">
                        <i class="material-icons">edit</i>
                        Edit
                     </a>
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