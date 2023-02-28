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

    public function cek_siswa(string $unique_code)
    {
        $this->join(
            'tb_kelas',
            'tb_kelas.id_kelas = tb_siswa.id_kelas',
            'LEFT'
        );
        return $this->where(['unique_code' => $unique_code])->first();
    }

    public function allSiswaWithKelas()
    {
        $this->join(
            'tb_kelas',
            'tb_kelas.id_kelas = tb_siswa.id_kelas',
            'LEFT'
        );
        return $this->findAll();
    }
}
