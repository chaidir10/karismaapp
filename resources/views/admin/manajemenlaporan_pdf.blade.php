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
        .summary { width: auto !important; text-align: left !important; }
        .summary td { border: none !important; text-align: left !important; padding: 1px 0; white-space: nowrap; font-size: 10px; }
        .page-break { page-break-after: always; }
        .cover { text-align: center; padding-top: 30vh; }
        .cover h1 { font-size: 20px; margin-bottom: 8px; }
        .cover h2 { font-size: 16px; margin-bottom: 4px; }
        .cover h3 { font-size: 14px; }
    </style>
</head>
<body>
    @php
    function fmtJM($m) { if (!is_numeric($m)) return $m ?: '-'; $j=floor($m/60); $s=$m%60; return ($j>0?$j.'j ':'').($s>0?$s.'m':($j>0?'':'0m')); }
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
        <table style="margin-bottom:2px; border:none;">
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
                    <th style="width:7%">Terlambat</th>
                    <th style="width:7%">Plg Cepat</th>
                    <th style="width:7%">Jam Kerja</th>
                    <th style="width:7%">Wkt Kurang</th>
                    <th style="width:14%">Lembur</th>
                    <th style="width:34%">Status</th>
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

        @php
            $ring = [
                ['Total Hari Kerja', $item['total_hari_kerja'].' Hari', 'Total Jam Kerja', fmtJM($item['summary']['total_jam_kerja'])],
                ['Total Hari Hadir', ($item['total_hari_hadir'] ?? 0).' Hari', 'Total Keterlambatan', $item['summary']['total_keterlambatan'].' menit'],
                ['Total Hari Lembur', ($item['total_hari_lembur'] ?? 0).' Hari', 'Total Waktu Kurang', $item['summary']['total_kekurangan'].' menit'],
                ['Total Hari Cuti/DL', ($item['total_hari_cuti'] ?? 0).' Hari', '', ''],
            ];
        @endphp
        <div style="margin-top:2px; font-size:10px; text-align:left;">
            <strong style="display:block; padding:2px 0;">Ringkasan:</strong>
            <table class="summary">
                @foreach($ring as $r)
                <tr>
                    <td>{{ $r[0] }}</td>
                    <td style="padding-left:4px;">: <strong>{{ $r[1] }}</strong></td>
                    @if($r[2])
                    <td style="padding-left:20px;">{{ $r[2] }}</td>
                    <td style="padding-left:4px;">: <strong>{{ $r[3] }}</strong></td>
                    @else
                    <td colspan="2"></td>
                    @endif
                </tr>
                @if($r[0] === 'Total Hari Cuti/DL' && !empty($item['cuti_details']))
                    @foreach($item['cuti_details'] as $cd)
                    @php
                        $m = $cd['mulai']; $s = $cd['selesai'];
                        if ($m->month === $s->month && $m->year === $s->year) {
                            $tgl = $m->translatedFormat('j') . ' s.d ' . $s->translatedFormat('j F Y');
                        } else {
                            $tgl = $m->translatedFormat('j F') . ' s.d ' . $s->translatedFormat('j F Y');
                        }
                    @endphp
                    <tr>
                        <td style="color:#888;"></td>
                        <td style="padding-left:4px; color:#555;">: {{ $cd['hari'] }} Hari ({{ $tgl }}) <em>{{ $cd['label'] }}</em></td>
                        <td colspan="2"></td>
                    </tr>
                    @endforeach
                @endif
                @endforeach
            </table>
        </div>
    </div>
    @endforeach
</body>
</html>
