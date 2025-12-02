<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LaporanExport implements WithMultipleSheets
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->laporan as $data) {
            $sheets[] = new LaporanPerPegawaiSheet($data);
        }

        return $sheets;
    }
}

/**
 * Sheet per pegawai
 */
class LaporanPerPegawaiSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $data;
    protected $liburNasional = [];
    protected $tahun;

    public function __construct($data)
    {
        $this->data = $data;
        $this->tahun = date('Y'); // Default tahun saat ini
        $this->initLiburNasional();
    }

    /**
     * Ambil data libur nasional dan cuti bersama dari API
     */
    private function initLiburNasional()
    {
        // Coba ambil dari cache dulu (cache 30 hari)
        $cacheKey = 'libur_nasional_' . $this->tahun;
        $this->liburNasional = Cache::remember($cacheKey, 2592000, function () { // 30 hari
            $liburData = [];
            
            try {
                // Ambil data dari API kalenderindonesia.com
                $response = Http::get("https://kalenderindonesia.com/api/holiday/{$this->tahun}/all");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['data']['holiday'])) {
                        foreach ($data['data']['holiday'] as $monthData) {
                            if (is_array($monthData)) {
                                foreach ($monthData as $libur) {
                                    if (isset($libur['date'])) {
                                        // Format tanggal dari API: "2024-01-01"
                                        $tanggal = $libur['date'];
                                        
                                        // Konversi ke format Y-m-d jika perlu
                                        if (strlen($tanggal) === 10) {
                                            $liburData[] = $tanggal;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                // Fallback: data libur nasional statis jika API gagal
                if (empty($liburData)) {
                    $liburData = $this->getLiburNasionalDefault();
                }
                
            } catch (\Exception $e) {
                // Jika error, gunakan data default
                $liburData = $this->getLiburNasionalDefault();
            }
            
            return $liburData;
        });
    }

    /**
     * Data libur nasional default jika API gagal
     */
    private function getLiburNasionalDefault()
    {
        // Data libur nasional default berdasarkan tahun
        $defaultLibur = [
            // Tahun Baru
            $this->tahun . '-01-01',
            
            // Hari Raya Idul Fitri (contoh untuk 2024, sesuaikan dengan tahun Hijriyah)
            // Biasanya 2 hari
            $this->tahun . '-04-10',
            $this->tahun . '-04-11',
            
            // Hari Buruh
            $this->tahun . '-05-01',
            
            // Kenaikan Isa Almasih (40 hari setelah Paskah)
            // Paskah biasanya Maret/April, Kenaikan Mei
            $this->tahun . '-05-09',
            
            // Hari Raya Waisak
            // Tergantung kalender lunar
            $this->tahun . '-05-23',
            
            // Hari Lahir Pancasila
            $this->tahun . '-06-01',
            
            // Hari Raya Idul Adha (contoh untuk 2024)
            $this->tahun . '-06-17',
            
            // Tahun Baru Islam
            $this->tahun . '-07-07',
            
            // Hari Kemerdekaan
            $this->tahun . '-08-17',
            
            // Maulid Nabi Muhammad SAW
            $this->tahun . '-09-16',
            
            // Hari Raya Natal
            $this->tahun . '-12-25',
        ];

        // Tambahkan cuti bersama (contoh, sesuaikan dengan pengumuman resmi)
        $cutiBersama = [
            // Cuti bersama Idul Fitri
            $this->tahun . '-04-08',
            $this->tahun . '-04-09',
            $this->tahun . '-04-12',
            
            // Cuti bersama Natal
            $this->tahun . '-12-26',
            $this->tahun . '-12-27',
        ];

        return array_merge($defaultLibur, $cutiBersama);
    }

    /**
     * Cek apakah tanggal termasuk hari libur
     */
    private function isLibur($tanggal)
    {
        try {
            // Parse tanggal
            $carbonDate = null;
            
            if (strpos($tanggal, '-') !== false) {
                $dateParts = explode('-', $tanggal);
                
                if (count($dateParts) === 3) {
                    // Jika format d-m-Y (misal: 01-01-2024)
                    if (strlen($dateParts[0]) <= 2 && strlen($dateParts[1]) <= 2 && strlen($dateParts[2]) === 4) {
                        $carbonDate = Carbon::createFromFormat('d-m-Y', $tanggal);
                    }
                    // Jika format Y-m-d (misal: 2024-01-01)
                    elseif (strlen($dateParts[0]) === 4 && strlen($dateParts[1]) <= 2 && strlen($dateParts[2]) <= 2) {
                        $carbonDate = Carbon::createFromFormat('Y-m-d', $tanggal);
                    }
                }
            }
            
            // Jika parsing gagal, coba metode lain
            if (!$carbonDate) {
                $carbonDate = Carbon::parse($tanggal);
            }
            
            // Cek apakah Sabtu (6) atau Minggu (7)
            $isWeekend = in_array($carbonDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);
            
            // Format untuk pengecekan libur nasional
            $dateString = $carbonDate->format('Y-m-d');
            
            // Cek apakah termasuk libur nasional
            $isHoliday = in_array($dateString, $this->liburNasional);
            
            return $isWeekend || $isHoliday;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Deteksi tahun dari data yang ada
     */
    private function detectYearFromData()
    {
        if (!empty($this->data['rows'])) {
            $firstDate = $this->data['rows'][0]['tanggal'] ?? null;
            if ($firstDate) {
                try {
                    $carbonDate = Carbon::parse($firstDate);
                    return $carbonDate->format('Y');
                } catch (\Exception $e) {
                    // Jika gagal, return tahun saat ini
                }
            }
        }
        return date('Y');
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PRESENSI PEGAWAI'],
            ['Nama: ' . $this->data['user']->name],
            ['NIP: ' . $this->data['user']->nip],
            [],
            ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Keterlambatan', 'Pulang Cepat', 'Jam Kerja', 'Waktu Kurang', 'Lembur'],
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->data['rows'] as $row) {
            $rows[] = [
                $row['tanggal'],
                $row['masuk'],
                $row['pulang'],
                $row['keterlambatan'] !== '-' ? $row['keterlambatan'] . ' mnt' : '-',
                $row['pulang_cepat'] !== '-' ? $row['pulang_cepat'] . ' mnt' : '-',
                $row['jam_kerja'] !== '-' ? $this->formatMenit($row['jam_kerja']) : '-',
                $row['waktu_kurang'] !== '-' ? $row['waktu_kurang'] . ' mnt' : '-',
                $row['lembur'] !== '-' ? $this->formatLembur($row['lembur']) : '-',
            ];
        }

        // 🔹 Tambahkan baris kosong
        $rows[] = [];

        // 🔹 Ringkasan: kolom A merge ke B, nilai di C merge ke D
        $rows[] = ['Total Hari Kerja', '', $this->data['total_hari_kerja']];
        $rows[] = ['Total Keterlambatan', '', $this->data['summary']['total_keterlambatan'] . ' menit'];
        $rows[] = ['Total Pulang Cepat', '', $this->data['summary']['total_pulang_cepat'] . ' menit'];
        $rows[] = ['Total Jam Kerja', '', $this->formatMenit($this->data['summary']['total_jam_kerja'])];
        $rows[] = ['Total Waktu Kurang', '', $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Lembur', '', $this->formatLembur($this->data['summary']['total_lembur'])];

        return $rows;
    }

    private function formatMenit($totalMenit)
    {
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        if ($totalMenit <= 0) return '-';
        return $menit === 0 ? "{$jam} jam" : "{$jam} jam {$menit} menit";
    }

    private function formatLembur($totalMenit)
    {
        $jam = floor($totalMenit / 60);
        return $jam > 0 ? "{$jam} jam" : '-';
    }

    public function title(): string
    {
        return $this->data['user']->name;
    }

    public function styles(Worksheet $sheet)
    {
        // 🔹 Header utama
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->getStyle('A1:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 🔹 Header tabel
        $sheet->getStyle('A5:H5')->getFont()->setBold(true)->getColor()->setRGB('000000');
        $sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
        $sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 🔹 Border data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:H{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 🔹 Warna merah untuk hari Sabtu, Minggu, dan libur nasional
        $dataStartRow = 6; // Baris 6 adalah data pertama (setelah header di baris 5)
        $dataEndRow = $dataStartRow + count($this->data['rows']) - 1;
        
        for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
            $tanggal = $sheet->getCell("A{$row}")->getValue();
            
            if ($tanggal && $this->isLibur($tanggal)) {
                // Warna merah untuk seluruh baris hari libur
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getFont()
                    ->getColor()
                    ->setARGB(Color::COLOR_RED);
                    
                // Opsional: tambahkan background kuning untuk highlight
                /*
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');
                */
            }
        }

        // 🔹 Lebar kolom
        $widths = [14, 10, 10, 14, 14, 14, 14, 12];
        foreach (range('A', 'H') as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        // 🔹 Page setup
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        // 🔹 Margin
        $sheet->getPageMargins()
            ->setTop(0.4)
            ->setRight(0.3)
            ->setLeft(0.3)
            ->setBottom(0.4);

        $sheet->getPageSetup()->setHorizontalCentered(true);

        // 🔹 Merge kolom A–B dan C–D di bagian ringkasan
        $highestRow = $sheet->getHighestRow();
        // Diasumsikan ringkasan = 6 baris terakhir
        for ($r = $highestRow - 5; $r <= $highestRow; $r++) {
            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->mergeCells("C{$r}:D{$r}");
        }

        // 🔹 Styling ringkasan
        $sheet->getStyle("A" . ($highestRow - 5) . ":D{$highestRow}")
            ->getFont()->setBold(true);

        $sheet->getStyle("A" . ($highestRow - 5) . ":B{$highestRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("C" . ($highestRow - 5) . ":D{$highestRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [];
    }
}