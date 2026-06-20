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

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        $shiftInfo = ($this->data['is_shift'] ?? false) ? ' | ' . $this->data['shift_nama'] : '';
        return [
            ['LAPORAN PRESENSI PEGAWAI'],
            ['Nama: ' . $this->data['user']->name . $shiftInfo],
            ['NIP: ' . $this->data['user']->nip],
            [],
            ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Keterlambatan', 'Pulang Cepat', 'Jam Kerja', 'Waktu Kurang', 'Lembur', 'Status'],
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
                is_numeric($row['lembur']) && $row['lembur'] > 0
                    ? $this->formatMenit($row['lembur']) . (!empty($row['lembur_waktu']) ? ' (' . $row['lembur_waktu'] . ')' : '')
                    : '-',
                $row['status_masuk'],
            ];
        }

        $rows[] = [];

        $rows[] = ['Total Hari Kerja', '', $this->data['total_hari_kerja']];
        $rows[] = ['Total Keterlambatan', '', $this->data['summary']['total_keterlambatan'] . ' menit'];
        $rows[] = ['Total Pulang Cepat', '', $this->data['summary']['total_pulang_cepat'] . ' menit'];
        $rows[] = ['Total Jam Kerja', '', $this->formatMenit($this->data['summary']['total_jam_kerja'])];
        $rows[] = ['Total Waktu Kurang', '', $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Hari Lembur', '', $this->data['total_hari_lembur'] ?? 0];
        $rows[] = ['Total Lembur', '', $this->formatMenit($this->data['summary']['total_lembur'])];

        return $rows;
    }

    private function formatMenit($totalMenit)
    {
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        if ($totalMenit <= 0) return '-';
        return $menit === 0 ? "{$jam} jam" : "{$jam} jam {$menit} menit";
    }

    public function title(): string
    {
        return $this->data['user']->name;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'I';

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:{$lastCol}5")->getFont()->setBold(true)->getColor()->setRGB('000000');
        $sheet->getStyle("A5:{$lastCol}5")->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('BFBFBF');
        $sheet->getStyle("A5:{$lastCol}5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:{$lastCol}{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $widths = [14, 10, 10, 14, 14, 14, 14, 12, 22];
        foreach (range('A', $lastCol) as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(1);
        $sheet->getPageMargins()
            ->setTop(0.3)->setRight(0.2)->setLeft(0.2)->setBottom(0.3);
        $sheet->getPageSetup()->setHorizontalCentered(true);

        $sheet->getPageSetup()->setPrintArea("A1:{$lastCol}{$highestRow}");
        $sheet->getSheetView()->setView('pageBreakPreview');

        $highestRow = $sheet->getHighestRow();
        for ($r = $highestRow - 6; $r <= $highestRow; $r++) {
            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->mergeCells("C{$r}:D{$r}");
        }
        $sheet->getStyle("A" . ($highestRow - 6) . ":D{$highestRow}")
            ->getFont()->setBold(true);

        // Pewarnaan: weekend & libur nasional dari data row
        $dataStart = 6;
        $rowIndex = 0;
        foreach ($this->data['rows'] as $row) {
            $r = $dataStart + $rowIndex;
            if ($r > $highestRow - 8) break;

            if (!empty($row['is_weekend'])) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FFCCCC');
            }

            $rowIndex++;
        }

        return [];
    }
}
