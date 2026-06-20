<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Performa Pegawai</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 0; }
        h2 { text-align: center; margin: 0 0 2px; font-size: 14px; }
        h3 { text-align: center; margin: 0 0 2px; font-size: 11px; font-weight: normal; }
        h4 { text-align: center; margin: 0 0 8px; font-size: 10px; font-weight: normal; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #000; padding: 3px 4px; text-align: center; font-size: 9px; }
        th { font-weight: bold; }
        .left { text-align: left; }
        .notes { margin-top: 10px; font-size: 8px; line-height: 1.6; }
        .notes strong { font-size: 8px; }
    </style>
</head>
<body>
    <h2>LAPORAN PERFORMA PEGAWAI</h2>
    <h3>Balai Kekarantinaan Kesehatan Kelas I Tarakan</h3>
    <h4>Periode: {{ $bulanNama }} &mdash; Hari Kerja Efektif: {{ $hariKerja }} hari</h4>

    <table>
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th style="width:18%" class="left">Nama Pegawai</th>
                <th style="width:10%">NIP</th>
                <th style="width:5%">Hadir</th>
                <th style="width:5%">Tidak Hadir</th>
                <th style="width:6%">Tepat Masuk</th>
                <th style="width:5%">Telat</th>
                <th style="width:6%">Pulang Tepat</th>
                <th style="width:6%">Pulang Cepat</th>
                <th style="width:6%">Jam Kerja Cukup</th>
                <th style="width:7%">Kehadiran (25%)</th>
                <th style="width:7%">Masuk (30%)</th>
                <th style="width:7%">Pulang (20%)</th>
                <th style="width:7%">Jam Kerja (25%)</th>
                <th style="width:7%">Performa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performa as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="left">{{ $item['nama'] }}</td>
                <td>{{ $item['nip'] ?? '-' }}</td>
                <td>{{ $item['hadir'] }}</td>
                <td>{{ $item['tidak_hadir'] }}</td>
                <td>{{ $item['tepat_masuk'] }}</td>
                <td>{{ $item['telat'] }}</td>
                <td>{{ $item['pulang_tepat'] }}</td>
                <td>{{ $item['pulang_cepat'] }}</td>
                <td>{{ $item['jam_kerja_cukup'] }}</td>
                <td>{{ number_format($item['skor_kehadiran'], 1) }}</td>
                <td>{{ number_format($item['skor_masuk'], 1) }}</td>
                <td>{{ number_format($item['skor_pulang'], 1) }}</td>
                <td>{{ number_format($item['skor_jam_kerja'], 1) }}</td>
                <td>{{ number_format($item['performa'], 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="notes">
        <strong>Keterangan Sistem Penilaian:</strong><br>
        1. Kehadiran (25%) = (Hari Hadir / Hari Kerja) x 25<br>
        2. Kedisiplinan Masuk (30%) = (Hari Tepat Masuk / Hari Kerja) x 30<br>
        3. Kedisiplinan Pulang (20%) = (Hari Pulang Tepat / Hari Kerja) x 20<br>
        4. Jam Kerja Terpenuhi (25%) = (Hari dgn Durasi Kerja >= Standar / Hari Kerja) x 25<br>
        Total Performa = Kehadiran + Masuk + Pulang + Jam Kerja (maks. 100%). Lembur tidak termasuk penilaian.
    </div>
</body>
</html>
