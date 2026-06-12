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
         'id_guru',
         'active'
      ];
   }

   protected $table = 'users';

   protected $primaryKey = 'id';

   /**
    * Map old form role integer values to Shield group names.
    */
   private const ROLE_GROUP_MAP = [
      '0' => 'scanner',
      '1' => 'superadmin',
      '2' => 'kepsek',
      '3' => 'admin',
   ];

   /**
    * Get all petugas (staff users) with group info.
    */
   public function getAllPetugas()
   {
      $db = \Config\Database::connect();
      $subquery = '(SELECT DISTINCT id_wali_kelas FROM ' . $db->protectIdentifiers('tb_kelas') . ' WHERE id_wali_kelas IS NOT NULL)';

      $users = $this->select('users.*, auth_identities.secret as email, tb_guru.nama_guru')
         ->select("CASE WHEN wk.id_wali_kelas IS NOT NULL THEN 1 ELSE 0 END as is_wali_kelas", false)
         ->join('auth_identities', 'users.id = auth_identities.user_id AND auth_identities.type = "email_password"', 'left')
         ->join('tb_guru', 'users.id_guru = tb_guru.id_guru', 'left')
         ->join($subquery . ' wk', 'users.id_guru = wk.id_wali_kelas', 'left')
         ->findAll();

      // Enrich with group information
      $groupModel = model(\CodeIgniter\Shield\Models\GroupModel::class);
      $userIds = array_column($users, 'id');
      $groupsByUser = $groupModel->getGroupsByUserIds($userIds);

      foreach ($users as &$user) {
         $user['groups'] = $groupsByUser[$user['id']] ?? [];
      }

      return $users;
   }

   /**
    * Get a single petugas by ID with group info.
    */
   public function getPetugasById($id)
   {
      $user = $this->select('users.*, auth_identities.secret as email')
         ->join('auth_identities', 'users.id = auth_identities.user_id AND auth_identities.type = "email_password"', 'left')
         ->where(['users.' . $this->primaryKey => $id])
         ->first();

      if ($user) {
         $groupModel = model(\CodeIgniter\Shield\Models\GroupModel::class);
         $userGroups = $groupModel->getForUser(new \CodeIgniter\Shield\Entities\User(['id' => $user['id']]));
         $user['groups'] = $userGroups;
      }

      return $user;
   }

   /**
    * Save (create or update) a petugas user.
    *
    * @param int|null  $idPetugas
    * @param string    $email
    * @param string    $username
    * @param string    $password
    * @param string    $role      Old integer role value ('0','1','2','3') or group name
    * @param int|null  $id_guru
    * @param int       $active
    * @return bool
    */
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

      $user->id_guru = $id_guru;
      $user->active  = $active;

      // Map role value to Shield group name
      $targetGroup = self::ROLE_GROUP_MAP[$role] ?? $role;
      // Validate it's a known group, fallback to 'admin'
      $validGroups = array_keys(setting('AuthGroups.groups'));
      if (!in_array($targetGroup, $validGroups, true)) {
         $targetGroup = 'admin';
      }

      if ($users->save($user)) {
         if (!$idPetugas) {
            $newId = $users->getInsertID();
            $savedUser = $users->find($newId);
         } else {
            $savedUser = $users->find($idPetugas);
         }

         // Sync groups: primary role group + 'guru' if has id_guru
         $groupsToSync = [$targetGroup];
         if (!empty($id_guru)) {
            $groupsToSync[] = 'guru';
         }

         $savedUser->syncGroups(...$groupsToSync);

         return true;
      }

      return false;
   }

   /**
    * Generate CSV object from uploaded file.
    */
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

   /**
    * Import a single CSV item and create a user.
    */
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

               $roleValue = getCSVInputValue($item, 'role', 'int'); // 1 or 0 (old format)
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
               $existing = $users->findByCredentials(['email' => $data['email']]);
               if (!$existing) {
                  $existing = $users->where('username', $data['username'])->first();
               }

               if (!empty($existing)) {
                  return null;
               }

               // Create user using Shield
               $user = new \CodeIgniter\Shield\Entities\User([
                  'username' => $data['username'],
                  'email'    => $data['email'],
                  'password' => $password,
               ]);
               $user->id_guru = $data['id_guru'];
               $user->active  = $data['active'];

               if ($users->save($user)) {
                  $newId = $users->getInsertID();
                  $savedUser = $users->find($newId);

                  // Map role to group
                  $targetGroup = self::ROLE_GROUP_MAP[(string) $roleValue] ?? 'admin';
                  
                  $groupsToSync = [$targetGroup];
                  if (!empty($data['id_guru'])) {
                     $groupsToSync[] = 'guru';
                  }
                  $savedUser->syncGroups(...$groupsToSync);
                  
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
