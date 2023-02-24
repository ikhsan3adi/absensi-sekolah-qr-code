 <?= $this->extend('templates/starting_page'); ?>

 <?= $this->section('content'); ?>
 <div class="main-panel">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
       <div class="container-fluid">
          <div class="navbar-wrapper">
             <a class="navbar-brand" href="#">Login</a>
          </div>
          <div class="collapse navbar-collapse justify-content-end">
             <a href="/" class="btn btn-primary pull-right">Scan QR
             </a>
          </div>
       </div>
    </nav>
    <!-- End Navbar -->

    <div class="content">
       <div class="container-fluid">
          <div class="row">
             <div class="col-md-4 m-auto">
                <div class="card">
                   <div class="card-header card-header-primary mb-48">
                      <h4 class="card-title">Login petugas</h4>
                      <p class="card-category">Silahkan masukkan username dan password anda</p>
                   </div>
                   <div class="card-body">
                      <form>
                         <div class="row">
                            <div class="col-md-12">
                               <div class="form-group">
                                  <label class="bmd-label-floating">Username</label>
                                  <input type="text" class="form-control">
                               </div>
                            </div>
                         </div>
                         <div class="row">
                            <div class="col-md-12">
                               <div class="form-group">
                                  <label class="bmd-label-floating">Password</label>
                                  <input type="text" class="form-control">
                               </div>
                            </div>
                         </div>
                         <button type="submit" class="btn btn-primary col-md-12">Login</button>
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