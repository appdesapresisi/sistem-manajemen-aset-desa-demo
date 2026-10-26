<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Bidang;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $kaur;
    private Bidang $bidang;
    private Role $roleSA;
    private Role $roleSK;
    private Role $roleKR;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->roleSA = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);
        $this->roleSK = Role::firstOrCreate(['nama' => 'sekretaris_desa'], ['tugas' => 'SK', 'deskripsi' => 'Approve']);
        $this->roleKR = Role::firstOrCreate(['nama' => 'kaur'], ['tugas' => 'KR', 'deskripsi' => 'Operator']);
        $this->bidang = Bidang::firstOrCreate(
            ['nama' => 'Bidang Test'],
            ['deskripsi' => 'Bidang untuk pengujian user role kaur']
        );

        $this->superAdmin = User::create([
            'nama' => 'SA User Test', 'username' => 'sa_usr_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleSA->id, 'is_aktif' => 1,
        ]);

        $this->kaur = User::create([
            'nama' => 'KR User Test', 'username' => 'kr_usr_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleKR->id, 'bidang_id' => $this->bidang->id, 'is_aktif' => 1,
        ]);
    }

    // ─── Index ───────────────────────────────────────────────

    public function test_superadmin_can_view_users_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('users.index'));
        $response->assertOk();
        $response->assertViewHas('users');
        $response->assertViewHas('stats');
    }

    public function test_kaur_can_view_users_index(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('users.index'));
        $response->assertOk();
    }

    public function test_users_index_ajax_returns_json(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('users.index'), ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk();
        $response->assertJsonStructure(['users', 'stats']);
    }

    public function test_users_index_search_filter(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('users.index', ['search' => 'SA User']));
        $response->assertOk();
    }

    public function test_kaur_role_displays_bidang_on_users_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('users.index', ['search' => 'KR User Test']));
        $response->assertOk();
        $response->assertSeeText('Kaur - ' . $this->bidang->nama);
    }

    // ─── Create ──────────────────────────────────────────────

    public function test_superadmin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('users.create'));
        $response->assertOk();
    }

    public function test_kaur_cannot_create_user(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('users.create'));
        $response->assertForbidden();
    }

    // ─── Store ───────────────────────────────────────────────

    public function test_superadmin_can_store_user(): void
    {
        $username = 'newuser_' . uniqid();

        $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama' => 'New User Test',
            'username' => $username,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kaur',
            'bidang_id' => $this->bidang->id,
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['username' => $username]);
    }

    public function test_store_user_validation_requires_name(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'username' => 'test_' . uniqid(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kaur',
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_store_user_validation_requires_unique_username(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama' => 'Duplicate User',
            'username' => $this->superAdmin->username, // already exists
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kaur',
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_store_user_validation_requires_password_confirmation(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama' => 'No Confirm',
            'username' => 'noconfirm_' . uniqid(),
            'password' => 'password123',
            // missing password_confirmation
            'role' => 'kaur',
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_store_user_validation_requires_pejabat_fields_when_checked(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama' => 'User Dengan Pejabat',
            'username' => 'user_pejabat_' . uniqid(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'sekretaris_desa',
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
            'create_pejabat' => '1',
            'pejabat_nama' => '',
            'pejabat_jabatan' => '',
            'pejabat_tugas' => '',
            'pejabat_alamat' => '',
        ]);

        $response->assertSessionHasErrors([
            'pejabat_nama',
            'pejabat_jabatan',
            'pejabat_tugas',
            'pejabat_alamat',
        ]);
    }

    public function test_superadmin_can_store_user_with_pejabat_without_signature(): void
    {
        $username = 'user_pejabat_nottd_' . uniqid();

        $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama' => 'User Pejabat Tanpa TTD',
            'username' => $username,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kaur',
            'bidang_id' => $this->bidang->id,
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
            'create_pejabat' => '1',
            'pejabat_nama' => 'Pejabat Tanpa TTD',
            'pejabat_jabatan' => 'Sekretaris Desa',
            'pejabat_tugas' => 'Menandatangani dokumen dan verifikasi administrasi',
            'pejabat_alamat' => 'Jl. Raya Demo Pejabat',
            // ttd_path tidak diisi (harus tetap lolos)
        ]);

        if ($response->isRedirect('http://127.0.0.1:8000')) {
            dump($response->status());
            dump(session()->all());
        }
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $user = User::where('username', $username)->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->pejabat_id);
        $this->assertDatabaseHas('pejabats', [
            'id' => $user->pejabat_id,
            'ttd_path' => null,
        ]);
    }

    public function test_cannot_store_second_sekretaris_desa_user(): void
    {
        User::create([
            'nama' => 'Sekretaris Existing',
            'username' => 'sek_existing_' . uniqid(),
            'password' => bcrypt('pw'),
            'role_id' => $this->roleSK->id,
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $username = 'sek_baru_' . uniqid();
        $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama' => 'Sekretaris Baru',
            'username' => $username,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'sekretaris_desa',
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('users', ['username' => $username]);
    }

    public function test_store_user_creates_activity_log(): void
    {
        $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama' => 'Log User Test',
            'username' => 'loguser_' . uniqid(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kaur',
            'bidang_id' => $this->bidang->id,
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'User',
            'action' => 'tambah',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    // ─── Show ────────────────────────────────────────────────

    public function test_superadmin_can_view_user_detail(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('users.show', $this->kaur->id));
        $response->assertOk();
        $response->assertViewHas('user');
    }

    public function test_show_nonexistent_user_redirects(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('users.show', 99999));
        $response->assertRedirect(route('users.index'));
    }

    public function test_kaur_role_displays_bidang_on_user_detail(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('users.show', $this->kaur->id));
        $response->assertOk();
        $response->assertSeeText('Kaur - ' . $this->bidang->nama);
    }

    // ─── Edit ────────────────────────────────────────────────

    public function test_superadmin_can_view_edit_user_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('users.edit', $this->kaur->id));
        $response->assertOk();
        $response->assertViewHas('user');
        $response->assertViewHas('roles');
    }

    public function test_kaur_cannot_edit_user(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('users.edit', $this->superAdmin->id));
        $response->assertForbidden();
    }

    // ─── Update ──────────────────────────────────────────────

    public function test_superadmin_can_update_user(): void
    {
        $target = User::create([
            'nama' => 'Target Update', 'username' => 'target_upd_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleKR->id, 'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('users.update', $target->id), [
            'nama' => 'Updated Name',
            'username' => $target->username,
            'role' => 'kaur',
            'bidang_id' => $this->bidang->id,
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo Updated',
        ]);

        $response->assertRedirect(route('users.index'));
        $target->refresh();
        $this->assertEquals('Updated Name', $target->nama);
    }

    public function test_update_user_password_optional(): void
    {
        $target = User::create([
            'nama' => 'No PW Change', 'username' => 'nopwch_' . uniqid(),
            'password' => bcrypt('oldpassword'), 'role_id' => $this->roleKR->id, 'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('users.update', $target->id), [
            'nama' => 'Still No PW',
            'username' => $target->username,
            'role' => 'kaur',
            'bidang_id' => $this->bidang->id,
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo Updated',
            // no password field = no password change
        ]);

        $response->assertRedirect(route('users.index'));
    }

    public function test_superadmin_can_update_user_to_inactive(): void
    {
        $target = User::create([
            'nama' => 'Inactive Target', 'username' => 'inactive_upd_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleKR->id, 'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('users.update', $target->id), [
            'nama' => 'Inactive Target',
            'username' => $target->username,
            'role' => 'kaur',
            'bidang_id' => $this->bidang->id,
            'is_aktif' => 0,
            'alamat' => 'Jl. Raya Demo Updated',
        ]);

        $response->assertRedirect(route('users.index'));
        $target->refresh();
        $this->assertSame(0, (int) $target->is_aktif);
    }

    public function test_cannot_update_user_to_sekretaris_if_another_exists(): void
    {
        User::create([
            'nama' => 'Sekretaris Existing',
            'username' => 'sek_existing_upd_' . uniqid(),
            'password' => bcrypt('pw'),
            'role_id' => $this->roleSK->id,
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $target = User::create([
            'nama' => 'Target Kaur',
            'username' => 'target_kaur_' . uniqid(),
            'password' => bcrypt('pw'),
            'role_id' => $this->roleKR->id,
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo',
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('users.update', $target->id), [
            'nama' => 'Target Kaur',
            'username' => $target->username,
            'role' => 'sekretaris_desa',
            'is_aktif' => 1,
            'alamat' => 'Jl. Raya Demo Updated',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $target->refresh();
        $this->assertEquals($this->roleKR->id, $target->role_id);
    }

    // ─── Delete ──────────────────────────────────────────────

    public function test_superadmin_can_delete_user(): void
    {
        $target = User::create([
            'nama' => 'To Delete', 'username' => 'del_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleKR->id, 'is_aktif' => 1,
        ]);
        $targetId = $target->id;

        $response = $this->actingAs($this->superAdmin)->delete(route('users.destroy', $targetId));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $targetId]);
    }

    public function test_kaur_cannot_delete_user(): void
    {
        $target = User::create([
            'nama' => 'No Delete', 'username' => 'nodel_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleKR->id, 'is_aktif' => 1,
        ]);

        $response = $this->actingAs($this->kaur)->delete(route('users.destroy', $target->id));
        $response->assertForbidden();
    }

    public function test_cannot_delete_user_with_inventaris(): void
    {
        $jenis = \App\Models\JenisInventaris::create(['nama' => 'Jenis Del Test ' . uniqid(), 'deskripsi' => 'Test']);

        $target = User::create([
            'nama' => 'Has Inv', 'username' => 'hasinv_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleKR->id, 'is_aktif' => 1,
        ]);

        $inv = new \App\Models\Inventaris();
        $inv->jenis_inventaris_id = $jenis->id;
        $inv->nama_barang = 'Blocking Item';
        $inv->kode_barang = 'BLK-' . uniqid();
        $inv->created_by = $target->id;
        $inv->status = 'pending';
        $inv->save();

        $response = $this->actingAs($this->superAdmin)->delete(route('users.destroy', $target->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    // ─── Bulk Destroy ────────────────────────────────────────

    public function test_superadmin_can_bulk_destroy_users(): void
    {
        $u1 = User::create([
            'nama' => 'Bulk Del 1', 'username' => 'bd1_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleKR->id, 'is_aktif' => 1,
        ]);
        $u2 = User::create([
            'nama' => 'Bulk Del 2', 'username' => 'bd2_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $this->roleKR->id, 'is_aktif' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('users.bulk-destroy'), [
            'ids' => [$u1->id, $u2->id],
        ]);

        $this->assertDatabaseMissing('users', ['id' => $u1->id]);
        $this->assertDatabaseMissing('users', ['id' => $u2->id]);
    }

    public function test_cannot_update_user_username_to_reserved_superdeveloper_username(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('users.update', $this->kaur->id), [
            'nama' => 'Kaur Updated',
            'username' => 'developer',
            'role' => 'kaur',
            'is_aktif' => 1,
            'alamat' => 'Desa Demo',
            'bidang_id' => $this->bidang->id,
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertDatabaseMissing('users', ['id' => $this->kaur->id, 'username' => 'developer']);
    }

    public function test_user_cannot_update_own_profile_username_to_reserved_superdeveloper_username(): void
    {
        $response = $this->actingAs($this->kaur)->put(route('profile.update'), [
            'nama' => $this->kaur->nama,
            'username' => 'owner',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertDatabaseMissing('users', ['id' => $this->kaur->id, 'username' => 'owner']);
    }

    public function test_store_user_form_request_rejects_reserved_superdeveloper_username(): void
    {
        $uniqueAttempt = 'sima_superdeveloper_' . uniqid();
        config(['desa.super_developer_usernames' => 'developer,owner,' . $uniqueAttempt]);

        $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama' => 'Attempt Superdev',
            'username' => $uniqueAttempt,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kaur',
            'bidang_id' => $this->bidang->id,
            'is_aktif' => 1,
            'alamat' => 'Desa Demo',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertDatabaseMissing('users', ['username' => $uniqueAttempt]);
    }
}
