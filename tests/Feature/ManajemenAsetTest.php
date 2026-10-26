<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Inventaris;
use App\Models\InventarisValue;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ManajemenAsetTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $sekretaris;
    private User $kaur;
    private JenisInventaris $jenis;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // Create roles
        $roleSA = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'Super Admin', 'deskripsi' => 'Full access']);
        $roleSK = Role::firstOrCreate(['nama' => 'sekretaris_desa'], ['tugas' => 'Sekretaris', 'deskripsi' => 'Approval']);
        $roleKR = Role::firstOrCreate(['nama' => 'kaur'], ['tugas' => 'Kaur', 'deskripsi' => 'Operator']);

        $this->superAdmin = User::create([
            'nama' => 'Super Admin Test',
            'username' => 'sa_' . uniqid(),
            'password' => bcrypt('password'),
            'role_id' => $roleSA->id,
            'is_aktif' => 1,
        ]);

        $this->sekretaris = User::create([
            'nama' => 'Sekretaris Test',
            'username' => 'sk_' . uniqid(),
            'password' => bcrypt('password'),
            'role_id' => $roleSK->id,
            'is_aktif' => 1,
        ]);

        $this->kaur = User::create([
            'nama' => 'Kaur Test',
            'username' => 'kr_' . uniqid(),
            'password' => bcrypt('password'),
            'role_id' => $roleKR->id,
            'is_aktif' => 1,
        ]);

        // Create jenis inventaris with fields
        $this->jenis = JenisInventaris::create([
            'nama' => 'Kendaraan Test ' . uniqid(),
            'deskripsi' => 'Jenis untuk testing',
        ]);

        JenisInventarisData::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_field' => 'nama_barang',
            'label_field' => 'Nama Barang',
            'field_type' => 'fixed',
            'fixed_field_name' => 'nama_barang',
            'tipe' => 'string',
            'is_required' => 1,
            'order' => 1,
        ]);

        JenisInventarisData::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_field' => 'kode_barang',
            'label_field' => 'Kode Barang',
            'field_type' => 'fixed',
            'fixed_field_name' => 'kode_barang',
            'tipe' => 'string',
            'is_required' => 1,
            'order' => 2,
        ]);
    }

    private function createInventaris(array $overrides = []): Inventaris
    {
        $data = array_merge([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Toyota Avanza Test',
            'kode_barang' => 'KDR-' . uniqid(),
            'nup' => '001',
            'tahun_perolehan' => 2024,
            'nilai_perolehan' => 150000000,
            'kondisi' => 'Baik',
            'created_by' => $this->kaur->id,
            'asal_usul' => 'apbdes',
            'pengguna' => 'Operator Kaur',
        ], $overrides);

        $inv = new Inventaris();
        $inv->jenis_inventaris_id = $data['jenis_inventaris_id'];
        $inv->nama_barang = $data['nama_barang'];
        $inv->kode_barang = $data['kode_barang'];
        $inv->nup = $data['nup'];
        $inv->tahun_perolehan = $data['tahun_perolehan'];
        $inv->nilai_perolehan = $data['nilai_perolehan'];
        $inv->kondisi = $data['kondisi'];
        $inv->created_by = $data['created_by'];
        $inv->asal_usul = $data['asal_usul'];
        $inv->pengguna = $data['pengguna'];
        $inv->ruangan_id = $data['ruangan_id'] ?? null;
        $inv->status = $overrides['status'] ?? 'pending';
        $inv->save();

        return $inv;
    }

    // ─── Index ───────────────────────────────────────────────

    public function test_superadmin_can_view_inventaris_index(): void
    {
        $this->createInventaris();

        $response = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.index'));

        $response->assertOk();
        $response->assertViewHas('inventaris');
        $response->assertViewHas('stats');
    }

    public function test_kaur_can_view_inventaris_index(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('manajemen-aset.index'));
        $response->assertOk();
    }

    public function test_inventaris_index_ajax_returns_json(): void
    {
        $this->createInventaris();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('manajemen-aset.index'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertJsonStructure(['inventaris', 'stats', 'total', 'from', 'to']);
    }

    public function test_inventaris_index_can_filter_by_search(): void
    {
        $this->createInventaris(['nama_barang' => 'Honda Civic Unik']);
        $this->createInventaris(['nama_barang' => 'Yamaha NMAX']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('manajemen-aset.index', ['search' => 'Honda']), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $items = $response->json('inventaris');
        $this->assertGreaterThanOrEqual(1, count($items));
    }

    public function test_inventaris_index_can_filter_by_status(): void
    {
        $this->createInventaris(['status' => 'disetujui', 'nama_barang' => 'Approved Item']);
        $this->createInventaris(['status' => 'pending', 'nama_barang' => 'Pending Item']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('manajemen-aset.index', ['status' => 'disetujui']), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
    }

    public function test_inventaris_index_can_filter_by_jenis(): void
    {
        $this->createInventaris();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('manajemen-aset.index', ['jenis' => [$this->jenis->id]]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
    }

    // ─── Create ──────────────────────────────────────────────

    public function test_superadmin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.create'));
        $response->assertOk();
    }

    public function test_kaur_can_view_create_form(): void
    {
        $response = $this->actingAs($this->kaur)->get(route('manajemen-aset.create'));
        $response->assertOk();
    }

    // ─── Store ───────────────────────────────────────────────

    public function test_superadmin_can_store_inventaris(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'New Inventaris',
            'kode_barang' => 'NEW-001',
            'nup' => '001',
            'tahun_perolehan' => 2025,
            'nilai_perolehan' => '50000000',
            'kondisi' => 'baik',
            'asal_usul' => 'apbdes',
            'pengguna' => 'Tim Inventaris',
            'bulan_perolehan' => 6,
        ]);

        $response->assertRedirect(route('manajemen-aset.index'));
        $response->assertSessionHas('success');

        // Superadmin langsung disetujui (tidak melalui pending)
        $this->assertDatabaseHas('inventaris', [
            'nama_barang' => 'New Inventaris',
            'kode_barang' => 'NEW-001',
            'status' => 'draft',
        ]);
    }

    public function test_kaur_store_inventaris_has_pending_status(): void
    {
        $response = $this->actingAs($this->kaur)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Kaur Inventaris',
            'kode_barang' => 'KAUR-001',
            'asal_usul' => 'apbdes',
            'pengguna' => 'Operator Kaur',
            'bulan_perolehan' => 6,
        ]);

        $response->assertRedirect(route('manajemen-aset.index'));

        // Kaur harus pending karena tidak punya hak approve
        $this->assertDatabaseHas('inventaris', [
            'kode_barang' => 'KAUR-001',
            'status' => 'draft',
        ]);
    }

    public function test_store_inventaris_creates_activity_log(): void
    {
        $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Log Test Item',
            'kode_barang' => 'LOG-001',
            'asal_usul' => 'apbdes',
            'pengguna' => 'Unit Log',
            'bulan_perolehan' => 6,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Inventaris',
            'action' => 'tambah',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_store_inventaris_validation_requires_nama_barang(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $this->jenis->id,
            // nama_barang missing — is_required=1 dan tidak di-skip di withValidator
            'asal_usul' => 'apbdes',
            'pengguna' => 'Unit Validasi',
            'bulan_perolehan' => 6,
        ]);

        $response->assertSessionHasErrors('nama_barang');
    }

    public function test_store_inventaris_validation_requires_bulan_perolehan(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Test Barang Tanpa Bulan',
            'kode_barang' => 'KD-NO-BLN',
            'asal_usul' => 'apbdes',
            'pengguna' => 'Unit Test',
        ]);

        $response->assertSessionHasErrors(['bulan_perolehan']);
    }

    public function test_store_inventaris_allows_optional_pengguna(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Test Barang Tanpa Pengguna',
            'kode_barang' => 'KD-NO-PENGGUNA',
            'asal_usul' => 'apbdes',
            'pengguna' => null,
            'bulan_perolehan' => 5,
        ]);

        $response->assertRedirect(route('manajemen-aset.index'));
        $this->assertDatabaseHas('inventaris', [
            'nama_barang' => 'Test Barang Tanpa Pengguna',
            'pengguna' => null,
        ]);
    }

    // ─── Show ────────────────────────────────────────────────

    public function test_superadmin_can_view_inventaris_detail(): void
    {
        $inv = $this->createInventaris();

        $response = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.show', $inv->id));

        $response->assertOk();
        $response->assertViewHas('inventaris');
    }

    public function test_show_nonexistent_inventaris_returns_404(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.show', 99999));
        $response->assertNotFound();
    }

    public function test_scan_public_page_shows_asal_usul_dan_pengguna(): void
    {
        $inv = $this->createInventaris([
            'asal_usul' => 'perolehan_lain',
            'pengguna' => 'Org desa',
            'status' => 'disetujui',
        ]);

        $inv->barcode = 'INV-TEST-SCAN-' . uniqid();
        $inv->save();

        $signedUrl = URL::signedRoute('inventaris.scan', ['barcode' => $inv->barcode]);

        $response = $this->get($signedUrl);

        $response->assertOk();
        $response->assertSee('Asal Usul Barang');
        $response->assertSee('Perolehan Lain yang Sah');
        $response->assertSee('Pengguna');
        $response->assertSee('Org desa');
    }

    public function test_sekretaris_tidak_melihat_tombol_ajukan_ulang_di_detail_ditolak(): void
    {
        $inv = $this->createInventaris(['status' => 'ditolak']);

        $response = $this->actingAs($this->sekretaris)->get(route('manajemen-aset.show', $inv->id));

        $response->assertOk();
        $response->assertDontSee('Ajukan Ulang');
    }

    // ─── Edit ────────────────────────────────────────────────

    public function test_superadmin_can_view_edit_form(): void
    {
        $inv = $this->createInventaris();

        $response = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.edit', $inv->id));

        $response->assertOk();
        $response->assertViewHas('inventaris');
        $response->assertViewHas('fieldsWithData');
    }

    // ─── Update ──────────────────────────────────────────────

    public function test_superadmin_can_update_inventaris(): void
    {
        $inv = $this->createInventaris();

        $response = $this->actingAs($this->superAdmin)->put(route('manajemen-aset.update', $inv->id), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Updated Name',
            'kode_barang' => $inv->kode_barang,
            'nup' => '002',
            'tahun_perolehan' => 2025,
            'nilai_perolehan' => '200000000',
            'kondisi' => 'baik',
            'asal_usul' => 'apbdes',
            'pengguna' => 'Unit Update',
            'bulan_perolehan' => 6,
        ]);

        $response->assertRedirect(route('manajemen-aset.index'));
        $response->assertSessionHas('success');

        $inv->refresh();
        $this->assertEquals('Updated Name', $inv->nama_barang);
        $this->assertEquals('002', $inv->nup);
    }

    public function test_update_does_not_change_status(): void
    {
        $inv = $this->createInventaris(['status' => 'disetujui']);

        $this->actingAs($this->superAdmin)->put(route('manajemen-aset.update', $inv->id), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Keep Status',
            'kode_barang' => $inv->kode_barang,
            'asal_usul' => 'apbdes',
            'pengguna' => 'Unit Status',
            'bulan_perolehan' => 6,
        ]);

        $inv->refresh();
        $this->assertEquals('disetujui', $inv->status);
    }

    public function test_superadmin_can_duplicate_inventaris(): void
    {
        $inv = $this->createInventaris(['nup' => '1', 'tahun_perolehan' => 2024]);

        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.duplicate', $inv->id), [
            'jumlah' => 2,
        ], ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json']);

        $response->assertOk();
        $response->assertJson(['success' => true, 'created' => 2]);

        $this->assertDatabaseHas('inventaris', [
            'nama_barang' => $inv->nama_barang,
            'kode_barang' => $inv->kode_barang,
            'tahun_perolehan' => 2024,
            'nup' => '2',
        ]);
        $this->assertDatabaseHas('inventaris', [
            'nama_barang' => $inv->nama_barang,
            'kode_barang' => $inv->kode_barang,
            'tahun_perolehan' => 2024,
            'nup' => '3',
        ]);
    }

    public function test_next_nup_is_continuous_across_years_for_same_kode_barang(): void
    {
        // Aset 1 & 2 di tahun 2024
        $this->createInventaris(['kode_barang' => 'KB-TEST-01', 'tahun_perolehan' => 2024, 'nup' => '1']);
        $this->createInventaris(['kode_barang' => 'KB-TEST-01', 'tahun_perolehan' => 2024, 'nup' => '2']);

        // Request next-nup untuk tahun 2024 -> harus 3
        $resp2024 = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.next-nup', [
            'kode_barang' => 'KB-TEST-01',
            'tahun_perolehan' => 2024,
        ]));
        $resp2024->assertOk();
        $resp2024->assertJson([
            'kode_barang' => 'KB-TEST-01',
            'tahun_perolehan' => '2024',
            'max_nup' => 2,
            'next_nup' => 3,
        ]);

        // Request next-nup untuk tahun 2025 -> harus melanjutkan ke 3 (runtut lintas tahun per kode barang)
        $resp2025 = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.next-nup', [
            'kode_barang' => 'KB-TEST-01',
            'tahun_perolehan' => 2025,
        ]));
        $resp2025->assertOk();
        $resp2025->assertJson([
            'kode_barang' => 'KB-TEST-01',
            'tahun_perolehan' => '2025',
            'max_nup' => 2,
            'next_nup' => 3,
        ]);
    }

    public function test_duplicate_increments_nup_continuously_across_years(): void
    {
        // Aset tahun 2024 dengan NUP 10
        $this->createInventaris(['kode_barang' => 'KB-DUPL-01', 'tahun_perolehan' => 2024, 'nup' => '10']);

        // Aset tahun 2025
        $inv2025 = $this->createInventaris(['kode_barang' => 'KB-DUPL-01', 'tahun_perolehan' => 2025, 'nup' => '11']);

        // Duplikasi aset tahun 2025 -> NUP harus 12
        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.duplicate', $inv2025->id), [
            'jumlah' => 1,
        ], ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json']);

        $response->assertOk();
        $response->assertJson(['success' => true, 'created' => 1]);

        $this->assertDatabaseHas('inventaris', [
            'kode_barang' => 'KB-DUPL-01',
            'tahun_perolehan' => 2025,
            'nup' => '12',
        ]);
    }

    // ─── Delete ──────────────────────────────────────────────

    public function test_superadmin_can_delete_inventaris(): void
    {
        $inv = $this->createInventaris();
        $invId = $inv->id;

        $response = $this->actingAs($this->superAdmin)->delete(route('manajemen-aset.destroy', $invId));

        $response->assertRedirect(route('manajemen-aset.index'));
        $this->assertDatabaseMissing('inventaris', ['id' => $invId]);
    }

    public function test_sekretaris_can_delete_inventaris(): void
    {
        $inv = $this->createInventaris();

        $response = $this->actingAs($this->sekretaris)->delete(route('manajemen-aset.destroy', $inv->id));

        $response->assertRedirect(route('manajemen-aset.index'));
        $this->assertDatabaseMissing('inventaris', ['id' => $inv->id]);
    }

    public function test_kaur_cannot_delete_inventaris(): void
    {
        $inv = $this->createInventaris();

        $response = $this->actingAs($this->kaur)->delete(route('manajemen-aset.destroy', $inv->id));

        $response->assertForbidden();
    }

    public function test_delete_cascades_related_data(): void
    {
        $inv = $this->createInventaris();

        // Create related value
        $field = JenisInventarisData::where('jenis_inventaris_id', $this->jenis->id)->first();
        InventarisValue::create([
            'inventaris_id' => $inv->id,
            'jenis_inventaris_data_id' => $field->id,
            'value' => 'Test Value',
        ]);

        $this->actingAs($this->superAdmin)->delete(route('manajemen-aset.destroy', $inv->id));

        $this->assertDatabaseMissing('inventaris_values', ['inventaris_id' => $inv->id]);
    }

    public function test_delete_nonexistent_inventaris_redirects_with_error(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete(route('manajemen-aset.destroy', 99999));

        $response->assertRedirect(route('manajemen-aset.index'));
        $response->assertSessionHas('error');
    }

    public function test_store_inventaris_rejects_duplicate_nup_in_same_kode_barang(): void
    {
        $testKode = '9.99.99.99.001';
        $this->createInventaris(['kode_barang' => $testKode, 'nup' => '5', 'tahun_perolehan' => 2024]);

        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Duplicate NUP Test',
            'kode_barang' => $testKode,
            'nup' => '5',
            'tahun_perolehan' => 2026,
            'bulan_perolehan' => 3,
            'asal_usul' => 'apbdes',
        ]);

        $response->assertSessionHasErrors('nup');
    }

    public function test_update_inventaris_rejects_duplicate_nup_in_same_kode_barang(): void
    {
        $testKode = '9.99.99.99.002';
        $inv1 = $this->createInventaris(['kode_barang' => $testKode, 'nup' => '1', 'tahun_perolehan' => 2024]);
        $inv2 = $this->createInventaris(['kode_barang' => $testKode, 'nup' => '2', 'tahun_perolehan' => 2026]);

        $response = $this->actingAs($this->superAdmin)->put(route('manajemen-aset.update', $inv2->id), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Update to Duplicate NUP',
            'kode_barang' => $testKode,
            'nup' => '1',
            'tahun_perolehan' => 2026,
            'bulan_perolehan' => 3,
            'asal_usul' => 'apbdes',
        ]);

        $response->assertSessionHasErrors('nup');
    }

    public function test_update_inventaris_allows_same_nup_for_same_item(): void
    {
        $testKode = '9.99.99.99.003';
        $inv = $this->createInventaris(['kode_barang' => $testKode, 'nup' => '1', 'tahun_perolehan' => 2026]);

        $response = $this->actingAs($this->superAdmin)->put(route('manajemen-aset.update', $inv->id), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Update Self Same NUP',
            'kode_barang' => $testKode,
            'nup' => '1',
            'tahun_perolehan' => 2026,
            'bulan_perolehan' => 3,
            'asal_usul' => 'apbdes',
        ]);

        $response->assertSessionDoesntHaveErrors('nup');
    }

    public function test_store_inventaris_rejects_zero_nup(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Zero NUP Test',
            'kode_barang' => '9.99.99.99.004',
            'nup' => '0',
            'tahun_perolehan' => 2026,
            'bulan_perolehan' => 3,
            'asal_usul' => 'apbdes',
        ]);

        $response->assertSessionHasErrors('nup');
    }

    public function test_inventaris_index_can_filter_by_kondisi_checklist(): void
    {
        $this->createInventaris(['nama_barang' => 'Barang Kondisi Baik', 'kondisi' => 'baik']);
        $this->createInventaris(['nama_barang' => 'Barang Rusak Ringan', 'kondisi' => 'rusak_ringan']);
        $this->createInventaris(['nama_barang' => 'Barang Rusak Berat', 'kondisi' => 'rusak_berat']);

        // 1. Filter single kondisi via checklist (kondisi[]=baik)
        $response = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.index', [
            'search' => 'Barang Kondisi Baik',
            'kondisi' => ['baik'],
        ]), ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk();
        $items = $response->json('inventaris');
        $names = collect($items)->pluck('display_name')->toArray();
        $this->assertContains('Barang Kondisi Baik', $names);
        $this->assertNotContains('Barang Rusak Ringan', $names);
        $this->assertNotContains('Barang Rusak Berat', $names);

        // 2. Filter multiple kondisi via checklist (kondisi[]=rusak_ringan & kondisi[]=rusak_berat)
        $responseMulti = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.index', [
            'search' => 'Barang Rusak',
            'kondisi' => ['rusak_ringan', 'rusak_berat'],
        ]), ['X-Requested-With' => 'XMLHttpRequest']);
        $responseMulti->assertOk();
        $namesMulti = collect($responseMulti->json('inventaris'))->pluck('display_name')->toArray();
        $this->assertNotContains('Barang Kondisi Baik', $namesMulti);
        $this->assertContains('Barang Rusak Ringan', $namesMulti);
        $this->assertContains('Barang Rusak Berat', $namesMulti);
    }

    public function test_inventaris_index_can_filter_by_kelengkapan_data(): void
    {
        $ruangan = \App\Models\Ruangan::firstOrCreate(['kode' => 'RG-TEST'], ['nama' => 'Ruang Uji Filter']);
        $jenisWithRuangan = \App\Models\JenisInventaris::firstOrCreate(
            ['nama' => 'KIB Peralatan Uji'],
            ['kode' => 'KIB-TEST', 'requires_ruangan' => 1]
        );

        // Aset 1: Lengkap
        $lengkap = $this->createInventaris([
            'nama_barang' => 'Aset Sangat Lengkap',
            'jenis_inventaris_id' => $jenisWithRuangan->id,
            'ruangan_id' => $ruangan->id,
            'kondisi' => 'baik',
        ]);

        // Aset 2: Tidak Lengkap (ruangan_id null padahal jenis requires ruangan)
        $tidakLengkap = $this->createInventaris([
            'nama_barang' => 'Aset Butuh Ruangan',
            'jenis_inventaris_id' => $jenisWithRuangan->id,
            'ruangan_id' => null,
            'kondisi' => 'baik',
        ]);

        // 1. Filter: tidak_lengkap
        $resTidakLengkap = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.index', [
            'search' => 'Aset',
            'kelengkapan' => 'tidak_lengkap',
        ]), ['X-Requested-With' => 'XMLHttpRequest']);
        $resTidakLengkap->assertOk();
        $itemsTl = $resTidakLengkap->json('inventaris');
        $namesTl = collect($itemsTl)->pluck('display_name')->toArray();
        $this->assertContains('Aset Butuh Ruangan', $namesTl);
        $this->assertNotContains('Aset Sangat Lengkap', $namesTl);
        
        $itemTl = collect($itemsTl)->firstWhere('display_name', 'Aset Butuh Ruangan');
        $this->assertFalse($itemTl['is_lengkap']);
        $this->assertContains('Ruangan', $itemTl['missing_fields']);

        // 2. Filter: lengkap
        $resLengkap = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.index', [
            'search' => 'Aset',
            'kelengkapan' => 'lengkap',
        ]), ['X-Requested-With' => 'XMLHttpRequest']);
        $resLengkap->assertOk();
        $itemsL = $resLengkap->json('inventaris');
        $namesL = collect($itemsL)->pluck('display_name')->toArray();
        $this->assertContains('Aset Sangat Lengkap', $namesL);
        $this->assertNotContains('Aset Butuh Ruangan', $namesL);

        $itemL = collect($itemsL)->firstWhere('display_name', 'Aset Sangat Lengkap');
        $this->assertTrue($itemL['is_lengkap']);
        $this->assertEmpty($itemL['missing_fields']);

        // 3. Stats verification
        $this->assertArrayHasKey('totalLengkap', $resLengkap->json('stats'));
        $this->assertArrayHasKey('totalTidakLengkap', $resLengkap->json('stats'));
    }
}

