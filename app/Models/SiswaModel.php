<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields = [
         'nis',
         'nama_siswa',
         'id_kelas',
         'jenis_kelamin',
         'no_hp',
         'unique_code',
         'rfid_code'
      ];
   }

   protected $table = 'tb_siswa';

   protected $primaryKey = 'id_siswa';

   public function cekSiswa(string $unique_code)
   {
      $this->select('tb_siswa.*, tb_kelas.tingkat, tb_kelas.index_kelas, tb_jurusan.jurusan, CONCAT(tb_kelas.tingkat, " ", tb_jurusan.jurusan, " ", tb_kelas.index_kelas) as kelas')
         ->join(
            'tb_kelas',
            'tb_kelas.id_kelas = tb_siswa.id_kelas',
            'LEFT'
         )->join(
            'tb_jurusan',
            'tb_jurusan.id = tb_kelas.id_jurusan',
            'LEFT'
         );
      return $this->where(['unique_code' => $unique_code])
         ->orWhere(['rfid_code' => $unique_code])
         ->first();
   }

   public function getSiswaById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function getAllSiswaWithKelas($kelas = null, $jurusan = null)
   {
      $query = $this->select('tb_siswa.*, tb_kelas.tingkat, tb_kelas.index_kelas, tb_jurusan.jurusan, CONCAT(tb_kelas.tingkat, " ", tb_jurusan.jurusan, " ", tb_kelas.index_kelas) as kelas')
         ->join(
            'tb_kelas',
            'tb_kelas.id_kelas = tb_siswa.id_kelas',
            'LEFT'
         )->join(
            'tb_jurusan',
            'tb_kelas.id_jurusan = tb_jurusan.id',
            'LEFT'
         );

      if (!empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['tb_jurusan.jurusan' => $jurusan, 'tb_kelas.tingkat' => $kelas]);
      } else if (empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['tb_jurusan.jurusan' => $jurusan]);
      } else if (!empty($kelas) && empty($jurusan)) {
         $query = $this->where(['tb_kelas.tingkat' => $kelas]);
      } else {
         $query = $this;
      }

      return $query->orderBy('nama_siswa')->findAll();
   }

   public function getSiswaByKelas($id_kelas)
   {
      return $this->select('tb_siswa.*, tb_kelas.tingkat, tb_kelas.index_kelas, tb_jurusan.jurusan, CONCAT(tb_kelas.tingkat, " ", tb_jurusan.jurusan, " ", tb_kelas.index_kelas) as kelas')
         ->join(
            'tb_kelas',
            'tb_kelas.id_kelas = tb_siswa.id_kelas',
            'LEFT'
         )
         ->join('tb_jurusan', 'tb_kelas.id_jurusan = tb_jurusan.id', 'left')
         ->where(['tb_siswa.id_kelas' => $id_kelas])
         ->orderBy('nama_siswa')
         ->findAll();
   }

   public function createSiswa($nis, $nama, $idKelas, $jenisKelamin, $noHp, $rfid = null)
   {
      return $this->save([
         'nis' => $nis,
         'nama_siswa' => $nama,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'unique_code' => generateToken(),
         'rfid_code' => $rfid
      ]);
   }

   public function updateSiswa($id, $nis, $nama, $idKelas, $jenisKelamin, $noHp, $rfid = null)
   {
      return $this->save([
         $this->primaryKey => $id,
         'nis' => $nis,
         'nama_siswa' => $nama,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'rfid_code' => $rfid
      ]);
   }

   public function getSiswaCountByKelas($kelasId)
   {
      $tree = array();
      $kelasId = cleanNumber($kelasId);
      if (!empty($kelasId)) {
         array_push($tree, $kelasId);
      }

      $kelasIds = $tree;
      if (countItems($kelasIds) < 1) {
         return array();
      }

      return $this->whereIn('tb_siswa.id_kelas', $kelasIds, false)->countAllResults();
   }

   //generate CSV object
   public function generateCSVObject($filePath)
   {
      if (!file_exists($filePath)) {
         log_message('error', 'CSV file not found: ' . $filePath);
         return false;
      }
      
      $array = array();
      $fields = array();
      $txtName = uniqid() . '.txt';
      $i = 0;
      $handle = @fopen($filePath, 'r');
      
      if (!$handle) {
         log_message('error', 'Failed to open CSV file: ' . $filePath);
         return false;
      }
      
      while (($row = fgetcsv($handle)) !== false) {
         if (empty($fields)) {
            $fields = $row;
            // Validate required headers
            $requiredHeaders = ['nis', 'nama_siswa', 'id_kelas', 'jenis_kelamin', 'no_hp'];
            foreach ($requiredHeaders as $header) {
               if (!in_array($header, $fields)) {
                  log_message('error', 'CSV missing required header: ' . $header);
                  fclose($handle);
                  @unlink($filePath);
                  return false;
               }
            }
            continue;
         }
         
         // Skip empty rows
         if (empty(array_filter($row))) {
            continue;
         }
         
         foreach ($row as $k => $value) {
            if (isset($fields[$k])) {
               $array[$i][$fields[$k]] = $value;
            }
         }
         $i++;
      }
      
      if (!feof($handle)) {
         log_message('error', 'Error reading CSV file, not reached EOF');
         fclose($handle);
         @unlink($filePath);
         return false;
      }
      
      fclose($handle);
      
      if (empty($array)) {
         log_message('error', 'CSV file contains no data rows');
         @unlink($filePath);
         return false;
      }
      
      $txtFile = @fopen(FCPATH . 'uploads/tmp/' . $txtName, 'w');
      if (!$txtFile) {
         log_message('error', 'Failed to create temp file: ' . FCPATH . 'uploads/tmp/' . $txtName);
         @unlink($filePath);
         return false;
      }
      
      fwrite($txtFile, serialize($array));
      fclose($txtFile);
      
      $obj = new \stdClass();
      $obj->numberOfItems = countItems($array);
      $obj->txtFileName = $txtName;
      @unlink($filePath);
      
      return $obj;
   }

   //import csv item
   public function importCSVItem($txtFileName, $index)
   {
      $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
      
      if (!file_exists($filePath)) {
         log_message('error', 'Temp file not found: ' . $filePath);
         return false;
      }
      
      $file = @fopen($filePath, 'r');
      if (!$file) {
         log_message('error', 'Failed to open temp file: ' . $filePath);
         return false;
      }
      
      $content = fread($file, filesize($filePath));
      fclose($file);
      
      $array = @unserialize($content);
      if (empty($array)) {
         log_message('error', 'Failed to unserialize temp file content');
         return false;
      }
      
      $i = 1;
      foreach ($array as $item) {
         if ($i == $index) {
            $data = array();
            $data['nis'] = getCSVInputValue($item, 'nis', 'int');
            $data['nama_siswa'] = getCSVInputValue($item, 'nama_siswa');
            $data['id_kelas'] = getCSVInputValue($item, 'id_kelas', 'int');
            $data['jenis_kelamin'] = getCSVInputValue($item, 'jenis_kelamin');
            $data['no_hp'] = getCSVInputValue($item, 'no_hp');
            $data['unique_code'] = generateToken();
            
            // Validate required fields
            if (empty($data['nis']) || empty($data['nama_siswa']) || empty($data['id_kelas'])) {
               log_message('error', 'CSV item missing required fields at index: ' . $index);
               return false;
            }
            
            try {
               $this->insert($data);
               return $data;
            } catch (\Exception $e) {
               log_message('error', 'Failed to insert CSV item at index ' . $index . ': ' . $e->getMessage());
               return false;
            }
         }
         $i++;
      }
      
      log_message('error', 'CSV item index not found: ' . $index);
      return false;
   }

   public function getSiswa($id)
   {
      return $this->where('id_siswa', cleanNumber($id))->get()->getRow();
   }

   //delete post
   public function deleteSiswa($id)
   {
      $siswa = $this->getSiswa($id);
      if (!empty($siswa)) {
         //delete siswa
         return $this->where('id_siswa', $siswa->id_siswa)->delete();
      }
      return false;
   }

   //delete multi post
   public function deleteMultiSelected($siswaIds)
   {
      if (!empty($siswaIds)) {
         foreach ($siswaIds as $id) {
            $this->deleteSiswa($id);
         }
      }
   }
}
