<?php

namespace App\Controllers\Admin;

use App\Models\JurusanModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;

class JurusanController extends ResourceController
{
    protected JurusanModel $jurusanModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $kelasController = new KelasController();
        return $kelasController->index();
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $result = $this->jurusanModel->findAll();

        $data = [
            'data' => $result,
            'empty' => empty($result)
        ];

        return view('admin/jurusan/list_jurusan', $data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        $data = [
            'ctx' => 'kelas',
            'title' => 'Tambah Data Jurusan',
        ];
        return view('/admin/jurusan/create', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        if (!$this->validate([
            'jurusan' => [
                'rules' => 'required|max_length[32]|is_unique[tb_jurusan.jurusan]',
            ],
        ])) {
            $data = [
                'ctx' => 'kelas',
                'title' => 'Tambah Data Jurusan',
                'validation' => $this->validator,
                'oldInput' => $this->request->getVar()
            ];
            return view('/admin/jurusan/new', $data);
        }

        // ambil variabel POST
        $jurusan = $this->request->getVar('jurusan');

        $result = $this->jurusanModel->insert(['jurusan' => $jurusan]);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Tambah data berhasil',
                'error' => false
            ]);
            return redirect()->to('/admin/jurusan');
        }

        session()->setFlashdata([
            'msg' => 'Gagal menambah data',
            'error' => true
        ]);
        return redirect()->to('/admin/jurusan/new');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $jurusan = $this->jurusanModel->where(['id' => $id])->first();

        if (!$jurusan) {
            throw new PageNotFoundException('Data jurusan dengan id ' . $id . ' tidak ditemukan');
        }

        $data = [
            'ctx' => 'kelas',
            'data' => $jurusan,
            'title' => 'Edit Jurusan',
        ];
        return view('/admin/jurusan/edit', $data);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $jurusan = $this->jurusanModel->where(['id' => $id])->first();

        // ambil variabel POST
        $namaJurusan = $this->request->getRawInputVar('jurusan');

        if ($jurusan['jurusan'] != $namaJurusan && !$this->validate([
            'jurusan' => [
                'rules' => 'required|max_length[32]|is_unique[tb_jurusan.jurusan]',
            ],
        ])) {
            if (!$jurusan) {
                throw new PageNotFoundException('Data jurusan dengan id ' . $id . ' tidak ditemukan');
            }

            $data = [
                'ctx' => 'kelas',
                'title' => 'Edit Jurusan',
                'data' => $jurusan,
                'validation' => $this->validator,
                'oldInput' => $this->request->getRawInput()
            ];
            return view('/admin/jurusan/edit', $data);
        }

        $result = $this->jurusanModel->update($id, [
            'jurusan' => $namaJurusan
        ]);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Edit data berhasil',
                'error' => false
            ]);
            return redirect()->to('/admin/jurusan');
        }

        session()->setFlashdata([
            'msg' => 'Gagal mengubah data',
            'error' => true
        ]);
        return redirect()->to('/admin/jurusan/' . $id . '/edit');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $result = $this->jurusanModel->delete($id);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Data berhasil dihapus',
                'error' => false
            ]);
            return redirect()->to('/admin/jurusan');
        }

        session()->setFlashdata([
            'msg' => 'Gagal menghapus data',
            'error' => true
        ]);
        return redirect()->to('/admin/jurusan');
    }
}
