<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header card-header-success">
                        <h4 class="card-title">Daftar Guru</h4>
                        <p class="card-category">Angkatan 2022/2023</p>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead class="text-success">
                                <th>ID</th>
                                <th>NUPTK</th>
                                <th>Nama Guru</th>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $value) : ?>
                                    <tr>
                                        <td><?= $value['id_guru']; ?></td>
                                        <td><?= $value['nuptk']; ?></td>
                                        <td><?= $value['nama_guru']; ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>