<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\ManajemenAset\ManajemenAsetController;
use App\Http\Controllers\ExportData\ExportController;
use App\Http\Controllers\Pejabat\PejabatController;
use App\Http\Controllers\JenisInventaris\JenisInventarisController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Ruangan\RuanganController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// // Root: redirect ke login (pilihan umum)
Route::redirect('/', '/login');

if (app()->environment('local')) {
    Route::get('/_preview/error/{code}', function (string $code) {
        abort_unless(in_array($code, ['403', '404', '405', '419', '429', '500', '503'], true), 404);

        return response()->view('errors.' . $code, [], (int) $code);
    })->where('code', '403|404|405|419|429|500|503')->name('errors.preview');
}

// Public: login page (blade: resources/views/auth/login.blade.php)
// Middleware 'guest' akan redirect ke dashboard jika user sudah login
Route::middleware(['guest'])->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])
        ->middleware('throttle:5,5')
        ->name('login.perform');

    // Forgot & Reset Password
    // CATATAN: Fitur ini sengaja dinonaktifkan. Reset password dilakukan manual oleh Super Admin.
    // Kode dibiarkan dan dikomentari untuk referensi bila fitur ini diaktifkan kembali di masa depan.
    // Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForm'])->name('password.request');
    // Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink'])
    //     ->middleware('throttle:5,1')
    //     ->name('password.email');
    // Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    // Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Route group untuk halaman yang harusnya dilindungi (pakai auth middleware jika aktif)
