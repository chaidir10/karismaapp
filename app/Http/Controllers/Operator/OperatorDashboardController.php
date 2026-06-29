<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\DeviceIssue;
use App\Models\Instansi;
use App\Models\JamKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\JamShift;
use App\Models\PengajuanPresensi;
use App\Models\Pengumuman;
use App\Models\Presensi;
use App\Models\User;
use App\Models\WilayahKerja;
use Carbon\Carbon;

class OperatorDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalPegawai = User::where('role', 'pegawai')->count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalOperator = User::where('role', 'operator')->count();
        $totalUsers = User::count();

        $presensiHariIni = Presensi::whereDate('tanggal', $today)->count();
        $presensiMasukHariIni = Presensi::whereDate('tanggal', $today)->where('jenis', 'masuk')->count();
        $presensiPending = Presensi::where('status', 'pending')->count();
        $pengajuanPending = PengajuanPresensi::where('status', 'pending')->count();

        $deviceIssuesOpen = DeviceIssue::whereNull('resolved_at')->count();
        $wilayahCount = WilayahKerja::count();
        $jamKerjaCount = JamKerja::count();
        $shiftCount = JamShift::count();
        $pengumumanAktif = Pengumuman::where('is_active', true)->count();

        $instansi = Instansi::first();

        $appSettings = AppSetting::query()
            ->whereIn('key', [
                'disable_presensi_hari_libur', 'enable_face_detection',
                'face_detection_mode', 'require_masuk_before_pulang',
                'enable_work_timer', 'enable_absen_darurat', 'absen_darurat_mode',
            ])
            ->pluck('value', 'key');

        $recentActivities = ActivityLog::with('user')
            ->latest('created_at')
            ->take(10)
            ->get();

        $onlineUsers = ActivityLog::select('user_id')
            ->where('created_at', '>=', now()->subMinutes(15))
            ->distinct()
            ->count();

        $presensi7Hari = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $presensi7Hari[] = [
                'tanggal' => $date->format('d/m'),
                'masuk' => Presensi::whereDate('tanggal', $date)->where('jenis', 'masuk')->count(),
            ];
        }

        return view('operator.dashboard', compact(
            'totalPegawai', 'totalAdmin', 'totalOperator', 'totalUsers',
            'presensiHariIni', 'presensiMasukHariIni', 'presensiPending',
            'pengajuanPending', 'deviceIssuesOpen', 'wilayahCount',
            'jamKerjaCount', 'shiftCount', 'pengumumanAktif',
            'instansi', 'appSettings', 'recentActivities',
            'onlineUsers', 'presensi7Hari'
        ));
    }

    public function akun()
    {
        return view('operator.akun');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        Auth::user()->update(['email' => $request->email]);

        return back()->with('success', 'Email berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->with('error', 'Password lama tidak sesuai');
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui');
    }
}
