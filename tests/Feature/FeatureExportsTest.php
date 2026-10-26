<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Tests\TestCase;

class FeatureExportsTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::whereHas('role', fn($q) => $q->where('nama', 'super_admin'))->first()
            ?? User::factory()->create(['role_id' => 1]);
    }

    public function test_penghapusan_aset_export_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('penghapusan-aset.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_penghapusan_aset_export_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('penghapusan-aset.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));
    }

    public function test_pejabat_export_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('pejabat.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_pejabat_export_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('pejabat.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));
    }

    public function test_pejabat_export_with_filters()
    {
        $response = $this->actingAs($this->admin)->get(route('pejabat.export', ['format' => 'excel', 'search' => 'Sekretaris', 'status' => 1]));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_users_export_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('users.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_users_export_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('users.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));
    }

    public function test_users_export_with_filters()
    {
        $response = $this->actingAs($this->admin)->get(route('users.export', ['format' => 'excel', 'search' => 'admin', 'status' => 1]));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_bidangs_export_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('bidangs.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_bidangs_export_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('bidangs.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));
    }

    public function test_dropdown_export_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('dropdown.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_dropdown_export_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('dropdown.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));
    }

    public function test_activities_export_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.activities.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_activities_export_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.activities.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));
    }
}
