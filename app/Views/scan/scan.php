<?= $this->extend('templates/starting_page_layout'); ?>

<?= $this->section('navaction') ?>
<?php
   $user = auth()->user();
   $dashUrl = '/admin';
   if ($user && $user->inGroup('superadmin')) {
      $dashUrl = '/admin';
   } elseif ($user && $user->inGroup('admin')) {
      $dashUrl = '/admin';
   } elseif ($user && $user->inGroup('kepsek')) {
      $dashUrl = '/admin';
   } elseif ($user && $user->inGroup('guru')) {
      $dashUrl = '/teacher/dashboard';
   } elseif ($user && $user->inGroup('scanner')) {
      $dashUrl = '/scan';
   }
?>
<a href="<?= base_url('/cek-kehadiran'); ?> " class="btn btn-default pull-right pl-3 mr-2">
   <i class="material-icons mr-2">visibility</i>
   Cek Kehadiran
</a>

<a href="<?= base_url('/izin'); ?>" class="btn btn-info pull-right pl-3 mr-2">
   <i class="material-icons mr-2">mail</i>
   Ajukan Izin
</a>

<a href="<?= base_url($dashUrl); ?>" class="btn btn-primary pull-right pl-3">
   <i class="material-icons mr-2">dashboard</i>
   Dashboard
</a>

<a href="<?= base_url('/logout'); ?> " class="btn btn-danger pull-right pl-3">
   <i class="material-icons mr-2">exit_to_app</i>
   Logout
</a>
<?= $this->endSection() ?>

<?= $this->section('content'); ?>
<?php
$oppBtn = '';

