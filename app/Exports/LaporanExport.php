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

    public function __construct($data)
    {
        $this->data = $data;
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

        // Baris kosong + ringkasan
        $rows[] = [];
        $rows[] = ['Total Hari Kerja', $this->data['total_hari_kerja']];
        $rows[] = ['Total Keterlambatan', $this->data['summary']['total_keterlambatan'] . ' menit'];
        $rows[] = ['Total Pulang Cepat', $this->data['summary']['total_pulang_cepat'] . ' menit'];
        $rows[] = ['Total Jam Kerja', $this->formatMenit($this->data['summary']['total_jam_kerja'])];
        $rows[] = ['Total Waktu Kurang', $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Lembur', $this->formatLembur($this->data['summary']['total_lembur'])];

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
        // Merge judul
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        // Header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:H3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Tabel header
        $sheet->getStyle('A5:H5')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0A9396');
        $sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:H{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Lebar kolom disesuaikan agar pas di A4 landscape
        $widths = [14, 10, 10, 14, 14, 14, 14, 12];
        foreach (range('A', 'H') as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        // Hitung posisi ringkasan
        $summaryStart = $lastRow - 6; // baris pertama dari 7 total ringkasan
        $summaryEnd = $lastRow;

        // Tambahkan garis atas tebal pemisah sebelum ringkasan
        $sheet->getStyle("A" . ($summaryStart - 1) . ":H" . ($summaryStart - 1))
            ->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);

        // Format ringkasan
        for ($r = $summaryStart; $r <= $summaryEnd; $r++) {
            $sheet->mergeCells("A{$r}:G{$r}");
            $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true);
            $sheet->getStyle("A{$r}:G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A{$r}:H{$r}")
                ->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D9EAD3');
        }

        // Page setup A4 landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        $sheet->getPageMargins()
            ->setTop(0.4)
            ->setRight(0.3)
            ->setLeft(0.3)
            ->setBottom(0.4);

        $sheet->getPageSetup()->setHorizontalCentered(true);

        return [];
    }
}
