<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class PerformaController extends Controller
{
    public function index()
    {
        return view('admin.performa');
    }

    public function getData(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|between:2020,2099',
        ]);

        return response()->json($this->generatePerforma($request->bulan, $request->tahun));
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|between:2020,2099',
        ]);

        $result = $this->generatePerforma($request->bulan, $request->tahun);
        $bulanNama = Carbon::create($request->tahun, $request->bulan)->translatedFormat('F Y');

        $pdf = PDF::loadView('admin.performa_pdf', [
            'performa' => $result['performa'],
            'hariKerja' => $result['hari_kerja'],
            'bulanNama' => $bulanNama,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("Performa Pegawai - {$bulanNama}.pdf");
    }

    private function generatePerforma($bulan, $tahun)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $today = Carbon::today();
        if ($endDate->gt($today)) {
            $endDate = $today;
        }

        if ($startDate->gt($today)) {
            return ['performa' => [], 'hari_kerja' => 0];
        }

        $holidays = $this->fetchHolidays($tahun);

        $hariKerja = 0;
        for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
            if ($d->dayOfWeek != 0 && $d->dayOfWeek != 6 && !in_array($d->format('Y-m-d'), $holidays)) {
                $hariKerja++;
            }
        }

        if ($hariKerja === 0) {
            return ['performa' => [], 'hari_kerja' => 0];
        }

        $users = User::where('role', '!=', 'superadmin')->orderBy('name')->get();
        $performa = [];

        foreach ($users as $user) {
            $presensi = Presensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('status', 'approved')
                ->where('is_lembur', false)
                ->get()
                ->groupBy('tanggal');

            $hadir = 0;
            $tepatMasuk = 0;
            $telat = 0;
            $pulangTepat = 0;
            $pulangCepat = 0;

            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                $tanggal = $d->format('Y-m-d');

                if ($d->dayOfWeek == 0 || $d->dayOfWeek == 6 || in_array($tanggal, $holidays)) {
                    continue;
                }

                $dayPresensi = $presensi->get($tanggal);
                if (!$dayPresensi) continue;

                $masuk = $dayPresensi->firstWhere('jenis', 'masuk');
                $pulang = $dayPresensi->firstWhere('jenis', 'pulang');

                if (!$masuk || !$pulang) continue;

                $hadir++;

                $jadwal = $user->getJadwalKerja($d);
                $jamMasukDefault = Carbon::createFromTimeString($jadwal['jam_masuk']);
                $jamToleransi = $jamMasukDefault->copy()->addMinute();
                $jamPulangDefault = Carbon::createFromTimeString($jadwal['jam_pulang']);

                $jamMasukObj = Carbon::parse($masuk->jam);
                $jamPulangObj = Carbon::parse($pulang->jam);

                if ($jamMasukObj->lt($jamToleransi)) {
                    $tepatMasuk++;
                } else {
                    $telat++;
                }

                if ($jamPulangObj->gte($jamPulangDefault)) {
                    $pulangTepat++;
                } else {
                    $pulangCepat++;
                }
            }

            $skorMasuk = round(($tepatMasuk / $hariKerja) * 60, 2);
            $skorPulang = round(($pulangTepat / $hariKerja) * 40, 2);
            $totalPerforma = round($skorMasuk + $skorPulang, 2);

            $performa[] = [
                'nama' => $user->name,
                'nip' => $user->nip,
                'jabatan' => $user->jabatan,
                'hari_kerja' => $hariKerja,
                'hadir' => $hadir,
                'tidak_hadir' => $hariKerja - $hadir,
                'tepat_masuk' => $tepatMasuk,
                'telat' => $telat,
                'pulang_tepat' => $pulangTepat,
                'pulang_cepat' => $pulangCepat,
                'skor_masuk' => $skorMasuk,
                'skor_pulang' => $skorPulang,
                'performa' => $totalPerforma,
            ];
        }

        usort($performa, function ($a, $b) {
            if ($b['performa'] !== $a['performa']) return $b['performa'] <=> $a['performa'];
            if ($b['hadir'] !== $a['hadir']) return $b['hadir'] <=> $a['hadir'];
            return $a['telat'] <=> $b['telat'];
        });

        return [
            'performa' => $performa,
            'hari_kerja' => $hariKerja,
        ];
    }

    private function fetchHolidays($year)
    {
        try {
            $json = @file_get_contents("https://libur.deno.dev/api?year={$year}");
            if (!$json) return [];
            $data = @json_decode($json, true);
            if (!is_array($data)) return [];
            return array_filter(array_column($data, 'tanggal'));
        } catch (\Exception $e) {
            return [];
        }
    }
}
