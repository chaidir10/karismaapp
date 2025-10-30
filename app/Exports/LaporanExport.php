<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

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
class LaporanPerPegawaiSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN KEHADIRAN'],
            [
                $this->data['user']->name . ' (NIP. ' . $this->data['user']->nip . ') - ' .
                ($this->data['user']->jabatan ?? 'Pegawai')
            ],
            [],
            ['Tanggal', 'Masuk', 'Pulang', 'Keterlambatan', 'Pulang Cepat', 'Jam Kerja', 'Waktu Kurang', 'Lembur'],
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
                $row['jam_kerja'] !== '-' ? $this->formatJamMenit($row['jam_kerja']) : '-',
                $row['waktu_kurang'] !== '-' ? $row['waktu_kurang'] . ' mnt' : '-',
                $row['lembur'] !== '-' ? $this->formatLemburJam($row['lembur']) : '-',
            ];
        }

        // Baris kosong dan ringkasan
        $rows[] = [];
        $rows[] = ['Total Hari Kerja', $this->data['total_hari_kerja']];
        $rows[] = ['Total Keterlambatan', $this->data['summary']['total_keterlambatan'] . ' menit'];
        $rows[] = ['Total Pulang Cepat', $this->data['summary']['total_pulang_cepat'] . ' menit'];
        $rows[] = ['Total Jam Kerja', $this->formatJamMenit($this->data['summary']['total_jam_kerja'])];
        $rows[] = ['Total Waktu Kurang', $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Lembur', $this->formatLemburJam($this->data['summary']['total_lembur'])];

        return $rows;
    }

    /**
     * Format total jam dan menit (contoh: 8 jam 30 menit)
     */
    private function formatJamMenit($totalMenit)
    {
        if ($totalMenit <= 0) return '-';
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        return $menit > 0 ? "{$jam} jam {$menit} menit" : "{$jam} jam";
    }

    /**
     * Format lembur hanya jam saja (contoh: 8 jam 58 menit → 8 jam)
     */
    private function formatLemburJam($totalMenit)
    {
        if ($totalMenit <= 0) return '-';
        $jam = floor($totalMenit / 60);
        return "{$jam} jam";
    }

    public function title(): string
    {
        return $this->data['user']->name;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styles
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('0A9396');
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal('center');

        // Border untuk semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A4:H{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle('thin');

        // ✅ Page setup agar pas 1 halaman A4 Landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        $sheet->getPageMargins()
            ->setTop(0.3)
            ->setRight(0.3)
            ->setLeft(0.3)
            ->setBottom(0.3);

        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);

        return [];
    }
}
