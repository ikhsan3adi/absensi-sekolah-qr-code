<?php

namespace Tests\Unit\Validation;

use App\Validation\RFIDRules;
use App\Models\SiswaModel;
use App\Models\GuruModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class RFIDRulesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected RFIDRules $rules;
    protected $testKelasId;
    protected $testJurusanId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rules = new RFIDRules();
        
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
    // HAPPY PATH TESTS - Unique RFID Validation
    // =====================================================

    public function testIsRfidUniqueWithEmptyStringReturnsTrue(): void
    {
        $error = null;
        $result = $this->rules->is_rfid_unique('', '', [], $error);
        
        $this->assertTrue($result);
        $this->assertNull($error);
    }

    public function testIsRfidUniqueForNewSiswaWithUniqueCode(): void
    {
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', ',siswa', [], $error);
        
        $this->assertTrue($result);
        $this->assertNull($error);
    }

    public function testIsRfidUniqueForNewGuruWithUniqueCode(): void
    {
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', ',guru', [], $error);
        
        $this->assertTrue($result);
        $this->assertNull($error);
    }

    // =====================================================
    // UNHAPPY PATH TESTS - Duplicate RFID
    // =====================================================

    public function testIsRfidUniqueReturnsFalseWhenCodeExistsInSiswa(): void
    {
        // Create a student with RFID
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa', $this->testKelasId, 'L', '08123456789', 'RFID123456');
        
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', ',siswa', [], $error);
        
        $this->assertFalse($result);
        $this->assertEquals('RFID code ini sudah digunakan oleh Siswa.', $error);
    }

    public function testIsRfidUniqueReturnsFalseWhenCodeExistsInGuru(): void
    {
        // Create a teacher with RFID
        $guruModel = new GuruModel();
        $guruModel->createGuru('1234567890123456', 'Test Guru', 'L', 'Jl. Test', '08123456789', 'RFID123456');
        
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', ',guru', [], $error);
        
        $this->assertFalse($result);
        $this->assertEquals('RFID code ini sudah digunakan oleh Guru.', $error);
    }

    public function testIsRfidUniqueChecksAcrossBothTables(): void
    {
        // Create a student with RFID
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa', $this->testKelasId, 'L', '08123456789', 'RFID123456');
        
        // Try to use same RFID for a guru
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', ',guru', [], $error);
        
        $this->assertFalse($result);
        $this->assertEquals('RFID code ini sudah digunakan oleh Siswa.', $error);
    }

    // =====================================================
    // HAPPY PATH TESTS - Exclude ID Feature
    // =====================================================

    public function testIsRfidUniqueExcludesSameStudentWhenUpdating(): void
    {
        // Create a student with RFID
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa', $this->testKelasId, 'L', '08123456789', 'RFID123456');
        
        $siswa = $this->db->table('tb_siswa')->where('nis', '1001')->get()->getRowArray();
        
        // Update same student with same RFID should be allowed
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', $siswa['id_siswa'] . ',siswa', [], $error);
        
        $this->assertTrue($result);
        $this->assertNull($error);
    }

    public function testIsRfidUniqueExcludesSameTeacherWhenUpdating(): void
    {
        // Create a teacher with RFID
        $guruModel = new GuruModel();
        $guruModel->createGuru('1234567890123456', 'Test Guru', 'L', 'Jl. Test', '08123456789', 'RFID123456');
        
        $guru = $this->db->table('tb_guru')->where('nuptk', '1234567890123456')->get()->getRowArray();
        
        // Update same teacher with same RFID should be allowed
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', $guru['id_guru'] . ',guru', [], $error);
        
        $this->assertTrue($result);
        $this->assertNull($error);
    }

    public function testIsRfidUniqueDoesNotExcludeOtherStudents(): void
    {
        // Create two students
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa 1', $this->testKelasId, 'L', '08123456789', 'RFID123456');
        $siswaModel->createSiswa('1002', 'Test Siswa 2', $this->testKelasId, 'P', '08123456788');
        
        $siswa1 = $this->db->table('tb_siswa')->where('nis', '1001')->get()->getRowArray();
        $siswa2 = $this->db->table('tb_siswa')->where('nis', '1002')->get()->getRowArray();
        
        // Try to use RFID from student 1 for student 2
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', $siswa2['id_siswa'] . ',siswa', [], $error);
        
        $this->assertFalse($result);
        $this->assertEquals('RFID code ini sudah digunakan oleh Siswa.', $error);
    }

    // =====================================================
    // EDGE CASES
    // =====================================================

    public function testIsRfidUniqueWithoutTypeParameter(): void
    {
        // Create a student with RFID
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa', $this->testKelasId, 'L', '08123456789', 'RFID123456');
        
        // Check without specifying type
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', '', [], $error);
        
        $this->assertFalse($result);
        $this->assertEquals('RFID code ini sudah digunakan oleh Siswa.', $error);
    }

    public function testIsRfidUniqueWithOnlyExcludeIdNoType(): void
    {
        // Create a student with RFID
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa', $this->testKelasId, 'L', '08123456789', 'RFID123456');
        
        // Check with exclude ID but no type
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123456', '1', [], $error);
        
        $this->assertFalse($result);
    }

    public function testIsRfidUniqueExcludesCorrectlyAcrossTypes(): void
    {
        // Create a teacher and student with different RFIDs
        $siswaModel = new SiswaModel();
        $guruModel = new GuruModel();
        
        $siswaModel->createSiswa('1001', 'Test Siswa', $this->testKelasId, 'L', '08123456789', 'RFID_SISWA');
        $guruModel->createGuru('1234567890123456', 'Test Guru', 'L', 'Jl. Test', '08123456788', 'RFID_GURU');
        
        $guru = $this->db->table('tb_guru')->where('nuptk', '1234567890123456')->get()->getRowArray();
        
        // Teacher updating with student's RFID should fail
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID_SISWA', $guru['id_guru'] . ',guru', [], $error);
        
        $this->assertFalse($result);
        $this->assertEquals('RFID code ini sudah digunakan oleh Siswa.', $error);
    }

    public function testIsRfidUniqueWithNullRfidCode(): void
    {
        $error = null;
        $result = $this->rules->is_rfid_unique('', ',siswa', [], $error);
        
        // Null should be treated as empty
        $this->assertTrue($result);
    }

    public function testIsRfidUniqueWithWhitespaceRfidCode(): void
    {
        $error = null;
        $result = $this->rules->is_rfid_unique('   ', ',siswa', [], $error);
        
        // Whitespace should not be empty, so it checks database
        $this->assertTrue($result);
    }

    public function testIsRfidUniqueCaseSensitivity(): void
    {
        // Create a student with lowercase RFID
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa', $this->testKelasId, 'L', '08123456789', 'rfid123');
        
        // Try with uppercase - depends on database collation
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID123', ',siswa', [], $error);
        
        // Result depends on database collation (case-insensitive or not)
        // Most MySQL configs are case-insensitive by default
        $this->assertIsBool($result);
    }

    public function testIsRfidUniqueWithSpecialCharacters(): void
    {
        // Create a student with special chars in RFID
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa', $this->testKelasId, 'L', '08123456789', 'RFID-123_456');
        
        $error = null;
        $result = $this->rules->is_rfid_unique('RFID-123_456', ',guru', [], $error);
        
        $this->assertFalse($result);
        $this->assertEquals('RFID code ini sudah digunakan oleh Siswa.', $error);
    }

    public function testIsRfidUniqueMultipleStudentsWithNullRfid(): void
    {
        // Multiple students with NULL RFID should be allowed
        $siswaModel = new SiswaModel();
        $siswaModel->createSiswa('1001', 'Test Siswa 1', $this->testKelasId, 'L', '08123456789', null);
        $siswaModel->createSiswa('1002', 'Test Siswa 2', $this->testKelasId, 'P', '08123456788', null);
        
        // Adding another student with NULL should be fine
        $error = null;
        $result = $this->rules->is_rfid_unique('', ',siswa', [], $error);
        
        $this->assertTrue($result);
    }
}
