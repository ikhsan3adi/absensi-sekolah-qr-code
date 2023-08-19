<?php

namespace App\Controllers\Admin;

use App\Models\SiswaModel;
use App\Models\KelasModel;

use App\Controllers\BaseController;
use App\Models\JurusanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataSiswa extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KelasModel $kelasModel;
   protected JurusanModel $jurusanModel;

   protected $siswaValidationRules = [
      'nis' => [
         'rules' => 'required|max_length[20]|min_length[4]',
         'errors' => [
            'required' => 'NIS harus diisi.',
            'is_unique' => 'NIS ini telah terdaftar.',
            'min_length[4]' => 'Panjang NIS minimal 4 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'id_kelas' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'Kelas harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]'
   ];

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->kelasModel = new KelasModel();
      $this->jurusanModel = new JurusanModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Siswa',
         'ctx' => 'siswa',
         'kelas' => $this->kelasModel->getAllKelas(),
         'jurusan' => $this->jurusanModel->findAll()
      ];

      return view('admin/data/data-siswa', $data);
   }

   public function ambilDataSiswa()
   {
      $kelas = $this->request->getVar('kelas') ?? null;
      $jurusan = $this->request->getVar('jurusan') ?? null;

      $result = $this->siswaModel->getAllSiswaWithKelas($kelas, $jurusan);

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-siswa', $data);
   }

   public function formTambahSiswa()
   {
      $kelas = $this->kelasModel->getAllKelas();

      $data = [
         'ctx' => 'siswa',
         'kelas' => $kelas,
         'title' => 'Tambah Data Siswa'
      ];

      return view('admin/data/create/create-data-siswa', $data);
   }

   public function saveSiswa()
   {
      // validasi
      if (!$this->validate($this->siswaValidationRules)) {
         $kelas = $this->kelasModel->getAllKelas();

         $data = [
            'ctx' => 'siswa',
            'kelas' => $kelas,
            'title' => 'Tambah Data Siswa',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-siswa', $data);
      }

      $nis = $this->request->getVar('nis');
      $namaSiswa = $this->request->getVar('nama');
      $idKelas = intval($this->request->getVar('id_kelas'));
      $jenisKelamin = $this->request->getVar('jk');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->siswaModel->saveSiswa(NULL, $nis, $namaSiswa, $idKelas, $jenisKelamin, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/siswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/siswa/create');
   }

   public function formEditSiswa($id)
   {
      $siswa = $this->siswaModel->getSiswaById($id);
      $kelas = $this->kelasModel->getAllKelas();

      if (empty($siswa) || empty($kelas)) {
         throw new PageNotFoundException('Data siswa dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $siswa,
         'kelas' => $kelas,
         'ctx' => 'siswa',
         'title' => 'Edit Siswa',
      ];

      return view('admin/data/edit/edit-data-siswa', $data);
   }

   public function updateSiswa()
   {
      $idSiswa = $this->request->getVar('id');

      $siswaLama = $this->siswaModel->getSiswaById($idSiswa);

      if ($siswaLama['nis'] != $this->request->getVar('nis')) {
         $this->siswaValidationRules['nis']['rules'] = 'required|max_length[20]|min_length[4]|is_unique[tb_siswa.nis]';
      }

      // validasi
      if (!$this->validate($this->siswaValidationRules)) {
         $siswa = $this->siswaModel->getSiswaById($idSiswa);
         $kelas = $this->kelasModel->getAllKelas();

         $data = [
            'data' => $siswa,
            'kelas' => $kelas,
            'ctx' => 'siswa',
            'title' => 'Edit Siswa',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-siswa', $data);
      }

      $nis = $this->request->getVar('nis');
      $namaSiswa = $this->request->getVar('nama');
      $idKelas = intval($this->request->getVar('id_kelas'));
      $jenisKelamin = $this->request->getVar('jk');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->siswaModel->saveSiswa($idSiswa, $nis, $namaSiswa, $idKelas, $jenisKelamin, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/siswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/siswa/edit/' . $idSiswa);
   }

   public function delete($id)
   {
      $result = $this->siswaModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/siswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/siswa');
   }
}
