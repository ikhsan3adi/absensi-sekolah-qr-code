<div class="card-body table-responsive">
   <?php if (!$empty): ?>
      <table class="table table-hover">
         <thead class="text-info">
            <th><b>No</b></th>
            <th><b>Username</b></th>
            <th><b>Email</b></th>
            <th><b>Role</b></th>
            <th><b>Guru</b></th>
            <th><b>Status</b></th>
            <th><b>Aksi</b></th>
         </thead>
         <tbody>
            <?php $i = 1;
            foreach ($data as $value): ?>
               <tr>
                  <td><?= $i; ?></td>
                  <td><?= $value['username']; ?></td>
                  <td><b><?= $value['email']; ?></b></td>
                  <td>
                     <?= $value['is_superadmin'] == '1' ? 'Super Admin' : ($value['is_superadmin'] == '2' ? 'Kepsep' : ($value['is_superadmin'] == '3' ? 'Staf Petugas' : 'Scanner')); ?>
                  </td>
                  <td><?= $value['nama_guru'] ?? '-'; ?></td>
                  <td>
                     <?php if (($value['active'] ?? 0) == 1): ?>
                        <span class="badge badge-success">Aktif</span>
                     <?php else: ?>
                        <span class="badge badge-danger">Non-aktif</span>
                     <?php endif; ?>
                  </td>
                  <td>
                     <?php if ($value['username'] == 'superadmin'): ?>
                        <button disabled class="btn btn-disabled p-2">
                           <i class="material-icons">edit</i>
                        </button>
                        <button disabled class="btn btn-disabled p-2">
                           <i class="material-icons">delete_forever</i>
                        </button>
                     <?php else: ?>
                        <a href="<?= base_url('admin/petugas/activate/' . $value['id']); ?>"
                           title="<?= ($value['active'] ?? 0) == 1 ? 'Non-aktifkan' : 'Aktifkan'; ?>"
                           class="btn <?= ($value['active'] ?? 0) == 1 ? 'btn-warning' : 'btn-success'; ?> p-2">
                           <i class="material-icons"><?= ($value['active'] ?? 0) == 1 ? 'block' : 'check_circle'; ?></i>
                        </a>
                        <a href="<?= base_url('admin/petugas/edit/' . $value['id']); ?>" type="button" class="btn btn-info p-2"
                           id="<?= $value['username']; ?>">
                           <i class="material-icons">edit</i>
                        </a>
                        <form action="<?= base_url('admin/petugas/delete/' . $value['id']); ?>" method="post" class="d-inline">
                           <?= csrf_field(); ?>
                           <input type="hidden" name="_method" value="DELETE">
                           <button onclick="return confirm('Konfirmasi untuk menghapus data');" type="submit"
                              class="btn btn-danger p-2" id="<?= $value['username']; ?>">
                              <i class="material-icons">delete_forever</i>
                              Delete
                           </button>
                        </form>
                     <?php endif; ?>
                  </td>
               </tr>
               <?php $i++;
            endforeach; ?>
         </tbody>
      </table>
   <?php else: ?>
      <div class="row">
         <div class="col">
            <h4 class="text-center text-danger">Data tidak ditemukan</h4>
         </div>
      </div>
   <?php endif; ?>
</div>