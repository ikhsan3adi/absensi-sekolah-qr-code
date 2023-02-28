<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;
use App\Models\SiswaModel;

use App\Controllers\BaseController;

class LihatData extends BaseController
{
    protected SiswaModel $siswaModel;
    protected GuruModel $guruModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->guruModel = new GuruModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'ctx' => ''
        ];

        return view('admin/dashboard', $data);
    }

    public function lihat_data_siswa()
    {
        $result = $this->siswaModel->allSiswaWithKelas();

        $data = [
            'title' => 'Data Siswa',
            'ctx' => 'siswa',
            'data' => $result
        ];

        return view('admin/data-siswa', $data);
    }

    public function lihat_data_guru()
    {
        $result = $this->guruModel->findAll();

        $data = [
            'title' => 'Data Guru',
            'ctx' => 'guru',
            'data' => $result
        ];

        return view('admin/data-guru', $data);
    }
}
