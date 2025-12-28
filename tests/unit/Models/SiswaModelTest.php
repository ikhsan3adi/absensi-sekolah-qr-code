<?php

namespace Tests\Unit\Models;

use App\Models\SiswaModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class SiswaModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected SiswaModel $model;
    protected $testKelasId;
    protected $testJurusanId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new SiswaModel();
        
        // Setup test data
        $this->db->table('tb_jurusan')->insert([
            'jurusan' => 'IPA',
        ]);
        
        $this->testJurusanId = $this->db->insertID();
        
        $this->db->table('tb_kelas')->insert([
            'tingkat' => '10',
            'id_jurusan' => $this->testJurusanId,
            'index_kelas' => 'A',
        ]);
        
        $this->testKelasId = $this->db->insertID();
    }

    // =====================================================
    // HAPPY PATH TESTS - CREATE OPERATIONS
    // =====================================================

    public function testCreateSiswaSuccessfully(): void
    {
        $result = $this->model->createSiswa(
            '1001',
            'John Doe',
            $this->testKelasId,
            'L',
            '08123456789'
        );
        
        $this->assertTrue($result);
        
        $siswa = $this->db->table('tb_siswa')
            ->where('nis', '1001')
            ->get()
            ->getRowArray();
        
        $this->assertNotNull($siswa);
        $this->assertEquals('John Doe', $siswa['nama_siswa']);
        $this->assertEquals($this->testKelasId, $siswa['id_kelas']);
        $this->assertEquals('L', $siswa['jenis_kelamin']);
        $this->assertEquals('08123456789', $siswa['no_hp']);
        $this->assertNotEmpty($siswa['unique_code']);
    }

    public function testCreateSiswaWithRfid(): void
    {
        $result = $this->model->createSiswa(
            '1002',
            'Jane Doe',
            $this->testKelasId,
            'P',
            '08123456788',
            'RFID123456'
        );
        
        $this->assertTrue($result);
        
        $siswa = $this->db->table('tb_siswa')
            ->where('nis', '1002')
            ->get()
            ->getRowArray();
        
        $this->assertEquals('RFID123456', $siswa['rfid_code']);
    }

    // =====================================================
    // HAPPY PATH TESTS - READ OPERATIONS
    // =====================================================

    public function testCekSiswaByUniqueCode(): void
    {
        $this->model->createSiswa(
            '1001',
            'John Doe',
            $this->testKelasId,
            'L',
            '08123456789'
        );
        
        $siswa = $this->db->table('tb_siswa')
            ->where('nis', '1001')
            ->get()
            ->getRowArray();
        
        $uniqueCode = $siswa['unique_code'];
        
        $result = $this->model->cekSiswa($uniqueCode);
        
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result['nama_siswa']);
        $this->assertArrayHasKey('kelas', $result);
        $this->assertArrayHasKey('jurusan', $result);
    }

    public function testCekSiswaByRfidCode(): void
    {
        $this->model->createSiswa(
            '1001',
            'John Doe',
            $this->testKelasId,
            'L',
            '08123456789',
            'RFID123456'
        );
        
        $result = $this->model->cekSiswa('RFID123456');
        
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result['nama_siswa']);
        $this->assertEquals('RFID123456', $result['rfid_code']);
    }

    public function testGetSiswaById(): void
    {
        $this->model->createSiswa(
            '1001',
            'John Doe',
            $this->testKelasId,
            'L',
            '08123456789'
        );
        
        $siswa = $this->db->table('tb_siswa')
            ->where('nis', '1001')
            ->get()
            ->getRowArray();
        
        $result = $this->model->getSiswaById($siswa['id_siswa']);
        
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result['nama_siswa']);
    }

    public function testGetAllSiswaWithKelas(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        $this->model->createSiswa('1002', 'Jane Doe', $this->testKelasId, 'P', '08123456788');
        
        $result = $this->model->getAllSiswaWithKelas();
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('kelas', $result[0]);
    }

    public function testGetAllSiswaWithKelasFilterByTingkat(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        
        // Create another class with different tingkat
        $this->db->table('tb_kelas')->insert([
            'tingkat' => '11',
            'id_jurusan' => $this->testJurusanId,
            'index_kelas' => 'A',
        ]);
        $kelasId2 = $this->db->insertID();
        
        $this->model->createSiswa('1002', 'Jane Doe', $kelasId2, 'P', '08123456788');
        
        $result = $this->model->getAllSiswaWithKelas('10', null);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['nama_siswa']);
    }

    public function testGetAllSiswaWithKelasFilterByJurusan(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        
        // Create another jurusan
        $this->db->table('tb_jurusan')->insert(['jurusan' => 'IPS']);
        $jurusanId2 = $this->db->insertID();
        
        $this->db->table('tb_kelas')->insert([
            'tingkat' => '10',
            'id_jurusan' => $jurusanId2,
            'index_kelas' => 'B',
        ]);
        $kelasId2 = $this->db->insertID();
        
        $this->model->createSiswa('1002', 'Jane Doe', $kelasId2, 'P', '08123456788');
        
        $result = $this->model->getAllSiswaWithKelas(null, 'IPA');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['nama_siswa']);
    }

    public function testGetSiswaByKelas(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        $this->model->createSiswa('1002', 'Jane Doe', $this->testKelasId, 'P', '08123456788');
        
        $result = $this->model->getSiswaByKelas($this->testKelasId);
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetSiswaCountByKelas(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        $this->model->createSiswa('1002', 'Jane Doe', $this->testKelasId, 'P', '08123456788');
        
        $result = $this->model->getSiswaCountByKelas($this->testKelasId);
        
        $this->assertEquals(2, $result);
    }

    // =====================================================
    // HAPPY PATH TESTS - UPDATE OPERATIONS
    // =====================================================

    public function testUpdateSiswa(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        
        $siswa = $this->db->table('tb_siswa')
            ->where('nis', '1001')
            ->get()
            ->getRowArray();
        
        $result = $this->model->updateSiswa(
            $siswa['id_siswa'],
            '1001',
            'John Updated',
            $this->testKelasId,
            'L',
            '08123456780'
        );
        
        $this->assertTrue($result);
        
        $updatedSiswa = $this->model->getSiswaById($siswa['id_siswa']);
        
        $this->assertEquals('John Updated', $updatedSiswa['nama_siswa']);
        $this->assertEquals('08123456780', $updatedSiswa['no_hp']);
    }

    public function testUpdateSiswaWithRfid(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        
        $siswa = $this->db->table('tb_siswa')
            ->where('nis', '1001')
            ->get()
            ->getRowArray();
        
        $result = $this->model->updateSiswa(
            $siswa['id_siswa'],
            '1001',
            'John Doe',
            $this->testKelasId,
            'L',
            '08123456789',
            'RFID789456'
        );
        
        $this->assertTrue($result);
        
        $updatedSiswa = $this->model->getSiswaById($siswa['id_siswa']);
        
        $this->assertEquals('RFID789456', $updatedSiswa['rfid_code']);
    }

    // =====================================================
    // HAPPY PATH TESTS - DELETE OPERATIONS
    // =====================================================

    public function testDeleteSiswa(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        
        $siswa = $this->db->table('tb_siswa')
            ->where('nis', '1001')
            ->get()
            ->getRowArray();
        
        $result = $this->model->deleteSiswa($siswa['id_siswa']);
        
        $this->assertTrue($result);
        
        $deletedSiswa = $this->model->getSiswaById($siswa['id_siswa']);
        
        $this->assertNull($deletedSiswa);
    }

    public function testDeleteMultiSelected(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        $this->model->createSiswa('1002', 'Jane Doe', $this->testKelasId, 'P', '08123456788');
        
        $siswa1 = $this->db->table('tb_siswa')->where('nis', '1001')->get()->getRowArray();
        $siswa2 = $this->db->table('tb_siswa')->where('nis', '1002')->get()->getRowArray();
        
        $ids = [$siswa1['id_siswa'], $siswa2['id_siswa']];
        
        $this->model->deleteMultiSelected($ids);
        
        $remainingSiswa = $this->model->getAllSiswaWithKelas();
        
        $this->assertEmpty($remainingSiswa);
    }

    // =====================================================
    // UNHAPPY PATH TESTS
    // =====================================================

    public function testCekSiswaWithInvalidCode(): void
    {
        $result = $this->model->cekSiswa('invalid-code-999');
        
        $this->assertNull($result);
    }

    public function testGetSiswaByIdWithInvalidId(): void
    {
        $result = $this->model->getSiswaById(99999);
        
        $this->assertNull($result);
    }

    public function testGetSiswaByKelasWithNoStudents(): void
    {
        $result = $this->model->getSiswaByKelas($this->testKelasId);
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetSiswaCountByKelasWithNoStudents(): void
    {
        $result = $this->model->getSiswaCountByKelas($this->testKelasId);
        
        $this->assertEquals(0, $result);
    }

    public function testDeleteSiswaWithInvalidId(): void
    {
        $result = $this->model->deleteSiswa(99999);
        
        $this->assertFalse($result);
    }

    public function testGetSiswaWithInvalidId(): void
    {
        $result = $this->model->getSiswa(99999);
        
        $this->assertNull($result);
    }

    // =====================================================
    // EDGE CASES
    // =====================================================

    public function testCreateSiswaGeneratesUniqueCode(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        $this->model->createSiswa('1002', 'Jane Doe', $this->testKelasId, 'P', '08123456788');
        
        $siswa1 = $this->db->table('tb_siswa')->where('nis', '1001')->get()->getRowArray();
        $siswa2 = $this->db->table('tb_siswa')->where('nis', '1002')->get()->getRowArray();
        
        $this->assertNotEquals($siswa1['unique_code'], $siswa2['unique_code']);
    }

    public function testUpdateSiswaDoesNotChangeUniqueCode(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        
        $siswa = $this->db->table('tb_siswa')->where('nis', '1001')->get()->getRowArray();
        $originalUniqueCode = $siswa['unique_code'];
        
        $this->model->updateSiswa(
            $siswa['id_siswa'],
            '1001',
            'John Updated',
            $this->testKelasId,
            'L',
            '08123456780'
        );
        
        $updatedSiswa = $this->model->getSiswaById($siswa['id_siswa']);
        
        $this->assertEquals($originalUniqueCode, $updatedSiswa['unique_code']);
    }

    public function testGetSiswaCountByKelasWithInvalidId(): void
    {
        $result = $this->model->getSiswaCountByKelas(99999);
        
        $this->assertEquals(0, $result);
    }

    public function testGetAllSiswaWithKelasOrderedByName(): void
    {
        $this->model->createSiswa('1001', 'Zack', $this->testKelasId, 'L', '08123456789');
        $this->model->createSiswa('1002', 'Alice', $this->testKelasId, 'P', '08123456788');
        $this->model->createSiswa('1003', 'Bob', $this->testKelasId, 'L', '08123456787');
        
        $result = $this->model->getAllSiswaWithKelas();
        
        $this->assertEquals('Alice', $result[0]['nama_siswa']);
        $this->assertEquals('Bob', $result[1]['nama_siswa']);
        $this->assertEquals('Zack', $result[2]['nama_siswa']);
    }

    public function testDeleteMultiSelectedWithEmptyArray(): void
    {
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789');
        
        $this->model->deleteMultiSelected([]);
        
        $siswa = $this->model->getAllSiswaWithKelas();
        
        // Should still have 1 student
        $this->assertCount(1, $siswa);
    }

    public function testCekSiswaReturnsFirstMatchForOrCondition(): void
    {
        // Test that cekSiswa works with either unique_code OR rfid_code
        $this->model->createSiswa('1001', 'John Doe', $this->testKelasId, 'L', '08123456789', 'RFID123');
        
        $siswa = $this->db->table('tb_siswa')->where('nis', '1001')->get()->getRowArray();
        
        $resultByUniqueCode = $this->model->cekSiswa($siswa['unique_code']);
        $resultByRfid = $this->model->cekSiswa('RFID123');
        
        $this->assertNotNull($resultByUniqueCode);
        $this->assertNotNull($resultByRfid);
        $this->assertEquals($resultByUniqueCode['id_siswa'], $resultByRfid['id_siswa']);
    }
}
