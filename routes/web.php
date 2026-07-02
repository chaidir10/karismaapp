<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Pegawai\AkunController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use App\Http\Controllers\Pegawai\PresensiController;
use App\Http\Controllers\Pegawai\PushSubscriptionController;
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
use App\Http\Controllers\Admin\ShiftPegawaiController;
use App\Http\Controllers\SuperAdmin\DashboardSuperAdminController;
use App\Http\Controllers\SuperAdmin\ManajemenAdminController;
use App\Http\Controllers\Operator\OperatorDashboardController;
use App\Http\Controllers\Operator\OperatorPengaturanController;
use App\Http\Controllers\Operator\OperatorActivityLogController;
use App\Http\Controllers\Operator\OperatorTrackingController;
use App\Http\Controllers\Operator\OperatorPresensiController;
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

// PWA manifest — dynamic icons from app settings
Route::get('/manifest.json', function () {
    $logoPath = \App\Models\AppSetting::getValue('app_logo');
    $hasCustom = $logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath);
    $iconUrl = $hasCustom ? '/public/storage/' . $logoPath : '/public/pwa/icons/icon-512x512.png';
    $icon192 = $hasCustom ? $iconUrl : '/public/pwa/icons/icon-192x192.png';

    return response()->json([
        'id' => '/pegawai/dashboard',
        'name' => 'KARISMA - Presensi ASN',
        'short_name' => 'KARISMA',
        'description' => 'Aplikasi Presensi ASN Balai Kekarantinaan Kesehatan Kelas I Tarakan',
        'start_url' => '/pegawai/dashboard',
        'scope' => '/',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'lang' => 'id',
        'dir' => 'ltr',
        'background_color' => '#0b0f19',
        'theme_color' => '#5AB6EA',
        'categories' => ['business', 'productivity'],
        'icons' => [
            ['src' => $icon192, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => $icon192, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'maskable'],
            ['src' => $iconUrl, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => $iconUrl, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
        ],
        'screenshots' => [
            ['src' => $iconUrl, 'sizes' => '512x512', 'type' => 'image/png', 'form_factor' => 'narrow', 'label' => 'KARISMA Dashboard'],
        ],
        'shortcuts' => [
            ['name' => 'Presensi', 'short_name' => 'Absen', 'url' => '/pegawai/dashboard', 'icons' => [['src' => $icon192, 'sizes' => '192x192']]],
            ['name' => 'Riwayat', 'short_name' => 'Riwayat', 'url' => '/pegawai/riwayat', 'icons' => [['src' => $icon192, 'sizes' => '192x192']]],
        ],
        'display_override' => ['standalone', 'minimal-ui'],
        'launch_handler' => ['client_mode' => 'navigate-existing'],
        'prefer_related_applications' => false,
    ])->header('Content-Type', 'application/manifest+json')
      ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
});

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
Route::middleware(['auth', 'verified', 'detectdevice', 'logactivity'])->group(function () {

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
        Route::post('/akun/toggle-darurat', [AkunController::class, 'toggleDarurat'])->name('akun.toggle-darurat');
        Route::post('/akun/save-setting', [AkunController::class, 'saveSetting'])->name('akun.save-setting');
        Route::post('/akun/reset-presensi', [AkunController::class, 'resetPresensi'])->name('akun.reset-presensi');
        Route::post('/akun/update-jam', [AkunController::class, 'updateJamPresensi'])->name('akun.update-jam');
        Route::post('/akun/simulasi-terpenuhi', [AkunController::class, 'simulasiTerpenuhi'])->name('akun.simulasi-terpenuhi');
        Route::post('/akun/set-wilayah', [AkunController::class, 'setWilayah'])->name('akun.set-wilayah');
        Route::post('/akun/logout', [AkunController::class, 'logout'])->name('akun.logout');
        Route::post('/report-device-issue', [\App\Http\Controllers\Admin\DeviceIssueController::class, 'report'])->name('report-device-issue');
        Route::post('/location-ping', [\App\Http\Controllers\Operator\OperatorTrackingController::class, 'ping'])->name('location.ping');

        // Push notification subscriptions
        Route::post('/push-subscription', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
        Route::delete('/push-subscription', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    });

    // --------------------
    // ADMIN
    // --------------------
    Route::prefix('admin')->name('admin.')->middleware('checkrole:admin')->group(function () {
        // Dashboard & API untuk modal detail
        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardAdminController::class, 'getDashboardData'])->name('dashboard.data');
        Route::get('/dashboard/chart', [DashboardAdminController::class, 'getChartData'])->name('dashboard.chart');
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

            // Hari Libur (harus sebelum /{id} agar tidak tertangkap)
            Route::get('/holidays', [JamKerjaController::class, 'holidays'])->name('holidays');
            Route::post('/holiday/sync', [JamKerjaController::class, 'syncHolidays'])->name('holiday.sync');
            Route::post('/holiday', [JamKerjaController::class, 'storeHoliday'])->name('holiday.store');
            Route::put('/holiday/{id}', [JamKerjaController::class, 'updateHoliday'])->name('holiday.update');
            Route::delete('/holiday/{id}', [JamKerjaController::class, 'destroyHoliday'])->name('holiday.destroy');
            Route::post('/holiday/{id}/toggle', [JamKerjaController::class, 'toggleHoliday'])->name('holiday.toggle');

            // Jam shift
            Route::post('/shift', [JamKerjaController::class, 'storeShift'])->name('shift.store');
            Route::get('/shift/{id}', [JamKerjaController::class, 'showShift'])->name('shift.show');
            Route::put('/shift/{id}', [JamKerjaController::class, 'updateShift'])->name('shift.update');
            Route::delete('/shift/{id}', [JamKerjaController::class, 'destroyShift'])->name('shift.destroy');

            // Jam kerja normal (/{id} paling bawah)
            Route::get('/{id}', [JamKerjaController::class, 'show'])->name('show');
            Route::post('/', [JamKerjaController::class, 'store'])->name('store');
            Route::put('/{id}', [JamKerjaController::class, 'update'])->name('update');
            Route::delete('/{id}', [JamKerjaController::class, 'destroy'])->name('destroy');
        });

        // --------------------
        // Shift Pegawai
        // --------------------
        Route::prefix('shift-pegawai')->name('shift-pegawai.')->group(function () {
            Route::get('/', [ShiftPegawaiController::class, 'index'])->name('index');
            Route::get('/non-shift-users', [ShiftPegawaiController::class, 'nonShiftUsers'])->name('non-shift-users');
            Route::post('/assign', [ShiftPegawaiController::class, 'assign'])->name('assign');
            Route::delete('/{id}', [ShiftPegawaiController::class, 'remove'])->name('remove');
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
            Route::post('/{id}/push', [PengumumanController::class, 'sendPush'])->name('push');
            Route::post('/broadcast-custom', [PengumumanController::class, 'broadcastCustom'])->name('broadcast-custom');
            Route::post('/upload-image', [PengumumanController::class, 'uploadImage'])->name('upload-image');
            Route::post('/reorder', [PengumumanController::class, 'reorder'])->name('reorder');
        });

        // --------------------
        // Kendala Perangkat
        // --------------------
        Route::get('/kendala-perangkat', [\App\Http\Controllers\Admin\DeviceIssueController::class, 'index'])->name('device-issues.index');
        Route::post('/kendala-perangkat/{id}/resolve', [\App\Http\Controllers\Admin\DeviceIssueController::class, 'resolve'])->name('device-issues.resolve');
        Route::post('/kendala-perangkat/user/{userId}/resolve', [\App\Http\Controllers\Admin\DeviceIssueController::class, 'resolveUser'])->name('device-issues.resolve-user');

        // --------------------
        // Pengaturan
        // --------------------
        Route::get('/pengaturan', function () {
            return view('admin.pengaturan', [
                'settings' => \App\Models\AppSetting::all()->pluck('value', 'key'),
            ]);
        })->name('pengaturan.index');

        Route::post('/pengaturan/upload-logo', function (\Illuminate\Http\Request $request) {
            $request->validate(['app_logo' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:2048']);
            $file = $request->file('app_logo');
            $path = $file->store('logo', 'public');
            $old = \App\Models\AppSetting::getValue('app_logo');
            if ($old && \Illuminate\Support\Facades\Storage::disk('public')->exists($old)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($old);
            }
            \App\Models\AppSetting::setValue('app_logo', $path);
            return redirect()->route('admin.pengaturan.index')->with('success', 'Logo berhasil diperbarui');
        })->name('pengaturan.upload-logo');

        Route::delete('/pengaturan/remove-logo', function () {
            $old = \App\Models\AppSetting::getValue('app_logo');
            if ($old && \Illuminate\Support\Facades\Storage::disk('public')->exists($old)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($old);
            }
            \App\Models\AppSetting::setValue('app_logo', '');
            return redirect()->route('admin.pengaturan.index')->with('success', 'Logo berhasil dihapus');
        })->name('pengaturan.remove-logo');

        Route::post('/pengaturan', function (\Illuminate\Http\Request $request) {
            \App\Models\AppSetting::setValue('disable_presensi_hari_libur', $request->boolean('disable_presensi_hari_libur') ? '1' : '0');
            \App\Models\AppSetting::setValue('enable_face_detection', $request->boolean('enable_face_detection') ? '1' : '0');
            \App\Models\AppSetting::setValue('face_detection_mode', $request->input('face_detection_mode', 'all'));
            \App\Models\AppSetting::setValue('face_detection_users_except', json_encode($request->input('face_detection_users_except', [])));
            \App\Models\AppSetting::setValue('face_detection_users_only', json_encode($request->input('face_detection_users_only', [])));
            \App\Models\AppSetting::setValue('require_masuk_before_pulang', $request->boolean('require_masuk_before_pulang') ? '1' : '0');
            \App\Models\AppSetting::setValue('enable_work_timer', $request->boolean('enable_work_timer') ? '1' : '0');
            \App\Models\AppSetting::setValue('enable_absen_darurat', $request->boolean('enable_absen_darurat') ? '1' : '0');
            \App\Models\AppSetting::setValue('absen_darurat_mode', $request->input('absen_darurat_mode', 'all'));
            \App\Models\AppSetting::setValue('absen_darurat_users_except', json_encode($request->input('absen_darurat_users_except', [])));
            \App\Models\AppSetting::setValue('absen_darurat_users_only', json_encode($request->input('absen_darurat_users_only', [])));
            return redirect()->route('admin.pengaturan.index')->with('success', 'Pengaturan berhasil disimpan');
        })->name('pengaturan.update');
    });

    // --------------------
    // OPERATOR
    // --------------------
    Route::prefix('operator')->name('operator.')->middleware(['checkrole:operator', 'logactivity'])->group(function () {
        Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard');

        // Pengaturan (logo, instansi, app settings)
        Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
            Route::get('/', [OperatorPengaturanController::class, 'index'])->name('index');
            Route::post('/instansi', [OperatorPengaturanController::class, 'updateInstansi'])->name('update-instansi');
            Route::post('/upload-logo', [OperatorPengaturanController::class, 'uploadLogo'])->name('upload-logo');
            Route::delete('/remove-logo', [OperatorPengaturanController::class, 'removeLogo'])->name('remove-logo');
            Route::post('/settings', [OperatorPengaturanController::class, 'updateSettings'])->name('update-settings');
        });

        // Log Aktivitas
        Route::get('/activity-logs', [OperatorActivityLogController::class, 'index'])->name('activity-logs.index');

        // Tracking User
        Route::get('/tracking', [OperatorTrackingController::class, 'index'])->name('tracking.index');
        Route::get('/tracking/live-map', [OperatorTrackingController::class, 'liveMap'])->name('tracking.live-map');
        Route::get('/tracking/locations-json', [OperatorTrackingController::class, 'locationsJson'])->name('tracking.locations-json');
        Route::get('/tracking/{userId}', [OperatorTrackingController::class, 'detail'])->name('tracking.detail');

        // Akun Operator
        Route::get('/akun', [OperatorDashboardController::class, 'akun'])->name('akun');
        Route::post('/akun/update-email', [OperatorDashboardController::class, 'updateEmail'])->name('akun.update-email');
        Route::post('/akun/update-password', [OperatorDashboardController::class, 'updatePassword'])->name('akun.update-password');

        // Database Presensi
        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [OperatorPresensiController::class, 'index'])->name('index');
            Route::put('/{id}', [OperatorPresensiController::class, 'update'])->name('update');
            Route::delete('/{id}', [OperatorPresensiController::class, 'destroy'])->name('destroy');
        });
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