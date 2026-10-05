<?php

namespace Tests\Integration;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Regression tests for the missing authorization on the admin
 * perizinan / holiday / audit-log routes.
 *
 * @internal
 */
final class AdminAuthorizationTest extends CIUnitTestCase
{
    use AuthenticationTesting;
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = null;
    protected $seed        = [
        '\App\Database\Seeds\GeneralSettingsSeeder',
    ];
    protected $seedOnce = true;

    private const EVIDENCE_FILE = '1781542037_254d78d19d81fb34aaa0.png';
    private const EVIDENCE_PNG  = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    /**
     * @var array<string, mixed>
     */
    private array $filtersBackup = [];

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is orthogonal to authorization: remove the global CSRF
        // filter so POST/DELETE requests reach the permission filter.
        $this->filtersBackup = config('Filters')->globals;
        unset(config('Filters')->globals['before']['csrf']);
    }

    protected function tearDown(): void
    {
        config('Filters')->globals = $this->filtersBackup;

        parent::tearDown();
    }

    public function testScannerCannotAccessPermitsHolidayOrAuditLog(): void
    {
        $this->actingAs($this->makeUser('scanner'));

        foreach (['admin/perizinan', 'admin/holiday', 'admin/audit-log'] as $path) {
            $result = $this->get($path);

            $result->assertRedirect();
            $this->assertStringNotContainsString('Data Perizinan Siswa', (string) $result->getBody());
        }
    }

    public function testKepsekCannotAccessPermitsHolidayOrAuditLog(): void
    {
        $this->actingAs($this->makeUser('kepsek'));

        foreach (['admin/perizinan', 'admin/holiday', 'admin/audit-log'] as $path) {
            $this->get($path)->assertRedirect();
        }
    }

    public function testAdminCanAccessPermitsButNotHolidayOrAuditLog(): void
    {
        $this->actingAs($this->makeUser('admin'));

        $this->get('admin/perizinan')->assertStatus(200);
        $this->get('admin/holiday')->assertRedirect();
        $this->get('admin/audit-log')->assertRedirect();
    }

    public function testSuperadminCanAccessAllThreeModules(): void
    {
        $this->actingAs($this->makeUser('superadmin'));

        $this->get('admin/perizinan')->assertStatus(200);
        $this->get('admin/holiday')->assertStatus(200);
        $this->get('admin/audit-log')->assertStatus(200);
    }

    public function testScannerCannotConfirmOrDeletePermit(): void
    {
        $this->actingAs($this->makeUser('scanner'));

        $permitId = $this->createPermit();

        $this->post('admin/perizinan/konfirmasi', [
            'id_perizinan' => $permitId,
            'status'       => 'Disetujui',
        ])->assertRedirect();

        $permit = $this->db->table('tb_perizinan')->where('id_perizinan', $permitId)->get()->getRowArray();
        $this->assertSame('Pending', $permit['status']);

        $this->delete('admin/perizinan/delete/' . $permitId)->assertRedirect();

        $this->seeInDatabase('tb_perizinan', ['id_perizinan' => $permitId]);
    }

    public function testScannerCannotDeleteOrBulkDeleteHoliday(): void
    {
        $this->actingAs($this->makeUser('scanner'));

        $this->db->table('tb_hari_libur')->insert([
            'tanggal'    => '2030-01-01',
            'keterangan' => 'Test',
        ]);
        $holidayId = (int) $this->db->insertID();

        $this->delete('admin/holiday/delete/' . $holidayId)->assertRedirect();
        $this->post('admin/holiday/bulk-delete', ['holiday_ids' => [$holidayId]])->assertRedirect();

        $this->seeInDatabase('tb_hari_libur', ['id' => $holidayId]);
    }

    public function testBuktiIsProtectedAndScopedToWaliKelas(): void
    {
        $this->ensureEvidenceFile();

        [$kelasId, $waliId, $otherTeacherId] = $this->createKelasWithTeachers();

        $this->db->table('tb_siswa')->insert([
            'nis'           => uniqid(),
            'nama_siswa'    => 'Siswa Uji Bukti',
            'id_kelas'      => $kelasId,
            'jenis_kelamin' => 'L',
            'no_hp'         => '081200000000',
            'unique_code'   => uniqid('siswa-'),
        ]);
        $siswaId = (int) $this->db->insertID();

        $this->createPermit([
            'id_siswa' => $siswaId,
            'bukti'    => self::EVIDENCE_FILE,
        ]);

        $this->actingAs($this->makeUser('scanner'));
        $this->get('admin/perizinan/bukti/' . self::EVIDENCE_FILE)->assertRedirect();

        $this->actingAs($this->makeUser('admin'));
        $this->get('admin/perizinan/bukti/' . self::EVIDENCE_FILE)->assertStatus(200);

        $wali = $this->assignGuruProfile($this->makeUser('guru'), $waliId);
        $this->actingAs($wali);
        $this->get('admin/perizinan/bukti/' . self::EVIDENCE_FILE)->assertStatus(200);

        $otherTeacher = $this->assignGuruProfile($this->makeUser('guru'), $otherTeacherId);
        $this->actingAs($otherTeacher);
        $this->get('admin/perizinan/bukti/' . self::EVIDENCE_FILE)->assertStatus(403);
    }

    public function testBuktiReturns404ForUnknownFile(): void
    {
        $this->actingAs($this->makeUser('admin'));

        $this->get('admin/perizinan/bukti/does-not-exist-' . uniqid() . '.png')
            ->assertStatus(404);
    }

    /**
     * The uploads directory is gitignored, so create a placeholder
     * evidence file when running from a fresh checkout.
     */
    private function ensureEvidenceFile(): void
    {
        $path = FCPATH . 'uploads/perizinan/' . self::EVIDENCE_FILE;

        if (is_file($path)) {
            return;
        }

        @mkdir(dirname($path), 0755, true);

        if (@file_put_contents($path, base64_decode(self::EVIDENCE_PNG)) === false) {
            $this->markTestSkipped('Evidence upload directory is not writable in this environment.');
        }
    }

    private function makeUser(string $group): User
    {
        $provider = auth()->getProvider();
        $suffix   = bin2hex(random_bytes(4));

        $user = new User([
            'username' => $group . '_' . $suffix,
            'email'    => $group . '_' . $suffix . '@example.com',
            'password' => 'Password123!',
        ]);
        $user->active = 1;

        $provider->save($user);
        $created = $provider->findById($provider->getInsertID());
        $created->addGroup($group);

        return $created;
    }

    private function assignGuruProfile(User $user, int $guruId): User
    {
        $this->db->table('users')->where('id', $user->id)->update(['id_guru' => $guruId]);

        return auth()->getProvider()->findById($user->id);
    }

    private function createPermit(array $overrides = []): int
    {
        $this->db->table('tb_perizinan')->insert(array_merge([
            'id_siswa'        => null,
            'id_guru'         => null,
            'tanggal_mulai'   => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d'),
            'tipe_izin'       => 'Izin',
            'alasan'          => 'Test',
            'bukti'           => null,
            'status'          => 'Pending',
        ], $overrides));

        return (int) $this->db->insertID();
    }

    /**
     * @return array{0: int, 1: int, 2: int} kelas, guru wali, guru lain
     */
    private function createKelasWithTeachers(): array
    {
        $this->db->table('tb_jurusan')->insert(['jurusan' => 'UJI-' . uniqid()]);
        $jurusanId = (int) $this->db->insertID();

        $this->db->table('tb_guru')->insert([
            'nuptk'         => uniqid(),
            'nama_guru'     => 'Guru Wali',
            'jenis_kelamin' => 'L',
            'alamat'        => 'Jl. Test',
            'no_hp'         => '081200000001',
            'unique_code'   => uniqid('guru-'),
        ]);
        $waliId = (int) $this->db->insertID();

        $this->db->table('tb_guru')->insert([
            'nuptk'         => uniqid(),
            'nama_guru'     => 'Guru Lain',
            'jenis_kelamin' => 'L',
            'alamat'        => 'Jl. Test',
            'no_hp'         => '081200000002',
            'unique_code'   => uniqid('guru-'),
        ]);
        $otherTeacherId = (int) $this->db->insertID();

        $this->db->table('tb_kelas')->insert([
            'tingkat'       => '10',
            'id_jurusan'    => $jurusanId,
            'index_kelas'   => 'A',
            'id_wali_kelas' => $waliId,
        ]);
        $kelasId = (int) $this->db->insertID();

        return [$kelasId, $waliId, $otherTeacherId];
    }
}
