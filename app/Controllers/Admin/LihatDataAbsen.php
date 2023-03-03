<?php

namespace App\Controllers\Admin;

use App\Models\KelasModel;

use App\Models\GuruModel;
use App\Models\SiswaModel;

use App\Controllers\BaseController;
use App\Models\LihatAbsensiSiswa;
use CodeIgniter\I18n\Time;

class LihatDataAbsen extends BaseController
{
    protected KelasModel $kelasModel;

    protected SiswaModel $siswaModel;
    protected GuruModel $guruModel;

    protected LihatAbsensiSiswa $absenSiswa;

    protected string $currentDate;

    public function __construct()
    {
        $this->currentDate = Time::today()->toDateString();

        $this->siswaModel = new SiswaModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();

        $this->absenSiswa = new LihatAbsensiSiswa();
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
        $kelas = $this->request->getVar('kelas');
        $id_kelas = $this->request->getVar('id_kelas');
        $tanggal = $this->request->getVar('tanggal');

        $result = $this->absenSiswa->get_presensi_byKelasTanggal($id_kelas, $tanggal);

        $data = [
            'kelas' => $kelas,
            'data' => $result
        ];

        return view('admin/absen/absen-siswa', $data);
    }
}
