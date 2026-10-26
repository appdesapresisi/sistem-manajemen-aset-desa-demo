<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\ActivityLog;
use App\Models\Inventaris;
use App\Models\InventarisLaporanAsetValue;
use App\Models\JenisInventaris;
use App\Models\Role;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class InventarisImportTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $kaur;
    private JenisInventaris $jenis;
    private Ruangan $ruangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $roleSA = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'Super Admin', 'deskripsi' => 'Full access']);
        $roleKR = Role::firstOrCreate(['nama' => 'kaur'], ['tugas' => 'Kaur', 'deskripsi' => 'Operator']);

        $this->superAdmin = User::create([
            'nama'     => 'Super Admin Test ' . uniqid(),
            'username' => 'sa_' . uniqid(),
            'password' => bcrypt('password'),
            'role_id'  => $roleSA->id,
            'is_aktif' => 1,
        ]);

        $this->kaur = User::create([
            'nama'     => 'Kaur Test ' . uniqid(),
            'username' => 'kr_' . uniqid(),
            'password' => bcrypt('password'),
            'role_id'  => $roleKR->id,
            'is_aktif' => 1,
        ]);

        $this->jenis = JenisInventaris::create([
            'nama'      => 'Peralatan Kantor Test ' . uniqid(),
            'deskripsi' => 'Jenis untuk import testing',
            'kib_type'  => 'B',
        ]);

        $this->ruangan = Ruangan::firstOrCreate(
            ['kode' => 'R-TEST'],
            ['nama' => 'Ruang Uji Coba', 'deskripsi' => 'Ruangan khusus testing']
        );
    }

    private function createCsvFile(array $rows): UploadedFile
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'test_csv_') . '.csv';
        $handle = fopen($tempPath, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return new UploadedFile(
            $tempPath,
            'inventaris_import_test.csv',
            'text/csv',
            null,
            true
        );
    }

    private function createExcelFile(array $rows): UploadedFile
    {
        return $this->createCsvFile($rows);
    }

    public function test_superadmin_can_download_import_template(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('manajemen-aset.template.download'));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition', ''), 'Template_Import_Inventaris_Desa')
        );
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition', ''));
    }

    public function test_xlsx_file_is_rejected_by_validation(): void
    {
        $file = UploadedFile::fake()->create('inventaris.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($this->superAdmin)
            ->post(route('manajemen-aset.import'), [
                'file_excel' => $file,
            ], [
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept'           => 'application/json',
            ]);

        $response->assertStatus(422);
    }

    public function test_guest_cannot_download_import_template(): void
    {
        $response = $this->get(route('manajemen-aset.template.download'));
        $response->assertRedirect(route('login'));
    }

    public function test_superadmin_can_import_valid_excel_data(): void
    {
        $kode = '02.03.01.01.999';
        $file = $this->createExcelFile([
            [
                'Kode Barang *', 'Nama Barang *', 'NUP *', 'Jenis Inventaris *',
                'Tahun Perolehan *', 'Bulan Perolehan', 'Nilai Perolehan', 'Kondisi *',
                'Asal Usul *', 'Satuan', 'Ruangan', 'Pengguna', 'Bukti Kepemilikan', 'Keterangan'
            ],
            [
                $kode, 'Printer Multifungsi Laser', '1', $this->jenis->nama,
                '2023', '5', '4500000', 'Baik',
                'APBDes', 'Unit', 'R-TEST', 'Kaur Keuangan', 'Faktur No. 12/2023', 'Pengadaan unit'
            ],
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('manajemen-aset.import'), [
                'file_excel' => $file,
            ], [
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept'           => 'application/json',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'stats'   => [
                'total_rows'     => 1,
                'imported_count' => 1,
                'skipped_count'  => 0,
                'failed_count'   => 0,
            ],
        ]);

        $this->assertDatabaseHas('inventaris', [
            'kode_barang'         => $kode,
            'nup'                 => 1,
            'nama_barang'         => 'Printer Multifungsi Laser',
            'jenis_inventaris_id' => $this->jenis->id,
            'tahun_perolehan'     => 2023,
            'nilai_perolehan'     => 4500000.00,
            'kondisi'             => 'baik',
            'asal_usul'           => 'apbdes',
            'ruangan_id'          => $this->ruangan->id,
            'satuan'              => 'Unit',
        ]);

        $created = Inventaris::where('kode_barang', $kode)->where('nup', 1)->first();
        $this->assertNotNull($created);
        $this->assertNotEmpty($created->barcode);
        $this->assertStringStartsWith('INV-', $created->barcode);

        $this->assertDatabaseHas('inventaris_laporan_aset_values', [
            'inventaris_id'   => $created->id,
            'bulan_perolehan' => 5,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Inventaris',
            'action' => 'tambah',
        ]);
    }

    public function test_import_skips_duplicate_kode_and_nup_without_failing_other_rows(): void
    {
        $kode = '02.03.01.01.992';

        // Pre-create asset in DB (NUP 1)
        Inventaris::create([
            'jenis_inventaris_id' => $this->jenis->id,
            'nama_barang'         => 'Aset Sudah Ada',
            'kode_barang'         => $kode,
            'nup'                 => 1,
            'tahun_perolehan'     => 2022,
            'kondisi'             => 'baik',
            'asal_usul'           => 'apbdes',
            'barcode'             => 'INV-PRE-001',
            'created_by'          => $this->superAdmin->id,
        ]);

        // Excel file contains NUP 1 (duplicate) and NUP 2 (brand new)
        $file = $this->createExcelFile([
            [
                'Kode Barang *', 'Nama Barang *', 'NUP *', 'Jenis Inventaris *',
                'Tahun Perolehan *', 'Bulan Perolehan', 'Nilai Perolehan', 'Kondisi *',
                'Asal Usul *', 'Satuan', 'Ruangan', 'Pengguna', 'Bukti Kepemilikan', 'Keterangan'
            ],
            [
                $kode, 'Aset Coba Duplikat', '1', $this->jenis->nama,
                '2022', '3', '5000000', 'Baik',
                'APBDes', 'Unit', '', '', '', ''
            ],
            [
                $kode, 'Aset Baru NUP 2', '2', $this->jenis->nama,
                '2023', '4', '6000000', 'Baik',
                'APBDes', 'Unit', '', '', '', ''
            ],
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('manajemen-aset.import'), [
                'file_excel' => $file,
            ], [
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept'           => 'application/json',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'stats'   => [
                'total_rows'     => 2,
                'imported_count' => 1,
                'skipped_count'  => 1,
                'failed_count'   => 0,
            ],
        ]);

        $responseData = $response->json();
        $this->assertCount(1, $responseData['skipped_rows']);
        $this->assertEquals($kode, $responseData['skipped_rows'][0]['kode_barang']);
        $this->assertEquals(1, $responseData['skipped_rows'][0]['nup']);

        // NUP 2 should be in database
        $this->assertDatabaseHas('inventaris', [
            'kode_barang' => $kode,
            'nup'         => 2,
            'nama_barang' => 'Aset Baru NUP 2',
        ]);
    }

    public function test_import_detects_duplicate_within_same_file(): void
    {
        $kode = '02.03.01.01.993';

        $file = $this->createExcelFile([
            [
                'Kode Barang *', 'Nama Barang *', 'NUP *', 'Jenis Inventaris *',
                'Tahun Perolehan *', 'Bulan Perolehan', 'Nilai Perolehan', 'Kondisi *',
                'Asal Usul *', 'Satuan', 'Ruangan', 'Pengguna', 'Bukti Kepemilikan', 'Keterangan'
            ],
            [
                $kode, 'Baris Pertama', '1', $this->jenis->nama,
                '2023', '1', '2000000', 'Baik',
                'APBDes', 'Unit', '', '', '', ''
            ],
            [
                $kode, 'Baris Kedua Duplikat', '1', $this->jenis->nama,
                '2023', '1', '2000000', 'Baik',
                'APBDes', 'Unit', '', '', '', ''
            ],
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('manajemen-aset.import'), [
                'file_excel' => $file,
            ], [
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept'           => 'application/json',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'stats'   => [
                'total_rows'     => 2,
                'imported_count' => 1,
                'skipped_count'  => 1,
                'failed_count'   => 0,
            ],
        ]);
    }

    public function test_import_reports_invalid_rows_accurately(): void
    {
        $kodeValid = '02.03.01.01.994';
        $file = $this->createExcelFile([
            [
                'Kode Barang *', 'Nama Barang *', 'NUP *', 'Jenis Inventaris *',
                'Tahun Perolehan *', 'Bulan Perolehan', 'Nilai Perolehan', 'Kondisi *',
                'Asal Usul *', 'Satuan', 'Ruangan', 'Pengguna', 'Bukti Kepemilikan', 'Keterangan'
            ],
            // Row 1: Invalid (empty nama barang)
            [
                '02.03.01.01.995', '', '1', $this->jenis->nama,
                '2023', '1', '1000000', 'Baik',
                'APBDes', 'Unit', '', '', '', ''
            ],
            // Row 2: Valid
            [
                $kodeValid, 'Meja Rapat Kayu Jati', '1', $this->jenis->nama,
                '2023', '2', '3500000', 'Rusak Ringan',
                'Kekayaan Asli', 'Unit', '', '', '', ''
            ],
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('manajemen-aset.import'), [
                'file_excel' => $file,
            ], [
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept'           => 'application/json',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'stats'   => [
                'total_rows'     => 2,
                'imported_count' => 1,
                'skipped_count'  => 0,
                'failed_count'   => 1,
            ],
        ]);

        $responseData = $response->json();
        $this->assertCount(1, $responseData['failed_rows']);
        $this->assertStringContainsString('Nama Barang wajib diisi', $responseData['failed_rows'][0]['reason']);

        $this->assertDatabaseHas('inventaris', [
            'kode_barang' => $kodeValid,
            'nup'         => 1,
            'nama_barang' => 'Meja Rapat Kayu Jati',
            'kondisi'     => 'rusak_ringan',
            'asal_usul'   => 'kekayaan_asli',
        ]);
    }
}
