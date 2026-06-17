<?= $this->extend('templates/starting_page_layout'); ?>

<?= $this->section('navaction') ?>
<!--<a href="<?= base_url('/'); ?>" class="btn btn-primary pull-right pl-3">
    <i class="material-icons mr-2">qr_code</i>
    Scan QR
</a>-->
<?= $this->endSection() ?>

<?= $this->section('content'); ?>
<div class="main-panel">
   <div class="content pt-5 pt-md-2 px-0 px-sm-1 px-md-2">
      <div class="container-fluid px-0 px-md-2">
         <div class="row">
            <div class="col-xxl-5 col-lg-7 col-md-8 col-sm-10 m-auto">
               <div class="card">
                  <div class="card-header card-header-primary mb-48">
                     <h4 class="card-title">Login</h4>
                     <p class="card-category">Silahkan masukkan email dan password anda</p>
                  </div>

                  <div class="card-body mx-5 my-3">
                     <?= view('\App\Views\admin\_message_block') ?>

                     <form action="<?= url_to('login') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label class="bmd-label-floating"><?= lang('Auth.email') ?></label>
                                 <input type="email"
                                    class="form-control <?php if (session('errors.login')): ?>is-invalid<?php endif ?>"
                                    name="email"
                                    inputmode="email"
                                    autocomplete="email"
                                    value="<?= old('email') ?>"
                                    required>
                                 <div class="invalid-feedback">
                                    <?= session('errors.login') ?>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row mt-3">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label class="bmd-label-floating">Password</label>
                                 <input type="password"
                                    name="password"
                                    class="form-control <?php if (session('errors.password')): ?>is-invalid<?php endif ?>"
                                    autocomplete="current-password"
                                    required>
                                 <div class="invalid-feedback">
                                    <?= session('errors.password') ?>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                           <div class="form-check">
                              <label class="form-check-label">
                                 <input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')): ?> checked <?php endif ?>>
                                 <?= lang('Auth.rememberMe') ?>
                              </label>
                           </div>
                        <?php endif; ?>

                        <br>

                        <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.login') ?></button>

                        <div class="text-center mt-3">
                           <p class="mb-1">Atau ajukan ketidakhadiran:</p>
                           <div class="d-flex flex-column">
                              <a href="<?= base_url('izin') ?>" class="btn btn-info btn-block mb-2">
                                 <i class="material-icons mr-2">mail</i> Ajukan Izin / Sakit
                              </a>
                              <a href="<?= base_url('cek-kehadiran') ?>" class="btn btn-default btn-block">
                                 <i class="material-icons mr-2">visibility</i> Cek Kehadiran Siswa
                              </a>
                           </div>
                        </div>

                        <?php if (setting('Auth.allowMagicLinkLogins')): ?>
                           <p class="text-center mt-3">
                              <a href="<?= url_to('magic-link') ?>"><?= lang('Auth.forgotPassword') ?></a>
                           </p>
                        <?php endif; ?>

                        <div class="clearfix"></div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?= $this->endSection(); ?>
