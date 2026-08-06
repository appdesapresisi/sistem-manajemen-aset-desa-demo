<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\Pejabat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JenisInventarisCrudTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $kaur;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $roleSA = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);
        $roleKR = Role::firstOrCreate(['nama' => 'kaur'], ['tugas' => 'KR', 'deskripsi' => 'Op']);

        $this->superAdmin = User::create([
            'nama' => 'SA JI Test', 'username' => 'sa_ji_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $roleSA->id, 'is_aktif' => 1,
        ]);
        $this->kaur = User::create([
            'nama' => 'KR JI Test', 'username' => 'kr_ji_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $roleKR->id, 'is_aktif' => 1,
        ]);
    }

    private function createJenis(array $overrides = []): JenisInventaris
    {
        return JenisInventaris::create(array_merge([
            'nama' => 'Jenis Test ' . uniqid(),
            'deskripsi' => 'Deskripsi test',
        ], $overrides));
    }

    // ─── Index ───────────────────────────────────────────────

    public function test_superadmin_can_view_jenis_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('jenis-inventaris.index'));
        $response->assertOk();
        $response->assertViewHas('jenisInventaris');
        $response->assertViewHas('stats');
    }

    public function test_kaur_can_view_jenis_index(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('jenis-inventaris.index'));
        $response->assertOk();
    }

    public function test_jenis_index_ajax_returns_json(): void
    {
        $this->createJenis();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('jenis-inventaris.index'), ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk();
        $response->assertJsonStructure(['jenisInventaris', 'stats', 'total']);
    }

    public function test_jenis_index_search_filter(): void
    {
        $this->createJenis(['nama' => 'Kendaraan Unik']);

        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('jenis-inventaris.index', ['search' => 'Kendaraan Unik']));

        $response->assertOk();
    }

    // ─── Create ──────────────────────────────────────────────

    public function test_superadmin_can_view_create_jenis_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('jenis-inventaris.create'));
        $response->assertOk();
    }

    // ─── Store ───────────────────────────────────────────────

    public function test_superadmin_can_store_jenis(): void
    {
        $nama = 'Jenis Baru ' . uniqid();

        $response = $this->actingAs($this->superAdmin)->post(route('jenis-inventaris.store'), [
            'nama' => $nama,
            'deskripsi' => 'Deskripsi baru',
            'fixed_fields' => ['kode_barang' => 1, 'nama_barang' => 2],
        ]);

        $response->assertRedirect(route('jenis-inventaris.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jenis_inventaris', ['nama' => $nama]);
    }

    public function test_kaur_cannot_store_jenis(): void
    {
        $response = $this->actingAs($this->kaur)->post(route('jenis-inventaris.store'), [
            'nama' => 'Kaur Jenis',
            'deskripsi' => 'Should fail',
        ]);

        $response->assertForbidden();
    }

    public function test_store_jenis_sends_notification_to_all(): void
    {
        $this->actingAs($this->superAdmin)->post(route('jenis-inventaris.store'), [
            'nama' => 'Notif Jenis ' . uniqid(),
            'deskripsi' => 'Test notif',
            'fixed_fields' => ['kode_barang' => 1],
        ]);

        $this->assertDatabaseHas('notifications', [
            'type' => 'jenis_inventaris_created',
        ]);
    }

    public function test_store_jenis_creates_activity_log(): void
    {
        $this->actingAs($this->superAdmin)->post(route('jenis-inventaris.store'), [
            'nama' => 'Log Jenis ' . uniqid(),
            'deskripsi' => 'Test log',
            'fixed_fields' => ['kode_barang' => 1],
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'JenisInventaris',
            'action' => 'tambah',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_store_jenis_validation_requires_nama(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('jenis-inventaris.store'), [
            'deskripsi' => 'No name',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    // ─── Show ────────────────────────────────────────────────

    public function test_superadmin_can_view_jenis_detail(): void
    {
        $jenis = $this->createJenis();

        $response = $this->actingAs($this->superAdmin)->get(route('jenis-inventaris.show', $jenis->id));
        $response->assertOk();
        $response->assertViewHas('jenis');
    }

    public function test_show_nonexistent_jenis_redirects(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('jenis-inventaris.show', 99999));
        $response->assertRedirect(route('jenis-inventaris.index'));
    }

    // ─── Edit ────────────────────────────────────────────────

    public function test_superadmin_can_view_edit_jenis_form(): void
    {
        $jenis = $this->createJenis();

        $response = $this->actingAs($this->superAdmin)->get(route('jenis-inventaris.edit', $jenis->id));
        $response->assertOk();
    }

    // ─── Update ──────────────────────────────────────────────

    public function test_superadmin_can_update_jenis(): void
    {
        $jenis = $this->createJenis();

        $response = $this->actingAs($this->superAdmin)->put(route('jenis-inventaris.update', $jenis->id), [
            'nama' => 'Updated Jenis Name',
            'deskripsi' => 'Updated deskripsi',
        ]);

        $response->assertRedirect(route('jenis-inventaris.index'));
        $jenis->refresh();
        $this->assertEquals('Updated Jenis Name', $jenis->nama);
    }

    public function test_kaur_cannot_update_jenis(): void
    {
        $jenis = $this->createJenis();

        $response = $this->actingAs($this->kaur)->put(route('jenis-inventaris.update', $jenis->id), [
            'nama' => 'Kaur Update',
            'deskripsi' => 'Should fail',
        ]);

        $response->assertForbidden();
    }

    // ─── Delete ──────────────────────────────────────────────

    public function test_superadmin_can_delete_jenis(): void
    {
        $jenis = $this->createJenis();
        $jenisId = $jenis->id;

        $response = $this->actingAs($this->superAdmin)->delete(route('jenis-inventaris.destroy', $jenisId));

        $response->assertRedirect(route('jenis-inventaris.index'));
        $this->assertDatabaseMissing('jenis_inventaris', ['id' => $jenisId]);
    }

    public function test_kaur_cannot_delete_jenis(): void
    {
        $jenis = $this->createJenis();

        $response = $this->actingAs($this->kaur)->delete(route('jenis-inventaris.destroy', $jenis->id));
        $response->assertForbidden();
    }

    // ─── Bulk Destroy ────────────────────────────────────────

    public function test_superadmin_can_bulk_destroy_jenis(): void
    {
        $j1 = $this->createJenis();
        $j2 = $this->createJenis();

        $response = $this->actingAs($this->superAdmin)->post(route('jenis-inventaris.bulk-destroy'), [
            'ids' => [$j1->id, $j2->id],
        ]);

        $this->assertDatabaseMissing('jenis_inventaris', ['id' => $j1->id]);
        $this->assertDatabaseMissing('jenis_inventaris', ['id' => $j2->id]);
    }

    // ─── API Fields Endpoint ─────────────────────────────────

    public function test_fields_api_returns_fields_for_jenis(): void
    {
        $jenis = $this->createJenis();
        JenisInventarisData::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_field' => 'kode_barang',
            'label_field' => 'Kode Barang',
            'field_type' => 'fixed',
            'fixed_field_name' => 'kode_barang',
            'tipe' => 'string',
            'is_required' => 1,
            'order' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('api.jenis-inventaris.fields', $jenis->id));

        $response->assertOk();
    }
}
