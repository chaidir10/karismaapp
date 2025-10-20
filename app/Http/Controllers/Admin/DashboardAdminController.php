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

        // === Data utama untuk ringkasan ===
        $jumlahPegawai = User::count();

        $jumlahHadir = Presensi::whereDate('tanggal', $today)
            ->where('jenis', 'masuk')
            ->where('status', 'approved')
            ->distinct('user_id')
            ->count('user_id');

        $jumlahPengajuan = PengajuanPresensi::where('status', 'pending')->count();

        // === Presensi hari ini (approved) ===
        $presensiHariIni = Presensi::with('user')
            ->whereDate('tanggal', $today)
            ->where('status', 'approved')
            ->orderBy('jam', 'asc')
            ->get();

        $jamMasukStandard  = '07:30:00';
        $jamPulangStandard = '16:00:00';

        foreach ($presensiHariIni as $presensi) {
            $presensi->terlambat = false;
            $presensi->waktu_kurang_menit = 0;
            $presensi->lembur_menit = 0;

            $hari = Carbon::parse($presensi->tanggal)->format('l');

            // Cek keterlambatan
            if ($presensi->jenis === 'masuk' && $presensi->jam > '07:30:59') {
                $presensi->terlambat = true;
                $presensi->waktu_kurang_menit = intval((strtotime($presensi->jam) - strtotime($jamMasukStandard)) / 60);
            }

            // Cek pulang lebih awal
            if ($presensi->jenis === 'pulang' && $presensi->jam < $jamPulangStandard) {
                $presensi->waktu_kurang_menit = intval((strtotime($jamPulangStandard) - strtotime($presensi->jam)) / 60);
            }

            // Cek lembur di hari libur/weekend
            if ($presensi->jenis === 'pulang' && in_array($hari, ['Saturday', 'Sunday'])) {
                if ($presensi->jam > $jamPulangStandard) {
                    $presensi->lembur_menit = intval((strtotime($presensi->jam) - strtotime($jamPulangStandard)) / 60);
                }
            }
        }

        // === Pengajuan pending ===
        $pengajuanPending = PengajuanPresensi::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->get();

        // === Presensi pending ===
        $presensiPending = Presensi::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Kirim semua data ke view
        return view('admin.dashboard', compact(
            'jumlahPegawai',
            'jumlahHadir',
            'jumlahPengajuan',
            'presensiHariIni',
            'pengajuanPending',
            'presensiPending'
        ));
    }

    // ====== APPROVE / REJECT PENGAJUAN ======
    public function approve($id)
    {
        $pengajuan = PengajuanPresensi::findOrFail($id);

        DB::transaction(function () use ($pengajuan) {
            $jamMasukDefault  = '07:30:00';
            $jamPulangDefault = '16:00:00';

            if (in_array($pengajuan->jenis, ['masuk', 'keduanya'])) {
                Presensi::updateOrCreate(
                    [
                        'user_id' => $pengajuan->user_id,
                        'tanggal' => $pengajuan->tanggal,
                        'jenis'   => 'masuk'
                    ],
                    [
                        'jam'     => $jamMasukDefault,
                        'status'  => 'approved',
                        'foto'    => $pengajuan->bukti ?? null,
                        'lokasi'  => $pengajuan->lokasi ?? null,
                    ]
                );
            }

            if (in_array($pengajuan->jenis, ['pulang', 'keduanya'])) {
                Presensi::updateOrCreate(
                    [
                        'user_id' => $pengajuan->user_id,
                        'tanggal' => $pengajuan->tanggal,
                        'jenis'   => 'pulang'
                    ],
                    [
                        'jam'     => $jamPulangDefault,
                        'status'  => 'approved',
                        'foto'    => $pengajuan->bukti ?? null,
                        'lokasi'  => $pengajuan->lokasi ?? null,
                    ]
                );
            }

            $pengajuan->update(['status' => 'approved']);
        });

        return redirect()->back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject($id)
    {
        $pengajuan = PengajuanPresensi::findOrFail($id);
        $pengajuan->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Pengajuan ditolak.');
    }

    // ====== APPROVE / REJECT PRESENSI ======
    public function approvePresensi($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Presensi berhasil disetujui.');
    }

    public function rejectPresensi($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Presensi ditolak.');
    }
}
