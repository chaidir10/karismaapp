<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Cuti;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    public function index()
    {
        $users = User::nonTester()->orderBy('name')->get();
        return view('admin.manajemenlaporan', compact('users'));
    }

    private function generateLaporan($userId = null, $bulan)
    {
        $startDate = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $endDate   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        $users = $userId
            ? User::nonTester()->where('id', $userId)->get()
            : User::nonTester()->orderBy('name')->get();

        $holidays = \App\Helpers\HolidayHelper::get($startDate->year);
        $laporan = [];

        foreach ($users as $user) {
            $allPresensi = Presensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where('status', 'approved')
                ->get();

            $presensiReguler = $allPresensi->where('is_lembur', false)->groupBy('tanggal');
            $presensiLembur  = $allPresensi->where('is_lembur', true)->groupBy('tanggal');

            // Cuti/DL approved untuk user ini di bulan ini
            try {
                $cutiList = Cuti::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->where('tanggal_mulai', '<=', $endDate)
                    ->where('tanggal_selesai', '>=', $startDate)
                    ->get();
            } catch (\Exception $e) {
                $cutiList = collect();
            }
            $cutiDates = [];
            $cutiDetails = [];
            foreach ($cutiList as $c) {
                $cStart = $c->tanggal_mulai->max($startDate);
                $cEnd = $c->tanggal_selesai->min($endDate);
                $count = 0;
                for ($d = $cStart->copy(); $d->lte($cEnd); $d->addDay()) {
                    if ($d->dayOfWeek == 0 || $d->dayOfWeek == 6 || isset($holidays[$d->format('Y-m-d')])) continue;
                    $cutiDates[$d->format('Y-m-d')] = $c->label;
                    $count++;
                }
                if ($count > 0) {
                    $cutiDetails[] = [
                        'hari'    => $count,
                        'mulai'   => $c->tanggal_mulai->copy(),
                        'selesai' => $c->tanggal_selesai->copy(),
                        'label'   => $c->label,
                    ];
                }
            }

            // Hitung total hari kerja efektif seluruh bulan (exclude weekend + libur)
            $totalHariKerjaEfektif = 0;
            for ($dd = $startDate->copy(); $dd->lte($endDate); $dd->addDay()) {
                if ($dd->dayOfWeek != 0 && $dd->dayOfWeek != 6 && !isset($holidays[$dd->format('Y-m-d')])) {
                    $totalHariKerjaEfektif++;
                }
            }

            $rows = [];
            $totalKeterlambatan = 0;
            $totalPulangCepat   = 0;
            $totalJamKerja      = 0;
            $totalKekurangan    = 0;
            $totalHariHadir     = 0;
            $totalLembur        = 0;
            $totalHariLembur    = 0;
            $totalHariTelat     = 0;
            $totalHariCuti      = 0;

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $tanggal   = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;
                $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                $isHoliday = isset($holidays[$tanggal]);
                $isLibur   = $isWeekend || $isHoliday;

                $statusLibur = 'Tidak Hadir';
                if ($isHoliday) {
                    $statusLibur = $this->shortenHoliday($holidays[$tanggal]);
                } elseif ($isWeekend) {
                    $statusLibur = 'Libur';
                }

                $masuk  = $presensiReguler->has($tanggal) ? $presensiReguler[$tanggal]->firstWhere('jenis', 'masuk') : null;
                $pulang = $presensiReguler->has($tanggal) ? $presensiReguler[$tanggal]->firstWhere('jenis', 'pulang') : null;

                $lemburMasuk  = $presensiLembur->has($tanggal) ? $presensiLembur[$tanggal]->firstWhere('jenis', 'masuk') : null;
                $lemburPulang = $presensiLembur->has($tanggal) ? $presensiLembur[$tanggal]->firstWhere('jenis', 'pulang') : null;

                $jadwal = $user->getJadwalKerja($date);
                $jamMasukDefault  = Carbon::createFromTimeString($jadwal['jam_masuk']);
                $jamToleransi     = $jamMasukDefault->copy()->addMinute();
                $jamPulangDefault = Carbon::createFromTimeString($jadwal['jam_pulang']);

                $row = [
                    'tanggal'        => $date->format('d/m/Y'),
                    'masuk'          => '-',
                    'pulang'         => '-',
                    'keterlambatan'  => '-',
                    'pulang_cepat'   => '-',
                    'jam_kerja'      => '-',
                    'waktu_kurang'   => '-',
                    'lembur'         => '-',
                    'lembur_waktu'   => null,
                    'is_weekend'     => $isLibur,
                    'is_holiday'     => $isHoliday,
                    'holiday_name'   => $isHoliday ? $holidays[$tanggal] : null,
                    'status_masuk'   => $isLibur ? $statusLibur : 'Tidak Hadir',
                    'is_cuti'        => false,
                ];

                // Cek cuti/DL — skip hari ini dari perhitungan kerja
                if (isset($cutiDates[$tanggal]) && !$isLibur) {
                    $row['status_masuk'] = $cutiDates[$tanggal];
                    $row['is_cuti'] = true;
                    $totalHariCuti++;
                    $rows[] = $row;
                    continue;
                }

                $hasExplicitLembur = $lemburMasuk && $lemburPulang;

                if ($masuk && $pulang) {
                    $jamMasukObj  = Carbon::parse($masuk->jam);
                    $jamPulangObj = Carbon::parse($pulang->jam);
                    $jamKerja = $this->calculateMinutesWithoutSeconds($jamMasukObj, $jamPulangObj);

                    if ($isLibur) {
                        if (!$hasExplicitLembur) {
                            $row['lembur']       = $jamKerja;
                            $row['lembur_waktu'] = $jamMasukObj->format('H:i') . '-' . $jamPulangObj->format('H:i');
                            $minLembur = $isHoliday ? 300 : 300;
                            $row['status_masuk'] = $jamKerja < $minLembur ? 'Lembur (Pulang Cepat)' : 'Lembur';
                            $totalLembur += $jamKerja;
                            $totalHariLembur++;
                        }
                        $row['jam_kerja'] = '-';
                    } else {
                        $row['masuk']  = $jamMasukObj->format('H:i');
                        $row['pulang'] = $jamPulangObj->format('H:i');

                        $jamKerjaWajib = $this->calculateMinutesWithoutSeconds($jamMasukDefault, $jamPulangDefault);

                        $keterlambatan = $jamMasukObj->gte($jamToleransi)
                            ? $this->calculateMinutesWithoutSeconds($jamMasukDefault, $jamMasukObj, true)
                            : 0;

                        if ($keterlambatan > 0) $totalHariTelat++;

                        $pulangCepat = $jamPulangObj->lt($jamPulangDefault)
                            ? $this->calculateMinutesWithoutSeconds($jamPulangObj, $jamPulangDefault, true)
                            : 0;

                        $waktuKurang = ($jamKerja < $jamKerjaWajib)
                            ? $this->roundUpToNearestMinute($jamKerjaWajib - $jamKerja)
                            : 0;

                        $row['status_masuk']  = $keterlambatan > 0 ? 'Telat' : 'Tepat Waktu';
                        $row['keterlambatan'] = $keterlambatan ?: '-';
                        $row['pulang_cepat']  = $pulangCepat ?: '-';
                        $row['jam_kerja']     = $jamKerja ?: '-';
                        $row['waktu_kurang']  = $waktuKurang ?: '-';

                        $totalKeterlambatan += $keterlambatan;
                        $totalPulangCepat   += $pulangCepat;
                        $totalJamKerja      += $jamKerja;
                        $totalKekurangan    += $waktuKurang;
                        $totalHariHadir++;
                    }
                } elseif ($masuk || $pulang) {
                    if (!$isLibur) {
                        $totalHariHadir++;
                        $row['masuk']  = $masuk ? Carbon::parse($masuk->jam)->format('H:i') : '-';
                        $row['pulang'] = $pulang ? Carbon::parse($pulang->jam)->format('H:i') : '-';
                    }
                    $row['status_masuk'] = 'Data Tidak Lengkap';
                }

                // Lembur eksplisit (dari tombol lembur) — berlaku semua hari
                if ($lemburMasuk && $lemburPulang) {
                    $lemburMasukObj  = Carbon::parse($lemburMasuk->jam);
                    $lemburPulangObj = Carbon::parse($lemburPulang->jam);
                    $lemburMenit = $this->calculateMinutesWithoutSeconds($lemburMasukObj, $lemburPulangObj);
                    $waktuRange = $lemburMasukObj->format('H:i') . '-' . $lemburPulangObj->format('H:i');

                    $currentLembur = is_numeric($row['lembur']) ? $row['lembur'] : 0;
                    $row['lembur'] = $currentLembur + $lemburMenit;
                    $row['lembur_waktu'] = $row['lembur_waktu'] ? $row['lembur_waktu'] . ', ' . $waktuRange : $waktuRange;
                    $totalLembur += $lemburMenit;
                    if ($currentLembur == 0) {
                        $totalHariLembur++;
                    }
                    if (!$masuk && !$pulang) {
                        $minLembur = $isLibur ? 300 : 180;
                        $totalLemburMenit = is_numeric($row['lembur']) ? $row['lembur'] : 0;
                        $row['status_masuk'] = $totalLemburMenit < $minLembur ? 'Lembur (Pulang Cepat)' : 'Lembur';
                    }
                }

                $rows[] = $row;
            }

            $isShiftUser = $user->can_shift;

            $laporan[] = [
                'user'             => $user,
                'bulan'            => $startDate->translatedFormat('F Y'),
                'total_hari_kerja'   => $totalHariKerjaEfektif,
                'total_hari_hadir'   => $totalHariHadir,
                'total_hari_telat'   => $totalHariTelat,
                'total_hari_lembur'  => $totalHariLembur,
                'total_hari_cuti'    => $totalHariCuti,
                'cuti_details'       => $cutiDetails,
                'is_shift'         => $isShiftUser,
                'shift_nama'       => $isShiftUser ? 'Pegawai Shift' : null,
                'rows'             => $rows,
                'summary'          => [
                    'total_keterlambatan' => $totalKeterlambatan,
                    'total_pulang_cepat'  => $totalPulangCepat,
                    'total_jam_kerja'     => $totalJamKerja,
                    'total_kekurangan'    => $totalKekurangan,
                    'total_lembur'        => $totalLembur,
                ],
            ];
        }

        return $laporan;
    }

    /**
     * Menghitung selisih menit antara dua waktu tanpa memperhitungkan detik
     * dan membulatkan ke atas jika diperlukan
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param bool $roundUp Apakah harus dibulatkan ke atas
     * @return int
     */
    private function calculateMinutesWithoutSeconds(Carbon $start, Carbon $end, bool $roundUp = false): int
    {
        $startWithoutSeconds = $start->copy()->setSeconds(0);
        $endWithoutSeconds = $end->copy()->setSeconds(0);

        $diffInMinutes = abs($startWithoutSeconds->diffInMinutes($endWithoutSeconds));

        if ($roundUp && $start->second > 0) {
            $diffInMinutes++;
        }

        return (int) $diffInMinutes;
    }

    private function shortenHoliday(string $name): string
    {
        $name = str_replace('Cuti Bersama ', 'Cutber ', $name);
        $name = preg_replace('/\s+\d{4}\s*Hijriyah$/i', '', $name);
        $name = preg_replace('/\s+\d{4}\s*Masehi$/i', '', $name);
        return $name;
    }

    /**
     * Membulatkan nilai menit ke atas ke menit terdekat
     *
     * @param float $minutes
     * @return int
     */
    private function roundUpToNearestMinute(float $minutes): int
    {
        return (int) ceil($minutes);
    }

    /**
     * Format menit ke format jam:menit
     *
     * @param int $minutes
     * @return string
     */
    private function formatMinutesToTime(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return sprintf("%02d:%02d", $hours, $remainingMinutes);
    }


    public function getLaporan(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'bulan'   => 'required|date_format:Y-m',
        ]);

        $laporan = $this->generateLaporan($request->user_id, $request->bulan);
        return response()->json($laporan);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'bulan'   => 'required|date_format:Y-m',
        ]);

        $laporan = $this->generateLaporan($request->user_id, $request->bulan);
        $pdf = PDF::loadView('admin.manajemenlaporan_pdf', compact('laporan'))
            ->setPaper('a4', 'landscape');

        $bulanNama = Carbon::createFromFormat('Y-m', $request->bulan)->isoFormat('MMMM Y');
        $filename = $request->user_id
            ? "Laporan Presensi {$laporan[0]['user']->name} ({$laporan[0]['user']->nip}) - {$bulanNama}.pdf"
            : "Laporan Presensi Seluruh Pegawai - {$bulanNama}.pdf";

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'bulan'   => 'required|date_format:Y-m',
        ]);

        $bulanNama = Carbon::createFromFormat('Y-m', $request->bulan)->isoFormat('MMMM Y');
        $filename = $request->user_id
            ? "Laporan Presensi " . User::find($request->user_id)->name . " - {$bulanNama}.xlsx"
            : "Laporan Presensi Seluruh Pegawai - {$bulanNama}.xlsx";

        return Excel::download(new LaporanExport(
            $this->generateLaporan($request->user_id, $request->bulan)
        ), $filename);
    }
}