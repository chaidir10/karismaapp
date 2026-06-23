<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran</title>
    <style>
        @page { size: A4 landscape; margin: 3mm 5mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 1.5px 2px; text-align: center; font-size: 10px; line-height: 1.2; }
        th { background-color: #e0e0e0; font-weight: bold; }
        .weekend { background-color: #f2f2f2; }
        .page-break { page-break-after: always; }
        .cover { text-align: center; padding-top: 30vh; }
        .cover h1 { font-size: 20px; margin-bottom: 8px; }
        .cover h2 { font-size: 16px; margin-bottom: 4px; }
        .cover h3 { font-size: 14px; }
    </style>
</head>
<body>
    @php
    function fmtJM($m) { if (!is_numeric($m)) return $m ?: '-'; $j=floor($m/60); $s=$m%60; return ($j>0?$j.'j':'').($s>0?$s.'m':($j>0?'':'0m')); }
    function fmtM($m) { if (!is_numeric($m)) return $m ?: '-'; return $m.'m'; }
    @endphp

    @if(request('mode') === 'all')
    <div class="cover page-break">
        <h1>LAPORAN KEHADIRAN PEGAWAI</h1>
        <h2>Balai Kekarantinaan Kesehatan Kelas I Tarakan</h2>
        <br><br>
        <h3>Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', request('bulan'))->translatedFormat('F Y') }}</h3>
        <br>
        <h2>SELURUH PEGAWAI</h2>
    </div>
    @endif

    @foreach($laporan as $item)
    <div @if(!$loop->last) class="page-break" @endif>
        <table style="margin-bottom:2px; border:1px solid #333;">
            <tr>
                <td style="border:none; text-align:left; padding:2px 4px;">
                    <strong style="font-size:11px;">LAPORAN KEHADIRAN PEGAWAI BKK KELAS I TARAKAN</strong><br>
                    <span style="font-size:9px;">Periode: {{ $item['bulan'] ?? \Carbon\Carbon::createFromFormat('Y-m', request('bulan'))->translatedFormat('F Y') }}</span>
                </td>
                <td style="border:none; text-align:right; padding:2px 4px;">
                    <strong style="font-size:10px;">{{ $item['user']->name }}</strong>@if($item['is_shift'] ?? false) <span style="font-size:9px;">| {{ $item['shift_nama'] }}</span>@endif<br>
                    <span style="font-size:9px;">NIP: {{ $item['user']->nip }}</span>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="width:10%">Tanggal</th>
                    <th style="width:7%">Masuk</th>
                    <th style="width:7%">Pulang</th>
                    <th style="width:9%">Terlambat</th>
                    <th style="width:9%">Plg Cepat</th>
                    <th style="width:10%">Jam Kerja</th>
                    <th style="width:9%">Wkt Kurang</th>
                    <th style="width:17%">Lembur</th>
                    <th style="width:22%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item['rows'] as $row)
                <tr @if($row['is_weekend']) class="weekend" @endif>
                    <td>{{ $row['tanggal'] }}</td>
                    <td>{{ $row['masuk'] }}</td>
                    <td>{{ $row['pulang'] }}</td>
                    <td>{{ fmtM($row['keterlambatan']) }}</td>
                    <td>{{ fmtM($row['pulang_cepat']) }}</td>
                    <td>{{ is_numeric($row['jam_kerja']) ? fmtJM($row['jam_kerja']) : $row['jam_kerja'] }}</td>
                    <td>{{ fmtM($row['waktu_kurang']) }}</td>
                    <td style="font-size:9px;">{{ is_numeric($row['lembur']) && $row['lembur'] > 0 ? fmtJM($row['lembur']) . ' (' . ($row['lembur_waktu'] ?? '') . ')' : '-' }}</td>
                    <td style="font-size:9px; white-space:nowrap;">{{ $row['status_masuk'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table style="margin-top:2px; border:none;">
            <tr><td colspan="4" style="border:none; font-weight:bold; font-size:10px; padding:2px 0;">Ringkasan:</td></tr>
            <tr>
                <td style="border:none; font-size:10px; padding:2px 0; white-space:nowrap;">Total Hari Kerja:</td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 8px;"><strong>{{ $item['total_hari_kerja'] }} Hari</strong></td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 24px; white-space:nowrap;">Total Jam Kerja:</td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 8px;"><strong>{{ fmtJM($item['summary']['total_jam_kerja']) }}</strong></td>
            </tr>
            <tr>
                <td style="border:none; font-size:10px; padding:2px 0; white-space:nowrap;">Total Hari Hadir:</td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 8px;"><strong>{{ $item['total_hari_hadir'] ?? 0 }} Hari</strong></td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 24px; white-space:nowrap;">Total Keterlambatan:</td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 8px;"><strong>{{ $item['summary']['total_keterlambatan'] }} menit</strong></td>
            </tr>
            <tr>
                <td style="border:none; font-size:10px; padding:2px 0; white-space:nowrap;">Total Hari Lembur:</td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 8px;"><strong>{{ $item['total_hari_lembur'] ?? 0 }} Hari</strong></td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 24px; white-space:nowrap;">Total Waktu Kurang:</td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 8px;"><strong>{{ $item['summary']['total_kekurangan'] }} menit</strong></td>
            </tr>
            <tr>
                <td style="border:none; font-size:10px; padding:2px 0; white-space:nowrap;">Total Hari Cuti/DL:</td>
                <td style="border:none; font-size:10px; padding:2px 0 2px 8px;"><strong>{{ $item['total_hari_cuti'] ?? 0 }} Hari</strong></td>
                <td style="border:none;"></td><td style="border:none;"></td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
