<?php

namespace Tests\Unit\Models;

use App\Models\JurusanModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

final class JurusanModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = null;

    protected JurusanModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new JurusanModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->db->table('tb_jurusan')->delete('1 = 1');
    }

    public function testGenerateCSVObjectSuccessfully(): void
    {
        $csvContent = "jurusan\n" .
                      "Rekayasa Perangkat Lunak\n" .
                      "Teknik Komputer Jaringan\n";

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $tempFile = $tmpDir . 'test_jurusan_' . uniqid() . '.csv';
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
        $csvContent = "\xEF\xBB\xBFjurusan\n" .
                      "Rekayasa Perangkat Lunak\n";

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $tempFile = $tmpDir . 'test_jurusan_bom_' . uniqid() . '.csv';
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
                'jurusan' => 'Rekayasa Perangkat Lunak'
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
        $this->assertEquals('Rekayasa Perangkat Lunak', $result['data']['jurusan']);

        $jurusan = $this->db->table('tb_jurusan')
            ->where('jurusan', 'Rekayasa Perangkat Lunak')
            ->get()
            ->getRowArray();

        $this->assertNotNull($jurusan);
        $this->assertEquals('Rekayasa Perangkat Lunak', $jurusan['jurusan']);

        @unlink($tmpDir . $txtFileName);
    }

    public function testImportCSVItemDuplicateHandling(): void
    {
        $this->db->table('tb_jurusan')->insert([
            'jurusan' => 'Rekayasa Perangkat Lunak'
        ]);

        $testData = [
            [
                'jurusan' => 'Rekayasa Perangkat Lunak'
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
                'jurusan' => 'Rekayasa Perangkat Lunak'
            ],
            [
                'jurusan' => 'Teknik Komputer Jaringan'
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
        $this->assertEquals('Teknik Komputer Jaringan', $result['data']['jurusan']);

        @unlink($tmpDir . $txtFileName);
    }

    public function testGenerateCSVObjectWithEmptyFile(): void
    {
        $csvContent = "jurusan\n";

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
