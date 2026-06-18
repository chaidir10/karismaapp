@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<style>
    :root {
        --blue: #3B82F6; --blue-light: #EFF6FF;
        --green: #10B981; --green-light: #ECFDF5;
        --amber: #F59E0B; --amber-light: #FFFBEB;
        --red: #EF4444; --red-light: #FEF2F2;
        --indigo: #6366F1; --indigo-light: #EEF2FF;
        --text: #0F172A; --text-2: #64748B; --text-3: #94A3B8;
        --surface: #F8FAFC; --surface-2: #F1F5F9; --border: #E2E8F0;
        --white: #FFFFFF;
        --radius: 18px; --radius-sm: 12px;
    }

    .dash-wrap {
        padding: 24px;
        background: var(--surface);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* ── Page Header ─────────────────────────── */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .page-title { font-size: 22px; font-weight: 700; color: var(--text); margin: 0 0 4px; }
    .page-sub { font-size: 13px; color: var(--text-2); margin: 0; }

    .date-pill {
        display: flex;
        align-items: center;
        gap: 7px;
        background: white;
        border: 1px solid var(--border);
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-2);
        white-space: nowrap;
    }

    .date-pill i { color: var(--blue); }

    /* ── KPI Cards ───────────────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .kpi-card {
        background: var(--white);
        border-radius: var(--radius);
        padding: 20px;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: default;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .kpi-icon.blue { background: var(--blue-light); color: var(--blue); }
    .kpi-icon.green { background: var(--green-light); color: var(--green); }
    .kpi-icon.amber { background: var(--amber-light); color: var(--amber); }
    .kpi-icon.red { background: var(--red-light); color: var(--red); }
    .kpi-icon.indigo { background: var(--indigo-light); color: var(--indigo); }

    .kpi-value { font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; margin-bottom: 4px; }
    .kpi-label { font-size: 12px; color: var(--text-2); font-weight: 500; }
    .kpi-trend {
        font-size: 11px;
        font-weight: 600;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 3px;
    }
    .kpi-trend.up { color: var(--green); }
    .kpi-trend.down { color: var(--red); }

    /* ── Attendance Chart ────────────────────── */
    .chart-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
        margin-bottom: 28px;
    }

    .card-box {
        background: var(--white);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .card-box-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
    }

    .card-box-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-box-title i { font-size: 15px; color: var(--blue); }

    .card-box-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .badge-blue { background: var(--blue-light); color: var(--blue); }
    .badge-amber { background: var(--amber-light); color: var(--amber); }
    .badge-red { background: var(--red-light); color: var(--red); }
    .badge-green { background: var(--green-light); color: var(--green); }
    .badge-gray { background: var(--surface-2); color: var(--text-2); }

    .card-box-body { padding: 20px; }

    /* Chart container */
    .chart-container { position: relative; height: 200px; }

    /* Donut chart (SVG-based for zero dependencies) */
    .donut-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 16px;
        padding: 16px;
    }

    .donut-svg { width: 120px; height: 120px; }

    .donut-legend { width: 100%; }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        padding: 5px 0;
        border-bottom: 1px solid var(--surface-2);
    }
    .legend-item:last-child { border-bottom: none; }

    .legend-dot-label { display: flex; align-items: center; gap: 7px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-count { font-weight: 700; color: var(--text); font-size: 12px; }

    /* ── Data Grid ───────────────────────────── */
    .data-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* ── Table ───────────────────────────────── */
    .smart-table { width: 100%; border-collapse: collapse; }

    .smart-table thead tr { background: var(--surface-2); }
    .smart-table th {
        padding: 11px 14px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: var(--text-2);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }
    .smart-table th.center { text-align: center; }
    .smart-table td {
        padding: 11px 14px;
        border-bottom: 1px solid var(--surface-2);
        font-size: 12px;
        color: var(--text);
        vertical-align: middle;
    }
    .smart-table td.center { text-align: center; }
    .smart-table tbody tr {
        cursor: pointer;
        transition: background 0.15s;
    }
    .smart-table tbody tr:hover { background: var(--surface); }
    .smart-table tbody tr:last-child td { border-bottom: none; }

    /* ── Badges ──────────────────────────────── */
    .tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 20px;
    }
    .tag.masuk { background: var(--blue-light); color: var(--blue); }
    .tag.pulang { background: var(--amber-light); color: var(--amber); }
    .tag.on-time { background: var(--green-light); color: var(--green); }
    .tag.late { background: var(--red-light); color: var(--red); }
    .tag.pending { background: var(--amber-light); color: var(--amber); }
    .tag.neutral { background: var(--surface-2); color: var(--text-2); }

    /* ── Action Buttons ──────────────────────── */
    .action-row { display: flex; gap: 6px; justify-content: center; }

    .act-btn {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .act-btn.approve { background: var(--green-light); color: var(--green); }
    .act-btn.approve:hover { background: var(--green); color: white; }
    .act-btn.reject { background: var(--red-light); color: var(--red); }
    .act-btn.reject:hover { background: var(--red); color: white; }

    /* ── Empty ───────────────────────────────── */
    .empty-row td {
        padding: 36px 20px;
        text-align: center;
        color: var(--text-3);
        cursor: default !important;
    }
    .empty-row:hover { background: none !important; }
    .empty-row i { font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.4; }

    /* ── Modal ───────────────────────────────── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.open { display: flex; }

    .modal-box {
        background: var(--white);
        border-radius: 22px;
        width: 90%;
        max-width: 540px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 24px 64px rgba(0,0,0,0.2);
        animation: modalIn 0.2s ease;
    }

    .modal-box.wide { max-width: 700px; }

    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.96) translateY(8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
    }

    .modal-head-title { font-size: 16px; font-weight: 700; color: var(--text); }

    .modal-x {
        width: 32px; height: 32px;
        background: var(--surface-2);
        border: none;
        border-radius: 10px;
        color: var(--text-2);
        font-size: 16px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .modal-x:hover { background: var(--red-light); color: var(--red); }

    .modal-body-inner { padding: 20px 24px; }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 16px;
    }

    .detail-cell { display: flex; flex-direction: column; gap: 4px; }
    .detail-cell.full { grid-column: 1/-1; }
    .detail-cell label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-2); }
    .detail-cell span { font-size: 13px; color: var(--text); font-weight: 500; }

    .map-pane {
        height: 260px;
        border-radius: var(--radius-sm);
        overflow: hidden;
        border: 1px solid var(--border);
        background: var(--surface-2);
        position: relative;
        margin-bottom: 12px;
    }

    #presensiMap { width: 100%; height: 100%; }

    .map-spinner, .map-err {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--text-2);
        font-size: 12px;
        background: var(--surface-2);
    }

    .foto-thumb {
        max-width: 100%;
        max-height: 180px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        object-fit: cover;
    }

    .modal-foot {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
        padding: 16px 24px;
        border-top: 1px solid var(--border);
    }

    .btn-base {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-close-modal { background: var(--surface-2); color: var(--text-2); }
    .btn-close-modal:hover { background: var(--border); }
    .btn-approve { background: var(--green); color: white; }
    .btn-approve:hover { background: #059669; }
    .btn-reject { background: var(--red); color: white; }
    .btn-reject:hover { background: #DC2626; }

    /* ── Responsive ──────────────────────────── */
    @media (max-width: 1100px) {
        .chart-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .dash-wrap { padding: 16px; }
        .kpi-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
        .data-grid { grid-template-columns: 1fr; }
        .kpi-value { font-size: 24px; }
        .kpi-icon { width: 44px; height: 44px; font-size: 18px; }
        .modal-box { width: 95%; }
        .detail-grid { grid-template-columns: 1fr; }
        .modal-foot { flex-direction: column; }
        .modal-foot .btn-base, .modal-foot form { width: 100%; }
        .modal-foot .btn-base { justify-content: center; }
    }
    @media (max-width: 480px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .page-header { flex-direction: column; }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

<div class="dash-wrap">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard Admin</h1>
            <p class="page-sub">Ringkasan presensi & aktivitas pegawai</p>
        </div>
        <div class="date-pill">
            <i class="fas fa-calendar-day"></i>
            <span id="liveDateAdmin">{{ now()->translatedFormat('l, d M Y') }}</span>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon blue"><i class="fas fa-user-check"></i></div>
            <div>
                <div class="kpi-value">{{ $jumlahHadir ?? 0 }}</div>
                <div class="kpi-label">Hadir Hari Ini</div>
                <div class="kpi-trend up"><i class="fas fa-arrow-up" style="font-size:9px"></i> Aktif</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon green"><i class="fas fa-users"></i></div>
            <div>
                <div class="kpi-value">{{ $jumlahPegawai ?? 0 }}</div>
                <div class="kpi-label">Total Pegawai</div>
                <div class="kpi-trend up"><i class="fas fa-circle" style="font-size:7px"></i> Terdaftar</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon amber"><i class="fas fa-clock"></i></div>
            <div>
                <div class="kpi-value">{{ count($presensiPending ?? []) }}</div>
                <div class="kpi-label">Pending Review</div>
                <div class="kpi-trend down"><i class="fas fa-exclamation-circle" style="font-size:9px"></i> Perlu aksi</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon red"><i class="fas fa-map-marker-alt"></i></div>
            <div>
                <div class="kpi-value">{{ collect($presensiPending ?? [])->where('status','pending')->count() }}</div>
                <div class="kpi-label">Di Luar Radius</div>
                <div class="kpi-trend down"><i class="fas fa-triangle-exclamation" style="font-size:9px"></i> Luar wilayah</div>
            </div>
        </div>
    </div>

    <!-- Chart Row -->
    <div class="chart-row">
        <!-- Attendance Chart (7 days) -->
        <div class="card-box">
            <div class="card-box-head">
                <div class="card-box-title"><i class="fas fa-chart-bar"></i> Kehadiran 7 Hari Terakhir</div>
                <span class="card-box-badge badge-blue">Minggu Ini</span>
            </div>
            <div class="card-box-body">
                <div class="chart-container" id="attendanceChartWrap">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Donut -->
        <div class="card-box">
            <div class="card-box-head">
                <div class="card-box-title"><i class="fas fa-circle-half-stroke"></i> Status Hari Ini</div>
            </div>
            <div class="donut-wrap">
                <svg class="donut-svg" viewBox="0 0 120 120" id="donutChart">
                    <!-- rendered by JS -->
                </svg>
                <div class="donut-legend" id="donutLegend">
                    <!-- rendered by JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="data-grid">
        <!-- Pending Approvals -->
        <div class="card-box">
            <div class="card-box-head">
                <div class="card-box-title"><i class="fas fa-hourglass-half"></i> Perlu Persetujuan</div>
                <span class="card-box-badge badge-amber">{{ count($presensiPending ?? []) }} pending</span>
            </div>
            <div style="overflow-x:auto">
                <table class="smart-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pegawai</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th class="center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pendingTableBody">
                        @forelse($presensiPending ?? [] as $idx => $p)
                        <tr data-user="{{ $p->user->name ?? 'N/A' }}"
                            data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}"
                            data-jenis="{{ $p->jenis ?? '' }}"
                            data-jam="{{ $p->jam ?? '-' }}"
                            data-lokasi="{{ $p->lokasi ?? '' }}"
                            data-foto="{{ $p->foto ? asset('storage/'.$p->foto) : '' }}"
                            data-approve="{{ route('admin.presensi.approve', $p->id) }}"
                            data-reject="{{ route('admin.presensi.reject', $p->id) }}"
                            onclick="openPendingModal(this)">
                            <td>{{ $idx + 1 }}</td>
                            <td style="font-weight:500">{{ $p->user->name ?? 'N/A' }}</td>
                            <td style="color:var(--text-2);font-size:11px">{{ \Carbon\Carbon::parse($p->tanggal ?? now())->format('d/m') }}</td>
                            <td><span class="tag {{ $p->jenis ?? '' }}">{{ ucfirst($p->jenis ?? '') }}</span></td>
                            <td class="center">
                                <div class="action-row" onclick="event.stopPropagation()">
                                    @if(Auth::user()->can_approve_pengajuan)
                                    <form method="POST" action="{{ route('admin.presensi.approve', $p->id) }}">
                                        @csrf
                                        <button type="submit" class="act-btn approve" title="Setujui"><i class="fas fa-check"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.presensi.reject', $p->id) }}">
                                        @csrf
                                        <button type="submit" class="act-btn reject" title="Tolak"><i class="fas fa-times"></i></button>
                                    </form>
                                    @else
                                    <span style="color:var(--text-3);font-size:11px">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="5">
                                <i class="fas fa-inbox"></i>
                                Tidak ada presensi pending
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="card-box">
            <div class="card-box-head">
                <div class="card-box-title"><i class="fas fa-clipboard-list"></i> Presensi Hari Ini</div>
                <span class="card-box-badge badge-blue">{{ count($presensiHariIni ?? []) }} aktivitas</span>
            </div>
            <div style="overflow-x:auto">
                <table class="smart-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Jam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensiHariIni ?? [] as $idx => $p)
                        <tr style="cursor:default">
                            <td>{{ $idx + 1 }}</td>
                            <td style="font-weight:500">{{ $p->user->name ?? 'N/A' }}</td>
                            <td><span class="tag {{ $p->jenis ?? '' }}">{{ ucfirst($p->jenis ?? '') }}</span></td>
                            <td style="font-family:monospace;font-size:12px;font-weight:600">{{ $p->jam ?? '-' }}</td>
                            <td>
                                @if(($p->jenis ?? '') === 'masuk')
                                    <span class="tag {{ $p->terlambat ? 'late' : 'on-time' }}">{{ $p->terlambat ? 'Terlambat' : 'Tepat Waktu' }}</span>
                                @elseif(($p->jenis ?? '') === 'pulang')
                                    <span class="tag {{ ($p->waktu_kurang_menit ?? 0) > 0 ? 'late' : 'on-time' }}">{{ ($p->waktu_kurang_menit ?? 0) > 0 ? 'Kurang' : 'Tepat Waktu' }}</span>
                                @else
                                    <span class="tag neutral">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row" style="cursor:default">
                            <td colspan="5">
                                <i class="fas fa-calendar-xmark"></i>
                                Belum ada presensi hari ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ── Pending Detail Modal ─────────────────────────────── -->
<div id="modalPending" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-head">
            <div class="modal-head-title"><i class="fas fa-map-marker-alt" style="color:var(--amber);margin-right:8px;"></i>Detail Presensi Pending</div>
            <button class="modal-x" onclick="closeModal('modalPending')">×</button>
        </div>
        <div class="modal-body-inner">
            <div class="detail-grid">
                <div class="detail-cell"><label>Nama Pegawai</label><span id="md-nama">—</span></div>
                <div class="detail-cell"><label>Tanggal</label><span id="md-tanggal">—</span></div>
                <div class="detail-cell"><label>Jenis</label><span id="md-jenis">—</span></div>
                <div class="detail-cell"><label>Jam</label><span id="md-jam">—</span></div>
                <div class="detail-cell full"><label>Koordinat Lokasi</label><span id="md-lokasi">—</span></div>
            </div>

            <label style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-2);display:block;margin-bottom:8px;">Peta Lokasi</label>
            <div class="map-pane">
                <div id="presensiMap"></div>
                <div id="mapSpinner" class="map-spinner"><i class="fas fa-spinner fa-spin fa-lg"></i><span>Memuat peta...</span></div>
                <div id="mapErr" class="map-err" style="display:none"><i class="fas fa-location-crosshairs fa-lg"></i><span>Koordinat tidak tersedia</span></div>
            </div>

            <label style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-2);display:block;margin-bottom:8px;">Foto Presensi</label>
            <div id="md-foto"><span style="color:var(--text-3);font-size:13px;font-style:italic">Tidak ada foto</span></div>
        </div>
        <div class="modal-foot">
            @if(Auth::user()->can_approve_pengajuan)
            <form id="formApprove" method="POST" style="margin:0">@csrf
                <button type="submit" class="btn-base btn-approve"><i class="fas fa-check"></i> Setujui</button>
            </form>
            <form id="formReject" method="POST" style="margin:0">@csrf
                <button type="submit" class="btn-base btn-reject"><i class="fas fa-times"></i> Tolak</button>
            </form>
            @endif
            <button class="btn-base btn-close-modal" onclick="closeModal('modalPending')">Tutup</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    buildBarChart();
    buildDonut();
    initModalListeners();
});

