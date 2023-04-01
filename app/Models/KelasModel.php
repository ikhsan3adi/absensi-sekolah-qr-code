<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields[] = ['kelas', 'jurusan'];
   }

   protected $table = 'tb_kelas';

   protected $primaryKey = 'id_kelas';

   public function getAllKelas()
   {
      return $this->findAll();
   }

   public function tambahKelas($kelas, $jurusan)
   {
      return $this->db->table($this->table)->insert([
         'kelas' => $kelas,
         'jurusan' => $jurusan
      ]);
   }
}
