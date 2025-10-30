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

        // Tambahkan baris kosong + total di bawahnya
        $rows[] = [];
        $rows[] = ['Total Hari Kerja', $this->data['total_hari_kerja']];
        $rows[] = ['Total Keterlambatan (mnt)', $this->data['summary']['total_keterlambatan']];
        $rows[] = ['Total Pulang Cepat (mnt)', $this->data['summary']['total_pulang_cepat']];
        $rows[] = ['Total Jam Kerja (mnt)', $this->data['summary']['total_jam_kerja']];
        $rows[] = ['Total Waktu Kurang (mnt)', $this->data['summary']['total_kekurangan']];
        $rows[] = ['Total Lembur (mnt)', $this->data['summary']['total_lembur']];

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PRESENSI PEGAWAI'],
            ['Nama: ' . $this->data['user']->name],
            ['NIP: ' . $this->data['user']->nip],
            [],
            ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Keterlambatan (mnt)', 'Pulang Cepat (mnt)', 'Jam Kerja (mnt)', 'Waktu Kurang (mnt)', 'Lembur (mnt)'],
        ];
    }

    public function title(): string
    {
        return $this->data['user']->name;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
            5    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0A9396']]],
        ];
    }
}