// ── Bar Chart (7-day attendance) ──────────────────────────
function buildBarChart() {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx) return;

    // Generate last 7 days labels
    const days = [];
    const now = new Date();
    for (let i = 6; i >= 0; i--) {
        const d = new Date(now);
        d.setDate(d.getDate() - i);
        days.push(d.toLocaleDateString('id-ID', { weekday: 'short' }));
    }

    // Use server data if available, otherwise fallback to zeros
    const hadirData = @json($chartDataHadir ?? [0,0,0,0,0,0,0]);
    const terlambatData = @json($chartDataTerlambat ?? [0,0,0,0,0,0,0]);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [
                {
                    label: 'Hadir',
                    data: hadirData,
                    backgroundColor: '#3B82F6',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Terlambat',
                    data: terlambatData,
                    backgroundColor: '#FCA5A5',
                    borderRadius: 6,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 11, family: 'Inter' }, usePointStyle: true, pointStyleWidth: 8 }
                },
                tooltip: {
                    backgroundColor: '#0F172A',
                    titleFont: { size: 12 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, family: 'Inter' }, color: '#94A3B8' }
                },
                y: {
                    grid: { color: '#F1F5F9' },
                    ticks: { font: { size: 11, family: 'Inter' }, color: '#94A3B8', stepSize: 1 },
                    beginAtZero: true
                }
            }
        }
    });
}

