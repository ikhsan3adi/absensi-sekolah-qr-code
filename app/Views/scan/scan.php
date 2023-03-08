 <?= $this->extend('templates/starting_page_layout'); ?>

 <?= $this->section('navaction') ?>
 <a href="<?= base_url('/admin'); ?> " class="btn btn-primary pull-right pl-3">
    <i class="material-icons mr-2">dashboard</i>
    Dashboard Petugas
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
          <div class="row">
             <div class="mw-50 mx-auto">
                <div class="card">
                   <div class="col-sm-10 mx-auto card-header card-header-primary">
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
                      <h4>Pilih kamera</h4>

                      <select id="pilihKamera" class="form-select" aria-label="Default select example">
                         <option selected>Select camera devices</option>
                      </select>

                      <br><br>

                      <div class="row align-items-center w-100">
                         <div class="col h-100">
                            <div id="previewParent">
                               <div class="text-center">
                                  <h4 class="d-none" id="searching"><b>Mencari...</b></h4>
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
          </div>
       </div>
    </div>
 </div>

 <script type="text/javascript" src="<?= base_url('public/assets/js/plugins/zxing/zxing.min.js') ?>"></script>
 <script src="<?= base_url('public/assets/js/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
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
          url: "<?= base_url('/cek'); ?>",
          type: 'post',
          data: {
             'unique_code': code,
             'waktu': '<?= strtolower($waktu); ?>'
          },
          success: function(response, status, xhr) {
             audio.play();
             console.log(response);
             $('#hasilScan').html(response);
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