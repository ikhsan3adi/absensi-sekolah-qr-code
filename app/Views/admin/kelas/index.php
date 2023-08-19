<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <?php if (session()->getFlashdata('msg')) : ?>
          <div class="pb-2 px-3">
            <div class="alert alert-success">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="material-icons">close</i>
              </button>
              <?= session()->getFlashdata('msg') ?>
            </div>
          </div>
        <?php endif; ?>
        <div class="row">
          <div class="col-12 col-lg-6">
            <div class="card">
              <div class="card-header card-header-tabs card-header-primary">
                <div class="nav-tabs-navigation">
                  <div class="row">
                    <div class="col">
                      <h4 class="card-title"><b>Daftar Kelas</b></h4>
                      <p class="card-category">Angkatan 2022/2023</p>
                    </div>
                    <div class="col-auto">
                      <div class="nav-tabs-wrapper">
                        <ul class="nav nav-tabs" data-tabs="tabs">
                          <li class="nav-item">
                            <a class="nav-link" id="tabBtn" onclick="removeHover()" href="<?= base_url('admin/kelas/new'); ?>">
                              <i class="material-icons">add</i> Tambah data kelas
                              <div class="ripple-container"></div>
                            </a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="refreshBtn" onclick="getDataKelas()" href="#" data-toggle="tab">
                              <i class="material-icons">refresh</i> Refresh
                              <div class="ripple-container"></div>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="dataKelas">
                <p class="text-center mt-3">Daftar kelas muncul disini</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-6">
            <div class="card">
              <div class="card-header card-header-tabs card-header-primary">
                <div class="nav-tabs-navigation">
                  <div class="row">
                    <div class="col">
                      <h4 class="card-title"><b>Daftar Jurusan</b></h4>
                      <p class="card-category">Angkatan 2022/2023</p>
                    </div>
                    <div class="col-auto">
                      <div class="nav-tabs-wrapper">
                        <ul class="nav nav-tabs" data-tabs="tabs">
                          <li class="nav-item">
                            <a class="nav-link" id="tabBtn2" onclick="removeHover()" href="<?= base_url('admin/jurusan/new'); ?>">
                              <i class="material-icons">add</i> Tambah data jurusan
                              <div class="ripple-container"></div>
                            </a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="refreshBtn2" onclick="getDataJurusan()" href="#" data-toggle="tab">
                              <i class="material-icons">refresh</i> Refresh
                              <div class="ripple-container"></div>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="dataJurusan">
                <p class="text-center mt-3">Daftar jurusan muncul disini</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  getDataKelas();
  getDataJurusan();

  function getDataKelas() {
    jQuery.ajax({
      url: "<?= base_url('/admin/kelas/get'); ?>",
      type: 'get',
      data: {},
      success: function(response, status, xhr) {
        $('#dataKelas').html(response);

        $('html, body').animate({
          scrollTop: $("#dataKelas").offset().top
        }, 500);
        $('#refreshBtn').removeClass('active show');
      },
      error: function(xhr, status, thrown) {
        console.log(thrown);
        $('#dataKelas').html(thrown);
        $('#refreshBtn').removeClass('active show');
      }
    });
  }

  function getDataJurusan() {
    jQuery.ajax({
      url: "<?= base_url('/admin/jurusan/get'); ?>",
      type: 'get',
      data: {},
      success: function(response, status, xhr) {
        $('#dataJurusan').html(response);

        $('html, body').animate({
          scrollTop: $("#dataJurusan").offset().top
        }, 500);
        $('#refreshBtn2').removeClass('active show');
      },
      error: function(xhr, status, thrown) {
        console.log(thrown);
        $('#dataJurusan').html(thrown);
        $('#refreshBtn2').removeClass('active show');
      }
    });
  }

  function removeHover() {
    setTimeout(() => {
      $('#tabBtn').removeClass('active show');
      $('#tabBtn2').removeClass('active show');
    }, 250);
  }
</script>
<?= $this->endSection() ?>