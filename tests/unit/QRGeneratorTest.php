<?php

use App\Controllers\Admin\QRGenerator;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class QRGeneratorTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    // For Migrations
    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db->table('tb_jurusan')->insert([
            'jurusan' => 'Z',
        ]);

        $this->db->table('tb_kelas')->insert([
            'kelas' => 'Z',
            'id_jurusan' => $this->db->table('tb_jurusan')->get()->getFirstRow()->id,
        ]);

        $this->db->table('tb_siswa')->insert([
            'nis' => '1234567890',
            'nama_siswa' => 'John Doe',
            'id_kelas' => $kelasId ?? 1,
            'no_hp' => '081234567890',
            'unique_code' => '1234567890',
        ]);
    }

    public function testGenerateQrCode(): void
    {
        $kelas = (new KelasModel)->first();
        $siswa = (new SiswaModel)->where('id_kelas', $kelas['id_kelas'])->first();

        $result = (new QRGenerator(qrCodeFilePath: "qr-siswa/test/"))->generate(
            $siswa['nama_siswa'],
            $siswa['nis'],
            $siswa['unique_code']
        );

        $this->assertIsString($result);
        $this->assertStringContainsString('public/uploads/qr-siswa/test/', $result);
        $this->assertStringContainsString('.png', $result);
    }
}
