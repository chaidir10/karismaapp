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

class LaporanPerPegawaiSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $data;
    protected $holidays = []; // array tanggal libur dari API

    public function __construct($data)
    {
        $this->data = $data;
        $this->holidays = $this->fetchHolidays(date('Y')); // ambil libur tahun sekarang
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
                $row['tanggal'], // misal '05/11/2025'
                $row['masuk'],
                $row['pulang'],
                $row['keterlambatan'] !== '-' ? $row['keterlambatan'] . ' mnt' : '-',
                $row['pulang_cepat'] !== '-' ? $row['pulang_cepat'] . ' mnt' : '-',
                $row['jam_kerja'] !== '-' ? $this->formatMenit($row['jam_kerja']) : '-',
                $row['waktu_kurang'] !== '-' ? $row['waktu_kurang'] . ' mnt' : '-',
                $row['lembur'] !== '-' ? $this->formatLembur($row['lembur']) : '-',
            ];
        }

        // baris kosong
        $rows[] = [];

        // ringkasan
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
        // =====================
        // Styling header & table
        // =====================
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A5:H5')->getFont()->setBold(true)->getColor()->setRGB('000000');
        $sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('BFBFBF');
        $sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:H{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $widths = [14,10,10,14,14,14,14,12];
        foreach (range('A','H') as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()
            ->setTop(0.4)->setRight(0.3)->setLeft(0.3)->setBottom(0.4);
        $sheet->getPageSetup()->setHorizontalCentered(true);

        // merge ringkasan
        $highestRow = $sheet->getHighestRow();
        for ($r = $highestRow - 5; $r <= $highestRow; $r++) {
            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->mergeCells("C{$r}:D{$r}");
        }
        $sheet->getStyle("A" . ($highestRow - 5) . ":D{$highestRow}")
            ->getFont()->setBold(true);

        // ====================================================
        // 🔥 Pewarnaan otomatis: weekend atau hari libur nasional
        // ====================================================
        $dataStart = 6; // baris pertama data setelah header

        for ($r = $dataStart; $r <= $highestRow - 7; $r++) {
            $tanggal = $sheet->getCell("A{$r}")->getValue();
            if (!$tanggal) continue;

            // parse tanggal dari format dd/mm/YYYY
            $dt = \DateTime::createFromFormat('d/m/Y', $tanggal);
            if (!$dt) continue;

            $dayOfWeek = (int) $dt->format('N'); // 6 = Sabtu, 7 = Minggu
            $isoDate = $dt->format('Y-m-d');     // untuk dibandingkan dengan API

            $isWeekend = ($dayOfWeek == 6 || $dayOfWeek == 7);
            $isHoliday = in_array($isoDate, $this->holidays);

            if ($isWeekend || $isHoliday) {
                $sheet->getStyle("A{$r}:H{$r}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FFCCCC');
            }
        }

        return [];
    }

    /**
     * Fetch daftar libur nasional & cuti bersama dari API
     * Mengembalikan array tanggal dalam format 'Y-m-d'
     */
    protected function fetchHolidays($year)
    {
        $url = "https://libur.deno.dev/api?year={$year}";

        // menggunakan file_get_contents (atau bisa pakai Guzzle / cURL)
        $json = @file_get_contents($url);
        if (!$json) {
            return [];
        }

        $data = @json_decode($json, true);
        if (!is_array($data)) {
            return [];
        }

        $dates = [];
        foreach ($data as $item) {
            if (!empty($item['tanggal'])) {
                // API mengembalikan misal "2025-12-25"
                $dates[] = $item['tanggal'];
            }
        }
        return $dates;
    }
}
