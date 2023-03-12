<?php

namespace App\Models;

use CodeIgniter\Model;

class KehadiranModel extends Model
{
   // protected function initialize()
   // {
   //     $this->allowedFields[] = [];
   // }

   protected $table = 'tb_kehadiran';

   protected $primaryKey = 'id_kehadiran';

   public function getAllKehadiran()
   {
      return $this->findAll();
   }
}
