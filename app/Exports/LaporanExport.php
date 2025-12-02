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
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

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

        // 🔹 Tambahkan baris kosong
        $rows[] = [];

        // 🔹 Ringkasan: kolom A merge ke B, nilai di C merge ke D
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
        // 🔹 Header utama
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->getStyle('A1:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 🔹 Header tabel
        $sheet->getStyle('A5:H5')->getFont()->setBold(true)->getColor()->setRGB('000000');
        $sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
        $sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 🔹 Border data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:H{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 🔹 Lebar kolom
        $widths = [14, 10, 10, 14, 14, 14, 14, 12];
        foreach (range('A', 'H') as $i => $col) {
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }

        // 🔹 Page setup
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        // 🔹 Margin
        $sheet->getPageMargins()
            ->setTop(0.4)
            ->setRight(0.3)
            ->setLeft(0.3)
            ->setBottom(0.4);

        $sheet->getPageSetup()->setHorizontalCentered(true);

        // 🔹 Merge kolom A–B dan C–D di bagian ringkasan
        $highestRow = $sheet->getHighestRow();
        // Diasumsikan ringkasan = 6 baris terakhir
        for ($r = $highestRow - 5; $r <= $highestRow; $r++) {
            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->mergeCells("C{$r}:D{$r}");
        }

        // 🔹 Styling ringkasan
        $sheet->getStyle("A" . ($highestRow - 5) . ":D{$highestRow}")
            ->getFont()->setBold(true);

        $sheet->getStyle("A" . ($highestRow - 5) . ":B{$highestRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("C" . ($highestRow - 5) . ":D{$highestRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // 🔹 WARNA MERAH UNTUK HARI SABTU DAN MINGGU
        $startDataRow = 6; // Baris pertama data (setelah header)
        $endDataRow = $highestRow - 7; // Baris terakhir data (sebelum ringkasan)
        
        for ($row = $startDataRow; $row <= $endDataRow; $row++) {
            $tanggal = $sheet->getCell("A{$row}")->getValue();
            
            // Coba parse tanggal dengan beberapa format
            $parsedDate = null;
            
            // Format umum dari database/array
            if (strtotime($tanggal) !== false) {
                $parsedDate = Carbon::parse($tanggal);
            }
            // Format Excel (angka serial)
            elseif (is_numeric($tanggal) && $tanggal > 0) {
                $parsedDate = Carbon::createFromTimestamp(
                    ($tanggal - 25569) * 86400 // Konversi dari Excel serial date
                );
            }
            
            if ($parsedDate) {
                $dayOfWeek = $parsedDate->dayOfWeek; // 6 = Sabtu, 0 = Minggu
                
                if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
                    // Set warna merah untuk seluruh baris (kolom A sampai H)
                    $sheet->getStyle("A{$row}:H{$row}")
                        ->getFont()
                        ->getColor()
                        ->setARGB(Color::COLOR_RED);
                }
            }
        }

        return [];
    }
}