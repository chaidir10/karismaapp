<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\DeviceIssue;
use App\Models\JamKerja;
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

        $presensiHariIni = Presensi::whereDate('tanggal', $today)->count();
        $presensiPending = Presensi::where('status', 'pending')->count();
        $pengajuanPending = PengajuanPresensi::where('status', 'pending')->count();

        $deviceIssuesOpen = DeviceIssue::whereNull('resolved_at')->count();
        $wilayahCount = WilayahKerja::count();
        $jamKerjaCount = JamKerja::count();
        $shiftCount = JamShift::count();
        $pengumumanAktif = Pengumuman::count();

        $adminTerbaru = User::whereIn('role', ['admin', 'operator'])
            ->latest()
            ->take(6)
            ->get();

        $pengajuanTerbaru = PengajuanPresensi::with('user')
            ->latest()
            ->take(8)
            ->get();

        $deviceIssueModel = new DeviceIssue();
        $deviceIssueOrderColumn = in_array('created_at', $deviceIssueModel->getConnection()->getSchemaBuilder()->getColumnListing($deviceIssueModel->getTable()))
            ? 'created_at'
            : 'id';

        $deviceIssuesTerbaru = DeviceIssue::with('user')
            ->orderByDesc($deviceIssueOrderColumn)
            ->take(8)
            ->get();

        $appSettings = AppSetting::query()
            ->whereIn('key', [
                'disable_presensi_hari_libur',
                'enable_face_detection',
                'face_detection_mode',
                'require_masuk_before_pulang',
                'enable_work_timer',
                'enable_absen_darurat',
                'absen_darurat_mode',
            ])
            ->pluck('value', 'key');

        return view('operator.dashboard', compact(
            'totalPegawai',
            'totalAdmin',
            'totalOperator',
            'presensiHariIni',
            'presensiPending',
            'pengajuanPending',
            'deviceIssuesOpen',
            'wilayahCount',
            'jamKerjaCount',
            'shiftCount',
            'pengumumanAktif',
            'adminTerbaru',
            'pengajuanTerbaru',
            'deviceIssuesTerbaru',
            'appSettings'
        ));
    }
}
