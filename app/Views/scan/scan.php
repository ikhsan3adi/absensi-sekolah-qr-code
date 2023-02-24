 <?= $this->extend('templates/starting_page'); ?>

 <?= $this->section('content'); ?>
 <div class="main-panel">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
       <div class="container-fluid">
          <div class="navbar-wrapper">
             <a class="navbar-brand" href="#">Scan QR</a>
          </div>
          <div class="collapse navbar-collapse justify-content-end">
             <a href="/login" class="btn btn-primary pull-right">Login petugas
             </a>
          </div>
       </div>
    </nav>
    <!-- End Navbar -->

    <div class="content">
       <div class="container-fluid">
          <div class="row">
             <div class="col m-auto">
                <div class="card">
                   <div class="col-md-6 mx-auto card-header card-header-primary">
                      <h4 class="card-title">Absen <?= $waktu; ?></h4>
                      <p class="card-category">Silahkan tunjukkan QR Code anda</p>
                   </div>
                   <div class="card-body m-auto">
                      <h4>Pilih kamera</h4>

                      <select id="pilihKamera" class="form-select" aria-label="Default select example">
                         <option selected>Select camera devices</option>
                      </select>

                      <br>
                      <br>
                      <div class="row">
                         <div class="col">
                            <video id="previewKamera"></video>
                         </div>
                      </div>
                      <div class="row">
                         <div class="col" id="hasilScan">
                         </div>
                      </div>
                      <br>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>

 <script type="text/javascript" src="<?= base_url('assets/js/plugins/zxing/zxing.min.js') ?>"></script>
 <script src="<?= base_url('assets/js/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
 <script type="text/javascript">
    let selectedDeviceId = null;
    let audio = new Audio("assets/audio/beep.mp3");
    const codeReader = new ZXing.BrowserMultiFormatReader();
    const sourceSelect = $('#pilihKamera');

    $(document).on('change', '#pilihKamera', function() {
       selectedDeviceId = $(this).val();
       if (codeReader) {
          codeReader.reset();
          initScanner();
       }
    })

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

             codeReader.decodeOnceFromVideoDevice(selectedDeviceId, 'previewKamera')
                .then(result => {
                   console.log(result.text);
                   cek_data(result.text);

                   if (codeReader) {
                      codeReader.reset();
                      //  initScanner();
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

    async function cek_data(code) {
       jQuery.ajax({
          url: '<?= base_url('/cek'); ?>',
          type: 'post',
          data: {
             'unique_code': code
          },
          success: function(response, status, xhr) {
             audio.play();
             console.log(response);
             $('#hasilScan').html(response);
          },
          error: function(xhr, status, thrown) {
             console.log(thrown);
          }
       });
    }
 </script>

 <?= $this->endSection(); ?>