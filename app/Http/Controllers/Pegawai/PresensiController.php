<?php

namespace App\Http\Controllers\Pegawai;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Intervention\Image\Facades\Image;
use App\Models\WilayahKerja;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LaporanController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

class PresensiController extends Controller
{
    public function darurat()
    {
        if (!\App\Models\AppSetting::getBool('enable_absen_darurat', false)) {
            return redirect()->route('pegawai.dashboard')->with('error', 'Absen darurat tidak aktif.');
        }

        $user = Auth::user();

        $daruratMode = \App\Models\AppSetting::getValue('absen_darurat_mode', 'all');
        $daruratUserIds = json_decode(\App\Models\AppSetting::getValue('absen_darurat_users_' . $daruratMode, '[]'), true) ?: [];
        if ($daruratMode === 'except' && !empty($daruratUserIds) && in_array($user->id, $daruratUserIds)) {
            return redirect()->route('pegawai.dashboard')->with('error', 'Anda tidak memiliki akses absen darurat.');
        } elseif ($daruratMode === 'only' && !empty($daruratUserIds) && !in_array($user->id, $daruratUserIds)) {
            return redirect()->route('pegawai.dashboard')->with('error', 'Anda tidak memiliki akses absen darurat.');
        }
        $wilayahList = $user->wilayahKerjaList;
        if ($wilayahList->isEmpty() && $user->wilayahKerja) {
            $wilayahList = collect([$user->wilayahKerja]);
        }
        $wilayahJson = $wilayahList->map(fn($w) => [
            'lat' => (float)$w->latitude, 'lng' => (float)$w->longitude,
            'radius' => (float)($w->radius ?? 100), 'alamat' => $w->alamat ?? '',
        ])->values()->toArray();

        $shifts = $user->can_shift ? \App\Models\JamShift::all() : collect();

        $today = now()->toDateString();
        $sudahMasuk = Presensi::where('user_id', $user->id)->where('tanggal', $today)->where('jenis', 'masuk')->where('is_lembur', false)->exists();
        $sudahPulang = Presensi::where('user_id', $user->id)->where('tanggal', $today)->where('jenis', 'pulang')->where('is_lembur', false)->exists();

        return view('pegawai.absen-darurat', compact('user', 'wilayahJson', 'shifts', 'sudahMasuk', 'sudahPulang'));
    }

    /**
     * Riwayat presensi hari ini (untuk dashboard pegawai)
     */
    public function today()
    {
        $today = Carbon::today()->format('Y-m-d');

        $todayPresensi = Presensi::where('user_id', Auth::id())
            ->whereDate('tanggal', $today)
            ->orderBy('jam', 'asc')
            ->get();

        return view('pegawai.dashboard', [
            'riwayatHariIni' => $todayPresensi,
        ]);
    }

    /**
     * Riwayat lengkap dengan filter bulan & grouping per hari
     */
    public function riwayat(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        $user = Auth::user();

        $records = Presensi::where('user_id', $user->id)
            ->whereYear('tanggal', Carbon::parse($bulan)->year)
            ->whereMonth('tanggal', Carbon::parse($bulan)->month)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'asc')
            ->get();

        $lemburByDate = $records->where('is_lembur', true)->groupBy('tanggal');

