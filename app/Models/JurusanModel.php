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

    //generate CSV object
    public function generateCSVObject($filePath)
    {
        $array = array();
        $fields = array();
        $txtName = uniqid() . '.txt';
        $i = 0;
        $handle = fopen($filePath, 'r');
        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    // Remove BOM from the first element if present
                    if (isset($fields[0])) {
                        $fields[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $fields[0]);
                    }
                    // Trim all fields
                    $fields = array_map('trim', $fields);
                    continue;
                }
                foreach ($row as $k => $value) {
                    if (isset($fields[$k])) {
                        $array[$i][$fields[$k]] = trim($value);
                    }
                }
                $i++;
            }
            if (!feof($handle)) {
                return false;
            }
            fclose($handle);
            if (!empty($array)) {
                $txtFile = fopen(FCPATH . 'uploads/tmp/' . $txtName, 'w');
                fwrite($txtFile, serialize($array));
                fclose($txtFile);
                $obj = new \stdClass();
                $obj->numberOfItems = countItems($array);
                $obj->txtFileName = $txtName;
                @unlink($filePath);
                return $obj;
            }
        }
        return false;
    }

    //import csv item
    public function importCSVItem($txtFileName, $index)
    {
        $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
        $file = fopen($filePath, 'r');
        $content = fread($file, filesize($filePath));
        $array = @unserialize($content);
        if (!empty($array)) {
            $i = 1;
            foreach ($array as $item) {
                if ($i == $index) {
                    $jurusan = getCSVInputValue($item, 'jurusan');

                    if (!empty($jurusan)) {
                        $data = [
                            'jurusan' => $jurusan
                        ];
                        // Check for duplicate
                        $exists = $this->builder->where('jurusan', $jurusan)->countAllResults();
                        if ($exists > 0) {
                            return ['status' => 'duplicate', 'data' => $data];
                        }
                        $this->builder->insert($data);
                        return ['status' => 'success', 'data' => $data];
                    }
                }
                $i++;
            }
        }
    }
}
