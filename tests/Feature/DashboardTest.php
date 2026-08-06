<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\ActivityLog;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\ExportLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $role = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);

        $this->superAdmin = User::create([
            'nama' => 'SA Dashboard', 'username' => 'sa_dash_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $role->id, 'is_aktif' => 1,
        ]);
    }

    // ─── Dashboard Page ──────────────────────────────────────

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_guest_cannot_view_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/login');
    }

    // ─── Dashboard API Endpoints ─────────────────────────────

    public function test_kpi_api_returns_json(): void
    {
        $response = $this->actingAs($this->superAdmin)->getJson(route('dashboard.api.kpi'));
        $response->assertOk();
    }

    public function test_charts_api_returns_json(): void
    {
        $response = $this->actingAs($this->superAdmin)->getJson(route('dashboard.api.charts'));
        $response->assertOk();
    }

    public function test_activities_api_returns_json(): void
    {
        // Create an activity log entry
        ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'module' => 'Inventaris',
            'action' => 'tambah',
            'detail' => 'Test activity',
        ]);

        $response = $this->actingAs($this->superAdmin)->getJson(route('dashboard.api.activities'));
        $response->assertOk();
    }

    public function test_exports_api_returns_json(): void
    {
        $response = $this->actingAs($this->superAdmin)->getJson(route('dashboard.api.exports'));
        $response->assertOk();
    }

    public function test_kpi_menghitung_semua_inventaris(): void
    {
        Cache::forget('dashboard_kpi');

        $beforeResponse = $this->actingAs($this->superAdmin)->getJson(route('dashboard.api.kpi'));
        $beforeResponse->assertOk();
        $before = $beforeResponse->json();

        $jenis = JenisInventaris::create(['nama' => 'Jenis KPI Filter', 'deskripsi' => 'Test']);

        $approved = new Inventaris();
        $approved->jenis_inventaris_id = $jenis->id;
        $approved->nama_barang = 'Approved KPI';
        $approved->kode_barang = 'KPI-APP-001';
        $approved->nilai_perolehan = 1000000;
        $approved->tahun_perolehan = (int) now()->format('Y');
        $approved->kondisi = 'baik';
        $approved->created_by = $this->superAdmin->id;
        $approved->status = 'disetujui';
        $approved->save();

        $pending = new Inventaris();
        $pending->jenis_inventaris_id = $jenis->id;
        $pending->nama_barang = 'Pending KPI';
        $pending->kode_barang = 'KPI-PEN-001';
        $pending->nilai_perolehan = 500000;
        $pending->tahun_perolehan = (int) now()->format('Y');
        $pending->kondisi = 'rusak_ringan';
        $pending->created_by = $this->superAdmin->id;
        $pending->status = 'pending';
        $pending->save();

        $rejected = new Inventaris();
        $rejected->jenis_inventaris_id = $jenis->id;
        $rejected->nama_barang = 'Rejected KPI';
        $rejected->kode_barang = 'KPI-REJ-001';
        $rejected->nilai_perolehan = 250000;
        $rejected->tahun_perolehan = (int) now()->format('Y');
        $rejected->kondisi = 'rusak_berat';
        $rejected->created_by = $this->superAdmin->id;
        $rejected->status = 'ditolak';
        $rejected->save();

        Cache::forget('dashboard_kpi');

        $afterResponse = $this->actingAs($this->superAdmin)->getJson(route('dashboard.api.kpi'));
        $afterResponse->assertOk();

        $after = $afterResponse->json();

        $this->assertSame(
            (int) ($before['totalAssets'] ?? 0) + 3,
            (int) ($after['totalAssets'] ?? 0)
        );
        $this->assertEquals(
            (float) ($before['totalValue'] ?? 0) + 1750000.0,
            (float) ($after['totalValue'] ?? 0)
        );
        $this->assertSame(
            (int) ($before['kondisiAset']['baik'] ?? 0) + 1,
            (int) ($after['kondisiAset']['baik'] ?? 0)
        );
        $this->assertSame(
            (int) ($before['kondisiAset']['rusakRingan'] ?? 0) + 1,
            (int) ($after['kondisiAset']['rusakRingan'] ?? 0)
        );
        $this->assertSame(
            (int) ($before['kondisiAset']['rusakBerat'] ?? 0) + 1,
            (int) ($after['kondisiAset']['rusakBerat'] ?? 0)
        );
    }

    public function test_charts_menghitung_semua_inventaris(): void
    {
        Cache::forget('dashboard_charts');

        $beforeResponse = $this->actingAs($this->superAdmin)
            ->getJson(route('dashboard.api.charts', ['bar_metric' => 'jumlah', 'donut_metric' => 'jumlah']));
        $beforeResponse->assertOk();
        $before = $beforeResponse->json();

        $jenis = JenisInventaris::create(['nama' => 'Jenis Chart Filter', 'deskripsi' => 'Test']);

        $approved = new Inventaris();
        $approved->jenis_inventaris_id = $jenis->id;
        $approved->nama_barang = 'Approved Chart';
        $approved->kode_barang = 'CHT-APP-001';
        $approved->nilai_perolehan = 1200000;
        $approved->tahun_perolehan = (int) now()->format('Y');
        $approved->kondisi = 'baik';
        $approved->created_by = $this->superAdmin->id;
        $approved->status = 'disetujui';
        $approved->save();

        $pending = new Inventaris();
        $pending->jenis_inventaris_id = $jenis->id;
        $pending->nama_barang = 'Pending Chart';
        $pending->kode_barang = 'CHT-PEN-001';
        $pending->nilai_perolehan = 900000;
        $pending->tahun_perolehan = (int) now()->format('Y');
        $pending->kondisi = 'baik';
        $pending->created_by = $this->superAdmin->id;
        $pending->status = 'pending';
        $pending->save();

        $afterResponse = $this->actingAs($this->superAdmin)
            ->getJson(route('dashboard.api.charts', ['bar_metric' => 'jumlah', 'donut_metric' => 'jumlah']));
        $afterResponse->assertOk();

        $after = $afterResponse->json();

        $this->assertSame(
            array_sum($before['barValues'] ?? []) + 2,
            array_sum($after['barValues'] ?? [])
        );
        $this->assertSame(
            array_sum($before['donutValues'] ?? []) + 2,
            array_sum($after['donutValues'] ?? [])
        );
        $this->assertSame(
            array_sum(array_values($before['inventarisByJenis'] ?? [])) + 2,
            array_sum(array_values($after['inventarisByJenis'] ?? []))
        );
        $this->assertSame(
            array_sum(array_column($before['monthlyTrend'] ?? [], 'count')) + 2,
            array_sum(array_column($after['monthlyTrend'] ?? [], 'count'))
        );
    }

    // ─── Activities Page ─────────────────────────────────────

    public function test_all_activities_page_loads(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.activities'));
        $response->assertOk();
    }

    public function test_chart_can_be_exported_to_pdf_and_logged(): void
    {
        $beforeCount = ExportLog::count();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('dashboard.charts.export', ['chart' => 'bar', 'bar_metric' => 'nilai']));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        $this->assertSame($beforeCount + 1, ExportLog::count());

        $latestLog = ExportLog::latest('id')->first();
        $this->assertNotNull($latestLog);
        $this->assertStringContainsString('dashboard-chart-bar_', (string) $latestLog->nama_file);
        $this->assertSame('pdf', strtolower((string) $latestLog->tipe_export));
    }
}
