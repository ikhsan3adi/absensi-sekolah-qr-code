<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">

      <div class="row">
         <div class="col-lg-7">

            <!-- ── Webcam Panel ── -->
            <div class="card">
               <div class="card-header card-header-primary">
                  <h4 class="card-title"><b><i class="material-icons mr-2" style="vertical-align:middle">photo_camera</i>Ambil Foto Wajah</b></h4>
                  <p class="card-category">Gunakan kamera browser untuk mengambil foto wajah</p>
               </div>
               <div class="card-body text-center">

                  <!-- Video Live Preview -->
                  <div id="cameraContainer" style="position:relative; display:inline-block;">
                     <video id="webcamVideo"
                        autoplay playsinline muted
                        style="width:100%;max-width:480px;border-radius:12px;background:#000;border:3px solid #9c27b0;">
                     </video>
                     <!-- Overlay guide -->
                     <div id="faceGuide" style="
                        position:absolute;top:50%;left:50%;
                        transform:translate(-50%,-50%);
                        width:200px;height:240px;
                        border:3px dashed rgba(156,39,176,0.7);
                        border-radius:50%;
                        pointer-events:none;
                        display:none;
                     "></div>
                  </div>

                  <!-- Canvas (hidden, untuk capture) -->
                  <canvas id="captureCanvas" style="display:none;"></canvas>

                  <!-- Preview hasil capture -->
                  <div id="previewContainer" style="display:none; margin-top:16px;">
                     <p class="text-muted mb-1"><small>Preview Foto:</small></p>
                     <img id="capturedImage"
                        alt="Preview foto"
                        style="max-width:240px;border-radius:12px;border:3px solid #4caf50;box-shadow:0 4px 12px rgba(0,0,0,0.15);"
                     >
                  </div>

                  <!-- Kamera Error -->
                  <div id="cameraError" class="alert alert-danger mt-3" style="display:none;">
                     <i class="material-icons mr-2" style="vertical-align:middle">error</i>
                     <span id="cameraErrorMsg">Kamera tidak dapat diakses.</span>
                  </div>

                  <!-- Tombol Kontrol Kamera -->
                  <div class="mt-3 d-flex justify-content-center flex-wrap" style="gap:8px;">
                     <button id="btnStartCamera" class="btn btn-info px-4" onclick="startCamera()">
                        <i class="material-icons mr-1">videocam</i> Aktifkan Kamera
                     </button>
                     <button id="btnCapture" class="btn btn-warning px-4" onclick="capturePhoto()" style="display:none;">
                        <i class="material-icons mr-1">camera_alt</i> Ambil Foto
                     </button>
                     <button id="btnRetake" class="btn btn-default px-4" onclick="retakePhoto()" style="display:none;">
                        <i class="material-icons mr-1">replay</i> Ulangi
                     </button>
                     <button id="btnStopCamera" class="btn btn-danger px-4" onclick="stopCamera()" style="display:none;">
                        <i class="material-icons mr-1">videocam_off</i> Matikan Kamera
                     </button>
                  </div>

               </div>
            </div>

         </div>
         <div class="col-lg-5">

            <!-- ── Form Simpan Foto ── -->
            <div class="card">
               <div class="card-header card-header-success">
                  <h4 class="card-title"><b>Data Foto</b></h4>
                  <p class="card-category">Isi informasi foto yang akan disimpan</p>
               </div>
               <div class="card-body">

                  <div id="saveAlert" class="alert" style="display:none;"></div>

                  <div class="form-group">
                     <label class="bmd-label-floating">Tipe Subjek <span class="text-danger">*</span></label>
                     <select id="entityType" class="custom-select" onchange="onEntityTypeChange()">
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                        <option value="umum">Umum (tanpa entitas)</option>
                     </select>
                  </div>

                  <!-- Siswa select -->
                  <div id="siswaGroup" class="form-group">
                     <label class="bmd-label-floating">Siswa <span class="text-danger">*</span></label>
                     <select id="siswaId" class="custom-select">
                        <option value="">-- Pilih Siswa --</option>
                        <?php foreach ($siswaList as $s): ?>
                           <option value="<?= $s['id_siswa'] ?>" data-name="<?= esc($s['nama_siswa']) ?>">
                              <?= esc($s['nama_siswa']) ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                  </div>

                  <!-- Guru select -->
                  <div id="guruGroup" class="form-group" style="display:none;">
                     <label class="bmd-label-floating">Guru <span class="text-danger">*</span></label>
                     <select id="guruId" class="custom-select">
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach ($guruList as $g): ?>
                           <option value="<?= $g['id_guru'] ?>" data-name="<?= esc($g['nama_guru']) ?>">
                              <?= esc($g['nama_guru']) ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                  </div>

                  <div class="form-group">
                     <label class="bmd-label-floating">Keterangan</label>
                     <select id="keterangan" class="custom-select">
                        <option value="Verifikasi wajah masuk">Verifikasi wajah masuk</option>
                        <option value="Verifikasi wajah pulang">Verifikasi wajah pulang</option>
                        <option value="Foto wajah awal pendaftaran">Foto wajah awal pendaftaran</option>
                        <option value="">Tanpa keterangan</option>
                     </select>
                  </div>

                  <div class="mt-4 d-flex" style="gap:8px;">
                     <button id="btnSave" class="btn btn-success flex-fill" onclick="savePhoto()" disabled>
                        <i class="material-icons mr-1">save</i> Simpan Foto
                     </button>
                     <a href="<?= base_url('admin/camera-capture') ?>" class="btn btn-default">
                        <i class="material-icons">arrow_back</i> Kembali
                     </a>
                  </div>

                  <!-- Loading indicator -->
                  <div id="savingIndicator" class="text-center mt-3" style="display:none;">
                     <div class="spinner-border text-success" role="status" style="width:2rem;height:2rem;">
                        <span class="sr-only">Menyimpan...</span>
                     </div>
                     <p class="text-muted mt-2">Menyimpan foto...</p>
                  </div>

               </div>
            </div>

            <!-- ── Tips Panel ── -->
            <div class="card" style="border-left:4px solid #2196F3;">
               <div class="card-body py-3">
                  <p class="mb-1"><i class="material-icons text-info mr-1" style="vertical-align:middle;font-size:18px;">info</i> <b>Tips pengambilan foto:</b></p>
                  <ul class="mb-0 pl-3" style="font-size:13px;color:#555;">
                     <li>Pastikan wajah menghadap kamera secara langsung</li>
                     <li>Pencahayaan cukup, hindari backlight</li>
                     <li>Posisikan wajah di dalam lingkaran panduan</li>
                     <li>Ekspresi netral, mata terbuka</li>
                  </ul>
               </div>
            </div>

         </div>
      </div>

   </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ══════════════════════════════════════════════════
