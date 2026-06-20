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

            $presensiByUser = $semuaPresensi->groupBy('user_id');

            foreach ($pegawai as $p) {
                $userPresensi = $presensiByUser->get($p->id, collect());
                $masukReguler = $userPresensi->where('jenis', 'masuk')->where('is_lembur', false)->first();
                $masukLembur = $userPresensi->where('jenis', 'masuk')->where('is_lembur', true)->first();

                if ($masukLembur && !$masukReguler) {
                    $kehadiranHariIni[$p->id] = ['status' => 'lembur', 'text' => 'Lembur'];
                } elseif ($masukReguler) {
                    $jadwal = $p->getJadwalKerja($today);
                    $batas = date('H:i:s', strtotime($jadwal['jam_masuk']) + 60);
                    if ($masukReguler->jam > $batas) {
                        $kehadiranHariIni[$p->id] = ['status' => 'telat', 'text' => 'Terlambat'];
                    } else {
                        $kehadiranHariIni[$p->id] = ['status' => 'tepat', 'text' => 'Masuk'];
                    }
                } else {
                    $kehadiranHariIni[$p->id] = ['status' => 'belum', 'text' => 'Belum Masuk'];
                }

                $riwayatHariIni[$p->id] = $semuaPresensi->where('user_id', $p->id)->values();
            }
        }

        return view('pegawai.pegawai', compact('pegawai', 'kehadiranHariIni', 'riwayatHariIni', 'userRole'));
    }
}
