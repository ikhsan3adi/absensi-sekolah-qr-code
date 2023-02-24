<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\SiswaModel;

class Scan extends BaseController
{
    protected SiswaModel $siswaModel;
    protected GuruModel $guruModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->guruModel = new GuruModel();
    }

    public function index($t = 'Masuk')
    {
        $data = ['waktu' => $t];
        return view('scan/scan', $data);
    }

    public function cek_kode()
    {
        $status = false;
        $type = 'siswa';

        $unique_code = $this->request->getVar('unique_code');

        $result = $this->siswaModel->cek_siswa($unique_code);

        if (empty($result)) {
            $result = $this->guruModel->cek_guru($unique_code);

            if (!empty($result)) {
                $status = true;
                $type = 'guru';
            } else {
                $status = false;

                $result = NULL;
            }
        } else {
            $status = true;
        }

        if ($status) { // data ditemukan
            $data = ['data' => $result];
            return $type == 'siswa'
                ? view('scan/scan-result-card-siswa', $data)
                : view('scan/scan-result-card-guru', $data);
        }

        $data = ['data' => $result, 'msg' => 'Data tidak ditemukan'];

        return view('scan/error-scan-result', $data);

        // $this->response->setJSON([
        //     'status' => $status,
        //     'data' => $result
        // ]);
    }

    public function cek_kehadiran(string $unique_code)
    {
        # code...
    }

    public function absen_masuk(string $unique_code)
    {
        # code...
    }

    public function absen_keluar(string $unique_code)
    {
        # code...
    }
}
