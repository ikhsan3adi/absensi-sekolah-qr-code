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
      'unique_code'
   ];

   protected $table = 'tb_guru';

   protected $primaryKey = 'id_guru';

   public function cekGuru(string $unique_code)
   {
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getAllGuru()
   {
      return $this->orderBy('nama_guru')->findAll();
   }

   public function getGuruById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function createGuru($nuptk, $nama, $jenisKelamin, $alamat, $noHp)
   {
      return $this->save([
         'nuptk' => $nuptk,
         'nama_guru' => $nama,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'unique_code' => sha1($nama . md5($nuptk . $nama . $noHp)) . substr(sha1($nuptk . rand(0, 100)), 0, 24)
      ]);
   }

   public function updateGuru($id, $nuptk, $nama, $jenisKelamin, $alamat, $noHp)
   {
      return $this->save([
         $this->primaryKey => $id,
         'nuptk' => $nuptk,
         'nama_guru' => $nama,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
      ]);
   }
}
