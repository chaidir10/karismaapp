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
use AzisHapidin\IndoHoliday\IndoHoliday;

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
    protected $holidayHelper;

    public function __construct($data)
    {
        $this->data = $data;
        $this->holidayHelper = new IndoHoliday();
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PRESENSI PEGAWAI'],
            ['Nama: ' . $this->data['user']->name],
            ['NIP: ' . $this->data['user']->nip],
            [],
            ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Keterlambatan', 'Pulang Cepat', 'Jam Kerja', 'Waktu Kurang', 'Lembur', 'Keterangan'],
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->data['rows'] as $row) {
            $tanggal = $row['tanggal'];
            $isHoliday = $this->isHoliday($tanggal);
            $keterangan = $this->getHolidayDescription($tanggal);
            
            $rows[] = [
                $tanggal, // format dd/mm/YYYY
                $row['masuk'],
                $row['pulang'],
                $row['keterlambatan'] !== '-' ? $row['keterlambatan'] . ' mnt' : '-',
                $row['pulang_cepat'] !== '-' ? $row['pulang_cepat'] . ' mnt' : '-',
                $row['jam_kerja'] !== '-' ? $this->formatMenit($row['jam_kerja']) : '-',
                $row['waktu_kurang'] !== '-' ? $row['waktu_kurang'] . ' mnt' : '-',
                $row['lembur'] !== '-' ? $this->formatLembur($row['lembur']) : '-',
                $isHoliday ? $keterangan : ''
            ];
        }

        // baris kosong
        $rows[] = [];

        // ringkasan
        $rows[] = ['Total Hari Kerja', '', $this->data['total_hari_kerja'], '', '', '', '', '', ''];
        $rows[] = ['Total Keterlambatan', '', $this->data['summary']['total_keterlambatan'] . ' menit', '', '', '', '', '', ''];
        $rows[] = ['Total Pulang Cepat', '', $this->data['summary']['total_pulang_cepat'] . ' menit', '', '', '', '', '', ''];
        $rows[] = ['Total Jam Kerja', '', $this->formatMenit($this->data['summary']['total_jam_kerja']), '', '', '', '', '', ''];
        $rows[] = ['Total Waktu Kurang', '', $this->data['summary']['total_kekurangan'] . ' menit', '', '', '', '', '', ''];
        $rows[] = ['Total Lembur', '', $this->formatLembur($this->data['summary']['total_lembur']), '', '', '', '', '', ''];

        return $rows;
    }

    /**
     * Cek apakah tanggal adalah libur nasional/cuti bersama
     */
    private function isHoliday(string $tanggal): bool
    {
        try {
            $dt = \DateTime::createFromFormat('d/m/Y', $tanggal);
            if (!$dt) return false;

            $year = $dt->format('Y');
            $month = $dt->format('m');
            $day = $dt->format('d');

            // Ambil data libur untuk tahun tersebut
            $holidays = $this->holidayHelper->getHoliday($year);

            foreach ($holidays as $holiday) {
                $holidayDate = \DateTime::createFromFormat('Y-m-d', $holiday['date']);
                if ($holidayDate && $holidayDate->format('d/m/Y') === $tanggal) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Dapatkan keterangan libur
     */
    private function getHolidayDescription(string $tanggal): string
    {
        try {
            $dt = \DateTime::createFromFormat('d/m/Y', $tanggal);
            if (!$dt) return '';

            $year = $dt->format('Y');
            $holidays = $this->holidayHelper->getHoliday($year);

            foreach ($holidays as $holiday) {
                $holidayDate = \DateTime::createFromFormat('Y-m-d', $holiday['date']);
                if ($holidayDate && $holidayDate->format('d/m/Y') === $tanggal) {
                    return $holiday['description'] . ' (' . $holiday['type'] . ')';
                }
            }

            return '';
        } catch (\Exception $e) {
            return '';
        }
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
        // Header utama
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->getStyle('A1:I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header tabel
        $sheet->getStyle('A5:I5')->getFont()->setBold(true)->getColor()->setRGB('000000');
        $sheet->getStyle('A5:I5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
        $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:I{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Lebar kolom
        $widths = [14, 10, 10, 14, 14, 14, 14, 12, 25];
        foreach (range('A', 'I') as $i => $col) {
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

        // Merge ringkasan (6 baris terakhir)
        $highestRow = $sheet->getHighestRow();
        for ($r = $highestRow - 5; $r <= $highestRow; $r++) {
            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->mergeCells("C{$r}:D{$r}");
        }

        $sheet->getStyle("A" . ($highestRow - 5) . ":D{$highestRow}")
            ->getFont()->setBold(true);

        // ============================================================
        // 🔥 PEWARNAAN WEEKEND DAN LIBUR NASIONAL
        // ============================================================

        $dataStart = 6; // baris data pertama setelah header
        for ($r = $dataStart; $r <= $highestRow - 7; $r++) {
            $tanggal = $sheet->getCell("A{$r}")->getValue();
            if (!$tanggal) continue;

            // Parse format dd/mm/YYYY
            $dt = \DateTime::createFromFormat('d/m/Y', $tanggal);
            if (!$dt) continue;

            $dayOfWeek = (int)$dt->format('N'); // 6=Sabtu, 7=Minggu
            $isHoliday = $this->isHoliday($tanggal);

            // Warna MERAH untuk weekend (Sabtu/Minggu)
            if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                $sheet->getStyle("A{$r}:I{$r}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FFCCCC');  // merah muda untuk weekend
                    
                // Tambah keterangan weekend di kolom keterangan
                $keterangan = $sheet->getCell("I{$r}")->getValue();
                $weekendDesc = $dayOfWeek == 6 ? 'Sabtu' : 'Minggu';
                if (empty($keterangan)) {
                    $sheet->setCellValue("I{$r}", "Weekend ({$weekendDesc})");
                } else {
                    $sheet->setCellValue("I{$r}", $keterangan . " + Weekend ({$weekendDesc})");
                }
            }

            // Warna MERAH TUA untuk libur nasional/cuti bersama
            if ($isHoliday) {
                $sheet->getStyle("A{$r}:I{$r}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FF6666');  // merah lebih terang untuk libur nasional
                    
                // Warna teks putih agar mudah dibaca
                $sheet->getStyle("A{$r}:I{$r}")
                    ->getFont()
                    ->getColor()
                    ->setRGB('FFFFFF');
            }
        }

        return [];
    }
}