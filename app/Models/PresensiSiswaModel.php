<?php

namespace App\Models;

class PresensiSiswaModel extends PresensiBaseModel
{
    // protected function initialize()
    // {
    //     $this->allowedFields[] = [];
    // }

    protected $table = 'tb_presensi_siswa';

    protected $primaryKey = 'id_presensi';
}
