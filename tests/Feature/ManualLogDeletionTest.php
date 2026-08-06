<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\ActivityLog;
use App\Models\ExportLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ManualLogDeletionTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $sekretaris;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $roleSuperAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);
        $roleSekretaris = Role::firstOrCreate(['nama' => 'sekretaris_desa'], ['tugas' => 'SK', 'deskripsi' => 'Approve']);

        $this->superAdmin = User::create([
            'nama' => 'Super Admin Log',
            'username' => 'sa_log_' . uniqid(),
            'password' => bcrypt('pw'),
            'role_id' => $roleSuperAdmin->id,
            'is_aktif' => 1,
        ]);

        $this->sekretaris = User::create([
            'nama' => 'Sekretaris Log',
            'username' => 'sek_log_' . uniqid(),
            'password' => bcrypt('pw'),
            'role_id' => $roleSekretaris->id,
            'is_aktif' => 1,
        ]);
    }

    private function createActivityLog(?int $userId = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId ?? $this->superAdmin->id,
            'module' => 'Inventaris',
            'action' => 'tambah',
            'detail' => 'Test activity log ' . uniqid(),
            'subject_name' => 'Inventaris Test',
        ]);
    }

    private function createExportLog(?int $userId = null): ExportLog
    {
        return ExportLog::create([
            'user_id' => $userId ?? $this->superAdmin->id,
            'tipe_export' => 'excel',
            'nama_file' => 'test-export-' . uniqid() . '.xlsx',
        ]);
    }

    public function test_super_admin_can_delete_single_activity_log(): void
    {
        $log = $this->createActivityLog();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('dashboard.activities.destroy', $log->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('activity_logs', ['id' => $log->id]);
    }

    public function test_non_super_admin_cannot_delete_single_activity_log(): void
    {
        $log = $this->createActivityLog();

        $response = $this->actingAs($this->sekretaris)
            ->delete(route('dashboard.activities.destroy', $log->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('activity_logs', ['id' => $log->id]);
    }

    public function test_super_admin_can_delete_all_activity_logs(): void
    {
        $this->createActivityLog();
        $this->createActivityLog($this->sekretaris->id);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('dashboard.activities.destroy-all'));

        $response->assertRedirect();
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_non_super_admin_cannot_delete_all_activity_logs(): void
    {
        $firstLog = $this->createActivityLog();
        $secondLog = $this->createActivityLog($this->sekretaris->id);

        $response = $this->actingAs($this->sekretaris)
            ->post(route('dashboard.activities.destroy-all'));

        $response->assertForbidden();
        $this->assertDatabaseHas('activity_logs', ['id' => $firstLog->id]);
        $this->assertDatabaseHas('activity_logs', ['id' => $secondLog->id]);
    }

    public function test_activity_delete_buttons_only_visible_for_super_admin(): void
    {
        $this->createActivityLog();

        $responseSuperAdmin = $this->actingAs($this->superAdmin)->get(route('dashboard.activities'));
        $responseSuperAdmin->assertOk();
        $responseSuperAdmin->assertSee('<form id="deleteAllActivitiesForm"', false);
        $responseSuperAdmin->assertSee('<form id="deleteActivityForm', false);

        $responseSekretaris = $this->actingAs($this->sekretaris)->get(route('dashboard.activities'));
        $responseSekretaris->assertOk();
        $responseSekretaris->assertDontSee('<form id="deleteAllActivitiesForm"', false);
        $responseSekretaris->assertDontSee('<form id="deleteActivityForm', false);
    }

    public function test_super_admin_can_delete_single_export_history_log(): void
    {
        $log = $this->createExportLog();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('export-data.history.destroy', $log->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('export_logs', ['id' => $log->id]);
    }

    public function test_non_super_admin_cannot_delete_single_export_history_log(): void
    {
        $log = $this->createExportLog();

        $response = $this->actingAs($this->sekretaris)
            ->delete(route('export-data.history.destroy', $log->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('export_logs', ['id' => $log->id]);
    }

    public function test_super_admin_can_delete_all_export_history_logs(): void
    {
        $this->createExportLog();
        $this->createExportLog($this->sekretaris->id);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('export-data.history.destroy-all'));

        $response->assertRedirect();
        $this->assertSame(0, ExportLog::count());
    }

    public function test_non_super_admin_cannot_delete_all_export_history_logs(): void
    {
        $firstLog = $this->createExportLog();
        $secondLog = $this->createExportLog($this->sekretaris->id);

        $response = $this->actingAs($this->sekretaris)
            ->post(route('export-data.history.destroy-all'));

        $response->assertForbidden();
        $this->assertDatabaseHas('export_logs', ['id' => $firstLog->id]);
        $this->assertDatabaseHas('export_logs', ['id' => $secondLog->id]);
    }

    public function test_export_history_delete_buttons_only_visible_for_super_admin(): void
    {
        $this->createExportLog();

        $responseSuperAdmin = $this->actingAs($this->superAdmin)->get(route('export-data.history'));
        $responseSuperAdmin->assertOk();
        $responseSuperAdmin->assertSee('<form id="deleteAllExportHistoryForm"', false);
        $responseSuperAdmin->assertSee('<form id="deleteExportHistoryForm', false);

        $responseSekretaris = $this->actingAs($this->sekretaris)->get(route('export-data.history'));
        $responseSekretaris->assertOk();
        $responseSekretaris->assertDontSee('<form id="deleteAllExportHistoryForm"', false);
        $responseSekretaris->assertDontSee('<form id="deleteExportHistoryForm', false);
    }
}
