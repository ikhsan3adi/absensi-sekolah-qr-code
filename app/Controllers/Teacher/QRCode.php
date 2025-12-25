<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Controllers\Admin\QRGenerator;

class QRCode extends BaseController
{
    protected KelasModel $kelasModel;
    protected SiswaModel $siswaModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $user = user();
        if (empty($user->id_guru)) {
            return redirect()->to('admin')->with('error', 'Anda bukan Wali Kelas.');
        }

        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);

        if (empty($kelas)) {
            return redirect()->to('teacher/dashboard')->with('error', 'Kelas belum ditugaskan.');
        }

        $siswa = $this->siswaModel->getSiswaByKelas($kelas['id_kelas']);

        $data = [
            'title' => 'Download QR Code Siswa',
            'ctx' => 'qr',
            'kelas' => $kelas,
            'siswa' => $siswa
        ];

        return view('teacher/qr_code', $data);
    }

    public function download()
    {
        $user = user();
        if (empty($user->id_guru)) {
            return redirect()->to('admin')->with('error', 'Anda bukan Wali Kelas.');
        }

        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);

        if (empty($kelas)) {
            return redirect()->back();
        }

        // We can reuse the admin QR generator logic
        $qrGenerator = new QRGenerator();
        $qrGenerator->initController($this->request, $this->response, service('logger'));

        $this->request->setGlobal('get', ['id_kelas' => $kelas['id_kelas']]);

        return $qrGenerator->downloadAllQrSiswa();
    }
}
