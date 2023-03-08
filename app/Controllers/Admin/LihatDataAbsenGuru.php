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

    protected PresensiGuruModel $presensiGuru;

    protected KehadiranModel $kehadiranModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();

        $this->presensiGuru = new PresensiGuruModel();

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

        $result = $this->presensiGuru->get_presensi_byTanggal($tanggal);

        $data = [
            'data' => $result,
            'listKehadiran' => $this->kehadiranModel->get_kehadiran(),
            'lewat' => $lewat
        ];

        return view('admin/absen/list-absen-guru', $data);
    }

    public function ubah_kehadiran()
    {
        // ambil variabel POST
        $id_kehadiran = $this->request->getVar('id_kehadiran');
        $id_guru = $this->request->getVar('id_guru');
        $tanggal = $this->request->getVar('tanggal');
        $jam_masuk = $this->request->getVar('jam_masuk');
        $keterangan = $this->request->getVar('keterangan');

        $cek = $this->presensiGuru->cek_absen($id_guru, $tanggal);

        $result = $this->presensiGuru->update_presensi(
            $cek == false ? NULL : $cek,
            $id_guru,
            $tanggal,
            $id_kehadiran,
            $jam_masuk ?? NULL,
            $keterangan
        );

        $response['nama_guru'] = $this->guruModel->getGuruById($id_guru)['nama_guru'];

        if ($result) {
            $response['status'] = TRUE;
        } else {
            $response['status'] = FALSE;
        }

        return $this->response->setJSON($response);
    }
}
