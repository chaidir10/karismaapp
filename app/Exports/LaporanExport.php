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
            ['LAPORAN KEHADIRAN'],
            [$this->data['user']->name . ' (NIP. ' . $this->data['user']->nip . ') - Pranata Komputer Ahli Pertama'],
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
                $row['keterlambatan'],
                $row['pulang_cepat'],
                $row['jam_kerja'],
                $row['waktu_kurang'],
                $row['lembur'],
            ];
        }

        // Baris kosong + ringkasan
        $rows[] = [];
        $rows[] = ['Ringkasan presensi ' . $this->data['user']->name . ' (NIP. ' . $this->data['user']->nip . ') - Pranata Komputer Ahli Pertama'];
        $rows[] = ['Total Hari Kerja', $this->data['total_hari_kerja']];
        $rows[] = ['Total Keterlambatan', $this->data['summary']['total_keterlambatan'] . ' menit'];
        $rows[] = ['Total Pulang Cepat', $this->data['summary']['total_pulang_cepat'] . ' menit'];
        $rows[] = ['Total Jam Kerja', $this->data['summary']['total_jam_kerja']];
        $rows[] = ['Total Waktu Kurang', $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Lembur', $this->data['summary']['total_lembur']];

        return $rows;
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

        // Header utama
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Header tabel
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border untuk data presensi
        $lastDataRow = count($this->data['rows']) + 4; // +4 untuk header
        $sheet->getStyle("A4:H{$lastDataRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Format ringkasan - mulai dari baris setelah data
        $summaryStart = $lastDataRow + 2; // +2 untuk baris kosong
        $summaryEnd = $summaryStart + 6; // 7 baris ringkasan

        // Merge baris judul ringkasan
        $sheet->mergeCells("A{$summaryStart}:H{$summaryStart}");

        // Format judul ringkasan
        $sheet->getStyle("A{$summaryStart}")->getFont()->setBold(true);
        $sheet->getStyle("A{$summaryStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Format baris ringkasan
        for ($r = $summaryStart + 1; $r <= $summaryEnd; $r++) {
            $sheet->getStyle("A{$r}")->getFont()->setBold(true);
            $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // Border untuk ringkasan (opsional)
        $sheet->getStyle("A" . ($summaryStart + 1) . ":B{$summaryEnd}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Lebar kolom disesuaikan
        $widths = [14, 12, 12, 16, 14, 16, 14, 12];
        foreach (range('A', 'H') as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
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