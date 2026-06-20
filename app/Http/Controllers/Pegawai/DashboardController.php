<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Presensi;
use App\Models\JamKerja;
use App\Models\JamShift;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard pegawai
     */
    public function index()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        $jadwalKerjaHariIni = $user->getJadwalKerja($today);

        $riwayatHariIni = Presensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->orderBy('jam', 'asc')
            ->get();

        $batasTelat = date('H:i:s', strtotime($jadwalKerjaHariIni['jam_masuk']) + 60);
        foreach ($riwayatHariIni as $p) {
            $p->terlambat = false;
            $p->waktu_kurang_menit = 0;
            if ($p->jenis === 'masuk' && $p->jam > $batasTelat) {
                $p->terlambat = true;
                $p->waktu_kurang_menit = intval((strtotime($p->jam) - strtotime($jadwalKerjaHariIni['jam_masuk'])) / 60);
            }
            if ($p->jenis === 'pulang' && $p->jam < $jadwalKerjaHariIni['jam_pulang']) {
                $p->waktu_kurang_menit = intval((strtotime($jadwalKerjaHariIni['jam_pulang']) - strtotime($p->jam)) / 60);
            }
        }

        $sudahPresensiMasuk = Presensi::where('user_id', $user->id)
            ->where('tanggal', $today)->where('jenis', 'masuk')->where('is_lembur', false)->exists();

        $sudahPresensiPulang = Presensi::where('user_id', $user->id)
            ->where('tanggal', $today)->where('jenis', 'pulang')->where('is_lembur', false)->exists();

        $sudahLemburMasuk = Presensi::where('user_id', $user->id)
            ->where('tanggal', $today)->where('jenis', 'masuk')->where('is_lembur', true)->exists();

        $sudahLemburPulang = Presensi::where('user_id', $user->id)
            ->where('tanggal', $today)->where('jenis', 'pulang')->where('is_lembur', true)->exists();

        // Ambil jam kerja, misal shift pertama
        $jamKerja = JamKerja::first();

        // Siapkan URL foto profil jika ada
        $fotoProfilUrl = null;
        if ($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil)) {
            $fotoProfilUrl = asset('storage/foto_profil/' . $user->foto_profil);
        }

        $wilayahList = $user->wilayahKerjaList;
        if ($wilayahList->isEmpty() && $user->wilayahKerja) {
            $wilayahList = collect([$user->wilayahKerja]);
        }

        $wilayahJson = $wilayahList->map(function ($w) {
            return [
                'lat' => (float) $w->latitude,
                'lng' => (float) $w->longitude,
                'radius' => (float) ($w->radius ?? 100),
                'alamat' => $w->alamat ?? '',
            ];
        })->values()->toArray();

        $shifts = $user->can_shift ? JamShift::all() : collect();

        // Cek shift yang sudah dipilih saat masuk hari ini
        $shiftHariIni = null;
        $jamMasukHariIni = null;
        $presensiMasukRecord = Presensi::where('user_id', $user->id)
            ->where('tanggal', $today)->where('jenis', 'masuk')
            ->where('is_lembur', false)->first();

        if ($presensiMasukRecord) {
            $jamMasukHariIni = $presensiMasukRecord->jam;
            if ($user->can_shift && $presensiMasukRecord->jam_shift_id) {
                $shiftHariIni = JamShift::find($presensiMasukRecord->jam_shift_id);
            }
        }

        $jamPulangTarget = $jadwalKerjaHariIni['jam_pulang'];

        $isLiburHariIni = false;
        $namaLibur = null;
        $todayCarbon = \Carbon\Carbon::today();
        if ($todayCarbon->dayOfWeek == 0 || $todayCarbon->dayOfWeek == 6) {
            $isLiburHariIni = true;
            $namaLibur = 'Libur';
        } else {
            $holidayName = \App\Helpers\HolidayHelper::getName($today);
            if ($holidayName) {
                $isLiburHariIni = true;
                $namaLibur = $holidayName;
            }
        }

        $pengumumans = Pengumuman::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pegawai.dashboard', compact(
            'riwayatHariIni',
            'user',
            'fotoProfilUrl',
            'jamKerja',
            'sudahPresensiMasuk',
            'sudahPresensiPulang',
            'sudahLemburMasuk',
            'sudahLemburPulang',
            'wilayahList',
            'wilayahJson',
            'shifts',
            'shiftHariIni',
            'jamMasukHariIni',
            'jamPulangTarget',
            'pengumumans',
            'jadwalKerjaHariIni',
            'isLiburHariIni',
            'namaLibur'
        ));
    }
}