$waktu == 'Masuk' ? $oppBtn = 'pulang' : $oppBtn = 'masuk';
?>
<div class="main-panel">
   <div class="content pt-2 px-0 px-sm-1 px-md-2">
      <div class="container-fluid px-0 px-md-2">
         <div class="row mx-auto">
            <div class="col-lg-6 col-xxl-5 order-1 order-lg-2">
               <div class="card">
                  <div class="col-10 mx-auto card-header card-header-primary">
                     <div class="row">
                        <div class="col-md-6">
                           <h4 class="card-title"><b>Absen <?= $waktu; ?></b></h4>
                           <p class="card-category text-nowrap">Silahkan tunjukkan QR Code anda</p>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center justify-content-md-end">
                           <a href="<?= base_url("scan/$oppBtn"); ?>" class="btn btn-<?= $oppBtn == 'masuk' ? 'success' : 'warning'; ?> px-3">
                              Absen <?= $oppBtn; ?>
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="card-body my-auto px-5">
                     <div class="togglebutton mb-3 text-center">
                        <label>
                           <input type="checkbox" id="toggleKamera" checked>
                           <span class="toggle"></span>
                           <b class="text-dark">Gunakan Kamera (Scan QR)</b>
                        </label>
                        <label class="ml-2">
                           <input type="checkbox" id="toggleRFID">
                           <span class="toggle"></span>
                           <b class="text-dark">RFID</b>
                        </label>
                        <label class="ml-2">
                           <input type="checkbox" id="toggleWajah">
                           <span class="toggle"></span>
                           <b class="text-dark">Scan Wajah</b>
                        </label>
                     </div>

                     <div id="cameraSection">
                        <h4 class="d-inline">Pilih kamera</h4>

                        <select id="pilihKamera" class="custom-select w-50 ml-2" aria-label="Default select example" style="height: 35px;">
                           <option selected>Select camera devices</option>
                        </select>

                        <br>

                        <div class="row pt-2">
                           <div class="col-sm-12 mx-auto">
                              <div class="previewParent">
                                 <div class="text-center">
                                    <h4 class="d-none w-100" id="searching"><b>Mencari...</b></h4>
                                 </div>
                                 <video id="previewKamera"></video>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row mt-3" id="rfidSection" style="display: none;">
                     <div class="col-12 text-center">
                        <div id="rfidStatus" class="mb-2">
                           <span class="badge badge-pill badge-secondary" id="statusBadge">
                              <i class="material-icons" style="font-size: 14px; vertical-align: middle;">usb</i>
                              <span id="statusText">RFID Reader: Mencari...</span>
                           </span>
                        </div>
                        <input type="text" id="rfidInput" class="form-control mx-auto text-center" style="width: 80%; border: 2px solid #9c27b0; border-radius: 5px;" placeholder="Siap Scan Kartu RFID"
                           autofocus autocomplete="off">
                        <small class="text-muted d-block mt-1">Pastikan kotak di atas tetap berwarna ungu saat
                           scan.</small>
                     </div>
                  </div>

                  <!-- ═══ FACE SCAN SECTION ═══ -->
                  <div id="faceSection" class="px-4 pb-3" style="display: none;">
                     <div class="text-center mb-2">
                        <span class="badge badge-pill badge-info" id="faceBadge">
                           <i class="material-icons" style="font-size: 14px; vertical-align: middle;">face</i>
                           <span id="faceStatusText">Kamera wajah: Memuat...</span>
                        </span>
                     </div>
                     <div class="text-center">
                        <div style="position: relative; display: inline-block; width: 100%; max-width: 420px;">
                           <video id="faceVideo" autoplay playsinline muted
                              style="width:100%; border-radius:12px; border:2px solid #9c27b0; background:#000;"></video>
                           <canvas id="faceCanvas" style="display:none;"></canvas>
                           <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                              width:160px;height:200px;border:2px dashed rgba(156,39,176,0.6);
                              border-radius:50%;pointer-events:none;"></div>
                        </div>
                     </div>
                     <div class="text-center mt-3">
                        <button id="btnCaptureFace" class="btn btn-lg"
                           style="background:linear-gradient(135deg,#9c27b0,#673ab7);color:#fff;
                                  border-radius:50px;padding:10px 36px;
                                  box-shadow:0 4px 15px rgba(156,39,176,0.4);transition:all .2s;">
                           <i class="material-icons mr-1" style="vertical-align:middle;">camera</i>
                           <span id="btnFaceLabel">Scan Wajah Sekarang</span>
                        </button>
                     </div>
                     <small class="text-muted d-block text-center mt-2">
                        Posisikan wajah di dalam lingkaran, lalu klik tombol di atas.
                     </small>
                  </div>

                  <div id="hasilScan" class="px-5 pb-4 pt-1"></div>
                  <br>
               </div>
            </div>
            <div class="col-lg-3 col-xxl-3 order-2 order-lg-1">
               <div class="card" id="tips-card">
                  <div class="card-body">
                     <h3 class="mt-2"><b>Tips</b></h3>
                     <ul class="pl-3">
                        <li>Tunjukkan qr code sampai terlihat jelas di kamera</li>
                        <li>Posisikan qr code tidak terlalu jauh maupun terlalu dekat</li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="col-lg-3 col-xxl-4 order-last">
               <div class="card" id="usage-card">
                  <div class="card-body">
                     <h3 class="mt-2"><b>Penggunaan</b></h3>
                     <ul class="pl-3">
                        <li>Jika berhasil scan maka akan muncul data siswa/guru dibawah preview kamera</li>
                        <li>Klik tombol <b><span class="text-success">Absen masuk</span> / <span class="text-warning">Absen
                                 pulang</span></b> untuk mengubah waktu absensi</li>
                        <li>Untuk melihat data absensi, klik tombol <span class="text-primary"><i class="material-icons" style="font-size: 16px;">dashboard</i> Dashboard</span></li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="text/javascript" src="<?= base_url('assets/js/plugins/zxing/zxing.min.js') ?>"></script>