        foreach ($records as $p) {
            if ($p->is_lembur) {
                if ($p->jenis === 'pulang') {
                    $lemburMasuk = $lemburByDate->get($p->tanggal)?->firstWhere('jenis', 'masuk');
                    if ($lemburMasuk) {
                        $tanggalCarbon = Carbon::parse($p->tanggal);
                        $isLibur = in_array($tanggalCarbon->dayOfWeek, [0, 6]) || \App\Helpers\HolidayHelper::isHoliday($p->tanggal);
                        $minLembur = $isLibur ? 300 : 180;
                        $durasi = (strtotime($p->jam) - strtotime($lemburMasuk->jam)) / 60;
                        if ($durasi < $minLembur) {
                            $p->badge_text = 'Pulang Cepat';
                            $p->badge_type = 'warning';
                        } else {
                            $p->badge_text = null;
                            $p->badge_type = null;
                        }
                    } else {
                        $p->badge_text = null;
                        $p->badge_type = null;
                    }
                } else {
                    $p->badge_text = null;
                    $p->badge_type = null;
                }
            } else {
                $jadwal = $user->getJadwalKerja($p->tanggal);
                if ($p->jenis === 'masuk') {
                    $batas = date('H:i:s', strtotime($jadwal['jam_masuk']) + 60);
                    if ($p->jam > $batas) {
                        $p->badge_text = 'Terlambat';
                        $p->badge_type = 'danger';
                    } else {
                        $p->badge_text = 'Tepat Waktu';
                        $p->badge_type = 'success';
                    }
                } else {
                    if ($p->jam < $jadwal['jam_pulang']) {
                        $p->badge_text = 'Pulang Cepat';
                        $p->badge_type = 'warning';
                    } else {
                        $p->badge_text = 'Tepat Waktu';
                        $p->badge_type = 'success';
                    }
                }
            }
        }

        $riwayat = $records->groupBy(function ($item) {
            return Carbon::parse($item->tanggal)->translatedFormat('d F Y');
        });

