<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presensi;
use App\Models\PengajuanPresensi;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $excludeTester = function($q) { $q->where('is_tester', false); };

        $jumlahHadir = Presensi::whereHas('user', $excludeTester)
            ->whereDate('tanggal', $today)
            ->where('jenis', 'masuk')
            ->where('status', 'approved')
            ->distinct('user_id')
            ->count('user_id');

        // Total pegawai
        $jumlahPegawai = User::nonTester()->count();

        // Total pengajuan pending
        $jumlahPengajuan = PengajuanPresensi::where('status', 'pending')->count();

        $presensiHariIni = Presensi::with('user')
            ->whereHas('user', $excludeTester)
            ->whereDate('tanggal', $today)
            ->where('status', 'approved')
            ->where('is_lembur', false)
            ->orderBy('jam', 'desc')
            ->get();

        foreach ($presensiHariIni as $presensi) {
            $presensi->terlambat = false;
            $presensi->waktu_kurang_menit = 0;
            $presensi->lembur_menit = 0;

            $hari = Carbon::parse($presensi->tanggal)->format('l');
            $jadwal = $presensi->user->getJadwalKerja($presensi->tanggal);
            $jamMasuk = $jadwal['jam_masuk'];
            $jamPulang = $jadwal['jam_pulang'];

            $batasTelat = date('H:i:s', strtotime($jamMasuk) + 60);

            if ($presensi->jenis === 'masuk' && $presensi->jam > $batasTelat) {
                $presensi->terlambat = true;
                $presensi->waktu_kurang_menit = intval((strtotime($presensi->jam) - strtotime($jamMasuk)) / 60);
            }

            if ($presensi->jenis === 'pulang' && $presensi->jam < $jamPulang) {
                $presensi->waktu_kurang_menit = intval((strtotime($jamPulang) - strtotime($presensi->jam)) / 60);
            }

            if ($presensi->jenis === 'pulang' && in_array($hari, ['Saturday', 'Sunday'])) {
                if ($presensi->jam > $jamPulang) {
                    $presensi->lembur_menit = intval((strtotime($presensi->jam) - strtotime($jamPulang)) / 60);
                }
            }
        }

        // Pengajuan pending dengan data lengkap untuk modal
        $pengajuanPending = PengajuanPresensi::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($pengajuan) {
                // Tambahkan URL bukti untuk modal
                $pengajuan->bukti_url = $pengajuan->bukti ? asset('storage/' . $pengajuan->bukti) : null;
                return $pengajuan;
            });

        // Presensi pending (untuk admin approve/reject) dengan data lengkap
        $presensiPending = Presensi::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Cuti/DL pending
        try {
            $cutiPending = Cuti::with('user')
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get();
        } catch (\Exception $e) {
            $cutiPending = collect();
        }

        // Jika request AJAX, return JSON response untuk auto-refresh
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'jumlahHadir' => $jumlahHadir,
                    'jumlahPegawai' => $jumlahPegawai,
                    'jumlahPengajuan' => $jumlahPengajuan,
                    'presensiHariIni' => $this->formatPresensiHariIni($presensiHariIni),
                    'pengajuanPending' => $this->formatPengajuanPending($pengajuanPending),
                    'presensiPending' => $this->formatPresensiPending($presensiPending),
                ]
            ]);
        }

        // Lembur hari ini
        $lemburHariIni = Presensi::with('user')
            ->whereDate('tanggal', $today)
            ->where('is_lembur', true)
            ->where('status', 'approved')
            ->orderBy('jam', 'desc')
            ->get();

        // Statistik kehadiran 7 hari terakhir
        $chartLabels = [];
        $chartHadir = [];
        $chartTelat = [];
        $chartLembur = [];
        $chartDaruratMasuk = [];
        $chartDaruratPulang = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D d/m');

            $hadirCount = Presensi::whereDate('tanggal', $date)
                ->where('jenis', 'masuk')->where('is_lembur', false)
                ->where('status', 'approved')->distinct('user_id')->count('user_id');
            $chartHadir[] = $hadirCount;

            $telatCount = 0;
            $masukRecords = Presensi::with('user')->whereDate('tanggal', $date)
                ->where('jenis', 'masuk')->where('is_lembur', false)
                ->where('status', 'approved')->get();
            foreach ($masukRecords as $m) {
                $jadwal = $m->user->getJadwalKerja($date);
                $batas = date('H:i:s', strtotime($jadwal['jam_masuk']) + 60);
                if ($m->jam > $batas) $telatCount++;
            }
            $chartTelat[] = $telatCount;

            $lemburCount = Presensi::whereDate('tanggal', $date)
                ->where('jenis', 'masuk')->where('is_lembur', true)
                ->where('status', 'approved')->distinct('user_id')->count('user_id');
            $chartLembur[] = $lemburCount;

            $chartDaruratMasuk[] = Presensi::whereDate('tanggal', $date)
                ->where('jenis', 'masuk')->where('is_darurat', true)
                ->distinct('user_id')->count('user_id');
            $chartDaruratPulang[] = Presensi::whereDate('tanggal', $date)
                ->where('jenis', 'pulang')->where('is_darurat', true)
                ->distinct('user_id')->count('user_id');
        }

        // Performa pegawai bulan ini — pakai PerformaController agar selalu sinkron
        $performaData = app(\App\Http\Controllers\Admin\PerformaController::class)
            ->getPerformaData((int) date('m'), (int) date('Y'));
        $performaList = collect($performaData['performa'] ?? [])
            ->sortByDesc('performa')
            ->take(10)
            ->map(function ($p) {
                return [
                    'user' => (object) ['name' => $p['nama'], 'nip' => $p['nip']],
                    'hadir' => $p['hadir'],
                    'telat' => $p['telat'],
                    'persen' => $p['performa'],
                    'total_menit_kerja' => $p['total_menit_kerja'],
                ];
            })
            ->values()
            ->all();

        return view('admin.dashboard', compact(
            'jumlahHadir',
            'jumlahPegawai',
            'jumlahPengajuan',
            'presensiHariIni',
            'pengajuanPending',
            'presensiPending',
            'cutiPending',
            'lemburHariIni',
            'chartLabels',
            'chartHadir',
            'chartTelat',
            'chartLembur',
            'chartDaruratMasuk',
            'chartDaruratPulang',
            'performaList'
        ));
    }

    /**
     * Format data presensi hari ini untuk response JSON
     */
    private function formatPresensiHariIni($presensiHariIni)
    {
        return $presensiHariIni->map(function ($presensi) {
            return [
                'id' => $presensi->id,
                'user_name' => $presensi->user->name ?? 'N/A',
                'jenis' => $presensi->jenis,
                'jam' => $presensi->jam,
                'terlambat' => $presensi->terlambat ?? false,
                'waktu_kurang_menit' => $presensi->waktu_kurang_menit ?? 0,
                'status_badge' => $this->getStatusBadge($presensi),
            ];
        });
    }

    /**
     * Format data pengajuan pending untuk response JSON
     */
    private function formatPengajuanPending($pengajuanPending)
    {
        return $pengajuanPending->map(function ($pengajuan) {
            return [
                'id' => $pengajuan->id,
                'user_name' => $pengajuan->user->name ?? 'N/A',
                'tanggal' => $pengajuan->tanggal,
                'tanggal_formatted' => Carbon::parse($pengajuan->tanggal)->translatedFormat('d M Y'),
                'jenis' => $pengajuan->jenis,
                'waktu' => $pengajuan->waktu ? Carbon::parse($pengajuan->waktu)->format('H:i') : '-',
                'alasan' => $pengajuan->alasan,
                'bukti' => $pengajuan->bukti,
                'bukti_url' => $pengajuan->bukti_url,
                'approve_url' => route('admin.pengajuan.approve', $pengajuan->id),
                'reject_url' => route('admin.pengajuan.reject', $pengajuan->id),
            ];
        });
    }

    /**
     * Format data presensi pending untuk response JSON
     */
    private function formatPresensiPending($presensiPending)
    {
        return $presensiPending->map(function ($presensi) {
            return [
                'id' => $presensi->id,
                'user_name' => $presensi->user->name ?? 'N/A',
                'tanggal' => $presensi->tanggal,
                'tanggal_formatted' => Carbon::parse($presensi->tanggal)->translatedFormat('d M Y'),
                'jenis' => $presensi->jenis,
                'jam' => $presensi->jam,
                'lokasi' => $presensi->lokasi ?? 'Tidak ada lokasi',
                'foto' => $presensi->foto,
                'foto_url' => $presensi->foto ? asset('storage/' . $presensi->foto) : null,
                'approve_url' => route('admin.presensi.approve', $presensi->id),
                'reject_url' => route('admin.presensi.reject', $presensi->id),
            ];
        });
    }

    /**
     * Get status badge untuk presensi
     */
    private function getStatusBadge($presensi)
    {
        if ($presensi->jenis === 'masuk') {
            return $presensi->terlambat ? 'Terlambat' : 'Tepat Waktu';
        } elseif ($presensi->jenis === 'pulang') {
            return $presensi->waktu_kurang_menit > 0 ? 'Waktu Kurang' : 'Tepat Waktu';
        }
        return '-';
    }

    // ----- Fungsi Approve/Reject Pengajuan -----
    public function approve($id)
    {
        try {
            $pengajuan = PengajuanPresensi::findOrFail($id);

            // Tentukan jam
            $jam = $pengajuan->waktu ?? null;
            if ($jam) {
                // Normalisasi ke H:i:s — hindari double :00
                $jam = Carbon::parse($jam)->format('H:i:s');
            } else {
                $hari = Carbon::parse($pengajuan->tanggal)->format('l');
                $jam = $pengajuan->jenis === 'masuk' ? '07:30:00'
                    : ($hari === 'Friday' ? '16:30:00' : '16:00:00');
            }

            Presensi::updateOrCreate(
                [
                    'user_id' => $pengajuan->user_id,
                    'tanggal' => $pengajuan->tanggal,
                    'jenis' => $pengajuan->jenis,
                    'is_lembur' => false,
                ],
                [
                    'jam' => $jam,
                    'status' => 'approved',
                ]
            );

            // Update status pengajuan
            $pengajuan->status = 'approved';
            $pengajuan->approved_by = auth()->id();
            $pengajuan->approved_at = now();
            $pengajuan->save();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Pengajuan disetujui']);
            }
            return redirect()->back()->with('success', 'Pengajuan berhasil disetujui.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal approve: ' . $e->getMessage());
        }
    }


    public function reject($id)
    {
        try {
            $pengajuan = PengajuanPresensi::findOrFail($id);
            $pengajuan->status = 'rejected';
            $pengajuan->approved_by = auth()->id();
            $pengajuan->approved_at = now();
            $pengajuan->save();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Pengajuan ditolak']);
            }
            return redirect()->back()->with('success', 'Pengajuan ditolak.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ----- Cuti Approve/Reject -----
    public function approveCuti($id)
    {
        $cuti = Cuti::findOrFail($id);
        $cuti->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Cuti/DL disetujui.']);
    }

    public function rejectCuti($id)
    {
        $cuti = Cuti::findOrFail($id);
        $cuti->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Cuti/DL ditolak.']);
    }

    // ----- Fungsi Approve/Reject Presensi Pending -----
    public function approvePresensi($id)
    {
        try {
            $presensi = Presensi::findOrFail($id);
            $presensi->status = 'approved';
            $presensi->save();

            if (request()->ajax()) return response()->json(['success' => true, 'message' => 'Presensi disetujui']);
            return redirect()->back()->with('success', 'Presensi berhasil disetujui.');
        } catch (\Exception $e) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectPresensi($id)
    {
        try {
            $presensi = Presensi::findOrFail($id);
            $presensi->status = 'rejected';
            $presensi->save();

            if (request()->ajax()) return response()->json(['success' => true, 'message' => 'Presensi ditolak']);
            return redirect()->back()->with('success', 'Presensi ditolak.');
        } catch (\Exception $e) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * API untuk mendapatkan data dashboard (untuk auto-refresh)
     */
    public function getDashboardData()
    {
        $today = Carbon::today()->toDateString();

        // Jumlah pegawai hadir hari ini
        $jumlahHadir = Presensi::whereDate('tanggal', $today)
            ->where('jenis', 'masuk')
            ->where('status', 'approved')
            ->distinct('user_id')
            ->count('user_id');

        // Total pegawai
        $jumlahPegawai = User::nonTester()->count();

        // Total pengajuan pending
        $jumlahPengajuan = PengajuanPresensi::where('status', 'pending')->count();

        // Presensi hari ini
        $presensiHariIni = Presensi::with('user')
            ->whereDate('tanggal', $today)
            ->where('status', 'approved')
            ->orderBy('jam', 'asc')
            ->get();

        foreach ($presensiHariIni as $presensi) {
            $presensi->terlambat = false;
            $presensi->waktu_kurang_menit = 0;

            $jadwal = $presensi->user->getJadwalKerja($presensi->tanggal);
            $batasTelat = date('H:i:s', strtotime($jadwal['jam_masuk']) + 60);

            if ($presensi->jenis === 'masuk' && $presensi->jam > $batasTelat) {
                $presensi->terlambat = true;
                $presensi->waktu_kurang_menit = intval((strtotime($presensi->jam) - strtotime($jadwal['jam_masuk'])) / 60);
            }

            if ($presensi->jenis === 'pulang' && $presensi->jam < $jadwal['jam_pulang']) {
                $presensi->waktu_kurang_menit = intval((strtotime($jadwal['jam_pulang']) - strtotime($presensi->jam)) / 60);
            }
        }

        // Pengajuan pending
        $pengajuanPending = PengajuanPresensi::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($pengajuan) {
                $pengajuan->bukti_url = $pengajuan->bukti ? asset('storage/' . $pengajuan->bukti) : null;
                return $pengajuan;
            });

        // Presensi pending
        $presensiPending = Presensi::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'jumlahHadir' => $jumlahHadir,
                'jumlahPegawai' => $jumlahPegawai,
                'jumlahPengajuan' => $jumlahPengajuan,
                'presensiHariIni' => $this->formatPresensiHariIni($presensiHariIni),
                'pengajuanPending' => $this->formatPengajuanPending($pengajuanPending),
                'presensiPending' => $this->formatPresensiPending($presensiPending),
            ],
            'last_updated' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get detail presensi untuk modal
     */
    public function getPresensiDetail($id)
    {
        try {
            $presensi = Presensi::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $presensi->id,
                    'user_name' => $presensi->user->name ?? 'N/A',
                    'tanggal' => Carbon::parse($presensi->tanggal)->translatedFormat('d M Y'),
                    'jenis' => $presensi->jenis,
                    'jam' => $presensi->jam,
                    'lokasi' => $presensi->lokasi ?? 'Tidak ada lokasi',
                    'foto' => $presensi->foto,
                    'foto_url' => $presensi->foto ? asset('storage/' . $presensi->foto) : null,
                    'status' => $presensi->status,
                    'approve_url' => route('admin.presensi.approve', $presensi->id),
                    'reject_url' => route('admin.presensi.reject', $presensi->id),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data presensi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get detail pengajuan untuk modal
     */
    public function getPengajuanDetail($id)
    {
        try {
            $pengajuan = PengajuanPresensi::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pengajuan->id,
                    'user_name' => $pengajuan->user->name ?? 'N/A',
                    'tanggal' => Carbon::parse($pengajuan->tanggal)->translatedFormat('d M Y'),
                    'jenis' => $pengajuan->jenis,
                    'alasan' => $pengajuan->alasan ?? 'Tidak ada alasan',
                    'bukti' => $pengajuan->bukti,
                    'bukti_url' => $pengajuan->bukti ? asset('storage/' . $pengajuan->bukti) : null,
                    'status' => $pengajuan->status,
                    'approve_url' => route('admin.pengajuan.approve', $pengajuan->id),
                    'reject_url' => route('admin.pengajuan.reject', $pengajuan->id),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ], 404);
        }
    }

    public function getChartData(Request $request)
    {
        $days = (int) $request->input('days', 7);
        $allowed = [7, 30, 90, 180, 365];
        if (!in_array($days, $allowed)) $days = 7;

        $startDate = Carbon::today()->subDays($days - 1);
        $endDate = Carbon::today();
        $format = $days <= 30 ? 'D d/m' : 'd/m';

        $allPresensi = Presensi::with('user')
            ->whereHas('user', function($q) { $q->where('is_tester', false); })
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'approved')
            ->get();

        $masukReguler = $allPresensi->where('jenis', 'masuk')->where('is_lembur', false)->groupBy('tanggal');
        $masukLembur = $allPresensi->where('jenis', 'masuk')->where('is_lembur', true)->groupBy('tanggal');
        $daruratMasukByDate = $allPresensi->where('jenis', 'masuk')->where('is_darurat', true)->groupBy('tanggal');
        $daruratPulangByDate = $allPresensi->where('jenis', 'pulang')->where('is_darurat', true)->groupBy('tanggal');

        $labels = [];
        $hadir = [];
        $telat = [];
        $lembur = [];
        $daruratMasuk = [];
        $daruratPulang = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $key = $date->toDateString();
            $labels[] = $date->translatedFormat($format);

            $dayReguler = $masukReguler->get($key, collect());
            $hadir[] = $dayReguler->unique('user_id')->count();

            $telatCount = 0;
            foreach ($dayReguler as $m) {
                if (!$m->user) continue;
                $jadwal = $m->user->getJadwalKerja($date);
                $batas = date('H:i:s', strtotime($jadwal['jam_masuk']) + 60);
                if ($m->jam > $batas) $telatCount++;
            }
            $telat[] = $telatCount;

            $lembur[] = $masukLembur->get($key, collect())->unique('user_id')->count();
            $daruratMasuk[] = $daruratMasukByDate->get($key, collect())->unique('user_id')->count();
            $daruratPulang[] = $daruratPulangByDate->get($key, collect())->unique('user_id')->count();
        }

        return response()->json(compact('labels', 'hadir', 'telat', 'lembur', 'daruratMasuk', 'daruratPulang'));
    }
}