// ── Donut (SVG) ───────────────────────────────────────────
function buildDonut() {
    const total = {{ $jumlahPegawai ?? 0 }};
    const hadir = {{ $jumlahHadir ?? 0 }};
    const pending = {{ count($presensiPending ?? []) }};
    const belum = Math.max(0, total - hadir);

    const segments = [
        { label: 'Hadir', count: hadir, color: '#10B981' },
        { label: 'Pending', count: pending, color: '#F59E0B' },
        { label: 'Belum Presensi', count: belum, color: '#E2E8F0' },
    ];

    const totalVal = segments.reduce((s, g) => s + g.count, 0) || 1;
    const cx = 60, cy = 60, r = 45, thick = 14;
    let startAngle = -Math.PI / 2;

    let paths = '';
    segments.forEach(seg => {
        if (seg.count <= 0) return;
        const angle = (seg.count / totalVal) * 2 * Math.PI;
        const endAngle = startAngle + angle;
        const x1 = cx + r * Math.cos(startAngle);
        const y1 = cy + r * Math.sin(startAngle);
        const x2 = cx + r * Math.cos(endAngle);
        const y2 = cy + r * Math.sin(endAngle);
        const large = angle > Math.PI ? 1 : 0;
        paths += `<path d="M ${x1} ${y1} A ${r} ${r} 0 ${large} 1 ${x2} ${y2}" fill="none" stroke="${seg.color}" stroke-width="${thick}" stroke-linecap="round"/>`;
        startAngle = endAngle;
    });

    const svg = document.getElementById('donutChart');
    if (svg) {
        svg.innerHTML = paths + `<text x="60" y="57" text-anchor="middle" font-family="Inter" font-weight="800" font-size="18" fill="#0F172A">${hadir}</text><text x="60" y="70" text-anchor="middle" font-family="Inter" font-size="9" fill="#94A3B8">hadir</text>`;
    }

    const legend = document.getElementById('donutLegend');
    if (legend) {
        legend.innerHTML = segments.map(seg =>
            `<div class="legend-item">
                <div class="legend-dot-label">
                    <div class="legend-dot" style="background:${seg.color}"></div>
                    <span style="color:#64748B">${seg.label}</span>
                </div>
                <span class="legend-count">${seg.count}</span>
            </div>`
        ).join('');
    }
}

