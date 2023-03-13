<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;

class GenerateLaporan extends BaseController
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
      $kelas = $this->kelasModel->getAllKelas();
      $guru = $this->guruModel->getAllGuru();

      $siswaPerKelas = [];

      foreach ($kelas as $value) {
         array_push($siswaPerKelas, $this->siswaModel->getSiswaByKelas($value['id_kelas']));
      }

      $data = [
         'title' => 'Generate Laporan',
         'ctx' => 'laporan',
         'siswaPerKelas' => $siswaPerKelas,
         'kelas' => $kelas,
         'guru' => $guru
      ];

      return view('admin/generate-laporan/generate-laporan', $data);
   }
}
