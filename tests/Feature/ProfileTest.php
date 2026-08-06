<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $role = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);

        $this->user = User::create([
            'nama' => 'Profile User',
            'username' => 'profuser_' . uniqid(),
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
            'is_aktif' => 1,
        ]);
    }

    // ─── Profile View ────────────────────────────────────────

    public function test_user_can_view_own_profile(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile'));
        $response->assertOk();
        $response->assertViewHas('user');
    }

    public function test_guest_cannot_view_profile(): void
    {
        $response = $this->get(route('profile'));
        $response->assertRedirect('/login');
    }

    // ─── Profile Update ──────────────────────────────────────

    public function test_user_can_update_profile(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'nama' => 'Updated Name',
            'username' => $this->user->username,
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->nama);
    }

    public function test_update_profile_validation_requires_nama(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'username' => $this->user->username,
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_update_profile_nama_min_3_chars(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'nama' => 'AB',
            'username' => $this->user->username,
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_update_profile_username_must_be_unique(): void
    {
        $otherUser = User::create([
            'nama' => 'Other',
            'username' => 'otherprof_' . uniqid(),
            'password' => bcrypt('pw'),
            'role_id' => $this->user->role_id,
            'is_aktif' => 1,
        ]);

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'nama' => 'My Name',
            'username' => $otherUser->username, // taken
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_update_profile_can_keep_same_username(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'nama' => 'Same Username',
            'username' => $this->user->username, // own username is fine
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success');
    }

    public function test_update_profile_creates_activity_log(): void
    {
        $this->actingAs($this->user)->put(route('profile.update'), [
            'nama' => 'Logged Name',
            'username' => $this->user->username,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'User',
            'action' => 'ubah',
            'user_id' => $this->user->id,
        ]);
    }

    // ─── Change Password ─────────────────────────────────────

    public function test_user_can_view_change_password_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile.password'));
        $response->assertOk();
    }

    public function test_user_can_change_password(): void
    {
        $response = $this->actingAs($this->user)->post(route('profile.change-password'), [
            'current_password' => 'password123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertRedirect(route('profile.password'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $this->user->password));
    }

    public function test_change_password_fails_with_wrong_current(): void
    {
        $response = $this->actingAs($this->user)->post(route('profile.change-password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_change_password_requires_confirmation(): void
    {
        $response = $this->actingAs($this->user)->post(route('profile.change-password'), [
            'current_password' => 'password123',
            'password' => 'newpassword456',
            // missing password_confirmation
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_change_password_min_8_chars(): void
    {
        $response = $this->actingAs($this->user)->post(route('profile.change-password'), [
            'current_password' => 'password123',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
