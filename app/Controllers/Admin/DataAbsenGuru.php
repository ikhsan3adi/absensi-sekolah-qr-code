<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiGuruModel;
use CodeIgniter\I18n\Time;

class DataAbsenGuru extends BaseController
{
   protected GuruModel $guruModel;

   protected PresensiGuruModel $presensiGuru;

   protected KehadiranModel $kehadiranModel;

   public function __construct()
   {
      $this->guruModel = new GuruModel();

      $this->presensiGuru = new PresensiGuruModel();

      $this->kehadiranModel = new KehadiranModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Absen Guru',
         'ctx' => 'absen-guru',
      ];

      return view('admin/absen/absen-guru', $data);
   }

   public function ambilDataGuru()
   {
      // ambil variabel POST
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      $result = $this->presensiGuru->getPresensiByTanggal($tanggal);

      $data = [
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-absen-guru', $data);
   }

   public function ambilKehadiran()
   {
      $idPresensi = $this->request->getVar('id_presensi');
      $idGuru = $this->request->getVar('id_guru');

      $data = [
         'presensi' => $this->presensiGuru->getPresensiById($idPresensi),
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'data' => $this->guruModel->getGuruById($idGuru)
      ];

      return view('admin/absen/ubah-kehadiran-modal', $data);
   }

   public function ubahKehadiran()
   {
      // ambil variabel POST
      $idKehadiran = $this->request->getVar('id_kehadiran');
      $idGuru = $this->request->getVar('id_guru');
      $tanggal = $this->request->getVar('tanggal');
      $jamMasuk = $this->request->getVar('jam_masuk');
      $jamKeluar = $this->request->getVar('jam_keluar');
      $keterangan = $this->request->getVar('keterangan');

      $cek = $this->presensiGuru->cekAbsen($idGuru, $tanggal);

      $result = $this->presensiGuru->updatePresensi(
         $cek == false ? NULL : $cek,
         $idGuru,
         $tanggal,
         $idKehadiran,
         $jamMasuk ?? NULL,
         $jamKeluar ?? NULL,
         $keterangan
      );

      $response['nama_guru'] = $this->guruModel->getGuruById($idGuru)['nama_guru'];

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }
}
