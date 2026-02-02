<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;

use App\Models\KehadiranModel;

class Dashboard extends BaseController
{
    protected KelasModel $kelasModel;
    protected SiswaModel $siswaModel;
    protected PresensiSiswaModel $presensiSiswaModel;
    protected KehadiranModel $kehadiranModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
        $this->kehadiranModel = new KehadiranModel();
    }

    public function index()
    {
        $user = user();
        if (!is_wali_kelas()) {
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
                'total_siswa' => $this->siswaModel->getSiswaCountByKelas($kelas['id_kelas']),
                'hadir_hari_ini' => count($this->presensiSiswaModel->getPresensiByKehadiran('1', $today, $kelas['id_kelas'])),
                'sakit_hari_ini' => count($this->presensiSiswaModel->getPresensiByKehadiran('2', $today, $kelas['id_kelas'])),
                'izin_hari_ini' => count($this->presensiSiswaModel->getPresensiByKehadiran('3', $today, $kelas['id_kelas'])),
                'alfa_hari_ini' => count($this->presensiSiswaModel->getPresensiByKehadiran('4', $today, $kelas['id_kelas']))
            ]
        ];

        // Weekly chart data using getAttendanceTrend
        $dateRange = [];
        for ($i = 6; $i >= 0; $i--) {
            if ($i == 0) {
                $formattedDate = "Hari ini";
            } else {
                $t = $now->subDays($i);
                $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
            }
            array_push($dateRange, $formattedDate);
        }

        // Get attendance trend for all 4 statuses
        $grafikKehadiran = $this->presensiSiswaModel->getAttendanceTrend(7, $kelas['id_kelas']);

        $data['dateRange'] = $dateRange;
        $data['grafikKehadiran'] = $grafikKehadiran;

        return view('teacher/dashboard', $data);
    }
    /**
     * Show attendance management page for the Wali Kelas.
     */
    public function attendance()
    {
        $user = user();
        if (!is_wali_kelas()) {
            return redirect()->to('teacher/dashboard')->with('error', 'Anda bukan Wali Kelas.');
        }

        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);
        if (empty($kelas)) {
            return redirect()->to('teacher/dashboard')->with('error', 'Anda belum ditugaskan sebagai Wali Kelas.');
        }

        $data = [
            'title' => 'Manajemen Kehadiran',
            'ctx' => 'attendance',
            'kelas' => $kelas,
            'date' => Time::now()->toDateString()
        ];

        return view('teacher/attendance', $data);
    }

    public function getAttendanceList()
    {
        $idKelas = $this->request->getVar('id_kelas');
        $namaKelas = $this->request->getVar('kelas'); // Just passed back to view
        $tanggal = $this->request->getVar('tanggal');

        $result = $this->presensiSiswaModel->getPresensiByKelasTanggal($idKelas, $tanggal);
        $lewat = Time::parse($tanggal)->isAfter(Time::today());

        $data = [
            'data' => $result,
            'kelas' => $namaKelas,
            'lewat' => $lewat
        ];

        return view('teacher/absen/list_absen_siswa', $data);
    }

    public function getEditModal()
    {
        $idPresensi = $this->request->getVar('id_presensi');
        $idSiswa = $this->request->getVar('id_siswa');

        $data = [
            'presensi' => $this->presensiSiswaModel->getPresensiById($idPresensi),
            'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
            'data' => $this->siswaModel->getSiswaById($idSiswa)
        ];

        return view('teacher/absen/modal_ubah_kehadiran', $data);
    }

    public function updateSingleAttendance()
    {
        $idKehadiran = $this->request->getVar('id_kehadiran');
        $idSiswa = $this->request->getVar('id_siswa');
        $idKelas = $this->request->getVar('id_kelas');
        $tanggal = $this->request->getVar('tanggal');
        $jamMasuk = $this->request->getVar('jam_masuk');
        $jamKeluar = $this->request->getVar('jam_keluar');
        $keterangan = $this->request->getVar('keterangan');

        // Check if attendance exists
        $cek = $this->presensiSiswaModel->cekAbsen($idSiswa, $tanggal);

        // Update or Insert (updatePresensi handles logic if first arg is ID or null/false)
        /* 
           wait, presensiSiswaModel->updatePresensi(idPresensi, ...)
           cekAbsen returns ID if exists, OR false.
           If false, we pass null to create new.
        */
        $result = $this->presensiSiswaModel->updatePresensi(
            $cek == false ? null : $cek,
            $idSiswa,
            $idKelas,
            $tanggal,
            $idKehadiran,
            $jamMasuk ?: null,
            $jamKeluar ?: null,
            $keterangan
        );

        $response['nama_siswa'] = $this->siswaModel->getSiswaById($idSiswa)['nama_siswa'];
        $response['status'] = $result ? true : false;

        return $this->response->setJSON($response);
    }
}
