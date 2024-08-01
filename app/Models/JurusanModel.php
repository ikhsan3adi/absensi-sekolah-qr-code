<?php

namespace App\Models;

class JurusanModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('tb_jurusan');
    }

    //input values
   public function inputValues()
   {
      return [
         'jurusan' => inputPost('jurusan'),
      ];
   }

   public function addJurusan()
   {
      $data = $this->inputValues();
      return $this->builder->insert($data);
   }

   public function editJurusan($id)
   {
      $jurusan = $this->getJurusan($id);
      if (!empty($jurusan)) {
         $data = $this->inputValues();
         return $this->builder->where('id', $jurusan->id)->update($data);
      }
      return false;
   }

    public function getDataJurusan()
    {
        return $this->builder->orderBy('id')->get()->getResult('array');
    }

    public function getJurusan($id)
    {
        return $this->builder->where('id', cleanNumber($id))->get()->getRow();
    }


    public function deleteJurusan($id)
   {
       $jurusan = $this->getJurusan($id);
       if (!empty($jurusan)) {
           return $this->builder->where('id', $jurusan->id)->delete();
       }
       return false;
   }
}
