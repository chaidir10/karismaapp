<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PengajuanPresensi as Pengajuan;

class DashboardSuperAdminController extends Controller
{
    /**
     * Tampilkan dashboard superadmin.
     */
    public function index()
    {
        // Total admin (role = admin)
        $totalAdmin = User::where('role', 'admin')->count();

        // Total pegawai
        $totalPegawai = User::where('role', 'pegawai')->count();

        // Pengajuan pending
        $pengajuanPending = Pengajuan::where('status', 'pending')->count();

        // Daftar admin
        $admins = User::where('role', 'admin')->orderBy('name')->get();

        // Daftar pengajuan pending (detail)
        $pengajuanList = Pengajuan::with('user')->where('status', 'pending')->orderBy('tanggal', 'desc')->get();

        return view('superadmin.dashboard', compact(
            'totalAdmin',
            'totalPegawai',
            'pengajuanPending',
            'admins',
            'pengajuanList'
        ));
    }
}