        return view('pegawai.riwayat', [
            'riwayat'       => $riwayat,
            'bulan'         => $bulan,
            'wilayahAlamat' => $user->wilayahKerjaList->pluck('alamat')->filter()->first()
                ?? $user->wilayahKerja->alamat ?? '',
        ]);
    }

    /**
     * Simpan presensi (masuk / pulang)
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis'        => 'required|in:masuk,pulang',
            'lokasi'       => 'nullable|string|max:255',
            'foto'         => 'required',
            'jam_shift_id' => 'nullable|exists:jam_shift,id',
        ]);

        $today = now()->format('Y-m-d');
        $userId = Auth::id();
        $isLembur = filter_var($request->input('is_lembur', false), FILTER_VALIDATE_BOOLEAN);

        if ($isLembur) {
            if ($request->jenis === 'pulang') {
                $sudahMasukLembur = Presensi::where('user_id', $userId)
                    ->where('tanggal', $today)->where('jenis', 'masuk')->where('is_lembur', true)->exists();
                if (!$sudahMasukLembur) {
                    return redirect()->route('pegawai.dashboard')
                        ->with('error', 'Anda belum melakukan presensi masuk lembur!');
                }
            }

            $sudahAda = Presensi::where('user_id', $userId)
                ->where('tanggal', $today)->where('jenis', $request->jenis)->where('is_lembur', true)->exists();
            if ($sudahAda) {
                return redirect()->route('pegawai.dashboard')
                    ->with('error', 'Anda sudah melakukan presensi ' . $request->jenis . ' lembur hari ini!');
            }
        } else {
            // Reguler: validasi seperti biasa
            if ($request->jenis === 'pulang') {
                $requireMasukFirst = \App\Models\AppSetting::getBool('require_masuk_before_pulang', true);
                if ($requireMasukFirst) {
                    $sudahMasuk = Presensi::where('user_id', $userId)
                        ->where('tanggal', $today)->where('jenis', 'masuk')->where('is_lembur', false)->exists();
                    if (!$sudahMasuk) {
                        return redirect()->route('pegawai.dashboard')
                            ->with('error', 'Anda belum melakukan presensi masuk hari ini!');
                    }
                }

                $sudahPulang = Presensi::where('user_id', $userId)
                    ->where('tanggal', $today)->where('jenis', 'pulang')->where('is_lembur', false)->exists();
                if ($sudahPulang) {
                    return redirect()->route('pegawai.dashboard')
                        ->with('error', 'Anda sudah melakukan presensi pulang hari ini!');
                }
            }

            if ($request->jenis === 'masuk') {
                $sudahMasuk = Presensi::where('user_id', $userId)
                    ->where('tanggal', $today)->where('jenis', 'masuk')->where('is_lembur', false)->exists();
                if ($sudahMasuk) {
                    return redirect()->route('pegawai.dashboard')
                        ->with('error', 'Anda sudah melakukan presensi masuk hari ini!');
                }
            }
        }

        $foto_db = null;

        try {
            // === Simpan foto dengan kompresi ===
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();

                $image = Image::make($file)
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode($file->getClientOriginalExtension(), 75);

                Storage::disk('public')->put('presensi/' . $filename, $image);
                $foto_db = 'presensi/' . $filename;
            } elseif (preg_match('/^data:image\/(\w+);base64,/', $request->foto)) {
                $image_parts = explode(";base64,", $request->foto);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);

                $image = Image::make($image_base64)
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode($image_type, 75);

                $filename = time() . '.' . $image_type;
                Storage::disk('public')->put('presensi/' . $filename, $image);
                $foto_db = 'presensi/' . $filename;
            } else {
                return back()->withErrors(['foto' => 'Format foto tidak valid']);
            }

            // === Ambil lokasi user ===
            $userLocation = explode(',', $request->lokasi);
            $userLat = isset($userLocation[0]) ? floatval($userLocation[0]) : null;
            $userLng = isset($userLocation[1]) ? floatval($userLocation[1]) : null;

            // === Cek di wilayah kerja yang di-assign ke user ===
            $status = 'pending';

            if ($userLat && $userLng) {
                $wilayahUser = Auth::user()->wilayahKerjaList;

                if ($wilayahUser->isEmpty()) {
                    $wilayahUser = collect();
                    $wk = Auth::user()->wilayahKerja;
                    if ($wk) $wilayahUser->push($wk);
                }

                foreach ($wilayahUser as $wilayah) {
                    $radius = $wilayah->radius ?? 100;
                    $distance = $this->haversineDistance(
                        $userLat,
                        $userLng,
                        $wilayah->latitude,
                        $wilayah->longitude
                    );

                    if ($distance <= $radius) {
                        $status = 'approved';
                        break;
                    }
                }
            }

            Presensi::create([
                'user_id'      => $userId,
                'jenis'        => $request->jenis,
                'foto'         => $foto_db,
                'lokasi'       => $request->lokasi,
                'tanggal'      => $today,
                'jam'          => now()->format('H:i:s'),
                'status'       => $status,
                'is_lembur'    => $isLembur,
                'is_darurat'   => filter_var($request->input('is_darurat', false), FILTER_VALIDATE_BOOLEAN),
                'jam_shift_id' => $request->jam_shift_id,
            ]);

            $label = $isLembur ? 'Lembur ' : 'Presensi ';
            $label .= $request->jenis === 'masuk' ? 'masuk' : 'pulang';
            return redirect()->route('pegawai.dashboard')
                ->with('success', $label . ' berhasil - Status: ' . strtoupper($status) . '!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus presensi sekaligus file foto
     */
    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);

        if ($presensi->foto && Storage::disk('public')->exists($presensi->foto)) {
            Storage::disk('public')->delete($presensi->foto);
        }

        $presensi->delete();

        return redirect()->route('pegawai.dashboard')
            ->with('success', 'Presensi berhasil dihapus!');
    }

    /**
     * Hitung jarak antara 2 titik koordinat (Haversine)
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meter

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    public function exportPdf(Request $request)
    {
        $request->validate(['bulan' => 'required|date_format:Y-m']);

        $laporanController = app(LaporanController::class);
        $fakeRequest = Request::create('/admin/laporan/pdf', 'GET', [
            'user_id' => Auth::id(),
            'bulan' => $request->bulan,
        ]);

        return $laporanController->exportPdf($fakeRequest);
    }
}