//  State Variables
// ══════════════════════════════════════════════════
let stream          = null;
let capturedBase64  = null;
const video         = document.getElementById('webcamVideo');
const canvas        = document.getElementById('captureCanvas');
const ctx           = canvas.getContext('2d');
const previewImg    = document.getElementById('capturedImage');

// ══════════════════════════════════════════════════
//  Camera Control
// ══════════════════════════════════════════════════
async function startCamera() {
   try {
      stream = await navigator.mediaDevices.getUserMedia({
         video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
         audio: false,
      });
      video.srcObject = stream;
      document.getElementById('faceGuide').style.display = 'block';
      toggleButtons('streaming');
      hideError();
   } catch (err) {
      showError('Kamera tidak dapat diakses: ' + err.message);
      console.error(err);
   }
}

function stopCamera() {
   if (stream) {
      stream.getTracks().forEach(t => t.stop());
      stream = null;
   }
   video.srcObject = null;
   document.getElementById('faceGuide').style.display = 'none';
   toggleButtons('idle');
   clearPreview();
}

// ══════════════════════════════════════════════════
//  Capture
// ══════════════════════════════════════════════════
function capturePhoto() {
   if (!stream) { showError('Kamera belum aktif.'); return; }

   canvas.width  = video.videoWidth  || 640;
   canvas.height = video.videoHeight || 480;
   ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

   // Ambil base64 JPEG
   capturedBase64 = canvas.toDataURL('image/jpeg', 0.85);
   previewImg.src = capturedBase64;

   document.getElementById('previewContainer').style.display = 'block';
   document.getElementById('btnSave').disabled = false;
   toggleButtons('captured');
}

function retakePhoto() {
   clearPreview();
   toggleButtons('streaming');
}

function clearPreview() {
   capturedBase64 = null;
   previewImg.src = '';
   document.getElementById('previewContainer').style.display = 'none';
   document.getElementById('btnSave').disabled = true;
}

