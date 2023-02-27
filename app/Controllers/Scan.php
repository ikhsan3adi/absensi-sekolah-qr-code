<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\PresensiGuruModel;
use App\Models\PresensiSiswaModel;
use App\Models\TipeUser;

class Scan extends BaseController
{
    protected SiswaModel $siswaModel;
    protected GuruModel $guruModel;

    protected PresensiSiswaModel $presensiSiswaModel;
    protected PresensiGuruModel $presensiGuruModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->guruModel = new GuruModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
        $this->presensiGuruModel = new PresensiGuruModel();
    }

    public function index($t = 'Masuk')
    {
        $data = ['waktu' => $t];
        return view('scan/scan', $data);
    }

    public function cek_kode()
    {
        $status = false;
        $type = TipeUser::Siswa;

        $unique_code = $this->request->getVar('unique_code');

        $result = $this->siswaModel->cek_siswa($unique_code);

        if (empty($result)) {
            $result = $this->guruModel->cek_guru($unique_code);

            if (!empty($result)) {
                $status = true;

                $type = TipeUser::Guru;
            } else {
                $status = false;

                $result = NULL;
            }
        } else {
            $status = true;
        }

        if (!$status) { // data tidak ditemukan
            $this->show_error_view('Data tidak ditemukan');
        }

        // data ditemukan
        $data = ['data' => $result];

        // absen masuk
        switch ($type) {
            case TipeUser::Guru:
                $id =  $result['id_guru'];

                $sudahAbsen = $this->presensiGuruModel->cek_absen($id, Time::today()->toDateString());

                if ($sudahAbsen) {
                    return $this->show_error_view('Anda sudah absen hari ini', [
                        'Nama : ' => $result['nama_guru']
                    ]);
                }

                $this->presensiGuruModel->absen_masuk($id);
                return view('scan/scan-result-card-guru', $data);

            case TipeUser::Siswa:

                $id =  $result['id_siswa'];

                $sudahAbsen = $this->presensiSiswaModel->cek_absen($id, Time::today()->toDateString());

                if ($sudahAbsen) {
                    return $this->show_error_view('Anda sudah absen hari ini', [
                        'Nama : ' => $result['nama_siswa'],
                        'Nis : ' => $result['nis'],
                        'Kelas : ' => $result['kelas']
                    ]);
                }

                $this->presensiSiswaModel->absen_masuk($id);
                return view('scan/scan-result-card-siswa', $data);

            default:
                $this->show_error_view('Tipe tidak valid');
        }
    }

    public function show_error_view(string $msg = 'no error message', $data = NULL)
    {
        $errdata = [
            'data' => $data,
            'msg' => $msg
        ];

        return view('scan/error-scan-result', $errdata);
    }
}
