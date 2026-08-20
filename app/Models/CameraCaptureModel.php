<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CameraCaptureModel
 *
 * Model untuk menyimpan metadata foto wajah siswa/guru yang diambil melalui kamera browser.
 * File gambar fisik disimpan di writable/faces/, metadata tersimpan di tabel tb_camera_capture.
 */
class CameraCaptureModel extends Model
{
    protected $table         = 'tb_camera_capture';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowedFields = [
        'filename',
        'filepath',
        'entity_type',
        'entity_id',
        'entity_name',
        'captured_by',
        'keterangan',
    ];

    // Kualitas output gambar
    protected int $jpgQuality  = 85;
    protected int $webpQuality = 80;
    protected string $imgExt   = '.jpg';

    // ──────────────────────────────────────────────
    // READ
    // ──────────────────────────────────────────────

    /**
     * Ambil semua data capture dengan info user yang mengambil.
     */
    public function getAllCaptures(?string $entityType = null): array
    {
        $builder = $this->select('tb_camera_capture.*, users.username as captured_by_username')
            ->join('users', 'users.id = tb_camera_capture.captured_by', 'LEFT')
            ->orderBy('tb_camera_capture.created_at', 'DESC');

        if (!empty($entityType)) {
            $builder->where('tb_camera_capture.entity_type', $entityType);
        }

        return $builder->findAll();
    }

    /**
     * Ambil satu data capture berdasarkan ID.
     */
    public function getCapture(int $id): ?array
    {
        return $this->select('tb_camera_capture.*, users.username as captured_by_username')
            ->join('users', 'users.id = tb_camera_capture.captured_by', 'LEFT')
            ->where('tb_camera_capture.id', $id)
            ->first();
    }

    /**
     * Ambil semua foto milik satu entitas (misalnya semua foto siswa tertentu).
     */
    public function getCapturesByEntity(string $entityType, int $entityId): array
    {
        return $this->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Hitung total capture per entity_type.
     */
    public function countByType(): array
    {
        $result = $this->db->table($this->table)
            ->select('entity_type, COUNT(*) as total')
            ->where('deleted_at IS NULL')
            ->groupBy('entity_type')
            ->get()
            ->getResultArray();

        $counts = ['siswa' => 0, 'guru' => 0, 'umum' => 0];
        foreach ($result as $row) {
            $counts[$row['entity_type']] = (int) $row['total'];
        }
        return $counts;
    }

    // ──────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────

    /**
     * Simpan gambar base64 ke file dan insert metadata ke database.
     *
     * @param string $base64Image  Data URI gambar (data:image/jpeg;base64,...)
     * @param array  $meta         Metadata: entity_type, entity_id, entity_name, captured_by, keterangan
     * @return int|false           Insert ID jika berhasil, false jika gagal
     * @throws \InvalidArgumentException Jika data base64 tidak valid
     * @throws \RuntimeException         Jika gagal menulis file
     */
    public function createCapture(string $base64Image, array $meta): int|false
    {
        // Hapus prefix data URI jika ada
        $raw = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);

        // Validasi karakter base64
        if (!preg_match('/^[a-zA-Z0-9\/\+=]+$/', $raw)) {
            throw new \InvalidArgumentException('Data gambar tidak valid (bukan base64 yang valid).');
        }

        $imageData = base64_decode($raw, true);
        if ($imageData === false) {
            throw new \InvalidArgumentException('Gagal men-decode data base64.');
        }

        // Pastikan direktori penyimpanan ada
        $storageDir = WRITEPATH . 'faces';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        // Buat nama file unik
        $filename = 'cam_' . time() . '_' . bin2hex(random_bytes(5)) . $this->imgExt;
        $fullPath = $storageDir . DIRECTORY_SEPARATOR . $filename;

        // Tulis file ke disk
        $written = file_put_contents($fullPath, $imageData);
        if ($written === false) {
            throw new \RuntimeException('Gagal menyimpan file gambar ke direktori penyimpanan.');
        }

        // Insert metadata ke database
        $insertData = [
            'filename'    => $filename,
            'filepath'    => 'writable/faces/' . $filename,
            'entity_type' => $meta['entity_type'] ?? 'umum',
            'entity_id'   => !empty($meta['entity_id']) ? (int) $meta['entity_id'] : null,
            'entity_name' => $meta['entity_name'] ?? null,
            'captured_by' => $meta['captured_by'] ?? null,
            'keterangan'  => $meta['keterangan'] ?? null,
        ];

        $this->insert($insertData);
        $insertId = $this->getInsertID();

        return $insertId ?: false;
    }

    // ──────────────────────────────────────────────
    // DELETE
    // ──────────────────────────────────────────────

    /**
     * Hapus record dari database sekaligus hapus file fisik dari disk.
     */
    public function deleteCapture(int $id): bool
    {
        $record = $this->where('id', $id)->first();
        if (empty($record)) {
            return false;
        }

        // Hapus file fisik
        $fullPath = WRITEPATH . 'faces' . DIRECTORY_SEPARATOR . $record['filename'];
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }

        // Soft delete record di database
        return (bool) $this->delete($id);
    }

    // ──────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────

    /**
     * Ambil URL publik gambar (relative dari base_url).
     * Karena file disimpan di writable/ (non-public), kita buat endpoint proxy.
     */
    public function getImageUrl(array $record): string
    {
        return base_url('admin/camera-capture/image/' . $record['id']);
    }

    /**
     * Baca file gambar sebagai string binary.
     */
    public function readImageFile(array $record): string|false
    {
        $fullPath = WRITEPATH . 'faces' . DIRECTORY_SEPARATOR . $record['filename'];
        if (!is_file($fullPath)) {
            return false;
        }
        return file_get_contents($fullPath);
    }

    /**
     * Getter kualitas JPG (untuk dipakai di controller jika perlu).
     */
    public function getJpgQuality(): int
    {
        return $this->jpgQuality;
    }
}
