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
            $idGuru =  $result['id_guru'];
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
            $idSiswa =  $result['id_siswa'];
            $idKelas =  $result['id_kelas'];
            $data['type'] = TipeUser::Siswa;

            $sudahAbsen = $this->presensiSiswaModel->cekAbsen($idSiswa, Time::today()->toDateString());

            if ($sudahAbsen) {
               $data['presensi'] = $this->presensiSiswaModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Anda sudah absen hari ini', $data);
            }

            $this->presensiSiswaModel->absenMasuk($idSiswa, $date, $time, $idKelas);
            $messageString = 'Siswa ' . $result['nama_siswa'] . ' dengan NIS ' . $result['nis'] . $messageString;
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
            $idGuru =  $result['id_guru'];
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
            $idSiswa =  $result['id_siswa'];
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
