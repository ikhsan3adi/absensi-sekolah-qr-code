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
    protected $seed        = [
        '\App\Database\Seeds\DatabaseSeeder',
        '\Tests\Support\Database\Seeds\SuperadminSeeder',
    ];
    protected $seedOnce    = true;

    protected $testSiswaId;
    protected $testGuruId;
    protected $testKelasId;
    protected $testJurusanId;
    protected string $siswaUniqueCode;
    protected string $guruUniqueCode;

    protected function setUp(): void
    {
        parent::setUp();

        $jurusanSlug = 'IPA-' . uniqid();
        
        // Create jurusan
        $this->db->table('tb_jurusan')->insert([
            'jurusan' => $jurusanSlug,
        ]);
        $jurusanId = $this->db->insertID();
        $this->testJurusanId = $jurusanId;
        
        // Create kelas
        $this->db->table('tb_kelas')->insert([
            'tingkat' => '10',
            'id_jurusan' => $jurusanId,
            'index_kelas' => 'A',
        ]);
        $this->testKelasId = $this->db->insertID();
        
        // Create test siswa
        $this->siswaUniqueCode = uniqid('test-siswa-');
        $this->db->table('tb_siswa')->insert([
            'nis' => uniqid(),
            'nama_siswa' => 'Test Siswa Integration',
            'id_kelas' => $this->testKelasId,
            'jenis_kelamin' => 'L',
            'no_hp' => '08123456789',
            'unique_code' => $this->siswaUniqueCode,
        ]);
        $this->testSiswaId = $this->db->insertID();
        
        // Create test guru
        $this->guruUniqueCode = uniqid('test-guru-');
        $this->db->table('tb_guru')->insert([
            'nuptk' => uniqid(),
            'nama_guru' => 'Test Guru Integration',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Test',
            'no_hp' => '08123456788',
            'unique_code' => $this->guruUniqueCode,
        ]);
        $this->testGuruId = $this->db->insertID();
        
        $this->login();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->logout();
    }
    
    protected function login(): void
    {
        $credentials = [
            'email' => \Tests\Support\Database\Seeds\SuperadminSeeder::$email,
        ];
        
        $user = auth()->getProvider()->findByCredentials($credentials);
        if ($user) {
            auth()->login($user);
        }
    }

    protected function logout(): void
    {
        auth()->logout();
    }

    // =====================================================
    // INTEGRATION TESTS - Student Attendance Flow
    // =====================================================

    public function testStudentAttendanceEntryFlowSuccess(): void
    {
        $date = Time::today()->toDateString();
        
        $result = $this->withSession()->post('/scan/cek', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'masuk'
        ]);
        
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

    public function testStudentAttendanceEntryPreventsDuplicate(): void
    {
        $date = Time::today()->toDateString();
        
        // First scan - should succeed
        $this->withSession()->post('/scan/cek', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'masuk'
        ]);
        
        // Second scan - should fail
        $result = $this->withSession()->post('/scan/cek', [
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

    public function testStudentAttendanceCompleteEntryAndExit(): void
    {
        $date = Time::today()->toDateString();
        
        // Entry scan
        $this->withSession()->post('/scan/cek', [
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
        $result = $this->withSession()->post('/scan/cek', [
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

    public function testStudentAttendanceExitWithoutEntry(): void
    {
        $result = $this->withSession()->post('/scan/cek', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'pulang'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Anda belum absen hari ini');
    }

    // =====================================================
    // INTEGRATION TESTS - Teacher Attendance Flow
    // =====================================================

    public function testTeacherAttendanceEntryFlowSuccess(): void
    {
        $date = Time::today()->toDateString();
        
        $result = $this->withSession()->post('/scan/cek', [
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

    public function testAttendanceWithInvalidUniqueCode(): void
    {
        $result = $this->withSession()->post('/scan/cek', [
            'unique_code' => 'invalid-code-999',
            'waktu' => 'masuk'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Data tidak ditemukan');
    }

    public function testAttendanceWithInvalidWaktu(): void
    {
        $result = $this->withSession()->post('/scan/cek', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'invalid'
        ]);
        
        $result->assertStatus(200);
        $result->assertSee('Data tidak valid');
    }

    // =====================================================
    // INTEGRATION TESTS - Cross-day Attendance
    // =====================================================

    public function testStudentCanAttendConsecutiveDays(): void
    {
        // Day 1
        $this->withSession()->post('/scan/cek', [
            'unique_code' => $this->siswaUniqueCode,
            'waktu' => 'masuk'
        ]);
        
        $day1Count = $this->db->table('tb_presensi_siswa')
            ->where('id_siswa', $this->testSiswaId)
            ->countAllResults();
        
        $this->assertEquals(1, $day1Count);
        
        // Simulate day 2 by manually inserting
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
}
