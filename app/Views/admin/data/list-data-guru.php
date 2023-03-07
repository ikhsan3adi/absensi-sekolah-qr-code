<div class="card-body table-responsive">
   <?php if (!$empty) : ?>
      <table class="table table-hover">
         <thead class="text-success">
            <th>No</th>
            <th>NUPTK</th>
            <th>Nama Guru</th>
            <th>Jenis Kelamin</th>
            <th>No HP</th>
            <th>Alamat</th>
         </thead>
         <tbody>
            <?php $i = 1;
            foreach ($data as $value) : ?>
               <tr>
                  <td><?= $i; ?></td>
                  <td><?= $value['nuptk']; ?></td>
                  <td><?= $value['nama_guru']; ?></td>
                  <td><?= $value['jenis_kelamin']; ?></td>
                  <td><?= $value['no_hp']; ?></td>
                  <td><?= $value['alamat']; ?></td>
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