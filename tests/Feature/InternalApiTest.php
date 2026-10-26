<?php

namespace Tests\Feature;

use App\Models\Bidang;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\Pejabat;
use App\Models\Role;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InternalApiTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $role = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);
        $this->user = User::create([
            'nama'     => 'Internal API Tester',
            'username' => 'internal_api_' . uniqid(),
            'role_id'  => $role->id,
            'is_aktif' => 1,
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_api_user_endpoint()
    {
        $response = $this->actingAs($this->user)->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonPath('id', $this->user->id)
                 ->assertJsonPath('username', $this->user->username);
    }

    public function test_api_ruangan_endpoint()
    {
        $ruangan = Ruangan::create(['nama' => 'Ruang Rapat ' . uniqid(), 'kode' => 'RR-' . rand(10,99)]);

        $response = $this->actingAs($this->user)->getJson('/api/ruangan');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'nama', 'kode']
                 ]);
    }

    public function test_api_jenis_inventaris_endpoints()
    {
        $jenis = JenisInventaris::create([
            'nama' => 'Inventaris Uji ' . uniqid(),
            'deskripsi' => 'Testing description',
            'icon' => 'box',
            'kib_type' => 'B',
        ]);

        // Index
        $response = $this->actingAs($this->user)->getJson('/api/jenis-inventaris');
        $response->assertStatus(200);

        // Show
        $response = $this->actingAs($this->user)->getJson("/api/jenis-inventaris/{$jenis->id}");
        $response->assertStatus(200)
                 ->assertJsonPath('id', $jenis->id);

        // Fields
        $response = $this->actingAs($this->user)->getJson("/api/jenis-inventaris/{$jenis->id}/fields");
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id', 'nama', 'fields'
                 ]);
    }

    public function test_api_pejabats_endpoints()
    {
        $pejabat = Pejabat::create([
            'nama' => 'Pejabat Test ' . uniqid(),
            'jabatan' => 'Kaur Keuangan',
            'is_aktif' => 1,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/pejabats');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);

        $response = $this->actingAs($this->user)->getJson("/api/pejabats/{$pejabat->id}");
        $response->assertStatus(200)
                 ->assertJsonPath('id', $pejabat->id);
    }

    public function test_api_bidangs_endpoints()
    {
        $bidang = Bidang::create([
            'nama' => 'Bidang Test ' . uniqid(),
            'deskripsi' => 'Deskripsi bidang',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/bidangs');
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);

        $response = $this->actingAs($this->user)->getJson("/api/bidangs/{$bidang->id}");
        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }

    public function test_api_users_endpoints()
    {
        $response = $this->actingAs($this->user)->getJson('/api/users');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);

        $response = $this->actingAs($this->user)->getJson("/api/users/{$this->user->id}");
        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $this->user->id);
    }

    public function test_api_dashboard_endpoints()
    {
        $response = $this->actingAs($this->user)->getJson('/api/dashboard/kpi');
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/charts');
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/activities');
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/exports');
        $response->assertStatus(200);
    }

    public function test_api_inventaris_endpoints()
    {
        $jenis = JenisInventaris::create([
            'nama' => 'Peralatan dan Mesin ' . uniqid(),
            'deskripsi' => 'Peralatan mesin',
            'icon' => 'tool',
            'kib_type' => 'B',
        ]);

        $field = JenisInventarisData::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_field' => 'merk',
            'label_field' => 'Merk / Tipe',
            'tipe' => 'text',
            'order' => 1,
        ]);

        // 1. Create via API
        $postData = [
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang' => 'Laptop Lenovo ThinkPad',
            'kode_barang' => '02.01.01.01.' . rand(100, 999),
            'nup' => '1',
            'tahun_perolehan' => 2024,
            'bulan_perolehan' => 1,
            'asal_usul' => 'apbdes',
            'nilai_perolehan' => 15000000,
            'kondisi' => 'baik',
            'keterangan' => 'Untuk operasional kantor',
            'dynamic' => [
                $field->id => 'ThinkPad T14',
            ],
        ];

        $createResponse = $this->actingAs($this->user)->postJson('/api/inventaris', $postData);
        $createResponse->assertStatus(201)
                       ->assertJsonPath('success', true)
                       ->assertJsonPath('data.nama_barang', 'Laptop Lenovo ThinkPad');

        $inventarisId = $createResponse->json('data.id');

        // 2. Index via API
        $indexResponse = $this->actingAs($this->user)->getJson('/api/inventaris');
        $indexResponse->assertStatus(200)
                      ->assertJsonPath('success', true)
                      ->assertJsonStructure(['data', 'meta']);

        // 3. Show via API
        $showResponse = $this->actingAs($this->user)->getJson("/api/inventaris/{$inventarisId}");
        $showResponse->assertStatus(200)
                     ->assertJsonPath('success', true)
                     ->assertJsonPath('data.id', $inventarisId);

        // 4. Update via API
        $updateData = [
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang' => 'Laptop Lenovo ThinkPad Gen 2',
            'kode_barang' => '02.01.01.01.888',
            'nup' => '1',
            'tahun_perolehan' => 2024,
            'bulan_perolehan' => 2,
            'asal_usul' => 'apbdes',
            'nilai_perolehan' => 16000000,
            'kondisi' => 'baik',
            'keterangan' => 'Updated via API',
            'dynamic' => [
                $field->id => 'ThinkPad T14 Gen 2',
            ],
        ];

        $updateResponse = $this->actingAs($this->user)->putJson("/api/inventaris/{$inventarisId}", $updateData);
        $updateResponse->assertStatus(200)
                       ->assertJsonPath('success', true)
                       ->assertJsonPath('data.nama_barang', 'Laptop Lenovo ThinkPad Gen 2');

        // 5. Delete via API
        $deleteResponse = $this->actingAs($this->user)->deleteJson("/api/inventaris/{$inventarisId}");
        $deleteResponse->assertStatus(200)
                       ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('inventaris', ['id' => $inventarisId]);
    }

    public function test_internal_api_can_be_accessed_via_bearer_token()
    {
        $token = $this->user->createToken('test-token', ['read'])->plainTextToken;

        // 1. /api/user
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonPath('id', $this->user->id)
                 ->assertJsonPath('username', $this->user->username);

        // 6. /api/kb/search
        $kbResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                           ->getJson('/api/kb/search?q=meja');
        $kbResponse->assertStatus(200);

        // 7. /api/jenis-inventaris/{id}/fields
        $jenis = JenisInventaris::first() ?? JenisInventaris::create(['nama' => 'Peralatan Test', 'kib_type' => 'B']);
        $fieldsResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                               ->getJson("/api/jenis-inventaris/{$jenis->id}/fields");
        $fieldsResponse->assertStatus(200)
                       ->assertJsonStructure(['id', 'nama', 'fields']);

        // 8. /api/ruangan
        $ruanganResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                                ->getJson('/api/ruangan');
        $ruanganResponse->assertStatus(200);
    }

    public function test_session_authenticated_user_can_search_kode_barang()
    {
        $response = $this->actingAs($this->user)->getJson('/api/kb/search?level=5&q=meja');
        $response->assertStatus(200);
    }

    public function test_external_token_can_access_internal_kb_search()
    {
        // 1. Dapatkan token dari endpoint external token
        $tokenResponse = $this->postJson('/api/external/token', [
            'username' => $this->user->username,
            'password' => 'password123',
        ]);
        $tokenResponse->assertStatus(200)->assertJsonPath('success', true);
        $token = $tokenResponse->json('token');

        // 2. Gunakan token tersebut di /api/kb/search
        $kbResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                           ->getJson('/api/kb/search?level=5&q=laptop');
        $kbResponse->assertStatus(200);
    }


    public function test_unauthenticated_user_cannot_access_internal_api()
    {
        $response = $this->getJson('/api/inventaris');
        $response->assertStatus(401);

        $response = $this->getJson('/api/user');
        $response->assertStatus(401);

        $response = $this->getJson('/api/ruangan');
        $response->assertStatus(401);
    }

    public function test_api_cannot_delete_system_jenis_inventaris_tidak_ditemukan_or_virtual(): void
    {
        $jenisLhi = JenisInventaris::firstOrCreate(
            ['nama' => JenisInventaris::NAMA_TIDAK_DITEMUKAN],
            ['deskripsi' => 'LHI', 'kib_type' => 'E']
        );

        $response = $this->actingAs($this->user)->deleteJson("/api/jenis-inventaris/{$jenisLhi->id}");
        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Jenis inventaris sistem ini dilindungi dan tidak dapat dihapus.');

        $this->assertDatabaseHas('jenis_inventaris', ['id' => $jenisLhi->id]);
    }

    public function test_api_pejabat_destroy_returns_422_when_used_as_default_signer(): void
    {
        $pejabat = Pejabat::create([
            'nama'     => 'Pejabat Signer ' . uniqid(),
            'jabatan'  => 'Kaur Tata Usaha',
            'is_aktif' => 1,
        ]);

        $jenis = JenisInventaris::create([
            'nama'     => 'Jenis With Signer ' . uniqid(),
            'kib_type' => 'B',
        ]);

        \App\Models\JenisInventarisPejabat::create([
            'jenis_inventaris_id' => $jenis->id,
            'pejabat_id'          => $pejabat->id,
            'order'               => 1,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/pejabats/{$pejabat->id}");
        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('pejabats', ['id' => $pejabat->id]);
    }

    public function test_api_users_store_and_update_preserves_bidang_and_dinas_and_rejects_superdev(): void
    {
        $bidang = Bidang::firstOrCreate(['nama' => 'Bidang API ' . uniqid()]);

        // 1. Coba buat username developer -> tolak
        $rejectRes = $this->actingAs($this->user)->postJson('/api/users', [
            'nama'                  => 'Superdev Attacker',
            'username'              => 'developer',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'kaur',
            'bidang_id'             => $bidang->id,
            'is_aktif'              => 1,
            'alamat'                => 'Jl. Testing',
        ]);
        $rejectRes->assertStatus(422);

        // 2. Buat user kaur valid via API
        $createRes = $this->actingAs($this->user)->postJson('/api/users', [
            'nama'                  => 'Kaur User API',
            'username'              => 'kaur_api_' . uniqid(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'kaur',
            'bidang_id'             => $bidang->id,
            'is_aktif'              => 1,
            'alamat'                => 'Jl. Testing API',
        ]);
        $createRes->assertStatus(201)
            ->assertJsonPath('success', true);
        $userId = $createRes->json('data.user.id');

        $this->assertDatabaseHas('users', [
            'id'        => $userId,
            'bidang_id' => $bidang->id,
        ]);

        // 3. Update via API
        $updateRes = $this->actingAs($this->user)->putJson("/api/users/{$userId}", [
            'nama'      => 'Kaur User API Updated',
            'username'  => $createRes->json('data.user.username'),
            'role'      => 'kaur',
            'bidang_id' => $bidang->id,
            'is_aktif'  => 1,
            'alamat'    => 'Jl. Testing API Updated',
        ]);
        $updateRes->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'id'     => $userId,
            'alamat' => 'Jl. Testing API Updated',
        ]);
    }

    public function test_notification_destroy_returns_json_when_requested(): void
    {
        $notif = \App\Models\Notification::send($this->user->id, 'info', 'Test Notif', 'Test Body');

        $response = $this->actingAs($this->user)->deleteJson(route('notifications.destroy', $notif->id));
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Notifikasi dihapus.');

        $this->assertDatabaseMissing('notifications', ['id' => $notif->id]);
    }

    public function test_api_bidang_crud_logs_activity(): void
    {
        // 1. Create via API
        $namaBidang = 'Bidang Log Uji ' . uniqid();
        $createRes = $this->actingAs($this->user)->postJson('/api/bidangs', [
            'nama' => $namaBidang,
            'deskripsi' => 'Deskripsi testing API log',
        ]);
        $createRes->assertStatus(201)
            ->assertJsonPath('success', true);
        $bidangId = $createRes->json('data.id');

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Bidang',
            'action' => 'tambah',
            'subject_id' => $bidangId,
        ]);

        // 2. Update via API
        $updateRes = $this->actingAs($this->user)->putJson("/api/bidangs/{$bidangId}", [
            'nama' => $namaBidang . ' Updated',
        ]);
        $updateRes->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Bidang',
            'action' => 'ubah',
            'subject_id' => $bidangId,
        ]);

        // 3. Delete via API
        $deleteRes = $this->actingAs($this->user)->deleteJson("/api/bidangs/{$bidangId}");
        $deleteRes->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Bidang',
            'action' => 'hapus',
            'subject_id' => $bidangId,
        ]);
    }

    public function test_pejabat_destroy_returns_informative_error_when_default_signer(): void
    {
        $pejabat = Pejabat::create([
            'nama' => 'Pejabat Default ' . uniqid(),
            'jabatan' => 'Kaur Keuangan',
            'is_aktif' => 1,
        ]);

        $jenis = JenisInventaris::create([
            'nama' => 'Jenis Signer ' . uniqid(),
            'kib_type' => 'B',
        ]);

        \App\Models\JenisInventarisPejabat::create([
            'jenis_inventaris_id' => $jenis->id,
            'pejabat_id' => $pejabat->id,
            'role_context' => 'mengetahui',
        ]);

        // Attempt delete via Web
        $response = $this->actingAs($this->user)->delete(route('pejabat.destroy', $pejabat->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMsg = session('error');
        $this->assertStringContainsString('masih digunakan sebagai pejabat penandatangan default', $errorMsg);

        // Pejabat should still exist
        $this->assertDatabaseHas('pejabats', ['id' => $pejabat->id]);
    }
}
