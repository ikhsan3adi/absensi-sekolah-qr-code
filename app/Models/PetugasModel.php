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
         'is_superadmin',
         'id_guru',
         'active'
      ];
   }

   protected $table = 'users';

   protected $primaryKey = 'id';

   public function getAllPetugas()
   {
      return $this->select('users.*, tb_guru.nama_guru')
         ->join('tb_guru', 'users.id_guru = tb_guru.id_guru', 'left')
         ->findAll();
   }

   public function getPetugasById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function savePetugas($idPetugas, $email, $username, $passwordHash, $role, $id_guru = null, $active = 1)
   {
      return $this->save([
         $this->primaryKey => $idPetugas,
         'email' => $email,
         'username' => $username,
         'password_hash' => $passwordHash,
         'is_superadmin' => $role ?? '0',
         'id_guru' => $id_guru,
         'active' => $active
      ]);
   }

   //generate CSV object
   public function generateCSVObject($filePath)
   {
      $array = array();
      $fields = array();
      $txtName = uniqid() . '.txt';
      $i = 0;
      $handle = fopen($filePath, 'r');
      if ($handle) {
         while (($row = fgetcsv($handle)) !== false) {
            if (empty($fields)) {
               $fields = $row;
               // Remove BOM from the first element
               $bom = pack('H*', 'EFBBBF');
               $fields[0] = preg_replace("/^$bom/", '', $fields[0]);
               // Trim headers to remove extra whitespace
               $fields = array_map('trim', $fields);
               continue;
            }
            foreach ($row as $k => $value) {
               $array[$i][$fields[$k]] = $value;
            }
            $i++;
         }
         if (!feof($handle)) {
            return false;
         }
         fclose($handle);
         if (!empty($array)) {
            $txtFile = fopen(FCPATH . 'uploads/tmp/' . $txtName, 'w');
            fwrite($txtFile, serialize($array));
            fclose($txtFile);
            $obj = new \stdClass();
            $obj->numberOfItems = countItems($array);
            $obj->txtFileName = $txtName;
            @unlink($filePath);
            return $obj;
         }
      }
      return false;
   }

   //import csv item
   public function importCSVItem($txtFileName, $index)
   {
      $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
      if (!file_exists($filePath)) {
          return null;
      }
      $file = fopen($filePath, 'r');
      $content = fread($file, filesize($filePath));
      fclose($file);
      $array = @unserialize($content);
      if (!empty($array)) {
         $i = 1;
         foreach ($array as $item) {
            if ($i == $index) {
               $data = array();
               $data['username'] = getCSVInputValue($item, 'username');
               $data['email'] = getCSVInputValue($item, 'email');
               // Password needs hashing
               $password = getCSVInputValue($item, 'password');
               $data['password_hash'] = \Myth\Auth\Password::hash($password);
               
               $data['is_superadmin'] = getCSVInputValue($item, 'role', 'int'); // 1 or 0
               $idGuru = getCSVInputValue($item, 'id_guru', 'int');
               $data['id_guru'] = !empty($idGuru) ? $idGuru : null;
               
               // Check if email or username already exists
               if ($this->where('email', $data['email'])->orWhere('username', $data['username'])->countAllResults() > 0) {
                   return null; // Or handle error gracefully
               }

               $this->insert($data);
               return $data;
            }
            $i++;
         }
      }
   }
}