// ══════════════════════════════════════════════════
//  UI Helpers
// ══════════════════════════════════════════════════
function toggleButtons(state) {
   const startBtn   = document.getElementById('btnStartCamera');
   const captureBtn = document.getElementById('btnCapture');
   const retakeBtn  = document.getElementById('btnRetake');
   const stopBtn    = document.getElementById('btnStopCamera');

   startBtn.style.display   = state === 'idle'      ? 'inline-flex' : 'none';
   captureBtn.style.display  = state === 'streaming' ? 'inline-flex' : 'none';
   retakeBtn.style.display   = state === 'captured'  ? 'inline-flex' : 'none';
   stopBtn.style.display     = (state === 'streaming' || state === 'captured') ? 'inline-flex' : 'none';
}

function showError(msg) {
   document.getElementById('cameraError').style.display = 'block';
   document.getElementById('cameraErrorMsg').textContent = msg;
}
function hideError() {
   document.getElementById('cameraError').style.display = 'none';
}

function onEntityTypeChange() {
   const type = document.getElementById('entityType').value;
   document.getElementById('siswaGroup').style.display = type === 'siswa' ? 'block' : 'none';
   document.getElementById('guruGroup').style.display  = type === 'guru'  ? 'block' : 'none';
}

// ══════════════════════════════════════════════════
//  Save Photo
// ══════════════════════════════════════════════════
function savePhoto() {
   if (!capturedBase64) {
      showSaveAlert('danger', 'Ambil foto terlebih dahulu.');
      return;
   }

   const entityType = document.getElementById('entityType').value;
   let entityId   = null;
   let entityName = null;

   if (entityType === 'siswa') {
      const sel = document.getElementById('siswaId');
      entityId  = sel.value;
      entityName = sel.options[sel.selectedIndex]?.dataset.name || null;
      if (!entityId) { showSaveAlert('warning', 'Pilih siswa terlebih dahulu.'); return; }
   } else if (entityType === 'guru') {
      const sel = document.getElementById('guruId');
      entityId  = sel.value;
      entityName = sel.options[sel.selectedIndex]?.dataset.name || null;
      if (!entityId) { showSaveAlert('warning', 'Pilih guru terlebih dahulu.'); return; }
   }

   const keterangan = document.getElementById('keterangan').value;

   // Show loading
   document.getElementById('btnSave').disabled = true;
   document.getElementById('savingIndicator').style.display = 'block';

   $.ajax({
      url: BaseConfig.baseURL + 'admin/camera-capture/store',
      type: 'POST',
      contentType: 'application/json',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        [BaseConfig.csrfTokenName]: Cookies.get(BaseConfig.csrfTokenName),
      },
      data: JSON.stringify({
         image:       capturedBase64,
         entity_type: entityType,
         entity_id:   entityId   || null,
         entity_name: entityName || null,
         keterangan:  keterangan || null,
      }),
      success: function (res) {
         document.getElementById('savingIndicator').style.display = 'none';
         if (res.success) {
            Swal.fire({
               title: 'Berhasil!',
               text: res.message,
               icon: 'success',
               confirmButtonText: 'Lihat Daftar',
               showCancelButton: true,
               cancelButtonText: 'Ambil Foto Lagi',
            }).then((result) => {
               if (result.isConfirmed) {
                  window.location.href = BaseConfig.baseURL + 'admin/camera-capture';
               } else {
                  clearPreview();
                  document.getElementById('btnSave').disabled = false;
                  if (stream) toggleButtons('streaming');
               }
            });
         } else {
            document.getElementById('btnSave').disabled = false;
            showSaveAlert('danger', res.message);
         }
      },
      error: function (xhr) {
         document.getElementById('savingIndicator').style.display = 'none';
         document.getElementById('btnSave').disabled = false;
         const res = xhr.responseJSON;
         showSaveAlert('danger', (res && res.message) ? res.message : 'Terjadi kesalahan pada server.');
      }
   });
}

function showSaveAlert(type, msg) {
   const el = document.getElementById('saveAlert');
   el.className = 'alert alert-' + type;
   el.innerHTML = '<i class="material-icons mr-1" style="vertical-align:middle;font-size:18px;">info</i> ' + msg;
   el.style.display = 'block';
   setTimeout(() => { el.style.display = 'none'; }, 5000);
}

// ── Cleanup saat halaman ditutup ──
window.addEventListener('beforeunload', function () {
   if (stream) stream.getTracks().forEach(t => t.stop());
});
</script>
<?= $this->endSection() ?>
