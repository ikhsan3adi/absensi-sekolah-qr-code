<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\PresensiGuruModel;
use App\Models\PresensiSiswaModel;
use App\Libraries\enums\TipeUser;

class Scan extends BaseController
{
   private bool $WANotificationEnabled;

   protected SiswaModel $siswaModel;
   protected GuruModel $guruModel;

   protected PresensiSiswaModel $presensiSiswaModel;
   protected PresensiGuruModel $presensiGuruModel;

   public function __construct()
   {
      $this->WANotificationEnabled = getenv('WA_NOTIFICATION') === 'true' ? true : false;

      $this->siswaModel = new SiswaModel();
      $this->guruModel = new GuruModel();
      $this->presensiSiswaModel = new PresensiSiswaModel();
      $this->presensiGuruModel = new PresensiGuruModel();
   }

   public function index($t = 'Masuk')
   {
      $data = ['waktu' => $t, 'title' => 'Absensi Siswa dan Guru Berbasis QR Code'];
      return view('scan/scan', $data);
   }

   public function cekKode()
   {
      // Cek apakah hari ini libur
      $holidayModel = new \App\Models\HariLiburModel();
      $today = date('Y-m-d');
      $holiday = $holidayModel->where('tanggal', $today)->first();

      if ($holiday) {
         return $this->showErrorView("Hari ini sistem presensi dinonaktifkan karena: " . $holiday['keterangan']);
      }

      // ambil variabel POST
      $uniqueCode = $this->request->getVar('unique_code');
      $waktuAbsen = $this->request->getVar('waktu');

      $status = false;
      $type = TipeUser::Siswa;

      // cek data siswa di database
      $result = $this->siswaModel->cekSiswa($uniqueCode);

      if (empty($result)) {
         // jika cek siswa gagal, cek data guru
         $result = $this->guruModel->cekGuru($uniqueCode);

         if (!empty($result)) {
            $status = true;

            $type = TipeUser::Guru;
         } else {
            $status = false;

            $result = NULL;
         }
      } else {
         $status = true;
      }

      if (!$status) { // data tidak ditemukan
         return $this->showErrorView('Data tidak ditemukan');
      }

      // jika data ditemukan
      switch ($waktuAbsen) {
         case 'masuk':
            return $this->absenMasuk($type, $result);
            break;

         case 'pulang':
            return $this->absenPulang($type, $result);
            break;

         default:
            return $this->showErrorView('Data tidak valid');
            break;
      }
   }

   public function absenMasuk($type, $result)
   {
      // data ditemukan
      $data['data'] = $result;
      $data['waktu'] = 'masuk';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();
      $messageString = " sudah absen masuk pada tanggal $date jam $time";
      // absen masuk
      switch ($type) {
         case TipeUser::Guru:
            $idGuru = $result['id_guru'];
            $data['type'] = TipeUser::Guru;

            $sudahAbsen = $this->presensiGuruModel->cekAbsen($idGuru, $date);

            if ($sudahAbsen) {
               $data['presensi'] = $this->presensiGuruModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Anda sudah absen hari ini', $data);
            }

            $this->presensiGuruModel->absenMasuk($idGuru, $date, $time);
            $messageString = $result['nama_guru'] . ' dengan NIP ' . $result['nuptk'] . $messageString;
            $data['presensi'] = $this->presensiGuruModel->getPresensiByIdGuruTanggal($idGuru, $date);

            break;

         case TipeUser::Siswa:
            $idSiswa = $result['id_siswa'];
            $idKelas = $result['id_kelas'];
            $data['type'] = TipeUser::Siswa;

            $sudahAbsen = $this->presensiSiswaModel->cekAbsen($idSiswa, Time::today()->toDateString());

            if ($sudahAbsen) {
               $data['presensi'] = $this->presensiSiswaModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Anda sudah absen hari ini', $data);
            }

            $menitKeterlambatan = 0;
            $jamMasukLimit = $this->generalSettings->jam_masuk_limit ?? null;

            if ($jamMasukLimit && $time > $jamMasukLimit) {
               $limit = Time::parse($date . ' ' . $jamMasukLimit);
               $current = Time::parse($date . ' ' . $time);
               $diff = $current->difference($limit);
               $menitKeterlambatan = abs($diff->getMinutes());
            }

            $this->presensiSiswaModel->absenMasuk($idSiswa, $date, $time, $idKelas, $menitKeterlambatan);
            $messageString = 'Siswa ' . $result['nama_siswa'] . ' dengan NIS ' . $result['nis'] . $messageString;
            if ($menitKeterlambatan > 0) {
               $messageString .= " (Terlambat $menitKeterlambatan menit)";
            }
            $data['presensi'] = $this->presensiSiswaModel->getPresensiByIdSiswaTanggal($idSiswa, $date);

            break;

         default:
            return $this->showErrorView('Tipe tidak valid');
      }

      // kirim notifikasi ke whatsapp
      if ($this->WANotificationEnabled && !empty($result['no_hp'])) {
         $message = [
            'destination' => $result['no_hp'],
            'message' => $messageString,
            'delay' => 0
         ];
         try {
            $this->sendNotification($message);
         } catch (\Exception $e) {
            log_message('error', 'Error sending notification: ' . $e->getMessage());
         }
      }
      return view('scan/scan-result', $data);
   }

