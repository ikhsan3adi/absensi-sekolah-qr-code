<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header card-header-tabs card-header-primary">
                        <div class="nav-tabs-navigation">
                            <div class="row">
                                <div class="col-md-2">
                                    <h4 class="card-title">Daftar Siswa</h4>
                                    <p class="card-category">Angkatan 2022/2023</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="nav-tabs-wrapper">
                                        <span class="nav-tabs-title">Kelas:</span>
                                        <ul class="nav nav-tabs" data-tabs="tabs">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#profile" data-toggle="tab">
                                                    <i class="material-icons">check</i> Semua
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#profile" data-toggle="tab">
                                                    <i class="material-icons">school</i> X
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#messages" data-toggle="tab">
                                                    <i class="material-icons">school</i> XI
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#settings" data-toggle="tab">
                                                    <i class="material-icons">school</i> XII
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="nav-tabs-wrapper">
                                        <span class="nav-tabs-title">Jurusan:</span>
                                        <ul class="nav nav-tabs" data-tabs="tabs">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#profile" data-toggle="tab">
                                                    <i class="material-icons">check</i> Semua
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#profile" data-toggle="tab">
                                                    <i class="material-icons">work</i> OTKP
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#messages" data-toggle="tab">
                                                    <i class="material-icons">work</i> BDP
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#settings" data-toggle="tab">
                                                    <i class="material-icons">work</i> AKL
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#settings" data-toggle="tab">
                                                    <i class="material-icons">work</i> RPL
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead class="text-warning">
                                <th>ID</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Kelamin</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>No HP</th>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $value) : ?>
                                    <tr>
                                        <td><?= $value['id_siswa']; ?></td>
                                        <td><?= $value['nama_siswa']; ?></td>
                                        <td><?= $value['jenis_kelamin']; ?></td>
                                        <td><?= $value['id_kelas']; ?></td>
                                        <td><?= $value['jurusan']; ?></td>
                                        <td><?= $value['no_hp']; ?></td>
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