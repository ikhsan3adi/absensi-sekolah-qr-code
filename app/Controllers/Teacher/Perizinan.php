<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\PerizinanModel;
use App\Models\PresensiSiswaModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;

class Perizinan extends BaseController
{
    protected $perizinanModel;
    protected $presensiSiswaModel;
    protected $siswaModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->perizinanModel = new PerizinanModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        helper(['user_helper']);
    }

    public function index()
    {
        $user = user();
        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);

        if (empty($kelas)) {
            return view('teacher/perizinan/index', [
                'title' => 'Data Perizinan Siswa',
                'perizinan' => [],
                'ctx' => 'perizinan',
                'no_class' => true
            ]);
        }

        // Ambil perizinan khusus untuk siswa di kelas ini
        $perizinan = $this->perizinanModel->db->table('tb_perizinan')
            ->select('tb_perizinan.*, tb_siswa.nama_siswa, tb_siswa.nis, tb_kelas.tingkat, tb_jurusan.jurusan, tb_kelas.index_kelas')
            ->join('tb_siswa', 'tb_siswa.id_siswa = tb_perizinan.id_siswa')
            ->join('tb_kelas', 'tb_kelas.id_kelas = tb_siswa.id_kelas')
            ->join('tb_jurusan', 'tb_jurusan.id = tb_kelas.id_jurusan')
            ->where('tb_siswa.id_kelas', $kelas['id_kelas'])
            ->orderBy('tb_perizinan.created_at', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Data Perizinan Siswa',
            'perizinan' => $perizinan,
            'ctx' => 'perizinan'
        ];
        return view('teacher/perizinan/index', $data);
    }

    public function konfirmasi()
    {
        $id_perizinan = request()->getPost('id_perizinan');
        $status = request()->getPost('status');
        $id_petugas = user_id();

        $result = $this->perizinanModel->konfirmasiPerizinan($id_perizinan, $status, $id_petugas);

        return $this->response->setJSON($result);
    }
}
