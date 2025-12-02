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

        // 🔹 Ringkasan
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
            ->setTop(0.4)->setRight(0.3)->setLeft(0.3)->setBottom(0.4);

        $sheet->getPageSetup()->setHorizontalCentered(true);


        /* =======================================================
         |  PERUBAHAN DIMULAI DI SINI
         |  WARNA MERAH UNTUK SABTU, MINGGU, LIBUR NASIONAL, CUTI
         ======================================================= */

        $dataStart = 6; // baris pertama berisi data tanggal
        $dataEnd = $this->data['total_hari_kerja'] + 5;

        // daftar libur nasional (bisa Anda ganti ambil dari DB)
        $liburNasional = $this->data['libur'] ?? [];  
        // contoh format:
        // $liburNasional = ['2025-01-01','2025-03-29','2025-04-18', ... ];

        for ($row = $dataStart; $row <= $dataEnd; $row++) {
            $tanggal = $sheet->getCell("A{$row}")->getValue();
            if (!$tanggal) continue;

            $carbonDate = Carbon::parse($tanggal);
            $hari = $carbonDate->dayOfWeek; // 0=minggu, 6=sabtu

            $isWeekend = ($hari === 0 || $hari === 6);
            $isLibur = in_array($tanggal, $liburNasional);

            if ($isWeekend || $isLibur) {
                // warna merah seluruh baris
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getFont()->getColor()->setRGB('FF0000');
            }
        }

        /* =======================================================
         |  PERUBAHAN SELESAI
         ======================================================= */


        // 🔹 Merge kolom A–B dan C–D di bagian ringkasan
        $highestRow = $sheet->getHighestRow();
        for ($r = $highestRow - 5; $r <= $highestRow; $r++) {
            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->mergeCells("C{$r}:D{$r}");
        }

        // 🔹 Styling ringkasan
        $sheet->getStyle("A" . ($highestRow - 5) . ":D{$highestRow}")
            ->getFont()->setBold(true);

        return [];
    }
}
