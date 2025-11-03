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

        // Presensi hari ini (masuk & pulang, hanya yang approved)
        $presensiHariIni = Presensi::with('user')
            ->whereDate('tanggal', $today)
            ->where('status', 'approved')
            ->orderBy('jam', 'asc')
            ->get();

        // Standar jam kerja
        $jamMasukStandard = '07:30:00';
        $jamPulangStandard = '16:00:00';

        foreach ($presensiHariIni as $presensi) {
            $presensi->terlambat = false;
            $presensi->waktu_kurang_menit = 0;
            $presensi->lembur_menit = 0;

            $hari = Carbon::parse($presensi->tanggal)->format('l'); // Nama hari (Monday, etc.)

            // === Cek Terlambat ===
            if ($presensi->jenis === 'masuk' && $presensi->jam > '07:31:59') {
                $presensi->terlambat = true;
                $presensi->waktu_kurang_menit = intval((strtotime($presensi->jam) - strtotime($jamMasukStandard)) / 60);
            }

            // === Cek Pulang Kurang Waktu ===
            if ($presensi->jenis === 'pulang' && $presensi->jam < $jamPulangStandard) {
                $presensi->waktu_kurang_menit = intval((strtotime($jamPulangStandard) - strtotime($presensi->jam)) / 60);
            }

            // === Cek Lembur (hanya weekend) ===
            if ($presensi->jenis === 'pulang' && in_array($hari, ['Saturday', 'Sunday'])) {
                if ($presensi->jam > $jamPulangStandard) {
                    $presensi->lembur_menit = intval((strtotime($presensi->jam) - strtotime($jamPulangStandard)) / 60);
                }
            }
        }

        // Pengajuan pending dengan data lengkap untuk modal
        $pengajuanPending = PengajuanPresensi::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($pengajuan) {
                // ✅ PERBAIKAN: Path yang benar
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

        return view('admin.dashboard', compact(
            'jumlahHadir',
            'jumlahPegawai',
            'jumlahPengajuan',
            'presensiHariIni',
            'pengajuanPending',
            'presensiPending'
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
                'bukti_url' => $pengajuan->bukti ? asset('storage/' . $pengajuan->bukti) : null, // ✅ PERBAIKAN: Path yang benar
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
            // ✅ PERBAIKAN: Parse koordinat dari field lokasi
            $coordinates = $this->parseCoordinates($presensi->lokasi);
            
            return [
                'id' => $presensi->id,
                'user_name' => $presensi->user->name ?? 'N/A',
                'tanggal' => $presensi->tanggal,
                'tanggal_formatted' => Carbon::parse($presensi->tanggal)->translatedFormat('d M Y'),
                'jenis' => $presensi->jenis,
                'jam' => $presensi->jam,
                'lokasi' => $presensi->lokasi ?? 'Tidak ada lokasi',
                'latitude' => $coordinates['latitude'], // ✅ PERBAIKAN: Diambil dari parsing lokasi
                'longitude' => $coordinates['longitude'], // ✅ PERBAIKAN: Diambil dari parsing lokasi
                'foto' => $presensi->foto,
                'foto_url' => $presensi->foto ? asset('storage/' . $presensi->foto) : null, // ✅ PERBAIKAN: Path yang benar
                'approve_url' => route('admin.presensi.approve', $presensi->id),
                'reject_url' => route('admin.presensi.reject', $presensi->id),
            ];
        });
    }

    /**
     * Parse coordinates from location string
     * Format: "latitude,longitude" contoh: "3.3163096,117.5788327"
     */
    private function parseCoordinates($location)
    {
        if (!$location) {
            return ['latitude' => null, 'longitude' => null];
        }

        $coordinates = explode(',', $location);
        
        return [
            'latitude' => count($coordinates) === 2 ? trim($coordinates[0]) : null,
            'longitude' => count($coordinates) === 2 ? trim($coordinates[1]) : null
        ];
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
                $jamMasukDefault = '07:30:00';
                $jamPulangDefault = '16:00:00';

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

        // Hitung status untuk presensi hari ini
        foreach ($presensiHariIni as $presensi) {
            $presensi->terlambat = false;
            $presensi->waktu_kurang_menit = 0;

            if ($presensi->jenis === 'masuk' && $presensi->jam > '07:30:59') {
                $presensi->terlambat = true;
                $presensi->waktu_kurang_menit = intval((strtotime($presensi->jam) - strtotime('07:30:00')) / 60);
            }

            if ($presensi->jenis === 'pulang' && $presensi->jam < '16:00:00') {
                $presensi->waktu_kurang_menit = intval((strtotime('16:00:00') - strtotime($presensi->jam)) / 60);
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
            
            // ✅ PERBAIKAN: Parse koordinat dari field lokasi
            $coordinates = $this->parseCoordinates($presensi->lokasi);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $presensi->id,
                    'user_name' => $presensi->user->name ?? 'N/A',
                    'tanggal' => Carbon::parse($presensi->tanggal)->translatedFormat('d M Y'),
                    'jenis' => $presensi->jenis,
                    'jam' => $presensi->jam,
                    'lokasi' => $presensi->lokasi ?? 'Tidak ada lokasi',
                    'latitude' => $coordinates['latitude'], // ✅ PERBAIKAN: Diambil dari parsing lokasi
                    'longitude' => $coordinates['longitude'], // ✅ PERBAIKAN: Diambil dari parsing lokasi
                    'foto' => $presensi->foto,
                    'foto_url' => $presensi->foto ? asset('storage/' . $presensi->foto) : null, // ✅ PERBAIKAN: Path yang benar
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
                    'bukti_url' => $pengajuan->bukti ? asset('storage/' . $pengajuan->bukti) : null, // ✅ PERBAIKAN: Path yang benar
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
}