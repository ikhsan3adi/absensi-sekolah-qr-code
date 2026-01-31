<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use DateTime;
use DateInterval;
use DatePeriod;

use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;

class Reports extends BaseController
{
    protected SiswaModel $siswaModel;
    protected KelasModel $kelasModel;
    protected PresensiSiswaModel $presensiSiswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
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

        $data = [
            'title' => 'Laporan Presensi Kelas',
            'ctx' => 'laporan-kelas',
            'kelas' => $kelas
        ];

        return view('teacher/reports', $data);
    }

    public function generate()
    {
        $user = user();
        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);

        if (empty($kelas)) {
            return redirect()->to('teacher/dashboard');
        }

        $idKelas = $kelas['id_kelas'];
        $siswa = $this->siswaModel->getSiswaByKelas($idKelas);
        $type = $this->request->getVar('type');

        if (empty($siswa)) {
            return redirect()->back()->with('error', 'Data siswa kosong!');
        }

        $kelasData = (array) $this->kelasModel->getKelas($idKelas);
        $bulan = $this->request->getVar('bulan');

        $begin = new Time($bulan, locale: 'id');
        $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $arrayTanggal = [];
        $dataAbsen = [];

        foreach ($period as $value) {
            if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
                $lewat = Time::parse($value->format('Y-m-d'))->isAfter(Time::today());
                $absenByTanggal = $this->presensiSiswaModel->getPresensiByKelasTanggal($idKelas, $value->format('Y-m-d'));
                $absenByTanggal['lewat'] = $lewat;
                array_push($dataAbsen, $absenByTanggal);
                array_push($arrayTanggal, Time::createFromInstance($value, locale: 'id'));
            }
        }

        $laki = 0;
        foreach ($siswa as $value) {
            if ($value['jenis_kelamin'] != 'Perempuan') {
                $laki++;
            }
        }

        $data = [
            'tanggal' => $arrayTanggal,
            'bulan' => $begin->toLocalizedString('MMMM'),
            'listAbsen' => $dataAbsen,
            'listSiswa' => $siswa,
            'rekapSiswa' => [
                'laki' => $laki,
                'perempuan' => count($siswa) - $laki
            ],
            'kelas' => $kelasData,
            'grup' => "kelas " . $kelasData['kelas'],
        ];

        if ($type == 'doc') {
            $this->response->setHeader('Content-type', 'application/vnd.ms-word');
            $this->response->setHeader(
                'Content-Disposition',
                'attachment;Filename=laporan_absen_' . $kelasData['kelas'] . '_' . $begin->toLocalizedString('MMMM-Y') . '.doc'
            );
            return view('admin/generate-laporan/laporan-siswa', $data);
        }

        return view('admin/generate-laporan/laporan-siswa', $data) . view('admin/generate-laporan/topdf');
    }
}
