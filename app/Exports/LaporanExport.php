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
        $rows[] = ['Total Hari Kerja', $this->data['total_hari_kerja'], 'Ringkasan presensi ' . $this->data['user']->name . ' (NIP. ' . $this->data['user']->nip . ') - Pranata Komputer Ahli Pertama'];
        $rows[] = ['Total Keterlambatan', $this->data['summary']['total_keterlambatan'] . ' menit'];
        $rows[] = ['Total Pulang Cepat', $this->data['summary']['total_pulang_cepat'] . ' menit'];
        $rows[] = ['Total Jam Kerja', $this->data['summary']['total_jam_kerja']];
        $rows[] = ['Total Waktu Kurang', $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Lembur', $this->data['summary']['total_lembur']];

        return $rows;
    }

    public function title(): string
    {
        // Hanya ambil nama depan untuk judul sheet
        $namaParts = explode(' ', $this->data['user']->name);
        return $namaParts[0];
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

        // Border semua data
        $lastRow = $sheet->getHighestRow();
        $dataStartRow = 4;
        $sheet->getStyle("A{$dataStartRow}:H{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Lebar kolom disesuaikan
        $widths = [14, 12, 12, 16, 14, 16, 14, 12];
        foreach (range('A', 'H') as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        // Format ringkasan - mulai dari baris terakhir dikurangi 6
        $summaryStart = $lastRow - 6;
        $summaryEnd = $lastRow;

        // Baris pertama ringkasan (Total Hari Kerja) - merge kolom B sampai H untuk deskripsi
        $sheet->mergeCells("C{$summaryStart}:H{$summaryStart}");
        
        // Format khusus untuk baris ringkasan
        for ($r = $summaryStart; $r <= $summaryEnd; $r++) {
            $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true);
            
            // Untuk baris pertama ringkasan, ratakan kiri untuk kolom C
            if ($r === $summaryStart) {
                $sheet->getStyle("C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
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