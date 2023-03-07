<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;
use App\Models\SiswaModel;

use App\Controllers\BaseController;
use App\Models\KelasModel;

class LihatData extends BaseController
{
    protected SiswaModel $siswaModel;
    protected GuruModel $guruModel;

    protected KelasModel $kelasModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->guruModel = new GuruModel();

        $this->kelasModel = new KelasModel();
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


        $data = [
            'title' => 'Data Siswa',
            'ctx' => 'siswa',
            'data' => $this->siswaModel->allSiswaWithKelas(),
            'kelas' => $this->kelasModel->all_kelas()
        ];

        return view('admin/data/data-siswa', $data);
    }

    public function ambil_data_siswa()
    {
        $kelas = $this->request->getVar('kelas') ?? null;
        $jurusan = $this->request->getVar('jurusan') ?? null;

        $data = ['data' => $this->siswaModel->allSiswaWithKelas($kelas, $jurusan)];

        return view('admin/data/list-data-siswa', $data);
    }

    public function lihat_data_guru()
    {
        $result = $this->guruModel->findAll();

        $data = [
            'title' => 'Data Guru',
            'ctx' => 'guru',
            'data' => $result
        ];

        return view('admin/data/data-guru', $data);
    }
}
