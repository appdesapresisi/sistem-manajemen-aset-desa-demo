<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\ExportLog;
use App\Models\Inventaris;
use App\Models\InventarisKibValue;
use App\Models\JenisInventaris;
use App\Models\Pejabat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KibExportTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected JenisInventaris $jenisKibB;
    protected JenisInventaris $jenisKibA;
    protected Pejabat $pejabatSekretaris;
    protected Pejabat $pejabatPetugas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $role = Role::firstOrCreate(
            ['nama' => 'super_admin'],
            ['tugas' => 'Akses penuh', 'deskripsi' => 'Role untuk pengujian']
        );

        $this->user = User::create([
            'nama' => 'Superadmin KIB QA',
            'email' => 'kib-qa-' . uniqid() . '@example.test',
            'password' => 'secret123',
            'role_id' => $role->id,
            'is_aktif' => 1,
        ]);

        $this->jenisKibB = JenisInventaris::create([
            'nama' => 'Peralatan dan Mesin ' . uniqid(),
            'deskripsi' => 'Uji KIB B',
            'kib_type' => 'B',
        ]);

        $this->jenisKibA = JenisInventaris::create([
            'nama' => 'Tanah ' . uniqid(),
            'deskripsi' => 'Uji KIB A',
            'kib_type' => 'A',
        ]);

        $this->pejabatSekretaris = Pejabat::create([
            'nama' => 'I Ketut Sekretaris',
            'nip' => '198001012010011001',
            'jabatan' => 'Sekretaris Desa Demo',
        ]);

        $this->pejabatPetugas = Pejabat::create([
            'nama' => 'I Made Pengurus',
            'nip' => '198501012010011002',
            'jabatan' => 'Pengurus Barang Desa Demo',
            'is_petugas_barang' => 1,
        ]);
    }

    public function test_get_kib_pejabat_info_endpoint(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('export-data.kib-pejabat-info'));
        $response->assertOk();
        $response->assertJsonStructure(['sekretaris', 'petugas']);
    }

    public function test_get_kib_count_endpoint(): void
    {
        $initialCountB = Inventaris::whereHas('jenis', fn($q) => $q->where('kib_type', 'B'))->count();

        // Buat 2 aset di KIB B
        $inv1 = Inventaris::create([
            'jenis_inventaris_id' => $this->jenisKibB->id,
            'nama_barang' => 'Laptop Asus Rog',
            'kode_barang' => '3.05.02.04.004',
            'nup' => '1',
            'tahun_perolehan' => 2025,
            'nilai_perolehan' => 25000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $inv2 = Inventaris::create([
            'jenis_inventaris_id' => $this->jenisKibB->id,
            'nama_barang' => 'Proyektor Epson',
            'kode_barang' => '3.05.02.04.005',
            'nup' => '1',
            'tahun_perolehan' => 2024,
            'nilai_perolehan' => 8000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        // Count KIB B semua tahun
        $respB = $this->actingAs($this->user)->getJson(route('export-data.kib-count', ['kib_type' => 'B', 'tahun' => 'all']));
        $respB->assertOk();
        $this->assertEquals($initialCountB + 2, $respB->json('count'));

        // Count KIB B tahun 2025
        $initialCountB2025 = Inventaris::whereHas('jenis', fn($q) => $q->where('kib_type', 'B'))->where('tahun_perolehan', 2025)->count();
        $respB2025 = $this->actingAs($this->user)->getJson(route('export-data.kib-count', ['kib_type' => 'B', 'tahun' => 2025]));
        $respB2025->assertOk();
        $this->assertEquals($initialCountB2025, $respB2025->json('count'));
    }

    public function test_export_kib_single_pdf(): void
    {
        $inv = Inventaris::create([
            'jenis_inventaris_id' => $this->jenisKibB->id,
            'nama_barang' => 'Sepeda Motor Honda Vario',
            'kode_barang' => '3.05.01.04.001',
            'nup' => '1',
            'tahun_perolehan' => 2025,
            'nilai_perolehan' => 22000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        InventarisKibValue::create([
            'inventaris_id' => $inv->id,
            'kib_field_key' => 'merk_type',
            'value' => 'Honda Vario 160',
        ]);

        InventarisKibValue::create([
            'inventaris_id' => $inv->id,
            'kib_field_key' => 'no_polisi',
            'value' => 'DK 1234 ABC',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('export-data.store-kib'), [
            'kib_type' => 'B',
            'format' => 'pdf',
            'tahun' => 2025,
            'signature_option' => 'with',
            'paper_size' => 'legal',
            'pejabat_kiri' => $this->pejabatSekretaris->id,
            'pejabat_kanan' => $this->pejabatPetugas->id,
            'tanggal_dokumen' => '2026-08-27',
            'keterangan_dokumen' => 'Demo',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $filename = $response->json('filename');
        $this->assertStringEndsWith('.pdf', $filename);

        $this->assertDatabaseHas('export_logs', [
            'user_id' => $this->user->id,
            'tipe_export' => 'pdf',
            'nama_file' => $filename,
        ]);
    }

    public function test_export_kib_single_excel(): void
    {
        $inv = Inventaris::create([
            'jenis_inventaris_id' => $this->jenisKibA->id,
            'nama_barang' => 'Tanah Kantor Desa',
            'kode_barang' => '3.01.01.01.001',
            'nup' => '1',
            'tahun_perolehan' => 2020,
            'nilai_perolehan' => 500000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        InventarisKibValue::create([
            'inventaris_id' => $inv->id,
            'kib_field_key' => 'luas_m2',
            'value' => '1200',
        ]);

        InventarisKibValue::create([
            'inventaris_id' => $inv->id,
            'kib_field_key' => 'hak',
            'value' => 'Hak Pakai',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('export-data.store-kib'), [
            'kib_type' => 'A',
            'format' => 'excel',
            'tahun' => 'all',
            'signature_option' => 'with',
            'pejabat_kiri' => $this->pejabatSekretaris->id,
            'pejabat_kanan' => $this->pejabatPetugas->id,
            'tanggal_dokumen' => '2026-08-27',
            'keterangan_dokumen' => 'Demo',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $filename = $response->json('filename');
        $this->assertStringEndsWith('.xlsx', $filename);

        $this->assertDatabaseHas('export_logs', [
            'user_id' => $this->user->id,
            'tipe_export' => 'excel',
            'nama_file' => $filename,
        ]);
    }

    public function test_export_kib_all_types_pdf(): void
    {
        // Data di KIB B
        Inventaris::create([
            'jenis_inventaris_id' => $this->jenisKibB->id,
            'nama_barang' => 'Komputer Server',
            'kode_barang' => '3.05.02.04.001',
            'nup' => '1',
            'tahun_perolehan' => 2025,
            'nilai_perolehan' => 35000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('export-data.store-kib'), [
            'kib_type' => 'all',
            'format' => 'pdf',
            'tahun' => 2025,
            'signature_option' => 'without',
            'paper_size' => 'legal',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $filename = $response->json('filename');
        $this->assertStringContainsString('kib_semua_', $filename);
        $this->assertStringEndsWith('.pdf', $filename);
    }

    public function test_export_kib_all_types_excel(): void
    {
        // Data di KIB B
        Inventaris::create([
            'jenis_inventaris_id' => $this->jenisKibB->id,
            'nama_barang' => 'Genset Desa',
            'kode_barang' => '3.05.02.01.001',
            'nup' => '1',
            'tahun_perolehan' => 2025,
            'nilai_perolehan' => 15000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('export-data.store-kib'), [
            'kib_type' => 'all',
            'format' => 'excel',
            'tahun' => 'all',
            'signature_option' => 'with',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $filename = $response->json('filename');
        $this->assertStringContainsString('kib_semua_', $filename);
        $this->assertStringEndsWith('.xlsx', $filename);
    }

    public function test_export_kib_e_pdf(): void
    {
        $jenisKibE = JenisInventaris::create([
            'nama' => 'Aset Tetap Lainnya ' . uniqid(),
            'deskripsi' => 'Uji KIB E',
            'kib_type' => 'E',
        ]);

        $inv = Inventaris::create([
            'jenis_inventaris_id' => $jenisKibE->id,
            'nama_barang' => 'Buku Ensiklopedia',
            'kode_barang' => '6.01.01.01.001',
            'nup' => '1',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 1500000,
            'kondisi' => 'baik',
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        InventarisKibValue::create([
            'inventaris_id' => $inv->id,
            'kib_field_key' => 'judul_pencipta',
            'value' => 'Ensiklopedia Sains',
        ]);

        InventarisKibValue::create([
            'inventaris_id' => $inv->id,
            'kib_field_key' => 'spesifikasi',
            'value' => 'Hard Cover',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('export-data.store-kib'), [
            'kib_type' => 'E',
            'format' => 'pdf',
            'tahun' => 'all',
            'signature_option' => 'with',
            'pejabat_kiri' => $this->pejabatSekretaris->id,
            'pejabat_kanan' => $this->pejabatPetugas->id,
            'tanggal_dokumen' => '2026-08-27',
            'keterangan_dokumen' => 'Demo',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $filename = $response->json('filename');
        $this->assertStringEndsWith('.pdf', $filename);
    }

    public function test_export_kib_all_types_excel_with_custom_pejabat(): void
    {
        $pejabatPerbekel = \App\Models\Pejabat::create([
            'nama' => 'I Nyoman Suwarjana, SE',
            'nip' => '197501012000031001',
            'jabatan' => 'Perbekel Desa Demo',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('export-data.store-kib'), [
            'kib_type' => 'all',
            'format' => 'excel',
            'tahun' => 'all',
            'signature_option' => 'with',
            'pejabat_kiri' => $pejabatPerbekel->id,
            'pejabat_kanan' => $this->pejabatSekretaris->id,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $filename = $response->json('filename');
        $this->assertStringContainsString('kib_semua_', $filename);
        $this->assertStringEndsWith('.xlsx', $filename);

        $log = \App\Models\ExportLog::where('nama_file', $filename)->first();
        $this->assertNotNull($log);
        $this->assertCount(2, $log->pejabat_override);
        $this->assertEquals('I Nyoman Suwarjana, SE', $log->pejabat_override[0]['nama']);
        $this->assertEquals($this->pejabatSekretaris->nama, $log->pejabat_override[1]['nama']);
    }
}
