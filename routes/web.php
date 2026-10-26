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
    })->where('code', '403|404|405|419|429|500|503')->name('errors.preview.local');

    Route::view('/design-preview', 'design-preview')->name('design.preview');
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
    Route::get('/dashboard/activities/export', [\App\Http\Controllers\Dashboard\DashboardController::class, 'exportActivities'])->name('dashboard.activities.export');
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
    Route::get('manajemen-aset/template/download', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'downloadTemplate'])->name('manajemen-aset.template.download');
    Route::post('manajemen-aset/import', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'import'])->name('manajemen-aset.import');
    Route::get('manajemen-aset/next-nup', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'getNextNup'])->name('manajemen-aset.next-nup');
    Route::post('manajemen-aset/bulk-destroy', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'bulkDestroy'])->name('manajemen-aset.bulk-destroy');
    Route::post('manajemen-aset/bulk-print-qr', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'bulkPrintQr'])->name('manajemen-aset.bulk-print-qr');
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
    Route::get('export-data/kib-count', [\App\Http\Controllers\ExportData\ExportController::class, 'getKibCount'])->name('export-data.kib-count');
    Route::get('export-data/kib-pejabat-info', [\App\Http\Controllers\ExportData\ExportController::class, 'getKibPejabatInfo'])->name('export-data.kib-pejabat-info');
    Route::post('export-data/store-laporan-aset', [\App\Http\Controllers\ExportData\ExportController::class, 'storeLaporanAset'])->name('export-data.store-laporan-aset');
    Route::get('export-data/laporan-count', [\App\Http\Controllers\ExportData\ExportController::class, 'getLaporanCount'])->name('export-data.laporan-count');
    Route::post('export-data/store-buku-inventaris-aset', [\App\Http\Controllers\ExportData\ExportController::class, 'storeBukuInventarisAset'])->name('export-data.store-buku-inventaris-aset');
    Route::get('export-data/buku-inventaris-count', [\App\Http\Controllers\ExportData\ExportController::class, 'getBukuInventarisCount'])->name('export-data.buku-inventaris-count');
    Route::post('export-data/set-override', [\App\Http\Controllers\ExportData\ExportController::class, 'setPejabatOverride'])->name('export-data.set-override');
    Route::resource('export-data', \App\Http\Controllers\ExportData\ExportController::class)->only(['index','store']);

    // Penghapusan Aset (Mutasi Berkurang)
    Route::get('penghapusan-aset/export', [\App\Http\Controllers\PenghapusanAset\PenghapusanAsetController::class, 'export'])->name('penghapusan-aset.export');
    Route::post('penghapusan-aset/restore', [\App\Http\Controllers\PenghapusanAset\PenghapusanAsetController::class, 'restore'])->name('penghapusan-aset.restore');
    Route::get('penghapusan-aset/{id}/download-pdf', [\App\Http\Controllers\PenghapusanAset\PenghapusanAsetController::class, 'downloadPdf'])->name('penghapusan-aset.download-pdf');
    Route::resource('penghapusan-aset', \App\Http\Controllers\PenghapusanAset\PenghapusanAsetController::class)->only(['index', 'store', 'show']);

    // Users & Pejabat (master data)
    Route::get('users/export', [\App\Http\Controllers\User\UserController::class, 'export'])->name('users.export');
    Route::get('users/template/download', [\App\Http\Controllers\User\UserController::class, 'downloadTemplate'])->name('users.template.download');
    Route::post('users/import', [\App\Http\Controllers\User\UserController::class, 'import'])->name('users.import');
    Route::post('users/bulk-destroy', [\App\Http\Controllers\User\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::get('users/check-username', [\App\Http\Controllers\User\UserController::class, 'checkUsername'])->name('users.check-username');
    Route::patch('users/{id}/toggle-aktif', [\App\Http\Controllers\User\UserController::class, 'toggleAktif'])->name('users.toggle-aktif');
    Route::resource('users', \App\Http\Controllers\User\UserController::class);
    
    // Bidang (master data)
    Route::get('bidangs/export', [\App\Http\Controllers\Bidang\BidangController::class, 'export'])->name('bidangs.export');
    Route::get('bidangs/template/download', [\App\Http\Controllers\Bidang\BidangController::class, 'downloadTemplate'])->name('bidangs.template.download');
    Route::post('bidangs/import', [\App\Http\Controllers\Bidang\BidangController::class, 'import'])->name('bidangs.import');
    Route::post('bidangs/bulk-destroy', [\App\Http\Controllers\Bidang\BidangController::class, 'bulkDestroy'])->name('bidangs.bulk-destroy');
    Route::resource('bidangs', \App\Http\Controllers\Bidang\BidangController::class);
    
    Route::get('pejabat/export', [\App\Http\Controllers\Pejabat\PejabatController::class, 'export'])->name('pejabat.export');
    Route::get('pejabat/template/download', [\App\Http\Controllers\Pejabat\PejabatController::class, 'downloadTemplate'])->name('pejabat.template.download');
    Route::post('pejabat/import', [\App\Http\Controllers\Pejabat\PejabatController::class, 'import'])->name('pejabat.import');
    Route::post('pejabat/bulk-destroy', [\App\Http\Controllers\Pejabat\PejabatController::class, 'bulkDestroy'])->name('pejabat.bulk-destroy');
    Route::get('pejabat/{id}/ttd', [\App\Http\Controllers\Pejabat\PejabatController::class, 'viewTtd'])->name('pejabat.ttd.view');
    Route::resource('pejabat', \App\Http\Controllers\Pejabat\PejabatController::class);

    // Jenis Inventaris
    Route::post('jenis-inventaris/bulk-destroy', [\App\Http\Controllers\JenisInventaris\JenisInventarisController::class, 'bulkDestroy'])->name('jenis-inventaris.bulk-destroy');
    Route::resource('jenis-inventaris', \App\Http\Controllers\JenisInventaris\JenisInventarisController::class);
    Route::get('api/jenis-inventaris-field/{fieldId}/usage', [\App\Http\Controllers\JenisInventaris\JenisInventarisController::class, 'fieldUsage'])->name('jenis-inventaris.field-usage');

    // Ruangan (Master Data)
    Route::get('ruangan/export', [\App\Http\Controllers\Ruangan\RuanganController::class, 'export'])->name('ruangan.export');
    Route::get('ruangan/template/download', [\App\Http\Controllers\Ruangan\RuanganController::class, 'downloadTemplate'])->name('ruangan.template.download');
    Route::post('ruangan/import', [\App\Http\Controllers\Ruangan\RuanganController::class, 'import'])->name('ruangan.import');
    Route::post('ruangan/bulk-destroy', [\App\Http\Controllers\Ruangan\RuanganController::class, 'bulkDestroy'])->name('ruangan.bulk-destroy');
    Route::resource('ruangan', \App\Http\Controllers\Ruangan\RuanganController::class);

    // Dropdown (Master Data)
    Route::get('dropdown/export', [\App\Http\Controllers\Dropdown\DropdownController::class, 'export'])->name('dropdown.export');
    Route::get('dropdown/template/download', [\App\Http\Controllers\Dropdown\DropdownController::class, 'downloadTemplate'])->name('dropdown.template.download');
    Route::post('dropdown/import', [\App\Http\Controllers\Dropdown\DropdownController::class, 'import'])->name('dropdown.import');
    Route::post('dropdown/bulk-destroy', [\App\Http\Controllers\Dropdown\DropdownController::class, 'bulkDestroy'])->name('dropdown.bulk-destroy');
    Route::resource('dropdown', \App\Http\Controllers\Dropdown\DropdownController::class);

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',            [NotificationController::class, 'index'])->name('index');
        Route::get('/dropdown',    [NotificationController::class, 'dropdown'])->name('dropdown');
        Route::post('/mark-all',   [NotificationController::class, 'markAllRead'])->name('mark-all');
        Route::post('/delete-read', [NotificationController::class, 'destroyRead'])->name('delete-read');
        Route::get('/{id}/read',   [NotificationController::class, 'read'])->name('read');
        Route::delete('/{id}',     [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Cadangan Basis Data (Super Admin only)
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Backup\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Backup\BackupController::class, 'store'])->name('store');
        Route::get('/download/{filename}', [\App\Http\Controllers\Backup\BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [\App\Http\Controllers\Backup\BackupController::class, 'destroy'])->name('destroy');
    });

});

// QR Code scan handler — hanya bisa diakses via signed URL (embedded di QR code)
// Tanpa signature yang valid (misal ketik manual) akan dapat 403
Route::get('/scan/{barcode}', [\App\Http\Controllers\ManajemenAset\ManajemenAsetController::class, 'scan'])
    ->middleware('signed')
    ->name('inventaris.scan');

// Jika belum pakai auth dan ingin akses semua halaman tanpa middleware,
// Anda bisa memindahkan/duplikasi route di atas keluar dari group middleware.

// Route Preview Halaman Error (Hanya aktif di environment local & testing untuk keperluan pengetesan desain)
if (app()->environment('local', 'testing')) {
    Route::get('/errors/{code}', function ($code) {
        if (view()->exists("errors.{$code}")) {
            return response()->view("errors.{$code}", [], (int) $code);
        }
        abort(404);
    })->where('code', '403|404|405|419|429|500|503')->name('errors.preview');
}