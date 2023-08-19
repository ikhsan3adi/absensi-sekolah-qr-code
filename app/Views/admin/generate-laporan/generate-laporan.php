<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <?php if (session()->getFlashdata('msg')) : ?>
               <div class="pb-2 px-3">
                  <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success'  ?> ">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                     </button>
                     <?= session()->getFlashdata('msg') ?>
                  </div>
               </div>
            <?php endif; ?>
            <div class="card">
               <div class="card-header card-header-tabs card-header-info">
                  <div class="nav-tabs-navigation">
                     <div class="row">
                        <div class="col">
                           <h4 class="card-title"><b>Generate Laporan</b></h4>
                           <p class="card-category">Laporan absen</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="card h-100">
                           <form action="<?= base_url('admin/laporan/siswa'); ?>" method="post" class="card-body d-flex flex-column">
                              <h4 class="text-primary"><b>Laporan Absen Siswa</b></h4>
                              <div class="row align-items-center">
                                 <div class="col-auto">
                                    <p class="d-inline"><b>Bulan :</b></p>
                                 </div>
                                 <div class="col-5">
                                    <input type="month" name="tanggalSiswa" id="tanggalSiswa" class="form-control" value="<?= date('Y-m'); ?>">
                                 </div>
                              </div>
                              <select name="kelas" class="custom-select mt-3">
                                 <option value="">--Pilih kelas--</option>
                                 <?php foreach ($kelas as $key => $value) : ?>
                                    <?php
                                    $idKelas = $value['id_kelas'];
                                    $kelas = "{$value['kelas']} {$value['jurusan']}";
                                    $jumlahSiswa = count($siswaPerKelas[$key]);
                                    ?>
                                    <option value="<?= $idKelas; ?>">
                                       <?= "$kelas - {$jumlahSiswa} siswa"; ?>
                                    </option>
                                 <?php endforeach; ?>
                              </select>
                              <div class="errMsg"></div>
                              <div class="mt-auto d-flex flex-column">
                                 <button type="submit" name="type" value="pdf" class="btn btn-danger pl-3">
                                    <div class="row align-items-center">
                                       <div class="col-auto">
                                          <i class="material-icons" style="font-size: 32px;">print</i>
                                       </div>
                                       <div class="col">
                                          <div class="text-start">
                                             <h4 class="d-inline"><b>Generate pdf</b></h4>
                                          </div>
                                       </div>
                                    </div>
                                 </button>
                                 <button type="submit" name="type" value="doc" class="btn btn-info pl-3">
                                    <div class="row align-items-center">
                                       <div class="col-auto">
                                          <i class="material-icons" style="font-size: 32px;">description</i>
                                       </div>
                                       <div class="col">
                                          <div class="text-start">
                                             <h4 class="d-inline"><b>Generate doc</b></h4>
                                          </div>
                                       </div>
                                    </div>
                                 </button>
                                 <!-- <button type="submit" name="type" value="xls" class="btn btn-success pl-3 mt-auto">
                                 <div class="row align-items-center">
                                    <div class="col-auto">
                                       <i class="material-icons" style="font-size: 32px;">table_view</i>
                                    </div>
                                    <div class="col">
                                       <div class="text-start">
                                          <h4 class="d-inline"><b>Generate xls</b></h4>
                                       </div>
                                    </div>
                                 </div>
                              </button> -->
                              </div>

                           </form>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="card h-100">
                           <form action="<?= base_url('admin/laporan/guru'); ?>" method="post" class="card-body d-flex flex-column">
                              <h4 class="text-success"><b>Laporan Absen Guru</b></h4>
                              <p>Total jumlah guru : <b><?= count($guru); ?></b></p>
                              <div class="row align-items-center">
                                 <div class="col-auto">
                                    <p class="d-inline"><b>Bulan :</b></p>
                                 </div>
                                 <div class="col-5">
                                    <input type="month" name="tanggalGuru" id="tanggalGuru" class="form-control" value="<?= date('Y-m'); ?>">
                                 </div>
                              </div>
                              <div class="mt-auto d-flex flex-column">
                                 <button type="submit" name="type" value="pdf" class="btn btn-danger pl-3">
                                    <div class="row align-items-center">
                                       <div class="col-auto">
                                          <i class="material-icons" style="font-size: 32px;">print</i>
                                       </div>
                                       <div class="col">
                                          <div class="text-start">
                                             <h4 class="d-inline"><b>Generate pdf</b></h4>
                                          </div>
                                       </div>
                                    </div>
                                 </button>
                                 <button type="submit" name="type" value="doc" class="btn btn-info pl-3">
                                    <div class="row align-items-center">
                                       <div class="col-auto">
                                          <i class="material-icons" style="font-size: 32px;">description</i>
                                       </div>
                                       <div class="col">
                                          <div class="text-start">
                                             <h4 class="d-inline"><b>Generate doc</b></h4>
                                          </div>
                                       </div>
                                    </div>
                                 </button>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
                  <br><br>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?= $this->endSection() ?>