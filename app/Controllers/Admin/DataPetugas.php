<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\PetugasModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Myth\Auth\Password;
use App\Libraries\enums\UserRole;

class DataPetugas extends BaseController
{
   protected PetugasModel $petugasModel;
   protected \App\Models\GuruModel $guruModel;
   protected \App\Models\UploadModel $uploadModel;

   protected $petugasValidationRules = [
      'email' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'Email harus diisi.',
            'is_unique' => 'Email ini telah terdaftar.'
         ]
      ],
      'username' => [
         'rules' => 'required|min_length[6]',
         'errors' => [
            'required' => 'Username harus diisi',
            'is_unique' => 'Username ini telah terdaftar.'
         ]
      ],
      'password' => [
         'rules' => 'permit_empty|min_length[6]',
      ],
      'role' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'Role wajib diisi'
         ]
      ]
   ];


   public function __construct()
   {
      $this->petugasModel = new PetugasModel();
      $this->guruModel = new \App\Models\GuruModel();
      $this->uploadModel = new \App\Models\UploadModel();
   }

   public function index()
   {
      if (!is_superadmin()) {
         return redirect()->to('admin');
      }

      $data = [
         'title' => 'Data Petugas',
         'ctx' => 'petugas'
      ];

      return view('admin/petugas/data-petugas', $data);
   }

   public function ambilDataPetugas()
   {
      $petugas = $this->petugasModel->getAllPetugas();

      $data = [
         'data' => $petugas,
         'empty' => empty($petugas)
      ];

      return view('admin/petugas/list-data-petugas', $data);
   }

   public function registerPetugas()
   {
      if (!is_superadmin()) {
         return redirect()->to('admin');
      }

      $data = [
         'title' => 'Register Petugas',
         'ctx' => 'petugas',
         'guru' => $this->guruModel->getAllGuru(),
         'roles' => UserRole::ALL_ROLES
      ];

      return view('admin/petugas/register', $data);
   }

   public function registerPetugasPost()
   {
      if (!is_superadmin()) {
         return redirect()->to('admin');
      }

      $this->petugasValidationRules['email']['rules'] .= '|is_unique[users.email]';
      $this->petugasValidationRules['username']['rules'] .= '|is_unique[users.username]';
      $this->petugasValidationRules['password']['rules'] = 'required|min_length[6]';

      if (!$this->validate($this->petugasValidationRules)) {
         return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
      }

      $email = $this->request->getVar('email');
      $username = $this->request->getVar('username');
      $password = $this->request->getVar('password');
      $passwordHash = Password::hash($password);
      $role = $this->request->getVar('role');
      $id_guru = $this->request->getVar('id_guru') ?: null;

      $result = $this->petugasModel->savePetugas(null, $email, $username, $passwordHash, $role, $id_guru, 1);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Registrasi petugas berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/petugas');
      }

      session()->setFlashdata([
         'msg' => 'Gagal registrasi petugas',
         'error' => true
      ]);
      return redirect()->back()->withInput();
   }

   public function formEditPetugas($id)
   {
      $petugas = $this->petugasModel->getPetugasById($id);

      if (empty($petugas)) {
         throw new PageNotFoundException('Data petugas dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $petugas,
         'ctx' => 'petugas',
         'title' => 'Edit Data Petugas',
         'guru' => $this->guruModel->getAllGuru(),
         'roles' => UserRole::ALL_ROLES
      ];

      return view('admin/petugas/edit-data-petugas', $data);
   }

   public function updatePetugas()
   {
      $idPetugas = $this->request->getVar('id');

      $petugasLama = $this->petugasModel->getPetugasById($idPetugas);

      if ($petugasLama['username'] != $this->request->getVar('username')) {
         $this->petugasValidationRules['username']['rules'] = 'required|is_unique[users.username]';
      }

      if ($petugasLama['email'] != $this->request->getVar('email')) {
         $this->petugasValidationRules['email']['rules'] = 'required|is_unique[users.email]';
      }

      // validasi
      if (!$this->validate($this->petugasValidationRules)) {
         $data = [
            'data' => $this->petugasModel->getPetugasById($idPetugas),
            'ctx' => 'petugas',
            'title' => 'Edit Data Petugas',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar(),
            'guru' => $this->guruModel->getAllGuru(),
            'roles' => UserRole::ALL_ROLES
         ];
         return view('admin/petugas/edit-data-petugas', $data);
      }

      $password = $this->request->getVar('password') ?? false;

      $email = $this->request->getVar('email');
      $username = $this->request->getVar('username');
      $passwordHash = $password ? Password::hash($password) : $petugasLama['password_hash'];
      $role = $this->request->getVar('role');
      $id_guru = $this->request->getVar('id_guru') ?: null;

      $result = $this->petugasModel->savePetugas(
         $idPetugas,
         $email,
         $username,
         $passwordHash,
         $role,
         $id_guru,
         $petugasLama['active']
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/petugas');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/petugas/edit/' . $idPetugas);
   }

   public function delete($id)
   {
      $result = $this->petugasModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/petugas');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/petugas');
   }

   public function toggleActivation($id)
   {
      if (!is_superadmin()) {
         return redirect()->to('admin');
      }

      $petugas = $this->petugasModel->getPetugasById($id);
      if (empty($petugas)) {
         throw new PageNotFoundException('Data petugas dengan id ' . $id . ' tidak ditemukan');
      }

      $newStatus = ($petugas['active'] ?? 0) == 1 ? 0 : 1;
      $this->petugasModel->update($id, ['active' => $newStatus]);

      session()->setFlashdata([
         'msg' => 'Status akun berhasil diubah',
         'error' => false
      ]);

      return redirect()->to('/admin/petugas');
   }

   /*
    *-------------------------------------------------------------------------------------------------
    * IMPORT PETUGAS
    *-------------------------------------------------------------------------------------------------
    */

   /**
    * Bulk Post Upload
    */
   public function bulkPost()
   {
      if (user()->toArray()['is_superadmin'] != '1') {
         return redirect()->to('admin');
      }

      $data['title'] = 'Import Petugas';
      $data['ctx'] = 'petugas';
      $data['guru'] = $this->guruModel->getAllGuru();

      return view('admin/petugas/import-petugas', $data);
   }

   /**
    * Generate CSV Object Post
    */
   public function generateCSVObjectPost()
   {
      //delete old txt files
      $files = glob(FCPATH . 'uploads/tmp/*.txt');
      if (!empty($files)) {
         foreach ($files as $item) {
            @unlink($item);
         }
      }
      $file = $this->uploadModel->uploadCSVFile('file');
      if (!empty($file) && !empty($file['path'])) {
         $obj = $this->petugasModel->generateCSVObject($file['path']);
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
      try {
          $petugas = $this->petugasModel->importCSVItem($txtFileName, $index);
          if (!empty($petugas)) {
             $data = [
                'result' => 1,
                'petugas' => $petugas,
                'index' => $index
             ];
             echo json_encode($data);
          } else {
             $data = [
                'result' => 0,
                'index' => $index,
                'message' => 'Duplicate or invalid data'
             ];
             echo json_encode($data);
          }
      } catch (\Exception $e) {
          $data = [
             'result' => 0,
             'index' => $index,
             'message' => 'Error: ' . $e->getMessage()
          ];
          echo json_encode($data);
      }
   }

   /**
    * Download CSV File Post
    */
   public function downloadCSVFilePost()
   {
      $file = FCPATH . 'assets/file/csv_petugas_template.csv';
      if(file_exists($file)){
          return $this->response->download($file, null);
      }
      return redirect()->back()->with('error', 'Template file not found.');
   }
}
