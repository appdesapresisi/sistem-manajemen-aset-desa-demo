<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JenisInventarisFieldApiController;
use App\Http\Controllers\Api\PejabatApiController;
use App\Http\Controllers\Api\JenisInventarisApiController;
use App\Http\Controllers\Api\InventarisApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\BidangController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Use web middleware to leverage existing session authentication
// and explicit throttling to reduce abuse/load spikes on API endpoints.
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // User info endpoint
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    // CRUD API Resources
    Route::apiResource('inventaris', InventarisApiController::class)->names('api.inventaris');
    
    Route::apiResource('users', UserApiController::class)->names('api.users');
    Route::apiResource('jenis-inventaris', JenisInventarisApiController::class)->except(['create','edit'])->names('api.jenis-inventaris');
    Route::apiResource('pejabats', PejabatApiController::class)->except(['create','edit'])->names('api.pejabats');
    Route::apiResource('bidangs', BidangController::class)->except(['create','edit'])->names('api.bidangs');
    
    // Dynamic fields endpoint
    Route::get('jenis-inventaris/{id}/fields', [JenisInventarisFieldApiController::class, 'show'])->name('api.jenis-inventaris.fields');

    // Ruangan & Dropdown endpoints
    Route::get('ruangan', [\App\Http\Controllers\Api\RuanganApiController::class, 'index']);
    Route::get('dropdown', [\App\Http\Controllers\Dropdown\DropdownController::class, 'index']);
    Route::get('dropdowns', [\App\Http\Controllers\Dropdown\DropdownController::class, 'index']);

    // Dashboard API endpoints
    Route::prefix('dashboard')->group(function () {
        Route::get('kpi', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getKpiData']);
        Route::get('charts', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getChartData']);
        Route::get('activities', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getRecentActivities']);
        Route::get('exports', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getRecentExports']);
    });

    // Next NUP endpoint
    Route::get('inventaris/next-nup', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'getNextNup'])->name('api.inventaris.next-nup');

    // Kode barang hierarki endpoints (5-level hierarchy search)
    Route::prefix('kb')->name('api.kb.')->group(function () {
        Route::get('search', [\App\Http\Controllers\Api\KodeBarangHierarkiController::class, 'search'])->name('search');
        Route::get('by-kode', [\App\Http\Controllers\Api\KodeBarangHierarkiController::class, 'getByKode'])->name('by-kode');
        Route::get('resolve-ancestors', [\App\Http\Controllers\Api\KodeBarangHierarkiController::class, 'resolveAncestors'])->name('resolve-ancestors');
    });
});

// =============================================================================
// EXTERNAL API — untuk client external (mobile app, sistem lain)
// Autentikasi via Bearer Token (Sanctum), READ-ONLY endpoints.
// Tidak mempengaruhi sistem web internal.
// =============================================================================

// Token generation (public — tidak perlu auth)
Route::post('external/token', [\App\Http\Controllers\Api\ExternalApiController::class, 'createToken'])
    ->middleware('throttle:10,5'); // max 10 percobaan per 5 menit

// Protected external endpoints (butuh Bearer token)
Route::middleware(['auth:sanctum', 'throttle:120,1'])->prefix('external')->group(function () {
    // Revoke token
    Route::delete('token', [\App\Http\Controllers\Api\ExternalApiController::class, 'revokeToken']);

    // Read-only data endpoints
    Route::get('inventaris', [\App\Http\Controllers\Api\ExternalApiController::class, 'inventaris']);
    Route::get('inventaris/{id}', [\App\Http\Controllers\Api\ExternalApiController::class, 'inventarisDetail']);
    Route::get('jenis-inventaris', [\App\Http\Controllers\Api\ExternalApiController::class, 'jenisInventaris']);
    Route::get('ruangan', [\App\Http\Controllers\Api\ExternalApiController::class, 'ruangan']);
    Route::get('pejabat', [\App\Http\Controllers\Api\ExternalApiController::class, 'pejabat']);
    Route::get('statistik', [\App\Http\Controllers\Api\ExternalApiController::class, 'statistik']);
});