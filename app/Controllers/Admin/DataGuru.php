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
}
