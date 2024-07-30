<?php

namespace App\Controllers\Admin;

use App\Models\JurusanModel;
use App\Models\KelasModel;
use App\Controllers\BaseController;

class KelasController extends BaseController
{
    protected KelasModel $kelasModel;

    protected JurusanModel $jurusanModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->kelasModel = new KelasModel();
        $this->jurusanModel = new JurusanModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $data = [
            'title' => 'Kelas & Jurusan',
            'ctx' => 'kelas',
        ];

        return view('admin/kelas/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function listData()
    {
        $vars['data'] = $this->kelasModel->getDataKelas();
        $htmlContent = '';
        if (!empty($vars['data'])) {
            $htmlContent = view('admin/kelas/list-kelas', $vars);
            $data = [
                'result' => 1,
                'htmlContent' => $htmlContent,
            ];
            echo json_encode($data);
        } else {
            echo json_encode(['result' => 0]);
        }
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function tambahKelas()
    {
        $data['ctx'] = 'kelas';
        $data['title'] = 'Tambah Data Kelas';
        $data['jurusan'] = $this->jurusanModel->findAll();

        return view('/admin/kelas/create', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function tambahKelasPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('kelas', 'Kelas', 'required|max_length[32]');
        $val->setRule('id_jurusan', 'Jurusan', 'required|numeric');

        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to('admin/kelas/tambah')->withInput();
        } else {
            if ($this->kelasModel->addKelas()) {
                $this->session->setFlashdata('success', 'Tambah data berhasil');
                return redirect()->to('admin/kelas');
            } else {
                $this->session->setFlashdata('error', 'Gagal menambah data');
                return redirect()->to('admin/kelas/tambah')->withInput();
            }
        }

        return redirect()->to('admin/kelas/tambah');
    }

    /**
     * Return a resource object, with default properties
     *
     * @return mixed
     */
    public function editKelas($id)
    {
        $data['title'] = 'Edit Kelas';
        $data['ctx'] = 'kelas';
        $data['jurusan'] = $this->jurusanModel->findAll();
        $data['kelas'] = $this->kelasModel->getKelas($id);
        if (empty($data['kelas'])) {
            return redirect()->to('admin/kelas');
        }

        return view('/admin/kelas/edit', $data);
    }

    /**
     * Edit a resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function editKelasPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('kelas', 'Kelas', 'required|max_length[32]');
        $val->setRule('id_jurusan', 'Jurusan', 'required|numeric');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back();
        } else {
            $id = inputPost('id');
            if ($this->kelasModel->editKelas($id)) {
                $this->session->setFlashdata('success', 'Edit data berhasil');
                return redirect()->to('admin/kelas');
            } else {
                $this->session->setFlashdata('error', 'Gagal Mengubah data');
            }
        }
        return redirect()->to('admin/kelas/edit/' . cleanNumber($id));
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function deleteKelasPost($id = null)
    {
        $id = inputPost('id');
        $kelas = $this->kelasModel->getKelas($id);
        if (!empty($kelas)) {
            $siswaModel = new \App\Models\SiswaModel();
            if (!empty($siswaModel->getSiswaCountByKelas($id))) {
                $this->session->setFlashdata('error', 'Kelas Masih Memiliki Siswa Aktif');
                exit();
            }
            if ($this->kelasModel->deleteKelas($id)) {
                $this->session->setFlashdata('success', 'Data berhasil dihapus');
            } else {
                $this->session->setFlashdata('error', 'Gagal menghapus data');
            }
        }
    }
}
