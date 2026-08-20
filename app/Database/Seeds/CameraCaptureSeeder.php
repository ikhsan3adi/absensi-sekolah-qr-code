<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Seeder;
use Config\Database;

/**
 * CameraCaptureSeeder
 *
 * Mengisi tabel tb_camera_capture dengan data contoh (dummy) foto wajah siswa/guru.
 * Seeder ini hanya untuk keperluan development/testing.
 * File gambar fisik tidak dibuat — hanya metadata di database.
 *
 * Cara menjalankan:
 *   php spark db:seed CameraCaptureSeeder
 */
class CameraCaptureSeeder extends Seeder
{
    private \Faker\Generator $faker;
    private array $siswaList;
    private array $guruList;
    private array $userList;

    public function __construct(Database $config, ?BaseConnection $db = null)
    {
        parent::__construct($config, $db);
        $this->faker = \Faker\Factory::create('id_ID');

        // Ambil data siswa, guru, dan user yang ada
        $this->siswaList = $this->db->table('tb_siswa')->select('id_siswa, nama_siswa')->get()->getResultArray();
        $this->guruList  = $this->db->table('tb_guru')->select('id_guru, nama_guru')->get()->getResultArray();
        $this->userList  = $this->db->table('users')->select('id')->where('deleted_at IS NULL')->get()->getResultArray();
    }

    public function run()
    {
        // Kosongkan tabel dulu sebelum seeding
        $this->db->table('tb_camera_capture')->truncate();

        $records = [];

        // Seed 10 foto siswa
        if (!empty($this->siswaList)) {
            for ($i = 0; $i < min(10, count($this->siswaList)); $i++) {
                $siswa     = $this->faker->randomElement($this->siswaList);
                $capturedBy = !empty($this->userList)
                    ? $this->faker->randomElement($this->userList)['id']
                    : null;

                $filename  = 'cam_demo_' . $this->faker->numerify('##########') . '_' . bin2hex(random_bytes(4)) . '.jpg';
                $records[] = [
                    'filename'     => $filename,
                    'filepath'     => 'writable/faces/' . $filename,
                    'entity_type'  => 'siswa',
                    'entity_id'    => $siswa['id_siswa'],
                    'entity_name'  => $siswa['nama_siswa'],
                    'captured_by'  => $capturedBy,
                    'keterangan'   => $this->faker->randomElement([
                        'Verifikasi wajah masuk',
                        'Verifikasi wajah pulang',
                        'Foto wajah awal pendaftaran',
                        null,
                    ]),
                    'created_at'   => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                    'deleted_at'   => null,
                ];
            }
        }

        // Seed 5 foto guru
        if (!empty($this->guruList)) {
            for ($i = 0; $i < min(5, count($this->guruList)); $i++) {
                $guru      = $this->faker->randomElement($this->guruList);
                $capturedBy = !empty($this->userList)
                    ? $this->faker->randomElement($this->userList)['id']
                    : null;

                $filename  = 'cam_demo_' . $this->faker->numerify('##########') . '_' . bin2hex(random_bytes(4)) . '.jpg';
                $records[] = [
                    'filename'     => $filename,
                    'filepath'     => 'writable/faces/' . $filename,
                    'entity_type'  => 'guru',
                    'entity_id'    => $guru['id_guru'],
                    'entity_name'  => $guru['nama_guru'],
                    'captured_by'  => $capturedBy,
                    'keterangan'   => $this->faker->randomElement([
                        'Verifikasi wajah masuk',
                        'Verifikasi wajah pulang',
                        'Foto wajah awal pendaftaran',
                        null,
                    ]),
                    'created_at'   => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                    'deleted_at'   => null,
                ];
            }
        }

        // Seed 3 foto umum (tanpa entity)
        for ($i = 0; $i < 3; $i++) {
            $capturedBy = !empty($this->userList)
                ? $this->faker->randomElement($this->userList)['id']
                : null;

            $filename  = 'cam_demo_' . $this->faker->numerify('##########') . '_' . bin2hex(random_bytes(4)) . '.jpg';
            $records[] = [
                'filename'     => $filename,
                'filepath'     => 'writable/faces/' . $filename,
                'entity_type'  => 'umum',
                'entity_id'    => null,
                'entity_name'  => null,
                'captured_by'  => $capturedBy,
                'keterangan'   => $this->faker->randomElement([
                    'Foto dokumentasi',
                    'Foto umum',
                    null,
                ]),
                'created_at'   => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
                'deleted_at'   => null,
            ];
        }

        if (!empty($records)) {
            $this->db->table('tb_camera_capture')->insertBatch($records);
            echo "CameraCaptureSeeder: " . count($records) . " record berhasil di-seed.\n";
        } else {
            echo "CameraCaptureSeeder: Tidak ada data siswa/guru ditemukan. Hanya 3 foto umum yang di-seed.\n";
        }
    }
}
