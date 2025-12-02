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

/**
 * Sheet per pegawai
 */
class LaporanPerPegawaiSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    // daftar libur nasional dan cuti bersama tahun berjalan
    protected $liburNasional = [
        '2025-01-01',
        '2025-01-29',
        '2025-03-31',
        '2025-04-18',
        '2025-04-20',
        '2025-05-01',
        '2025-05-29',
        '2025-06-01',
        '2025-06-06',
        '2025-06-27',
        '2025-08-17',
        '2025-10-06',
        '2025-12-25'
    ];

    protected $cutiBersama = [
        '2025-01-30',
        '2025-04-21',
        '2025-05-02',
        '2025-12-26'
    ];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PRESENSI PEGAWAI'],
            ['Nama: ' . ($this->data['user']->name ?? '-')],
            ['NIP: ' . ($this->data['user']->nip ?? '-')],
            [],
            ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Keterlambatan', 'Pulang Cepat', 'Jam Kerja', 'Waktu Kurang', 'Lembur'],
        ];
    }

    public function array(): array
    {
        $rows = [];
        $detailRows = $this->data['rows'] ?? [];

        if (empty($detailRows)) {
            $rows[] = ['-', '-', '-', '-', '-', '-', '-', '-'];
        } else {
            foreach ($detailRows as $row) {
                $rows[] = [
                    $row['tanggal'] ?? '-',
                    $row['masuk'] ?? '-',
                    $row['pulang'] ?? '-',
                    isset($row['keterlambatan']) && $row['keterlambatan'] !== '-' ? $row['keterlambatan'] . ' mnt' : '-',
                    isset($row['pulang_cepat']) && $row['pulang_cepat'] !== '-' ? $row['pulang_cepat'] . ' mnt' : '-',
                    isset($row['jam_kerja']) && $row['jam_kerja'] !== '-' ? $this->formatMenit($row['jam_kerja']) : '-',
                    isset($row['waktu_kurang']) && $row['waktu_kurang'] !== '-' ? $row['waktu_kurang'] . ' mnt' : '-',
                    isset($row['lembur']) && $row['lembur'] !== '-' ? $this->formatLembur($row['lembur']) : '-',
                ];
            }
        }

        // ringkasan
        $rows[] = [];

        $summary = $this->data['summary'] ?? [];

        $rows[] = ['Total Hari Kerja', '', $this->data['total_hari_kerja'] ?? 0];
        $rows[] = ['Total Keterlambatan', '', ($summary['total_keterlambatan'] ?? 0) . ' menit'];
        $rows[] = ['Total Pulang Cepat', '', ($summary['total_pulang_cepat'] ?? 0) . ' menit'];
        $rows[] = ['Total Jam Kerja', '', $this->formatMenit($summary['total_jam_kerja'] ?? 0)];
        $rows[] = ['Total Waktu Kurang', '', ($summary['total_kekurangan'] ?? 0) . ' menit'];
        $rows[] = ['Total Lembur', '', $this->formatLembur($summary['total_lembur'] ?? 0)];

        return $rows;
    }


    // =======================
    //   🎨  STYLING
    // =======================

    public function styles(Worksheet $sheet)
    {
        // Header utama
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->getStyle('A1:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header tabel
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
        $sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border semua data
        $lastRow = $sheet->getHighestRow();

        if ($lastRow >= 5) {
            $sheet->getStyle("A5:H{$lastRow}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Pewarnaan baris berdasarkan tanggal
        for ($row = 6; $row <= $lastRow; $row++) {
            $tanggal = $sheet->getCell("A{$row}")->getValue();

            if (!$tanggal || $tanggal === '-') continue;

            $timestamp = strtotime($tanggal);
            $hari = date('N', $timestamp); // 6 = Sabtu, 7 = Minggu
            $tglStr = date('Y-m-d', $timestamp);

            // Weekend
            if ($hari == 6 || $hari == 7) {
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFE5E5'); // pink lembut
            }

            // Libur Nasional
            if (in_array($tglStr, $this->liburNasional)) {
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFCCCC'); // merah muda
            }

            // Cuti Bersama
            if (in_array($tglStr, $this->cutiBersama)) {
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFE5CC'); // oranye soft
            }
        }

        // Lebar kolom
        $widths = [14, 10, 10, 14, 14, 14, 14, 12];
        foreach (range('A', 'H') as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        // Page setup
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        $sheet->getPageMargins()
            ->setTop(0.4)->setRight(0.3)->setLeft(0.3)->setBottom(0.4);

        $sheet->getPageSetup()->setHorizontalCentered(true);

        return [];
    }


    private function formatMenit($totalMenit)
    {
        if ($totalMenit <= 0) return '-';
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        return $menit === 0 ? "{$jam} jam" : "{$jam} jam {$menit} menit";
    }

    private function formatLembur($totalMenit)
    {
        $jam = floor($totalMenit / 60);
        return $jam > 0 ? "{$jam} jam" : '-';
    }

    public function title(): string
    {
        return $this->data['user']->name ?? 'Pegawai';
    }
}
