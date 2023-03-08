<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;
use App\Models\SiswaModel;

use App\Controllers\BaseController;
use App\Models\KelasModel;

class LihatData extends BaseController
{
   protected SiswaModel $siswaModel;
   protected GuruModel $guruModel;

   protected KelasModel $kelasModel;

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->guruModel = new GuruModel();

      $this->kelasModel = new KelasModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Dashboard',
         'ctx' => ''
      ];

      return view('admin/dashboard', $data);
   }

   public function lihatDataSiswa()
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

   // === === === === === === === === === === === === === === === //
   // === === === === === === === === === === === === === === === //

   public function lihatDataGuru()
   {
      $data = [
         'title' => 'Data Guru',
         'ctx' => 'guru',
      ];

      return view('admin/data/data-guru', $data);
   }

   public function ambilDataGuru()
   {
      $result = $this->guruModel->getAllGuru();

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-guru', $data);
   }
}
