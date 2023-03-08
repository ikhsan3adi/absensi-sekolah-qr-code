<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;

use App\Controllers\BaseController;

class DataGuru extends BaseController
{
   protected GuruModel $guruModel;

   public function __construct()
   {
      $this->guruModel = new GuruModel();
   }

   public function index()
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

   public function formTambahGuru($id)
   {
      $data = [
         'ctx' => 'guru',
         'title' => 'Tambah Data Guru'
      ];

      return view('admin/data/create/create-data-guru', $data);
   }

   public function formEditGuru($id)
   {
      $guru = $this->guruModel->getGuruById($id);

      $data = [
         'data' => $guru,
         'ctx' => 'guru',
         'title' => 'Edit Data Guru',
         'empty' => empty($result)
      ];

      return view('admin/data/edit/edit-data-guru', $data);
   }

   public function updateGuru()
   {
      $result = $this->request->getVar();

      dd($result);
   }

   public function delete($id)
   {
      $result = $this->guruModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/data-guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/data-guru');
   }
}
