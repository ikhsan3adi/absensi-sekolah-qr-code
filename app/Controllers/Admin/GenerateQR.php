<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;

class GenerateQR extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KelasModel $kelasModel;

   protected GuruModel $guruModel;

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->kelasModel = new KelasModel();

      $this->guruModel = new GuruModel();
   }

   public function index()
   {
      $siswa = $this->siswaModel->getAllSiswaWithKelas();
      $kelas = $this->kelasModel->getDataKelas();
      $guru = $this->guruModel->getAllGuru();

      $data = [
         'title' => 'Generate QR Code',
         'ctx' => 'qr',
         'siswa' => $siswa,
         'kelas' => $kelas,
         'guru' => $guru
      ];

      return view('admin/generate-qr/generate-qr', $data);
   }

   public function getSiswaByKelas()
   {
      $idKelas = $this->request->getVar('idKelas');

      $siswa = $this->siswaModel->getSiswaByKelas($idKelas);

      return $this->response->setJSON($siswa);
   }
}
