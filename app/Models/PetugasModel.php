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
      return $this->select('users.*, auth_identities.name as email, tb_guru.nama_guru')
         ->join('auth_identities', 'users.id = auth_identities.user_id AND auth_identities.type = "email_password"', 'left')
         ->join('tb_guru', 'users.id_guru = tb_guru.id_guru', 'left')
         ->findAll();
   }

   public function getPetugasById($id)
   {
      return $this->select('users.*, auth_identities.name as email')
         ->join('auth_identities', 'users.id = auth_identities.user_id AND auth_identities.type = "email_password"', 'left')
         ->where(['users.' . $this->primaryKey => $id])
         ->first();
   }

   public function savePetugas($idPetugas, $email, $username, $password, $role, $id_guru = null, $active = 1)
   {
      $users = auth()->getProvider();

      if ($idPetugas) {
         $user = $users->find($idPetugas);
      } else {
         $user = new \CodeIgniter\Shield\Entities\User();
      }

      $user->fill([
         'username' => $username,
         'email'    => $email,
      ]);

      if (!empty($password)) {
         $user->password = $password;
      }

      $user->is_superadmin = $role ?? '0';
      $user->id_guru       = $id_guru;
      $user->active        = $active;

      if ($users->save($user)) {
         if (!$idPetugas) {
            $newId = $users->getInsertID();
            $savedUser = $users->find($newId);
            if ($role == '1') {
               $savedUser->addGroup('superadmin');
            } else {
               $savedUser->addGroup('admin');
            }
         }
         return true;
      }

      return false;
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

            fwrite($txtFile, json_encode($array));
            fclose($txtFile);

            $obj = new \stdClass();
            $obj->numberOfItems = countItems($array);
            $obj->txtFileName = $txtName;

            if (file_exists($filePath) && !unlink($filePath)) {
               if (function_exists('log_message')) {
                  log_message('error', 'Failed to delete CSV file: ' . $filePath);
               }
            }

            return $obj;
         }
      }
      return false;
   }

   //import csv item
   public function importCSVItem($txtFileName, $index)
   {
      // Validate txtFileName to prevent path traversal and disallow unsafe characters
      if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $txtFileName) || strpos($txtFileName, '..') !== false) {
         return null;
      }

      $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
      if (!file_exists($filePath)) {
         return null;
      }
      $file = fopen($filePath, 'r');
      $content = fread($file, filesize($filePath));
      fclose($file);
      $array = json_decode($content, true);
      if (!empty($array)) {
         $i = 1;
         foreach ($array as $item) {
            if ($i == $index) {
               $data = array();
               $data['username'] = getCSVInputValue($item, 'username');
               $data['email'] = getCSVInputValue($item, 'email');

               // Validate input data
               if (empty($data['username']) || strlen($data['username']) < 3) {
                  return null; // Username too short or empty
               }
               if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                  return null; // Invalid email
               }

               // Password needs hashing
               $password = getCSVInputValue($item, 'password');
               if (empty($password) || strlen($password) < 6) {
                  return null; // Password too short or empty
               }

               $data['is_superadmin'] = getCSVInputValue($item, 'role', 'int'); // 1 or 0
               $idGuru = getCSVInputValue($item, 'id_guru', 'int');
               
               // Validate id_guru foreign key reference
               if (!empty($idGuru)) {
                  $guruModel = new GuruModel();
                  $guru = $guruModel->find($idGuru);
                  if ($guru === null) {
                     return null; // Guru does not exist
                  }
                  $data['id_guru'] = $idGuru;
               } else {
                  $data['id_guru'] = null;
               }

               $data['active'] = 1;

               // Check if email or username already exists using Shield
               $users = auth()->getProvider();
               $existing = $users->where('email', $data['email'])->orWhere('username', $data['username'])->first();

               if (!empty($existing)) {
                  return null; // Or handle error gracefully
               }

               // Create user using Shield
               $user = new \CodeIgniter\Shield\Entities\User([
                  'username' => $data['username'],
                  'email'    => $data['email'],
                  'password' => $password,
               ]);
               $user->is_superadmin = $data['is_superadmin'] ?? '0';
               $user->id_guru       = $data['id_guru'];
               $user->active        = $data['active'];

               if ($users->save($user)) {
                  $newId = $users->getInsertID();
                  $savedUser = $users->find($newId);
                  if ($user->is_superadmin == '1') {
                     $savedUser->addGroup('superadmin');
                  } else {
                     $savedUser->addGroup('admin');
                  }
                  
                  $responseData = $data;
                  return $responseData;
               }

               return null;
            }
            $i++;
         }
      }
   }
}
