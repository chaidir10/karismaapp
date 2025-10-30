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
        return [
            ['LAPORAN KEHADIRAN PEGAWAI'],
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

        // Baris kosong dan ringkasan total
        $rows[] = [];
        $rows[] = ['Total Hari Kerja', $this->data['total_hari_kerja']];
        $rows[] = ['Total Keterlambatan', $this->data['summary']['total_keterlambatan'] . ' menit'];
        $rows[] = ['Total Pulang Cepat', $this->data['summary']['total_pulang_cepat'] . ' menit'];
        $rows[] = ['Total Jam Kerja', $this->formatJamMenit($this->data['summary']['total_jam_kerja'])];
        $rows[] = ['Total Waktu Kurang', $this->data['summary']['total_kekurangan'] . ' menit'];
        $rows[] = ['Total Lembur', $this->formatLemburJam($this->data['summary']['total_lembur'])];

        return $rows;
    }

    private function formatJamMenit($totalMenit)
    {
        if ($totalMenit <= 0) return '-';
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        return $menit > 0 ? "{$jam} jam {$menit} menit" : "{$jam} jam";
    }

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
        // Font umum
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Header besar
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Header tabel
        $sheet->getStyle('A4:H4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A9396']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
        ]);

        // Border seluruh data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A4:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        // Lebar kolom disesuaikan agar sama seperti contoh
        $sheet->getColumnDimension('A')->setWidth(14); // Tanggal
        $sheet->getColumnDimension('B')->setWidth(10); // Masuk
        $sheet->getColumnDimension('C')->setWidth(10); // Pulang
        $sheet->getColumnDimension('D')->setWidth(15); // Keterlambatan
        $sheet->getColumnDimension('E')->setWidth(15); // Pulang Cepat
        $sheet->getColumnDimension('F')->setWidth(15); // Jam Kerja
        $sheet->getColumnDimension('G')->setWidth(16); // Waktu Kurang
        $sheet->getColumnDimension('H')->setWidth(12); // Lembur

        // Rata tengah untuk kolom waktu
        $sheet->getStyle('A5:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Page setup agar muat di 1 lembar A4 landscape
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

        return [];
    }
}
