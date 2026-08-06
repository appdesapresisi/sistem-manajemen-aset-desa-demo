<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    private function createRole(string $nama = 'super_admin'): Role
    {
        return Role::firstOrCreate(
            ['nama' => $nama],
            ['tugas' => ucwords(str_replace('_', ' ', $nama)), 'deskripsi' => 'Test role']
        );
    }

    private function createUser(array $overrides = []): User
    {
        $role = $this->createRole($overrides['role_nama'] ?? 'super_admin');
        unset($overrides['role_nama']);

        return User::create(array_merge([
            'nama' => 'Test User',
            'username' => 'testuser_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'is_aktif' => 1,
        ], $overrides));
    }

    // ─── Login Page ──────────────────────────────────────────

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('login');
    }

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    // ─── Successful Login ────────────────────────────────────

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = $this->createUser(['username' => 'validuser', 'password' => bcrypt('secret123')]);

        $response = $this->post('/login', [
            'username' => 'validuser',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_returns_json_for_ajax_request(): void
    {
        $user = $this->createUser(['username' => 'ajaxuser', 'password' => bcrypt('secret123')]);

        $response = $this->postJson('/login', [
            'username' => 'ajaxuser',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['success', 'message', 'redirect']);
    }

    // ─── Failed Login ────────────────────────────────────────

    public function test_login_fails_with_wrong_password(): void
    {
        $this->createUser(['username' => 'wrongpw', 'password' => bcrypt('correct')]);

        $response = $this->post('/login', [
            'username' => 'wrongpw',
            'password' => 'incorrect',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_username(): void
    {
        $response = $this->post('/login', [
            'username' => 'nobody_' . uniqid(),
            'password' => 'whatever',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->createUser([
            'username' => 'inactiveuser',
            'password' => bcrypt('secret123'),
            'is_aktif' => 0,
        ]);

        $response = $this->post('/login', [
            'username' => 'inactiveuser',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_validation_requires_username_and_password(): void
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['username', 'password']);
    }

    public function test_login_ajax_fails_returns_422(): void
    {
        $this->createUser(['username' => 'ajaxfail', 'password' => bcrypt('correct')]);

        $response = $this->postJson('/login', [
            'username' => 'ajaxfail',
            'password' => 'wrong',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }

    public function test_login_blocked_when_maximum_device_limit_reached(): void
    {
        $user = $this->createUser(['username' => 'limit_user', 'password' => bcrypt('secret123')]);

        for ($i = 1; $i <= 5; $i++) {
            DB::table('user_device_sessions')->insert([
                'user_id' => $user->id,
                'device_id' => 'device-' . $i,
                'session_id' => 'session-' . $i,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'last_seen_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->withCookie('device_id', 'device-6')->post('/login', [
            'username' => 'limit_user',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_is_allowed_for_registered_device_when_limit_is_reached(): void
    {
        $user = $this->createUser(['username' => 'existing_device', 'password' => bcrypt('secret123')]);

        for ($i = 1; $i <= 5; $i++) {
            DB::table('user_device_sessions')->insert([
                'user_id' => $user->id,
                'device_id' => 'known-device-' . $i,
                'session_id' => 'known-session-' . $i,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'last_seen_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->withCookie('device_id', 'known-device-3')->post('/login', [
            'username' => 'existing_device',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_releases_slot_so_new_device_can_login(): void
    {
        $user = $this->createUser(['username' => 'slot_release_user', 'password' => bcrypt('secret123')]);

        for ($i = 1; $i <= 5; $i++) {
            DB::table('user_device_sessions')->insert([
                'user_id' => $user->id,
                'device_id' => 'slot-device-' . $i,
                'session_id' => 'slot-session-' . $i,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'last_seen_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $logoutResponse = $this->withCookie('device_id', 'slot-device-1')
            ->actingAs($user)
            ->get('/logout');

        $logoutResponse->assertRedirect(route('login'));
        $this->assertGuest();

        $this->assertDatabaseMissing('user_device_sessions', [
            'user_id' => $user->id,
            'device_id' => 'slot-device-1',
        ]);

        $this->assertSame(
            4,
            DB::table('user_device_sessions')->where('user_id', $user->id)->count()
        );

        $loginResponse = $this->withCookie('device_id', 'slot-device-6')->post('/login', [
            'username' => 'slot_release_user',
            'password' => 'secret123',
        ]);

        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    // ─── Logout ──────────────────────────────────────────────

    public function test_user_can_logout(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_logout_with_get_route(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guest_accessing_logout_is_redirected_to_login(): void
    {
        $response = $this->get('/logout');

        $response->assertRedirect('/login');
    }

    // ─── Auth Middleware ─────────────────────────────────────

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_manajemen_aset(): void
    {
        $response = $this->get('/manajemen-aset');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect(route('dashboard'));
    }
}
