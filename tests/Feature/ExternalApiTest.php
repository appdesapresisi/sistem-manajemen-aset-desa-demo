<?php

namespace Tests\Feature;

use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExternalApiTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected JenisInventaris $jenis;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);
        $this->user = User::create([
            'nama'     => 'API User Test',
            'username' => 'api_user_' . uniqid(),
            'role_id'  => $role->id,
            'is_aktif' => 1,
            'password' => bcrypt('password123'),
        ]);

        $this->jenis = JenisInventaris::create([
            'nama'      => 'Inventaris Gedung dan Bangunan ' . uniqid(),
            'deskripsi' => 'Aset Desa berupa bangunan',
            'icon'      => 'home',
            'kib_type'  => 'C',
        ]);
    }

    public function test_external_token_creation(): void
    {
        $response = $this->postJson('/api/external/token', [
            'username' => $this->user->username,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'nama', 'username'],
            ]);
    }

    public function test_external_jenis_inventaris_returns_enriched_data(): void
    {
        Inventaris::create([
            'nama_barang'         => 'Gedung Serbaguna',
            'jenis_inventaris_id' => $this->jenis->id,
            'kondisi'             => 'baik',
            'nilai_perolehan'     => 1000000,
            'created_by'          => $this->user->id,
        ]);

        Inventaris::create([
            'nama_barang'         => 'Kantor Desa',
            'jenis_inventaris_id' => $this->jenis->id,
            'kondisi'             => 'rusak_ringan',
            'nilai_perolehan'     => 2500000,
            'created_by'          => $this->user->id,
        ]);

        Inventaris::create([
            'nama_barang'         => 'Pos Ronda',
            'jenis_inventaris_id' => $this->jenis->id,
            'kondisi'             => 'rusak_berat',
            'nilai_perolehan'     => 500000,
            'created_by'          => $this->user->id,
        ]);

        $token = $this->user->createToken('external-api', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/external/jenis-inventaris');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id'           => $this->jenis->id,
                'nama'         => $this->jenis->nama,
                'jumlah_aset'  => 3,
                'nilai_aset'   => 4000000,
                'nilai_format' => 'Rp 4.000.000',
                'kondisi'      => [
                    'baik'         => 1,
                    'rusak_ringan' => 1,
                    'rusak_berat'  => 1,
                ],
            ]);
    }
}
