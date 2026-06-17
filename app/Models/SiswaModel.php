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

   public function getAllSiswaWithKelas($kelas = null, $jurusan = null, $index = null)
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

      if (!empty($kelas)) {
         $query->where('tb_kelas.tingkat', $kelas);
      }
      if (!empty($jurusan)) {
         $query->where('tb_jurusan.jurusan', $jurusan);
      }
      if (!empty($index)) {
         $query->where('tb_kelas.index_kelas', $index);
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

   public function getSiswaCountByKelas($kelasId = null)
   {
      if (empty($kelasId)) {
         return $this->countAllResults();
      }

      $tree = array();
      $kelasId = cleanNumber($kelasId);
      if (!empty($kelasId)) {
         array_push($tree, $kelasId);
      }

      $kelasIds = $tree;
      if (countItems($kelasIds) < 1) {
         return 0;
      }

      return $this->whereIn('tb_siswa.id_kelas', $kelasIds, false)->countAllResults();
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
               $data['nis'] = getCSVInputValue($item, 'nis');
               $data['nama_siswa'] = getCSVInputValue($item, 'nama_siswa');
               $data['id_kelas'] = getCSVInputValue($item, 'id_kelas');
               $data['no_hp'] = getCSVInputValue($item, 'no_hp');
               $data['unique_code'] = generateToken();

               if (empty($data['nis']) || empty($data['nama_siswa']) || empty($data['id_kelas'])) {
                  return ['status' => 'error', 'message' => 'NIS, Nama, dan Kelas wajib diisi'];
               }

               $kelasExists = $this->db->table('tb_kelas')
                  ->where('id_kelas', $data['id_kelas'])
                  ->countAllResults();
               if (!$kelasExists) {
                  return ['status' => 'error', 'message' => 'ID Kelas ' . $data['id_kelas'] . ' tidak ditemukan'];
               }

               $nisExists = $this->where('nis', $data['nis'])->countAllResults();
               if ($nisExists > 0) {
                  return ['status' => 'duplicate', 'message' => 'NIS ' . $data['nis'] . ' sudah terdaftar'];
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
