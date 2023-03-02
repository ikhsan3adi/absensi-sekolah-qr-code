<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    // protected function initialize()
    // {
    //     $this->allowedFields[] = [];
    // }

    protected $table = 'tb_kelas';

    protected $primaryKey = 'id_kelas';

    public function all_kelas()
    {
        return $this->findAll();
    }
}
