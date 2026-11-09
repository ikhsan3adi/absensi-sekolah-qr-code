<?php

namespace Tests\Integration;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Regression tests for tanggal validation before presensi queries.
 *
 * @internal
 */
final class PresensiDateValidationTest extends CIUnitTestCase
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

    /**
     * @var array<string, mixed>
     */
    private array $filtersBackup = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->filtersBackup = config('Filters')->globals;
        unset(config('Filters')->globals['before']['csrf']);
    }

    protected function tearDown(): void
    {
        config('Filters')->globals = $this->filtersBackup;

        parent::tearDown();
    }

    public function testTeacherAttendanceListRejectsSqlInjectionPayload(): void
    {
        $this->actingAs($this->makeUser('guru'));

        $this->post('teacher/attendance/get-list', [
            'id_kelas' => '1',
            'kelas'    => 'X',
            'tanggal'  => "2024-01-01' OR SLEEP(5)-- -",
        ])->assertStatus(400);
    }

    public function testTeacherAttendanceListRejectsMalformedDate(): void
    {
        $this->actingAs($this->makeUser('guru'));

        $this->post('teacher/attendance/get-list', [
            'id_kelas' => '1',
            'kelas'    => 'X',
            'tanggal'  => '2024-02-31',
        ])->assertStatus(400);
    }

    public function testAdminStudentAttendanceListRejectsSqlInjectionPayload(): void
    {
        $this->actingAs($this->makeUser('superadmin'));

        $this->post('admin/absen-siswa', [
            'id_kelas' => '1',
            'kelas'    => 'X',
            'tanggal'  => "2024-01-01' OR 1=1 -- ",
        ])->assertStatus(400);
    }

    public function testAdminTeacherAttendanceListRejectsSqlInjectionPayload(): void
    {
        $this->actingAs($this->makeUser('superadmin'));

        $this->post('admin/absen-guru', [
            'tanggal' => "2024-01-01' OR 1=1 -- ",
        ])->assertStatus(400);
    }

    public function testTeacherAttendanceListAcceptsValidDate(): void
    {
        $this->actingAs($this->makeUser('guru'));

        $this->post('teacher/attendance/get-list', [
            'id_kelas' => '1',
            'kelas'    => 'X',
            'tanggal'  => '2024-01-01',
        ])->assertStatus(200);
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
}
