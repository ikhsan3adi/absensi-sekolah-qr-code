<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-info mb-48">
                        <h4 class="card-title"><?= lang('Auth.register') ?></h4>
                        <p class="card-category">Buat akun petugas</p>
                    </div>
                    <div class="card-body mx-5 my-3">

                        <?= view('Myth\Auth\Views\_message_block') ?>

                        <form action="<?= base_url('admin/petugas/register') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="form-group mt-4">
                                <label for="email"><?= lang('Auth.email') ?></label>
                                <input type="email" id="email"
                                    class="form-control <?php if (session('errors.email')): ?>is-invalid<?php endif ?>"
                                    name="email" aria-describedby="emailHelp" placeholder="example@email.com"
                                    value="<?= old('email') ?>">
                                <?php if (session('errors.email')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.email') ?>
                                    </div>
                                <?php endif ?>
                            </div>

                            <div class="form-group mt-4">
                                <label for="username"><?= lang('Auth.username') ?></label>
                                <input type="text" id="username"
                                    class="form-control <?php if (session('errors.username')): ?>is-invalid<?php endif ?>"
                                    name="username" placeholder="yourusername" value="<?= old('username') ?>">
                                <div class="invalid-feedback">
                                    <?= session('errors.username') ?>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label for="password"><?= lang('Auth.password') ?></label>
                                <input type="password" id="password" name="password"
                                    class="form-control <?php if (session('errors.password')): ?>is-invalid<?php endif ?>"
                                    autocomplete="off">
                                <div class="invalid-feedback">
                                    <?= session('errors.password') ?>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label for="pass_confirm"><?= lang('Auth.repeatPassword') ?></label>
                                <input type="password" id="pass_confirm" name="pass_confirm"
                                    class="form-control <?php if (session('errors.pass_confirm')): ?>is-invalid<?php endif ?>"
                                    autocomplete="off">
                                <div class="invalid-feedback">
                                    <?= session('errors.pass_confirm') ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mt-4">
                                        <label for="role">Role</label>
                                        <select
                                            class="custom-select <?php if (session('errors.role')): ?>is-invalid<?php endif ?>"
                                            id="role"
                                            name="role">
                                            <option value="">--Pilih role--</option>
                                            <option value="0" <?= old('role') == "0" ? 'selected' : ''; ?>>
                                                Scanner
                                            </option>
                                            <option value="1" <?= old('role') == "1" ? 'selected' : ''; ?>>
                                                Super Admin
                                            </option>
                                            <option value="2" <?= old('role') == "2" ? 'selected' : ''; ?>>
                                                Kepsek
                                            </option>
                                            <option value="3" <?= old('role') == "3" ? 'selected' : ''; ?>>
                                                Staf Petugas
                                            </option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?= session('errors.role') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-4">
                                        <label for="id_guru">Hubungkan ke Guru (Opsional)</label>
                                        <select class="custom-select" id="id_guru" name="id_guru">
                                            <option value="">--Pilih Guru--</option>
                                            <?php foreach ($guru as $g): ?>
                                                <option value="<?= $g['id_guru']; ?>" <?= old('id_guru') == $g['id_guru'] ? 'selected' : ''; ?>><?= $g['nama_guru']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="btn btn-info btn-block mt-4"><?= lang('Auth.register') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>