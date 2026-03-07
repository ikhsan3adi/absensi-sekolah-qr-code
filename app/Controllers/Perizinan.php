<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\PerizinanModel;
use CodeIgniter\I18n\Time;

class Perizinan extends BaseController
{
    protected $siswaModel;
    protected $perizinanModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->perizinanModel = new PerizinanModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Pengajuan Izin/Sakit Digital',
        ];
        return view('perizinan/form_pengajuan', $data);
    }

    public function getSiswaByNis()
    {
        $type = request()->getPost('type');
        $id_number = request()->getPost('nis');

        if ($type === 'guru') {
            $guruModel = new \App\Models\GuruModel();
            $result = $guruModel->where('nuptk', $id_number)->first();
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [
                        'id' => $result['id_guru'],
                        'nama' => $result['nama_guru'],
                    ]
                ]);
            }
        } else {
            $siswa = $this->siswaModel->where('nis', $id_number)->first();
            if ($siswa) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [
                        'id' => $siswa['id_siswa'],
                        'nama' => $siswa['nama_siswa'],
                    ]
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => ($type === 'guru' ? 'Guru dengan NUPTK' : 'Siswa dengan NIS') . ' tersebut tidak ditemukan.'
        ]);
    }

    public function submit()
    {
        $type = request()->getPost('type');
        $validationRules = [
            'id_target' => 'required',
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date',
            'tipe_izin' => 'required|in_list[Sakit,Izin]',
            'alasan' => 'required',
            'bukti' => 'uploaded[bukti]|max_size[bukti,2048]|is_image[bukti]|mime_in[bukti,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = request()->getFile('bukti');
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/perizinan', $newName);

        $data = [
            'tanggal_mulai' => request()->getPost('tanggal_mulai'),
            'tanggal_selesai' => request()->getPost('tanggal_selesai'),
            'tipe_izin' => request()->getPost('tipe_izin'),
            'alasan' => request()->getPost('alasan'),
            'bukti' => $newName,
            'status' => 'Pending',
        ];

        if ($type === 'guru') {
            $data['id_guru'] = request()->getPost('id_target');
        } else {
            $data['id_siswa'] = request()->getPost('id_target');
        }

        $this->perizinanModel->insert($data);

        return redirect()->to(base_url('izin'))->with('success', 'Pengajuan izin berhasil dikirim. Silakan tunggu konfirmasi dari Admin.');
    }
}
