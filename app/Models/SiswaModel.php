<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    // protected function initialize()
    // {
    //     $this->allowedFields[] = [];
    // }

    protected $table = 'tb_siswa';

    protected $primaryKey = 'id_siswa';

    public function cekSiswa(string $unique_code)
    {
        $this->join(
            'tb_kelas',
            'tb_kelas.id_kelas = tb_siswa.id_kelas',
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

    public function get_siswa_byKelas($id_kelas)
    {
        return $this->where(['id_kelas' => $id_kelas])->findAll();
    }
}
