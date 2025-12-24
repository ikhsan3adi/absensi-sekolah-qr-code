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
    /**
     * Show attendance management page for the Wali Kelas.
     */
    public function attendance()
    {
        $user = user();
        if (empty($user->id_guru)) {
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
