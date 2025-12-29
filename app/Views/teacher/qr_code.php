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
                                            <div class="col-12 col-xl-3 px-1">
                                                <button onclick="generateAllQrSiswa()"
                                                    class="btn btn-primary p-2 px-md-4 w-100">
                                                    <div class="d-flex align-items-center justify-content-center"
                                                        style="gap: 12px;">
                                                        <div>
                                                            <i class="material-icons"
                                                                style="font-size: 24px;">qr_code</i>
                                                        </div>
                                                        <div>
                                                            <h4 class="d-inline font-weight-bold">Generate All</h4>
                                                            <div id="progressSiswa" class="d-none mt-2">
                                                                <span id="progressTextSiswa"></span>
                                                                <i id="progressSelesaiSiswa"
                                                                    class="material-icons d-none">check</i>
                                                                <div class="progress progress-siswa">
                                                                    <div id="progressBarSiswa"
                                                                        class="progress-bar my-progress-bar bg-white"
                                                                        style="width: 0%;" role="progressbar"
                                                                        aria-valuenow="" aria-valuemin=""
                                                                        aria-valuemax=""></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </button>
                                            </div>
                                            <div class="col-12 col-xl-3 px-1">
                                                <a href="<?= base_url('teacher/qr/download'); ?>"
                                                    class="btn btn-success p-2 px-md-4 w-100 h-100">
                                                    <div class="d-flex align-items-center justify-content-center h-100"
                                                        style="gap: 12px;">
                                                        <div>
                                                            <i class="material-icons"
                                                                style="font-size: 24px;">cloud_download</i>
                                                        </div>
                                                        <div>
                                                            <div class="text-start">
                                                                <h4 class="d-inline font-weight-bold">Download All
                                                                    (.zip)</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="table-responsive mt-4">
                                            <table class="table table-hover">
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
                                                                <a href="<?= base_url('admin/qr/siswa/' . $s['id_siswa'] . '/download'); ?>"
                                                                    class="btn btn-info btn-sm">
                                                                    <i class="material-icons">download</i> Download QR
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
