<?php

namespace App\Models;

use CodeIgniter\Model;

class PetugasModel extends Model
{
    // protected function initialize()
    // {
    //     $this->allowedFields[] = [];
    // }

    protected $table = 'tb_petugas';

    protected $primaryKey = 'id_petugas';

    public function cekPetugas()
    {
    }
}
