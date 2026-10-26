<?php

namespace Tests\Feature;

use App\Models\Inventaris;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewRequestedFeaturesTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::whereHas('role', fn($q) => $q->where('nama', 'super_admin'))->first()
            ?? User::factory()->create(['role_id' => 1]);
    }

    public function test_ruangan_export_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('ruangan.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_ruangan_export_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('ruangan.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));
    }

    public function test_ruangan_export_with_filters()
    {
        $response = $this->actingAs($this->admin)->get(route('ruangan.export', ['format' => 'excel', 'search' => 'Kantor', 'status' => 'all']));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_bulk_print_qr_requires_ids()
    {
        $response = $this->actingAs($this->admin)->post(route('manajemen-aset.bulk-print-qr'), ['ids' => []]);
        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_bulk_print_qr_generates_pdf()
    {
        $inv = Inventaris::first();
        if (!$inv) {
            $this->markTestSkipped('No inventaris in test DB.');
        }

        // Test format sticker (default)
        $response = $this->actingAs($this->admin)->post(route('manajemen-aset.bulk-print-qr'), [
            'ids' => [$inv->id],
            'format' => 'sticker',
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));

        // Test format plain
        $responsePlain = $this->actingAs($this->admin)->post(route('manajemen-aset.bulk-print-qr'), [
            'ids' => [$inv->id],
            'format' => 'plain',
        ]);

        $responsePlain->assertStatus(200);
        $this->assertStringContainsString('pdf', $responsePlain->headers->get('content-type'));
    }

    public function test_backup_forbidden_for_village_super_admin_without_developer_access()
    {
        // Pastikan username ini TIDAK ada di konfigurasi super developer
        config(['desa.super_developer_usernames' => 'mydeveloper,owner']);

        $villageAdmin = User::firstOrCreate(
            ['username' => 'admin_desa_biasa'],
            [
                'nama'     => 'Admin Desa Biasa',
                'password' => bcrypt('password'),
                'role_id'  => 1,
                'is_aktif' => 1,
            ]
        );

        $response = $this->actingAs($villageAdmin)->get(route('backup.index'));
        $response->assertStatus(403);

        User::where('username', 'admin_desa_biasa')->delete();
    }

    public function test_backup_index_accessible_by_super_developer()
    {
        $devUser = User::firstOrCreate(
            ['username' => 'developer'],
            [
                'nama'     => 'Super Developer',
                'password' => bcrypt('password'),
                'role_id'  => 1,
                'is_aktif' => 1,
            ]
        );

        $response = $this->actingAs($devUser)->get(route('backup.index'));
        $response->assertStatus(200);
        $response->assertSee('Cadangan Basis Data');
    }

    public function test_backup_create_download_and_destroy_cycle()
    {
        $devUser = User::firstOrCreate(
            ['username' => 'developer'],
            [
                'nama'     => 'Super Developer',
                'password' => bcrypt('password'),
                'role_id'  => 1,
                'is_aktif' => 1,
            ]
        );

        $dir = storage_path('app/backups');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Test create
        $response = $this->actingAs($devUser)->post(route('backup.store'));
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Check file created
        $files = glob($dir . DIRECTORY_SEPARATOR . 'backup_sima_*.sql');
        $this->assertNotEmpty($files, 'Backup SQL file should be created');

        $latestFile = basename(end($files));

        // Test download
        $downloadResponse = $this->actingAs($devUser)->get(route('backup.download', ['filename' => $latestFile]));
        $downloadResponse->assertStatus(200);
        $this->assertStringContainsString('sql', $downloadResponse->headers->get('content-type'));

        // Test delete
        $deleteResponse = $this->actingAs($devUser)->delete(route('backup.destroy', ['filename' => $latestFile]));
        $deleteResponse->assertStatus(302);
        $deleteResponse->assertSessionHas('success');

        $this->assertFileDoesNotExist($dir . DIRECTORY_SEPARATOR . $latestFile);
    }

    public function test_super_developer_accounts_are_strictly_excluded_from_user_management()
    {
        config(['desa.super_developer_usernames' => 'developer,owner,superdeveloper']);

        $owner = User::firstOrCreate(
            ['username' => 'owner'],
            ['nama' => 'Platform Owner', 'password' => bcrypt('password'), 'role_id' => 1, 'is_aktif' => 1]
        );

        $superadmin = User::firstOrCreate(
            ['username' => 'superadmin'],
            ['nama' => 'Super Admin Demo', 'password' => bcrypt('password'), 'role_id' => 1, 'is_aktif' => 1]
        );

        // 1. Index web (HTML) tidak boleh memuat nama atau username owner
        $response = $this->actingAs($superadmin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertDontSeeText('Platform Owner');

        // 2. Index AJAX / JSON tidak boleh memuat akun owner
        $ajaxResponse = $this->actingAs($superadmin)->getJson(route('users.index'), ['X-Requested-With' => 'XMLHttpRequest']);
        $ajaxResponse->assertStatus(200);
        $usernames = collect($ajaxResponse->json('users'))->pluck('username')->all();
        $this->assertNotContains('owner', $usernames);
        $this->assertNotContains('developer', $usernames);

        // 3. Akses langsung detail show / edit akun owner harus ditolak
        $showResponse = $this->actingAs($superadmin)->get(route('users.show', ['user' => $owner->id]));
        $showResponse->assertRedirect(route('users.index'));
        $showResponse->assertSessionHas('errors');

        $editResponse = $this->actingAs($superadmin)->get(route('users.edit', ['user' => $owner->id]));
        $editResponse->assertRedirect(route('users.index'));
        $editResponse->assertSessionHas('errors');

        // 4. Hapus akun owner harus ditolak
        $deleteResponse = $this->actingAs($superadmin)->delete(route('users.destroy', ['user' => $owner->id]));
        $deleteResponse->assertRedirect(route('users.index'));
        $deleteResponse->assertSessionHas('errors');
    }
}


