<?php

namespace App\Controllers\Admin;

use App\Models\KelasModel;

use App\Models\GuruModel;
use App\Models\SiswaModel;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;

class LihatDataAbsen extends BaseController
{
    protected KelasModel $kelasModel;

    protected SiswaModel $siswaModel;
    protected GuruModel $guruModel;

    protected string $currentDate;

    public function __construct()
    {
        $this->currentDate = Time::today()->toDateString();

        $this->siswaModel = new SiswaModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
    }

    public function data_kelas()
    {
        $result = $this->kelasModel->all_kelas();

        $data = [
            'title' => 'Data Absen Siswa',
            'ctx' => 'absen-siswa',
            'data' => $result
        ];

        return view('admin/absen/data-kelas', $data);
    }

    public function ambil_siswa()
    {
        // ambil variabel POST
        $id_kelas = $this->request->getVar('id_kelas');

        $result = $this->siswaModel->get_siswa_byKelas($id_kelas);

        $data = [
            'data' => $result
        ];

        return view('admin/absen/absen-siswa', $data);
    }
}
