<?php

namespace App\Controllers\Admin;

use App\Models\SiswaModel;
use App\Models\KelasModel;

use App\Controllers\BaseController;

class DataSiswa extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KelasModel $kelasModel;

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->kelasModel = new KelasModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Siswa',
         'ctx' => 'siswa',
         'kelas' => $this->kelasModel->getAllKelas()
      ];

      return view('admin/data/data-siswa', $data);
   }

   public function ambilDataSiswa()
   {
      $kelas = $this->request->getVar('kelas') ?? null;
      $jurusan = $this->request->getVar('jurusan') ?? null;

      $result = $this->siswaModel->getAllSiswaWithKelas($kelas, $jurusan);

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-siswa', $data);
   }

   public function formEditSiswa($id)
   {
      $siswa = $this->siswaModel->getSiswaById($id);
      $kelas = $this->kelasModel->getAllKelas();

      $data = [
         'data' => $siswa,
         'kelas' => $kelas,
         'ctx' => 'siswa',
         'title' => 'Edit Siswa',
         'empty' => empty($result)
      ];

      return view('admin/data/edit/edit-data-siswa', $data);
   }
}
