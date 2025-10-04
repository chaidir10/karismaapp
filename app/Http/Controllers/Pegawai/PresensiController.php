<?php

namespace App\Http\Controllers\Pegawai;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\WilayahKerja;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PresensiController extends Controller
{
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

        $riwayat = Presensi::where('user_id', Auth::id())
            ->whereYear('tanggal', Carbon::parse($bulan)->year)
            ->whereMonth('tanggal', Carbon::parse($bulan)->month)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->translatedFormat('d F Y');
            });

        return view('pegawai.riwayat', [
            'riwayat' => $riwayat,
            'bulan'   => $bulan,
        ]);
    }

    /**
     * Simpan presensi masuk/pulang
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis'  => 'required|in:masuk,pulang',
            'lokasi' => 'nullable|string|max:255',
            'foto'   => 'required',
        ]);

        $foto_db = null;

        try {
            // Simpan foto
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/presensi', $filename);
                $foto_db = 'presensi/' . $filename;
            } elseif (preg_match('/^data:image\/(\w+);base64,/', $request->foto)) {
                $image_parts = explode(";base64,", $request->foto);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = time() . '.' . $image_type;
                Storage::disk('public')->put('presensi/' . $filename, $image_base64);
                $foto_db = 'presensi/' . $filename;
            } else {
                return back()->withErrors(['foto' => 'Format foto tidak valid']);
            }

            // Ambil lokasi user
            $userLocation = explode(',', $request->lokasi);
            $userLat = isset($userLocation[0]) ? floatval($userLocation[0]) : null;
            $userLng = isset($userLocation[1]) ? floatval($userLocation[1]) : null;

            // Ambil wilayah kerja user
            $wilayah = Auth::user()->wilayahKerja;
            $status = 'pending';

            if ($wilayah && $userLat && $userLng) {
                $radius = $wilayah->radius ?? 100; // meter
                $distance = $this->haversineDistance($userLat, $userLng, $wilayah->latitude, $wilayah->longitude);

                if ($distance <= $radius) {
                    $status = 'approved';
                }
            }

            Presensi::create([
                'user_id' => Auth::id(),
                'jenis'   => $request->jenis,
                'foto'    => $foto_db,
                'lokasi'  => $request->lokasi,
                'tanggal' => now()->format('Y-m-d'),
                'jam'     => now()->format('H:i:s'),
                'status'  => $status,
            ]);

            return redirect()->route('pegawai.dashboard')
                ->with('success', 'Presensi berhasil disimpan dan status: ' . strtoupper($status) . '!');
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
}
