<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Pegawai\AkunController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use App\Http\Controllers\Pegawai\PresensiController;
use App\Http\Controllers\Pegawai\PengajuanController;
use App\Http\Controllers\Pegawai\PegawaiController as PegawaiListController;
use App\Http\Controllers\Pegawai\CutiController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\ManajemenPegawaiController;
use App\Http\Controllers\Admin\ManajemenLokasiController;
use App\Http\Controllers\Admin\JamKerjaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PerformaController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\SuperAdmin\DashboardSuperAdminController;
use App\Http\Controllers\SuperAdmin\ManajemenAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/download', function () {
    return view('download');
})->name('download');

// PWA — serve SW from root scope
Route::get('/sw.js', function () {
    return response(file_get_contents(public_path('sw.js')), 200)
        ->header('Content-Type', 'application/javascript')
        ->header('Service-Worker-Allowed', '/');
});

// Digital Asset Links — required for TWA (no address bar)
Route::get('/.well-known/assetlinks.json', function () {
    return response()->json([[
        'relation' => ['delegate_permission/common.handle_all_urls'],
        'target' => [
            'namespace' => 'android_app',
            'package_name' => 'com.karismaapp.twa',
            'sha256_cert_fingerprints' => ['01:C2:65:6D:60:E7:CC:0D:9F:0A:E1:41:24:85:16:BE:B8:BB:DF:5B:70:3C:6C:0A:FC:FF:E3:3B:92:C7:0A:34'],
        ],
    ]]);
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

// Register dengan kode_instansi
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// --------------------
// Dashboard / Auth-protected routes
// --------------------
Route::middleware(['auth', 'verified', 'detectdevice'])->group(function () {

    // --------------------
    // PEGAWAI
    // --------------------
    Route::prefix('pegawai')->name('pegawai.')->middleware('checkrole:pegawai')->group(function () {
        Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');

        // Presensi
        Route::get('/presensi', fn() => redirect()->route('pegawai.dashboard'));
        Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');
        Route::get('/absen-darurat', [PresensiController::class, 'darurat'])->name('presensi.darurat');
        Route::get('/riwayat', [PresensiController::class, 'riwayat'])->name('riwayat');
        Route::get('/riwayat/pdf', [PresensiController::class, 'exportPdf'])->name('riwayat.pdf');

        // Pengajuan presensi
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');

        // Daftar pegawai
        Route::get('/daftar', [PegawaiListController::class, 'index'])->name('daftar');

        // Akun Pegawai
        Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
        Route::post('/akun', [AkunController::class, 'update'])->name('akun.update');
        Route::post('/akun/logout', [AkunController::class, 'logout'])->name('akun.logout');
    });

    // --------------------
    // ADMIN
    // --------------------
    Route::prefix('admin')->name('admin.')->middleware('checkrole:admin')->group(function () {
        // Dashboard & API untuk modal detail
        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardAdminController::class, 'getDashboardData'])->name('dashboard.data');
        Route::get('/presensi/{id}/detail', [DashboardAdminController::class, 'getPresensiDetail'])->name('presensi.detail');
        Route::get('/pengajuan/{id}/detail', [DashboardAdminController::class, 'getPengajuanDetail'])->name('pengajuan.detail');

        // Pengajuan Approve / Reject
        Route::post('/pengajuan/{id}/approve', [DashboardAdminController::class, 'approve'])->name('pengajuan.approve');
        Route::post('/pengajuan/{id}/reject', [DashboardAdminController::class, 'reject'])->name('pengajuan.reject');
        Route::post('/cuti/{id}/approve', [DashboardAdminController::class, 'approveCuti'])->name('cuti.approve');
        Route::post('/cuti/{id}/reject', [DashboardAdminController::class, 'rejectCuti'])->name('cuti.reject');

        // Presensi Pending Approve / Reject
        Route::post('/presensi/{id}/approve', [DashboardAdminController::class, 'approvePresensi'])->name('presensi.approve');
        Route::post('/presensi/{id}/reject', [DashboardAdminController::class, 'rejectPresensi'])->name('presensi.reject');

        // --------------------
        // Manajemen Pegawai
        // --------------------
        Route::prefix('manajemen-pegawai')->name('manajemenpegawai.')->group(function () {
            Route::get('/', [ManajemenPegawaiController::class, 'index'])->name('index');
            Route::post('/', [ManajemenPegawaiController::class, 'store'])->name('store');
            Route::get('/{id}', [ManajemenPegawaiController::class, 'show'])->name('show');
            Route::put('/{id}', [ManajemenPegawaiController::class, 'update'])->name('update');
            Route::delete('/{id}', [ManajemenPegawaiController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/reset-password', [ManajemenPegawaiController::class, 'resetPassword'])->name('reset-password');
        });

        // --------------------
        // Manajemen Lokasi Presensi
        // --------------------
        Route::prefix('lokasi')->name('lokasi.')->group(function () {
            Route::get('/', [ManajemenLokasiController::class, 'index'])->name('index');
            Route::post('/', [ManajemenLokasiController::class, 'store'])->name('store');
            Route::get('/{id}', [ManajemenLokasiController::class, 'show'])->name('show');
            Route::put('/{id}', [ManajemenLokasiController::class, 'update'])->name('update');
            Route::delete('/{id}', [ManajemenLokasiController::class, 'destroy'])->name('destroy');
        });

        // --------------------
        // Manajemen Jam Kerja & Jam Shift
        // --------------------
        Route::prefix('jamkerja')->name('jamkerja.')->group(function () {
            Route::get('/', [JamKerjaController::class, 'index'])->name('index');

            // Jam kerja normal
            Route::get('/{id}', [JamKerjaController::class, 'show'])->name('show');
            Route::post('/', [JamKerjaController::class, 'store'])->name('store');
            Route::put('/{id}', [JamKerjaController::class, 'update'])->name('update');
            Route::delete('/{id}', [JamKerjaController::class, 'destroy'])->name('destroy');

            // Jam shift
            Route::post('/shift', [JamKerjaController::class, 'storeShift'])->name('shift.store');
            Route::get('/shift/{id}', [JamKerjaController::class, 'showShift'])->name('shift.show');
            Route::put('/shift/{id}', [JamKerjaController::class, 'updateShift'])->name('shift.update');
            Route::delete('/shift/{id}', [JamKerjaController::class, 'destroyShift'])->name('shift.destroy');
        });

        // --------------------
        // Laporan Kehadiran
        // --------------------
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/data', [LaporanController::class, 'getLaporan'])->name('data');
            Route::get('/pdf', [LaporanController::class, 'exportPdf'])->name('pdf');
            Route::get('/excel', [LaporanController::class, 'exportExcel'])->name('excel');
        });

        // --------------------
        // Performa Pegawai
        // --------------------
        Route::prefix('performa')->name('performa.')->group(function () {
            Route::get('/', [PerformaController::class, 'index'])->name('index');
            Route::get('/data', [PerformaController::class, 'getData'])->name('data');
            Route::get('/pdf', [PerformaController::class, 'exportPdf'])->name('pdf');
        });

        // --------------------
        // Pengumuman
        // --------------------
        Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
            Route::get('/', [PengumumanController::class, 'index'])->name('index');
            Route::post('/', [PengumumanController::class, 'store'])->name('store');
            Route::get('/{id}', [PengumumanController::class, 'show'])->name('show');
            Route::put('/{id}', [PengumumanController::class, 'update'])->name('update');
            Route::delete('/{id}', [PengumumanController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle', [PengumumanController::class, 'toggle'])->name('toggle');
            Route::post('/upload-image', [PengumumanController::class, 'uploadImage'])->name('upload-image');
            Route::post('/reorder', [PengumumanController::class, 'reorder'])->name('reorder');
        });

        // --------------------
        // Pengaturan
        // --------------------
        Route::get('/pengaturan', function () {
            return view('admin.pengaturan', [
                'settings' => \App\Models\AppSetting::all()->pluck('value', 'key'),
            ]);
        })->name('pengaturan.index');

        Route::post('/pengaturan', function (\Illuminate\Http\Request $request) {
            \App\Models\AppSetting::setValue('disable_presensi_hari_libur', $request->boolean('disable_presensi_hari_libur') ? '1' : '0');
            \App\Models\AppSetting::setValue('enable_face_detection', $request->boolean('enable_face_detection') ? '1' : '0');
            \App\Models\AppSetting::setValue('face_detection_mode', $request->input('face_detection_mode', 'all'));
            \App\Models\AppSetting::setValue('face_detection_users', json_encode($request->input('face_detection_users', [])));
            \App\Models\AppSetting::setValue('require_masuk_before_pulang', $request->boolean('require_masuk_before_pulang') ? '1' : '0');
            \App\Models\AppSetting::setValue('enable_absen_darurat', $request->boolean('enable_absen_darurat') ? '1' : '0');
            \App\Models\AppSetting::setValue('absen_darurat_mode', $request->input('absen_darurat_mode', 'all'));
            \App\Models\AppSetting::setValue('absen_darurat_users', json_encode($request->input('absen_darurat_users', [])));
            return redirect()->route('admin.pengaturan.index')->with('success', 'Pengaturan berhasil disimpan');
        })->name('pengaturan.update');
    });

    // --------------------
    // SUPERADMIN
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