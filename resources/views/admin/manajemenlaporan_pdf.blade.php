<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        h1,
        h2,
        h3,
        h4 {
            text-align: center;
            margin: 5px 0;
        }

        .summary {
            margin-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .cover {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .cover h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .cover h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .cover h3 {
            font-size: 18px;
        }
    </style>
</head>

<body>
    @php
    function formatJamMenit($menit) {
    if (!is_numeric($menit)) return $menit ?: '-';
    $jam = floor($menit / 60);
    $sisa = $menit % 60;
    return ($jam > 0 ? $jam.' jam ' : '') . ($sisa > 0 ? $sisa.' menit' : ($jam > 0 ? '' : '0 menit'));
    }

    function formatMenitOnly($menit) {
    if (!is_numeric($menit)) return $menit ?: '-';
    return $menit.' menit';
    }

    function formatJamOnly($menit) {
    if (!is_numeric($menit)) return $menit ?: '-';
    return floor($menit / 60).' jam (lembur)';
    }
    @endphp

    {{-- COVER --}}
    @if(request('mode') === 'all')
    <div class="cover page-break">
        <h1>LAPORAN KEHADIRAN PEGAWAI</h1>
        <h2>Balai Kekarantinaan Kesehatan Kelas I Tarakan</h2>
        <br><br><br><br>
        <h3>
            Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', request('bulan'))->translatedFormat('F Y') }}
        </h3>
        <br><br>
        <h2>SELURUH PEGAWAI</h2>
    </div>
    @endif

    {{-- LAPORAN PER PEGAWAI --}}
    @foreach($laporan as $idx => $item)
    <div @if(!$loop->last) class="page-break" @endif>
        <h3>{{ $item['user']->name }} (NIP. {{ $item['user']->nip }}) - {{ $item['user']->jabatan }}
            @if($item['is_shift'] ?? false)
                | <span style="color:#4f46e5">{{ $item['shift_nama'] }}</span>
            @endif
        </h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Keterlambatan</th>
                    <th>Pulang Cepat</th>
                    <th>Jam Kerja</th>
                    <th>Waktu Kurang</th>
                    <th>Lembur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item['rows'] as $row)
                <tr @if($row['is_weekend']) style="background-color: #f2f2f2;" @endif>
                    <td>{{ $row['tanggal'] }}</td>
                    <td>{{ $row['masuk'] }}</td>
                    <td>{{ $row['pulang'] }}</td>
                    <td>{{ formatMenitOnly($row['keterlambatan']) }}</td>
                    <td>{{ formatMenitOnly($row['pulang_cepat']) }}</td>
                    <td>{{ is_numeric($row['jam_kerja']) ? formatJamMenit($row['jam_kerja']) : $row['jam_kerja'] }}</td>
                    <td>{{ formatMenitOnly($row['waktu_kurang']) }}</td>
                    <td>
                        @if(is_numeric($row['lembur']) && $row['lembur'] > 0)
                            {{ formatJamMenit($row['lembur']) }}
                            <br><small>{{ $row['lembur_masuk'] ?? '-' }} - {{ $row['lembur_pulang'] ?? '-' }}</small>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <strong>Hari Kerja:</strong> {{ $item['total_hari_kerja'] }} |
            <strong>Hari Telat:</strong> {{ $item['total_hari_telat'] }} |
            <strong>Keterlambatan:</strong> {{ formatMenitOnly($item['summary']['total_keterlambatan']) }} |
            <strong>Pulang Cepat:</strong> {{ formatMenitOnly($item['summary']['total_pulang_cepat']) }} |
            <strong>Jam Kerja:</strong> {{ formatJamMenit($item['summary']['total_jam_kerja']) }} |
            <strong>Waktu Kurang:</strong> {{ formatMenitOnly($item['summary']['total_kekurangan']) }} |
            <strong>Lembur:</strong> {{ formatJamMenit($item['summary']['total_lembur']) }}
        </div>
    </div>
    @endforeach
</body>

</html>