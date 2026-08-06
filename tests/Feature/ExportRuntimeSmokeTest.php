<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\ExportLog;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportRuntimeSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_runtime_export_per_jenis_and_all_jenis_returns_json_and_writes_history(): void
    {
        Storage::fake('public');
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $role = Role::firstOrCreate(
            ['nama' => 'super_admin'],
            ['tugas' => 'Akses penuh', 'deskripsi' => 'Role untuk pengujian']
        );

        $user = User::create([
            'nama' => 'QA Export Runtime',
            'email' => 'qa-export-runtime-' . uniqid() . '@example.test',
            'password' => 'secret123',
            'role_id' => $role->id,
            'is_aktif' => 1,
        ]);

        $jenis = JenisInventaris::create([
            'nama' => 'Jenis Test Runtime ' . uniqid(),
            'deskripsi' => 'Data uji runtime export',
        ]);

        JenisInventarisData::create([
            'jenis_inventaris_id' => $jenis->id,
            'nama_field' => 'nama_barang',
            'label_field' => 'Nama Barang',
            'field_type' => 'fixed',
            'fixed_field_name' => 'nama_barang',
            'tipe' => 'string',
            'is_required' => 1,
            'order' => 1,
        ]);

        $inventaris = new Inventaris();
        $inventaris->jenis_inventaris_id = $jenis->id;
        $inventaris->nama_barang = 'Aset Uji Runtime';
        $inventaris->kode_barang = 'RT-001';
        $inventaris->status = 'disetujui';
        $inventaris->created_by = $user->id;
        $inventaris->updated_by = $user->id;
        $inventaris->save();

        $tahun = now()->format('Y');

        // 1) Export per jenis (excel)
        $perJenisResponse = $this->actingAs($user)->post(route('export-data.store'), [
            'jenis_inventaris_id' => (string) $jenis->id,
            'format' => 'excel',
            'tahun' => $tahun,
        ]);

        $perJenisResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'filename', 'file_url']);

        $perJenisFilename = (string) $perJenisResponse->json('filename');
        $this->assertStringEndsWith('.xlsx', $perJenisFilename);

        $this->assertDatabaseHas('export_logs', [
            'user_id' => $user->id,
            'jenis_inventaris_id' => $jenis->id,
            'tipe_export' => 'excel',
            'nama_file' => $perJenisFilename,
        ]);

        // 2) Export semua jenis (pdf)
        $allJenisResponse = $this->actingAs($user)->post(route('export-data.store-all'), [
            'format' => 'pdf',
            'tahun' => $tahun,
        ]);

        $allJenisResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'filename', 'file_url']);

        $allJenisFilename = (string) $allJenisResponse->json('filename');
        $this->assertStringEndsWith('.pdf', $allJenisFilename);

        $this->assertDatabaseHas('export_logs', [
            'user_id' => $user->id,
            'jenis_inventaris_id' => null,
            'tipe_export' => 'pdf',
            'nama_file' => $allJenisFilename,
        ]);

        $allJenisLog = ExportLog::where('nama_file', $allJenisFilename)->first();
        $this->assertNotNull($allJenisLog);
        $this->assertSame('all_jenis', data_get($allJenisLog->filters, 'mode'));

        // 3) History JSON by jenis harus memuat export per-jenis
        $historyByJenis = $this->actingAs($user)
            ->getJson(route('export-data.history-json', ['jenis_inventaris_id' => $jenis->id]))
            ->assertOk()
            ->json();

        $this->assertTrue(
            collect($historyByJenis)->contains(fn ($item) =>
                ($item['format'] ?? null) === 'EXCEL'
                && ($item['note'] ?? null) === $perJenisFilename
            ),
            'History by jenis tidak memuat log export per-jenis.'
        );

        // 4) History JSON global harus memuat export semua-jenis
        $historyGlobal = $this->actingAs($user)
            ->getJson(route('export-data.history-json'))
            ->assertOk()
            ->json();

        $this->assertTrue(
            collect($historyGlobal)->contains(fn ($item) =>
                ($item['format'] ?? null) === 'PDF'
                && ($item['note'] ?? null) === $allJenisFilename
            ),
            'History global tidak memuat log export semua-jenis.'
        );
    }
}
