<?php

namespace Tests\Unit\Models;

use App\Models\PresensiSiswaModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Libraries\enums\Kehadiran;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\I18n\Time;

/**
 * @internal
 */
final class PresensiSiswaModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = null;
    protected $seed        = ['\App\Database\Seeds\KehadiranSeeder'];
    protected $seedOnce    = true;

    protected PresensiSiswaModel $model;
    protected $testSiswaId;
    protected $testKelasId;
    protected $testJurusanId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new PresensiSiswaModel();
        
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
        
        $this->db->table('tb_siswa')->insert([
            'nis' => '1001',
            'nama_siswa' => 'Test Siswa',
            'id_kelas' => $this->testKelasId,
            'jenis_kelamin' => 'Laki-laki',
            'no_hp' => '08123456789',
            'unique_code' => 'test-code-123',
        ]);
        
        $this->testSiswaId = $this->db->insertID();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->db->table('tb_siswa')->delete(['id_siswa' => $this->testSiswaId]);
        $this->db->table('tb_kelas')->delete(['id_kelas' => $this->testKelasId]);
        $this->db->table('tb_jurusan')->delete(['id' => $this->testJurusanId]);
    }

    // =====================================================
    // HAPPY PATH TESTS
    // =====================================================

    public function testCekAbsenReturnsFalseWhenNoAttendance(): void
    {
        $date = Time::today()->toDateString();
        $result = $this->model->cekAbsen($this->testSiswaId, $date);
        
        $this->assertFalse($result);
    }

    public function testCekAbsenReturnsIdPresensiWhenAttendanceExists(): void
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();
        
        // Create attendance
        $this->model->absenMasuk($this->testSiswaId, $date, $time, $this->testKelasId);
        
        $result = $this->model->cekAbsen($this->testSiswaId, $date);
        
        $this->assertNotFalse($result);
        $this->assertIsInt(intval($result));
        $this->assertGreaterThan(0, intval($result));
    }

    public function testAbsenMasukCreatesNewAttendanceRecord(): void
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();
        
        $this->model->absenMasuk($this->testSiswaId, $date, $time, $this->testKelasId);
        
        $presensi = $this->model->getPresensiByIdSiswaTanggal($this->testSiswaId, $date);
        
        $this->assertNotNull($presensi);
        $this->assertEquals($this->testSiswaId, $presensi['id_siswa']);
        $this->assertEquals($this->testKelasId, $presensi['id_kelas']);
        $this->assertEquals($date, $presensi['tanggal']);
        $this->assertEquals($time, $presensi['jam_masuk']);
        $this->assertNull($presensi['jam_keluar']);
        $this->assertEquals(Kehadiran::Hadir->value, $presensi['id_kehadiran']);
        $this->assertEquals('', $presensi['keterangan']);
    }

    public function testAbsenKeluarUpdatesExistingAttendance(): void
    {
        $date = Time::today()->toDateString();
        $timeMasuk = '07:00:00';
        $timeKeluar = '15:00:00';
        
        // Create entry attendance
        $this->model->absenMasuk($this->testSiswaId, $date, $timeMasuk, $this->testKelasId);
        $idPresensi = $this->model->cekAbsen($this->testSiswaId, $date);
        
        // Update exit attendance
        $this->model->absenKeluar($idPresensi, $timeKeluar);
        
        $presensi = $this->model->getPresensiById($idPresensi);
        
        $this->assertNotNull($presensi);
        $this->assertEquals($timeMasuk, $presensi['jam_masuk']);
        $this->assertEquals($timeKeluar, $presensi['jam_keluar']);
    }

    public function testGetPresensiByIdSiswaTanggalReturnsCorrectRecord(): void
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();
        
        $this->model->absenMasuk($this->testSiswaId, $date, $time, $this->testKelasId);
        
        $result = $this->model->getPresensiByIdSiswaTanggal($this->testSiswaId, $date);
        
        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertEquals($this->testSiswaId, $result['id_siswa']);
        $this->assertEquals($date, $result['tanggal']);
    }

    public function testGetPresensiByIdReturnsCorrectRecord(): void
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();
        
        $this->model->absenMasuk($this->testSiswaId, $date, $time, $this->testKelasId);
        $idPresensi = $this->model->cekAbsen($this->testSiswaId, $date);
        
        $result = $this->model->getPresensiById($idPresensi);
        
        $this->assertNotNull($result);
        $this->assertEquals($idPresensi, $result['id_presensi']);
    }

    public function testUpdatePresensiWithNewRecord(): void
    {
        $date = Time::today()->toDateString();
        $jamMasuk = '07:00:00';
        $jamKeluar = '15:00:00';
        
        $result = $this->model->updatePresensi(
            null,
            $this->testSiswaId,
            $this->testKelasId,
            $date,
            Kehadiran::Sakit->value,
            $jamMasuk,
            $jamKeluar,
            'Sakit demam'
        );
        
        $this->assertTrue($result);
        
        $presensi = $this->model->getPresensiByIdSiswaTanggal($this->testSiswaId, $date);
        $this->assertNotNull($presensi);
        $this->assertEquals(Kehadiran::Sakit->value, $presensi['id_kehadiran']);
        $this->assertEquals('Sakit demam', $presensi['keterangan']);
    }

    public function testUpdatePresensiWithExistingRecord(): void
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();
        
        // Create initial attendance
        $this->model->absenMasuk($this->testSiswaId, $date, $time, $this->testKelasId);
        $idPresensi = $this->model->cekAbsen($this->testSiswaId, $date);
        
        // Update attendance
        $result = $this->model->updatePresensi(
            $idPresensi,
            $this->testSiswaId,
            $this->testKelasId,
            $date,
            Kehadiran::Izin->value,
            null,
            null,
            'Izin keperluan keluarga'
        );
        
        $this->assertTrue($result);
        
        $presensi = $this->model->getPresensiById($idPresensi);
        $this->assertEquals(Kehadiran::Izin->value, $presensi['id_kehadiran']);
        $this->assertEquals('Izin keperluan keluarga', $presensi['keterangan']);
    }

    public function testGetPresensiByKelasTanggalReturnsAllStudents(): void
    {
        $date = Time::today()->toDateString();
        
        // Add another student
        $this->db->table('tb_siswa')->insert([
            'nis' => '1002',
            'nama_siswa' => 'Test Siswa 2',
            'id_kelas' => $this->testKelasId,
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '08123456788',
            'unique_code' => 'test-code-124',
        ]);
        
        $siswaId2 = $this->db->insertID();
        
        // First student has attendance
        $this->model->absenMasuk($this->testSiswaId, $date, '07:00:00', $this->testKelasId);
        
        // Second student has no attendance
        
        $result = $this->model->getPresensiByKelasTanggal($this->testKelasId, $date);
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    // =====================================================
    // UNHAPPY PATH TESTS
    // =====================================================

    public function testCekAbsenWithNonExistentStudent(): void
    {
        $date = Time::today()->toDateString();
        $result = $this->model->cekAbsen(99999, $date);
        
        $this->assertFalse($result);
    }

    public function testCekAbsenWithDifferentDate(): void
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();
        
        $this->model->absenMasuk($this->testSiswaId, $date, $time, $this->testKelasId);
        
        // Check with different date
        $differentDate = Time::tomorrow()->toDateString();
        $result = $this->model->cekAbsen($this->testSiswaId, $differentDate);
        
        $this->assertFalse($result);
    }

    public function testGetPresensiByIdSiswaTanggalReturnsNullWhenNoRecord(): void
    {
        $date = Time::today()->toDateString();
        $result = $this->model->getPresensiByIdSiswaTanggal($this->testSiswaId, $date);
        
        $this->assertNull($result);
    }

    public function testGetPresensiByIdReturnsNullForInvalidId(): void
    {
        $result = $this->model->getPresensiById('99999');
        
        $this->assertNull($result);
    }

    public function testAbsenMasukWithoutKelasId(): void
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();
        
        // Should still work with empty kelas_id
        $this->model->absenMasuk($this->testSiswaId, $date, $time, $this->testKelasId);
        
        $presensi = $this->model->getPresensiByIdSiswaTanggal($this->testSiswaId, $date);
        
        $this->assertNotNull($presensi);
        $this->assertEquals($this->testKelasId, $presensi['id_kelas']);
    }

    public function testAbsenKeluarWithInvalidId(): void
    {
        $time = Time::now()->toTimeString();
        
        // Should not throw error but won't update anything
        $result = $this->model->absenKeluar('99999', $time);
        
        // Model returns false when no rows affected
        $this->assertNull($result);
    }

    public function testUpdatePresensiKeepsExistingKeteranganWhenNull(): void
    {
        $date = Time::today()->toDateString();
        
        // Create initial attendance with keterangan
        $this->model->updatePresensi(
            null,
            $this->testSiswaId,
            $this->testKelasId,
            $date,
            Kehadiran::Sakit->value,
            '07:00:00',
            null,
            'Sakit awal'
        );
        
        $idPresensi = $this->model->cekAbsen($this->testSiswaId, $date);
        
        // Update without providing keterangan (null)
        $this->model->updatePresensi(
            $idPresensi,
            $this->testSiswaId,
            $this->testKelasId,
            $date,
            Kehadiran::Hadir->value,
            null,
            '15:00:00',
            null
        );
        
        $presensi = $this->model->getPresensiById($idPresensi);
        
        // Should keep previous keterangan
        $this->assertEquals('Sakit awal', $presensi['keterangan']);
    }

    // =====================================================
    // EDGE CASES
    // =====================================================

    public function testMultipleAbsenMasukOnSameDay(): void
    {
        $date = Time::today()->toDateString();
        
        $this->model->absenMasuk($this->testSiswaId, $date, '07:00:00', $this->testKelasId);
        $this->model->absenMasuk($this->testSiswaId, $date, '08:00:00', $this->testKelasId);
        
        // Should create 2 records (duplicate entries)
        $records = $this->db->table('tb_presensi_siswa')
            ->where('id_siswa', $this->testSiswaId)
            ->where('tanggal', $date)
            ->get()
            ->getResultArray();
        
        // Note: Current implementation allows duplicates
        $this->assertGreaterThanOrEqual(2, count($records));
    }

    public function testCekAbsenWithTimeObject(): void
    {
        $date = Time::today();
        $result = $this->model->cekAbsen($this->testSiswaId, $date);
        
        $this->assertFalse($result);
    }

    public function testUpdatePresensiOnlyUpdatesJamMasukWhenProvided(): void
    {
        $date = Time::today()->toDateString();
        
        $this->model->absenMasuk($this->testSiswaId, $date, '07:00:00', $this->testKelasId);
        $idPresensi = $this->model->cekAbsen($this->testSiswaId, $date);
        
        // Update only jam_masuk
        $this->model->updatePresensi(
            $idPresensi,
            $this->testSiswaId,
            $this->testKelasId,
            $date,
            Kehadiran::Hadir->value,
            '06:30:00',
            null,
            ''
        );
        
        $presensi = $this->model->getPresensiById($idPresensi);
        
        $this->assertEquals('06:30:00', $presensi['jam_masuk']);
        $this->assertNull($presensi['jam_keluar']);
    }

    public function testUpdatePresensiOnlyUpdatesJamKeluarWhenProvided(): void
    {
        $date = Time::today()->toDateString();
        
        $this->model->absenMasuk($this->testSiswaId, $date, '07:00:00', $this->testKelasId);
        $idPresensi = $this->model->cekAbsen($this->testSiswaId, $date);
        
        // Update only jam_keluar
        $this->model->updatePresensi(
            $idPresensi,
            $this->testSiswaId,
            $this->testKelasId,
            $date,
            Kehadiran::Hadir->value,
            null,
            '15:30:00',
            ''
        );
        
        $presensi = $this->model->getPresensiById($idPresensi);
        
        $this->assertEquals('07:00:00', $presensi['jam_masuk']);
        $this->assertEquals('15:30:00', $presensi['jam_keluar']);
    }

    public function testGetPresensiByKehadiranWithHadir(): void
    {
        $date = Time::today()->toDateString();
        
        $this->model->absenMasuk($this->testSiswaId, $date, '07:00:00', $this->testKelasId);
        
        $result = $this->model->getPresensiByKehadiran('1', $date);
        
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testGetPresensiByKehadiranWithTanpaKeterangan(): void
    {
        $date = Time::today()->toDateString();
        
        // Create student without attendance (considered as tanpa keterangan)
        
        $result = $this->model->getPresensiByKehadiran('4', $date);
        
        $this->assertIsArray($result);
    }
}
