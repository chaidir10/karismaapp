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

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $tanggal   = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;
                $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);

                $masuk  = $presensi->has($tanggal) ? $presensi[$tanggal]->firstWhere('jenis', 'masuk') : null;
                $pulang = $presensi->has($tanggal) ? $presensi[$tanggal]->firstWhere('jenis', 'pulang') : null;

                $jamMasukDefault  = Carbon::createFromTimeString('07:30');
                $jamToleransi     = Carbon::createFromTimeString('07:31'); // batas keterlambatan
                $jamPulangDefault = $date->isFriday()
                    ? Carbon::createFromTimeString('16:30')
                    : Carbon::createFromTimeString('16:00');

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
                ];

                if ($masuk && $pulang) {
                    $jamMasukObj  = Carbon::parse($masuk->jam);
                    $jamPulangObj = Carbon::parse($pulang->jam);

                    // Total jam kerja aktual
                    $jamKerja = abs(floor($jamMasukObj->diffInMinutes($jamPulangObj)));

                    if ($isWeekend) {
                        // Sabtu/Minggu = lembur penuh
                        $row['jam_kerja'] = $jamKerja;
                        $row['lembur']    = $jamKerja;
                        $totalLembur     += $jamKerja;
                    } else {
                        // Jam kerja wajib (dari 07:30 sampai jam pulang)
                        $jamKerjaWajib = abs(floor($jamMasukDefault->diffInMinutes($jamPulangDefault)));

                        // ✅ Keterlambatan hanya jika >= 07:31
                        $keterlambatan = $jamMasukObj->gte($jamToleransi)
                            ? abs(floor($jamMasukObj->diffInMinutes($jamMasukDefault)))
                            : 0;

                        // ✅ Pulang cepat jika sebelum jam wajib pulang
                        $pulangCepat = $jamPulangObj->lt($jamPulangDefault)
                            ? abs(floor($jamPulangDefault->diffInMinutes($jamPulangObj)))
                            : 0;

                        // ✅ Kekurangan jam kerja (tidak negatif)
                        $waktuKurang = ($jamKerja < $jamKerjaWajib)
                            ? abs(floor($jamKerjaWajib - $jamKerja))
                            : 0;

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
                }

                $rows[] = $row;
            }

            $laporan[] = [
                'user'             => $user,
                'total_hari_kerja' => $totalHariKerja,
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
