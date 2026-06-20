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
        th, td { border: 1px solid #000; padding: 3px 5px; text-align: center; font-size: 9px; }
        th { font-weight: bold; font-size: 9px; }
        .left { text-align: left; }
        .notes { margin-top: 10px; font-size: 8px; line-height: 1.5; }
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
                <th style="width:4%">No</th>
                <th style="width:22%" class="left">Nama Pegawai</th>
                <th style="width:12%">NIP</th>
                <th style="width:7%">Hadir</th>
                <th style="width:7%">Tidak Hadir</th>
                <th style="width:8%">Tepat Masuk</th>
                <th style="width:6%">Telat</th>
                <th style="width:8%">Pulang Tepat</th>
                <th style="width:8%">Pulang Cepat</th>
                <th style="width:8%">Skor Masuk (60%)</th>
                <th style="width:8%">Skor Pulang (40%)</th>
                <th style="width:8%">Performa</th>
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
                <td>{{ number_format($item['skor_masuk'], 1) }}</td>
                <td>{{ number_format($item['skor_pulang'], 1) }}</td>
                <td>{{ number_format($item['performa'], 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="notes">
        <strong>Keterangan Sistem Penilaian:</strong><br>
        Skor Masuk = (Hari Tepat Masuk / Hari Kerja Efektif) x 60 &nbsp;&nbsp;|&nbsp;&nbsp;
        Skor Pulang = (Hari Pulang Tepat / Hari Kerja Efektif) x 40 &nbsp;&nbsp;|&nbsp;&nbsp;
        Total Performa = Skor Masuk + Skor Pulang (maks. 100%)<br>
        Penilaian hanya berlaku pada hari kerja (Senin-Jumat, non-libur nasional). Lembur tidak termasuk dalam penilaian performa.
    </div>
</body>
</html>
