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
            ->distinct('user_id')
            ->count('user_id');

        // Total pegawai
        $jumlahPegawai = User::count();

        // Total pengajuan pending
        $jumlahPengajuan = PengajuanPresensi::where('status', 'pending')->count();

        // Presensi hari ini (reguler saja, bukan lembur)
        $presensiHariIni = Presensi::with('user')
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
        }

        // Performa pegawai bulan ini (sinkron dengan halaman Performa)
        $startMonth = Carbon::now()->startOfMonth();
        $endToday = Carbon::today();

        $holidays = $this->fetchHolidays($startMonth->year);

        $hariKerjaBulanIni = 0;
        for ($d = $startMonth->copy(); $d->lte($endToday); $d->addDay()) {
            if ($d->dayOfWeek != 0 && $d->dayOfWeek != 6 && !in_array($d->format('Y-m-d'), $holidays)) {
                $hariKerjaBulanIni++;
            }
        }

        $allUsers = User::where('role', '!=', 'superadmin')->get();
        $performaList = [];

        foreach ($allUsers as $u) {
            $presensiUser = Presensi::where('user_id', $u->id)
                ->whereBetween('tanggal', [$startMonth->format('Y-m-d'), $endToday->format('Y-m-d')])
                ->where('status', 'approved')->where('is_lembur', false)
                ->get()->groupBy('tanggal');

            $hadir = 0; $tepatMasuk = 0; $telat = 0; $pulangTepat = 0; $jamKerjaCukup = 0; $totalMenitKerja = 0;

            for ($d = $startMonth->copy(); $d->lte($endToday); $d->addDay()) {
                $tgl = $d->format('Y-m-d');
                if ($d->dayOfWeek == 0 || $d->dayOfWeek == 6 || in_array($tgl, $holidays)) continue;

                $dayP = $presensiUser->get($tgl);
                if (!$dayP) continue;
                $msk = $dayP->firstWhere('jenis', 'masuk');
                $plg = $dayP->firstWhere('jenis', 'pulang');
                if (!$msk || !$plg) continue;

                $hadir++;
                $jadwal = $u->getJadwalKerja($d);
                $jmDefault = Carbon::createFromTimeString($jadwal['jam_masuk']);
                $jpDefault = Carbon::createFromTimeString($jadwal['jam_pulang']);
                $jmObj = Carbon::parse($msk->jam);
                $jpObj = Carbon::parse($plg->jam);

                if ($jmObj->lt($jmDefault->copy()->addMinute())) { $tepatMasuk++; } else { $telat++; }
                if ($jpObj->gte($jpDefault)) { $pulangTepat++; }

                $dStandar = $jmDefault->diffInMinutes($jpDefault);
                $dAktual = $jmObj->copy()->setSeconds(0)->diffInMinutes($jpObj->copy()->setSeconds(0));
                $totalMenitKerja += $dAktual;
                if ($dAktual >= $dStandar) { $jamKerjaCukup++; }
            }

            if ($hariKerjaBulanIni > 0) {
                $persen = round(
                    (($hadir / $hariKerjaBulanIni) * 25) +
                    (($tepatMasuk / $hariKerjaBulanIni) * 30) +
                    (($pulangTepat / $hariKerjaBulanIni) * 20) +
                    (($jamKerjaCukup / $hariKerjaBulanIni) * 25), 1
                );
            } else {
                $persen = 0;
            }

            $performaList[] = [
                'user' => $u,
                'hadir' => $hadir,
                'telat' => $telat,
                'persen' => $persen,
                'total_menit_kerja' => $totalMenitKerja,
            ];
        }

        usort($performaList, function ($a, $b) {
            if ($b['persen'] != $a['persen']) return $b['persen'] <=> $a['persen'];
            return $b['total_menit_kerja'] <=> $a['total_menit_kerja'];
        });
        $performaList = array_slice($performaList, 0, 10);

        return view('admin.dashboard', compact(
            'jumlahHadir',
            'jumlahPegawai',
            'jumlahPengajuan',
            'presensiHariIni',
            'pengajuanPending',
            'presensiPending',
            'lemburHariIni',
            'chartLabels',
            'chartHadir',
            'chartTelat',
            'chartLembur',
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

            DB::transaction(function () use ($pengajuan) {

                // default jam masuk
                $jamMasukDefault = '07:30:00';

                // tentukan jam pulang berdasarkan hari
                $hari = \Carbon\Carbon::parse($pengajuan->tanggal)->format('l'); // Friday, Monday, etc

                if ($hari === 'Friday') {
                    $jamPulangDefault = '16:30:00';   // Jumat
                } else {
                    $jamPulangDefault = '16:00:00';   // Senin – Kamis
                }

                // Jika pengajuan jam masuk
                if ($pengajuan->jenis === 'masuk' || $pengajuan->jenis === 'keduanya') {
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

                // Jika pengajuan jam pulang
                if ($pengajuan->jenis === 'pulang' || $pengajuan->jenis === 'keduanya') {
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

                // Simpan status pengajuan
                $pengajuan->status = 'approved';
                $pengajuan->approved_by = auth()->id();
                $pengajuan->approved_at = now();
                $pengajuan->save();
            });

            return redirect()->back()->with('success', 'Pengajuan berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

            return redirect()->back()->with('success', 'Pengajuan ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ----- Fungsi Approve/Reject Presensi Pending -----
    public function approvePresensi($id)
    {
        try {
            $presensi = Presensi::findOrFail($id);
            $presensi->status = 'approved';
            $presensi->save();

            return redirect()->back()->with('success', 'Presensi berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectPresensi($id)
    {
        try {
            $presensi = Presensi::findOrFail($id);
            $presensi->status = 'rejected';
            $presensi->save();

            return redirect()->back()->with('success', 'Presensi ditolak.');
        } catch (\Exception $e) {
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
        $jumlahPegawai = User::count();

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

    private function fetchHolidays($year)
    {
        try {
            $json = @file_get_contents("https://libur.deno.dev/api?year={$year}");
            if (!$json) return [];
            $data = @json_decode($json, true);
            if (!is_array($data)) return [];
            return array_filter(array_column($data, 'date'));
        } catch (\Exception $e) {
            return [];
        }
    }
}
