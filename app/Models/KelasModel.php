<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
   protected $DBGroup          = 'default';
   protected $useAutoIncrement = true;
   protected $returnType       = 'array';
   protected $useSoftDeletes   = true;
   protected $protectFields    = true;
   protected $allowedFields    = ['kelas', 'id_jurusan'];

   protected $table = 'tb_kelas';

   protected $primaryKey = 'id_kelas';

   public function getAllKelas()
   {
      return $this->join('tb_jurusan', 'tb_kelas.id_jurusan = tb_jurusan.id', 'left')->findAll();
   }

   public function tambahKelas($kelas, $idJurusan)
   {
      return $this->db->table($this->table)->insert([
         'kelas' => $kelas,
         'id_jurusan' => $idJurusan
      ]);
   }
}
