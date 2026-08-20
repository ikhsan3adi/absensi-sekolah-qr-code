<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CameraCaptureModel;
use App\Models\SiswaModel;
use App\Models\GuruModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CameraCapture Controller (Admin)
 *
 * Mengelola CRUD foto wajah siswa/guru yang diambil melalui kamera browser.
 * File gambar disimpan di writable/faces/, metadata di tabel tb_camera_capture.
 *
 * Routes (terdaftar di Config/Routes.php):
 *   GET  admin/camera-capture          → index()
 *   GET  admin/camera-capture/create   → create()
 *   POST admin/camera-capture/store    → store()    [JSON API]
 *   GET  admin/camera-capture/(:num)   → show($id)
 *   GET  admin/camera-capture/image/(:num) → serveImage($id) [proxy gambar]
 *   DELETE admin/camera-capture/delete/(:num) → delete($id)
 */
class CameraCapture extends BaseController
{
    protected CameraCaptureModel $model;
    protected SiswaModel $siswaModel;
    protected GuruModel $guruModel;

    public function __construct()
    {
        $this->model      = new CameraCaptureModel();
        $this->siswaModel = new SiswaModel();
        $this->guruModel  = new GuruModel();
    }

    // ──────────────────────────────────────────────
    // INDEX – Daftar semua foto yang tersimpan
    // ──────────────────────────────────────────────

    public function index(): string
    {
        $entityType = $this->request->getVar('type') ?? null;

        $data = [
            'title'       => 'Camera Capture – Foto Wajah',
            'ctx'         => 'camera-capture',
            'captures'    => $this->model->getAllCaptures($entityType),
            'counts'      => $this->model->countByType(),
            'activeType'  => $entityType,
        ];

        return view('admin/camera-capture/index', $data);
    }

    // ──────────────────────────────────────────────
    // CREATE – Form pengambilan foto lewat webcam
    // ──────────────────────────────────────────────

    public function create(): string
    {
        $data = [
            'title'     => 'Ambil Foto Wajah',
            'ctx'       => 'camera-capture',
            'siswaList' => $this->siswaModel->orderBy('nama_siswa')->findAll(),
            'guruList'  => $this->guruModel->orderBy('nama_guru')->findAll(),
        ];

        return view('admin/camera-capture/create', $data);
    }

    // ──────────────────────────────────────────────
    // STORE – Simpan foto (JSON API, dipanggil dari JS)
    // ──────────────────────────────────────────────

    public function store(): ResponseInterface
    {
        $json = $this->request->getJSON(true);

        // Ambil data dari JSON body
        $base64Image = $json['image']       ?? null;
        $entityType  = $json['entity_type'] ?? 'umum';
        $entityId    = $json['entity_id']   ?? null;
        $entityName  = $json['entity_name'] ?? null;
        $keterangan  = $json['keterangan']  ?? null;

        // Validasi input
        if (empty($base64Image)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Data gambar tidak boleh kosong.',
            ]);
        }

        if (!in_array($entityType, ['siswa', 'guru', 'umum'], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Tipe entitas tidak valid.',
            ]);
        }

        // Jika entity siswa/guru, ambil nama dari DB untuk konsistensi
        if ($entityType === 'siswa' && !empty($entityId)) {
            $siswa = $this->siswaModel->find((int) $entityId);
            if ($siswa) {
                $entityName = $siswa['nama_siswa'];
            }
        } elseif ($entityType === 'guru' && !empty($entityId)) {
            $guru = $this->guruModel->find((int) $entityId);
            if ($guru) {
                $entityName = $guru['nama_guru'];
            }
        }

        try {
            $insertId = $this->model->createCapture($base64Image, [
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'entity_name' => $entityName,
                'captured_by' => auth()->id(),
                'keterangan'  => $keterangan,
            ]);

            if ($insertId === false) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data ke database.',
                ]);
            }

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Foto berhasil disimpan.',
                'id'        => $insertId,
                'image_url' => base_url('admin/camera-capture/image/' . $insertId),
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\RuntimeException $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // ──────────────────────────────────────────────
    // SHOW – Detail / preview satu foto
    // ──────────────────────────────────────────────

    public function show(int $id): string
    {
        $capture = $this->model->getCapture($id);

        if (empty($capture)) {
            throw new PageNotFoundException("Data camera capture dengan ID {$id} tidak ditemukan.");
        }

        $data = [
            'title'   => 'Detail Foto Wajah',
            'ctx'     => 'camera-capture',
            'capture' => $capture,
        ];

        return view('admin/camera-capture/show', $data);
    }

    // ──────────────────────────────────────────────
    // IMAGE – Serve file gambar dari writable/faces/ (proxy)
    // ──────────────────────────────────────────────

    public function serveImage(int $id): ResponseInterface
    {
        $capture = $this->model->where('id', $id)->first();

        if (empty($capture)) {
            return $this->response->setStatusCode(404)->setBody('Gambar tidak ditemukan.');
        }

        $imageData = $this->model->readImageFile($capture);

        if ($imageData === false) {
            // Kembalikan placeholder jika file tidak ada
            return $this->response->setStatusCode(404)->setBody('File gambar tidak ditemukan di server.');
        }

        return $this->response
            ->setHeader('Content-Type', 'image/jpeg')
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setBody($imageData);
    }

    // ──────────────────────────────────────────────
    // DELETE – Hapus foto (file + record DB)
    // ──────────────────────────────────────────────

    public function delete(int $id): ResponseInterface
    {
        $capture = $this->model->getCapture($id);

        if (empty($capture)) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ]);
        }

        $result = $this->model->deleteCapture($id);

        if ($result) {
            session()->setFlashdata([
                'msg'   => 'Foto berhasil dihapus.',
                'error' => false,
            ]);
        } else {
            session()->setFlashdata([
                'msg'   => 'Gagal menghapus foto.',
                'error' => true,
            ]);
        }

        return $this->response->setJSON([
            'success' => $result,
            'message' => $result ? 'Foto berhasil dihapus.' : 'Gagal menghapus foto.',
        ]);
    }
}