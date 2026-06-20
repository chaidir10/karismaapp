<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Presensi;
use App\Models\WilayahKerja;
use Carbon\Carbon;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = User::with('wilayahKerja')->get();
        $today = Carbon::today()->format('Y-m-d');
        $userRole = Auth::user()->role ?? 'pegawai';
        $kehadiranHariIni = [];

        $riwayatHariIni = [];

        if (in_array(strtolower($userRole), ['admin', 'superadmin'])) {
            $semuaPresensi = Presensi::where('tanggal', $today)
                ->where('status', 'approved')
                ->orderBy('jam', 'asc')
                ->get();

            $presensiMasuk = $semuaPresensi->where('jenis', 'masuk')->where('is_lembur', false)->keyBy('user_id');

            foreach ($pegawai as $p) {
                $masuk = $presensiMasuk->get($p->id);
                if (!$masuk) {
                    $kehadiranHariIni[$p->id] = ['status' => 'belum', 'text' => 'Belum Hadir'];
                } else {
                    $jadwal = $p->getJadwalKerja($today);
                    $batas = date('H:i:s', strtotime($jadwal['jam_masuk']) + 60);
                    if ($masuk->jam > $batas) {
                        $kehadiranHariIni[$p->id] = ['status' => 'telat', 'text' => 'Terlambat'];
                    } else {
                        $kehadiranHariIni[$p->id] = ['status' => 'tepat', 'text' => 'Tepat Waktu'];
                    }
                }

                $riwayatHariIni[$p->id] = $semuaPresensi->where('user_id', $p->id)->values();
            }
        }

        return view('pegawai.pegawai', compact('pegawai', 'kehadiranHariIni', 'riwayatHariIni', 'userRole'));
    }
}
