<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Presensi;
use App\Models\JamKerja; // <- tambahkan
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard pegawai
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil riwayat presensi hari ini untuk user yang login
        $riwayatHariIni = Presensi::where('user_id', $user->id)
            ->where('tanggal', now()->format('Y-m-d'))
            ->orderBy('jam', 'asc')
            ->get();

        // Ambil jam kerja, misal shift pertama
        $jamKerja = JamKerja::first();

        // Siapkan URL foto profil jika ada
        $fotoProfilUrl = null;
        if ($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil)) {
            $fotoProfilUrl = asset('storage/foto_profil/' . $user->foto_profil);
        }

        return view('pegawai.dashboard', compact('riwayatHariIni', 'user', 'fotoProfilUrl', 'jamKerja'));
    }
}
