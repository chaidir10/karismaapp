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
            ['LAPORAN KEHADIRAN PEGAWAI', '', '', '', '', '', '', 'Nama: ' . $this->data['user']->name . $shiftInfo],
            ['BKK KELAS I TARAKAN', '', '', '', '', '', '', 'NIP: ' . $this->data['user']->nip],
            ['Periode: ' . $bulan],
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

        $rows[] = ['Ringkasan:'];
        $rows[] = ['Total Hari Kerja:', $this->data['total_hari_kerja'] . ' Hari', '', '', 'Total Jam Kerja:', $this->formatMenit($this->data['summary']['total_jam_kerja'])];
        $rows[] = ['Total Hari Cuti/DL:', ($this->data['total_hari_cuti'] ?? 0) . ' Hari', '', '', 'Total Waktu Kurang:', $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Keterlambatan:', $this->data['summary']['total_keterlambatan'] . ' menit', '', '', 'Total Hari Lembur:', ($this->data['total_hari_lembur'] ?? 0) . ' Hari'];

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
        $lastRow = $sheet->getHighestRow();

        // --- Header (baris 1-3): kiri = judul, kanan = nama/nip ---
        $sheet->mergeCells("A1:G1");
        $sheet->mergeCells("H1:I1");
        $sheet->mergeCells("A2:G2");
        $sheet->mergeCells("H2:I2");
        $sheet->mergeCells("A3:{$lastCol}3");

        // Kiri: judul + instansi + periode
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A3')->getFont()->setSize(9);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Kanan: nama + nip
        $sheet->getStyle('H1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('H2')->getFont()->setSize(10);
        $sheet->getStyle('H1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // --- Kolom header (baris 5) ---
        $sheet->getStyle("A5:{$lastCol}5")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A5:{$lastCol}5")->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E2F3');
        $sheet->getStyle("A5:{$lastCol}5")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // --- Border seluruh data ---
        $sheet->getStyle("A5:{$lastCol}{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // --- Lebar kolom (landscape A4 ~39cm usable / 9 cols) ---
        $widths = [16, 14, 14, 16, 16, 18, 16, 16, 22];
        foreach (range('A', $lastCol) as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        // --- Font dan alignment data ---
        $sheet->getStyle("A6:{$lastCol}{$lastRow}")
            ->getFont()->setSize(9);
        $sheet->getStyle("A6:{$lastCol}{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle("A6:A{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- Row height data (compact agar muat 1 halaman) ---
        for ($r = 1; $r <= $lastRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(14);
        }
        $sheet->getRowDimension(1)->setRowHeight(16);
        $sheet->getRowDimension(4)->setRowHeight(4);
        $sheet->getRowDimension(5)->setRowHeight(16);

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

        // --- Summary rows (4 rows: ringkasan header + 3 data rows) ---
        $summaryStart = $lastRow - 3;
        $sheet->mergeCells("A{$summaryStart}:{$lastCol}{$summaryStart}");
        $sheet->getStyle("A{$summaryStart}:{$lastCol}{$lastRow}")
            ->getFont()->setBold(true);
        $sheet->getStyle("A{$summaryStart}:{$lastCol}{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("A{$summaryStart}:{$lastCol}{$lastRow}")
            ->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

        // --- Pewarnaan: weekend/libur & cuti ---
        $dataStart = 6;
        $rowIndex = 0;
        foreach ($this->data['rows'] as $row) {
            $r = $dataStart + $rowIndex;
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
