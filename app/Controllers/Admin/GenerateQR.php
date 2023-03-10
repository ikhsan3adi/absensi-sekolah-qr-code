<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class GenerateQR extends BaseController
{
   public function index()
   {
      $data = [
         'title' => 'Generate QR Code',
         'ctx' => 'qr'
      ];

      return view('admin/generate-qr/generate-qr', $data);
   }
}
