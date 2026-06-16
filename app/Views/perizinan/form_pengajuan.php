<?= $this->extend('templates/starting_page_layout') ?>

<?= $this->section('content') ?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center">
                <div class="col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Form Pengajuan Izin/Sakit</h4>
                            <p class="card-category">Isi formulir di bawah ini dengan benar</p>
                        </div>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success">
                                    <?= session()->getFlashdata('success') ?>
                                </div>
                            <?php endif; ?>

                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('izin/submit') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Saya adalah:</label>
                                            <div class="form-check form-check-radio">
                                                <label class="form-check-label mr-3">
                                                    <input class="form-check-input" type="radio" name="type" id="typeSiswa" value="siswa" checked> Siswa
                                                    <span class="circle"><span class="check"></span></span>
                                                </label>
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="radio" name="type" id="typeGuru" value="guru"> Guru
                                                    <span class="circle"><span class="check"></span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label id="labelId" class="bmd-label-floating">NIS Siswa</label>
                                            <div class="input-group">
                                                <input type="text" name="nis" id="nis" class="form-control" required value="<?= old('nis') ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary btn-sm my-0" type="button" id="btnCekNis">Verifikasi</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="containerSiswa" style="display:none;">
                                    <div class="col-md-12">
                                        <div class="alert alert-info py-2">
                                            Nama: <b id="namaSiswa"></b>
                                            <input type="hidden" name="id_target" id="id_target">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label-control">Tanggal Mulai</label>
                                            <input type="date" name="tanggal_mulai" class="form-control" required value="<?= old('tanggal_mulai', date('Y-m-d')) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label-control">Tanggal Selesai</label>
                                            <input type="date" name="tanggal_selesai" class="form-control" required value="<?= old('tanggal_selesai', date('Y-m-d')) ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Tipe Izin</label>
                                            <select name="tipe_izin" class="form-control" required>
                                                <option value="Sakit" <?= old('tipe_izin') == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                                <option value="Izin" <?= old('tipe_izin') == 'Izin' ? 'selected' : '' ?>>Izin (Acara Keluarga/Lainnya)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="bmd-label-floating">Alasan / Keterangan</label>
                                            <textarea name="alasan" class="form-control" rows="3" required><?= old('alasan') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <label>Unggah Bukti (Foto Surat Dokter/Izin)</label>
                                        <input type="file" name="bukti" class="form-control" required accept="image/*">
                                        <small class="text-muted">Maksimal 2MB (Format: JPG, JPEG, PNG)</small>
                                    </div>
                                </div>

                                <button type="submit" id="btnSubmit" class="btn btn-primary btn-block mt-4" disabled>Kirim Pengajuan</button>
                                <a href="<?= base_url() ?>" class="btn btn-default btn-block">Kembali ke Beranda</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnCekNis = document.getElementById('btnCekNis');
        const inputNis = document.getElementById('nis');
        const labelId = document.getElementById('labelId');
        const containerSiswa = document.getElementById('containerSiswa');
        const namaSiswa = document.getElementById('namaSiswa');
        const idTarget = document.getElementById('id_target');
        const btnSubmit = document.getElementById('btnSubmit');
        const typeRadios = document.querySelectorAll('input[name="type"]');

        typeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                labelId.innerText = this.value === 'guru' ? 'NUPTK Guru' : 'NIS Siswa';
                containerSiswa.style.display = 'none';
                btnSubmit.disabled = true;
            });
        });

        btnCekNis.addEventListener('click', function() {
            const idValue = inputNis.value;
            const typeValue = document.querySelector('input[name="type"]:checked').value;

            if (!idValue) return swal("Peringatan", "Masukkan nomor identitas terlebih dahulu", "warning");

            fetch('<?= base_url('izin/get-siswa') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `nis=${idValue}&type=${typeValue}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        containerSiswa.style.display = 'block';
                        namaSiswa.innerText = result.data.nama;
                        idTarget.value = result.data.id;
                        btnSubmit.disabled = false;
                        swal("Berhasil", "Data ditemukan: " + result.data.nama, "success");
                    } else {
                        containerSiswa.style.display = 'none';
                        btnSubmit.disabled = true;
                        swal("Gagal", result.message, "error");
                    }
                })
                .catch(err => {
                    console.error(err);
                    swal("Error", "Terjadi kesalahan saat mengecek data.", "error");
                });
        });
    });
</script>
<?= $this->endSection() ?>
