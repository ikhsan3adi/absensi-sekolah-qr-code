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
         'unique_code'
      ];
   }

   protected $table = 'tb_siswa';

   protected $primaryKey = 'id_siswa';

   public function cekSiswa(string $unique_code)
   {
      $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_jurusan.id = tb_kelas.id_jurusan',
         'LEFT'
      );
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getSiswaById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function getAllSiswaWithKelas($kelas = null, $jurusan = null)
   {
      $query = $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_kelas.id_jurusan = tb_jurusan.id',
         'LEFT'
      );

      if (!empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas, 'jurusan' => $jurusan]);
      } else if (empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['jurusan' => $jurusan]);
      } else if (!empty($kelas) && empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas]);
      } else {
         $query = $this;
      }

      return $query->orderBy('nama_siswa')->findAll();
   }

   public function getSiswaByKelas($id_kelas)
   {
      return $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )
         ->join('tb_jurusan', 'tb_kelas.id_jurusan = tb_jurusan.id', 'left')
         ->where(['tb_siswa.id_kelas' => $id_kelas])->findAll();
   }

   public function saveSiswa($idSiswa, $nis, $namaSiswa, $idKelas, $jenisKelamin, $noHp)
   {
      return $this->save([
         $this->primaryKey => $idSiswa,
         'nis' => $nis,
         'nama_siswa' => $namaSiswa,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'unique_code' => sha1($namaSiswa . md5($nis . $noHp . $namaSiswa)) . substr(sha1($nis . rand(0, 100)), 0, 24)
      ]);
   }
}
