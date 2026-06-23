<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<style>
  .progress-siswa {
    height: 5px;
    border-radius: 0px;
    background-color: rgb(186, 124, 222);
  }

  .my-progress-bar {
    height: 5px;
    border-radius: 0px;
  }
</style>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <?php if (session()->getFlashdata('msg')): ?>
          <div class="pb-2 px-3">
            <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?> ">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="material-icons">close</i>
              </button>
              <?= session()->getFlashdata('msg') ?>
            </div>
          </div>
        <?php endif; ?>
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title"><b>QR Code Siswa - <?= $kelas['kelas']; ?></b></h4>
            <p class="card-category">Generate dan download QR Code untuk siswa di kelas Anda</p>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-primary"><b>Data Siswa</b></h4>
                    <p>Total jumlah siswa : <b><?= count($siswa); ?></b></p>
                    <div class="row px-2">
                      <div class="col-12 col-xl-4 px-1 mb-2 mb-xl-0">
                        <button onclick="generateAllQrSiswa()" class="btn btn-primary btn-block p-2 font-weight-bold">
                          <i class="material-icons align-middle" style="font-size: 20px;">qr_code</i>
                          <span class="align-middle">Generate All</span>
                          <div id="progressSiswa" class="d-none mt-1 small">
                            <span id="progressTextSiswa"></span>
                            <i id="progressSelesaiSiswa" class="material-icons d-none" style="font-size: 16px;">check</i>
                            <div class="progress progress-siswa" style="height: 3px;">
                              <div id="progressBarSiswa" class="progress-bar my-progress-bar bg-white"
                                style="width: 0%;" role="progressbar"></div>
                            </div>
                          </div>
                        </button>
                      </div>
                      <div class="col-12 col-xl-4 px-1 mb-2 mb-xl-0">
                        <a href="<?= base_url('teacher/qr/download'); ?>" class="btn btn-success btn-block p-2 font-weight-bold">
                          <i class="material-icons align-middle" style="font-size: 20px;">cloud_download</i>
                          <span class="align-middle">Download All (.zip)</span>
                        </a>
                      </div>
                      <div class="col-12 col-xl-4 px-1 mb-2 mb-xl-0">
                        <a href="<?= base_url('teacher/qr/print'); ?>" class="btn btn-primary btn-block p-2 font-weight-bold" target="_blank">
                          <i class="material-icons align-middle" style="font-size: 20px;">print</i>
                          <span class="align-middle">Cetak QR</span>
                        </a>
                      </div>
                    </div>
                    <hr>
                      <div class="table-responsive mt-4">
                        <table id="tableSiswa" class="table table-hover">
                          <thead class="text-primary">
                            <th><b>No</b></th>
                            <th><b>NIS</b></th>
                            <th><b>Nama Siswa</b></th>
                            <th class="text-center"><b>Aksi</b></th>
                          </thead>
                          <tbody>
                            <?php $i = 1;
                            foreach ($siswa as $s): ?>
                              <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $s['nis']; ?></td>
                                <td><?= $s['nama_siswa']; ?></td>
                                <td class="text-center">
                                  <a href="<?= base_url('admin/qr/siswa/' . $s['id_siswa'] . '/download'); ?>" class="btn btn-info btn-sm">
                                    <i class="material-icons">download</i> Download QR
                                  </a>
                                  <a href="<?= base_url('admin/qr/siswa/print-single/' . $s['id_siswa']); ?>" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="material-icons">print</i> Cetak
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                  </div>
                </div>
              </div>
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
  $(document).ready(function() {
    $('#tableSiswa').DataTable({
      columnDefs: [{ orderable: false, targets: [-1] }]
    });
  });

  const dataSiswa = [
    <?php foreach ($siswa as $value) {
      echo "{
              'nama' : `$value[nama_siswa]`,
              'unique_code' : `$value[unique_code]`,
              'id_kelas' : `$value[id_kelas]`,
              'nomor' : `$value[nis]`
            },";
    }
    ; ?>
  ];

  function generateAllQrSiswa() {
    var i = 1;
    $('#progressSiswa').removeClass('d-none');
    $('#progressBarSiswa')
      .attr('aria-valuenow', '0')
      .attr('aria-valuemin', '0')
      .attr('aria-valuemax', dataSiswa.length)
      .attr('style', 'width: 0%;');

    dataSiswa.forEach(element => {
      jQuery.ajax({
        url: "<?= base_url('admin/generate/siswa'); ?>",
        type: 'post',
        data: {
          nama: element['nama'],
          unique_code: element['unique_code'],
          id_kelas: element['id_kelas'],
          nomor: element['nomor']
        },
        success: function (response) {
          if (!response) return;
          if (i != dataSiswa.length) {
            $('#progressTextSiswa').html('Progres: ' + i + '/' + dataSiswa.length);
          } else {
            $('#progressTextSiswa').html('Progres: ' + i + '/' + dataSiswa.length + ' selesai');
            $('#progressSelesaiSiswa').removeClass('d-none');
          }

          $('#progressBarSiswa')
            .attr('aria-valuenow', i)
            .attr('style', 'width: ' + (i / dataSiswa.length) * 100 + '%;');
          i++;
        }
      });
    });
  }
</script>
<?= $this->endSection() ?>