<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;

class CekKehadiran extends BaseController
{
    protected $siswaModel;
    protected $presensiSiswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        return view('cek_kehadiran/index', [
            'title' => 'Portal Cek Kehadiran Mandiri'
        ]);
    }

    public function view()
    {
        $nis = request()->getPost('nis');
        $no_hp = request()->getPost('no_hp');

        // Validasi identitas
        $siswa = $this->siswaModel->where(['nis' => $nis, 'no_hp' => $no_hp])->first();

        if (!$siswa) {
            return redirect()->back()->with('error', 'Kombinasi NIS dan Nomor HP tidak cocok.');
        }

        // Ambil data presensi tahun ini untuk DataTables
        $year = date('Y');
        
        $history = $this->presensiSiswaModel
            ->where('id_siswa', $siswa['id_siswa'])
            ->where('YEAR(tanggal)', $year)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        // Hitung Summary (Bulan Berjalan)
        $month = date('m');
        $stats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alfa' => 0
        ];

        foreach ($history as $h) {
            if (date('m', strtotime($h['tanggal'])) == $month) {
                if ($h['id_kehadiran'] == 1) $stats['hadir']++;
                elseif ($h['id_kehadiran'] == 2) $stats['sakit']++;
                elseif ($h['id_kehadiran'] == 3) $stats['izin']++;
                elseif ($h['id_kehadiran'] == 4) $stats['alfa']++;
            }
        }

        return view('cek_kehadiran/hasil', [
            'title' => 'Riwayat Kehadiran: ' . $siswa['nama_siswa'],
            'siswa' => $siswa,
            'history' => $history,
            'stats' => $stats,
            'monthName' => date('F Y')
        ]);
    }
}
