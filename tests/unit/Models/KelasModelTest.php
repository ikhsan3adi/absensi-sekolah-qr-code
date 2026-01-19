<?php

namespace Tests\Unit\Models;

use App\Models\KelasModel;
use App\Models\JurusanModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

final class KelasModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = null;

    protected KelasModel $model;
    protected JurusanModel $jurusanModel;
    protected $testJurusanId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new KelasModel();
        $this->jurusanModel = new JurusanModel();
        
        $this->db->table('tb_jurusan')->insert([
            'jurusan' => 'Teknik Komputer Jaringan',
        ]);
        $this->testJurusanId = $this->db->insertID();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->db->table('tb_kelas')->delete('1 = 1');
        $this->db->table('tb_jurusan')->delete('1 = 1');
    }

    public function testGenerateCSVObjectSuccessfully(): void
    {
        $csvContent = "tingkat,jurusan,index_kelas\n" .
                      "X,Teknik Komputer Jaringan,1\n" .
                      "XI,Teknik Komputer Jaringan,2\n";

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $tempFile = $tmpDir . 'test_kelas_' . uniqid() . '.csv';
        file_put_contents($tempFile, $csvContent);

        $result = $this->model->generateCSVObject($tempFile);

        $this->assertNotEmpty($result);
        $this->assertEquals(2, $result->numberOfItems);
        $this->assertNotEmpty($result->txtFileName);

        $txtFile = $tmpDir . $result->txtFileName;
        $this->assertFileExists($txtFile);

        @unlink($txtFile);
    }

    public function testGenerateCSVObjectWithBOM(): void
    {
        $csvContent = "\xEF\xBB\xBFtingkat,jurusan,index_kelas\n" .
                      "X,Teknik Komputer Jaringan,1\n";

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $tempFile = $tmpDir . 'test_kelas_bom_' . uniqid() . '.csv';
        file_put_contents($tempFile, $csvContent);

        $result = $this->model->generateCSVObject($tempFile);

        $this->assertNotEmpty($result);
        $this->assertEquals(1, $result->numberOfItems);

        $txtFile = FCPATH . 'uploads/tmp/' . $result->txtFileName;
        @unlink($txtFile);
    }

    public function testImportCSVItemSuccessfully(): void
    {
        $testData = [
            [
                'tingkat' => 'X',
                'jurusan' => 'Teknik Komputer Jaringan',
                'index_kelas' => '1'
            ]
        ];

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $txtFileName = 'test_' . uniqid() . '.txt';
        $txtFile = fopen($tmpDir . $txtFileName, 'w');
        fwrite($txtFile, serialize($testData));
        fclose($txtFile);

        $result = $this->model->importCSVItem($txtFileName, 1);

        $this->assertNotEmpty($result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('X', $result['data']['tingkat']);
        $this->assertEquals('1', $result['data']['index_kelas']);

        $kelas = $this->db->table('tb_kelas')
            ->where('tingkat', 'X')
            ->where('id_jurusan', $this->testJurusanId)
            ->where('index_kelas', '1')
            ->get()
            ->getRowArray();

        $this->assertNotNull($kelas);

        @unlink($tmpDir . $txtFileName);
    }

    public function testImportCSVItemDuplicateHandling(): void
    {
        $this->db->table('tb_kelas')->insert([
            'tingkat' => 'X',
            'id_jurusan' => $this->testJurusanId,
            'index_kelas' => '1'
        ]);

        $testData = [
            [
                'tingkat' => 'X',
                'jurusan' => 'Teknik Komputer Jaringan',
                'index_kelas' => '1'
            ]
        ];

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $txtFileName = 'test_dup_' . uniqid() . '.txt';
        $txtFile = fopen($tmpDir . $txtFileName, 'w');
        fwrite($txtFile, serialize($testData));
        fclose($txtFile);

        $result = $this->model->importCSVItem($txtFileName, 1);

        $this->assertNotEmpty($result);
        $this->assertEquals('duplicate', $result['status']);

        @unlink($tmpDir . $txtFileName);
    }

    public function testImportCSVItemWithMultipleRecords(): void
    {
        $testData = [
            [
                'tingkat' => 'X',
                'jurusan' => 'Teknik Komputer Jaringan',
                'index_kelas' => '1'
            ],
            [
                'tingkat' => 'XI',
                'jurusan' => 'Teknik Komputer Jaringan',
                'index_kelas' => '2'
            ]
        ];

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $txtFileName = 'test_multi_' . uniqid() . '.txt';
        $txtFile = fopen($tmpDir . $txtFileName, 'w');
        fwrite($txtFile, serialize($testData));
        fclose($txtFile);

        $result = $this->model->importCSVItem($txtFileName, 2);

        $this->assertNotEmpty($result);
        $this->assertEquals('XI', $result['data']['tingkat']);
        $this->assertEquals('2', $result['data']['index_kelas']);

        @unlink($tmpDir . $txtFileName);
    }

    public function testGenerateCSVObjectWithEmptyFile(): void
    {
        $csvContent = "tingkat,jurusan,index_kelas\n";

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $tempFile = $tmpDir . 'test_empty_' . uniqid() . '.csv';
        file_put_contents($tempFile, $csvContent);

        $result = $this->model->generateCSVObject($tempFile);

        $this->assertFalse($result);
    }
}
