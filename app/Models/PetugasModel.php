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
         'password_hash',
         'is_superadmin'
      ];
   }

   protected $table = 'users';

   protected $primaryKey = 'id';

   public function getAllPetugas()
   {
      return $this->findAll();
   }

   public function getPetugasById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function savePetugas($idPetugas, $email, $username, $passwordHash, $role)
   {
      return $this->save([
         $this->primaryKey => $idPetugas,
         'email' => $email,
         'username' => $username,
         'password_hash' => $passwordHash,
         'is_superadmin' => $role ?? '0',
      ]);
   }
}
