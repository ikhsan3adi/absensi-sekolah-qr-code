<?php

namespace App\Models;

class PresensiGuruModel extends PresensiBaseModel
{
    // protected function initialize()
    // {
    //     $this->allowedFields[] = [];
    // }

    protected $table = 'tb_presensi_guru';

    protected $primaryKey = 'id_presensi';
}
