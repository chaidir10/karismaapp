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
        $pegawai = User::nonTester()->with('wilayahKerja')->get();
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
                $lastActivity = $userPresensi->sortByDesc('jam')->first();

                if (!$lastActivity) {
                    $kehadiranHariIni[$p->id] = ['status' => 'belum', 'text' => 'Belum Masuk'];
                } else {
                    $jadwal = $p->getJadwalKerja($today);
                    $isLembur = $lastActivity->is_lembur;
                    $jenis = $lastActivity->jenis;

                    if ($isLembur) {
                        if ($jenis === 'pulang') {
                            $lemburMasuk = $userPresensi->where('is_lembur', true)->where('jenis', 'masuk')->first();
                            if ($lemburMasuk) {
                                $durasi = (strtotime($lastActivity->jam) - strtotime($lemburMasuk->jam)) / 60;
                                $isLibur = in_array(Carbon::today()->dayOfWeek, [0, 6]) || \App\Helpers\HolidayHelper::isHoliday($today);
                                $min = $isLibur ? 300 : 180;
                                $kehadiranHariIni[$p->id] = $durasi < $min
                                    ? ['status' => 'warning', 'text' => 'Lembur Pulang Cepat']
                                    : ['status' => 'tepat', 'text' => 'Selesai Lembur'];
                            } else {
                                $kehadiranHariIni[$p->id] = ['status' => 'tepat', 'text' => 'Selesai Lembur'];
                            }
                        } else {
                            $kehadiranHariIni[$p->id] = ['status' => 'lembur', 'text' => 'Lembur'];
                        }
                    } else {
                        if ($jenis === 'pulang') {
                            if ($lastActivity->jam < $jadwal['jam_pulang']) {
                                $kehadiranHariIni[$p->id] = ['status' => 'warning', 'text' => 'Pulang Cepat'];
                            } else {
                                $kehadiranHariIni[$p->id] = ['status' => 'tepat', 'text' => 'Sudah Pulang'];
                            }
                        } else {
                            $batas = date('H:i:s', strtotime($jadwal['jam_masuk']) + 60);
                            if ($lastActivity->jam > $batas) {
                                $kehadiranHariIni[$p->id] = ['status' => 'telat', 'text' => 'Terlambat'];
                            } else {
                                $kehadiranHariIni[$p->id] = ['status' => 'tepat', 'text' => 'Masuk'];
                            }
                        }
                    }
                }

                $riwayatHariIni[$p->id] = $semuaPresensi->where('user_id', $p->id)->values();
            }
        }

        return view('pegawai.pegawai', compact('pegawai', 'kehadiranHariIni', 'riwayatHariIni', 'userRole'));
    }
}
