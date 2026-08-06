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

    // ─── Delete ──────────────────────────────────────────────

    public function test_superadmin_can_delete_inventaris(): void
    {
        $inv = $this->createInventaris();
        $invId = $inv->id;

        $response = $this->actingAs($this->superAdmin)->delete(route('manajemen-aset.destroy', $invId));

        $response->assertRedirect(route('manajemen-aset.index'));
        $this->assertDatabaseMissing('inventaris', ['id' => $invId]);
    }

    public function test_sekretaris_cannot_delete_inventaris(): void
    {
        $inv = $this->createInventaris();

        $response = $this->actingAs($this->sekretaris)->delete(route('manajemen-aset.destroy', $inv->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('inventaris', ['id' => $inv->id]);
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
}
