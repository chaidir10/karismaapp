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
        $bulan = $this->data['bulan'] ?? now()->format('F Y');
        return [
            ['LAPORAN KEHADIRAN PEGAWAI BKK KELAS I TARAKAN', '', '', '', '', '', '', '', 'Nama: ' . $this->data['user']->name . $shiftInfo],
            ['Periode: ' . $bulan, '', '', '', '', '', '', '', 'NIP: ' . $this->data['user']->nip],
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
                    ? $this->formatMenitShort($row['lembur']) . (!empty($row['lembur_waktu']) ? ' (' . $row['lembur_waktu'] . ')' : '')
                    : '-',
                $row['status_masuk'],
            ];
        }

        // Ringkasan — 9 kolom: A+B=label kiri, C=value kiri, D kosong, E+F=label kanan, G+H+I=value kanan
        $rows[] = ['Ringkasan:'];
        $rows[] = ['Total Hari Kerja', '', ': ' . $this->data['total_hari_kerja'] . ' Hari', '', 'Total Jam Kerja', '', ': ' . $this->formatMenit($this->data['summary']['total_jam_kerja'])];
        $rows[] = ['Total Hari Hadir', '', ': ' . ($this->data['total_hari_hadir'] ?? 0) . ' Hari', '', 'Total Waktu Kurang', '', ': ' . $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Hari Cuti/DL', '', ': ' . ($this->data['total_hari_cuti'] ?? 0) . ' Hari', '', 'Total Hari Lembur', '', ': ' . ($this->data['total_hari_lembur'] ?? 0) . ' Hari'];
        $rows[] = ['Total Keterlambatan', '', ': ' . $this->data['summary']['total_keterlambatan'] . ' menit'];

        return $rows;
    }

    private function formatMenit($totalMenit)
    {
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        if ($totalMenit <= 0) return '-';
        return $menit === 0 ? "{$jam} jam" : "{$jam} jam {$menit} menit";
    }

    private function formatMenitShort($totalMenit)
    {
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        if ($totalMenit <= 0) return '-';
        return $menit === 0 ? "{$jam}j" : "{$jam}j{$menit}m";
    }

    public function title(): string
    {
        return $this->data['user']->name;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'I';
        $lastRow = $sheet->getHighestRow();

        // --- Header (baris 1-2) ---
        $sheet->mergeCells("A1:H1");
        $sheet->mergeCells("A2:H2");
        // NIP dan Nama di kolom I (kanan)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('I1:I2')->getFont()->setSize(9);
        $sheet->getStyle('I1:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // --- Baris 3 = spacer ---
        $sheet->getRowDimension(3)->setRowHeight(4);

        // --- Kolom header (baris 4) ---
        $sheet->getStyle("A4:{$lastCol}4")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A4:{$lastCol}4")->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E2F3');
        $sheet->getStyle("A4:{$lastCol}4")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // --- Border seluruh data (dari header kolom sampai akhir) ---
        $sheet->getStyle("A4:{$lastCol}{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // --- Lebar kolom ---
        //         A:Tgl  B:Masuk C:Plg  D:Telat E:PlgCpt F:JamKrj G:Kurang H:Lembur  I:Status
        $widths = [12,    10,     10,    12,     12,      14,      12,      20,        28];
        foreach (range('A', $lastCol) as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        // --- Font dan alignment data (mulai baris 5) ---
        $dataStart = 5;
        $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")
            ->getFont()->setSize(9);
        $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(false);
        $sheet->getStyle("A{$dataStart}:A{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- Row height (compact) ---
        for ($r = 1; $r <= $lastRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(14);
        }
        $sheet->getRowDimension(1)->setRowHeight(16);
        $sheet->getRowDimension(4)->setRowHeight(16);

        // --- Page setup: LANDSCAPE, fit 1 halaman lebar ---
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()
            ->setTop(0.15)->setRight(0.15)->setLeft(0.15)->setBottom(0.15)
            ->setHeader(0.1)->setFooter(0.1);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setPrintArea("A1:{$lastCol}{$lastRow}");

        // --- Summary: 5 rows (1 header + 4 data) ---
        $summaryStart = $lastRow - 4;
        $ds = $summaryStart + 1;
        $de = $summaryStart + 4; // last data row (Total Keterlambatan)

        // Ringkasan header — merge full row
        $sheet->mergeCells("A{$summaryStart}:{$lastCol}{$summaryStart}");

        // Rows 1-3 of data (ds to de-1): left label A+B, left value C, right label E+F, right value G+H+I
        for ($r = $ds; $r <= $de - 1; $r++) {
            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->mergeCells("E{$r}:F{$r}");
            $sheet->mergeCells("G{$r}:{$lastCol}{$r}");
        }
        // Last row (Total Keterlambatan): label A+B, value C+D+E+F+G+H+I
        $sheet->mergeCells("A{$de}:B{$de}");
        $sheet->mergeCells("C{$de}:{$lastCol}{$de}");

        // Font bold + alignment
        $sheet->getStyle("A{$summaryStart}:{$lastCol}{$lastRow}")
            ->getFont()->setBold(true);
        $sheet->getStyle("A{$summaryStart}:{$lastCol}{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Border: header row
        $sheet->getStyle("A{$summaryStart}:{$lastCol}{$summaryStart}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // Border: left side (A-C) all data rows
        $sheet->getStyle("A{$ds}:C{$de}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // Border: right side (E-I) rows 1-3 only
        $deLast3 = $de - 1;
        $sheet->getStyle("E{$ds}:{$lastCol}{$deLast3}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // --- Pewarnaan: weekend/libur & cuti ---
        $colorStart = 5;
        $rowIndex = 0;
        foreach ($this->data['rows'] as $row) {
            $r = $colorStart + $rowIndex;
            if ($r >= $summaryStart) break;

            if (!empty($row['is_cuti'])) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E8DAEF');
            } elseif (!empty($row['is_weekend'])) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FCE4E4');
            }

            $rowIndex++;
        }

        return [];
    }
}
