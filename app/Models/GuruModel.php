<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    // protected function initialize()
    // {
    //     $this->allowedFields[] = [];
    // }

    protected $table = 'tb_guru';

    protected $primaryKey = 'id_guru';

    public function cek_guru(string $unique_code)
    {
        return $this->where(['unique_code' => $unique_code])->first();
    }

    public function allGuru()
    {
        return $this->findAll();
    }
}
