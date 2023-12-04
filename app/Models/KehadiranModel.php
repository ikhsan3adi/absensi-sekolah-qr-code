<?php

namespace App\Models;

use CodeIgniter\Model;

class KehadiranModel extends Model
{
   protected $table = 'tb_kehadiran';

   protected $primaryKey = 'id_kehadiran';

   public function getAllKehadiran()
   {
      return $this->findAll();
   }
}
