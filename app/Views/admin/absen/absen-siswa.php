<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-3 pl-3">
                            <h4><b>Daftar Kelas</b></h4>
                            <p>Silakan pilih kelas</p>
                        </div>

                        <div class="card-body pt-1 px-3">
                            <div class="row">
                                <?php foreach ($data as $value) : ?>
                                    <?php
                                    $id_kelas = $value['id_kelas'];
                                    $kelas =  $value['kelas'] . ' ' . $value['jurusan'];
                                    ?>
                                    <div class="col-md-3">
                                        <button id="kelas-<?= $id_kelas; ?>" onclick="get_siswa(<?= $id_kelas; ?>, '<?= $kelas; ?>')" class="btn btn-primary w-100">
                                            <?= $kelas; ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="pt-3 pl-3 pb-2">
                                    <h4><b>Tanggal</b></h4>
                                    <input class="form-control" type="date" name="tangal" id="tanggal" value="<?= date('Y-m-d'); ?>" onchange="onDateChange(this.value)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" id="dataSiswa">
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class="col-auto me-auto">
                        <div class="pt-3 pl-3">
                            <h4><b>Absen Siswa</b></h4>
                            <p>Daftar siswa muncul disini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('public/assets/js/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
<script>
    var lastIdKelas;
    var lastKelas;

    function onDateChange(tanggal) {
        if (lastIdKelas != null && lastKelas != null) get_siswa(lastIdKelas, lastKelas);
    }

    function get_siswa(id_kelas, kelas) {
        var tanggal = $('#tanggal').val();

        updateBtn(id_kelas);

        jQuery.ajax({
            url: "<?= base_url('/admin/absen-siswa'); ?>",
            type: 'post',
            data: {
                'kelas': kelas,
                'id_kelas': id_kelas,
                'tanggal': tanggal
            },
            success: function(response, status, xhr) {
                // console.log(status);
                $('#dataSiswa').html(response);

                $('html, body').animate({
                    scrollTop: $("#dataSiswa").offset().top
                }, 500);
            },
            error: function(xhr, status, thrown) {
                console.log(thrown);
                $('#dataSiswa').html(thrown);
            }
        });

        lastIdKelas = id_kelas;
        lastKelas = kelas;
    }

    function updateBtn(id_btn) {
        for (let index = 1; index <= <?= count($data); ?>; index++) {
            if (index != id_btn) {
                $('#kelas-' + index).removeClass('btn-success');
                $('#kelas-' + index).addClass('btn-primary');
            } else {
                $('#kelas-' + index).removeClass('btn-primary');
                $('#kelas-' + index).addClass('btn-success');
            }
        }
    }

    function ubahKehadiran(idKehadiran, idSiswa, idKelas, jam_masuk = '') {
        if (!confirm("Apakah yakin untuk mengubah kehadiran?")) {
            return;
        }

        var tanggal = $('#tanggal').val();

        jQuery.ajax({
            url: "<?= base_url('/admin/absen-siswa/edit'); ?>",
            type: 'post',
            data: {
                'id_kehadiran': idKehadiran,
                'id_siswa': idSiswa,
                'id_kelas': idKelas,
                'tanggal': tanggal,
                'jam_masuk': jam_masuk
            },
            success: function(response, status, xhr) {
                // console.log(status);

                if (response['status']) {
                    alert('Berhasil ubah kehadiran : ' + response['nama_siswa']);
                } else {
                    alert('Gagal ubah kehadiran : ' + response['nama_siswa']);
                }

                get_siswa(lastIdKelas, lastKelas);
            },
            error: function(xhr, status, thrown) {
                console.log(thrown);
                alert('Gagal ubah kehadiran\n' + thrown);
            }
        });
    }
</script>
<?= $this->endSection() ?>