 <?= $this->extend('templates/starting_page_layout'); ?>

 <?= $this->section('navaction') ?>
 <a href="<?= base_url('/admin'); ?> " class="btn btn-primary pull-right pl-3">
    <i class="material-icons mr-2">dashboard</i>
    Dashboard
 </a>
 <?= $this->endSection() ?>

 <?= $this->section('content'); ?>
 <?php
   $oppBtn = '';

   $waktu == 'Masuk' ? $oppBtn = 'pulang' : $oppBtn = 'masuk';
   ?>
 <div class="main-panel">
    <div class="content">
       <div class="container-fluid">
          <div class="row mx-auto">
             <div class="col-lg-3 col-xl-4">
                <div class="card">
                   <div class="card-body">
                      <h3 class="mt-2"><b>Tips</b></h3>
                      <ul class="pl-3">
                         <li>Tunjukkan qr code sampai terlihat jelas di kamera</li>
                         <li>Posisikan qr code tidak terlalu jauh maupun terlalu dekat</li>
                      </ul>
                   </div>
                </div>
             </div>
             <div class="col-lg-6 col-xl-4">
                <div class="card">
                   <div class="col-10 mx-auto card-header card-header-primary">
                      <div class="row">
                         <div class="col">
                            <h4 class="card-title"><b>Absen <?= $waktu; ?></b></h4>
                            <p class="card-category">Silahkan tunjukkan QR Code anda</p>
                         </div>
                         <div class="col-md-auto">
                            <a href="<?= base_url("scan/$oppBtn"); ?>" class="btn btn-<?= $oppBtn == 'masuk' ? 'success' : 'warning'; ?>">
                               Absen <?= $oppBtn; ?>
                            </a>
                         </div>
                      </div>
                   </div>
                   <div class="card-body my-auto px-5">
                      <h4 class="d-inline">Pilih kamera</h4>

                      <select id="pilihKamera" class="custom-select w-50 ml-2" aria-label="Default select example" style="height: 35px;">
                         <option selected>Select camera devices</option>
                      </select>

                      <br>

                      <div class="row">
                         <div class="col-sm-12 mx-auto">
                            <div class="previewParent">
                               <div class="text-center">
                                  <h4 class="d-none w-100" id="searching"><b>Mencari...</b></h4>
                               </div>
                               <video id="previewKamera"></video>
                            </div>
                         </div>
                      </div>
                      <div id="hasilScan"></div>
                      <br>
                   </div>
                </div>
             </div>
             <div class="col-lg-3 col-xl-4">
                <div class="card">
                   <div class="card-body">
                      <h3 class="mt-2"><b>Penggunaan</b></h3>
                      <ul class="pl-3">
                         <li>Jika berhasil scan maka akan muncul data siswa/guru dibawah preview kamera</li>
                         <li>Klik tombol <b><span class="text-success">Absen masuk</span> / <span class="text-warning">Absen pulang</span></b> untuk mengubah waktu absensi</li>
                         <li>Untuk melihat data absensi, klik tombol <span class="text-primary"><i class="material-icons" style="font-size: 16px;">dashboard</i> Dashboard Petugas</span></li>
                         <li>Untuk mengakses halaman petugas anda harus login terlebih dahulu</li>
                      </ul>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>

 <script type="text/javascript" src="<?= base_url('public/assets/js/plugins/zxing/zxing.min.js') ?>"></script>
 <script src="<?= base_url('public/assets/js/core/jquery-3.5.1.min.js') ?>"></script>
 <script type="text/javascript">
    let selectedDeviceId = null;
    let audio = new Audio("<?= base_url('public/assets/audio/beep.mp3'); ?>");
    const codeReader = new ZXing.BrowserMultiFormatReader();
    const sourceSelect = $('#pilihKamera');

    $(document).on('change', '#pilihKamera', function() {
       selectedDeviceId = $(this).val();
       if (codeReader) {
          codeReader.reset();
          initScanner();
       }
    })

    const previewParent = document.getElementById('previewParent');
    const preview = document.getElementById('previewKamera');

    function initScanner() {
       codeReader.listVideoInputDevices()
          .then(videoInputDevices => {
             videoInputDevices.forEach(device =>
                console.log(`${device.label}, ${device.deviceId}`)
             );

             if (videoInputDevices.length < 1) {
                alert("Camera not found!");
                return;
             }

             if (selectedDeviceId == null) {
                if (videoInputDevices.length <= 1) {
                   selectedDeviceId = videoInputDevices[0].deviceId
                } else {
                   selectedDeviceId = videoInputDevices[1].deviceId
                }
             }

             if (videoInputDevices.length >= 1) {
                sourceSelect.html('');
                videoInputDevices.forEach((element) => {
                   const sourceOption = document.createElement('option')
                   sourceOption.text = element.label
                   sourceOption.value = element.deviceId
                   if (element.deviceId == selectedDeviceId) {
                      sourceOption.selected = 'selected';
                   }
                   sourceSelect.append(sourceOption)
                })
             }

             $('#previewParent').removeClass('unpreview');
             $('#previewKamera').removeClass('d-none');
             $('#searching').addClass('d-none');

             codeReader.decodeOnceFromVideoDevice(selectedDeviceId, 'previewKamera')
                .then(result => {
                   console.log(result.text);
                   cekData(result.text);

                   $('#previewKamera').addClass('d-none');
                   $('#previewParent').addClass('unpreview');
                   $('#searching').removeClass('d-none');

                   if (codeReader) {
                      codeReader.reset();

                      // delay 2,5 detik setelah berhasil meng-scan
                      setTimeout(() => {
                         initScanner();
                      }, 2500);
                   }
                })
                .catch(err => console.error(err));

          })
          .catch(err => console.error(err));
    }

    if (navigator.mediaDevices) {
       initScanner();
    } else {
       alert('Cannot access camera.');
    }

    async function cekData(code) {
       jQuery.ajax({
          url: "<?= base_url('scan/cek'); ?>",
          type: 'post',
          data: {
             'unique_code': code,
             'waktu': '<?= strtolower($waktu); ?>'
          },
          success: function(response, status, xhr) {
             audio.play();
             console.log(response);
             $('#hasilScan').html(response);

             $('html, body').animate({
                scrollTop: $("#hasilScan").offset().top
             }, 500);
          },
          error: function(xhr, status, thrown) {
             console.log(thrown);
             $('#hasilScan').html(thrown);
          }
       });
    }

    function clearData() {
       $('#hasilScan').html('');
    }
 </script>

 <?= $this->endSection(); ?>