   public function absenPulang($type, $result)
   {
      // data ditemukan
      $data['data'] = $result;
      $data['waktu'] = 'pulang';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();
      $messageString = " sudah absen pulang pada tanggal $date jam $time";

      // absen pulang
      switch ($type) {
         case TipeUser::Guru:
            $idGuru = $result['id_guru'];
            $data['type'] = TipeUser::Guru;

            $sudahAbsen = $this->presensiGuruModel->cekAbsen($idGuru, $date);

            if (!$sudahAbsen) {
               return $this->showErrorView('Anda belum absen hari ini', $data);
            }

            $this->presensiGuruModel->absenKeluar($sudahAbsen, $time);
            $messageString = $result['nama_guru'] . ' dengan NIP ' . $result['nuptk'] . $messageString;
            $data['presensi'] = $this->presensiGuruModel->getPresensiById($sudahAbsen);

            break;

         case TipeUser::Siswa:
            $idSiswa = $result['id_siswa'];
            $data['type'] = TipeUser::Siswa;

            $sudahAbsen = $this->presensiSiswaModel->cekAbsen($idSiswa, $date);

            if (!$sudahAbsen) {
               return $this->showErrorView('Anda belum absen hari ini', $data);
            }

            $this->presensiSiswaModel->absenKeluar($sudahAbsen, $time);
            $messageString = 'Siswa ' . $result['nama_siswa'] . ' dengan NIS ' . $result['nis'] . $messageString;
            $data['presensi'] = $this->presensiSiswaModel->getPresensiById($sudahAbsen);

            break;
         default:
            return $this->showErrorView('Tipe tidak valid');
      }

      // kirim notifikasi ke whatsapp
      if ($this->WANotificationEnabled && !empty($result['no_hp'])) {
         $message = [
            'destination' => $result['no_hp'],
            'message' => $messageString,
            'delay' => 0
         ];
         try {
            $this->sendNotification($message);
         } catch (\Exception $e) {
            log_message('error', 'Error sending notification: ' . $e->getMessage());
         }
      }

      return view('scan/scan-result', $data);
   }

   /**
    * Verify wajah via Python face-recognition service (localhost:5000/verify-face)
    * Menerima POST: face_encoding (base64 data URL), waktu (masuk|pulang)
    * Service mengembalikan: {status: 'success', name: '<unique_code>'} atau error
    */
   public function verifyFace()
   {
      // Cek apakah hari ini libur
      $holidayModel = new \App\Models\HariLiburModel();
      $today = date('Y-m-d');
      $holiday = $holidayModel->where('tanggal', $today)->first();

      if ($holiday) {
         return $this->showErrorView("Hari ini sistem presensi dinonaktifkan karena: " . $holiday['keterangan']);
      }

      $faceEncoding = $this->request->getVar('face_encoding');
      $waktuAbsen   = $this->request->getVar('waktu');

      if (empty($faceEncoding)) {
         return $this->showErrorView('Data gambar wajah tidak ditemukan.');
      }

      // ── Kirim ke Python face-recognition service ──
      $faceApiBase = rtrim(getenv('FACE_API_URL') ?: 'http://localhost:5000', '/');
      $faceApiUrl  = str_ends_with($faceApiBase, '/verify-face') ? $faceApiBase : ($faceApiBase . '/verify-face');

      $ch = curl_init($faceApiUrl);
      curl_setopt_array($ch, [
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_POST           => true,
         CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
         CURLOPT_POSTFIELDS     => json_encode(['face_encoding' => $faceEncoding]),
         CURLOPT_TIMEOUT        => 15,
      ]);
      $raw      = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curlErr  = curl_error($ch);
      curl_close($ch);

      if ($raw === false) {
        return $this->showErrorView('Face recognition service tidak dapat dihubungi' . ($curlErr ? ': ' . $curlErr : ''));
      }

      $apiResult = json_decode($raw, true);
      if (!is_array($apiResult)) {
        return $this->showErrorView('Respons dari face recognition service tidak valid.');
      }

      if ($httpCode !== 200 || ($apiResult['status'] ?? '') !== 'success') {
         $errMsg = $apiResult['message'] ?? 'Wajah tidak dikenali.';
         return $this->showErrorView($errMsg);
      }

      // ── Nama file wajah = unique_code siswa/guru ──
      $uniqueCode = $apiResult['name'] ?? '';

      if (empty($uniqueCode)) {
         return $this->showErrorView('Respons service tidak valid.');
      }

      // ── Cari data di database ──
      $status = false;
      $type   = TipeUser::Siswa;
      $result = $this->siswaModel->cekSiswa($uniqueCode);

      if (empty($result)) {
         $result = $this->guruModel->cekGuru($uniqueCode);
         if (!empty($result)) {
            $status = true;
            $type   = TipeUser::Guru;
         }
      } else {
         $status = true;
      }

      if (!$status) {
         return $this->showErrorView('Data tidak ditemukan untuk wajah yang terdeteksi (kode: ' . $uniqueCode . ')');
      }

      switch ($waktuAbsen) {
         case 'masuk':
            return $this->absenMasuk($type, $result);
         case 'pulang':
            return $this->absenPulang($type, $result);
         default:
            return $this->showErrorView('Waktu absen tidak valid.');
      }
   }

   public function showErrorView(string $msg = 'no error message', $data = NULL)
   {
      $errdata = $data ?? [];
      $errdata['msg'] = $msg;

      return view('scan/error-scan-result', $errdata);
   }

   protected function sendNotification($message)
   {
      $token = getenv('WHATSAPP_TOKEN');
      $provider = getenv('WHATSAPP_PROVIDER');

      if (empty($provider)) {
         return;
      }
      if (empty($token)) {
         return;
      }

      switch ($provider) {
         case 'Fonnte':
            $whatsapp = new \App\Libraries\Whatsapp\Fonnte\Fonnte($token);
            break;
         default:
            return;
      }
      $whatsapp->sendMessage($message);
   }
}
