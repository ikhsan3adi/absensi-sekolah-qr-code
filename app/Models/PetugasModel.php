<?php

namespace App\Models;

use CodeIgniter\Model;

class PetugasModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields = [
         'email',
         'username',
         'superadmin'
      ];
   }

   protected $table = 'tb_petugas';

   protected $primaryKey = 'id';

   public function getAllPetugas()
   {
      return $this->findAll();
   }

   public function getPetugasById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function savePetugas($idPetugas, $email, $username, $role)
   {
      return $this->save([
         $this->primaryKey => $idPetugas,
         'email' => $email,
         'username' => $username,
         'superadmin' => $role ?? '0',
      ]);
   }
}
