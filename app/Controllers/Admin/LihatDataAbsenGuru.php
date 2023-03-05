<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiGuruModel;
use CodeIgniter\I18n\Time;

class LihatDataAbsenGuru extends BaseController
{
    protected GuruModel $guruModel;

    protected PresensiGuruModel $presensiGuruModel;

    protected KehadiranModel $kehadiranModel;

    public function __construct()
    {
        $this->currentDate = Time::today()->toDateString();

        $this->guruModel = new GuruModel();

        $this->presensiGuruModel = new PresensiGuruModel();

        $this->kehadiranModel = new KehadiranModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Absen Guru',
            'ctx' => 'absen-guru',
            'data' => $this->guruModel->allGuru()
        ];

        return view('admin/absen/absen-guru', $data);
    }

    public function ambil_guru()
    {
        // ambil variabel POST
        $tanggal = $this->request->getVar('tanggal');

        $lewat = Time::parse($tanggal)->isAfter(Time::today());

        $result = $this->presensiGuruModel->get_presensi_byTanggal($tanggal);

        $data = [
            'data' => $result,
            'listKehadiran' => $this->kehadiranModel->get_kehadiran(),
            'lewat' => $lewat
        ];

        return view('admin/absen/list-absen-guru', $data);
    }
}
