<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presensi;
use App\Models\PengajuanPresensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // Jumlah pegawai hadir hari ini (masuk yang approved)
        $jumlahHadir = Presensi::whereDate('tanggal', $today)
            ->where('jenis', 'masuk')
            ->where('status', 'approved')
            ->distinct('user_id') // hitung unique user
            ->count('user_id');

        // Total pegawai
        $jumlahPegawai = User::count();

        // Total pengajuan pending
        $jumlahPengajuan = PengajuanPresensi::where('status', 'pending')->count();

        // Presensi hari ini (masuk & pulang, hanya yang approved)
        $presensiHariIni = Presensi::with('user')
            ->whereDate('tanggal', $today)
            ->where('status', 'approved') // Hanya yang approved
            ->orderBy('jam', 'asc')
            ->get();

        // Tandai terlambat + hitung waktu kurang
        $jamMasukStandard = '07:30:00';
        $jamPulangStandard = '16:00:00';
        foreach ($presensiHariIni as $presensi) {
            $presensi->terlambat = false;
            $presensi->waktu_kurang_menit = 0;

            if ($presensi->jenis === 'masuk' && $presensi->jam > $jamMasukStandard) {
                $presensi->terlambat = true;
                $presensi->waktu_kurang_menit = intval((strtotime($presensi->jam) - strtotime($jamMasukStandard)) / 60);
            }

            if ($presensi->jenis === 'pulang' && $presensi->jam < $jamPulangStandard) {
                $presensi->waktu_kurang_menit = intval((strtotime($jamPulangStandard) - strtotime($presensi->jam)) / 60);
            }
        }

        // Pengajuan pending
        $pengajuanPending = PengajuanPresensi::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Presensi pending (untuk admin approve/reject)
        $presensiPending = Presensi::with('user')
            ->where('status', 'pending') // hanya pending
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'jumlahHadir',
            'jumlahPegawai',
            'jumlahPengajuan',
            'presensiHariIni',
            'pengajuanPending',
            'presensiPending'
        ));
    }

    // ----- Fungsi Approve/Reject Pengajuan -----
    public function approve($id)
    {
        $pengajuan = PengajuanPresensi::findOrFail($id);

        DB::transaction(function () use ($pengajuan) {
            $jamMasukDefault = '07:30:00';
            $jamPulangDefault = '16:00:00';

            if ($pengajuan->jenis === 'masuk' || $pengajuan->jenis === 'kedua') {
                Presensi::updateOrCreate(
                    [
                        'user_id' => $pengajuan->user_id,
                        'tanggal' => $pengajuan->tanggal,
                        'jenis' => 'masuk'
                    ],
                    [
                        'jam' => $jamMasukDefault,
                        'status' => 'approved',
                        'foto' => $pengajuan->bukti ?? null,
                        'lokasi' => $pengajuan->lokasi ?? null
                    ]
                );
            }

            if ($pengajuan->jenis === 'pulang' || $pengajuan->jenis === 'kedua') {
                Presensi::updateOrCreate(
                    [
                        'user_id' => $pengajuan->user_id,
                        'tanggal' => $pengajuan->tanggal,
                        'jenis' => 'pulang'
                    ],
                    [
                        'jam' => $jamPulangDefault,
                        'status' => 'approved',
                        'foto' => $pengajuan->bukti ?? null,
                        'lokasi' => $pengajuan->lokasi ?? null
                    ]
                );
            }

            $pengajuan->status = 'approved';
            $pengajuan->save();
        });

        return redirect()->back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject($id)
    {
        $pengajuan = PengajuanPresensi::findOrFail($id);
        $pengajuan->status = 'rejected';
        $pengajuan->save();

        return redirect()->back()->with('success', 'Pengajuan ditolak.');
    }

    // ----- Fungsi Approve/Reject Presensi Pending -----
    public function approvePresensi($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->status = 'approved';
        $presensi->save();

        return redirect()->back()->with('success', 'Presensi berhasil disetujui.');
    }

    public function rejectPresensi($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->status = 'rejected';
        $presensi->save();

        return redirect()->back()->with('success', 'Presensi ditolak.');
    }
}
