<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;

class Dashboard extends BaseController
{
    protected KelasModel $kelasModel;
    protected SiswaModel $siswaModel;
    protected PresensiSiswaModel $presensiSiswaModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
    }

    public function index()
    {
        $user = user();
        if (empty($user->id_guru)) {
            return redirect()->to('admin')->with('error', 'Anda bukan Wali Kelas.');
        }

        // Get class where the teacher is Wali Kelas
        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);

        if (empty($kelas)) {
            $data = [
                'title' => 'Dashboard Wali Kelas',
                'ctx' => 'dashboard',
                'no_class' => true
            ];
            return view('teacher/dashboard', $data);
        }

        $now = Time::now();
        $today = $now->toDateString();

        // Basic stats
        $data = [
            'title' => 'Dashboard Wali Kelas',
            'ctx' => 'dashboard',
            'kelas' => $kelas,
            'summary' => [
                'total_siswa' => $this->siswaModel->where('id_kelas', $kelas['id_kelas'])->countAllResults(),
                'hadir_hari_ini' => $this->presensiSiswaModel->where(['id_kelas' => $kelas['id_kelas'], 'tanggal' => $today, 'id_kehadiran' => '1'])->countAllResults(),
                'sakit_hari_ini' => $this->presensiSiswaModel->where(['id_kelas' => $kelas['id_kelas'], 'tanggal' => $today, 'id_kehadiran' => '2'])->countAllResults(),
                'izin_hari_ini' => $this->presensiSiswaModel->where(['id_kelas' => $kelas['id_kelas'], 'tanggal' => $today, 'id_kehadiran' => '3'])->countAllResults(),
                'alfa_hari_ini' => $this->presensiSiswaModel->where(['id_kelas' => $kelas['id_kelas'], 'tanggal' => $today, 'id_kehadiran' => '4'])->countAllResults(),
            ]
        ];

        // Weekly chart data
        $dateRange = [];
        $kehadiranArray = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->subDays($i)->toDateString();
            if ($i == 0) {
                $formattedDate = "Hari ini";
            } else {
                $t = $now->subDays($i);
                $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
            }
            array_push($dateRange, $formattedDate);
            array_push(
                $kehadiranArray,
                $this->presensiSiswaModel->where(['id_kelas' => $kelas['id_kelas'], 'tanggal' => $date, 'id_kehadiran' => '1'])->countAllResults()
            );
        }

        $data['dateRange'] = $dateRange;
        $data['kehadiranArray'] = $kehadiranArray;

        return view('teacher/dashboard', $data);
    }
}
