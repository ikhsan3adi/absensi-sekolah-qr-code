<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PerizinanModel;
use App\Models\PresensiSiswaModel;
use App\Models\SiswaModel;
use CodeIgniter\I18n\Time;

class Perizinan extends BaseController
{
    protected $perizinanModel;
    protected $presensiSiswaModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->perizinanModel = new PerizinanModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
        $this->siswaModel = new SiswaModel();
        helper(['user_helper']);
    }

    public function index()
    {
        $data = [
            'title' => 'Data Perizinan Siswa',
            'perizinan' => $this->perizinanModel->getPerizinanWithSiswa(),
            'ctx' => 'perizinan'
        ];
        return view('admin/perizinan/index', $data);
    }

    public function konfirmasi()
    {
        $id_perizinan = request()->getPost('id_perizinan');
        $status = request()->getPost('status');
        $id_petugas = user_id();

        $result = $this->perizinanModel->konfirmasiPerizinan($id_perizinan, $status, $id_petugas);

        return $this->response->setJSON($result);
    }

    public function delete($id)
    {
        $perizinan = $this->perizinanModel->find($id);
        if ($perizinan && $perizinan['bukti']) {
            @unlink(FCPATH . 'uploads/perizinan/' . $perizinan['bukti']);
        }
        $this->perizinanModel->delete($id);
        return redirect()->to(base_url('admin/perizinan'))->with('success', 'Data perizinan berhasil dihapus.');
    }
}
