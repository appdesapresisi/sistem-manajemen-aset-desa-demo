<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Inventaris;
use App\Models\InventarisDeletedLog;
use App\Models\JenisInventaris;
use App\Models\Role;
use App\Models\User;
use App\Services\LaporanAsetService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenghapusanAsetTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected JenisInventaris $jenis;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $role = Role::firstOrCreate(
            ['nama' => 'super_admin'],
            ['tugas' => 'Akses penuh', 'deskripsi' => 'Role untuk pengujian']
        );

        $this->user = User::create([
            'nama' => 'Admin Penghapusan QA',
            'email' => 'penghapusan-qa-' . uniqid() . '@example.test',
            'password' => 'secret123',
            'role_id' => $role->id,
            'is_aktif' => 1,
        ]);

        $this->jenis = JenisInventaris::create([
            'nama' => 'Peralatan QA ' . uniqid(),
            'deskripsi' => 'Uji Penghapusan Aset',
            'kib_type' => 'B',
        ]);
    }

    public function test_penghapusan_aset_index_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('penghapusan-aset.index'));
        $response->assertOk();
        $response->assertSee('Penghapusan Aset (Mutasi Berkurang)');
    }

    public function test_penghapusan_aset_index_with_preselected_asset(): void
    {
        $aset = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Mesin Genset Rusak Total',
            'kode_barang' => '3.05.01.01.001',
            'nup' => '1',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 45000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('penghapusan-aset.index', ['inventaris_id' => $aset->id]));
        $response->assertOk();
        $response->assertSee('Mesin Genset Rusak Total');
    }

    public function test_process_official_asset_deletion_with_pdf(): void
    {
        $aset = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Komputer Kantor Rusak Berat',
            'kode_barang' => '3.05.02.04.099',
            'nup' => '2',
            'tahun_perolehan' => 2024,
            'nilai_perolehan' => 12000000,
            'satuan' => 'Unit',
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $pdfFile = UploadedFile::fake()->create('Berita_Acara_Penghapusan_01.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('penghapusan-aset.store'), [
            'inventaris_id'      => $aset->id,
            'tanggal_hapus'      => '2026-08-27',
            'alasan_penghapusan' => 'Rusak Berat / Tidak Dapat Dipakai',
            'nomor_sk'           => 'BA.01/PEM-DS/VIII/2026',
            'keterangan'         => 'Komputer tersambar petir dan motherboard mati total.',
            'dokumen_pdf'        => $pdfFile,
        ]);

        $response->assertRedirect(route('penghapusan-aset.index', ['tab' => 'aktif']));
        $response->assertSessionHas('success');

        // Pastikan aset sudah terhapus dari tabel inventaris
        $this->assertDatabaseMissing('inventaris', ['id' => $aset->id]);

        // Pastikan aset tersimpan di inventaris_deleted_log
        $this->assertDatabaseHas('inventaris_deleted_log', [
            'kode_barang'        => '3.05.02.04.099',
            'nup'                => '2',
            'nama_barang'        => 'Komputer Kantor Rusak Berat',
            'nomor_sk'           => 'BA.01/PEM-DS/VIII/2026',
            'alasan_penghapusan' => 'Rusak Berat / Tidak Dapat Dipakai',
            'nilai_perolehan'    => 12000000,
        ]);

        $deletedLog = InventarisDeletedLog::where('nomor_sk', 'BA.01/PEM-DS/VIII/2026')->first();
        $this->assertNotNull($deletedLog);
        $this->assertNotNull($deletedLog->dokumen_pdf_path);
        Storage::disk('public')->assertExists($deletedLog->dokumen_pdf_path);

        // Test Show Detail API
        $showResp = $this->actingAs($this->user)->getJson(route('penghapusan-aset.show', $deletedLog->id));
        $showResp->assertOk()->assertJson(['success' => true]);
        $showResp->assertJsonPath('data.nomor_sk', 'BA.01/PEM-DS/VIII/2026');

        // Test Download PDF
        $downloadResp = $this->actingAs($this->user)->get(route('penghapusan-aset.download-pdf', $deletedLog->id));
        $downloadResp->assertOk();

        // Pastikan record ini masuk ke perhitungan Laporan Aset "Mutasi Berkurang"
        $laporanService = app(LaporanAsetService::class);
        $rows = $laporanService->buildRows(2026, 'tahunan');
        $matchingRow = collect($rows)->first(fn($r) => str_contains($r['nama'], 'Komputer Kantor Rusak Berat'));
        $this->assertNotNull($matchingRow, 'Aset yang dihapus resmi tidak muncul di Laporan Aset.');
        $this->assertEquals(1, $matchingRow['kurang_kwant']);
        $this->assertEquals(12000000, $matchingRow['kurang_nilai']);
    }

    public function test_bulk_official_asset_deletion_with_single_pdf(): void
    {
        $aset1 = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Kursi Kerja Rusak 1',
            'kode_barang' => '3.05.02.01.001',
            'nup' => '1',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 500000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $aset2 = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Kursi Kerja Rusak 2',
            'kode_barang' => '3.05.02.01.001',
            'nup' => '2',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 500000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $aset3 = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Meja Rapat Rusak',
            'kode_barang' => '3.05.02.01.002',
            'nup' => '1',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 2000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $pdfFile = UploadedFile::fake()->create('BA_Penghapusan_Masal.pdf', 800, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('penghapusan-aset.store'), [
            'inventaris_ids'     => [$aset1->id, $aset2->id, $aset3->id],
            'tanggal_hapus'      => '2026-08-27',
            'alasan_penghapusan' => 'Rusak Berat / Tidak Dapat Dipakai',
            'nomor_sk'           => 'BA.MASAL/09/2026',
            'keterangan'         => 'Penghapusan inventaris mebel kantor tahun 2023 yang lapuk termakan rayap.',
            'dokumen_pdf'        => $pdfFile,
        ]);

        $response->assertRedirect(route('penghapusan-aset.index', ['tab' => 'aktif']));
        $response->assertSessionHas('success');

        // Pastikan ketiga aset terhapus dari tabel inventaris
        $this->assertDatabaseMissing('inventaris', ['id' => $aset1->id]);
        $this->assertDatabaseMissing('inventaris', ['id' => $aset2->id]);
        $this->assertDatabaseMissing('inventaris', ['id' => $aset3->id]);

        // Pastikan ketiga aset tercatat di log dengan nomor SK dan file PDF yang sama
        $logs = InventarisDeletedLog::where('nomor_sk', 'BA.MASAL/09/2026')->get();
        $this->assertCount(3, $logs);
        
        $firstLogPath = $logs[0]->dokumen_pdf_path;
        $this->assertNotNull($firstLogPath);
        Storage::disk('public')->assertExists($firstLogPath);

        foreach ($logs as $log) {
            $this->assertEquals($firstLogPath, $log->dokumen_pdf_path);
            $this->assertEquals('BA.MASAL/09/2026', $log->nomor_sk);
        }
    }

    public function test_process_asset_deletion_with_image_file(): void
    {
        $aset = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Kamera Foto Rusak',
            'kode_barang' => '3.05.02.04.088',
            'nup' => '1',
            'tahun_perolehan' => 2024,
            'nilai_perolehan' => 7500000,
            'satuan' => 'Unit',
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $imageFile = UploadedFile::fake()->image('foto_bukti_rusak.jpg', 600, 600);

        $response = $this->actingAs($this->user)->post(route('penghapusan-aset.store'), [
            'inventaris_id' => $aset->id,
            'tanggal_hapus' => '2026-08-28',
            'dokumen_pdf'   => $imageFile,
        ]);

        $response->assertRedirect(route('penghapusan-aset.index', ['tab' => 'aktif']));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('inventaris', ['id' => $aset->id]);
        $deletedLog = InventarisDeletedLog::where('kode_barang', '3.05.02.04.088')->first();
        $this->assertNotNull($deletedLog);
        $this->assertEquals('foto_bukti_rusak.jpg', $deletedLog->dokumen_pdf_name);
        Storage::disk('public')->assertExists($deletedLog->dokumen_pdf_path);
    }

    public function test_process_asset_deletion_with_existing_document(): void
    {
        Storage::fake('public');
        // Buat file dokumen existing di fake storage
        $existingPath = 'dokumen-penghapusan/sk_bersama_2026.pdf';
        Storage::disk('public')->put($existingPath, 'Dummy PDF content');

        // Buat log sebelumnya yang memakai file ini
        InventarisDeletedLog::create([
            'kode_barang'           => '1.01.01.01.001',
            'nama_barang'           => 'Aset Lama Terhapus',
            'jenis_inventaris_nama' => 'Tanah',
            'satuan'                => 'M2',
            'nilai_perolehan'       => 100000000,
            'tahun_perolehan'       => 2020,
            'nomor_sk'              => 'SK/01/2026',
            'dokumen_pdf_path'      => $existingPath,
            'dokumen_pdf_name'      => 'SK_Penghapusan_Bersama.pdf',
            'tanggal_hapus'         => '2026-09-01',
            'deleted_at'            => now(),
            'deleted_by'            => $this->user->id,
        ]);

        $aset = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Laptop Rusak Total',
            'kode_barang' => '2.05.01.05.099',
            'nup' => '1',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 12000000,
            'satuan' => 'Unit',
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('penghapusan-aset.store'), [
            'inventaris_id'         => $aset->id,
            'tanggal_hapus'         => '2026-09-05',
            'metode_dokumen'        => 'existing',
            'existing_dokumen_path' => $existingPath,
            'nomor_sk'              => 'SK/01/2026',
        ]);

        $response->assertRedirect(route('penghapusan-aset.index', ['tab' => 'aktif']));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('inventaris', ['id' => $aset->id]);
        $newLog = InventarisDeletedLog::where('kode_barang', '2.05.01.05.099')->first();
        $this->assertNotNull($newLog);
        $this->assertEquals($existingPath, $newLog->dokumen_pdf_path);
        $this->assertEquals('SK_Penghapusan_Bersama.pdf', $newLog->dokumen_pdf_name);
    }

    public function test_tabs_pagination_and_filters_are_completely_independent(): void
    {
        // 1. When requesting tab=riwayat with per_page=20
        $responseRiwayat = $this->actingAs($this->user)->get(route('penghapusan-aset.index', [
            'tab' => 'riwayat',
            'per_page' => 20,
            'search' => 'NonExistentAssetXYZ'
        ]));

        $responseRiwayat->assertOk();
        $activeAssets = $responseRiwayat->viewData('activeAssets');
        $deletedLogs = $responseRiwayat->viewData('deletedLogs');

        // Tab Riwayat should receive 20 items per page and be filtered by the search
        $this->assertEquals(20, $deletedLogs->perPage());
        $this->assertEquals(0, $deletedLogs->total());

        // Tab Aktif must remain at default 10 per page and NOT be filtered by riwayat search!
        $this->assertEquals(10, $activeAssets->perPage());

        // 2. When requesting tab=aktif with per_page=50
        $responseAktif = $this->actingAs($this->user)->get(route('penghapusan-aset.index', [
            'tab' => 'aktif',
            'per_page' => 50,
        ]));

        $responseAktif->assertOk();
        $this->assertEquals(50, $responseAktif->viewData('activeAssets')->perPage());
        $this->assertEquals(10, $responseAktif->viewData('deletedLogs')->perPage());
    }

    public function test_penghapusan_aset_index_ajax_returns_json_for_both_tabs(): void
    {
        // 1. AJAX request for tab aktif
        $responseAktif = $this->actingAs($this->user)->getJson(route('penghapusan-aset.index', ['tab' => 'aktif']));
        $responseAktif->assertOk();
        $responseAktif->assertJsonStructure([
            'tab',
            'items',
            'total',
            'pagination' => ['firstItem', 'lastItem', 'currentPage', 'lastPage', 'perPage', 'total'],
            'totalActiveCount',
        ]);
        $this->assertEquals('aktif', $responseAktif->json('tab'));

        // 2. AJAX request for tab riwayat
        $responseRiwayat = $this->actingAs($this->user)->getJson(route('penghapusan-aset.index', ['tab' => 'riwayat']));
        $responseRiwayat->assertOk();
        $responseRiwayat->assertJsonStructure([
            'tab',
            'items',
            'total',
            'pagination' => ['firstItem', 'lastItem', 'currentPage', 'lastPage', 'perPage', 'total'],
            'stats' => ['totalDihapus', 'totalNilai', 'dihapusTahunIni', 'topKategoriNama', 'topKategoriTotal', 'trends'],
            'totalDeletedCount',
        ]);
        $this->assertEquals('riwayat', $responseRiwayat->json('tab'));
    }

    public function test_penghapusan_aset_index_supports_matching_filters_structure(): void
    {
        // Test tab aktif with array jenis, kondisi, and sort nama_asc
        $responseAktif = $this->actingAs($this->user)->get(route('penghapusan-aset.index', [
            'tab' => 'aktif',
            'jenis' => [$this->jenis->id],
            'tahun' => '2023',
            'kondisi' => ['rusak_berat'],
            'sort' => 'nama_asc',
        ]));
        $responseAktif->assertOk();
        $responseAktif->assertSee('Kondisi Aset');
        $responseAktif->assertSee('name="kondisiAktif[]"', false);

        // Test tab riwayat with array jenis_nama, tahun_hapus, and sort nama_asc
        $responseRiwayat = $this->actingAs($this->user)->get(route('penghapusan-aset.index', [
            'tab' => 'riwayat',
            'jenis_nama' => [$this->jenis->nama],
            'tahun_hapus' => '2024',
            'sort' => 'nama_asc',
        ]));
        $responseRiwayat->assertOk();
    }

    public function test_only_kaur_umum_and_superiors_can_delete_asset(): void
    {
        $aset = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'kode_barang' => '02.01.01.01.099',
            'nup' => '99',
            'nama_barang' => 'Aset Uji Otorisasi',
            'nilai_perolehan' => 2500000,
            'tahun_perolehan' => 2024,
            'status' => 'aktif',
        ]);

        $roleStaf = Role::firstOrCreate(['nama' => 'staf'], ['tugas' => 'Staf', 'deskripsi' => 'Staf']);
        $userStaf = User::create([
            'nama' => 'Staf Demo',
            'username' => 'staf.test.' . uniqid(),
            'password' => 'secret123',
            'role_id' => $roleStaf->id,
            'is_aktif' => 1,
        ]);

        // Staf attempting to delete should be forbidden (403)
        $file = UploadedFile::fake()->create('sk_penghapusan.pdf', 100, 'application/pdf');
        $responseForbidden = $this->actingAs($userStaf)->post(route('penghapusan-aset.store'), [
            'inventaris_id' => $aset->id,
            'tanggal_hapus' => '2026-08-29',
            'alasan_penghapusan' => 'Penghapusan Resmi',
            'dokumen_pdf' => $file,
        ]);
        $responseForbidden->assertForbidden();

        // Check that UI for Staf hides bulk bar, select all checkbox, and deletion modal
        $responseUiStaf = $this->actingAs($userStaf)->get(route('penghapusan-aset.index'));
        $responseUiStaf->assertOk();
        $responseUiStaf->assertDontSee('id="bulk-bar"', false);
        $responseUiStaf->assertDontSee('id="check-all"', false);
        $responseUiStaf->assertDontSee('id="modal-create-penghapusan"', false);
    }

    public function test_restore_single_asset_from_deleted_log_to_active_inventory(): void
    {
        // 1. Create deleted log with snapshot
        $log = InventarisDeletedLog::create([
            'kode_barang'           => '3.05.02.04.101',
            'nup'                   => '5',
            'nama_barang'           => 'Laptop Dell Kantor Desa',
            'jenis_inventaris_nama' => $this->jenis->nama,
            'merk_type'             => 'Dell Latitude 3420',
            'satuan'                => 'Unit',
            'nilai_perolehan'       => 15000000,
            'tahun_perolehan'       => 2023,
            'nomor_sk'              => 'SK/RESTORE/01',
            'alasan_penghapusan'    => 'Salah Hapus',
            'deleted_at'            => now(),
            'deleted_by'            => $this->user->id,
            'asset_data_snapshot'   => [
                'inventaris' => [
                    'jenis_inventaris_id' => $this->jenis->id,
                    'nama_barang'         => 'Laptop Dell Kantor Desa',
                    'kode_barang'         => '3.05.02.04.101',
                    'nup'                 => '5',
                    'tahun_perolehan'     => 2023,
                    'nilai_perolehan'     => 15000000,
                    'satuan'              => 'Unit',
                    'status'              => 'disetujui',
                    'created_by'          => $this->user->id,
                ],
                'kib_values' => [
                    ['merk_type' => 'Dell Latitude 3420']
                ],
                'kir_values' => [],
                'values'     => [],
            ],
        ]);

        $this->assertDatabaseHas('inventaris_deleted_log', ['id' => $log->id]);

        // 2. Execute restore
        $response = $this->actingAs($this->user)->post(route('penghapusan-aset.restore'), [
            'log_id' => $log->id,
        ]);

        $response->assertRedirect(route('penghapusan-aset.index', ['tab' => 'riwayat']));
        $response->assertSessionHas('success');

        // 3. Verify deleted log is removed and inventaris is restored
        $this->assertDatabaseMissing('inventaris_deleted_log', ['id' => $log->id]);
        $this->assertDatabaseHas('inventaris', [
            'nama_barang'     => 'Laptop Dell Kantor Desa',
            'kode_barang'     => '3.05.02.04.101',
            'nup'             => '5',
            'nilai_perolehan' => 15000000,
        ]);
    }

    public function test_bulk_restore_assets_from_deleted_log(): void
    {
        $log1 = InventarisDeletedLog::create([
            'kode_barang'           => '3.05.02.04.201',
            'nup'                   => '1',
            'nama_barang'           => 'Kursi Putar Rapat 01',
            'jenis_inventaris_nama' => $this->jenis->nama,
            'merk_type'             => 'Chitose',
            'satuan'                => 'Unit',
            'nilai_perolehan'       => 750000,
            'tahun_perolehan'       => 2022,
            'deleted_at'            => now(),
            'deleted_by'            => $this->user->id,
        ]);

        $log2 = InventarisDeletedLog::create([
            'kode_barang'           => '3.05.02.04.202',
            'nup'                   => '2',
            'nama_barang'           => 'Kursi Putar Rapat 02',
            'jenis_inventaris_nama' => $this->jenis->nama,
            'merk_type'             => 'Chitose',
            'satuan'                => 'Unit',
            'nilai_perolehan'       => 750000,
            'tahun_perolehan'       => 2022,
            'deleted_at'            => now(),
            'deleted_by'            => $this->user->id,
        ]);

        // AJAX Bulk Restore
        $response = $this->actingAs($this->user)->postJson(route('penghapusan-aset.restore'), [
            'log_ids' => [$log1->id, $log2->id],
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'        => true,
            'restored_count' => 2,
        ]);

        $this->assertDatabaseMissing('inventaris_deleted_log', ['id' => $log1->id]);
        $this->assertDatabaseMissing('inventaris_deleted_log', ['id' => $log2->id]);
        $this->assertDatabaseHas('inventaris', ['nama_barang' => 'Kursi Putar Rapat 01']);
        $this->assertDatabaseHas('inventaris', ['nama_barang' => 'Kursi Putar Rapat 02']);
    }

    public function test_unauthorized_user_cannot_restore_asset(): void
    {
        $log = InventarisDeletedLog::create([
            'kode_barang'           => '3.05.02.04.999',
            'nup'                   => '99',
            'nama_barang'           => 'Aset Terlarang Pulih',
            'jenis_inventaris_nama' => $this->jenis->nama,
            'nilai_perolehan'       => 1000000,
            'tahun_perolehan'       => 2021,
            'deleted_at'            => now(),
        ]);

        $roleStaf = Role::firstOrCreate(['nama' => 'staf'], ['tugas' => 'Staf', 'deskripsi' => 'Staf']);
        $userStaf = User::create([
            'nama' => 'Staf Demo 2',
            'username' => 'staf.test2.' . uniqid(),
            'password' => 'secret123',
            'role_id' => $roleStaf->id,
            'is_aktif' => 1,
        ]);

        $responseForbidden = $this->actingAs($userStaf)->post(route('penghapusan-aset.restore'), [
            'log_id' => $log->id,
        ]);
        $responseForbidden->assertForbidden();

        $this->assertDatabaseHas('inventaris_deleted_log', ['id' => $log->id]);
    }

    public function test_penghapusan_aset_index_with_preselected_multiple_assets(): void
    {
        $aset1 = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Aset Bulk 01 ' . uniqid(),
            'kode_barang' => '3.05.01.01.101',
            'nup' => '1',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 1000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $aset2 = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Aset Bulk 02 ' . uniqid(),
            'kode_barang' => '3.05.01.01.102',
            'nup' => '2',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 2000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('penghapusan-aset.index', [
            'inventaris_ids' => "{$aset1->id},{$aset2->id}"
        ]));

        $response->assertOk();
        $response->assertSee($aset1->nama_barang);
        $response->assertSee($aset2->nama_barang);
        $response->assertSee('openPreselectedBulkModal');
    }

    public function test_manajemen_aset_bulk_destroy_rejects_mutasi_berkurang(): void
    {
        $aset = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Aset Uji Guard ' . uniqid(),
            'kode_barang' => '3.05.01.01.103',
            'nup' => '3',
            'tahun_perolehan' => 2024,
            'nilai_perolehan' => 500000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->json('POST', route('manajemen-aset.bulk-destroy'), [
            'ids' => [$aset->id],
            'tipe_hapus' => 'mutasi_berkurang',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonStructure(['redirect_url']);

        // Aset tidak boleh terhapus
        $this->assertDatabaseHas('inventaris', ['id' => $aset->id]);
    }

    public function test_manajemen_aset_single_destroy_rejects_mutasi_berkurang(): void
    {
        $aset = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Aset Single Guard ' . uniqid(),
            'kode_barang' => '3.05.01.01.104',
            'nup' => '4',
            'tahun_perolehan' => 2024,
            'nilai_perolehan' => 500000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->json('DELETE', route('manajemen-aset.destroy', $aset->id), [
            'tipe_hapus' => 'mutasi_berkurang',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonStructure(['redirect_url']);

        // Aset tidak boleh terhapus
        $this->assertDatabaseHas('inventaris', ['id' => $aset->id]);
    }

    public function test_penghapusan_aset_restores_custom_field_values_and_bulan_perolehan(): void
    {
        $field = \App\Models\JenisInventarisData::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_field' => 'nomor_rangka',
            'label_field' => 'Nomor Rangka',
            'field_type' => 'custom',
            'tipe' => 'text',
        ]);

        $aset = Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang' => 'Kendaraan Restorasi ' . uniqid(),
            'kode_barang' => '3.05.01.01.105',
            'nup' => '5',
            'tahun_perolehan' => 2024,
            'nilai_perolehan' => 25000000,
            'asal_usul' => 'apbdes',
            'status' => 'disetujui',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        \App\Models\InventarisValue::create([
            'inventaris_id' => $aset->id,
            'jenis_inventaris_data_id' => $field->id,
            'value' => 'MH1KC511XKK-9988',
        ]);

        \App\Models\InventarisLaporanAsetValue::create([
            'inventaris_id' => $aset->id,
            'bulan_perolehan' => 4,
        ]);

        // Hapus aset via modul Penghapusan Aset
        $file = UploadedFile::fake()->create('berita_acara.pdf', 100, 'application/pdf');
        $this->actingAs($this->user)->post(route('penghapusan-aset.store'), [
            'inventaris_id' => $aset->id,
            'tanggal_hapus' => now()->toDateString(),
            'alasan_penghapusan' => 'Rusak Berat',
            'dokumen_pdf' => $file,
        ]);

        $this->assertDatabaseMissing('inventaris', ['id' => $aset->id]);

        $log = InventarisDeletedLog::where('kode_barang', '3.05.01.01.105')->first();
        $this->assertNotNull($log);

        // Restore aset
        $restoreResp = $this->actingAs($this->user)->post(route('penghapusan-aset.restore'), [
            'log_id' => $log->id,
        ]);
        $restoreResp->assertRedirect();

        $restored = Inventaris::where('kode_barang', '3.05.01.01.105')->first();
        $this->assertNotNull($restored);

        // Verifikasi bahwa custom field value dan bulan perolehan terpulihkan
        $this->assertDatabaseHas('inventaris_values', [
            'inventaris_id' => $restored->id,
            'jenis_inventaris_data_id' => $field->id,
            'value' => 'MH1KC511XKK-9988',
        ]);

        $this->assertDatabaseHas('inventaris_laporan_aset_values', [
            'inventaris_id' => $restored->id,
            'bulan_perolehan' => 4,
        ]);
    }

    public function test_download_pdf_enforces_can_delete_aset_authorization(): void
    {
        Storage::fake('public');
        $pdfPath = 'dokumen-penghapusan/sk_uji.pdf';
        Storage::disk('public')->put($pdfPath, 'PDF dummy content');

        $log = InventarisDeletedLog::create([
            'kode_barang' => '3.05.01.01.999',
            'nama_barang' => 'Aset Uji PDF',
            'tahun_perolehan' => 2022,
            'nilai_perolehan' => 500000,
            'asal_usul' => 'apbdes',
            'kondisi' => 'rusak_berat',
            'alasan_penghapusan' => 'Rusak Total',
            'tanggal_hapus' => now()->toDateString(),
            'dokumen_pdf_path' => $pdfPath,
            'dokumen_pdf_name' => 'SK_Uji.pdf',
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);

        // 1. User staf (tanpa hak canDeleteAset) harus ditolak dengan HTTP 403 Forbidden
        $roleStaf = Role::firstOrCreate(['nama' => 'staf'], ['deskripsi' => 'Staf']);
        $userStaf = User::create([
            'nama' => 'Staf Biasa ' . uniqid(),
            'username' => 'staf.' . uniqid(),
            'password' => 'secret123',
            'role_id' => $roleStaf->id,
            'is_aktif' => 1,
        ]);

        $responseForbidden = $this->actingAs($userStaf)->get(route('penghapusan-aset.download-pdf', ['id' => $log->id]));
        $responseForbidden->assertForbidden();

        // 2. User berwenang (misal Super Admin / Kaur Umum dengan canDeleteAset) berhasil mengunduh berkas
        $responseOk = $this->actingAs($this->user)->get(route('penghapusan-aset.download-pdf', ['id' => $log->id]));
        $responseOk->assertOk();
    }
}


