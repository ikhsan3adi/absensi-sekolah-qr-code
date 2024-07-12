<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<style>
  .progress-siswa {
    height: 5px;
    border-radius: 0px;
    background-color: rgb(186, 124, 222);
  }

  .progress-guru {
    height: 5px;
    border-radius: 0px;
    background-color: rgb(58, 192, 85);
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
        <?php if (session()->getFlashdata('msg')) : ?>
          <div class="pb-2 px-3">
            <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success'  ?> ">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="material-icons">close</i>
              </button>
              <?= session()->getFlashdata('msg') ?>
            </div>
          </div>
        <?php endif; ?>
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title"><b>Generate QR Code</b></h4>
            <p class="card-category">Generate QR berdasarkan kode unik data siswa/guru</p>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-primary"><b>Data Siswa</b></h4>
                    <p>Total jumlah siswa : <b><?= count($siswa); ?></b>
                      <br>
                      <a href="<?= base_url('admin/siswa'); ?>">Lihat data</a>
                    </p>
                    <div class="row px-2">
                      <div class="col-12 col-xl-6 px-1">
                        <button onclick="generateAllQrSiswa()" class="btn btn-primary p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">qr_code</i>
                            </div>
                            <div>
                              <h4 class="d-inline font-weight-bold">Generate All</h4>
                              <div id="progressSiswa" class="d-none mt-2">
                                <span id="progressTextSiswa"></span>
                                <i id="progressSelesaiSiswa" class="material-icons d-none" class="d-none">check</i>
                                <div class="progress progress-siswa">
                                  <div id="progressBarSiswa" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </button>
                      </div>
                      <div class="col-12 col-xl-6 px-1">
                        <a href="<?= base_url('admin/qr/siswa/download'); ?>" class="btn btn-primary p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">cloud_download</i>
                            </div>
                            <div>
                              <div class="text-start">
                                <h4 class="d-inline font-weight-bold">Download All</h4>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                    <hr>
                    <br>
                    <h4 class="text-primary"><b>Generate per kelas</b></h4>
                    <form action="<?= base_url('admin/qr/siswa/download'); ?>" method="get">
                      <select name="id_kelas" id="kelasSelect" class="custom-select mb-3" required>
                        <option value="">--Pilih kelas--</option>
                        <?php foreach ($kelas as $value) : ?>
                          <option id="idKelas<?= $value['id_kelas']; ?>" value="<?= $value['id_kelas']; ?>">
                            <?= $value['kelas'] . ' ' . $value['jurusan']; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <b class="text-danger mt-2" id="textErrorKelas"></b>
                      <div class="row px-2">
                        <div class="col-12 col-xl-6 px-1">
                          <button type="button" onclick="generateQrSiswaByKelas()" class="btn btn-primary p-2 px-md-4 w-100">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                              <div>
                                <i class="material-icons" style="font-size: 24px;">qr_code</i>
                              </div>
                              <div>
                                <div class="text-start">
                                  <h6 class="d-inline">Generate per kelas</h6>
                                </div>
                                <div id="progressKelas" class="d-none">
                                  <span id="progressTextKelas"></span>
                                  <i id="progressSelesaiKelas" class="material-icons d-none" class="d-none">check</i>
                                  <div class="progress progress-siswa d-none" id="progressBarBgKelas">
                                    <div id="progressBarKelas" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </button>
                        </div>
                        <div class="col-12 col-xl-6 px-1">
                          <button type="submit" class="btn btn-primary p-2 px-md-4 w-100">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                              <div>
                                <i class="material-icons" style="font-size: 24px;">cloud_download</i>
                              </div>
                              <div>
                                <div class="text-start">
                                  <h6 class="d-inline">Download Per Kelas</h6>
                                </div>
                              </div>
                            </div>
                          </button>
                        </div>
                      </div>
                    </form>
                    <br>
                    <p>
                      Untuk generate/download QR Code per masing-masing siswa kunjungi
                      <a href="<?= base_url('admin/siswa'); ?>"><b>data siswa</b></a>
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-success"><b>Data Guru</b></h4>
                    <p>Total jumlah guru : <b><?= count($guru); ?></b>
                      <br>
                      <a href="<?= base_url('admin/guru'); ?>" class="text-success">Lihat data</a>
                    </p>
                    <div class="row px-2">
                      <div class="col-12 col-xl-6 px-1">
                        <button onclick="generateAllQrGuru()" class="btn btn-success p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">qr_code</i>
                            </div>
                            <div>
                              <h4 class="d-inline font-weight-bold">Generate All</h4>
                              <div>
                                <div id="progressGuru" class="d-none mt-2">
                                  <span id="progressTextGuru"></span>
                                  <i id="progressSelesaiGuru" class="material-icons d-none" class="d-none">check</i>
                                  <div class="progress progress-guru">
                                    <div id="progressBarGuru" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </button>
                      </div>
                      <div class="col-12 col-xl-6 px-1">
                        <a href="<?= base_url('admin/qr/guru/download'); ?>" class="btn btn-success p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">cloud_download</i>
                            </div>
                            <div>
                              <div class="text-start">
                                <h4 class="d-inline font-weight-bold">Download All</h4>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                    <br>
                    <br>
                    <p>
                      Untuk generate/download QR Code per masing-masing guru kunjungi
                      <a href="<?= base_url('admin/guru'); ?>" class="text-success"><b>data guru</b></a>
                    </p>
                  </div>
                </div>
                <p class="text-danger">
                  <i class="material-icons" style="font-size: 16px;">warning</i>
                  File image QR Code tersimpan di [folder website]/public/uploads/
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  const dataGuru = [
    <?php foreach ($guru as $value) {
      echo "{
              'nama' : `$value[nama_guru]`,
              'unique_code' : `$value[unique_code]`,
              'nomor' : `$value[nuptk]`
            },";
    }; ?>
  ];

  const dataSiswa = [
    <?php foreach ($siswa as $value) {
      echo "{
              'nama' : `$value[nama_siswa]`,
              'unique_code' : `$value[unique_code]`,
              'id_kelas' : `$value[id_kelas]`,
              'nomor' : `$value[nis]`
            },";
    }; ?>
  ];

  var dataSiswaPerKelas = [];

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
        success: function(response) {
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

  function generateQrSiswaByKelas() {
    var i = 1;

    idKelas = $('#kelasSelect').val();

    if (idKelas == '') {
      $('#progressKelas').addClass('d-none');
      $('#textErrorKelas').html('Pilih kelas terlebih dahulu');
      return;
    }

    kelas = $('#idKelas' + idKelas).html();

    jQuery.ajax({
      url: "<?= base_url('admin/generate/siswa-by-kelas'); ?>",
      type: 'post',
      data: {
        idKelas: idKelas
      },
      success: function(response) {
        dataSiswaPerKelas = response;

        if (dataSiswaPerKelas.length < 1) {
          $('#progressKelas').addClass('d-none');
          $('#textErrorKelas').html('Data siswa kelas ' + kelas + ' tidak ditemukan');
          return;
        }

        $('#textErrorKelas').html('')

        $('#progressKelas').removeClass('d-none');
        $('#progressBarBgKelas')
          .removeClass('d-none');
        $('#progressBarKelas')
          .removeClass('d-none')
          .attr('aria-valuenow', '0')
          .attr('aria-valuemin', '0')
          .attr('aria-valuemax', dataSiswaPerKelas.length)
          .attr('style', 'width: 0%;');

        dataSiswaPerKelas.forEach(element => {
          jQuery.ajax({
            url: "<?= base_url('admin/generate/siswa'); ?>",
            type: 'post',
            data: {
              nama: element['nama_siswa'],
              unique_code: element['unique_code'],
              id_kelas: element['id_kelas'],
              nomor: element['nis']
            },
            success: function(response) {
              if (!response) return;
              if (i != dataSiswaPerKelas.length) {
                $('#progressTextKelas').html('Progres: ' + i + '/' + dataSiswaPerKelas.length);
              } else {
                $('#progressTextKelas').html('Progres: ' + i + '/' + dataSiswaPerKelas.length + ' selesai');
                $('#progressSelesaiKelas').removeClass('d-none');
              }

              $('#progressBarKelas')
                .attr('aria-valuenow', i)
                .attr('style', 'width: ' + (i / dataSiswaPerKelas.length) * 100 + '%;');
              i++;
            },
            error: function(xhr, status, thrown) {
              console.error(xhr + status + thrown);
            }
          });
        });
      }
    });
  }

  function generateAllQrGuru() {
    var i = 1;
    $('#progressGuru').removeClass('d-none');
    $('#progressBarGuru')
      .attr('aria-valuenow', '0')
      .attr('aria-valuemin', '0')
      .attr('aria-valuemax', dataGuru.length)
      .attr('style', 'width: 0%;');

    dataGuru.forEach(element => {
      jQuery.ajax({
        url: "<?= base_url('admin/generate/guru'); ?>",
        type: 'post',
        data: {
          nama: element['nama'],
          unique_code: element['unique_code'],
          nomor: element['nomor']
        },
        success: function(response) {
          if (!response) return;
          if (i != dataGuru.length) {
            $('#progressTextGuru').html('Progres: ' + i + '/' + dataGuru.length);
          } else {
            $('#progressTextGuru').html('Progres: ' + i + '/' + dataGuru.length + ' selesai');
            $('#progressSelesaiGuru').removeClass('d-none');
          }

          $('#progressBarGuru')
            .attr('aria-valuenow', i)
            .attr('style', 'width: ' + (i / dataGuru.length) * 100 + '%;');
          i++;
        }
      });
    });
  }
</script>
<?= $this->endSection() ?>