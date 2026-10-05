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
        if ($denied = $this->denyUnlessPermitted()) {
            return $denied;
        }

        $data = [
            'title' => 'Data Perizinan Siswa',
            'perizinan' => $this->perizinanModel->getPerizinanWithSiswa(),
            'ctx' => 'perizinan'
        ];
        return view('admin/perizinan/index', $data);
    }

    public function konfirmasi()
    {
        if ($denied = $this->denyUnlessPermitted()) {
            return $denied;
        }

        $id_perizinan = request()->getPost('id_perizinan');
        $status = request()->getPost('status');
        $id_petugas = user_id();

        $result = $this->perizinanModel->konfirmasiPerizinan($id_perizinan, $status, $id_petugas);

        return $this->response->setJSON($result);
    }

    public function delete($id)
    {
        if ($denied = $this->denyUnlessPermitted()) {
            return $denied;
        }

        $perizinan = $this->perizinanModel->find($id);
        if ($perizinan && $perizinan['bukti']) {
            @unlink(FCPATH . 'uploads/perizinan/' . $perizinan['bukti']);
        }
        $this->perizinanModel->delete($id);
        return redirect()->to(base_url('admin/perizinan'))->with('success', 'Data perizinan berhasil dihapus.');
    }

    /**
     * Menyajikan file bukti perizinan secara terproteksi.
     * Admin dengan permits.manage dapat melihat semua bukti; guru
     * hanya bukti perizinan siswa di kelas yang ia ampu.
     */
    public function bukti($filename)
    {
        $user = user();
        if ($user === null) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $filename = basename((string) $filename);
        $perizinan = $this->perizinanModel->where('bukti', $filename)->first();

        if (! $perizinan) {
            return $this->response->setStatusCode(404)->setBody('File tidak ditemukan.');
        }

        if (! $user->can('permits.manage')) {
            if (! $user->can('teacher.access') || ! $this->isPermitInTeacherClass($perizinan, $user->id_guru ?? null)) {
                return $this->response->setStatusCode(403)->setBody('Forbidden');
            }
        }

        $baseDir = realpath(FCPATH . 'uploads/perizinan');
        $path = realpath(FCPATH . 'uploads/perizinan/' . $filename);

        if ($baseDir === false || $path === false || ! str_starts_with($path, $baseDir . DIRECTORY_SEPARATOR)) {
            return $this->response->setStatusCode(404)->setBody('File tidak ditemukan.');
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody(file_get_contents($path));
    }

    private function isPermitInTeacherClass(array $perizinan, $idGuru): bool
    {
        if (empty($perizinan['id_siswa']) || empty($idGuru)) {
            return false;
        }

        $siswa = $this->siswaModel->find($perizinan['id_siswa']);
        if (! $siswa) {
            return false;
        }

        return $this->perizinanModel->db->table('tb_kelas')
            ->where('id_kelas', $siswa['id_kelas'])
            ->where('id_wali_kelas', $idGuru)
            ->countAllResults() > 0;
    }

    private function denyUnlessPermitted()
    {
        $user = user();
        if ($user !== null && $user->can('permits.manage')) {
            return null;
        }

        return $this->response->setStatusCode(403)->setBody('Forbidden');
    }
}
