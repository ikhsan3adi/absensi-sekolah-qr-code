<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
   protected $allowedFields = [
      'nuptk',
      'nama_guru',
      'jenis_kelamin',
      'alamat',
      'no_hp',
      'unique_code',
      'rfid_code'
   ];

   protected $table = 'tb_guru';

   protected $primaryKey = 'id_guru';

   public function cekGuru(string $unique_code)
   {
      return $this->where(['unique_code' => $unique_code])
         ->orWhere(['rfid_code' => $unique_code])
         ->first();
   }

   public function getAllGuru()
   {
      return $this->orderBy('nama_guru')->findAll();
   }

   public function getGuruById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function createGuru($nuptk, $nama, $jenisKelamin, $alamat, $noHp, $rfid = null)
   {
      return $this->save([
         'nuptk' => $nuptk,
         'nama_guru' => $nama,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'unique_code' => sha1($nama . md5($nuptk . $nama . $noHp)) . substr(sha1($nuptk . rand(0, 100)), 0, 24),
         'rfid_code' => $rfid
      ]);
   }

   public function updateGuru($id, $nuptk, $nama, $jenisKelamin, $alamat, $noHp, $rfid = null)
   {
      return $this->save([
         $this->primaryKey => $id,
         'nuptk' => $nuptk,
         'nama_guru' => $nama,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'rfid_code' => $rfid,
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
               // Remove BOM from the first element if present
                if (isset($fields[0])) {
                   $fields[0] = preg_replace('/^\xEF\xBB\xBF/', '', $fields[0]);
                }
               // Trim all fields
               $fields = array_map('trim', $fields);
               continue;
            }
            foreach ($row as $k => $value) {
               if (isset($fields[$k])) {
                  $array[$i][$fields[$k]] = trim($value);
               }
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
      $file = fopen($filePath, 'r');
      $content = fread($file, filesize($filePath));
      $array = @unserialize($content);
      if (!empty($array)) {
         $i = 1;
         foreach ($array as $item) {
            if ($i == $index) {
               $data = array();
               $data['nuptk'] = getCSVInputValue($item, 'nuptk');
               $data['nama_guru'] = getCSVInputValue($item, 'nama_guru');
               $data['alamat'] = getCSVInputValue($item, 'alamat');
               $data['no_hp'] = getCSVInputValue($item, 'no_hp');
               $data['unique_code'] = sha1($data['nama_guru'] . md5($data['nuptk'] . $data['nama_guru'] . $data['no_hp'])) . substr(sha1($data['nuptk'] . rand(0, 100)), 0, 24);

               if (empty($data['nuptk']) || empty($data['nama_guru'])) {
                  return ['status' => 'error', 'message' => 'NUPTK dan Nama Guru wajib diisi'];
               }

               $nuptkExists = $this->where('nuptk', $data['nuptk'])->countAllResults();
               if ($nuptkExists > 0) {
                  return ['status' => 'duplicate', 'message' => 'NUPTK ' . $data['nuptk'] . ' sudah terdaftar'];
               }

               $jk = strtolower(getCSVInputValue($item, 'jenis_kelamin'));
               if (in_array($jk, ['l', 'laki-laki', 'laki laki', 'laki'])) {
                  $data['jenis_kelamin'] = 'Laki-laki';
               } elseif (in_array($jk, ['p', 'perempuan', 'wanita'])) {
                  $data['jenis_kelamin'] = 'Perempuan';
               } else {
                  return ['status' => 'error', 'message' => 'Jenis kelamin tidak dikenal: ' . $jk];
               }

               if ($this->insert($data)) {
                  return $data;
               }
               return ['status' => 'error', 'message' => 'Gagal menyimpan data'];
            }
            $i++;
         }
      }
      return ['status' => 'error', 'message' => 'Data CSV tidak ditemukan'];
   }
}