<!-- TTS Konfigurasi (dari .env) -->
<script>
   window.ttsApiBase  = <?= json_encode(rtrim(getenv('TTS_API_BASE') ?: 'http://localhost:8085', '/')); ?>;
   window.ttsVoice    = <?= json_encode(getenv('TTS_VOICE') ?: 'female'); ?>;
   window.ttsRate     = <?= json_encode(getenv('TTS_RATE') ?: '+0%'); ?>;
   window.ttsLanguage = 'indonesian';
</script>
<script type="text/javascript" src="<?= base_url('assets/js/tts.js') ?>"></script>

<script type="text/javascript">
   let selectedDeviceId = null;
   let audio = new Audio("<?= base_url('assets/audio/beep.mp3'); ?>");
   const codeReader = new ZXing.BrowserMultiFormatReader();
   const sourceSelect = $('#pilihKamera');

   $(document).on('change', '#pilihKamera', function () {
      selectedDeviceId = $(this).val();
      if (codeReader && $('#toggleKamera').is(':checked')) {
         codeReader.reset();
         initScanner();
      }
   })

   $(document).on('change', '#toggleKamera', function () {
      if (this.checked) {
         $('#cameraSection').slideDown();
         initScanner();
      } else {
         codeReader.reset();
         $('#cameraSection').slideUp();
      }
   });

   $(document).on('change', '#toggleRFID', function () {
      if (this.checked) {
         $('#rfidSection').slideDown();
         $('#rfidInput').focus();
      } else {
         $('#rfidSection').slideUp();
      }
   });

   // ═══ FACE SCAN LOGIC ═══
   let faceStream = null;

   $(document).on('change', '#toggleWajah', function () {
      if (this.checked) {
         $('#faceSection').slideDown();
         startFaceCamera();
      } else {
         stopFaceCamera();
         $('#faceSection').slideUp();
      }
   });

   function startFaceCamera() {
      const video = document.getElementById('faceVideo');
      $('#faceStatusText').text('Kamera wajah: Memuat...');
      $('#faceBadge').removeClass('badge-success badge-danger').addClass('badge-info');

      navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } })
         .then(function (stream) {
            faceStream = stream;
            video.srcObject = stream;
            $('#faceStatusText').text('Kamera wajah: Siap');
            $('#faceBadge').removeClass('badge-info badge-danger').addClass('badge-success');
         })
         .catch(function (err) {
            console.error('Face camera error:', err);
            $('#faceStatusText').text('Kamera tidak dapat diakses!');
            $('#faceBadge').removeClass('badge-info badge-success').addClass('badge-danger');
         });
   }

   function stopFaceCamera() {
      const video = document.getElementById('faceVideo');
      if (faceStream) {
         faceStream.getTracks().forEach(t => t.stop());
         faceStream = null;
      }
      video.srcObject = null;
      $('#faceStatusText').text('Kamera wajah: Memuat...');
      $('#faceBadge').removeClass('badge-success badge-danger').addClass('badge-info');
   }

   $('#btnCaptureFace').on('click', function () {
      const video  = document.getElementById('faceVideo');
      const canvas = document.getElementById('faceCanvas');

      if (!faceStream || !video.videoWidth) {
         alert('Kamera belum siap. Harap tunggu sebentar.');
         return;
      }

      // Capture frame
      canvas.width  = video.videoWidth;
      canvas.height = video.videoHeight;
      canvas.getContext('2d').drawImage(video, 0, 0);
      const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

      // UI: loading state
      const btn = $('#btnCaptureFace');
      btn.prop('disabled', true);
      $('#btnFaceLabel').text('Memproses...');
      $('#faceBadge').removeClass('badge-success badge-danger').addClass('badge-info');
      $('#faceStatusText').text('Mengenali wajah...');

      $.ajax({
         url: '<?= base_url("scan/face"); ?>',
         type: 'POST',
         data: setAjaxData({
            'face_encoding': dataUrl,
            'waktu': '<?= strtolower($waktu); ?>'
         }),
success: function (response) {
             audio.play();
             $('#hasilScan').html(response);
             announceAbsensiFromResponse();
             $('html, body').animate({ scrollTop: $('#hasilScan').offset().top }, 500);
             $('#faceStatusText').text('Kamera wajah: Siap');
             $('#faceBadge').removeClass('badge-info badge-danger').addClass('badge-success');
          },
         error: function (xhr) {
            $('#hasilScan').html(xhr.responseText || 'Terjadi kesalahan.');
            $('#faceStatusText').text('Gagal mengenali wajah');
            $('#faceBadge').removeClass('badge-info badge-success').addClass('badge-danger');
         },
         complete: function () {
            btn.prop('disabled', false);
            $('#btnFaceLabel').text('Scan Wajah Sekarang');
         }
      });
   });

   const previewParent = document.getElementById('previewParent');
   const preview = document.getElementById('previewKamera');

   function initScanner() {
      if (!$('#toggleKamera').is(':checked')) {
         console.log("Scanner disabled by user toggle.");
         return;
      }
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
               .catch(err => console.warn(err));

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
         data: setAjaxData({
            'unique_code': code,
            'waktu': '<?= strtolower($waktu); ?>'
         }),
success: function (response, status, xhr) {
             audio.play();
             console.log(response);
             $('#hasilScan').html(response);
             announceAbsensiFromResponse();
             $('html, body').animate({
                scrollTop: $("#hasilScan").offset().top
             }, 500);
          },
         error: function (xhr, status, thrown) {
            console.log(thrown);
            $('#hasilScan').html(thrown);
         }
      });
   }

   function clearData() {
      $('#hasilScan').html('');
}

    function announceAbsensiFromResponse() {
       var wrapper = $('#hasilScan').find('.absen-result-wrapper');
       if (wrapper.length) {
          var nama  = wrapper.data('tts-name');
          var waktu = wrapper.data('tts-waktu');
          if (nama && typeof speakAbsensi === 'function') {
             speakAbsensi(nama, waktu);
          }
       }
    }

    // RFID Listener
   $('#rfidInput').on('keypress', function (e) {
      if (e.which == 13) {
         let code = $(this).val().trim();
         if (code.length > 0) {
            console.log("RFID Scanned: " + code);
            cekData(code);
            $(this).val('');
         }
      }
   });

   $(document).ready(function () {
      const rfidInput = $('#rfidInput');
      const statusBadge = $('#statusBadge');
      const statusText = $('#statusText');

      function updateStatus(focused) {
         if (focused) {
            statusBadge.removeClass('badge-secondary').addClass('badge-success');
            statusText.text('RFID Reader: Siap');
            rfidInput.css('border-color', '#4caf50');
         } else {
            statusBadge.removeClass('badge-success').addClass('badge-secondary');
            statusText.text('RFID Reader: Tidak Fokus (Klik Disini)');
            rfidInput.css('border-color', '#f44336');
         }
      }

      if ($('#toggleRFID').is(':checked')) {
         rfidInput.focus();
         updateStatus(true);
      } else {
         updateStatus(false);
      }

      rfidInput.on('focus', function () {
         updateStatus(true);
      });

      rfidInput.on('blur', function () {
         updateStatus(false);
         // Auto refocus after a short delay if not focusing on other inputs
         setTimeout(() => {
            if ($('#toggleRFID').is(':checked') && !$('#pilihKamera').is(':focus')) {
               rfidInput.focus();
            }
         }, 3000);
      });

      // Ensure focus remains on rfidInput if clicked elsewhere (except camera select)
      $(document).on('click', function (e) {
         if ($('#toggleRFID').is(':checked') && !$(e.target).closest('#pilihKamera, #toggleKamera, #toggleRFID').length) {
            rfidInput.focus();
         }
      });
   });
</script>

<?= $this->endSection(); ?>
