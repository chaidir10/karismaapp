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

        // jika bulan kosong → tetap kasih 1 baris
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

        // baris kosong sebelum ringkasan
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

        // Merge ringkasan jika baris cukup
        $highestRow = $sheet->getHighestRow();
        $ringkasanMulai = $highestRow - 5;

        if ($ringkasanMulai > 6) {
            for ($r = $ringkasanMulai; $r <= $highestRow; $r++) {
                $sheet->mergeCells("A{$r}:B{$r}");
                $sheet->mergeCells("C{$r}:D{$r}");
            }
        }

        return [];
    }
}
