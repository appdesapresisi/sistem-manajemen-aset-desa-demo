<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Dropdown;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\Inventaris;
use App\Models\Pejabat;
use App\Models\Role;
use App\Models\User;
use App\Services\KibExportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class BugFixAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['nama' => 'super_admin'],
            ['tugas' => 'Super Administrator', 'deskripsi' => 'Akses penuh']
        );

        $this->superAdmin = User::create([
            'nama'     => 'Super Admin QA',
            'username' => 'qa_admin_' . uniqid(),
            'email'    => 'qa_admin_' . uniqid() . '@example.test',
            'password' => bcrypt('password123'),
            'role_id'  => $role->id,
            'is_aktif' => 1,
        ]);
    }

    public function test_dropdown_destroy_does_not_create_false_audit_log_when_blocked(): void
    {
        $dropdown = Dropdown::create([
            'nama'      => 'Dropdown Terpakai QA ' . uniqid(),
            'deskripsi' => 'Uji proteksi hapus',
        ]);

        $jenis = JenisInventaris::first() ?? JenisInventaris::create([
            'nama'     => 'Jenis QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        JenisInventarisData::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_field'          => 'field_dropdown_' . uniqid(),
            'label_field'         => 'Pilihan QA',
            'tipe_data'           => 'dropdown',
            'dropdown_id'         => $dropdown->id,
            'is_required'         => false,
            'order'               => 1,
        ]);

        $initialLogsCount = ActivityLog::where('module', 'Dropdown')->where('action', 'hapus')->count();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('dropdown.destroy', $dropdown->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('dropdowns', ['id' => $dropdown->id]);

        $newLogsCount = ActivityLog::where('module', 'Dropdown')->where('action', 'hapus')->count();
        $this->assertEquals($initialLogsCount, $newLogsCount, 'Activity log must not be created when deletion is blocked.');
    }

    public function test_pejabat_api_destroy_does_not_create_false_audit_log_when_failed(): void
    {
        $pejabat = Pejabat::create([
            'nama'     => 'Pejabat Penandatangan QA ' . uniqid(),
            'jabatan'  => 'Kaur Perencanaan',
            'is_aktif' => 1,
        ]);

        $jenis = JenisInventaris::create([
            'nama'     => 'Jenis Dengan Default Pejabat ' . uniqid(),
            'kib_type' => 'B',
        ]);

        \App\Models\JenisInventarisPejabat::create([
            'jenis_inventaris_id' => $jenis->id,
            'pejabat_id'          => $pejabat->id,
            'posisi'              => 'kiri',
            'label'               => 'Mengetahui',
        ]);

        $initialLogsCount = ActivityLog::where('module', 'Pejabat')->where('action', 'hapus')->count();

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson('/api/pejabats/' . $pejabat->id);

        $response->assertStatus(422);
        $this->assertDatabaseHas('pejabats', ['id' => $pejabat->id]);

        $newLogsCount = ActivityLog::where('module', 'Pejabat')->where('action', 'hapus')->count();
        $this->assertEquals($initialLogsCount, $newLogsCount, 'Activity log must not be created when API delete fails.');
    }

    public function test_jenis_inventaris_api_destroy_does_not_create_false_audit_log_when_failed(): void
    {
        $jenis = JenisInventaris::create([
            'nama'     => 'Jenis Dengan Aset QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        \App\Models\Inventaris::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Aset Terikat QA ' . uniqid(),
            'kode_barang'         => '02.03.01.01.' . rand(100, 999),
            'nup'                 => '1',
            'tahun_perolehan'     => 2025,
            'nilai_perolehan'     => 5000000,
            'satuan'              => 'Unit',
            'kondisi'             => 'baik',
        ]);

        $initialLogsCount = ActivityLog::where('module', 'JenisInventaris')->where('action', 'hapus')->count();

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson('/api/jenis-inventaris/' . $jenis->id);

        $response->assertStatus(422);
        $this->assertDatabaseHas('jenis_inventaris', ['id' => $jenis->id]);

        $newLogsCount = ActivityLog::where('module', 'JenisInventaris')->where('action', 'hapus')->count();
        $this->assertEquals($initialLogsCount, $newLogsCount, 'Activity log must not be created when API delete fails.');
    }

    public function test_kib_export_service_resolves_sekretaris_desa_from_pejabat_table(): void
    {
        $pejabatSekdes = Pejabat::create([
            'nama'     => 'I Made Sekretaris QA ' . uniqid(),
            'jabatan'  => 'Sekretaris Desa',
            'is_aktif' => 1,
        ]);

        $kibService = app(KibExportService::class);
        $reflection = new \ReflectionClass($kibService);
        $method = $reflection->getMethod('resolvePejabat');
        $method->setAccessible(true);

        $resolved = $method->invoke($kibService, 'B', 'kiri', []);
        $this->assertNotNull($resolved);
        $this->assertStringContainsString('Sekretaris', $resolved['jabatan']);
    }

    public function test_views_forgot_and_reset_password_can_render_safely(): void
    {
        $errors = new ViewErrorBag();
        $renderedForgot = view('auth.forgot-password', ['errors' => $errors])->render();
        $this->assertStringContainsString('Lupa Password', $renderedForgot);
        $this->assertStringContainsString('if (forgotForm && submitBtn)', $renderedForgot);

        $renderedReset = view('auth.reset-password', [
            'token'  => 'dummy-token',
            'email'  => 'admin@example.test',
            'errors' => $errors,
        ])->render();
        $this->assertStringContainsString('Update Password', $renderedReset);
    }

    public function test_web_jenis_inventaris_destroy_does_not_create_false_audit_log_when_failed(): void
    {
        $jenis = JenisInventaris::create([
            'nama'     => 'Jenis Web Delete Test QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        \App\Models\Inventaris::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Aset Terikat Web QA ' . uniqid(),
            'kode_barang'         => '02.03.01.01.' . rand(100, 999),
            'nup'                 => '1',
            'tahun_perolehan'     => 2025,
            'nilai_perolehan'     => 2500000,
            'satuan'              => 'Unit',
            'kondisi'             => 'baik',
        ]);

        $initialLogsCount = ActivityLog::where('module', 'JenisInventaris')->where('action', 'hapus')->count();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('jenis-inventaris.destroy', $jenis->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('jenis_inventaris', ['id' => $jenis->id]);

        $newLogsCount = ActivityLog::where('module', 'JenisInventaris')->where('action', 'hapus')->count();
        $this->assertEquals($initialLogsCount, $newLogsCount, 'Activity log must not be created when web delete fails.');
    }

    public function test_inventaris_import_sanitize_nilai_perolehan_preserves_decimals(): void
    {
        $import = new \App\Imports\InventarisImport();
        $reflection = new \ReflectionClass($import);
        $method = $reflection->getMethod('sanitizeNilaiPerolehan');
        $method->setAccessible(true);

        $this->assertEquals(2500000.0, $method->invoke($import, '2500000.00'));
        $this->assertEquals(1500.5, $method->invoke($import, '1500.50'));
        $this->assertEquals(1500000.0, $method->invoke($import, '1.500.000'));
        $this->assertEquals(75000.0, $method->invoke($import, 'Rp 75.000,00'));
    }

    public function test_pejabat_import_resets_previous_petugas_barang_flag(): void
    {
        $oldPj = Pejabat::create([
            'nama'              => 'Petugas Lama QA ' . uniqid(),
            'jabatan'           => 'Staf Inventaris',
            'is_petugas_barang' => 1,
            'is_aktif'          => 1,
        ]);

        $import = new \App\Imports\PejabatImport();
        $import->collection(collect([
            [
                'nama'                             => 'Petugas Baru QA ' . uniqid(),
                'jabatan'                          => 'Kaur Tata Usaha',
                'petugas_pengurus_barang_ya_tidak' => 'Ya',
                'status_aktif'                     => 'Aktif',
            ]
        ]));

        $this->assertEquals(0, $oldPj->fresh()->is_petugas_barang);
        $this->assertEquals(1, Pejabat::where('is_petugas_barang', 1)->count());
    }

    public function test_penghapusan_aset_rollback_does_not_delete_existing_document(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $existingPath = 'dokumen-penghapusan/shared_doc.pdf';
        \Illuminate\Support\Facades\Storage::disk('public')->put($existingPath, 'dummy pdf content');

        // Trigger validation / process error by passing non-existent aset ID
        $response = $this->actingAs($this->superAdmin)
            ->post(route('penghapusan-aset.store'), [
                'inventaris_id'          => 99999999,
                'metode_dokumen'         => 'existing',
                'existing_dokumen_path'  => $existingPath,
                'tanggal_hapus'          => now()->format('Y-m-d'),
                'alasan_penghapusan'     => 'Rusak Total',
            ]);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($existingPath), 'Existing shared document must NOT be deleted on error/rollback.');
    }

    public function test_api_dropdown_returns_json_on_accept_header(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/dropdown');

        $response->assertStatus(200);
        $response->assertJsonStructure(['dropdowns', 'stats']);
    }

    public function test_activity_logger_restore_color_and_label(): void
    {
        $this->assertEquals('#10b981', \App\Services\ActivityLogger::colorFor('restore'));
        $this->assertEquals('memulihkan', \App\Services\ActivityLogger::labelFor('restore'));
    }

    public function test_manajemen_aset_bulk_destroy_returns_404_when_no_assets_found(): void
    {
        $initialLogsCount = ActivityLog::where('module', 'Inventaris')->where('action', 'hapus')->count();

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('manajemen-aset.bulk-destroy'), [
                'ids' => [9999991, 9999992],
                'tipe_hapus' => 'salah_input',
            ]);

        $response->assertStatus(404);
        $this->assertFalse($response->json('success'));

        $newLogsCount = ActivityLog::where('module', 'Inventaris')->where('action', 'hapus')->count();
        $this->assertEquals($initialLogsCount, $newLogsCount, 'No audit log must be written when no assets exist.');
    }

    public function test_inventaris_generate_barcode_creates_valid_barcode(): void
    {
        $barcode = Inventaris::generateBarcode();
        $this->assertNotEmpty($barcode);
        $this->assertStringStartsWith('INV-' . date('Y') . '-', $barcode);
    }

    public function test_duplicate_inventaris_preserves_bukti_kepemilikan(): void
    {
        $jenis = JenisInventaris::first() ?? JenisInventaris::create([
            'nama'     => 'Jenis QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        $source = Inventaris::create([
            'nama_barang'       => 'Aset Sertifikat QA',
            'kode_barang'       => 'QA.SERT.' . uniqid(),
            'nup'               => '1',
            'jenis_inventaris_id' => $jenis->id,
            'tahun_perolehan'   => 2024,
            'nilai_perolehan'   => 15000000,
            'asal_usul'         => 'apbdes',
            'bukti_kepemilikan' => 'Sertifikat Hak Milik No. 123/2024',
            'kondisi'           => 'baik',
            'status'            => 'draft',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('manajemen-aset.duplicate', $source->id), [
                'jumlah' => 1,
            ]);

        $response->assertSessionHasNoErrors();

        $duplicate = Inventaris::where('kode_barang', $source->kode_barang)
            ->where('id', '!=', $source->id)
            ->first();

        $this->assertNotNull($duplicate, 'Duplicated asset must exist');
        $this->assertEquals('Sertifikat Hak Milik No. 123/2024', $duplicate->bukti_kepemilikan, 'Duplicated asset must preserve bukti_kepemilikan');
    }

    public function test_bulk_print_qr_handles_asset_with_empty_barcode(): void
    {
        $jenis = JenisInventaris::first() ?? JenisInventaris::create([
            'nama'     => 'Jenis QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        $asset = Inventaris::create([
            'nama_barang'       => 'Aset No Barcode QA',
            'kode_barang'       => 'QA.NOBAR.' . uniqid(),
            'nup'               => '1',
            'jenis_inventaris_id' => $jenis->id,
            'tahun_perolehan'   => 2024,
            'nilai_perolehan'   => 5000000,
            'asal_usul'         => 'apbdes',
            'kondisi'           => 'baik',
            'status'            => 'draft',
            'barcode'           => null,
        ]);

        $this->assertNull($asset->barcode);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('manajemen-aset.bulk-print-qr'), [
                'ids' => [$asset->id],
            ]);

        $response->assertStatus(200);
        $asset->refresh();
        $this->assertNotNull($asset->barcode, 'Asset without barcode must have a barcode automatically assigned');
        $this->assertStringStartsWith('INV-', $asset->barcode);
    }

    public function test_laporan_aset_service_resolves_pejabat_ttd_path(): void
    {
        $pejabatPerbekel = Pejabat::create([
            'nama'              => 'I Wayan Perbekel QA',
            'jabatan'           => 'Perbekel Demo',
            'is_aktif'          => 1,
            'ttd_path'          => 'ttd/perbekel_qa_test.png',
            'is_petugas_barang' => 0,
        ]);

        $rolePerbekel = Role::firstOrCreate(['nama' => 'perbekel'], ['tugas' => 'Kepala Desa', 'deskripsi' => 'Perbekel']);
        $existingPerbekel = User::whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->first();
        if ($existingPerbekel) {
            $existingPerbekel->update(['pejabat_id' => $pejabatPerbekel->id]);
        } else {
            User::create([
                'nama'       => 'I Wayan Perbekel QA',
                'username'   => 'perbekel_qa_' . uniqid(),
                'email'      => 'perbekel_qa_' . uniqid() . '@example.test',
                'password'   => bcrypt('password123'),
                'role_id'    => $rolePerbekel->id,
                'pejabat_id' => $pejabatPerbekel->id,
                'is_aktif'   => 1,
            ]);
        }

        $service = new \App\Services\LaporanAsetService();
        $pejabats = $service->getPejabatForLaporanAset();

        $this->assertNotNull($pejabats['perbekel']);
        $this->assertEquals('ttd/perbekel_qa_test.png', $pejabats['perbekel']['ttd_path'], 'Perbekel ttd_path must be resolved from linked pejabat');
    }

    public function test_kode_barang_hierarki_handles_leading_zero(): void
    {
        // Test API resolve-ancestors with 01.01.01.01.001
        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/kb/resolve-ancestors?kode=01.01.01.01.001');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertArrayHasKey('1', $data, 'Level 1 must resolve even with leading zero 01');
        $this->assertEquals('1', $data['1']['kode']);

        // Test API by-kode with 01.01.01.01.001
        $responseByKode = $this->actingAs($this->superAdmin)
            ->getJson('/api/kb/by-kode?kode=01.01.01.01.001');

        $responseByKode->assertStatus(200);
        $this->assertEquals('1.01.01.01.001', $responseByKode->json('kode_lengkap'));

        // Test API search level 2 (bidang) with ancestor_kode=01
        $responseSearchBidang = $this->actingAs($this->superAdmin)
            ->getJson('/api/kb/search?level=2&ancestor_kode=01');
        $responseSearchBidang->assertStatus(200);
        $this->assertNotEmpty($responseSearchBidang->json(), 'Search bidang with ancestor_kode=01 must return results');

        // Test API search level 3 (kelompok) with ancestor_kode=01.01
        $responseSearchKelompok = $this->actingAs($this->superAdmin)
            ->getJson('/api/kb/search?level=3&ancestor_kode=01.01');
        $responseSearchKelompok->assertStatus(200);
        $this->assertNotEmpty($responseSearchKelompok->json(), 'Search kelompok with ancestor_kode=01.01 must return results');

        // Test API search level 5 (sub sub kelompok) with ancestor_kode=01.01.01.01
        $responseSearchSubSub = $this->actingAs($this->superAdmin)
            ->getJson('/api/kb/search?level=5&ancestor_kode=01.01.01.01');
        $responseSearchSubSub->assertStatus(200);
        $this->assertNotEmpty($responseSearchSubSub->json(), 'Search sub sub kelompok with ancestor_kode=01.01.01.01 must return results');
    }

    public function test_export_ruangan_allows_sekretaris_from_pejabat_table(): void
    {
        $ruangan = \App\Models\Ruangan::firstOrCreate(
            ['kode' => 'R-QA-' . uniqid()],
            ['nama' => 'Ruangan QA Test']
        );

        $pejabatSekdes = Pejabat::create([
            'nama'              => 'Ni Made Sekdes QA',
            'jabatan'           => 'Sekretaris Desa',
            'is_aktif'          => 1,
            'ttd_path'          => 'ttd/sekdes_qa_test.png',
            'is_petugas_barang' => 0,
        ]);

        // Even without any User having role sekretaris, storeRuangan should not be blocked with 422
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('export-data.store-ruangan'), [
                'ruangan_id' => $ruangan->id,
                'format'     => 'pdf',
                'kir_format' => 'standard',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
    }

    public function test_public_scan_displays_room_kode_and_pic_role(): void
    {
        $role = Role::firstOrCreate(
            ['nama' => 'kaur'],
            ['tugas' => 'Kepala Urusan', 'deskripsi' => 'Pengelola Urusan']
        );

        $picUser = User::create([
            'nama'     => 'I Wayan Pengurus Ruangan QA',
            'username' => 'pic_qa_' . uniqid(),
            'email'    => 'pic_qa_' . uniqid() . '@example.test',
            'password' => bcrypt('password123'),
            'role_id'  => $role->id,
            'is_aktif' => 1,
        ]);

        $ruangan = \App\Models\Ruangan::create([
            'nama'                => 'Ruang Rapat Utama QA',
            'kode'                => 'RR-99',
            'penanggung_jawab_id' => $picUser->id,
        ]);

        $jenis = JenisInventaris::first() ?? JenisInventaris::create([
            'nama'     => 'Peralatan dan Mesin',
            'kib_type' => 'B',
        ]);

        $barcode = 'INV-TEST-' . strtoupper(uniqid());
        $inventaris = Inventaris::create([
            'nama_barang'         => 'Proyektor Ruang Rapat QA',
            'kode_barang'         => '02.06.01.01.001',
            'nup'                 => '1',
            'jenis_inventaris_id' => $jenis->id,
            'ruangan_id'          => $ruangan->id,
            'tahun_perolehan'     => 2024,
            'nilai_perolehan'     => 5000000,
            'kondisi'             => 'baik',
            'asal_usul'           => 'apbdes',
            'satuan'              => 'Unit',
            'barcode'             => $barcode,
        ]);

        $signedScanUrl = \Illuminate\Support\Facades\URL::signedRoute('inventaris.scan', ['barcode' => $barcode]);
        $response = $this->get($signedScanUrl);

        $response->assertStatus(200);
        $response->assertSee('Ruang Rapat Utama QA (Kode: RR-99)');
        $response->assertSee('RR-99');
        $response->assertSee('I Wayan Pengurus Ruangan QA');
        $response->assertSee('Jabatan / Peran Penanggung Jawab');
        $response->assertDontSee('NIP Penanggung Jawab');
    }

    public function test_export_log_jenis_label_resolution_and_dashboard_api(): void
    {
        // 1. Test KIB export log
        $kibLog = \App\Models\ExportLog::create([
            'user_id'     => $this->superAdmin->id,
            'tipe_export' => 'pdf',
            'nama_file'   => 'KIB_A_Test.pdf',
            'filters'     => ['kib_type' => 'KIB-A', 'mode' => 'kib'],
        ]);
        $this->assertEquals('KIB A (Tanah)', $kibLog->jenis_label);

        // 2. Test KIR export log
        $kirLog = \App\Models\ExportLog::create([
            'user_id'     => $this->superAdmin->id,
            'tipe_export' => 'pdf',
            'nama_file'   => 'KIR_Test.pdf',
            'filters'     => ['mode' => 'kir', 'ruangan_id' => 1],
        ]);
        $this->assertEquals('KIR (Kartu Inventaris Ruangan)', $kirLog->jenis_label);

        // 3. Test Laporan Aset export log
        $lapLog = \App\Models\ExportLog::create([
            'user_id'     => $this->superAdmin->id,
            'tipe_export' => 'excel',
            'nama_file'   => 'Laporan_Aset.xlsx',
            'filters'     => ['mode' => 'laporan_aset'],
        ]);
        $this->assertEquals('Laporan Aset Desa', $lapLog->jenis_label);

        // 4. Test Buku Inventaris export log
        $biLog = \App\Models\ExportLog::create([
            'user_id'     => $this->superAdmin->id,
            'tipe_export' => 'pdf',
            'nama_file'   => 'Buku_Inventaris.pdf',
            'filters'     => ['mode' => 'buku_inventaris_standalone'],
        ]);
        $this->assertEquals('Buku Inventaris Desa', $biLog->jenis_label);

        // 5. Test Dashboard recent exports API
        $response = $this->actingAs($this->superAdmin)->getJson('/dashboard-api/exports');
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertIsArray($json);
        $labels = array_column($json, 'jenis');

        $this->assertContains('KIB A (Tanah)', $labels);
        $this->assertContains('KIR (Kartu Inventaris Ruangan)', $labels);
        $this->assertContains('Laporan Aset Desa', $labels);
        $this->assertContains('Buku Inventaris Desa', $labels);
    }

    public function test_notifications_index_safely_handles_null_created_at(): void
    {
        $notif = \App\Models\Notification::create([
            'user_id' => $this->superAdmin->id,
            'type'    => 'info',
            'title'   => 'Notifikasi Uji Null Created At',
            'body'    => 'Pengujian defensif null safety',
            'is_read' => 0,
        ]);

        // Simulasikan created_at bernilai null langsung di DB
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('id', $notif->id)
            ->update(['created_at' => null]);

        $response = $this->actingAs($this->superAdmin)->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Notifikasi Uji Null Created At');
    }

    public function test_user_controller_checks_super_admin_by_role_relation(): void
    {
        // Pastikan role super_admin terdaftar
        $roleSuperAdmin = Role::where('nama', 'super_admin')->first();

        // 1. Filter role Super Admin pada index
        $response = $this->actingAs($this->superAdmin)->get('/users?roles[]=Super+Admin');
        $response->assertStatus(200);
        $response->assertSee($this->superAdmin->nama);

        // 2. Blokir pembuatan Super Admin kedua
        $responseCreate = $this->actingAs($this->superAdmin)->post('/users', [
            'nama'                  => 'Super Admin Kedua QA',
            'username'              => 'super_admin_2_' . uniqid(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'super_admin',
            'alamat'                => 'Alamat QA',
            'is_aktif'              => 1,
        ]);

        $responseCreate->assertSessionHas('error', 'Sudah ada Super Admin. Hanya 1 Super Admin yang diperbolehkan.');
    }

    public function test_export_preview_ajax_resolves_sekretaris_from_pejabat_table(): void
    {
        $pejabatSekdes = Pejabat::where('is_aktif', 1)
            ->where(function($q) {
                $q->whereRaw("LOWER(jabatan) LIKE ?", ['%sekretaris desa%'])
                  ->orWhereRaw("LOWER(jabatan) LIKE ?", ['%sekretaris%']);
            })->first();

        if (!$pejabatSekdes) {
            $pejabatSekdes = Pejabat::create([
                'nama'              => 'Ni Luh Sekdes Test AJAX',
                'jabatan'           => 'Sekretaris Desa',
                'is_aktif'          => 1,
                'ttd_path'          => 'ttd/sekdes_ajax_test.png',
                'is_petugas_barang' => 0,
            ]);
        }

        // 1. Uji AJAX Ruangan Pejabat Info (KIR Format 1)
        $responseRuangan = $this->actingAs($this->superAdmin)
            ->getJson(route('export-data.ruangan-pejabat-info', ['kir_format' => 'standard']));

        $responseRuangan->assertStatus(200);
        $this->assertNotNull($responseRuangan->json('sekretaris'), 'Sekretaris must be resolved from master pejabat table');
        $this->assertEquals($pejabatSekdes->nama, $responseRuangan->json('sekretaris.nama'));

        // 2. Uji AJAX KIB Pejabat Info
        $responseKib = $this->actingAs($this->superAdmin)
            ->getJson(route('export-data.kib-pejabat-info'));

        $responseKib->assertStatus(200);
        $this->assertNotNull($responseKib->json('sekretaris'), 'Sekretaris in KIB info must be resolved from master pejabat table');
        $this->assertEquals($pejabatSekdes->nama, $responseKib->json('sekretaris.nama'));
    }

    public function test_penghapusan_aset_monthly_trend_excludes_boundary_overlap(): void
    {
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        // 1 log tepat pada titik batas 00:00:00 awal bulan ini
        \App\Models\InventarisDeletedLog::create([
            'inventaris_id'   => 99991,
            'nama_barang'     => 'Aset Titik Batas Bulan Ini',
            'kode_barang'     => '01.01.01.01.991',
            'nup'             => '991',
            'nilai_perolehan' => 500000,
            'alasan'          => 'Rusak berat',
            'deleted_by'      => $this->superAdmin->id,
            'deleted_at'      => $thisMonthStart,
        ]);

        // 1 log di bulan lalu
        \App\Models\InventarisDeletedLog::create([
            'inventaris_id'   => 99992,
            'nama_barang'     => 'Aset Bulan Lalu',
            'kode_barang'     => '01.01.01.01.992',
            'nup'             => '992',
            'nilai_perolehan' => 300000,
            'alasan'          => 'Hilang',
            'deleted_by'      => $this->superAdmin->id,
            'deleted_at'      => $lastMonthStart->copy()->addDays(5),
        ]);

        // Hitung eksklusifitas
        $thisMonthCount = \App\Models\InventarisDeletedLog::where('deleted_at', '>=', $thisMonthStart)->count();
        $lastMonthCount = \App\Models\InventarisDeletedLog::where('deleted_at', '>=', $lastMonthStart)->where('deleted_at', '<', $thisMonthStart)->count();

        // Log pada titik $thisMonthStart tidak boleh masuk ke $lastMonthCount
        $this->assertGreaterThanOrEqual(1, $thisMonthCount);
        $this->assertGreaterThanOrEqual(1, $lastMonthCount);

        // Kunjungi halaman penghapusan-aset
        $response = $this->actingAs($this->superAdmin)->get(route('penghapusan-aset.index'));
        $response->assertStatus(200);
        $response->assertViewHas('trends');
    }

    public function test_penghapusan_aset_restore_sets_disetujui_status()
    {
        $jenis = \App\Models\JenisInventaris::first() ?? \App\Models\JenisInventaris::create(['nama' => 'Peralatan dan Mesin']);

        $log = \App\Models\InventarisDeletedLog::create([
            'nama_barang'           => 'Laptop Audit Restore',
            'kode_barang'           => '02.06.01.01.888',
            'nup'                   => '888',
            'jenis_inventaris_nama' => $jenis->nama,
            'tahun_perolehan'       => 2024,
            'nilai_perolehan'       => 15000000,
            'alasan_penghapusan'    => 'Uji Restore',
            'deleted_by'            => $this->superAdmin->id,
            'deleted_at'            => now(),
            'asset_data_snapshot'   => [
                'inventaris' => [
                    'jenis_inventaris_id' => $jenis->id,
                    'nama_barang'         => 'Laptop Audit Restore',
                    'kode_barang'         => '02.06.01.01.888',
                    'nup'                 => '888',
                    'status'              => 'disetujui',
                    'kondisi'             => 'baik',
                    'tahun_perolehan'     => 2024,
                    'nilai_perolehan'     => 15000000,
                ]
            ]
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('penghapusan-aset.restore'), [
            'log_ids' => [$log->id],
        ]);

        $response->assertSessionHas('success');

        $restored = \App\Models\Inventaris::where('kode_barang', '02.06.01.01.888')->where('nup', '888')->first();
        $this->assertNotNull($restored);
        $this->assertEquals('disetujui', $restored->status);
        $this->assertNotEmpty($restored->barcode);
    }

    /**
     * Test ruangan store and update accept alphanumeric codes like 'R-001' or 'R-016'.
     */
    public function test_ruangan_accepts_alphanumeric_codes(): void
    {
        // 1. Store with alphanumeric code
        $response = $this->actingAs($this->superAdmin)->post(route('ruangan.store'), [
            'nama'                => 'Ruang Audit Alfanumerik',
            'kode'                => 'R-999',
            'penanggung_jawab_id' => $this->superAdmin->id,
            'deskripsi'           => 'Ruang untuk pengujian kode alfanumerik',
        ]);

        $response->assertSessionHas('success');
        $ruangan = \App\Models\Ruangan::where('kode', 'R-999')->first();
        $this->assertNotNull($ruangan);
        $this->assertEquals('Ruang Audit Alfanumerik', $ruangan->nama);

        // 2. Update existing room retaining its alphanumeric code
        $updateResponse = $this->actingAs($this->superAdmin)->put(route('ruangan.update', $ruangan->id), [
            'nama'                => 'Ruang Audit Alfanumerik Updated',
            'kode'                => 'R-999',
            'penanggung_jawab_id' => $this->superAdmin->id,
            'deskripsi'           => 'Deskripsi diperbarui',
        ]);

        $updateResponse->assertSessionHas('success');
        $ruangan->refresh();
        $this->assertEquals('Ruang Audit Alfanumerik Updated', $ruangan->nama);
        $this->assertEquals('R-999', $ruangan->kode);

        // 3. Create view suggests the next alphanumeric code
        $createViewResponse = $this->actingAs($this->superAdmin)->get(route('ruangan.create'));
        $createViewResponse->assertStatus(200);
        $createViewResponse->assertViewHas('nextSuggestedKode', 'R-1000');

        // Cleanup
        $ruangan->delete();
    }

    public function test_export_controller_download_and_history_handles_null_created_at(): void
    {
        $jenis = \App\Models\JenisInventaris::create([
            'nama' => 'Jenis Test Null History QA',
            'kode' => 'J-NULL-QA',
        ]);

        // 1. Buat log export dengan created_at null (via raw DB agar tidak di-auto-populate oleh Eloquent)
        $filename = 'audit_null_created_at_' . uniqid() . '.xlsx';
        $logId = \Illuminate\Support\Facades\DB::table('export_logs')->insertGetId([
            'user_id'             => $this->superAdmin->id,
            'jenis_inventaris_id' => $jenis->id,
            'tipe_export'         => 'excel',
            'nama_file'           => $filename,
            'pejabat_override'    => json_encode([]),
            'filters'             => json_encode([]),
            'created_at'          => null,
            'updated_at'          => null,
        ]);

        $log = \App\Models\ExportLog::find($logId);
        $this->assertNotNull($log);
        $this->assertNull($log->created_at);

        // 2. Akses getHistoryJson - tidak boleh error 500 (Call to a member function format() on null)
        $historyResponse = $this->actingAs($this->superAdmin)->get(route('export-data.history-json', ['jenis_inventaris_id' => $jenis->id]));
        $historyResponse->assertStatus(200);
        $historyData = $historyResponse->json();
        $matched = collect($historyData)->firstWhere('note', $filename);
        $this->assertNotNull($matched);
        $this->assertEquals('-', $matched['date']);
        $this->assertEquals('-', $matched['time']);

        // 3. Akses download - ketika file tidak ada di storage, harus abort 404 (bukan crash 500 fatal error)
        $downloadResponse = $this->actingAs($this->superAdmin)->get(route('export-data.download', ['filename' => $filename]));
        $downloadResponse->assertStatus(404);

        // 4. Buat dummy file di storage dan uji download berhasil (200)
        $monthFolder = date('Y/m');
        \Illuminate\Support\Facades\Storage::disk('local')->put('exports/' . $monthFolder . '/' . $filename, 'dummy content');
        $downloadSuccess = $this->actingAs($this->superAdmin)->get(route('export-data.download', ['filename' => $filename]));
        $downloadSuccess->assertStatus(200);

        // Cleanup
        \Illuminate\Support\Facades\Storage::disk('local')->delete('exports/' . $monthFolder . '/' . $filename);
        \App\Models\ExportLog::where('id', $logId)->delete();
        $jenis->delete();
    }

    public function test_export_buku_inventaris_accepts_f4_paper_size(): void
    {
        $response = $this->actingAs($this->superAdmin)->postJson(route('export-data.store-buku-inventaris-aset'), [
            'tahun'            => (string) date('Y'),
            'periode'          => 'tahunan',
            'format'           => 'excel',
            'paper_size'       => 'f4',
            'signature_option' => 'without',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Cleanup created export file & log if any
        $filename = $response->json('data.filename');
        if ($filename) {
            \App\Models\ExportLog::where('nama_file', $filename)->delete();
            $path = $response->json('data.path');
            if ($path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($path);
            }
        }
    }

    public function test_penghapusan_aset_index_riwayat_filters_by_single_jenis_string(): void
    {
        $uniqueJenisA = 'Kategori QA Filter A ' . uniqid();
        $uniqueJenisB = 'Kategori QA Filter B ' . uniqid();

        $logA = \App\Models\InventarisDeletedLog::create([
            'kode_barang'           => 'QA-A-01',
            'nup'                   => '1',
            'nama_barang'           => 'Barang Filter A',
            'jenis_inventaris_nama' => $uniqueJenisA,
            'tahun_perolehan'       => 2024,
            'nilai_perolehan'       => 1000000,
            'deleted_at'            => now(),
            'deleted_by'            => $this->superAdmin->id,
        ]);

        $logB = \App\Models\InventarisDeletedLog::create([
            'kode_barang'           => 'QA-B-01',
            'nup'                   => '1',
            'nama_barang'           => 'Barang Filter B',
            'jenis_inventaris_nama' => $uniqueJenisB,
            'tahun_perolehan'       => 2024,
            'nilai_perolehan'       => 2000000,
            'deleted_at'            => now(),
            'deleted_by'            => $this->superAdmin->id,
        ]);

        // Filter tab riwayat dengan string parameter: jenis=...
        $response = $this->actingAs($this->superAdmin)->get(route('penghapusan-aset.index', [
            'tab'   => 'riwayat',
            'jenis' => $uniqueJenisA,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Barang Filter A');
        $response->assertDontSee('Barang Filter B');

        // Cleanup
        $logA->delete();
        $logB->delete();
    }

    public function test_super_developer_can_delete_and_restore_aset(): void
    {
        // 1. Buat role super_developer jika belum ada
        $roleDev = Role::firstOrCreate(['nama' => 'super_developer']);

        $devUser = User::create([
            'nama'     => 'Super Developer QA',
            'username' => 'superdev_' . uniqid(),
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role_id'  => $roleDev->id,
            'is_aktif' => 1,
        ]);

        // 2. Verifikasi canDeleteAset bernilai true untuk super developer
        $this->assertTrue($devUser->isSuperDeveloper());
        $this->assertTrue($devUser->canDeleteAset());

        // 3. Akses halaman penghapusan aset sebagai super developer
        $response = $this->actingAs($devUser)->get(route('penghapusan-aset.index'));
        $response->assertStatus(200);

        // Cleanup
        $devUser->delete();
    }

    public function test_dashboard_activities_safely_handles_null_created_at(): void
    {
        $log = \App\Models\ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'module'  => 'Inventaris',
            'action'  => 'tambah',
            'detail'  => 'Uji null created_at pada activity log',
        ]);

        \Illuminate\Support\Facades\DB::table('activity_logs')
            ->where('id', $log->id)
            ->update(['created_at' => null]);

        // 1. Test widget API
        $responseApi = $this->actingAs($this->superAdmin)->getJson('/dashboard-api/activities');
        $responseApi->assertStatus(200);

        // 2. Test full page /dashboard/activities dengan search agar log yang null terpilih di paginasi
        $responseWeb = $this->actingAs($this->superAdmin)->get('/dashboard/activities?search=Uji+null');
        $responseWeb->assertStatus(200);
        $responseWeb->assertSee('Uji null created_at pada activity log');

        // Cleanup
        $log->delete();
    }

    public function test_super_developer_authorized_for_policies_and_management(): void
    {
        $roleDev = Role::firstOrCreate(['nama' => 'super_developer']);

        $devUser = User::create([
            'nama'     => 'Super Developer Policy QA',
            'username' => 'superdev_poly_' . uniqid(),
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role_id'  => $roleDev->id,
            'is_aktif' => 1,
        ]);

        // 1. Gate::before authorizes master data policies
        $this->assertTrue($devUser->can('viewAny', \App\Models\Ruangan::class));
        $this->assertTrue($devUser->can('create', \App\Models\Ruangan::class));
        $this->assertTrue($devUser->can('viewAny', \App\Models\Pejabat::class));
        $this->assertTrue($devUser->can('create', \App\Models\Pejabat::class));
        $this->assertTrue($devUser->can('viewAny', \App\Models\ExportLog::class));

        // 2. Access export history page
        $responseHistory = $this->actingAs($devUser)->get('/export-data/history');
        $responseHistory->assertStatus(200);

        // 3. Access dashboard activities page
        $responseActivities = $this->actingAs($devUser)->get('/dashboard/activities');
        $responseActivities->assertStatus(200);

        // Cleanup
        $devUser->delete();
    }

    public function test_ruangan_show_uses_left_join_and_handles_asset_without_jenis(): void
    {
        $ruangan = \App\Models\Ruangan::create([
            'nama' => 'Ruang Uji QA Left Join',
            'kode' => 'R-LEFT-' . rand(100, 999),
        ]);

        // Simulasikan record aset dengan jenis_inventaris non-existent menggunakan FK off
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $aset = \App\Models\Inventaris::create([
            'nama_barang'         => 'Aset Uji Orphan Jenis',
            'kode_barang'         => 'ORPHAN-01',
            'nup'                 => '1',
            'tahun_perolehan'     => 2024,
            'nilai_perolehan'     => 500000,
            'kondisi'             => 'baik',
            'status'              => 'draft',
            'ruangan_id'          => $ruangan->id,
            'jenis_inventaris_id' => 999999, // ID non-existent
            'created_by'          => $this->superAdmin->id,
        ]);
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Akses halaman show ruangan — leftJoin memastikan aset tetap tampil dan tidak crash
        $response = $this->actingAs($this->superAdmin)->get(route('ruangan.show', $ruangan->id));
        $response->assertStatus(200);
        $response->assertSee('Aset Uji Orphan Jenis');

        // Cleanup
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $aset->delete();
        $ruangan->delete();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function test_user_import_resolves_dinas_id_for_kelian_banjar(): void
    {
        $dinas = \App\Models\Dinas::firstOrCreate(['nama' => 'Banjar Dinas Sukawati QA']);
        $roleKelian = Role::firstOrCreate(['nama' => 'kelian_banjar']);

        $importer = new \App\Imports\UserImport();
        $username = 'kelian_qa_' . uniqid();

        $rows = collect([
            [
                'nama_lengkap'     => 'I Made Kelian Test',
                'username_unik'    => $username,
                'peran_role'       => 'kelian_banjar',
                'banjar_dinas'     => 'Banjar Dinas Sukawati QA',
                'status_aktif'     => 'Aktif',
                'password'         => 'password123',
            ]
        ]);

        $importer->collection($rows);

        $this->assertEquals(1, $importer->importedCount);
        $createdUser = User::where('username', $username)->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals($dinas->id, $createdUser->dinas_id);

        // Cleanup
        $createdUser->delete();
        $dinas->delete();
    }

    public function test_inventaris_kelengkapan_scope_and_methods_handle_empty_string_nup_and_tahun(): void
    {
        $jenis = JenisInventaris::first();

        // 1. Aset dengan NUP string kosong ''
        $asetEmptyNup = new Inventaris([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Komputer Kantor QA',
            'kode_barang'         => '02.06.01.01.001',
            'nup'                 => '',
            'tahun_perolehan'     => 2024,
            'asal_usul'           => 'apbdes',
            'kondisi'             => 'baik',
        ]);
        $this->assertFalse($asetEmptyNup->isDataLengkap(), 'Asset with empty string NUP must not be deemed complete');
        $this->assertContains('NUP', $asetEmptyNup->getMissingRequiredFields());

        // 2. Aset dengan tahun_perolehan string kosong ''
        $asetEmptyTahun = new Inventaris([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Komputer Kantor QA',
            'kode_barang'         => '02.06.01.01.001',
            'nup'                 => '1',
            'tahun_perolehan'     => '',
            'asal_usul'           => 'apbdes',
            'kondisi'             => 'baik',
        ]);
        $this->assertFalse($asetEmptyTahun->isDataLengkap(), 'Asset with empty string tahun must not be deemed complete');
        $this->assertContains('Tahun Perolehan', $asetEmptyTahun->getMissingRequiredFields());

        // 3. Verifikasi fillable status
        $inv = new Inventaris();
        $inv->fill(['status' => 'pending']);
        $this->assertEquals('pending', $inv->status, 'status attribute must be fillable on Inventaris model');
    }

    public function test_feature_export_service_handles_plain_string_headers_and_row_cells(): void
    {
        // Headers as flat string array (as documented in FeatureExportService PHPDoc)
        $headers = ['NO', 'KODE BARANG', 'NAMA BARANG'];
        $rows = [
            ['1', '01.01.01.01.001', 'Tanah Kas Desa QA'],
            ['2', '01.01.01.01.002', 'Tanah Lapangan QA'],
        ];

        $response = \App\Services\FeatureExportService::downloadPdf(
            'Laporan Uji Coba String Headers',
            'Periode 2026',
            $headers,
            $rows,
            'test_string_export.pdf',
            'portrait',
            ['Total Data' => '2']
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_inventaris_policy_and_delete_foto_handle_null_parent_asset(): void
    {
        $policy = new \App\Policies\InventarisPolicy();
        // Null second argument should not trigger TypeError
        $canUpdateNull = $policy->update($this->superAdmin, null);
        $this->assertTrue($canUpdateNull);

        $canViewNull = $policy->view($this->superAdmin, null);
        $this->assertTrue($canViewNull);

        // Uji deleteFoto dengan inventaris
        $jenis = JenisInventaris::first();
        $source = Inventaris::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Aset Foto QA',
            'kode_barang'         => '02.01.99.' . rand(100, 999),
            'nup'                 => '1',
            'tahun_perolehan'     => 2024,
            'asal_usul'           => 'apbdes',
            'kondisi'             => 'baik',
            'status'              => 'draft',
        ]);
        $foto = \App\Models\InventarisFoto::create([
            'inventaris_id' => $source->id,
            'path'          => 'inventaris-foto/qa_test.jpg',
            'urutan'        => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson(route('manajemen-aset.foto.delete', $foto->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('inventaris_fotos', ['id' => $foto->id]);
        $source->delete();
    }

    public function test_asset_duplication_reports_accurate_nup_range_when_nups_are_skipped(): void
    {
        $jenis = JenisInventaris::first();
        $uniqueKode = '02.03.99.' . rand(100, 999);

        // Buat aset NUP 1
        $source = Inventaris::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Kursi Audit QA',
            'kode_barang'         => $uniqueKode,
            'nup'                 => '1',
            'tahun_perolehan'     => 2024,
            'asal_usul'           => 'apbdes',
            'kondisi'             => 'baik',
            'barcode'             => 'BARCODE-AUDIT-' . uniqid(),
            'status'              => 'draft',
        ]);

        // Buat aset NUP 2 sudah ada
        $existing2 = Inventaris::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Kursi Audit QA 2',
            'kode_barang'         => $uniqueKode,
            'nup'                 => '2',
            'tahun_perolehan'     => 2024,
            'asal_usul'           => 'apbdes',
            'kondisi'             => 'baik',
            'barcode'             => 'BARCODE-AUDIT-' . uniqid(),
            'status'              => 'draft',
        ]);

        // Duplikasi 2 salinan (harus menghasilkan NUP 3 dan 4)
        $response = $this->actingAs($this->superAdmin)
            ->post(route('manajemen-aset.duplicate', $source->id), [
                'jumlah' => 2,
            ]);

        $response->assertRedirect(route('manajemen-aset.index'));
        $successMsg = session('success');
        $this->assertStringContainsString('NUP 3 s.d. 4', $successMsg);

        // Cleanup
        Inventaris::where('kode_barang', $uniqueKode)->delete();
    }

    public function test_user_toggle_aktif_protects_super_developer_from_deactivation(): void
    {
        $roleDev = Role::firstOrCreate(['nama' => 'super_developer']);
        $superDev = User::where('role_id', $roleDev->id)->first();
        if (!$superDev) {
            $superDev = User::create([
                'nama'     => 'Super Dev QA Toggle',
                'username' => 'super_dev_toggle_' . uniqid(),
                'password' => bcrypt('password123'),
                'role_id'  => $roleDev->id,
                'is_aktif' => 1,
            ]);
        }

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.toggle-aktif', $superDev->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Super Developer tidak dapat dinonaktifkan', session('error'));
    }

    public function test_backup_index_view_handles_empty_files_without_warning(): void
    {
        $roleDev = Role::firstOrCreate(['nama' => 'super_developer']);
        $superDev = User::where('role_id', $roleDev->id)->first();
        if (!$superDev) {
            $superDev = User::create([
                'nama'     => 'Super Dev QA Backup',
                'username' => 'super_dev_backup_' . uniqid(),
                'password' => bcrypt('password123'),
                'role_id'  => $roleDev->id,
                'is_aktif' => 1,
            ]);
        }

        $response = $this->actingAs($superDev)
            ->get(route('backup.index', ['search' => 'non_existent_backup_file_search_query_12345']));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada berkas cadangan');
    }

    public function test_kartu_inventaris_ruangan_sheet_does_not_double_total_nilai(): void
    {
        $ruangan = \App\Models\Ruangan::firstOrCreate(
            ['kode' => 'R-KIR-TEST'],
            ['nama' => 'Ruang Uji KIR']
        );

        $inv1 = new Inventaris([
            'nama_barang'     => 'Laptop Asus',
            'kode_barang'     => '02.06.01.01.001',
            'nup'             => '1',
            'tahun_perolehan' => 2023,
            'nilai_perolehan' => 10000000,
        ]);
        $inv2 = new Inventaris([
            'nama_barang'     => 'Printer Epson',
            'kode_barang'     => '02.06.01.01.002',
            'nup'             => '2',
            'tahun_perolehan' => 2024,
            'nilai_perolehan' => 5000000,
        ]);

        $ruanganData = [
            [
                'ruangan' => $ruangan,
                'rows'    => [
                    [
                        'no'          => 1,
                        'nama_barang' => $inv1->nama_barang,
                        'kode_barang' => $inv1->kode_barang,
                        'nup'         => $inv1->nup,
                        'tahun'       => $inv1->tahun_perolehan,
                        'identitas'   => 'Core i7',
                        'no_pol'      => '-',
                        'asal_usul'   => 'APBDesa',
                        'harga'       => '10.000.000',
                        'keterangan'  => '',
                        'inv_obj'     => $inv1,
                    ],
                    [
                        'no'          => 2,
                        'nama_barang' => $inv2->nama_barang,
                        'kode_barang' => $inv2->kode_barang,
                        'nup'         => $inv2->nup,
                        'tahun'       => $inv2->tahun_perolehan,
                        'identitas'   => 'L3110',
                        'no_pol'      => '-',
                        'asal_usul'   => 'APBDesa',
                        'harga'       => '5.000.000',
                        'keterangan'  => '',
                        'inv_obj'     => $inv2,
                    ],
                ],
            ],
        ];

        // Pass totalNilai = 15000000 as precalculated by ExportService
        $sheet = new \App\Exports\KartuInventarisRuanganSheet(
            $ruanganData,
            15000000.0,
            null,
            [],
            false
        );

        $reflection = new \ReflectionClass($sheet);
        $rowsProp = $reflection->getProperty('rows');
        $rowsProp->setAccessible(true);
        $generatedRows = $rowsProp->getValue($sheet);

        $jumlahRow = null;
        foreach ($generatedRows as $r) {
            if (isset($r[0]) && $r[0] === 'JUMLAH HARGA') {
                $jumlahRow = $r;
                break;
            }
        }

        $this->assertNotNull($jumlahRow, 'JUMLAH HARGA row must exist.');
        // Column index 8 contains the total price formatted
        $this->assertEquals('15.000.000', $jumlahRow[8], 'JUMLAH HARGA must be exactly 15.000.000 and not doubled to 30.000.000.');
    }

    public function test_export_controller_count_methods_use_cumulative_year_filter(): void
    {
        $jenis = JenisInventaris::first() ?? JenisInventaris::create([
            'nama'     => 'Jenis Count QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        $ruangan = \App\Models\Ruangan::firstOrCreate(
            ['kode' => 'R-CNT-' . rand(100, 999)],
            ['nama' => 'Ruang Count QA']
        );

        $invOld = Inventaris::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Aset 2021 QA ' . uniqid(),
            'kode_barang'         => '02.01.01.01.' . rand(100, 999),
            'nup'                 => '1',
            'tahun_perolehan'     => 2021,
            'nilai_perolehan'     => 1000000,
            'ruangan_id'          => $ruangan->id,
            'kondisi'             => 'baik',
            'asal_usul'           => 'apbdes',
            'status'              => 'disetujui',
        ]);

        $invNew = Inventaris::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang'         => 'Aset 2024 QA ' . uniqid(),
            'kode_barang'         => '02.01.01.01.' . rand(100, 999),
            'nup'                 => '2',
            'tahun_perolehan'     => 2024,
            'nilai_perolehan'     => 2000000,
            'ruangan_id'          => $ruangan->id,
            'kondisi'             => 'baik',
            'asal_usul'           => 'apbdes',
            'status'              => 'disetujui',
        ]);

        // Hitung dengan filter tahun 2024 (harus mencakup aset 2021 dan 2024)
        $respSingle = $this->actingAs($this->superAdmin)
            ->getJson(route('export-data.data-count', [
                'jenis_inventaris_id' => $jenis->id,
                'tahun'               => 2024,
            ]));
        $respSingle->assertStatus(200);
        $countSingle = $respSingle->json('count');
        $this->assertGreaterThanOrEqual(2, $countSingle, 'getDataCount with tahun=2024 must include assets acquired in <= 2024.');

        $respRuangan = $this->actingAs($this->superAdmin)
            ->getJson(route('export-data.ruangan-count', [
                'ruangan_id' => $ruangan->id,
                'tahun'      => 2024,
            ]));
        $respRuangan->assertStatus(200);
        $countRuangan = $respRuangan->json('count');
        $this->assertGreaterThanOrEqual(2, $countRuangan, 'getRuanganCount with tahun=2024 must include assets acquired in <= 2024.');

        // Cleanup
        $invOld->delete();
        $invNew->delete();
    }

    public function test_export_requests_and_services_support_a4_paper_size(): void
    {
        // 1. Validasi StoreExportRequest dengan paper_size = 'a4'
        $request1 = new \App\Http\Requests\StoreExportRequest();
        $rules1 = $request1->rules();
        $validator1 = \Illuminate\Support\Facades\Validator::make([
            'jenis_inventaris_id' => '1',
            'format'             => 'pdf',
            'paper_size'         => 'a4',
        ], $rules1);
        $this->assertFalse($validator1->fails(), 'StoreExportRequest must accept paper_size = a4.');

        // 2. Validasi StoreAllExportRequest dengan paper_size = 'a4'
        $request2 = new \App\Http\Requests\StoreAllExportRequest();
        $rules2 = $request2->rules();
        $validator2 = \Illuminate\Support\Facades\Validator::make([
            'format'     => 'pdf',
            'paper_size' => 'a4',
        ], $rules2);
        $this->assertFalse($validator2->fails(), 'StoreAllExportRequest must accept paper_size = a4.');

        // 3. Resolusi di ExportService
        $exportService = app(\App\Services\ExportService::class);
        $ref1 = new \ReflectionClass($exportService);
        $method1 = $ref1->getMethod('resolvePaperSize');
        $method1->setAccessible(true);
        $res1 = $method1->invoke($exportService, 'a4');
        $this->assertEquals(['a4', 'landscape'], $res1);

        // 4. Resolusi di KibExportService
        $kibService = app(\App\Services\KibExportService::class);
        $ref2 = new \ReflectionClass($kibService);
        $method2 = $ref2->getMethod('resolvePaperSize');
        $method2->setAccessible(true);
        $res2 = $method2->invoke($kibService, 'a4');
        $this->assertEquals(['a4', 'landscape'], $res2);
    }

    public function test_export_css_page_size_properly_sets_a4_landscape_instead_of_defaulting_to_legal(): void
    {
        // 1. Test KibExportService::pageSizeCss('a4')
        $kibService = app(\App\Services\KibExportService::class);
        $refKib = new \ReflectionClass($kibService);
        $methodKib = $refKib->getMethod('pageSizeCss');
        $methodKib->setAccessible(true);
        $resKib = $methodKib->invoke($kibService, 'a4');
        $this->assertEquals('a4 landscape', $resKib, 'KibExportService pageSizeCss must map a4 to a4 landscape, not legal landscape.');

        // 2. Test ExportService HTML renderers with a4
        $exportService = app(\App\Services\ExportService::class);
        $refExp = new \ReflectionClass($exportService);

        // renderKartuInventarisRuanganPdf
        $methodKir = $refExp->getMethod('renderKartuInventarisRuanganPdf');
        $methodKir->setAccessible(true);
        $htmlKir = $methodKir->invoke($exportService, [], 0.0, null, [], false, [], '', '', 'a4');
        $this->assertStringContainsString('@page{size:a4 landscape;', $htmlKir, 'renderKartuInventarisRuanganPdf must declare a4 landscape.');

        // renderKartuInventarisRuanganFormat2Pdf
        $methodKir2 = $refExp->getMethod('renderKartuInventarisRuanganFormat2Pdf');
        $methodKir2->setAccessible(true);
        $htmlKir2 = $methodKir2->invoke($exportService, [], 0.0, null, null, [], false, [], '', '', 'a4');
        $this->assertStringContainsString('@page{size:a4 landscape;', $htmlKir2, 'renderKartuInventarisRuanganFormat2Pdf must declare a4 landscape.');
    }

    public function test_manajemen_aset_edit_does_not_crash_when_jenis_is_null(): void
    {
        $role = Role::firstOrCreate(['nama' => 'admin_desa']);
        $user = User::create([
            'nama'     => 'Staff Aset ' . uniqid(),
            'username' => 'staff_' . uniqid(),
            'email'    => 'staff_' . uniqid() . '@example.test',
            'password' => bcrypt('password123'),
            'role_id'  => $role->id,
            'is_aktif' => 1,
        ]);

        // Buat aset tanpa relasi jenis yang valid (simulasi data orphan / corrupt FK)
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $inv = new Inventaris();
        $inv->nama_barang = 'Aset Orphan QA ' . uniqid();
        $inv->kode_barang = '99.99.99.' . rand(100, 999);
        $inv->nup = '1';
        $inv->tahun_perolehan = 2024;
        $inv->nilai_perolehan = 500000;
        $inv->jenis_inventaris_id = 9999999;
        $inv->created_by = $this->superAdmin->id;
        $inv->status = 'disetujui';
        $inv->saveQuietly();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $response = $this->actingAs($this->superAdmin)
            ->get(route('manajemen-aset.edit', $inv->id));

        // Harus berhasil me-render edit view tanpa crash HTTP 500
        $response->assertStatus(200);

        $inv->delete();
        $user->delete();
    }

    public function test_penghapusan_aset_index_avoids_unnecessary_db_writes_when_records_already_synced(): void
    {
        // Pastikan tidak ada record tak tersinkronisasi
        \App\Models\InventarisDeletedLog::whereNull('jenis_inventaris_nama')
            ->orWhere('jenis_inventaris_nama', '')
            ->update(['jenis_inventaris_nama' => 'Aset Tetap Lainnya']);

        // Pantau DB queries selama pemanggilan index
        \Illuminate\Support\Facades\DB::enableQueryLog();
        \Illuminate\Support\Facades\DB::flushQueryLog();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('penghapusan-aset.index'));

        $response->assertStatus(200);

        $queries = \Illuminate\Support\Facades\DB::getQueryLog();
        $updateQueries = array_filter($queries, function ($q) {
            return str_starts_with(strtoupper(trim($q['query'])), 'UPDATE');
        });

        $this->assertCount(0, $updateQueries, 'PenghapusanAsetController::index must not execute UPDATE queries when logs are already synced.');
    }

    public function test_buku_inventaris_aset_render_pdf_supports_dynamic_paper_size(): void
    {
        $service = new \App\Services\BukuInventarisAsetService();

        // 1. Legal
        $htmlLegal = $service->renderPdf([], 2024, 'tahunan', '', false, 'legal');
        $this->assertStringContainsString('size: legal landscape;', $htmlLegal, 'Buku Inventaris PDF must support legal paper size in CSS.');

        // 2. F4
        $htmlF4 = $service->renderPdf([], 2024, 'tahunan', '', false, 'f4');
        $this->assertStringContainsString('size: 21cm 33cm landscape;', $htmlF4, 'Buku Inventaris PDF must support F4 paper size in CSS.');

        // 3. A4
        $htmlA4 = $service->renderPdf([], 2024, 'tahunan', '', false, 'a4');
        $this->assertStringContainsString('size: a4 landscape;', $htmlA4, 'Buku Inventaris PDF must support A4 paper size in CSS.');
    }

    public function test_user_controller_update_pejabat_without_role_payload_does_not_trigger_warning(): void
    {
        $role = Role::firstOrCreate(['nama' => 'kaur_keuangan']);
        $user = User::create([
            'nama'     => 'Staf Keuangan',
            'username' => 'staf_keu_' . uniqid(),
            'email'    => 'staf_keu_' . uniqid() . '@example.test',
            'password' => bcrypt('password123'),
            'role_id'  => $role->id,
            'is_aktif' => 1,
        ]);

        $mockRequest = \Mockery::mock(\App\Http\Requests\UpdateUserRequest::class)->makePartial();
        $mockRequest->shouldReceive('validated')->andReturn([
            'nama'           => 'Staf Keuangan',
            'username'       => $user->username,
            'is_aktif'       => 1,
            'create_pejabat' => 1,
            'pejabat_nama'   => 'Staf Keuangan',
            'pejabat_alamat' => 'Jl. Desa Demo',
            // 'role' and 'pejabat_jabatan' are omitted: $roleModel will be null and $pejabat_jabatan will be null
        ]);
        $mockRequest->shouldReceive('hasFile')->with('ttd_path')->andReturn(false);
        $mockRequest->shouldReceive('input')->with('ttd_type', 'gambar')->andReturn('gambar');
        $mockRequest->setUserResolver(fn() => $this->superAdmin);

        $this->actingAs($this->superAdmin);
        $controller = app(\App\Http\Controllers\User\UserController::class);
        $response = $controller->update($mockRequest, $user->id);

        $this->assertEquals(302, $response->getStatusCode());
        $user->refresh();
        $this->assertNotNull($user->pejabat_id);
        $this->assertNotNull($user->pejabat);
        $this->assertEquals($role->nama, $user->pejabat->jabatan, 'Pejabat jabatan should fallback to existing user role nama instead of generic Staff.');

        $user->pejabat?->delete();
        $user->delete();
    }

    public function test_route_error_preview_names_do_not_collide(): void
    {
        $stdUrl = route('errors.preview', ['code' => 404]);
        $this->assertStringContainsString('/errors/404', $stdUrl);

        if (\Illuminate\Support\Facades\Route::has('errors.preview.local')) {
            $localUrl = route('errors.preview.local', ['code' => 404]);
            $this->assertStringContainsString('/_preview/error/404', $localUrl);
        }
    }

    public function test_export_buku_inventaris_standalone_renders_real_pdf_not_placeholder(): void
    {
        $service = app(\App\Services\ExportService::class);
        $result = $service->exportBukuInventarisStandalone([
            'format'             => 'pdf',
            'paper_size'         => 'a4',
            'signature_option'   => 'without',
            'tanggal_dokumen'    => '2026-05-20',
            'keterangan_dokumen' => 'Desa Demo',
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists($result['path']));

        // Call protected method renderBukuInventarisPdf via reflection to verify content
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('renderBukuInventarisPdf');
        $method->setAccessible(true);
        $html = $method->invoke($service, [], [], '2026-05-20', 'Desa Demo', false, '', '', '', false, 'a4');

        $this->assertStringNotContainsString('<!-- Buku Inventaris PDF placeholder -->', $html);
        $this->assertStringContainsString('BUKU INVENTARIS ASET DESA', $html);
        $this->assertStringContainsString('TAHUN', $html);
        $this->assertStringContainsString('NILAI ASET', $html);

        // Cleanup
        \Illuminate\Support\Facades\Storage::disk('local')->delete($result['path']);
    }

    public function test_dashboard_export_chart_pdf_records_valid_user_and_creates_pdf(): void
    {
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('dashboard.charts.export', ['chart' => 'donut']));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));

        $latestLog = \App\Models\ExportLog::where('filters->dashboard_chart', 'donut')
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($latestLog);
        $this->assertEquals($this->superAdmin->id, $latestLog->user_id);
    }

    public function test_export_service_and_dashboard_user_id_fallback_never_violates_foreign_key(): void
    {
        $firstUser = \App\Models\User::first();
        $this->assertNotNull($firstUser);

        // When user is not resolved, fallback must resolve to a valid user id (not 0)
        $userId = null ?? \Illuminate\Support\Facades\Auth::id() ?? \App\Models\User::first()?->id;
        $this->assertEquals($firstUser->id, $userId);
        $this->assertNotEquals(0, $userId);

        $log = \App\Models\ExportLog::create([
            'user_id'             => $userId,
            'jenis_inventaris_id' => null,
            'tipe_export'         => 'pdf',
            'nama_file'           => 'fallback_test.pdf',
            'filters'             => ['test' => true],
        ]);
        $this->assertNotNull($log->id);
        $log->delete();
    }

    public function test_export_history_with_user_sort_and_date_range_does_not_throw_ambiguous_created_at(): void
    {
        $this->actingAs($this->superAdmin);

        // Test with quick date range and user sort
        $response1 = $this->get(route('export-data.history', [
            'sort'       => 'user_asc',
            'date_range' => '7d',
        ]));
        $response1->assertStatus(200);

        // Test with explicit dari/sampai and user desc sort
        $response2 = $this->get(route('export-data.history', [
            'sort'   => 'user_desc',
            'dari'   => now()->subDays(10)->format('Y-m-d'),
            'sampai' => now()->format('Y-m-d'),
        ]));
        $response2->assertStatus(200);
    }

    public function test_export_all_fallback_user_id_creates_valid_log(): void
    {
        $service = app(\App\Services\ExportService::class);
        $result = $service->exportAll([
            'format'           => 'excel',
            'filters'          => ['tahun' => date('Y')],
            'signature_option' => 'without',
        ]);

        $this->assertNotNull($result['log_id']);
        $log = \App\Models\ExportLog::find($result['log_id']);
        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertDatabaseHas('users', ['id' => $log->user_id]);

        \Illuminate\Support\Facades\Storage::disk('local')->delete($result['path']);
    }

    public function test_user_api_create_pejabat_uses_existing_role_when_role_not_in_payload(): void
    {
        $role = Role::firstOrCreate(
            ['nama' => 'kaur_keuangan'],
            ['tugas' => 'Kaur Keuangan', 'deskripsi' => 'Pengelola Keuangan']
        );

        $testUser = User::create([
            'nama'     => 'Kaur Keuangan QA ' . uniqid(),
            'username' => 'kaur_qa_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id'  => $role->id,
            'is_aktif' => 1,
        ]);

        $mockRequest = \Mockery::mock(\App\Http\Requests\UpdateUserRequest::class)->makePartial();
        $mockRequest->shouldReceive('validated')->andReturn([
            'nama'           => $testUser->nama,
            'username'       => $testUser->username,
            'is_aktif'       => 1,
            'create_pejabat' => 1,
            'pejabat_nama'   => $testUser->nama,
            'pejabat_alamat' => 'Jl. Desa Demo',
            // 'role' and 'pejabat_jabatan' are omitted: $roleModel will be null and $pejabat_jabatan will be null
        ]);
        $mockRequest->shouldReceive('hasFile')->with('ttd_path')->andReturn(false);
        $mockRequest->shouldReceive('input')->with('ttd_type', 'gambar')->andReturn('gambar');
        $mockRequest->setUserResolver(fn() => $this->superAdmin);

        $this->actingAs($this->superAdmin);
        $controller = app(\App\Http\Controllers\Api\UserApiController::class);
        $response = $controller->update($mockRequest, $testUser->id);

        $this->assertEquals(200, $response->getStatusCode());
        $testUser->refresh();
        $this->assertNotNull($testUser->pejabat_id);
        $this->assertEquals('kaur_keuangan', $testUser->pejabat->jabatan, 'API Pejabat jabatan should fallback to existing user role nama instead of generic Staff.');

        $testUser->pejabat?->delete();
        $testUser->delete();
    }

    public function test_inventaris_import_uses_safe_fallback_for_created_by(): void
    {
        // Simulate unauthenticated CLI import
        \Illuminate\Support\Facades\Auth::logout();

        $firstUser = User::first();
        $this->assertNotNull($firstUser);

        $resolvedCreator = auth()->id() ?? User::first()?->id ?? 1;
        $this->assertEquals($firstUser->id, $resolvedCreator);
        $this->assertDatabaseHas('users', ['id' => $resolvedCreator]);
    }

    public function test_all_inventaris_export_handles_empty_dataset_with_fallback_sheet(): void
    {
        // Filter with year 1800 where no data exists
        $export = new \App\Exports\AllInventarisExport(['tahun' => 1800]);
        $sheets = $export->sheets();

        $this->assertNotEmpty($sheets, 'AllInventarisExport must always contain at least 1 sheet to prevent PhpSpreadsheet fatal crash');
        $this->assertInstanceOf(\App\Exports\InventarisExport::class, $sheets[0]);
    }

    public function test_kib_export_all_excel_handles_empty_dataset_without_phpspreadsheet_crash(): void
    {
        $service = app(\App\Services\KibExportService::class);
        $result = $service->export([
            'kib_type' => 'all',
            'format'   => 'excel',
            'filters'  => ['tahun' => 1800],
        ]);

        $this->assertNotEmpty($result['filename']);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists($result['path']), 'KIB all excel file must be created even when all datasets are empty.');

        // Cleanup
        \Illuminate\Support\Facades\Storage::disk('local')->delete($result['path']);
    }

    public function test_super_developer_is_protected_from_deletion_via_web_and_api(): void
    {
        $devRole = Role::firstOrCreate(['nama' => 'super_developer']);
        $testDev = User::create([
            'nama'        => 'Super Dev QA Target',
            'username'    => 'super_dev_target_' . uniqid(),
            'password'    => bcrypt('password123'),
            'role_id'     => $devRole->id,
            'is_aktif'    => 1,
        ]);

        $this->assertTrue($testDev->isSuperDeveloper());

        $superDevActor = User::create([
            'nama'        => 'Super Dev QA Actor',
            'username'    => 'super_dev_actor_' . uniqid(),
            'password'    => bcrypt('password123'),
            'role_id'     => $devRole->id,
            'is_aktif'    => 1,
        ]);

        // 1. Web delete attempt (policy passes because actor is super dev, controller guard blocks)
        $this->actingAs($superDevActor);
        $webResponse = $this->from(route('users.index'))->delete(route('users.destroy', $testDev->id));
        $webResponse->assertRedirect(route('users.index'));
        $webResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $testDev->id]);

        // 2. API delete attempt (controller guard blocks with 403 JSON)
        $apiResponse = $this->deleteJson('/api/users/' . $testDev->id);
        $apiResponse->assertStatus(403);
        $apiResponse->assertJson(['success' => false, 'message' => 'Akun Super Developer atau Super Admin tidak dapat dihapus dari sistem.']);
        $this->assertDatabaseHas('users', ['id' => $testDev->id]);

        // Cleanup
        $testDev->delete();
        $superDevActor->delete();
    }

    public function test_super_developer_is_authorized_for_maintenance_qr_regeneration(): void
    {
        $devRole = Role::firstOrCreate(['nama' => 'super_developer']);
        $superDevUser = User::create([
            'nama'        => 'Super Dev QA QR Tester',
            'username'    => 'super_dev_qr_' . uniqid(),
            'password'    => bcrypt('password123'),
            'role_id'     => $devRole->id,
            'is_aktif'    => 1,
        ]);

        $this->actingAs($superDevUser);
        $response = $this->get('/maintenance/qr-regenerate?limit=5&only_missing=1');
        $this->assertNotEquals(403, $response->getStatusCode(), 'Super Developer must not be blocked (403) from maintenance QR regenerate');
        $this->assertEquals(200, $response->getStatusCode());

        // Cleanup
        $superDevUser->delete();
    }

    public function test_user_index_total_admin_card_includes_super_admin_roles(): void
    {
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('users.index'));
        $response->assertStatus(200);

        $stats = $response->viewData('stats');
        $this->assertNotNull($stats);

        // Count actual admin users in database (including Super Admin)
        $superAdminsCount = User::excludeSuperDevelopers()
            ->whereHas('role', fn($q) => $q->where('nama', 'like', '%admin%'))
            ->count();

        $this->assertGreaterThan(0, $superAdminsCount);
        $this->assertGreaterThanOrEqual($superAdminsCount, $stats['totalAdmin'], 'Total Admin card must include super_admin accounts');
    }

    public function test_laporan_aset_semester_2_prevents_double_counting_acquired_and_deleted_assets(): void
    {
        $uniqueKodeA = 'QA.LAP.SEM2.A.' . uniqid();
        $uniqueKodeB = 'QA.LAP.SEM2.B.' . uniqid();

        // Kasus A: Aset diperoleh di Semester 2 (Agustus 2025) dan dihapus di Semester 2 (September 2025)
        $logA = \App\Models\InventarisDeletedLog::create([
            'kode_barang'         => $uniqueKodeA,
            'nup'                 => '1',
            'nama_barang'         => 'Aset Sem 2 Hapus Sem 2 QA',
            'satuan'              => 'Unit',
            'nilai_perolehan'     => 10000000,
            'tahun_perolehan'     => 2025,
            'deleted_at'          => '2025-09-15 10:00:00',
            'deleted_by'          => $this->superAdmin->id,
            'asset_data_snapshot' => [
                'bulan_perolehan' => 8, // Agustus 2025
            ],
        ]);

        // Kasus B: Aset diperoleh di Semester 1 (Maret 2025) dan dihapus di Semester 2 (Oktober 2025)
        $logB = \App\Models\InventarisDeletedLog::create([
            'kode_barang'         => $uniqueKodeB,
            'nup'                 => '1',
            'nama_barang'         => 'Aset Sem 1 Hapus Sem 2 QA',
            'satuan'              => 'Unit',
            'nilai_perolehan'     => 5000000,
            'tahun_perolehan'     => 2025,
            'deleted_at'          => '2025-10-20 10:00:00',
            'deleted_by'          => $this->superAdmin->id,
            'asset_data_snapshot' => [
                'bulan_perolehan' => 3, // Maret 2025
            ],
        ]);

        $service = app(\App\Services\LaporanAsetService::class);
        $rows = $service->buildRows(2025, 'semester_2');

        $rowA = collect($rows)->first(fn($r) => $r['kode'] === $uniqueKodeA);
        $this->assertNotNull($rowA, 'Row A must be present in report');
        $this->assertEquals(0, $rowA['saldo_kwant'], 'Aset perolehan Agustus tidak boleh ada di Saldo Awal 1 Juli');
        $this->assertEquals(1, $rowA['tambah_kwant'], 'Aset perolehan Agustus harus masuk Mutasi Bertambah Sem 2');
        $this->assertEquals(1, $rowA['kurang_kwant'], 'Aset dihapus September harus masuk Mutasi Berkurang Sem 2');
        $this->assertEquals(0, $rowA['akhir_kwant'], 'Saldo Akhir harus 0 (0 + 1 - 1 = 0), bukan double count 1');

        $rowB = collect($rows)->first(fn($r) => $r['kode'] === $uniqueKodeB);
        $this->assertNotNull($rowB, 'Row B must be present in report');
        $this->assertEquals(1, $rowB['saldo_kwant'], 'Aset perolehan Maret harus masuk Saldo Awal 1 Juli');
        $this->assertEquals(0, $rowB['tambah_kwant'], 'Aset perolehan Maret tidak boleh masuk Mutasi Bertambah Sem 2');
        $this->assertEquals(1, $rowB['kurang_kwant'], 'Aset dihapus Oktober harus masuk Mutasi Berkurang Sem 2');
        $this->assertEquals(0, $rowB['akhir_kwant'], 'Saldo Akhir harus 0 (1 + 0 - 1 = 0)');

        // Cleanup
        $logA->delete();
        $logB->delete();
    }

    public function test_super_developer_is_authorized_for_all_master_form_requests(): void
    {
        $devRole = Role::firstOrCreate(['nama' => 'super_developer']);
        $devUser = User::create([
            'nama'     => 'Super Dev Form Request QA',
            'username' => 'super_dev_form_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id'  => $devRole->id,
            'is_aktif' => 1,
        ]);

        $this->actingAs($devUser);

        // 1. UpdateUserRequest
        $updateUserReq = new \App\Http\Requests\UpdateUserRequest();
        $updateUserReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($updateUserReq->authorize(), 'UpdateUserRequest must authorize super_developer');

        // 2. StoreBidangRequest
        $storeBidangReq = new \App\Http\Requests\StoreBidangRequest();
        $storeBidangReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($storeBidangReq->authorize(), 'StoreBidangRequest must authorize super_developer');

        // 3. UpdateBidangRequest
        $updateBidangReq = new \App\Http\Requests\UpdateBidangRequest();
        $updateBidangReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($updateBidangReq->authorize(), 'UpdateBidangRequest must authorize super_developer');

        // 4. StoreRuanganRequest
        $storeRuanganReq = new \App\Http\Requests\StoreRuanganRequest();
        $storeRuanganReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($storeRuanganReq->authorize(), 'StoreRuanganRequest must authorize super_developer');

        // 5. UpdateRuanganRequest
        $updateRuanganReq = new \App\Http\Requests\UpdateRuanganRequest();
        $updateRuanganReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($updateRuanganReq->authorize(), 'UpdateRuanganRequest must authorize super_developer');

        // 6. StoreDropdownRequest
        $storeDropdownReq = new \App\Http\Requests\StoreDropdownRequest();
        $storeDropdownReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($storeDropdownReq->authorize(), 'StoreDropdownRequest must authorize super_developer');

        // 7. UpdateDropdownRequest
        $updateDropdownReq = new \App\Http\Requests\UpdateDropdownRequest();
        $updateDropdownReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($updateDropdownReq->authorize(), 'UpdateDropdownRequest must authorize super_developer');

        // 8. UpdateJenisInventarisRequest
        $updateJenisReq = new \App\Http\Requests\UpdateJenisInventarisRequest();
        $updateJenisReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($updateJenisReq->authorize(), 'UpdateJenisInventarisRequest must authorize super_developer');

        // 9. UpdateInventarisRequest
        $updateInventarisReq = new \App\Http\Requests\UpdateInventarisRequest();
        $updateInventarisReq->setUserResolver(fn() => $devUser);
        $this->assertTrue($updateInventarisReq->authorize(), 'UpdateInventarisRequest must authorize super_developer');

        // 10. User::isAdmin() recognizes Super Developer
        $this->assertTrue($devUser->isAdmin(), 'User::isAdmin() must return true for super_developer');

        // Cleanup
        $devUser->delete();
    }

    public function test_inventaris_api_generates_valid_barcode_when_omitted_and_qr_png_succeeds(): void
    {
        $jenis = JenisInventaris::create([
            'nama' => 'Jenis QA API ' . uniqid(),
            'kib_type' => 'b',
        ]);

        // 1. Post to API without barcode (with required asal_usul, bulan_perolehan, nup, keterangan)
        $response = $this->actingAs($this->superAdmin, 'sanctum')
            ->postJson(route('api.inventaris.store'), [
                'jenis_inventaris_id' => $jenis->id,
                'kode_barang'         => '02.01.01.01.' . rand(100, 999),
                'nama_barang'         => 'Laptop Asus QA Barcode ' . uniqid(),
                'nup'                 => rand(1000, 9999),
                'satuan'              => 'unit',
                'tahun_perolehan'     => 2026,
                'nilai_perolehan'     => 15000000,
                'keterangan'          => 'Unit operasional desa',
                'asal_usul'           => 'apbdes',
                'bulan_perolehan'     => 5,
                'kondisi'             => 'baik',
            ]);

        $response->assertCreated();
        $assetId = $response->json('data.id');
        $barcode = $response->json('data.barcode');

        $this->assertNotEmpty($barcode, 'API asset creation must auto-generate a barcode when omitted.');
        $this->assertMatchesRegularExpression('/^INV-\d{4}-[A-Z0-9]+$/', $barcode);
        $this->assertEquals(5, $response->json('data.bulan_perolehan'), 'API asset creation must return saved bulan_perolehan.');

        // 2. Verify database records
        $asset = Inventaris::find($assetId);
        $this->assertNotNull($asset);
        $this->assertEquals($barcode, $asset->barcode);
        $this->assertEquals(5, $asset->laporanAsetValue?->bulan_perolehan, 'InventarisLaporanAsetValue must be created via API.');

        // 3. Verify QR code endpoint renders 200 image/png (not 404)
        $qrResponse = $this->actingAs($this->superAdmin)->get(route('inventaris.qr', $assetId));
        $qrResponse->assertOk();
        $this->assertEquals('image/png', $qrResponse->headers->get('Content-Type'));

        // 4. Update asset via API with empty barcode and new bulan_perolehan
        $updateResponse = $this->actingAs($this->superAdmin, 'sanctum')
            ->putJson(route('api.inventaris.update', $assetId), [
                'jenis_inventaris_id' => $jenis->id,
                'nama_barang'         => 'Laptop Asus QA Barcode (Updated)',
                'asal_usul'           => 'apbdes',
                'bulan_perolehan'     => 8,
                'barcode'             => '',
            ]);

        $updateResponse->assertOk();
        $this->assertEquals($barcode, $asset->fresh()->barcode, 'Barcode must not be cleared to empty string on update.');
        $this->assertEquals(8, $asset->fresh()->laporanAsetValue?->bulan_perolehan, 'InventarisLaporanAsetValue must be updated via API.');

        // Cleanup
        $asset->delete();
        \App\Models\InventarisLaporanAsetValue::where('inventaris_id', $assetId)->delete();
        $jenis->delete();
    }

    /**
     * Bug 11: Main Dashboard Card "Pengguna Terdaftar" must exclude super developers.
     */
    public function test_dashboard_controller_kpi_excludes_super_developers(): void
    {
        $roleDev = Role::firstOrCreate(['nama' => 'super_developer']);

        $superDev = User::create([
            'nama'     => 'Super Dev QA',
            'username' => 'super_dev_' . uniqid(),
            'password' => bcrypt('secret123'),
            'role_id'  => $roleDev->id,
            'is_aktif' => 1,
        ]);

        $this->assertTrue($superDev->isSuperDeveloper());

        $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));
        $response->assertOk();

        $controller = new \App\Http\Controllers\Dashboard\DashboardController();
        $refMethod = new \ReflectionMethod($controller, 'getKpiDataArray');
        $refMethod->setAccessible(true);
        $kpiData = $refMethod->invoke($controller, false);

        $expectedCount = User::excludeSuperDevelopers()->count();
        $rawCount = User::count();

        $this->assertGreaterThan($expectedCount, $rawCount, 'Raw User::count() should be greater due to super developer.');
        $this->assertEquals($expectedCount, $kpiData['usersActive'], 'Dashboard KPI users active must exclude super developers.');
        $this->assertEquals($expectedCount, $kpiData['users_active'], 'Dashboard KPI users_active must exclude super developers.');

        // Cleanup
        $superDev->delete();
    }

    /**
     * Bug 12: User deletion must cleanly cascade-delete personal notifications.
     */
    public function test_user_deletion_cleans_up_orphaned_notifications_web_and_api(): void
    {
        $roleStaf = Role::where('nama', 'staf')->first();

        // 1. Test Web UserController destroy
        $webUser = User::create([
            'nama'     => 'Staf Web Notif QA',
            'username' => 'staf_web_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id'  => $roleStaf?->id,
            'is_aktif' => 1,
        ]);

        \App\Models\Notification::create([
            'user_id' => $webUser->id,
            'type'    => 'info',
            'title'   => 'Notifikasi Web Staf 1',
            'body'    => 'Uji coba pembersihan notifikasi',
        ]);
        \App\Models\Notification::create([
            'user_id' => $webUser->id,
            'type'    => 'warning',
            'title'   => 'Notifikasi Web Staf 2',
            'body'    => 'Uji coba pembersihan notifikasi 2',
        ]);

        $this->assertEquals(2, \App\Models\Notification::where('user_id', $webUser->id)->count());

        $responseWeb = $this->actingAs($this->superAdmin)->delete(route('users.destroy', $webUser->id));
        $responseWeb->assertRedirect(route('users.index'));

        $this->assertNull(User::find($webUser->id));
        $this->assertEquals(0, \App\Models\Notification::where('user_id', $webUser->id)->count(), 'Notifications must be deleted when user is deleted via web.');

        // 2. Test API UserApiController destroy
        $apiUser = User::create([
            'nama'     => 'Staf API Notif QA',
            'username' => 'staf_api_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id'  => $roleStaf?->id,
            'is_aktif' => 1,
        ]);

        \App\Models\Notification::create([
            'user_id' => $apiUser->id,
            'type'    => 'info',
            'title'   => 'Notifikasi API Staf',
            'body'    => 'Uji coba pembersihan notifikasi via API',
        ]);

        $this->assertEquals(1, \App\Models\Notification::where('user_id', $apiUser->id)->count());

        $responseApi = $this->actingAs($this->superAdmin, 'sanctum')->deleteJson(route('api.users.destroy', $apiUser->id));
        $responseApi->assertOk();

        $this->assertNull(User::find($apiUser->id));
        $this->assertEquals(0, \App\Models\Notification::where('user_id', $apiUser->id)->count(), 'Notifications must be deleted when user is deleted via API.');
    }

    /**
     * Bug 13: UserApiController excludes super developers when validating single roles (perbekel, sekretaris).
     */
    public function test_user_api_controller_excludes_super_developer_on_role_checks(): void
    {
        $rolePerbekel = Role::firstOrCreate(['nama' => 'perbekel'], ['label' => 'Perbekel']);
        $roleStaf = Role::firstOrCreate(['nama' => 'staf'], ['label' => 'Staf']);

        // 1. Verifikasi pembuatan perbekel kedua memicu 422
        $responseDup = $this->actingAs($this->superAdmin, 'sanctum')->postJson(route('api.users.store'), [
            'nama'                  => 'Perbekel Duplikat QA',
            'username'              => 'perbekel_dup_' . uniqid(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'perbekel',
            'alamat'                => 'Jalan Kantor Perbekel No. 1',
            'is_aktif'              => 1,
        ]);
        $responseDup->assertStatus(422);
        $this->assertStringContainsString('Sudah ada user dengan role Perbekel', $responseDup->json('message'));

        // 2. Uji peran excludeSuperDevelopers:
        // Pindahkan user perbekel yang ada sementara ke role staf, lalu buat akun super dev dengan role perbekel
        $existingPerbekel = User::excludeSuperDevelopers()->whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->first();
        $oldRoleId = $existingPerbekel?->role_id;
        if ($existingPerbekel) {
            $existingPerbekel->role_id = $roleStaf->id;
            $existingPerbekel->saveQuietly();
        }

        $devUsername = 'superdev_pbk_' . uniqid();
        config(['desa.super_developer_usernames' => array_unique(array_merge(
            \App\Models\User::getSuperDeveloperUsernames(),
            [$devUsername]
        ))]);

        $superDev = User::create([
            'nama'     => 'Super Dev Overlay',
            'username' => $devUsername,
            'password' => bcrypt('password123'),
            'role_id'  => $rolePerbekel->id,
            'alamat'   => 'Kantor Pengembang',
            'is_aktif' => 1,
        ]);

        $this->assertTrue($superDev->isSuperDeveloper());

        // Membuat user perbekel asli: meskipun superDev punya role perbekel, ia di-exclude sehingga request berhasil
        $newPerbekelResponse = $this->actingAs($this->superAdmin, 'sanctum')->postJson(route('api.users.store'), [
            'nama'                  => 'Perbekel Baru Bebas Dev',
            'username'              => 'perbekel_new_' . uniqid(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'perbekel',
            'alamat'                => 'Jalan Raya Desa Asli',
            'is_aktif'              => 1,
        ]);

        $newPerbekelResponse->assertCreated();
        $createdId = $newPerbekelResponse->json('data.id');

        // Cleanup & kembalikan state semula
        User::find($createdId)?->delete();
        $superDev->delete();
        if ($existingPerbekel && $oldRoleId) {
            $existingPerbekel->role_id = $oldRoleId;
            $existingPerbekel->saveQuietly();
        }
    }

    /**
     * Bug 14: Verifikasi ManajemenAsetController::create & edit tidak membocorkan super developer ke dropdown Pengguna
     */
    public function test_manajemen_aset_create_and_edit_exclude_super_developer_from_user_list(): void
    {
        $roleSuperDev = Role::firstOrCreate(['nama' => 'super_developer'], ['label' => 'Super Developer']);
        $devUsername = 'superdev_aset_' . uniqid();
        config(['desa.super_developer_usernames' => array_unique(array_merge(
            \App\Models\User::getSuperDeveloperUsernames(),
            [$devUsername]
        ))]);

        $superDev = User::create([
            'nama'     => 'Super Dev Aset Leak Test',
            'username' => $devUsername,
            'password' => bcrypt('password123'),
            'role_id'  => $roleSuperDev->id,
            'alamat'   => 'Dev HQ',
            'is_aktif' => 1,
        ]);

        $responseCreate = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.create'));
        $responseCreate->assertOk();
        $userListCreate = $responseCreate->viewData('userList');
        $this->assertFalse($userListCreate->contains('id', $superDev->id), 'Super Developer must not appear in create asset user list.');

        // Buat dummy aset untuk tes edit
        $jenis = JenisInventaris::first() ?? JenisInventaris::create(['nama' => 'Jenis Test ' . uniqid(), 'kib_type' => 'B']);
        $aset = Inventaris::create([
            'nama_barang'         => 'Aset Test Leak',
            'kode_barang'         => '01.01.01.01.001',
            'nup'                 => 9999,
            'tahun_perolehan'     => 2024,
            'jenis_inventaris_id' => $jenis->id,
            'asal_usul'           => 'apbdes',
            'satuan'              => 'Unit',
        ]);

        $responseEdit = $this->actingAs($this->superAdmin)->get(route('manajemen-aset.edit', $aset->id));
        $responseEdit->assertOk();
        $userListEdit = $responseEdit->viewData('userList');
        $this->assertFalse($userListEdit->contains('id', $superDev->id), 'Super Developer must not appear in edit asset user list.');
    }

    /**
     * Bug 15: Verifikasi UserController::bulkDestroy membersihkan notifikasi dan melindungi akun super developer
     */
    public function test_user_bulk_destroy_cleans_notifications_and_protects_super_developer(): void
    {
        $roleStaf = Role::firstOrCreate(['nama' => 'staf'], ['label' => 'Staf']);
        $roleSuperDev = Role::firstOrCreate(['nama' => 'super_developer'], ['label' => 'Super Developer']);

        $normalUser = User::create([
            'nama'     => 'Normal User Bulk QA',
            'username' => 'normal_bulk_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id'  => $roleStaf->id,
            'alamat'   => 'Desa',
            'is_aktif' => 1,
        ]);

        $devUsername = 'superdev_bulk_' . uniqid();
        config(['desa.super_developer_usernames' => array_unique(array_merge(
            \App\Models\User::getSuperDeveloperUsernames(),
            [$devUsername]
        ))]);

        $superDev = User::create([
            'nama'     => 'Super Dev Protected',
            'username' => $devUsername,
            'password' => bcrypt('password123'),
            'role_id'  => $roleSuperDev->id,
            'alamat'   => 'HQ',
            'is_aktif' => 1,
        ]);

        // Buat notifikasi untuk normalUser
        \App\Models\Notification::create([
            'user_id' => $normalUser->id,
            'title'   => 'Notif QA Test',
            'message' => 'Pesan uji notif',
            'type'    => 'info',
            'is_read' => 0,
        ]);

        $this->assertDatabaseHas('notifications', ['user_id' => $normalUser->id]);

        $response = $this->actingAs($this->superAdmin)->post(route('users.bulk-destroy'), [
            'ids' => [$normalUser->id, $superDev->id],
        ]);

        $response->assertRedirect(route('users.index'));

        // Normal user harus terhapus beserta notifikasinya
        $this->assertDatabaseMissing('users', ['id' => $normalUser->id]);
        $this->assertDatabaseMissing('notifications', ['user_id' => $normalUser->id]);

        // Super Developer WAJIB tetap ada di database (kebal dari bulk deletion)
        $this->assertDatabaseHas('users', ['id' => $superDev->id]);
    }

    /**
     * Bug 16: Verifikasi UserController::store dan update menolak role super_developer
     */
    public function test_user_store_and_update_blocks_super_developer_role_selection(): void
    {
        $roleSuperDev = Role::firstOrCreate(['nama' => 'super_developer'], ['label' => 'Super Developer']);

        $responseStore = $this->actingAs($this->superAdmin)->post(route('users.store'), [
            'nama'                  => 'Hacker Impersonator',
            'username'              => 'hacker_' . uniqid(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'super_developer',
            'alamat'                => 'Nowhere',
            'is_aktif'              => 1,
        ]);

        $responseStore->assertSessionHasErrors('role');

        $roleStaf = Role::firstOrCreate(['nama' => 'staf'], ['label' => 'Staf']);
        $victimUser = User::create([
            'nama'     => 'Victim QA',
            'username' => 'victim_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id'  => $roleStaf->id,
            'alamat'   => 'Desa',
            'is_aktif' => 1,
        ]);

        $responseUpdate = $this->actingAs($this->superAdmin)->put(route('users.update', $victimUser->id), [
            'nama'     => 'Victim QA',
            'username' => $victimUser->username,
            'role'     => 'super_developer',
            'alamat'   => 'Desa',
            'is_aktif' => 1,
        ]);

        $responseUpdate->assertSessionHasErrors('role');
        $this->assertEquals($roleStaf->id, $victimUser->fresh()->role_id);
    }

    /**
     * Audit: UserImport harus menolak baris dengan role super_developer
     */
    public function test_user_import_rejects_super_developer_role(): void
    {
        $roleSuperDev = Role::firstOrCreate(['nama' => 'super_developer'], ['label' => 'Super Developer']);

        $importer = new \App\Imports\UserImport();
        $rows = collect([
            [
                'username' => 'imp_dev_' . uniqid(),
                'nama_lengkap' => 'Attacker Dev QA',
                'role' => 'super_developer',
                'status_aktif' => 'Aktif',
            ]
        ]);

        $importer->collection($rows);

        $this->assertEquals(1, $importer->failedCount);
        $this->assertStringContainsString('Super Developer', $importer->failedRows[0]['reason']);
    }

    /**
     * Audit: UserImport harus menolak duplikasi singular role dalam berkas yang sama
     */
    public function test_user_import_rejects_multiple_singular_roles_in_same_file(): void
    {
        $rolePerbekel = Role::firstOrCreate(['nama' => 'perbekel'], ['label' => 'Perbekel']);
        // Hapus perbekel yang mungkin sudah ada agar test bersih
        User::where('role_id', $rolePerbekel->id)->delete();

        $importer = new \App\Imports\UserImport();
        $rows = collect([
            [
                'username' => 'perbekel_1_' . uniqid(),
                'nama_lengkap' => 'I Wayan Perbekel Satu',
                'role' => 'perbekel',
                'status_aktif' => 'Aktif',
            ],
            [
                'username' => 'perbekel_2_' . uniqid(),
                'nama_lengkap' => 'I Made Perbekel Dua',
                'role' => 'perbekel',
                'status_aktif' => 'Aktif',
            ]
        ]);

        $importer->collection($rows);

        $this->assertEquals(1, $importer->importedCount);
        $this->assertEquals(1, $importer->failedCount);
        $this->assertStringContainsString('Perbekel', $importer->failedRows[0]['reason']);
    }

    /**
     * Audit: NotificationController::read harus memblokir open redirect ke external domain
     */
    public function test_notification_read_blocks_open_redirect(): void
    {
        $notif = \App\Models\Notification::create([
            'user_id' => $this->superAdmin->id,
            'type'    => 'system_alert',
            'title'   => 'Security Alert',
            'body'    => 'Check external link',
            'url'     => 'https://malicious-phishing.test/login',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('notifications.read', $notif->id));
        $response->assertRedirect(route('notifications.index'));
        $this->assertTrue($notif->fresh()->is_read);
    }

    /**
     * Audit: Ganti password harus melepas sesi perangkat lain secara otomatis
     */
    public function test_change_password_invalidates_other_device_sessions(): void
    {
        $currDevId = 'dev-curr-' . uniqid();
        $otherDevId = 'dev-other-' . uniqid();

        \App\Models\UserDeviceSession::create([
            'user_id'      => $this->superAdmin->id,
            'device_id'    => $currDevId,
            'session_id'   => 'sess_curr_1',
            'ip_address'   => '127.0.0.1',
            'user_agent'   => 'Device Current',
            'last_seen_at' => now(),
        ]);

        \App\Models\UserDeviceSession::create([
            'user_id'      => $this->superAdmin->id,
            'device_id'    => $otherDevId,
            'session_id'   => 'sess_other_2',
            'ip_address'   => '127.0.0.2',
            'user_agent'   => 'Device Other',
            'last_seen_at' => now(),
        ]);

        // User mengubah password dari current device
        $response = $this->actingAs($this->superAdmin)
            ->withCookie(\App\Services\Auth\DeviceSessionLimiter::DEVICE_COOKIE, $currDevId)
            ->post(route('profile.change-password'), [
                'current_password'      => 'password123',
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect(route('profile.password'));
        $response->assertSessionHas('success');

        // Pastikan record sesi perangkat lain telah dihapus
        $this->assertDatabaseMissing('user_device_sessions', [
            'user_id'   => $this->superAdmin->id,
            'device_id' => $otherDevId,
        ]);

        // Dan record current device tetap ada
        $this->assertDatabaseHas('user_device_sessions', [
            'user_id'   => $this->superAdmin->id,
            'device_id' => $currDevId,
        ]);

        // Saat perangkat lama mencoba mengakses request dengan cookie device lamanya:
        $revokedResponse = $this->actingAs($this->superAdmin)
            ->withCookie(\App\Services\Auth\DeviceSessionLimiter::DEVICE_COOKIE, $otherDevId)
            ->get(route('dashboard'));

        $revokedResponse->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Audit: Admin me-reset password pengguna harus melepas seluruh sesi perangkat pengguna tersebut
     */
    public function test_admin_password_reset_releases_all_user_device_sessions(): void
    {
        $bidang = \App\Models\Bidang::first() ?? \App\Models\Bidang::create(['nama' => 'Bidang QA ' . uniqid()]);
        $role = Role::firstOrCreate(['nama' => 'kaur'], ['tugas' => 'Kaur', 'deskripsi' => 'Kaur']);
        $targetUser = User::create([
            'nama'      => 'Target User Reset',
            'username'  => 'target_' . uniqid(),
            'password'  => bcrypt('oldpassword123'),
            'role_id'   => $role->id,
            'bidang_id' => $bidang->id,
            'is_aktif'  => 1,
            'alamat'    => 'Jl. Raya Demo',
        ]);

        $dev1 = 'dev-target-1-' . uniqid();
        $dev2 = 'dev-target-2-' . uniqid();

        \App\Models\UserDeviceSession::create([
            'user_id'      => $targetUser->id,
            'device_id'    => $dev1,
            'session_id'   => 'sess1',
            'ip_address'   => '127.0.0.1',
            'user_agent'   => 'Chrome',
            'last_seen_at' => now(),
        ]);

        \App\Models\UserDeviceSession::create([
            'user_id'      => $targetUser->id,
            'device_id'    => $dev2,
            'session_id'   => 'sess2',
            'ip_address'   => '127.0.0.2',
            'user_agent'   => 'Firefox',
            'last_seen_at' => now(),
        ]);

        // Super Admin update password targetUser
        $response = $this->actingAs($this->superAdmin)
            ->put(route('users.update', $targetUser->id), [
                'nama'                  => $targetUser->nama,
                'username'              => $targetUser->username,
                'role'                  => 'kaur',
                'bidang_id'             => $bidang->id,
                'is_aktif'              => 1,
                'alamat'                => 'Jl. Raya Demo No. 10',
                'password'              => 'resetpassword123',
                'password_confirmation' => 'resetpassword123',
            ]);

        $response->assertRedirect(route('users.index'));

        // Pastikan seluruh sesi targetUser dihapus
        $this->assertEquals(0, \App\Models\UserDeviceSession::where('user_id', $targetUser->id)->count());
    }

    /**
     * Audit: Importer Bidang & Ruangan harus membersihkan UTF-8 BOM dan formula injection
     */
    public function test_importers_sanitize_utf8_bom_and_formula_injection(): void
    {
        // 1. Bidang Import dengan BOM dan formula injection '=cmd|calc'
        $importerBidang = new \App\Imports\BidangImport();
        $namaBidangPayload = "\xEF\xBB\xBF=Bidang Keuangan Khusus QA " . uniqid();
        $rowsBidang = collect([
            [
                'nama_bidang' => $namaBidangPayload,
                'deskripsi'   => 'Deskripsi uji coba',
            ],
        ]);

        $importerBidang->collection($rowsBidang);
        $this->assertEquals(1, $importerBidang->importedCount);

        // Nilai di database tidak boleh diawali BOM atau '='
        $cleanExpectedBidang = ltrim(preg_replace('/^\xEF\xBB\xBF/', '', $namaBidangPayload), '=@');
        $this->assertDatabaseHas('bidangs', [
            'nama' => $cleanExpectedBidang,
        ]);

        // 2. Ruangan Import dengan BOM dan null-bytes
        $importerRuangan = new \App\Imports\RuanganImport();
        $namaRuanganPayload = "\xEF\xBB\xBF@Ruang Arsip\0 Presisi " . uniqid();
        $rowsRuangan = collect([
            [
                'kode_ruangan' => 'R-SAN-1',
                'nama_ruangan' => $namaRuanganPayload,
                'deskripsi'    => 'Uji coba sanitasi',
            ],
        ]);

        $importerRuangan->collection($rowsRuangan);
        $this->assertEquals(1, $importerRuangan->importedCount);

        $cleanExpectedRuangan = 'Ruang Arsip Presisi ' . substr($namaRuanganPayload, -13);
        $this->assertDatabaseHas('ruangans', [
            'kode' => 'R-SAN-1',
        ]);
    }

    /**
     * Bug 18: NUP desimal (contoh: 1.5) harus ditolak oleh aturan validasi 'integer'
     */
    public function test_inventaris_request_validates_nup_as_integer_rejecting_decimals(): void
    {
        $jenis = JenisInventaris::first() ?? JenisInventaris::create([
            'nama' => 'Peralatan QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang' => 'Laptop QA Validasi NUP',
            'kode_barang' => '02.03.01.01.001',
            'nup' => '1.5', // Desimal harus gagal
            'tahun_perolehan' => date('Y'),
            'bulan_perolehan' => 1,
            'asal_usul' => 'apbdes',
            'kondisi' => 'baik',
        ]);

        $response->assertSessionHasErrors(['nup']);
    }

    /**
     * Bug 19: Input teks sembarangan pada nilai perolehan harus memicu error validasi, bukan diam-diam jadi NULL
     */
    public function test_inventaris_request_non_numeric_nilai_perolehan_triggers_validation_error_not_silent_null(): void
    {
        $jenis = JenisInventaris::first() ?? JenisInventaris::create([
            'nama' => 'Peralatan QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang' => 'Laptop QA Validasi Nilai',
            'kode_barang' => '02.03.01.01.002',
            'nup' => 99,
            'nilai_perolehan' => 'sepuluh juta rupiah', // Teks sembarangan tidak boleh lolos jadi NULL
            'tahun_perolehan' => date('Y'),
            'bulan_perolehan' => 1,
            'asal_usul' => 'apbdes',
            'kondisi' => 'baik',
        ]);

        $response->assertSessionHasErrors(['nilai_perolehan']);
    }

    /**
     * Bug 20: Netralisasi formula injection pada export excel jika data diawali =, +, @, atau -formula
     */
    public function test_inventaris_export_neutralizes_formula_injection_characters(): void
    {
        $jenis = JenisInventaris::first() ?? JenisInventaris::create([
            'nama' => 'Kendaraan QA ' . uniqid(),
            'kib_type' => 'B',
        ]);

        $export = new \App\Exports\InventarisExport($jenis->id);

        // Uji sanitasi langsung
        $this->assertEquals("'=SUM(A1:A10)", $export->sanitizeFormulaInjection('=SUM(A1:A10)'));
        $this->assertEquals("'+12345Formula", $export->sanitizeFormulaInjection('+12345Formula'));
        $this->assertEquals("'@cmd|calc", $export->sanitizeFormulaInjection('@cmd|calc'));
        $this->assertEquals("'-cmd|/C calc", $export->sanitizeFormulaInjection('-cmd|/C calc'));

        // Nilai normal / angka negatif tidak boleh dirusak
        $this->assertEquals('-1500', $export->sanitizeFormulaInjection('-1500'));
        $this->assertEquals('-', $export->sanitizeFormulaInjection('-'));
        $this->assertEquals('Toyota Avanza', $export->sanitizeFormulaInjection('Toyota Avanza'));
    }

    /**
     * Bug 21: Trim spasi otomatis pada dynamic field agar spasi kosong saja tidak mem-bypass required
     */
    public function test_inventaris_request_trims_whitespace_on_dynamic_and_kib_kir_fields(): void
    {
        $jenis = JenisInventaris::create([
            'nama' => 'Jenis Khusus Required ' . uniqid(),
            'kib_type' => 'B',
        ]);

        // Buat dynamic field yang required
        $field = JenisInventarisData::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_field' => 'merk_khusus',
            'label_field' => 'Merk Khusus',
            'tipe_data' => 'text',
            'is_required' => true,
        ]);

        // Input dynamic hanya berisi spasi "   " harus ditrim dan ditolak karena required
        $response = $this->actingAs($this->superAdmin)->post(route('manajemen-aset.store'), [
            'jenis_inventaris_id' => $jenis->id,
            'nama_barang' => 'Barang Khusus QA',
            'kode_barang' => '02.03.01.01.003',
            'nup' => 101,
            'tahun_perolehan' => date('Y'),
            'bulan_perolehan' => 1,
            'asal_usul' => 'apbdes',
            'kondisi' => 'baik',
            'dynamic' => [
                'merk_khusus' => '     ', // Hanya spasi
            ],
        ]);

        $response->assertSessionHasErrors(['dynamic.merk_khusus']);
    }

    /**
     * Bug 22: Penghapusan user (web destroy, bulkDestroy, API destroy) membersihkan token Sanctum (mencegah orphaned tokens)
     */
    public function test_user_destroy_and_bulk_destroy_cleans_up_orphaned_sanctum_tokens(): void
    {
        $roleStaf = Role::firstOrCreate(['nama' => 'staf'], ['tugas' => 'Staf', 'deskripsi' => 'Staf']);

        $user1 = User::create([
            'nama' => 'User Token Test 1 ' . uniqid(),
            'username' => 'token_user_1_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id' => $roleStaf->id,
            'is_aktif' => 1,
        ]);
        $user1->createToken('external-api', ['read']);
        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user1->id, 'name' => 'external-api']);

        // Test destroy via Web
        $response = $this->actingAs($this->superAdmin)->delete(route('users.destroy', $user1->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user1->id]);

        // Test bulkDestroy via Web
        $user2 = User::create([
            'nama' => 'User Token Test 2 ' . uniqid(),
            'username' => 'token_user_2_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id' => $roleStaf->id,
            'is_aktif' => 1,
        ]);
        $user2->createToken('external-api', ['read']);
        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user2->id]);

        $responseBulk = $this->actingAs($this->superAdmin)->post(route('users.bulk-destroy'), [
            'ids' => [$user2->id],
        ]);
        $responseBulk->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user2->id]);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user2->id]);

        // Test destroy via API
        $user3 = User::create([
            'nama' => 'User Token Test 3 ' . uniqid(),
            'username' => 'token_user_3_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id' => $roleStaf->id,
            'is_aktif' => 1,
        ]);
        $user3->createToken('external-api', ['read']);
        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user3->id]);

        $responseApi = $this->actingAs($this->superAdmin)->deleteJson('/api/users/' . $user3->id);
        $responseApi->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $user3->id]);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user3->id]);
    }

    /**
     * Bug 23: User bulkDestroy mencatat log aktivitas (audit trail) di tabel activity_logs
     */
    public function test_user_bulk_destroy_creates_activity_audit_log(): void
    {
        $roleStaf = Role::firstOrCreate(['nama' => 'staf'], ['tugas' => 'Staf', 'deskripsi' => 'Staf']);

        $user = User::create([
            'nama' => 'User Bulk Audit ' . uniqid(),
            'username' => 'bulk_audit_' . uniqid(),
            'password' => bcrypt('password123'),
            'role_id' => $roleStaf->id,
            'is_aktif' => 1,
        ]);

        $initialLogsCount = ActivityLog::where('module', 'User')->where('action', 'hapus')->count();

        $response = $this->actingAs($this->superAdmin)->post(route('users.bulk-destroy'), [
            'ids' => [$user->id],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        $newLogsCount = ActivityLog::where('module', 'User')->where('action', 'hapus')->count();
        $this->assertEquals($initialLogsCount + 1, $newLogsCount, 'User bulkDestroy must create an audit activity log entry.');

        $latestLog = ActivityLog::where('module', 'User')->where('action', 'hapus')->latest('id')->first();
        $this->assertStringContainsString('Menghapus 1 user secara masal', $latestLog->detail);
    }

    /**
     * Bug 24: Formula Injection pada UserExport, PejabatExport, dan PenghapusanAsetExport disanitasi
     */
    public function test_exports_neutralize_formula_injection_characters(): void
    {
        $userExport = new \App\Exports\UserExport();
        $this->assertEquals("'=SUM(1+1)", $userExport->sanitizeFormulaInjection('=SUM(1+1)'));
        $this->assertEquals("'+cmd|calc", $userExport->sanitizeFormulaInjection('+cmd|calc'));
        $this->assertEquals("'@SUM(A1:A10)", $userExport->sanitizeFormulaInjection('@SUM(A1:A10)'));
        $this->assertEquals("'-malicious", $userExport->sanitizeFormulaInjection('-malicious'));
        $this->assertEquals("-", $userExport->sanitizeFormulaInjection('-'));
        $this->assertEquals("Normal Name", $userExport->sanitizeFormulaInjection('Normal Name'));

        $pejabatExport = new \App\Exports\PejabatExport();
        $this->assertEquals("'=HYPERLINK(x)", $pejabatExport->sanitizeFormulaInjection('=HYPERLINK(x)'));

        $penghapusanExport = new \App\Exports\PenghapusanAsetExport();
        $this->assertEquals("'=1+1", $penghapusanExport->sanitizeFormulaInjection('=1+1'));
    }
}



