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
        $data = ['waktu' => $t, 'title' => 'Absensi SMK ICB Cinta Niaga'];
        return view('scan/scan', $data);
    }

    public function cek_kode()
    {
        // ambil variabel POST
        $unique_code = $this->request->getVar('unique_code');
        $waktu_absen = $this->request->getVar('waktu');

        $status = false;
        $type = TipeUser::Siswa;

        // cek data siswa di database
        $result = $this->siswaModel->cek_siswa($unique_code);

        if (empty($result)) {
            // jika cek siswa gagal, cek data guru
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
            return $this->show_error_view('Data tidak ditemukan');
        }

        // jika data ditemukan
        switch ($waktu_absen) {
            case 'masuk':
                return $this->absen_masuk($type, $result);
                break;

            case 'pulang':
                return $this->absen_pulang($type, $result);
                break;

            default:
                return $this->show_error_view('Data tidak valid');
                break;
        }
    }

    public function absen_masuk($type, $result)
    {
        // data ditemukan
        $data['data'] = $result;
        $data['waktu'] = 'masuk';

        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();

        // absen masuk
        switch ($type) {
            case TipeUser::Guru:
                $id =  $result['id_guru'];
                $data['type'] = TipeUser::Guru;

                $sudahAbsen = $this->presensiGuruModel->cek_absen($id, $date);

                if ($sudahAbsen != false) {
                    $data['presensi'] = $this->presensiGuruModel->get_presensi_byId($sudahAbsen);
                    return $this->show_error_view('Anda sudah absen hari ini', $data);
                }

                $this->presensiGuruModel->absen_masuk($id, $date, $time);

                $data['presensi'] = $this->presensiGuruModel->get_presensi($id, $date);

                return view('scan/scan-result', $data);

            case TipeUser::Siswa:
                $id =  $result['id_siswa'];
                $id_kelas =  $result['id_kelas'];
                $data['type'] = TipeUser::Siswa;

                $sudahAbsen = $this->presensiSiswaModel->cek_absen($id, Time::today()->toDateString());

                if ($sudahAbsen != false) {
                    $data['presensi'] = $this->presensiSiswaModel->get_presensi_byId($sudahAbsen);
                    return $this->show_error_view('Anda sudah absen hari ini', $data);
                }

                $this->presensiSiswaModel->absen_masuk($id, $date, $time, $id_kelas);

                $data['presensi'] = $this->presensiSiswaModel->get_presensi($id, $date);

                return view('scan/scan-result', $data);

            default:
                return $this->show_error_view('Tipe tidak valid');
        }
    }

    public function absen_pulang($type, $result)
    {
        // data ditemukan
        $data['data'] = $result;
        $data['waktu'] = 'pulang';

        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();

        // absen pulang
        switch ($type) {
            case TipeUser::Guru:
                $id =  $result['id_guru'];
                $data['type'] = TipeUser::Guru;

                $sudahAbsen = $this->presensiGuruModel->cek_absen($id, $date);

                if ($sudahAbsen == false) {
                    return $this->show_error_view('Anda belum absen hari ini', $data);
                }

                $this->presensiGuruModel->absen_keluar($sudahAbsen, $time);

                $data['presensi'] = $this->presensiGuruModel->get_presensi_byId($sudahAbsen);

                return view('scan/scan-result', $data);

            case TipeUser::Siswa:
                $id =  $result['id_siswa'];
                $data['type'] = TipeUser::Siswa;

                $sudahAbsen = $this->presensiSiswaModel->cek_absen($id, $date);

                if ($sudahAbsen == false) {
                    return $this->show_error_view('Anda belum absen hari ini', $data);
                }

                $this->presensiSiswaModel->absen_keluar($sudahAbsen, $time);

                $data['presensi'] = $this->presensiSiswaModel->get_presensi_byId($sudahAbsen);

                return view('scan/scan-result', $data);
            default:
                return $this->show_error_view('Tipe tidak valid');
        }
    }

    public function show_error_view(string $msg = 'no error message', $data = NULL)
    {
        $errdata = $data ?? [];
        $errdata['msg'] = $msg;

        return view('scan/error-scan-result', $errdata);
    }
}
