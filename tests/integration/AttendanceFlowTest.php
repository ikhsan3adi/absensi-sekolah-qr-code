<?php

namespace Tests\Integration;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\I18n\Time;

/**
 * Example Integration Test for Attendance Flow
 * 
 * This test demonstrates how to test the complete attendance flow
 * from QR code scanning to database recording.
 * 
 * @internal
 */
final class AttendanceFlowTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = null;

    protected $testSiswaId;
    protected $testGuruId;
    protected $testKelasId;
    protected $siswaUniqueCode;
    protected $guruUniqueCode;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup test data
        $this->setupTestData();
    }

    private function setupTestData(): void
    {
        // Create jurusan
        $this->db->table('tb_jurusan')->insert([
            'jurusan' => 'IPA',
        ]);
        $jurusanId = $this->db->insertID();
        
        // Create kelas
        $this->db->table('tb_kelas')->insert([
            'tingkat' => '10',
            'id_jurusan' => $jurusanId,
            'index_kelas' => 'A',
        ]);
        $this->testKelasId = $this->db->insertID();
        
        // Create kehadiran types
        $this->db->table('tb_kehadiran')->insert([
            ['id_kehadiran' => 1, 'nama' => 'Hadir'],
            ['id_kehadiran' => 2, 'nama' => 'Sakit'],
            ['id_kehadiran' => 3, 'nama' => 'Izin'],
            ['id_kehadiran' => 4, 'nama' => 'Tanpa Keterangan'],
        ]);
        
        // Create test siswa
        $this->siswaUniqueCode = 'test-siswa-' . uniqid();
        $this->db->table('tb_siswa')->insert([
            'nis' => '1001',
            'nama_siswa' => 'Test Siswa Integration',
            'id_kelas' => $this->testKelasId,
            'jenis_kelamin' => 'L',
            'no_hp' => '08123456789',
            'unique_code' => $this->siswaUniqueCode,
        ]);
        $this->testSiswaId = $this->db->insertID();
        
        // Create test guru
        $this->guruUniqueCode = 'test-guru-' . uniqid();
        $this->db->table('tb_guru')->insert([
            'nuptk' => '1234567890123456',
            'nama_guru' => 'Test Guru Integration',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Test',
            'no_hp' => '08123456788',
            'unique_code' => $this->guruUniqueCode,
        ]);
        $this->testGuruId = $this->db->insertID();
    }

    // =====================================================
    // INTEGRATION TESTS - Student Attendance Flow
    // =====================================================

    /**
     * Test complete student attendance entry flow
     * 
     * Scenario:
     * 1. Student scans QR code for entry
     * 2. System validates student exists
     * 3. System checks if already absent today
     * 4. System records attendance
     * 5. System returns success response
     */
    public function testStudentAttendanceEntryFlowSuccess(): void
    {
        $date = Time::today()->toDateString();
        
        // Simulate QR code scan POST request
        $result = $this->post('/scan/cekKode', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'masuk'
        ]);
        
        // Should return 200 and show success view
        $result->assertStatus(200);
        $result->assertSee('Test Siswa Integration');
        
        // Verify attendance record was created in database
        $attendance = $this->db->table('tb_presensi_siswa')
            ->where('id_siswa', $this->testSiswaId)
            ->where('tanggal', $date)
            ->get()
            ->getRowArray();
        
        $this->assertNotNull($attendance, 'Attendance record should be created');
        $this->assertEquals(1, $attendance['id_kehadiran'], 'Should be marked as present');
        $this->assertEquals($this->testKelasId, $attendance['id_kelas']);
        $this->assertNotNull($attendance['jam_masuk']);
        $this->assertNull($attendance['jam_keluar']);
    }

    /**
     * Test duplicate entry prevention
     * 
     * Scenario:
     * 1. Student already has attendance for today
     * 2. Student tries to scan again
     * 3. System should prevent duplicate entry
     * 4. System should show error message
     */
    public function testStudentAttendanceEntryPreventsDuplicate(): void
    {
        $date = Time::today()->toDateString();
        
        // First scan - should succeed
        $this->post('/scan/cekKode', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'masuk'
        ]);
        
        // Second scan - should fail
        $result = $this->post('/scan/cekKode', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'masuk'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Anda sudah absen hari ini');
        
        // Verify only one record exists
        $count = $this->db->table('tb_presensi_siswa')
            ->where('id_siswa', $this->testSiswaId)
            ->where('tanggal', $date)
            ->countAllResults();
        
        $this->assertEquals(1, $count, 'Should have only one attendance record');
    }

    /**
     * Test complete entry and exit flow
     * 
     * Scenario:
     * 1. Student scans for entry (masuk)
     * 2. Later, student scans for exit (pulang)
     * 3. System updates the same record with exit time
     */
    public function testStudentAttendanceCompleteEntryAndExit(): void
    {
        $date = Time::today()->toDateString();
        
        // Entry scan
        $this->post('/scan/cekKode', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'masuk'
        ]);
        
        // Get attendance ID
        $attendance = $this->db->table('tb_presensi_siswa')
            ->where('id_siswa', $this->testSiswaId)
            ->where('tanggal', $date)
            ->get()
            ->getRowArray();
        
        $this->assertNull($attendance['jam_keluar'], 'Exit time should be null initially');
        
        // Exit scan
        $result = $this->post('/scan/cekKode', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'pulang'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Test Siswa Integration');
        
        // Verify exit time was recorded
        $updatedAttendance = $this->db->table('tb_presensi_siswa')
            ->where('id_siswa', $this->testSiswaId)
            ->where('tanggal', $date)
            ->get()
            ->getRowArray();
        
        $this->assertNotNull($updatedAttendance['jam_keluar'], 'Exit time should be recorded');
        $this->assertNotNull($updatedAttendance['jam_masuk'], 'Entry time should still exist');
    }

    /**
     * Test exit without entry
     * 
     * Scenario:
     * 1. Student tries to scan for exit without entry
     * 2. System should reject the request
     */
    public function testStudentAttendanceExitWithoutEntry(): void
    {
        // Try to scan for exit without entry
        $result = $this->post('/scan/cekKode', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'pulang'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Anda belum absen hari ini');
    }

    // =====================================================
    // INTEGRATION TESTS - Teacher Attendance Flow
    // =====================================================

    /**
     * Test teacher attendance flow
     */
    public function testTeacherAttendanceEntryFlowSuccess(): void
    {
        $date = Time::today()->toDateString();
        
        $result = $this->post('/scan/cekKode', [
            'unique_code' => $this->guruUniqueCode,
            'waktu' => 'masuk'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Test Guru Integration');
        
        // Verify attendance record
        $attendance = $this->db->table('tb_presensi_guru')
            ->where('id_guru', $this->testGuruId)
            ->where('tanggal', $date)
            ->get()
            ->getRowArray();
        
        $this->assertNotNull($attendance);
        $this->assertEquals(1, $attendance['id_kehadiran']);
    }

    // =====================================================
    // INTEGRATION TESTS - Invalid Input Handling
    // =====================================================

    /**
     * Test with invalid unique code
     */
    public function testAttendanceWithInvalidUniqueCode(): void
    {
        $result = $this->post('/scan/cekKode', [
            'unique_code' => 'invalid-code-999',
            'waktu' => 'masuk'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Data tidak ditemukan');
    }

    /**
     * Test with invalid waktu parameter
     */
    public function testAttendanceWithInvalidWaktu(): void
    {
        $result = $this->post('/scan/cekKode', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'invalid'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Data tidak valid');
    }

    // =====================================================
    // INTEGRATION TESTS - Cross-day Attendance
    // =====================================================

    /**
     * Test that students can attend on consecutive days
     */
    public function testStudentCanAttendConsecutiveDays(): void
    {
        // Day 1
        $this->post('/scan/cekKode', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'masuk'
        ]);
        
        $day1Count = $this->db->table('tb_presensi_siswa')
            ->where('id_siswa', $this->testSiswaId)
            ->countAllResults();
        
        $this->assertEquals(1, $day1Count);
        
        // Simulate day 2 by manually inserting
        // (In real integration test, you'd manipulate time or use time travel)
        $tomorrow = Time::tomorrow()->toDateString();
        $this->db->table('tb_presensi_siswa')->insert([
            'id_siswa' => $this->testSiswaId,
            'id_kelas' => $this->testKelasId,
            'tanggal' => $tomorrow,
            'jam_masuk' => '07:00:00',
            'id_kehadiran' => 1,
            'keterangan' => ''
        ]);
        
        $totalCount = $this->db->table('tb_presensi_siswa')
            ->where('id_siswa', $this->testSiswaId)
            ->countAllResults();
        
        $this->assertEquals(2, $totalCount, 'Should have attendance for 2 days');
    }

    /**
     * NOTE: WhatsApp notification integration tests should mock the HTTP client
     * to avoid hitting real API endpoints during testing.
     * 
     * Example structure (not implemented here):
     * 
     * public function testWhatsappNotificationSentOnAttendance(): void
     * {
     *     // Setup HTTP mock
     *     // Scan QR code
     *     // Verify mock was called with correct parameters
     * }
     */
}
