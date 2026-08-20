<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">

      <div class="row justify-content-center">
         <div class="col-lg-8">
            <div class="card">
               <div class="card-header card-header-primary">
                  <h4 class="card-title"><b>Detail Foto Wajah #<?= $capture['id'] ?></b></h4>
                  <p class="card-category">
                     <?= date('l, d F Y H:i', strtotime($capture['created_at'])) ?>
                  </p>
               </div>
               <div class="card-body">

                  <div class="row">
                     <!-- Foto -->
                     <div class="col-md-5 text-center mb-4">
                        <img
                           id="fotoPreview"
                           src="<?= base_url('admin/camera-capture/image/' . $capture['id']) ?>"
                           alt="Foto wajah"
                           class="img-fluid rounded shadow"
                           style="max-height:360px;object-fit:contain;border:3px solid #9c27b0;"
                           onerror="this.src='<?= base_url('assets/img/placeholder-face.png') ?>'"
                        >
                        <div class="mt-2">
                           <a href="<?= base_url('admin/camera-capture/image/' . $capture['id']) ?>"
                              download="foto_wajah_<?= $capture['id'] ?>.jpg"
                              class="btn btn-sm btn-info">
                              <i class="material-icons" style="font-size:16px;vertical-align:middle;">download</i> Download
                           </a>
                        </div>
                     </div>

                     <!-- Metadata -->
                     <div class="col-md-7">
                        <table class="table table-sm">
                           <tbody>
                              <tr>
                                 <th style="width:40%">ID</th>
                                 <td>#<?= $capture['id'] ?></td>
                              </tr>
                              <tr>
                                 <th>Tipe Subjek</th>
                                 <td>
                                    <?php
                                       echo match($capture['entity_type']) {
                                          'siswa' => '<span class="badge badge-warning">Siswa</span>',
                                          'guru'  => '<span class="badge badge-success">Guru</span>',
                                          default => '<span class="badge badge-info">Umum</span>',
                                       };
                                    ?>
                                 </td>
                              </tr>
                              <tr>
                                 <th>Nama</th>
                                 <td><?= esc($capture['entity_name'] ?? '—') ?></td>
                              </tr>
                              <?php if (!empty($capture['entity_id'])): ?>
                              <tr>
                                 <th>ID Entitas</th>
                                 <td><?= $capture['entity_id'] ?></td>
                              </tr>
                              <?php endif; ?>
                              <tr>
                                 <th>Keterangan</th>
                                 <td><?= esc($capture['keterangan'] ?? '—') ?></td>
                              </tr>
                              <tr>
                                 <th>Diambil Oleh</th>
                                 <td><?= esc($capture['captured_by_username'] ?? '—') ?></td>
                              </tr>
                              <tr>
                                 <th>File</th>
                                 <td><code style="font-size:11px;"><?= esc($capture['filename']) ?></code></td>
                              </tr>
                              <tr>
                                 <th>Waktu Capture</th>
                                 <td><?= !empty($capture['created_at']) ? date('d/m/Y H:i:s', strtotime($capture['created_at'])) : '—' ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>

                  <div class="d-flex mt-3" style="gap:8px;">
                     <a href="<?= base_url('admin/camera-capture') ?>" class="btn btn-default">
                        <i class="material-icons mr-1">arrow_back</i> Kembali
                     </a>
                     <a href="<?= base_url('admin/camera-capture/create') ?>" class="btn btn-primary">
                        <i class="material-icons mr-1">add_a_photo</i> Ambil Foto Baru
                     </a>
                     <button
                        class="btn btn-danger ml-auto btn-delete-capture"
                        data-id="<?= $capture['id'] ?>"
                        data-name="<?= esc($capture['entity_name'] ?? 'Foto #' . $capture['id']) ?>">
                        <i class="material-icons mr-1">delete</i> Hapus Foto Ini
                     </button>
                  </div>

               </div>
            </div>
         </div>
      </div>

   </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
   $(document).on('click', '.btn-delete-capture', function () {
      const id   = $(this).data('id');
      const name = $(this).data('name');

      Swal.fire({
         title: 'Hapus Foto?',
         html: `Foto <b>${name}</b> akan dihapus permanen.`,
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
            data: { [BaseConfig.csrfTokenName]: Cookies.get(BaseConfig.csrfTokenName) },
            success: function (res) {
               if (res.success) {
                  Swal.fire('Terhapus!', res.message, 'success').then(() => {
                     window.location.href = BaseConfig.baseURL + 'admin/camera-capture';
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