Route::middleware(['auth'])->group(function () {

    // logout
    Route::get('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout.get');
    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Profile\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Profile\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [\App\Http\Controllers\Profile\ProfileController::class, 'showChangePassword'])->name('profile.password');
    Route::post('/profile/change-password', [\App\Http\Controllers\Profile\ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/activities', [\App\Http\Controllers\Dashboard\DashboardController::class, 'allActivities'])->name('dashboard.activities');
    Route::post('/dashboard/activities/delete-all', [\App\Http\Controllers\Dashboard\DashboardController::class, 'destroyAllActivities'])->name('dashboard.activities.destroy-all');
    Route::delete('/dashboard/activities/{activityLog}', [\App\Http\Controllers\Dashboard\DashboardController::class, 'destroyActivity'])->name('dashboard.activities.destroy');
    Route::get('/dashboard/charts/export/{chart}', [\App\Http\Controllers\Dashboard\DashboardController::class, 'exportChartPdf'])->name('dashboard.charts.export');

    Route::get('/dashboard/exports', [\App\Http\Controllers\Dashboard\DashboardController::class, 'allExports'])->name('dashboard.exports');
    
    // Dashboard API endpoints (alternative jika tidak pakai Sanctum, bisa langsung via web routes)
    Route::prefix('dashboard-api')->group(function () {
        Route::get('kpi', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getKpiData'])->name('dashboard.api.kpi');
        Route::get('charts', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getChartData'])->name('dashboard.api.charts');
        Route::get('activities', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getRecentActivities'])->name('dashboard.api.activities');
        Route::get('exports', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getRecentExports'])->name('dashboard.api.exports');
    });

    // Manajemen Aset (resource controller)
    Route::post('manajemen-aset/bulk-destroy', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'bulkDestroy'])->name('manajemen-aset.bulk-destroy');
    Route::post('manajemen-aset/{id}/duplicate', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'duplicate'])->name('manajemen-aset.duplicate');
    Route::resource('manajemen-aset', \App\Http\Controllers\ManajemenAset\ManajemenAsetController::class);
    Route::get('manajemen-aset/{id}/qr.png', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'qrImage'])->name('inventaris.qr');
    Route::delete('manajemen-aset/foto/{id}', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'deleteFoto'])->name('manajemen-aset.foto.delete');
    // Maintenance: regenerate barcode/QR PNG via browser (super admin only)
    Route::get('maintenance/qr-regenerate', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'regenerateQr'])->name('maintenance.qr-regenerate');

    // Inventaris form (legacy/static path kept for compatibility)
    Route::get('/inventaris-form', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'create'])->name('inventaris.form');
    Route::get('/inventaris-form/{id}', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'show'])->name('inventaris.form.show');

    // Export Data (resource-ish) - specific routes BEFORE resource routes
    Route::get('export-data/data-count', [\App\Http\Controllers\ExportData\ExportController::class, 'getDataCount'])->name('export-data.data-count');
    Route::get('export-data/ruangan-count', [\App\Http\Controllers\ExportData\ExportController::class, 'getRuanganCount'])->name('export-data.ruangan-count');
    Route::get('export-data/ruangan-pejabat-info', [\App\Http\Controllers\ExportData\ExportController::class, 'getRuanganPejabatInfo'])->name('export-data.ruangan-pejabat-info');
    Route::get('export-data/history-json', [\App\Http\Controllers\ExportData\ExportController::class, 'getHistoryJson'])->name('export-data.history-json');
    Route::get('export-data/history', [\App\Http\Controllers\ExportData\ExportController::class, 'history'])->name('export-data.history');
    Route::post('export-data/history/delete-all', [\App\Http\Controllers\ExportData\ExportController::class, 'destroyAllHistory'])->name('export-data.history.destroy-all');
    Route::delete('export-data/history/{log}', [\App\Http\Controllers\ExportData\ExportController::class, 'destroyHistory'])->name('export-data.history.destroy');
    Route::get('export-data/download/{filename}', [\App\Http\Controllers\ExportData\ExportController::class, 'download'])->name('export-data.download');
    Route::post('export-data/store-all', [\App\Http\Controllers\ExportData\ExportController::class, 'storeAll'])->name('export-data.store-all');
    Route::post('export-data/store-ruangan', [\App\Http\Controllers\ExportData\ExportController::class, 'storeRuangan'])->name('export-data.store-ruangan');
    Route::post('export-data/store-kib', [\App\Http\Controllers\ExportData\ExportController::class, 'storeKib'])->name('export-data.store-kib');
    Route::get('export-data/kib-pejabat-info', [\App\Http\Controllers\ExportData\ExportController::class, 'getKibPejabatInfo'])->name('export-data.kib-pejabat-info');
    Route::post('export-data/store-laporan-aset', [\App\Http\Controllers\ExportData\ExportController::class, 'storeLaporanAset'])->name('export-data.store-laporan-aset');
    Route::get('export-data/laporan-count', [\App\Http\Controllers\ExportData\ExportController::class, 'getLaporanCount'])->name('export-data.laporan-count');
    Route::post('export-data/set-override', [\App\Http\Controllers\ExportData\ExportController::class, 'setPejabatOverride'])->name('export-data.set-override');
    Route::resource('export-data', \App\Http\Controllers\ExportData\ExportController::class)->only(['index','store']);

    // Users & Pejabat (master data)
    Route::post('users/bulk-destroy', [\App\Http\Controllers\User\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::get('users/check-username', [\App\Http\Controllers\User\UserController::class, 'checkUsername'])->name('users.check-username');
    Route::patch('users/{id}/toggle-aktif', [\App\Http\Controllers\User\UserController::class, 'toggleAktif'])->name('users.toggle-aktif');
    Route::resource('users', \App\Http\Controllers\User\UserController::class);
    
    // Bidang (master data)
    Route::post('bidangs/bulk-destroy', [\App\Http\Controllers\Bidang\BidangController::class, 'bulkDestroy'])->name('bidangs.bulk-destroy');
    Route::resource('bidangs', \App\Http\Controllers\Bidang\BidangController::class);
    
    Route::post('pejabat/bulk-destroy', [\App\Http\Controllers\Pejabat\PejabatController::class, 'bulkDestroy'])->name('pejabat.bulk-destroy');
    Route::get('pejabat/{id}/ttd', [\App\Http\Controllers\Pejabat\PejabatController::class, 'viewTtd'])->name('pejabat.ttd.view');
    Route::resource('pejabat', \App\Http\Controllers\Pejabat\PejabatController::class);

    // Jenis Inventaris
    Route::post('jenis-inventaris/bulk-destroy', [\App\Http\Controllers\JenisInventaris\JenisInventarisController::class, 'bulkDestroy'])->name('jenis-inventaris.bulk-destroy');
    Route::resource('jenis-inventaris', \App\Http\Controllers\JenisInventaris\JenisInventarisController::class);
    Route::get('api/jenis-inventaris-field/{fieldId}/usage', [\App\Http\Controllers\JenisInventaris\JenisInventarisController::class, 'fieldUsage'])->name('jenis-inventaris.field-usage');

    // Ruangan (Master Data)
    Route::post('ruangan/bulk-destroy', [\App\Http\Controllers\Ruangan\RuanganController::class, 'bulkDestroy'])->name('ruangan.bulk-destroy');
    Route::resource('ruangan', \App\Http\Controllers\Ruangan\RuanganController::class);

    // Dropdown (Master Data)
    Route::post('dropdown/bulk-destroy', [\App\Http\Controllers\Dropdown\DropdownController::class, 'bulkDestroy'])->name('dropdown.bulk-destroy');
    Route::resource('dropdown', \App\Http\Controllers\Dropdown\DropdownController::class);

    // API endpoint for dynamic fields (used by frontend to render forms)
    Route::get('api/jenis-inventaris/{id}/fields', [\App\Http\Controllers\Api\JenisInventarisFieldApiController::class, 'show'])->name('api.jenis-inventaris.fields');

    // API endpoint for kode barang hierarki (5-level hierarchy search)
    // Catatan: route ini ada di web.php (bukan api.php) agar menggunakan session auth
    Route::get('api/kb/search', [\App\Http\Controllers\Api\KodeBarangHierarkiController::class, 'search'])->name('api.kb.search');
    Route::get('api/kb/by-kode', [\App\Http\Controllers\Api\KodeBarangHierarkiController::class, 'getByKode'])->name('api.kb.by-kode');
    Route::get('api/kb/resolve-ancestors', [\App\Http\Controllers\Api\KodeBarangHierarkiController::class, 'resolveAncestors'])->name('api.kb.resolve-ancestors');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',            [NotificationController::class, 'index'])->name('index');
        Route::get('/dropdown',    [NotificationController::class, 'dropdown'])->name('dropdown');
        Route::post('/mark-all',   [NotificationController::class, 'markAllRead'])->name('mark-all');
        Route::post('/delete-read', [NotificationController::class, 'destroyRead'])->name('delete-read');
        Route::get('/{id}/read',   [NotificationController::class, 'read'])->name('read');
        Route::delete('/{id}',     [NotificationController::class, 'destroy'])->name('destroy');
    });

});

// QR Code scan handler — hanya bisa diakses via signed URL (embedded di QR code)
// Tanpa signature yang valid (misal ketik manual) akan dapat 403
Route::get('/scan/{barcode}', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'scan'])
    ->middleware('signed')
    ->name('inventaris.scan');

// Jika belum pakai auth dan ingin akses semua halaman tanpa middleware,
// Anda bisa memindahkan/duplikasi route di atas keluar dari group middleware.

// Fallback / 404 (opsional): tampilkan view custom
// Route::fallback(function () {
//     return view('errors.404');
// });