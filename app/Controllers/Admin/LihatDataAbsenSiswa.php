<?php

namespace App\Controllers\Admin;

use App\Models\KelasModel;

use App\Models\SiswaModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;

class LihatDataAbsenSiswa extends BaseController
{
    protected KelasModel $kelasModel;

    protected SiswaModel $siswaModel;

    protected KehadiranModel $kehadiranModel;

    protected PresensiSiswaModel $presensiSiswa;

    protected string $currentDate;

    public function __construct()
    {
        $this->currentDate = Time::today()->toDateString();

        $this->siswaModel = new SiswaModel();

        $this->kehadiranModel = new KehadiranModel();

        $this->kelasModel = new KelasModel();

        $this->presensiSiswa = new PresensiSiswaModel();
    }

    public function index()
    {
        $result = $this->kelasModel->all_kelas();

        $data = [
            'title' => 'Data Absen Siswa',
            'ctx' => 'absen-siswa',
            'data' => $result
        ];

        return view('admin/absen/absen-siswa', $data);
    }

    public function ambil_siswa()
    {
        // ambil variabel POST
        $kelas = $this->request->getVar('kelas');
        $id_kelas = $this->request->getVar('id_kelas');
        $tanggal = $this->request->getVar('tanggal');

        $lewat = Time::parse($tanggal)->isAfter(Time::today());

        $result = $this->presensiSiswa->get_presensi_byKelasTanggal($id_kelas, $tanggal);

        $data = [
            'kelas' => $kelas,
            'data' => $result,
            'listKehadiran' => $this->kehadiranModel->get_kehadiran(),
            'lewat' => $lewat
        ];

        return view('admin/absen/list-absen-siswa', $data);
    }

    public function ubah_kehadiran()
    {
        // ambil variabel POST
        $id_kehadiran = $this->request->getVar('id_kehadiran');
        $id_siswa = $this->request->getVar('id_siswa');
        $id_kelas = $this->request->getVar('id_kelas');
        $tanggal = $this->request->getVar('tanggal');
        $jam_masuk = $this->request->getVar('jam_masuk');
        $keterangan = $this->request->getVar('keterangan');

        $cek = $this->presensiSiswa->cek_absen($id_siswa, $tanggal);

        $result = $this->presensiSiswa->update_presensi(
            $cek == false ? NULL : $cek,
            $id_siswa,
            $id_kelas,
            $tanggal,
            $id_kehadiran,
            $jam_masuk ?? NULL,
            $keterangan
        );

        $response['nama_siswa'] = $this->siswaModel->getSiswaById($id_siswa)['nama_siswa'];

        if ($result) {
            $response['status'] = TRUE;
        } else {
            $response['status'] = FALSE;
        }

        return $this->response->setJSON($response);
    }
}
