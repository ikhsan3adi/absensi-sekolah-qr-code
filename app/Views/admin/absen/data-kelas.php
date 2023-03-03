<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="pt-3 pl-3">
                        <h4>Daftar Kelas</h4>
                        <p>Silakan pilih kelas</p>
                    </div>

                    <div class="card-body table-responsive">
                        <div class="row">
                            <?php foreach ($data as $value) : ?>
                                <?php $id_kelas = $value['id_kelas']; ?>
                                <div class="col-md-3">
                                    <button id="kelas-<?= $id_kelas; ?>" onclick="get_siswa(<?= $id_kelas; ?>, '<?= $value['kelas'] . ' ' . $value['jurusan']; ?>')" class="btn btn-primary w-100">
                                        <?= $value['kelas'] . " " . $value['jurusan']; ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h4>Tanggal</h4>
                                <input class="form-control" type="date" name="tangal" id="tanggal" value="<?= date('Y-m-d'); ?>" onchange="onDateChange(this.value)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card" id="dataSiswa">
                    <div class="pt-3 pl-3">
                        <h4>Absen Siswa</h4>
                        <p>Daftar siswa muncul disini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('public/assets/js/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
<script>
    var lastIdKelas = '';

    function onDateChange(tanggal) {
        get_siswa(lastIdKelas);
    }

    function get_siswa(id_kelas, kelas) {
        var tanggal = $('#tanggal').val();
        jQuery.ajax({
            url: "<?= base_url('/admin/absen-siswa'); ?>",
            type: 'post',
            data: {
                'kelas': kelas,
                'id_kelas': id_kelas,
                'tanggal': tanggal
            },
            success: function(response, status, xhr) {
                console.log(response);
                $('#dataSiswa').html(response);
            },
            error: function(xhr, status, thrown) {
                console.log(thrown);
                $('#dataSiswa').html(thrown);
            }
        });

        lastIdKelas = id_kelas;
    }
</script>
<?= $this->endSection() ?>