<?php

namespace App\Controllers\Admin;

use App\Models\SiswaModel;
use App\Models\KelasModel;

use App\Controllers\BaseController;
use App\Models\JurusanModel;
use App\Models\UploadModel;
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
         'kelas' => $this->kelasModel->getDataKelas(),
         'jurusan' => $this->jurusanModel->getDataJurusan()
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
      $kelas = $this->kelasModel->getDataKelas();

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
         $kelas = $this->kelasModel->getDataKelas();

         $data = [
            'ctx' => 'siswa',
            'kelas' => $kelas,
            'title' => 'Tambah Data Siswa',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-siswa', $data);
      }

      // simpan
      $result = $this->siswaModel->createSiswa(
         nis: $this->request->getVar('nis'),
         nama: $this->request->getVar('nama'),
         idKelas: intval($this->request->getVar('id_kelas')),
         jenisKelamin: $this->request->getVar('jk'),
         noHp: $this->request->getVar('no_hp'),
      );

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
      $kelas = $this->kelasModel->getDataKelas();

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
         $kelas = $this->kelasModel->getDataKelas();

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

      // update
      $result = $this->siswaModel->updateSiswa(
         id: $idSiswa,
         nis: $this->request->getVar('nis'),
         nama: $this->request->getVar('nama'),
         idKelas: intval($this->request->getVar('id_kelas')),
         jenisKelamin: $this->request->getVar('jk'),
         noHp: $this->request->getVar('no_hp'),
      );

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

   /**
    * Delete Selected Posts
    */
   public function deleteSelectedSiswa()
   {
      $siswaIds = inputPost('siswa_ids');
      $this->siswaModel->deleteMultiSelected($siswaIds);
   }

   /*
    *-------------------------------------------------------------------------------------------------
    * IMPORT SISWA
    *-------------------------------------------------------------------------------------------------
    */

   /**
    * Bulk Post Upload
    */
   public function bulkPostSiswa()
   {
      $data['title'] = 'Import Siswa';
      $data['ctx'] = 'siswa';
      $data['kelas'] = $this->kelasModel->getDataKelas();

      return view('/admin/data/import-siswa', $data);
   }

   /**
    * Generate CSV Object Post
    */
   public function generateCSVObjectPost()
   {
      $uploadModel = new UploadModel();
      //delete old txt files
      $files = glob(FCPATH . 'uploads/tmp/*.txt');
      if (!empty($files)) {
         foreach ($files as $item) {
            @unlink($item);
         }
      }
      $file = $uploadModel->uploadCSVFile('file');
      if (!empty($file) && !empty($file['path'])) {
         $obj = $this->siswaModel->generateCSVObject($file['path']);
         if (!empty($obj)) {
            $data = [
               'result' => 1,
               'numberOfItems' => $obj->numberOfItems,
               'txtFileName' => $obj->txtFileName,
            ];
            echo json_encode($data);
            exit();
         }
      }
      echo json_encode(['result' => 0]);
   }

   /**
    * Import CSV Item Post
    */
   public function importCSVItemPost()
   {
      $txtFileName = inputPost('txtFileName');
      $index = inputPost('index');
      $siswa = $this->siswaModel->importCSVItem($txtFileName, $index);
      if (!empty($siswa)) {
         $data = [
            'result' => 1,
            'siswa' => $siswa,
            'index' => $index
         ];
         echo json_encode($data);
      } else {
         $data = [
            'result' => 0,
            'index' => $index
         ];
         echo json_encode($data);
      }
   }

   /**
    * Download CSV File Post
    */
   public function downloadCSVFilePost()
   {
      $submit = inputPost('submit');
      $response = \Config\Services::response();
      if ($submit == 'csv_siswa_template') {
         return $response->download(FCPATH . 'assets/file/csv_siswa_template.csv', null);
      } elseif ($submit == 'csv_guru_template') {
         return $response->download(FCPATH . 'assets/file/csv_guru_template.csv', null);
      }
   }
}
