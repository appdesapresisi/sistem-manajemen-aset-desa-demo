<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Pejabat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PejabatCrudTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $kaur;
    private Pejabat $ownPejabat;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $roleSA = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);
        $roleKR = Role::firstOrCreate(['nama' => 'kaur'], ['tugas' => 'KR', 'deskripsi' => 'Op']);

        $this->superAdmin = User::create([
            'nama' => 'SA Pejabat Test', 'username' => 'sa_pjb_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $roleSA->id, 'is_aktif' => 1,
        ]);
        $this->kaur = User::create([
            'nama' => 'KR Pejabat Test', 'username' => 'kr_pjb_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $roleKR->id, 'is_aktif' => 1,
        ]);

        $this->ownPejabat = Pejabat::create([
            'nama' => 'Pejabat Milik Kaur',
            'jabatan' => 'Kaur Umum',
            'tugas' => 'Mengelola administrasi umum',
            'is_aktif' => 1,
        ]);

        $this->kaur->pejabat_id = $this->ownPejabat->id;
        $this->kaur->save();
    }

    private function createPejabat(array $overrides = []): Pejabat
    {
        return Pejabat::create(array_merge([
            'nama' => 'Pejabat Test ' . uniqid(),
            'jabatan' => 'Kepala Urusan Keuangan',
            'tugas' => 'Mengelola keuangan desa',
            'is_aktif' => 1,
        ], $overrides));
    }

    // ─── Index ───────────────────────────────────────────────

    public function test_superadmin_can_view_pejabat_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('pejabat.index'));
        $response->assertOk();
        $response->assertViewHas('pejabats');
        $response->assertViewHas('stats');
    }

    public function test_kaur_can_view_pejabat_index(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('pejabat.index'));
        $response->assertOk();
    }

    public function test_pejabat_index_ajax_returns_json(): void
    {
        $this->createPejabat();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('pejabat.index'), ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk();
        $response->assertJsonStructure(['pejabats', 'stats']);
    }

    public function test_pejabat_index_search_filter(): void
    {
        $this->createPejabat(['nama' => 'Unik Nama Pejabat']);

        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('pejabat.index', ['search' => 'Unik Nama']));
        $response->assertOk();
    }

    // ─── Create ──────────────────────────────────────────────

    public function test_superadmin_can_view_create_pejabat_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('pejabat.create'));
        $response->assertOk();
    }

    public function test_kaur_cannot_create_pejabat(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('pejabat.create'));
        $response->assertForbidden();
    }

    // ─── Store ───────────────────────────────────────────────

    public function test_superadmin_can_store_pejabat(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('ttd.png', 200, 100);

        $response = $this->actingAs($this->superAdmin)->post(route('pejabat.store'), [
            'nama' => 'New Pejabat',
            'jabatan' => 'Sekretaris Desa',
            'tugas' => 'Tugas baru',
            'ttd_path' => $file,
        ]);

        $response->assertRedirect(route('pejabat.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pejabats', ['nama' => 'New Pejabat']);
    }

    public function test_superadmin_can_store_pejabat_without_signature(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->superAdmin)->post(route('pejabat.store'), [
            'nama' => 'Pejabat Tanpa TTD',
            'jabatan' => 'Kaur Umum',
            'tugas' => 'Melakukan administrasi aset desa',
        ]);

        $response->assertRedirect(route('pejabat.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pejabats', [
            'nama' => 'Pejabat Tanpa TTD',
            'ttd_path' => null,
        ]);
    }

    public function test_store_pejabat_validation_requires_nama(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->superAdmin)->post(route('pejabat.store'), [
            'jabatan' => 'Kaur',
            'ttd_path' => UploadedFile::fake()->image('ttd.png'),
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_store_pejabat_creates_activity_log(): void
    {
        Storage::fake('public');

        $this->actingAs($this->superAdmin)->post(route('pejabat.store'), [
            'nama' => 'Log Pejabat',
            'jabatan' => 'Kaur Umum',
            'ttd_path' => UploadedFile::fake()->image('ttd.png'),
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Pejabat',
            'action' => 'tambah',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    // ─── Show ────────────────────────────────────────────────

    public function test_superadmin_can_view_pejabat_detail(): void
    {
        $pejabat = $this->createPejabat();

        $response = $this->actingAs($this->superAdmin)->get(route('pejabat.show', $pejabat->id));
        $response->assertOk();
    }

    public function test_show_nonexistent_pejabat_redirects(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('pejabat.show', 99999));
        $response->assertRedirect(route('pejabat.index'));
    }

    // ─── Edit ────────────────────────────────────────────────

    public function test_superadmin_can_view_edit_pejabat_form(): void
    {
        $pejabat = $this->createPejabat();

        $response = $this->actingAs($this->superAdmin)->get(route('pejabat.edit', $pejabat->id));
        $response->assertOk();
    }

    public function test_kaur_can_edit_own_pejabat_form(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('pejabat.edit', $this->ownPejabat->id));
        $response->assertOk();
    }

    public function test_kaur_cannot_edit_other_pejabat(): void
    {
        $pejabat = $this->createPejabat();

        $response = $this->actingAs($this->kaur)->get(route('pejabat.edit', $pejabat->id));
        $response->assertForbidden();
    }

    // ─── Update ──────────────────────────────────────────────

    public function test_superadmin_can_update_pejabat(): void
    {
        $pejabat = $this->createPejabat();

        $response = $this->actingAs($this->superAdmin)->put(route('pejabat.update', $pejabat->id), [
            'nama' => 'Updated Pejabat',
            'jabatan' => 'Kepala Desa',
            'tugas' => 'Tugas baru',
        ]);

        $response->assertRedirect(route('pejabat.index'));
        $pejabat->refresh();
        $this->assertEquals('Updated Pejabat', $pejabat->nama);
    }

    public function test_kaur_can_update_own_pejabat(): void
    {
        $response = $this->actingAs($this->kaur)->put(route('pejabat.update', $this->ownPejabat->id), [
            'nama' => 'Pejabat Kaur Updated',
            'jabatan' => 'Kaur Umum',
            'tugas' => 'Tugas diperbarui oleh pemilik akun',
        ]);

        $response->assertRedirect(route('pejabat.index'));
        $this->ownPejabat->refresh();
        $this->assertEquals('Pejabat Kaur Updated', $this->ownPejabat->nama);
    }

    public function test_kaur_cannot_change_own_pejabat_status(): void
    {
        $this->assertEquals(1, (int) $this->ownPejabat->is_aktif);

        $response = $this->actingAs($this->kaur)->put(route('pejabat.update', $this->ownPejabat->id), [
            'nama' => 'Pejabat Kaur Status Tetap',
            'jabatan' => 'Kaur Umum',
            'tugas' => 'Mencoba ubah status lewat request',
            'is_aktif' => 0,
        ]);

        $response->assertRedirect(route('pejabat.index'));
        $this->ownPejabat->refresh();
        $this->assertEquals(1, (int) $this->ownPejabat->is_aktif);
        $this->assertEquals('Pejabat Kaur Status Tetap', $this->ownPejabat->nama);
    }

    public function test_kaur_cannot_update_other_pejabat(): void
    {
        $pejabat = $this->createPejabat();

        $response = $this->actingAs($this->kaur)->put(route('pejabat.update', $pejabat->id), [
            'nama' => 'Kaur Update',
            'jabatan' => 'Kaur',
        ]);

        $response->assertForbidden();
    }

    // ─── Delete ──────────────────────────────────────────────

    public function test_superadmin_can_delete_pejabat(): void
    {
        $pejabat = $this->createPejabat();
        $pejabatId = $pejabat->id;

        $response = $this->actingAs($this->superAdmin)->delete(route('pejabat.destroy', $pejabatId));

        $response->assertRedirect(route('pejabat.index'));
        $this->assertDatabaseMissing('pejabats', ['id' => $pejabatId]);
    }

    public function test_kaur_cannot_delete_pejabat(): void
    {
        $pejabat = $this->createPejabat();

        $response = $this->actingAs($this->kaur)->delete(route('pejabat.destroy', $pejabat->id));
        $response->assertForbidden();
    }

    // ─── Bulk Destroy ────────────────────────────────────────

    public function test_superadmin_can_bulk_destroy_pejabat(): void
    {
        $p1 = $this->createPejabat();
        $p2 = $this->createPejabat();

        $response = $this->actingAs($this->superAdmin)->post(route('pejabat.bulk-destroy'), [
            'ids' => [$p1->id, $p2->id],
        ]);

        $this->assertDatabaseMissing('pejabats', ['id' => $p1->id]);
        $this->assertDatabaseMissing('pejabats', ['id' => $p2->id]);
    }
}
