<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Pegawai\AkunController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use App\Http\Controllers\Pegawai\PresensiController;
use App\Http\Controllers\Pegawai\PengajuanController;
use App\Http\Controllers\Pegawai\PegawaiController as PegawaiListController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\ManajemenPegawaiController;
use App\Http\Controllers\Admin\ManajemenLokasiController;
use App\Http\Controllers\Admin\JamKerjaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\SuperAdmin\DashboardSuperAdminController;
use App\Http\Controllers\SuperAdmin\ManajemenAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// --------------------
// Auth Routes
// --------------------
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('preventbackhistory');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('preventbackhistory');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

// Handle proses register
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// --------------------
// Dashboard / Auth-protected routes
// --------------------
Route::middleware(['auth', 'verified'])->group(function () {

    // --------------------
    // Pegawai
    // --------------------
    Route::prefix('pegawai')->name('pegawai.')->middleware('checkrole:pegawai')->group(function () {
        Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');

        // Presensi
        Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');
        Route::get('/riwayat', [PresensiController::class, 'riwayat'])->name('riwayat');

        // Pengajuan presensi
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');

        // Daftar pegawai
        Route::get('/daftar', [PegawaiListController::class, 'index'])->name('daftar');

        // Akun Pegawai
        Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
        Route::post('/akun', [AkunController::class, 'update'])->name('akun.update');
        Route::post('/akun/logout', [AkunController::class, 'logout'])->name('akun.logout');
    });

    // --------------------
    // Admin
    // --------------------
    Route::prefix('admin')->name('admin.')->middleware('checkrole:admin')->group(function () {
        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

        // Pengajuan Approve / Reject
        Route::post('/pengajuan/{id}/approve', [DashboardAdminController::class, 'approve'])->name('pengajuan.approve');
        Route::post('/pengajuan/{id}/reject', [DashboardAdminController::class, 'reject'])->name('pengajuan.reject');

        // Presensi Pending Approve / Reject
        Route::post('/presensi/{id}/approve', [DashboardAdminController::class, 'approvePresensi'])->name('presensi.approve');
        Route::post('/presensi/{id}/reject', [DashboardAdminController::class, 'rejectPresensi'])->name('presensi.reject');

        // Manajemen Pegawai
        Route::prefix('manajemen-pegawai')->name('manajemenpegawai.')->group(function () {
            Route::get('/', [ManajemenPegawaiController::class, 'index'])->name('index');
            Route::post('/', [ManajemenPegawaiController::class, 'store'])->name('store');
            Route::get('/{id}', [ManajemenPegawaiController::class, 'show'])->name('show');
            Route::put('/{id}', [ManajemenPegawaiController::class, 'update'])->name('update');
            Route::delete('/{id}', [ManajemenPegawaiController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/reset-password', [ManajemenPegawaiController::class, 'resetPassword'])->name('reset-password');
        });

        // Manajemen Lokasi Presensi
        Route::prefix('lokasi')->name('lokasi.')->group(function () {
            Route::get('/', [ManajemenLokasiController::class, 'index'])->name('index');
            Route::post('/', [ManajemenLokasiController::class, 'store'])->name('store');
            Route::get('/{id}', [ManajemenLokasiController::class, 'show'])->name('show');
            Route::put('/{id}', [ManajemenLokasiController::class, 'update'])->name('update');
            Route::delete('/{id}', [ManajemenLokasiController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Jam Kerja
        Route::prefix('jamkerja')->name('jamkerja.')->group(function () {
            Route::get('/', [JamKerjaController::class, 'index'])->name('index');
            Route::get('/{id}', [JamKerjaController::class, 'show'])->name('show');
            Route::post('/', [JamKerjaController::class, 'store'])->name('store');
            Route::put('/{id}', [JamKerjaController::class, 'update'])->name('update');
            Route::delete('/{id}', [JamKerjaController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Laporan Kehadiran
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/data', [LaporanController::class, 'getLaporan'])->name('data');
            Route::get('/pdf', [LaporanController::class, 'exportPdf'])->name('pdf');
            Route::get('/excel', [LaporanController::class, 'exportExcel'])->name('excel');
        });
    });

    // --------------------
    // Superadmin
    // --------------------
    Route::prefix('superadmin')->name('superadmin.')->middleware('checkrole:superadmin')->group(function () {
        Route::get('/dashboard', [DashboardSuperAdminController::class, 'index'])->name('dashboard');

        // Manajemen Admin
        Route::prefix('manajemen-admin')->name('manajemenadmin.')->group(function () {
            Route::get('/', [ManajemenAdminController::class, 'index'])->name('index');
            Route::post('/', [ManajemenAdminController::class, 'store'])->name('store');
            Route::put('/{id}', [ManajemenAdminController::class, 'update'])->name('update');
            Route::delete('/{id}', [ManajemenAdminController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/reset-password', [ManajemenAdminController::class, 'resetPassword'])->name('reset-password');
        });
    });
});