// ── Modal ─────────────────────────────────────────────────
let pendingMap = null, pendingCoords = null;

function initModalListeners() {
    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) closeModal(o.id); });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal('modalPending');
    });
}

function openPendingModal(row) {
    document.getElementById('md-nama').textContent = row.dataset.user || '—';
    document.getElementById('md-tanggal').textContent = row.dataset.tanggal || '—';
    document.getElementById('md-jenis').textContent = (row.dataset.jenis||'').charAt(0).toUpperCase() + (row.dataset.jenis||'').slice(1);
    document.getElementById('md-jam').textContent = row.dataset.jam || '—';
    document.getElementById('md-lokasi').textContent = row.dataset.lokasi || '—';

    const fotoEl = document.getElementById('md-foto');
    fotoEl.innerHTML = row.dataset.foto
        ? `<img src="${row.dataset.foto}" class="foto-thumb" onerror="this.style.display='none'">`
        : '<span style="color:var(--text-3);font-size:13px;font-style:italic">Tidak ada foto</span>';

    document.getElementById('formApprove').action = row.dataset.approve || '';
    document.getElementById('formReject').action = row.dataset.reject || '';

    let lat = NaN, lng = NaN;
    if (row.dataset.lokasi) {
        const parts = row.dataset.lokasi.split(',');
        if (parts.length === 2) { lat = parseFloat(parts[0]); lng = parseFloat(parts[1]); }
    }
    pendingCoords = { lat, lng };

    const modal = document.getElementById('modalPending');
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';

    renderPendingMap();
}

function closeModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('open');
    m.style.display = '';
    document.body.style.overflow = '';
    if (id === 'modalPending') destroyPendingMap();
}

function renderPendingMap() {
    const spinner = document.getElementById('mapSpinner');
    const err = document.getElementById('mapErr');
    spinner.style.display = 'flex';
    err.style.display = 'none';

    destroyPendingMap();

    if (!pendingCoords || isNaN(pendingCoords.lat)) {
        spinner.style.display = 'none';
        err.style.display = 'flex';
        return;
    }

    setTimeout(() => {
        const el = document.getElementById('presensiMap');
        if (!el || !el.offsetWidth) { spinner.style.display = 'none'; err.style.display = 'flex'; return; }
        try {
            pendingMap = L.map('presensiMap', { zoomControl: true }).setView([pendingCoords.lat, pendingCoords.lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM', maxZoom: 19 }).addTo(pendingMap);
            L.marker([pendingCoords.lat, pendingCoords.lng]).addTo(pendingMap).bindPopup('Lokasi Presensi').openPopup();
            pendingMap.invalidateSize();
            spinner.style.display = 'none';
        } catch(e) {
            spinner.style.display = 'none';
            err.style.display = 'flex';
        }
    }, 250);
}

function destroyPendingMap() {
    if (pendingMap) { try { pendingMap.remove(); } catch(e){} pendingMap = null; }
}
</script>

@endsection