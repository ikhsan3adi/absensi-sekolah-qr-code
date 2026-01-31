<div class="card-body table-responsive">
  <table class="table table-hover">
    <thead class="text-primary">
      <th><b>No</b></th>
      <th><b>Tingkat</b></th>
      <th><b>Jurusan</b></th>
      <th><b>Indeks</b></th>
      <th><b>Wali Kelas</b></th>
      <th><b>Aksi</b></th>
    </thead>
    <tbody>
      <?php $i = 1;
      foreach ($data as $value): ?>
        <tr>
          <td><?= $i; ?></td>
          <td><b><?= $value['tingkat']; ?></b></td>
          <td><?= $value['jurusan']; ?></td>
          <td><?= $value['index_kelas']; ?></td>
          <td><?= $value['nama_wali_kelas'] ?? '-'; ?></td>
          <td>
            <a href="<?= base_url('admin/kelas/edit/' . $value['id_kelas']); ?>" type="button" class="btn btn-primary p-2" id="<?= $value['id_kelas']; ?>">
              <i class="material-icons">edit</i>
            </a>
            <button onclick='deleteItem("admin/kelas/deleteKelasPost","<?= $value["id_kelas"]; ?>","Konfirmasi untuk menghapus data");' class="btn btn-danger p-2" id="<?= $value['id_kelas']; ?>">
              <i class="material-icons">delete_forever</i>
            </button>
          </td>
        </tr>
        <?php $i++;
      endforeach; ?>
    </tbody>
  </table>
</div>