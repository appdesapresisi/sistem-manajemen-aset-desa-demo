<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BukuInventarisAsetExportTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $role = Role::firstOrCreate(
            ['nama' => 'super_admin'],
            ['tugas' => 'Akses penuh', 'deskripsi' => 'Role untuk pengujian']
        );

        $this->user = User::create([
            'nama' => 'QA Buku Inventaris',
            'email' => 'qa-buku-inv-' . uniqid() . '@example.test',
            'password' => 'secret123',
            'role_id' => $role->id,
            'is_aktif' => 1,
        ]);

        JenisInventaris::firstOrCreate(
            ['nama' => 'Aset Uji QA'],
            ['keterangan' => 'Untuk test export']
        );
    }

    public function test_buku_inventaris_aset_pdf_export_with_legal_paper_succeeds(): void
    {
        $response = $this->actingAs($this->user)->post(route('export-data.store-buku-inventaris-aset'), [
            'tahun' => '2026',
            'periode' => 'tahunan',
            'format' => 'pdf',
            'paper_size' => 'legal',
            'signature_option' => 'with',
            'tanggal_dokumen' => '2026-09-05',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'filename', 'file_url']);

        $filename = (string) $response->json('filename');
        $this->assertStringEndsWith('.pdf', $filename);
    }

    public function test_buku_inventaris_aset_excel_export_succeeds(): void
    {
        $jenis = JenisInventaris::first();
        Inventaris::create([
            'kode_barang' => '1.01.03.04.006',
            'nama_barang' => 'Flashdisk Kingstone Test',
            'nup' => '1',
            'tahun_perolehan' => 2025,
            'nilai_perolehan' => 150000,
            'asal_usul' => 'apbdes',
            'kondisi' => 'baik',
            'jenis_inventaris_id' => $jenis->id,
        ]);

        Inventaris::create([
            'kode_barang' => '1.01.03.04.009',
            'nama_barang' => 'Hardisk Internal NAS Test',
            'nup' => '1',
            'tahun_perolehan' => 2025,
            'nilai_perolehan' => 2500000,
            'asal_usul' => 'apbdes',
            'kondisi' => 'baik',
            'jenis_inventaris_id' => $jenis->id,
        ]);

        $service = app(\App\Services\BukuInventarisAsetService::class);
        $rows = $service->buildRows(2025, 'tahunan');

        $export = new \App\Exports\BukuInventarisAsetExport($rows, 2025, 'tahunan', '2025-12-31', false);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Populate collection into worksheet
        $collection = $export->collection();
        $rowIdx = 1;
        foreach ($collection as $row) {
            $colIdx = 1;
            foreach ($row as $val) {
                $sheet->setCellValueExplicit(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++) . $rowIdx,
                    $val,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
            }
            $rowIdx++;
        }

        // Fire AfterSheet event
        $event = new \Maatwebsite\Excel\Events\AfterSheet(new \Maatwebsite\Excel\Sheet($sheet), $export);
        $events = $export->registerEvents();
        $events[\Maatwebsite\Excel\Events\AfterSheet::class]($event);

        // Assert Table Headers on Row 9
        $this->assertEquals('NO', $sheet->getCell('A9')->getValue());
        $this->assertEquals('KODE BARANG', $sheet->getCell('B9')->getValue());
        $this->assertEquals('NAMA BARANG', $sheet->getCell('C9')->getValue());
        $this->assertEquals('NUP', $sheet->getCell('D9')->getValue());

        // Assert Item #1 is strictly at Row 10 (NOT overwritten!)
        $this->assertEquals('1', $sheet->getCell('A10')->getValue());
        $this->assertEquals($rows[0]['kode'], $sheet->getCell('B10')->getValue());
        $this->assertEquals($rows[0]['nama_barang'], $sheet->getCell('C10')->getValue());

        // Assert Item #2 is at Row 11
        $this->assertEquals('2', $sheet->getCell('A11')->getValue());
        $this->assertEquals($rows[1]['kode'], $sheet->getCell('B11')->getValue());
        $this->assertEquals($rows[1]['nama_barang'], $sheet->getCell('C11')->getValue());
    }

    public function test_export_all_jenis_pdf_with_legal_paper_succeeds(): void
    {
        $response = $this->actingAs($this->user)->post(route('export-data.store-all'), [
            'tahun' => '2025',
            'format' => 'pdf',
            'paper_size' => 'legal',
            'signature_option' => 'without',
            'tanggal_dokumen' => '2026-09-05',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'filename', 'file_url']);

        $filename = (string) $response->json('filename');
        $this->assertStringEndsWith('.pdf', $filename);
    }

    public function test_resolve_bukti_kepemilikan_prioritizes_custom_field(): void
    {
        $service = app(\App\Services\BukuInventarisAsetService::class);
        $jenis = JenisInventaris::first();

        $invWithBukti = new Inventaris([
            'nama_barang' => 'Printer Test',
            'bukti_kepemilikan' => 'KWITANSI',
            'jenis_inventaris_id' => $jenis->id,
        ]);
        $invWithBukti->setRelation('jenis', $jenis);

        $this->assertEquals('KWITANSI', $service->resolveBuktiKepemilikan($invWithBukti));

        $invEmptyBukti = new Inventaris([
            'nama_barang' => 'Meja Test',
            'bukti_kepemilikan' => null,
            'jenis_inventaris_id' => $jenis->id,
        ]);
        $invEmptyBukti->setRelation('jenis', $jenis);

        $this->assertEquals('-', $service->resolveBuktiKepemilikan($invEmptyBukti));
    }
}

