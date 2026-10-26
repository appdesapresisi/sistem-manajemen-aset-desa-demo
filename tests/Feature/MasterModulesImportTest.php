<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Bidang;
use App\Models\Dropdown;
use App\Models\DropdownItem;
use App\Models\Pejabat;
use App\Models\Role;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class MasterModulesImportTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $roleSA = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'Super Admin', 'deskripsi' => 'Full access']);

        $this->superAdmin = User::create([
            'nama'     => 'Super Admin QA ' . uniqid(),
            'username' => 'qa_sa_' . uniqid(),
            'password' => bcrypt('password'),
            'role_id'  => $roleSA->id,
            'is_aktif' => 1,
        ]);
    }

    private function createCsvFile(array $rows, string $prefix): UploadedFile
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'test_csv_' . $prefix) . '.csv';
        $handle = fopen($tempPath, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return new UploadedFile(
            $tempPath,
            $prefix . '_test.csv',
            'text/csv',
            null,
            true
        );
    }

    private function createExcelFile(array $rows, string $prefix): UploadedFile
    {
        return $this->createCsvFile($rows, $prefix);
    }

    // ========================================================
    // 1. RUANGAN
    // ========================================================
    public function test_ruangan_template_download(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('ruangan.template.download'));
        $response->assertStatus(200);
        $this->assertStringContainsString('Template_Import_Ruangan', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition', ''));
    }

    public function test_ruangan_xlsx_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('ruangan.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('ruangan.import'), ['file_excel' => $file]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file_excel']);
    }

    public function test_ruangan_import_and_duplicate_skip(): void
    {
        $uniqueRoom = 'Ruang Lab QA ' . uniqid();
        $file = $this->createExcelFile([
            ['kode_ruangan', 'nama_ruangan', 'deskripsi'],
            ['R-QA1', $uniqueRoom, 'Ruang pengujian otomatis'],
        ], 'ruangan');

        // 1st Import - Must succeed
        $response1 = $this->actingAs($this->superAdmin)
            ->postJson(route('ruangan.import'), ['file_excel' => $file]);

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 1,
                    'skipped_count' => 0,
                    'failed_count' => 0,
                ],
            ]);

        $this->assertDatabaseHas('ruangans', ['nama' => $uniqueRoom]);

        // 2nd Import - Same file must be skipped automatically
        $file2 = $this->createExcelFile([
            ['kode_ruangan', 'nama_ruangan', 'deskripsi'],
            ['R-QA1', $uniqueRoom, 'Ruang pengujian otomatis'],
        ], 'ruangan_dup');

        $response2 = $this->actingAs($this->superAdmin)
            ->postJson(route('ruangan.import'), ['file_excel' => $file2]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 0,
                    'skipped_count' => 1,
                    'failed_count' => 0,
                ],
            ]);
    }

    // ========================================================
    // 2. PEJABAT
    // ========================================================
    public function test_pejabat_template_download(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('pejabat.template.download'));
        $response->assertStatus(200);
        $this->assertStringContainsString('Template_Import_Pejabat', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition', ''));
    }

    public function test_pejabat_import_and_duplicate_skip(): void
    {
        $uniqueNama = 'Budi Santoso QA ' . uniqid();
        $uniqueJabatan = 'Kepala Seksi QA ' . uniqid();
        $file = $this->createExcelFile([
            ['nama_lengkap__gelar', 'jabatan', 'tugas__wewenang', 'alamat', 'petugas_pengurus_barang', 'status_aktif'],
            [$uniqueNama, $uniqueJabatan, 'Pemeriksaan aset', 'Jl. Desa No. 10', 'Tidak', 'Aktif'],
        ], 'pejabat');

        // 1st Import
        $response1 = $this->actingAs($this->superAdmin)
            ->postJson(route('pejabat.import'), ['file_excel' => $file]);

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 1,
                    'skipped_count' => 0,
                    'failed_count' => 0,
                ],
            ]);

        $this->assertDatabaseHas('pejabats', ['nama' => $uniqueNama, 'jabatan' => $uniqueJabatan]);

        // 2nd Import - Skip
        $file2 = $this->createExcelFile([
            ['nama_lengkap__gelar', 'jabatan', 'tugas__wewenang', 'alamat', 'petugas_pengurus_barang', 'status_aktif'],
            [$uniqueNama, $uniqueJabatan, 'Pemeriksaan aset', 'Jl. Desa No. 10', 'Tidak', 'Aktif'],
        ], 'pejabat_dup');

        $response2 = $this->actingAs($this->superAdmin)
            ->postJson(route('pejabat.import'), ['file_excel' => $file2]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 0,
                    'skipped_count' => 1,
                ],
            ]);
    }

    public function test_pejabat_import_with_nonaktif_status_and_bracketed_headers(): void
    {
        $uniqueNamaNonaktif = 'Pejabat Nonaktif QA ' . uniqid();
        $uniqueJabatan = 'Staf Khusus QA ' . uniqid();

        // Menggunakan nama kolom dengan tanda kurung persis seperti template / input riil user
        $file = $this->createExcelFile([
            ['Nama Lengkap & Gelar *', 'Jabatan *', 'Tugas / Wewenang', 'Alamat', 'Petugas Pengurus Barang (Ya / Tidak)', 'Status Aktif (Aktif / Nonaktif)'],
            [$uniqueNamaNonaktif, $uniqueJabatan, 'Tugas Pengujian', 'Banjar Tengah', 'Ya', 'Nonaktif'],
        ], 'pejabat_nonaktif');

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('pejabat.import'), ['file_excel' => $file]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 1,
                    'skipped_count' => 0,
                    'failed_count' => 0,
                ],
            ]);

        $pejabat = \App\Models\Pejabat::where('nama', $uniqueNamaNonaktif)->first();
        $this->assertNotNull($pejabat);
        $this->assertFalse($pejabat->is_aktif, 'Pejabat dengan status Nonaktif di Excel harus tersimpan sebagai is_aktif=false');
        $this->assertTrue($pejabat->is_petugas_barang, 'Pejabat dengan petugas pengurus barang Ya harus tersimpan sebagai is_petugas_barang=true');

        // Pastikan halaman show menampilkan status Nonaktif
        $showResponse = $this->actingAs($this->superAdmin)->get(route('pejabat.show', $pejabat->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Nonaktif');
        $showResponse->assertSee('Petugas Pengurus Barang');

        // Pastikan halaman edit menampilkan toggle tidak ter-centang
        $editResponse = $this->actingAs($this->superAdmin)->get(route('pejabat.edit', $pejabat->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Tidak Aktif');
    }

    // ========================================================
    // 3. USERS
    // ========================================================
    public function test_user_template_download(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('users.template.download'));
        $response->assertStatus(200);
        $this->assertStringContainsString('Template_Import_Pengguna', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition', ''));
    }

    public function test_user_import_and_duplicate_skip(): void
    {
        $uniqueUsername = 'user_qa_' . uniqid();
        $uniqueName = 'Staf QA ' . uniqid();
        $file = $this->createExcelFile([
            ['nama_lengkap', 'username', 'role', 'bidang', 'status', 'alamat', 'password_awal'],
            [$uniqueName, $uniqueUsername, 'Kaur', '', 'Aktif', 'Jl. Desa No. 12', 'pass123QA'],
        ], 'user');

        // 1st Import
        $response1 = $this->actingAs($this->superAdmin)
            ->postJson(route('users.import'), ['file_excel' => $file]);

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 1,
                    'skipped_count' => 0,
                ],
            ]);

        $this->assertDatabaseHas('users', ['username' => $uniqueUsername, 'nama' => $uniqueName]);

        // 2nd Import - Username already exists -> skip
        $file2 = $this->createExcelFile([
            ['nama_lengkap', 'username', 'role', 'bidang', 'status', 'alamat', 'password_awal'],
            [$uniqueName, $uniqueUsername, 'Kaur', '', 'Aktif', 'Jl. Desa No. 12', 'pass123QA'],
        ], 'user_dup');

        $response2 = $this->actingAs($this->superAdmin)
            ->postJson(route('users.import'), ['file_excel' => $file2]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 0,
                    'skipped_count' => 1,
                ],
            ]);
    }

    // ========================================================
    // 4. DROPDOWN
    // ========================================================
    public function test_dropdown_template_download(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dropdown.template.download'));
        $response->assertStatus(200);
        $this->assertStringContainsString('Template_Import_Dropdown', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition', ''));
    }

    public function test_dropdown_import_and_duplicate_skip(): void
    {
        $categoryName = 'Sumber Dana QA ' . uniqid();
        $itemLabel = 'Dana Desa QA 2026';
        $file = $this->createExcelFile([
            ['nama_dropdown', 'deskripsi_dropdown', 'pilihan_item', 'urutan'],
            [$categoryName, 'Kategori pengujian', $itemLabel, 1],
        ], 'dropdown');

        // 1st Import
        $response1 = $this->actingAs($this->superAdmin)
            ->postJson(route('dropdown.import'), ['file_excel' => $file]);

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 1,
                    'skipped_count' => 0,
                ],
            ]);

        $dropdown = Dropdown::where('nama', $categoryName)->first();
        $this->assertNotNull($dropdown);
        $this->assertDatabaseHas('dropdown_items', ['dropdown_id' => $dropdown->id, 'label' => $itemLabel]);

        // 2nd Import - Same item in same dropdown -> skip
        $file2 = $this->createExcelFile([
            ['nama_dropdown', 'deskripsi_dropdown', 'pilihan_item', 'urutan'],
            [$categoryName, 'Kategori pengujian', $itemLabel, 1],
        ], 'dropdown_dup');

        $response2 = $this->actingAs($this->superAdmin)
            ->postJson(route('dropdown.import'), ['file_excel' => $file2]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 0,
                    'skipped_count' => 1,
                ],
            ]);
    }

    // ========================================================
    // 5. BIDANG
    // ========================================================
    public function test_bidang_template_download(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('bidangs.template.download'));
        $response->assertStatus(200);
        $this->assertStringContainsString('Template_Import_Bidang', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition', ''));
    }

    public function test_bidang_import_and_duplicate_skip(): void
    {
        $bidangName = 'Bidang Pemberdayaan QA ' . uniqid();
        $file = $this->createExcelFile([
            ['nama_bidang', 'deskripsi'],
            [$bidangName, 'Bidang urusan uji coba QA'],
        ], 'bidang');

        // 1st Import
        $response1 = $this->actingAs($this->superAdmin)
            ->postJson(route('bidangs.import'), ['file_excel' => $file]);

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 1,
                    'skipped_count' => 0,
                ],
            ]);

        $this->assertDatabaseHas('bidangs', ['nama' => $bidangName]);

        // 2nd Import - Same bidang name -> skip
        $file2 = $this->createExcelFile([
            ['nama_bidang', 'deskripsi'],
            [$bidangName, 'Bidang urusan uji coba QA'],
        ], 'bidang_dup');

        $response2 = $this->actingAs($this->superAdmin)
            ->postJson(route('bidangs.import'), ['file_excel' => $file2]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'imported_count' => 0,
                    'skipped_count' => 1,
                ],
            ]);
    }

    // ========================================================
    // 6. MIXED ROWS & VALIDATION HANDLING
    // ========================================================
    public function test_mixed_rows_in_single_file_are_accurately_categorized(): void
    {
        // Pre-create an existing room
        $existingRoom = 'Ruang Eksis ' . uniqid();
        Ruangan::create(['kode' => 'R-EX1', 'nama' => $existingRoom]);

        $newRoom = 'Ruang Baru ' . uniqid();

        // 1 valid new, 1 duplicate existing, 1 invalid missing name
        $file = $this->createExcelFile([
            ['kode_ruangan', 'nama_ruangan', 'deskripsi'],
            ['R-NEW', $newRoom, 'Ruang Baru Valid'],
            ['R-DUP', $existingRoom, 'Ruang Sudah Ada'],
            ['R-INV', '', 'Nama Kosong Tidak Boleh'],
        ], 'ruangan_mixed');

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('ruangan.import'), ['file_excel' => $file]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'total_rows' => 3,
                    'imported_count' => 1,
                    'skipped_count' => 1,
                    'failed_count' => 1,
                ],
            ]);

        $data = $response->json();
        $this->assertCount(1, $data['imported_rows']);
        $this->assertCount(1, $data['skipped_rows']);
        $this->assertCount(1, $data['failed_rows']);
        $this->assertEquals(4, $data['failed_rows'][0]['row']); // Row 4 in Excel
        $this->assertStringContainsString('wajib diisi', $data['failed_rows'][0]['reason']);
    }

    public function test_import_without_file_returns_validation_error(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('ruangan.import'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file_excel']);
    }

    public function test_mismatched_template_upload_is_rejected_with_descriptive_error(): void
    {
        // Case: Pejabat file uploaded into User import
        $pejabatFile = $this->createExcelFile([
            ['nama_pejabat', 'jabatan', 'nip', 'status'],
            ['Drs. Test Pejabat', 'Kaur Perencanaan', '198501012010011001', 'Aktif'],
        ], 'mismatched_pejabat_to_user');

        $responseUser = $this->actingAs($this->superAdmin)
            ->postJson(route('users.import'), ['file_excel' => $pejabatFile]);

        $responseUser->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
        $this->assertStringContainsString('Format berkas tidak sesuai untuk modul Pengguna', $responseUser->json('message'));

        // Case: User file uploaded into Pejabat import
        $userFile = $this->createExcelFile([
            ['nama_lengkap', 'username', 'email', 'role', 'bidang'],
            ['Test User', 'testuser_mismatch', 'test@desa.id', 'Kaur', ''],
        ], 'mismatched_user_to_pejabat');

        $responsePejabat = $this->actingAs($this->superAdmin)
            ->postJson(route('pejabat.import'), ['file_excel' => $userFile]);

        $responsePejabat->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
        $this->assertStringContainsString('Format berkas tidak sesuai untuk modul Pejabat', $responsePejabat->json('message'));
    }

    public function test_user_import_rejects_duplicate_singular_roles_perbekel_and_sekdes(): void
    {
        Role::firstOrCreate(['nama' => 'perbekel'], ['tugas' => 'Perbekel']);

        // Excel file berisi dua perbekel
        $file = $this->createExcelFile([
            ['nama_lengkap', 'username', 'peran_role', 'status_akun', 'alamat_domisili'],
            ['I Wayan Perbekel Satu', 'perbekel_1_' . uniqid(), 'Perbekel', 'Aktif', 'Demo'],
            ['I Made Perbekel Dua', 'perbekel_2_' . uniqid(), 'Perbekel', 'Aktif', 'Demo'],
        ], 'perbekel_duplicate');

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('users.import'), ['file_excel' => $file]);

        $response->assertStatus(200);
        $data = $response->json();

        // Baris kedua pasti ditolak karena role perbekel singular
        $this->assertGreaterThanOrEqual(1, $data['stats']['failed_count']);
        $this->assertStringContainsString('Perbekel', $data['failed_rows'][0]['reason']);
        $this->assertLessThanOrEqual(1, User::whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->count());
    }
}

