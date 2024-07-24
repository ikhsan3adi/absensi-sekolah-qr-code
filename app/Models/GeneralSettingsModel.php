<?php

namespace App\Models;

use CodeIgniter\Model;

class GeneralSettingsModel extends BaseModel
{
   protected $builder;

   public function __construct()
   {
      parent::__construct();
      $this->builder = $this->db->table('general_settings');
   }

   //input values
   public function inputValues()
   {
      return [
         'school_name' => inputPost('school_name'),
         'school_year' => inputPost('school_year'),
         'copyright' => inputPost('copyright'),
      ];
   }

   public function updateSettings()
   {
      $data = $this->inputValues();

      $uploadModel = new UploadModel();
      $logoPath = $uploadModel->uploadLogo('logo');

      if (!empty($logoPath) && !empty($logoPath['path'])) {
         $oldLogo = $this->generalSettings->logo;
         $data['logo'] = $logoPath['path'];
         if (file_exists($oldLogo)) {
            @unlink($oldLogo);
        }
      }

      return $this->builder->where('id', 1)->update($data);
   }
}
