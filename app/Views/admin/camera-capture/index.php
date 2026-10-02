<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">

      <?php if (session()->getFlashdata('msg')): ?>
         <div class="pb-2 px-3">
            <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?>">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               <?= session()->getFlashdata('msg') ?>
            </div>
         </div>
      <?php endif; ?>

      <!-- ── Header Actions ── -->
      <div class="d-flex align-items-center flex-wrap gap-2 mb-2 px-3">
         <a class="btn btn-primary pl-3 py-3 mr-2" href="<?= base_url('admin/camera-capture/create') ?>">
            <i class="material-icons mr-2">photo_camera</i> Ambil Foto Baru
         </a>
         <!-- Filter by type -->
         <div class="btn-group" role="group">
            <a href="<?= base_url('admin/camera-capture') ?>"
               class="btn btn-<?= $activeType === null ? 'info' : 'default' ?>">
               Semua <span class="badge badge-light ml-1"><?= array_sum($counts) ?></span>
            </a>
            <a href="<?= base_url('admin/camera-capture?type=siswa') ?>"
               class="btn btn-<?= $activeType === 'siswa' ? 'info' : 'default' ?>">
               Siswa <span class="badge badge-light ml-1"><?= $counts['siswa'] ?></span>
            </a>
            <a href="<?= base_url('admin/camera-capture?type=guru') ?>"
               class="btn btn-<?= $activeType === 'guru' ? 'info' : 'default' ?>">
               Guru <span class="badge badge-light ml-1"><?= $counts['guru'] ?></span>
            </a>
            <a href="<?= base_url('admin/camera-capture?type=umum') ?>"
               class="btn btn-<?= $activeType === 'umum' ? 'info' : 'default' ?>">
               Umum <span class="badge badge-light ml-1"><?= $counts['umum'] ?></span>
            </a>
         </div>
      </div>

      <!-- ── Statistik Cards ── -->
      <div class="row px-3 mb-3">
         <div class="col-md-4">
            <div class="card card-stats">
               <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon"><i class="material-icons">people</i></div>
                  <p class="card-category">Foto Siswa</p>
                  <h3 class="card-title"><?= number_format($counts['siswa']) ?></h3>
               </div>
               <div class="card-footer"><div class="stats"><i class="material-icons">photo</i> total foto wajah siswa</div></div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="card card-stats">
               <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon"><i class="material-icons">person_4</i></div>
                  <p class="card-category">Foto Guru</p>
                  <h3 class="card-title"><?= number_format($counts['guru']) ?></h3>
               </div>
               <div class="card-footer"><div class="stats"><i class="material-icons">photo</i> total foto wajah guru</div></div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="card card-stats">
               <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon"><i class="material-icons">photo_camera</i></div>
                  <p class="card-category">Total Semua Foto</p>
                  <h3 class="card-title"><?= number_format(array_sum($counts)) ?></h3>
               </div>
               <div class="card-footer"><div class="stats"><i class="material-icons">update</i> data tersimpan</div></div>
            </div>
         </div>
      </div>

      <!-- ── Tabel Data ── -->
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header card-header-primary">
                  <h4 class="card-title"><b>Daftar Foto Wajah</b></h4>
                  <p class="card-category">
                     <?= $activeType ? 'Filter: ' . ucfirst($activeType) : 'Semua tipe' ?>
                  </p>
               </div>
               <div class="card-body">

                  <?php if (empty($captures)): ?>
                     <div class="text-center py-5">
                        <i class="material-icons" style="font-size:64px;color:#ccc;">photo_camera_off</i>
                        <p class="text-muted mt-2">Belum ada foto yang tersimpan.</p>
                        <a href="<?= base_url('admin/camera-capture/create') ?>" class="btn btn-primary">
                           <i class="material-icons mr-1">add_a_photo</i> Ambil Foto Pertama
                        </a>
                     </div>
                  <?php else: ?>
                     <div class="table-responsive">
                        <table id="tableCameraCapture" class="table table-hover table-striped">
                           <thead class="text-primary">
                              <tr>
                                 <th style="width:80px">#</th>
                                 <th style="width:90px">Foto</th>
                                 <th>Nama</th>
                                 <th>Tipe</th>
                                 <th>Keterangan</th>
                                 <th>Diambil Oleh</th>
                                 <th>Waktu</th>
                                 <th class="text-center" style="width:120px">Aksi</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($captures as $i => $cap): ?>
                                 <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                       <a href="<?= base_url('admin/camera-capture/' . $cap['id']) ?>">
                                          <img
                                             src="<?= base_url('admin/camera-capture/image/' . $cap['id']) ?>"
                                             alt="foto"
                                             class="img-thumbnail capture-thumb"
                                             style="width:64px;height:64px;object-fit:cover;cursor:pointer;"
                                             onerror="this.src='<?= base_url('assets/img/placeholder-face.png') ?>'"
                                          >
                                       </a>
                                    </td>
                                    <td>
                                       <?= esc($cap['entity_name'] ?? '—') ?>
                                    </td>
                                    <td>
                                       <?php
                                          $typeBadge = match($cap['entity_type']) {
                                             'siswa' => '<span class="badge badge-warning">Siswa</span>',
                                             'guru'  => '<span class="badge badge-success">Guru</span>',
                                             default => '<span class="badge badge-info">Umum</span>',
                                          };
                                          echo $typeBadge;
                                       ?>
                                    </td>
                                    <td><?= esc($cap['keterangan'] ?? '—') ?></td>
                                    <td><?= esc($cap['captured_by_username'] ?? '—') ?></td>
                                    <td>
                                       <small><?= !empty($cap['created_at']) ? date('d/m/Y H:i', strtotime($cap['created_at'])) : '—' ?></small>
                                    </td>
                                    <td class="text-center">
                                       <a href="<?= base_url('admin/camera-capture/' . $cap['id']) ?>"
                                          class="btn btn-info btn-sm" title="Lihat Detail">
                                          <i class="material-icons" style="font-size:16px;">visibility</i>
                                       </a>
                                       <button
                                          class="btn btn-danger btn-sm btn-delete-capture"
                                          data-id="<?= $cap['id'] ?>"
                                          data-name="<?= esc($cap['entity_name'] ?? 'Foto #' . $cap['id']) ?>"
                                          title="Hapus Foto">
                                          <i class="material-icons" style="font-size:16px;">delete</i>
                                       </button>
                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                           </tbody>
                        </table>
                     </div>
                  <?php endif; ?>

               </div>
            </div>
         </div>
      </div>

   </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
   // ── Init DataTable ──
   $(document).ready(function () {
      if ($('#tableCameraCapture').length) {
         $('#tableCameraCapture').DataTable({
            order: [[6, 'desc']],
            columnDefs: [
               { orderable: false, targets: [1, 7] }
            ]
         });
      }
   });

   // ── Delete Capture ──
   $(document).on('click', '.btn-delete-capture', function () {
      const id   = $(this).data('id');
      const name = $(this).data('name');
      const $row = $(this).closest('tr');

      Swal.fire({
         title: 'Hapus Foto?',
         html: `Foto <b>${name}</b> akan dihapus permanen termasuk file gambarnya.`,
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#f44336',
         cancelButtonColor: '#9e9e9e',
         confirmButtonText: 'Ya, Hapus!',
         cancelButtonText: 'Batal',
      }).then((result) => {
         if (!result.isConfirmed) return;

         $.ajax({
            url: BaseConfig.baseURL + 'admin/camera-capture/delete/' + id,
            type: 'DELETE',
            data: {
               [BaseConfig.csrfTokenName]: Cookies.get(BaseConfig.csrfTokenName)
            },
            success: function (res) {
               if (res.success) {
                  Swal.fire('Terhapus!', res.message, 'success').then(() => {
                     location.reload();
                  });
               } else {
                  Swal.fire('Gagal!', res.message, 'error');
               }
            },
            error: function () {
               Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
            }
         });
      });
   });
</script>
<?= $this->endSection() ?>
