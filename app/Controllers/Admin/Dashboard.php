<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;
use App\Models\SiswaModel;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
   protected SiswaModel $siswaModel;
   protected GuruModel $guruModel;

   public function index()
   {
      $data = [
         'title' => 'Dashboard'
      ];

      return view('admin/dashboard', $data);
   }
}
