<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.manajemenlaporan', compact('users'));
    }

    private function generateLaporan($userId = null, $bulan)
    {
        $startDate = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $endDate   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        $users = $userId ? User::where('id', $userId)->get() : User::orderBy('name')->get();
        $laporan = [];

        // Konfigurasi jam kerja
        $config = [
            'jam_masuk' => '07:30',
            'batas_telat' => '07:31', // 1 menit toleransi
            'jam_pulang_hari_biasa' => '16:00',
            'jam_pulang_jumat' => '16:30',
            'jam_kerja_harian' => 8 * 60, // 8 jam dalam menit
        ];

        foreach ($users as $user) {
            $presensi = Presensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where('status', 'approved')
                ->get()
                ->groupBy('tanggal');

            $rows = [];
            $totalKeterlambatan = 0;
            $totalPulangCepat   = 0;
            $totalJamKerja      = 0;
            $totalKekurangan    = 0;
            $totalHariKerja     = 0;
            $totalLembur        = 0;
            $totalHariTelat     = 0;

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $tanggal   = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;
                $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                $isFriday  = ($dayOfWeek == 5); // Jumat

                $masuk  = $presensi->has($tanggal) ? $presensi[$tanggal]->firstWhere('jenis', 'masuk') : null;
                $pulang = $presensi->has($tanggal) ? $presensi[$tanggal]->firstWhere('jenis', 'pulang') : null;

                $jamMasukDefault  = Carbon::createFromTimeString($config['jam_masuk']);
                $jamToleransi     = Carbon::createFromTimeString($config['batas_telat']);
                $jamPulangDefault = $isFriday
                    ? Carbon::createFromTimeString($config['jam_pulang_jumat'])
                    : Carbon::createFromTimeString($config['jam_pulang_hari_biasa']);

                $row = [
                    'tanggal'       => $date->format('d/m/Y'),
                    'masuk'         => $masuk ? Carbon::parse($masuk->jam)->format('H:i') : '-',
                    'pulang'        => $pulang ? Carbon::parse($pulang->jam)->format('H:i') : '-',
                    'keterlambatan' => '-',
                    'pulang_cepat'  => '-',
                    'jam_kerja'     => '-',
                    'waktu_kurang'  => '-',
                    'lembur'        => '-',
                    'is_weekend'    => $isWeekend,
                    'status_masuk'  => 'Tidak Hadir',
                ];

                if ($masuk && $pulang) {
                    $jamMasukObj  = Carbon::parse($masuk->jam);
                    $jamPulangObj = Carbon::parse($pulang->jam);

                    // Total jam kerja aktual (dalam menit, tanpa detik)
                    $jamKerja = $this->calculateMinutesWithoutSeconds($jamMasukObj, $jamPulangObj);

                    if ($isWeekend) {
                        // Sabtu/Minggu = lembur penuh
                        $row['jam_kerja'] = $jamKerja;
                        $row['lembur']    = $jamKerja;
                        $row['status_masuk'] = 'Lembur';
                        $totalLembur     += $jamKerja;
                    } else {
                        // Jam kerja wajib (dari 07:30 sampai jam pulang)
                        $jamKerjaWajib = $this->calculateMinutesWithoutSeconds($jamMasukDefault, $jamPulangDefault);

                        // ✅ Keterlambatan hanya jika >= 07:31 (dibulatkan ke atas)
                        $keterlambatan = $jamMasukObj->gte($jamToleransi)
                            ? $this->calculateMinutesWithoutSeconds($jamMasukDefault, $jamMasukObj, true)
                            : 0;

                        // Hitung hari telat
                        if ($keterlambatan > 0) {
                            $totalHariTelat++;
                        }

                        // ✅ Pulang cepat jika sebelum jam wajib pulang (dibulatkan ke atas)
                        $pulangCepat = $jamPulangObj->lt($jamPulangDefault)
                            ? $this->calculateMinutesWithoutSeconds($jamPulangObj, $jamPulangDefault, true)
                            : 0;

                        // ✅ Kekurangan jam kerja (tidak negatif, dibulatkan ke atas)
                        $waktuKurang = ($jamKerja < $jamKerjaWajib)
                            ? $this->roundUpToNearestMinute($jamKerjaWajib - $jamKerja)
                            : 0;

                        // Tentukan status
                        if ($keterlambatan > 0) {
                            $row['status_masuk'] = 'Telat';
                        } else {
                            $row['status_masuk'] = 'Tepat Waktu';
                        }

                        $row['keterlambatan'] = $keterlambatan ?: '-';
                        $row['pulang_cepat']  = $pulangCepat ?: '-';
                        $row['jam_kerja']     = $jamKerja ?: '-';
                        $row['waktu_kurang']  = $waktuKurang ?: '-';

                        $totalKeterlambatan += $keterlambatan;
                        $totalPulangCepat   += $pulangCepat;
                        $totalJamKerja      += $jamKerja;
                        $totalKekurangan    += $waktuKurang;
                        $totalHariKerja++;
                    }
                } elseif ($masuk || $pulang) {
                    if (!$isWeekend) $totalHariKerja++;
                    $row['status_masuk'] = 'Data Tidak Lengkap';
                }

                $rows[] = $row;
            }

            $laporan[] = [
                'user'             => $user,
                'total_hari_kerja' => $totalHariKerja,
                'total_hari_telat' => $totalHariTelat,
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
        // Buat copy tanpa detik
        $startWithoutSeconds = $start->copy()->setSeconds(0);
        $endWithoutSeconds = $end->copy()->setSeconds(0);
        
        $diffInMinutes = $startWithoutSeconds->diffInMinutes($endWithoutSeconds);
        
        // Jika perlu dibulatkan ke atas dan ada sisa detik yang signifikan
        if ($roundUp && $start->second > 0) {
            $diffInMinutes++;
        }
        
        return $diffInMinutes;
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
            ->setPaper('a4', 'portrait');

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