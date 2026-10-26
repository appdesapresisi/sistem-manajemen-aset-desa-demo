<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\ExportLog;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExcludeSuperDeveloperFromLogsTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $superDeveloper;
    private User $platformOwner;

    protected function setUp(): void
    {
        parent::setUp();

        $roleSuperAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Super Admin']);
        $roleSuperDev   = Role::firstOrCreate(['nama' => 'super_developer'], ['tugas' => 'SD', 'deskripsi' => 'Super Developer']);

        $this->superAdmin = User::create([
            'nama'     => 'Super Admin Desa',
            'username' => 'sa_desa_' . uniqid(),
            'password' => bcrypt('password'),
            'role_id'  => $roleSuperAdmin->id,
            'is_aktif' => 1,
        ]);

        $this->superDeveloper = User::create([
            'nama'     => 'Super Developer QA',
            'username' => 'developer_qa_' . uniqid(),
            'password' => bcrypt('password'),
            'role_id'  => $roleSuperDev->id,
            'is_aktif' => 1,
        ]);

        $this->platformOwner = User::create([
            'nama'     => 'Platform Owner QA',
            'username' => 'owner',
            'password' => bcrypt('password'),
            'role_id'  => $roleSuperAdmin->id,
            'is_aktif' => 1,
        ]);
    }

    public function test_activity_log_exclude_super_developers_scope(): void
    {
        $logAdmin = ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'module'  => 'Inventaris',
            'action'  => 'tambah',
            'detail'  => 'Aktivitas Admin Normal',
        ]);

        $logDev = ActivityLog::create([
            'user_id' => $this->superDeveloper->id,
            'module'  => 'Backup',
            'action'  => 'tambah',
            'detail'  => 'Aktivitas Super Developer',
        ]);

        $logOwner = ActivityLog::create([
            'user_id' => $this->platformOwner->id,
            'module'  => 'Backup',
            'action'  => 'hapus',
            'detail'  => 'Aktivitas Platform Owner',
        ]);

        $excludedIds = ActivityLog::excludeSuperDevelopers()->pluck('id');

        $this->assertTrue($excludedIds->contains($logAdmin->id), 'Log Super Admin wajib tampil.');
        $this->assertFalse($excludedIds->contains($logDev->id), 'Log Super Developer TIDAK boleh tampil.');
        $this->assertFalse($excludedIds->contains($logOwner->id), 'Log Platform Owner TIDAK boleh tampil.');
    }

    public function test_export_log_exclude_super_developers_scope(): void
    {
        $exportAdmin = ExportLog::create([
            'user_id'     => $this->superAdmin->id,
            'tipe_export' => 'excel',
            'nama_file'   => 'export_admin_' . uniqid() . '.xlsx',
        ]);

        $exportDev = ExportLog::create([
            'user_id'     => $this->superDeveloper->id,
            'tipe_export' => 'excel',
            'nama_file'   => 'export_dev_' . uniqid() . '.xlsx',
        ]);

        $exportOwner = ExportLog::create([
            'user_id'     => $this->platformOwner->id,
            'tipe_export' => 'pdf',
            'nama_file'   => 'export_owner_' . uniqid() . '.pdf',
        ]);

        $excludedIds = ExportLog::excludeSuperDevelopers()->pluck('id');

        $this->assertTrue($excludedIds->contains($exportAdmin->id), 'Export Log Super Admin wajib tampil.');
        $this->assertFalse($excludedIds->contains($exportDev->id), 'Export Log Super Developer TIDAK boleh tampil.');
        $this->assertFalse($excludedIds->contains($exportOwner->id), 'Export Log Platform Owner TIDAK boleh tampil.');
    }

    public function test_dashboard_activities_web_page_does_not_show_super_developer_or_owner(): void
    {
        $uniqueAdminText = 'Detail Khusus Admin ' . uniqid();
        $uniqueDevText   = 'Detail Khusus Developer ' . uniqid();
        $uniqueOwnerText = 'Detail Khusus Owner ' . uniqid();

        ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'module'  => 'Inventaris',
            'action'  => 'tambah',
            'detail'  => $uniqueAdminText,
        ]);

        ActivityLog::create([
            'user_id' => $this->superDeveloper->id,
            'module'  => 'Backup',
            'action'  => 'tambah',
            'detail'  => $uniqueDevText,
        ]);

        ActivityLog::create([
            'user_id' => $this->platformOwner->id,
            'module'  => 'Backup',
            'action'  => 'hapus',
            'detail'  => $uniqueOwnerText,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.activities'));
        $response->assertStatus(200);
        $response->assertSee($uniqueAdminText);
        $response->assertDontSee($uniqueDevText);
        $response->assertDontSee($uniqueOwnerText);
    }

    public function test_export_history_web_page_does_not_show_super_developer_or_owner(): void
    {
        $uniqueAdminFile = 'admin_file_' . uniqid() . '.xlsx';
        $uniqueDevFile   = 'dev_file_' . uniqid() . '.xlsx';
        $uniqueOwnerFile = 'owner_file_' . uniqid() . '.xlsx';

        ExportLog::create([
            'user_id'     => $this->superAdmin->id,
            'tipe_export' => 'excel',
            'nama_file'   => $uniqueAdminFile,
        ]);

        ExportLog::create([
            'user_id'     => $this->superDeveloper->id,
            'tipe_export' => 'excel',
            'nama_file'   => $uniqueDevFile,
        ]);

        ExportLog::create([
            'user_id'     => $this->platformOwner->id,
            'tipe_export' => 'excel',
            'nama_file'   => $uniqueOwnerFile,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('export-data.history'));
        $response->assertStatus(200);
        $response->assertSee($uniqueAdminFile);
        $response->assertDontSee($uniqueDevFile);
        $response->assertDontSee($uniqueOwnerFile);
    }

    public function test_activity_logger_service_skips_when_authenticated_as_super_developer(): void
    {
        $this->actingAs($this->superDeveloper);

        $uniqueAction = 'Aksi Khusus Insinyur ' . uniqid();
        ActivityLogger::log('Backup', 'tambah', $uniqueAction);

        $this->assertDatabaseMissing('activity_logs', [
            'detail' => $uniqueAction,
        ]);
    }
}
