<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
            ['Tanggal', 'Masuk', 'Pulang', 'Keterlambatan', 'Pulang Cepat', 'Jam Kerja', 'Waktu Kurang'],
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
                $row['keterlambatan'] !== '-' ? $row['keterlambatan'] . ' menit' : '-',
                $row['pulang_cepat'] !== '-' ? $row['pulang_cepat'] . ' menit' : '-',
                $row['jam_kerja'] !== '-' ? $this->formatMenit($row['jam_kerja']) : '-',
                $row['waktu_kurang'] !== '-' ? $row['waktu_kurang'] . ' menit' : '-',
            ];
        }

        // Tambahkan baris kosong dan total ringkasan
        $rows[] = [];
        $rows[] = [
            'Total Hari Kerja: ' . $this->data['total_hari_kerja'],
            'Total Keterlambatan: ' . $this->data['summary']['total_keterlambatan'] . ' menit',
            'Total Pulang Cepat: ' . $this->data['summary']['total_pulang_cepat'] . ' menit',
            'Total Jam Kerja: ' . $this->formatMenit($this->data['summary']['total_jam_kerja']),
            'Total Waktu Kurang: ' . $this->data['summary']['total_kekurangan'] . ' menit',
            'Total Lembur: ' . $this->formatMenit($this->data['summary']['total_lembur']),
        ];

        return $rows;
    }

    private function formatMenit($totalMenit)
    {
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        if ($totalMenit <= 0) return '-';
        if ($menit == 0) return "{$jam} jam";
        return "{$jam} jam {$menit} menit";
    }

    public function title(): string
    {
        return $this->data['user']->name;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('0A9396');
        $sheet->getStyle('A4:G4')->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
