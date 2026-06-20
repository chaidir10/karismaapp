@extends('layouts.admin')

@section('title', 'Dashboard ')

@section('content')
<style>
    :root {
        --primary: #3b82f6;
        --primary-light: #60a5fa;
        --primary-dark: #2563eb;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --light: #f8fafc;
        --dark: #1e293b;
        --white: #ffffff;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
    }

    .admin-dashboard {
        padding: 20px;
        background: var(--light);
        min-height: 100vh;
    }

    .dashboard-header {
        margin-bottom: 30px;
    }

    .dashboard-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 5px 0;
    }

    .dashboard-subtitle {
        color: var(--gray-500);
        margin: 0;
        font-size: 14px;
    }

    /* Statistics Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--white);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 4px solid var(--primary);
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 5px 0;
        line-height: 1;
    }

    .stat-label {
        color: var(--gray-500);
        font-size: 14px;
        margin: 0;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        background: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 20px;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    .content-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid var(--gray-200);
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-badge {
        padding: 4px 10px;
        background: var(--gray-300);
        color: var(--gray-700);
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .card-content {
        padding: 0;
    }

    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: var(--gray-100);
    }

    .data-table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-600);
        font-size: 12px;
        text-transform: uppercase;
        border-bottom: 1px solid var(--gray-200);
    }

    .data-table th[data-sort] {
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
    }

    .data-table th[data-sort]:hover {
        color: var(--primary);
    }

    .data-table th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        font-size: 10px;
        opacity: 0.3;
        vertical-align: middle;
    }

    .data-table th.sort-asc .sort-icon,
    .data-table th.sort-desc .sort-icon {
        opacity: 1;
        color: var(--primary);
    }

    .data-table th.text-center {
        text-align: center;
    }

    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .data-table td.text-center {
        text-align: center;
    }

    .data-table tbody tr:hover {
        background: var(--gray-100);
    }

    .user-name {
        font-size: 12px;
        font-weight: 500;
        color: var(--dark);
    }

    /* Pagination */
    .table-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        border-top: 1px solid var(--gray-200);
        font-size: 12px;
        color: var(--gray-500);
    }

    .pagination-info {
        white-space: nowrap;
    }

    .pagination-buttons {
        display: flex;
        gap: 4px;
    }

    .pagination-buttons button {
        width: 30px;
        height: 30px;
        border: 1px solid var(--gray-200);
        background: var(--white);
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        color: var(--gray-600);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .pagination-buttons button:hover:not(:disabled) {
        background: var(--gray-100);
        border-color: var(--gray-300);
    }

    .pagination-buttons button.active {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary);
    }

    .pagination-buttons button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Badges */
    .badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .jenis-badge {
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary);
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
    }

    .status-badge.on-time {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-badge.late {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .status-badge.neutral {
        background: rgba(100, 116, 139, 0.1);
        color: var(--gray-500);
    }

    .status-badge.pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .date-cell {
        font-size: 13px;
        color: var(--gray-600);
    }

    .time-cell {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: var(--dark);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .inline-form {
        display: inline;
    }

    .btn-success,
    .btn-danger {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12px;
    }

    .btn-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .btn-success:hover {
        background: var(--success);
        color: var(--white);
    }

    .btn-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .btn-danger:hover {
        background: var(--danger);
        color: var(--white);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 30px 20px;
    }

    .empty-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        color: var(--gray-400);
    }

    .empty-content i {
        font-size: 32px;
    }

    .empty-content p {
        margin: 0;
        font-size: 13px;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal-container {
        background: var(--white);
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        position: relative;
    }

    .modal-large {
        max-width: 700px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--gray-200);
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 18px;
        color: var(--gray-500);
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: var(--gray-100);
        color: var(--danger);
    }

    .modal-content {
        padding: 15px 20px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 15px;
        margin-bottom: 12px;
    }

    .detail-item {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-item label {
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        min-width: fit-content;
        margin: 0;
    }

    .detail-item label::after {
        content: ':';
    }

    .detail-item span {
        font-size: 13px;
        color: var(--dark);
        word-break: break-word;
    }

    .modal-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: wrap;
        border-top: 1px solid var(--gray-200);
        padding-top: 20px;
    }

    .btn-secondary {
        padding: 8px 16px;
        background: var(--gray-300);
        color: var(--gray-700);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-secondary:hover {
        background: var(--gray-400);
    }

    .text-muted {
        color: var(--gray-400);
        font-style: italic;
    }

    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-row:hover {
        background: var(--gray-50) !important;
    }

    .bukti-image,
    .foto-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 6px;
        border: 1px solid var(--gray-200);
        object-fit: cover;
    }

    /* Foto + Map Side by Side */
    .media-row {
        display: flex !important;
        flex-direction: row !important;
        gap: 15px;
        margin-bottom: 12px;
        width: 100%;
        align-items: stretch;
    }

    .media-col {
        flex: 1 1 0%;
        min-width: 0;
        width: 50%;
        display: flex;
        flex-direction: column;
    }

    .media-col label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        margin-bottom: 6px;
        flex-shrink: 0;
    }

    .media-col label::after {
        content: '' !important;
    }

    .foto-wrapper {
        flex: 1;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
        background: var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .foto-wrapper .foto-image {
        width: 100%;
        height: auto;
        max-height: none;
        display: block;
        border: none;
        border-radius: 0;
    }

    .foto-wrapper .text-muted {
        font-size: 13px;
        padding: 40px 0;
    }

    /* Map Styles */
    .map-container {
        position: relative;
        flex: 1;
        min-height: 250px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
        background: var(--gray-100);
    }

    #presensiMap,
    #hariIniMap {
        width: 100%;
        height: 100%;
    }

    .map-loading,
    .map-error {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        pointer-events: none;
    }

    .map-loading {
        background: rgba(255, 255, 255, 0.9);
        color: var(--gray-500);
        z-index: 1000;
    }

    .map-error {
        background: var(--gray-100);
        color: var(--gray-500);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .admin-dashboard { padding: 15px; }
        .stats-grid { grid-template-columns: 1fr; gap: 15px; }
        .content-grid { gap: 15px; }
        .data-table th,
        .data-table td { padding: 10px 12px; font-size: 12px; }
        .stat-card { padding: 15px; }
        .stat-value { font-size: 24px; }
        .stat-icon { width: 40px; height: 40px; font-size: 16px; }
        .modal-container { width: 95%; margin: 20px; }
        .modal-large { max-width: 95%; }
        .detail-grid { grid-template-columns: 1fr; gap: 12px; }
        .modal-actions { flex-direction: column; }
        .modal-actions .inline-form,
        .modal-actions button { width: 100%; }
        .map-container { height: 220px; }
        .media-row { flex-direction: column !important; }
        .media-col { width: 100%; }
        .map-container { min-height: 220px; }
    }
</style>

<div class="admin-dashboard text-sm">
    <!-- Header -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard Admin</h1>
        <p class="dashboard-subtitle">Ringkasan aktivitas dan statistik sistem</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card" style="border-left-color:#10b981;">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahHadir ?? 0 }}</h3>
                <p class="stat-label">Hadir Hari Ini</p>
            </div>
            <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPegawai ?? 0 }}</h3>
                <p class="stat-label">Total Pegawai</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card" style="border-left-color:#f59e0b;">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPengajuan ?? 0 }}</h3>
                <p class="stat-label">Pengajuan Pending</p>
            </div>
            <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>

    {{-- Grafik + Performa --}}
    <div class="chart-performa-grid" style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:20px;">
        <div class="content-card" style="margin:0;">
            <div class="card-header">
                <h2 class="card-title">Tren Kehadiran 7 Hari</h2>
            </div>
            <div class="card-content" style="padding:15px;">
                <div style="position:relative; height:250px;">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="content-card" style="margin:0;">
            <div class="card-header">
                <h2 class="card-title">Performa Bulan Ini</h2>
            </div>
            <div class="card-content" style="padding:0; max-height:310px; overflow-y:auto;">
                @foreach($performaList as $idx => $pf)
                <div style="display:flex; align-items:center; gap:10px; padding:8px 14px; border-bottom:1px solid var(--gray-200); {{ $idx < 3 ? 'background:rgba(16,185,129,0.03);' : '' }}">
                    <div style="width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0;
                        {{ $idx === 0 ? 'background:#fbbf24;color:#fff;' : ($idx === 1 ? 'background:#94a3b8;color:#fff;' : ($idx === 2 ? 'background:#cd7f32;color:#fff;' : 'background:var(--gray-200);color:var(--gray-600);')) }}">
                        {{ $idx + 1 }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:12px; font-weight:600; color:var(--dark); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $pf['user']->name }}</div>
                        <div style="font-size:10px; color:var(--gray-500);">{{ $pf['hadir'] }} hadir · {{ $pf['telat'] }} telat</div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div style="font-size:14px; font-weight:700; color:{{ $pf['persen'] >= 90 ? '#10b981' : ($pf['persen'] >= 75 ? '#2563eb' : ($pf['persen'] >= 50 ? '#f59e0b' : '#ef4444')) }};">{{ $pf['persen'] }}%</div>
                    </div>
                </div>
                @endforeach
                @if(count($performaList) === 0)
                <div style="padding:20px; text-align:center; color:var(--gray-400); font-size:12px;">Belum ada data</div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            .chart-performa-grid { grid-template-columns: 1fr !important; }
        }
    </style>

    {{-- Content Grid --}}
    <div class="content-grid">

        {{-- Presensi Diluar Radius --}}
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Presensi Diluar Radius</h2>
                <span class="card-badge">{{ count($presensiPending ?? []) }} menunggu</span>
            </div>
            <div class="card-content">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th data-sort="text">Pegawai <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="date">Tanggal <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="text">Jenis <i class="fas fa-sort sort-icon"></i></th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="presensiPendingTable" data-paginate="10">
                            @forelse($presensiPending ?? [] as $index => $p)
                            <tr class="clickable-row"
                                data-user-name="{{ $p->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $p->jenis ?? '' }}"
                                data-jam="{{ $p->jam ?? '-' }}"
                                data-lokasi="{{ $p->lokasi ?? '' }}"
                                data-foto-url="{{ $p->foto ? asset('public/storage/' . $p->foto) : '' }}"
                                data-approve-url="{{ route('admin.presensi.approve', $p->id) }}"
                                data-reject-url="{{ route('admin.presensi.reject', $p->id) }}">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge jenis-badge">{{ ucfirst($p->jenis ?? '') }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if(Auth::user()->can_approve_pengajuan)
                                        <form action="{{ route('admin.presensi.approve', $p->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-success" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.presensi.reject', $p->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-danger" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-gray-400 text-xs">Tidak memiliki hak akses</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-inbox"></i>
                                        <p>Tidak ada presensi pending</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pengajuan Pending --}}
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Pengajuan Pending</h2>
                <span class="card-badge">{{ count($pengajuanPending ?? []) }} menunggu</span>
            </div>
            <div class="card-content">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th data-sort="text">Pegawai <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="date">Tanggal <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="text">Jenis <i class="fas fa-sort sort-icon"></i></th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengajuanPendingTable" data-paginate="10">
                            @forelse($pengajuanPending ?? [] as $index => $peng)
                            <tr class="clickable-row"
                                data-user-name="{{ $peng->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $peng->jenis ?? '' }}"
                                data-alasan="{{ $peng->alasan ?? 'Tidak ada alasan' }}"
                                data-bukti-url="{{ $peng->bukti ? asset('public/storage/' . $peng->bukti) : '' }}"
                                data-approve-url="{{ route('admin.pengajuan.approve', $peng->id) }}"
                                data-reject-url="{{ route('admin.pengajuan.reject', $peng->id) }}">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $peng->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">{{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge jenis-badge">{{ ucfirst($peng->jenis ?? '') }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if(Auth::user()->can_approve_pengajuan)
                                        <form action="{{ route('admin.pengajuan.approve', $peng->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-success" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.pengajuan.reject', $peng->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-danger" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-gray-400 text-xs">Tidak memiliki hak akses</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-inbox"></i>
                                        <p>Tidak ada pengajuan pending</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Presensi Hari Ini --}}
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Daftar Presensi Hari Ini</h2>
                <span class="card-badge">{{ count($presensiHariIni ?? []) }} aktivitas</span>
            </div>
            <div class="card-content">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th data-sort="text">Nama Pegawai <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="text">Jenis <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="text">Jam <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="text">Status <i class="fas fa-sort sort-icon"></i></th>
                            </tr>
                        </thead>
                        <tbody id="presensiHariIniTable" data-paginate="10">
                            @forelse($presensiHariIni ?? [] as $index => $p)
                            <tr class="clickable-row"
                                data-user-name="{{ $p->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $p->jenis ?? '' }}"
                                data-jam="{{ $p->jam ?? '-' }}"
                                data-lokasi="{{ $p->lokasi ?? '' }}"
                                data-foto-url="{{ $p->foto ? asset('public/storage/' . $p->foto) : '' }}"
                                data-status="{{ $p->status ?? '' }}"
                                data-status-label="@if(($p->jenis ?? '') === 'masuk'){{ $p->terlambat ? 'Terlambat' : 'Tepat Waktu' }}@elseif(($p->jenis ?? '') === 'pulang'){{ ($p->waktu_kurang_menit ?? 0) > 0 ? 'Waktu Kurang' : 'Tepat Waktu' }}@else -@endif">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge jenis-badge">{{ ucfirst($p->jenis ?? '') }}</span>
                                </td>
                                <td class="time-cell">{{ $p->jam ?? '-' }}</td>
                                <td>
                                    @if(($p->jenis ?? '') === 'masuk')
                                        @if($p->terlambat)
                                            <span class="status-badge late">Terlambat</span>
                                        @else
                                            <span class="status-badge on-time">Tepat Waktu</span>
                                        @endif
                                    @elseif(($p->jenis ?? '') === 'pulang')
                                        @if($p->waktu_kurang_menit > 0)
                                            <span class="status-badge late">Waktu Kurang</span>
                                        @else
                                            <span class="status-badge on-time">Tepat Waktu</span>
                                        @endif
                                    @else
                                        <span class="status-badge neutral">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-clipboard-list"></i>
                                        <p>Belum ada presensi hari ini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Lembur Hari Ini --}}
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Lembur Hari Ini</h2>
                <span class="card-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">{{ $lemburHariIni->where('jenis','masuk')->count() }} pegawai</span>
            </div>
            <div class="card-content">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Pegawai</th>
                                <th>Jenis</th>
                                <th>Jam</th>
                            </tr>
                        </thead>
                        <tbody id="lemburHariIniTable" data-paginate="10">
                            @forelse($lemburHariIni as $index => $l)
                            <tr>
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $l->user->name ?? 'N/A' }}</td>
                                <td><span class="badge" style="background:rgba(245,158,11,0.1);color:#d97706;border:1px solid rgba(245,158,11,0.2);">Lembur {{ ucfirst($l->jenis) }}</span></td>
                                <td class="time-cell">{{ $l->jam ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-moon"></i>
                                        <p>Tidak ada lembur hari ini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- end .content-grid --}}

</div>{{-- end .admin-dashboard --}}


{{-- ========== MODAL PRESENSI PENDING ========== --}}
<div id="modalPresensiPending" class="modal-overlay">
    <div class="modal-container modal-large">
        <div class="modal-header">
            <h3 class="modal-title">Detail Presensi Pending</h3>
            <button class="modal-close" onclick="closeModal('modalPresensiPending')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Pegawai</label>
                    <span id="detailPegawaiPresensi">-</span>
                </div>
                <div class="detail-item">
                    <label>Tanggal</label>
                    <span id="detailTanggalPresensi">-</span>
                </div>
                <div class="detail-item">
                    <label>Jenis</label>
                    <span id="detailJenisPresensi">-</span>
                </div>
                <div class="detail-item">
                    <label>Jam</label>
                    <span id="detailJamPresensi">-</span>
                </div>
                <div class="detail-item">
                    <label>Lokasi</label>
                    <span id="detailLokasiPresensi" style="font-size:11px">-</span>
                </div>
                <div class="detail-item">
                    <label>Status</label>
                    <span class="status-badge pending">Pending</span>
                </div>
            </div>
            <div class="media-row">
                <div class="media-col">
                    <label>Foto</label>
                    <div class="foto-wrapper" id="detailFotoPresensi">
                        <span class="text-muted">Tidak ada foto</span>
                    </div>
                </div>
                <div class="media-col">
                    <label>Peta Lokasi</label>
                    <div class="map-container">
                        <div id="presensiMap"></div>
                        <div id="mapLoading" class="map-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Memuat peta...</span>
                        </div>
                        <div id="mapError" class="map-error" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Koordinat tidak tersedia</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                @if(Auth::user()->can_approve_pengajuan)
                <form id="formApprovePresensi" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn-success" title="Setujui">
                        <i class="fas fa-check"></i> 
                    </button>
                </form>
                <form id="formRejectPresensi" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn-danger" title="Tolak">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
                @endif
                <button type="button" class="btn-secondary" onclick="closeModal('modalPresensiPending')">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ========== MODAL PENGAJUAN PENDING ========== --}}
<div id="modalPengajuanPending" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Detail Pengajuan Pending</h3>
            <button class="modal-close" onclick="closeModal('modalPengajuanPending')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Pegawai</label>
                    <span id="detailPegawaiPengajuan">-</span>
                </div>
                <div class="detail-item">
                    <label>Tanggal</label>
                    <span id="detailTanggalPengajuan">-</span>
                </div>
                <div class="detail-item">
                    <label>Jenis</label>
                    <span id="detailJenisPengajuan">-</span>
                </div>
                <div class="detail-item">
                    <label>Status</label>
                    <span class="status-badge pending">Pending</span>
                </div>
                <div class="detail-item full-width">
                    <label>Alasan</label>
                    <span id="detailAlasanPengajuan">-</span>
                </div>
            </div>
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--gray-500);text-transform:uppercase;margin-bottom:6px">Bukti</label>
                <div id="detailBuktiPengajuan">
                    <span class="text-muted">Tidak ada bukti</span>
                </div>
            </div>
            <div class="modal-actions">
                @if(Auth::user()->can_approve_pengajuan)
                <form id="formApprovePengajuan" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn-success" title="Setujui">
                        <i class="fas fa-check"></i> 
                </form>
                <form id="formRejectPengajuan" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn-danger" title="Tolak">
                        <i class="fas fa-times"></i> 
                    </button>
                </form>
                @endif
                <button type="button" class="btn-secondary" onclick="closeModal('modalPengajuanPending')">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ========== MODAL DETAIL PRESENSI HARI INI ========== --}}
<div id="modalDetailHariIni" class="modal-overlay">
    <div class="modal-container modal-large">
        <div class="modal-header">
            <h3 class="modal-title">Detail Presensi</h3>
            <button class="modal-close" onclick="closeModal('modalDetailHariIni')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Pegawai</label>
                    <span id="detailNamaHariIni">-</span>
                </div>
                <div class="detail-item">
                    <label>Tanggal</label>
                    <span id="detailTanggalHariIni">-</span>
                </div>
                <div class="detail-item">
                    <label>Jenis</label>
                    <span id="detailJenisHariIni">-</span>
                </div>
                <div class="detail-item">
                    <label>Jam</label>
                    <span id="detailJamHariIni">-</span>
                </div>
                <div class="detail-item">
                    <label>Kehadiran</label>
                    <span id="detailStatusHariIni">-</span>
                </div>
                <div class="detail-item">
                    <label>Verifikasi</label>
                    <span id="detailVerifikasiHariIni">-</span>
                </div>
                <div class="detail-item full-width">
                    <label>Lokasi</label>
                    <span id="detailLokasiHariIni" style="font-size:11px">-</span>
                </div>
            </div>
            <div class="media-row">
                <div class="media-col">
                    <label>Foto</label>
                    <div class="foto-wrapper" id="detailFotoHariIni">
                        <span class="text-muted">Tidak ada foto</span>
                    </div>
                </div>
                <div class="media-col">
                    <label>Peta Lokasi</label>
                    <div class="map-container">
                        <div id="hariIniMap"></div>
                        <div id="hariIniMapLoading" class="map-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Memuat peta...</span>
                        </div>
                        <div id="hariIniMapError" class="map-error" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Koordinat tidak tersedia</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('modalDetailHariIni')">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // ─── State ───────────────────────────────────────────────────────────────
    let presensiMap    = null;
    let currentMarker  = null;
    let pendingCoords  = null;

    let hariIniMap     = null;
    let hariIniMarker  = null;
    let hariIniCoords  = null;

    // ─── DOM Ready ───────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {

        // Klik baris presensi pending
        document.querySelectorAll('#presensiPendingTable .clickable-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                // Abaikan klik pada tombol aksi
                if (e.target.closest('.action-buttons')) return;

                openPresensiModal({
                    user_name         : this.dataset.userName,
                    tanggal           : this.dataset.tanggal,
                    jenis             : this.dataset.jenis,
                    jam               : this.dataset.jam,
                    lokasi            : this.dataset.lokasi,
                    foto_url          : this.dataset.fotoUrl,
                    approve_url       : this.dataset.approveUrl,
                    reject_url        : this.dataset.rejectUrl
                });
            });
        });

        // Klik baris pengajuan pending
        document.querySelectorAll('#pengajuanPendingTable .clickable-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.closest('.action-buttons')) return;

                openPengajuanModal({
                    user_name         : this.dataset.userName,
                    tanggal           : this.dataset.tanggal,
                    jenis             : this.dataset.jenis,
                    alasan            : this.dataset.alasan,
                    bukti_url         : this.dataset.buktiUrl,
                    approve_url       : this.dataset.approveUrl,
                    reject_url        : this.dataset.rejectUrl
                });
            });
        });

        // Klik baris presensi hari ini
        document.querySelectorAll('#presensiHariIniTable .clickable-row').forEach(function (row) {
            row.addEventListener('click', function () {
                openHariIniModal({
                    user_name    : this.dataset.userName,
                    tanggal      : this.dataset.tanggal,
                    jenis        : this.dataset.jenis,
                    jam          : this.dataset.jam,
                    lokasi       : this.dataset.lokasi,
                    foto_url     : this.dataset.fotoUrl,
                    status       : this.dataset.status,
                    status_label : this.dataset.statusLabel
                });
            });
        });

        // Tutup modal klik di luar container
        document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        // Tutup modal dengan ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay').forEach(function (m) {
                    closeModal(m.id);
                });
            }
        });
    });

    // ─── Modal Presensi ───────────────────────────────────────────────────────
    function openPresensiModal(data) {
        document.getElementById('detailPegawaiPresensi').textContent = data.user_name || 'N/A';
        document.getElementById('detailTanggalPresensi').textContent  = data.tanggal  || '-';
        document.getElementById('detailJenisPresensi').textContent    = capitalize(data.jenis);
        document.getElementById('detailJamPresensi').textContent      = data.jam      || '-';
        document.getElementById('detailLokasiPresensi').textContent   = data.lokasi
            ? data.lokasi
            : 'Tidak ada lokasi';

        // Foto
        var fotoEl = document.getElementById('detailFotoPresensi');
        fotoEl.innerHTML = data.foto_url
            ? '<img src="' + data.foto_url + '" alt="Foto Presensi" class="foto-image" onerror="this.style.display=\'none\'">'
            : '<span class="text-muted">Tidak ada foto</span>';

        // Form action
        setFormAction('formApprovePresensi', data.approve_url);
        setFormAction('formRejectPresensi',  data.reject_url);

        // Parse koordinat dari string lokasi format "lat,lng"
        var lat = NaN;
        var lng = NaN;

        if (data.lokasi) {
            var parts = data.lokasi.trim().split(',');
            if (parts.length === 2) {
                lat = parseFloat(parts[0].trim());
                lng = parseFloat(parts[1].trim());
            }
        }

        pendingCoords = {
            lat   : lat,
            lng   : lng,
            lokasi: data.lokasi || 'Lokasi tidak tersedia'
        };

        openModal('modalPresensiPending');
    }

    // ─── Modal Pengajuan ──────────────────────────────────────────────────────
    function openPengajuanModal(data) {
        document.getElementById('detailPegawaiPengajuan').textContent = data.user_name || 'N/A';
        document.getElementById('detailTanggalPengajuan').textContent  = data.tanggal  || '-';
        document.getElementById('detailJenisPengajuan').textContent    = capitalize(data.jenis);
        document.getElementById('detailAlasanPengajuan').textContent   = data.alasan   || 'Tidak ada alasan';

        var buktiEl = document.getElementById('detailBuktiPengajuan');
        buktiEl.innerHTML = data.bukti_url
            ? '<a href="' + data.bukti_url + '" target="_blank"><img src="' + data.bukti_url + '" class="bukti-image" onerror="this.style.display=\'none\'"></a>'
            : '<span class="text-muted">Tidak ada bukti</span>';

        setFormAction('formApprovePengajuan', data.approve_url);
        setFormAction('formRejectPengajuan',  data.reject_url);

        openModal('modalPengajuanPending');
    }

    // ─── Modal Detail Hari Ini ───────────────────────────────────────────────
    function openHariIniModal(data) {
        document.getElementById('detailNamaHariIni').textContent    = data.user_name || 'N/A';
        document.getElementById('detailTanggalHariIni').textContent = data.tanggal   || '-';
        document.getElementById('detailJenisHariIni').textContent   = capitalize(data.jenis);
        document.getElementById('detailJamHariIni').textContent     = data.jam       || '-';
        document.getElementById('detailLokasiHariIni').textContent  = data.lokasi    || 'Tidak ada lokasi';

        // Status kehadiran
        var statusEl = document.getElementById('detailStatusHariIni');
        var label = (data.status_label || '-').trim();
        var cls = 'neutral';
        if (label === 'Tepat Waktu') cls = 'on-time';
        else if (label === 'Terlambat' || label === 'Waktu Kurang') cls = 'late';
        statusEl.innerHTML = '<span class="status-badge ' + cls + '">' + label + '</span>';

        // Status verifikasi
        var verifEl = document.getElementById('detailVerifikasiHariIni');
        var st = (data.status || '').toLowerCase();
        var verifCls = st === 'approved' ? 'on-time' : st === 'rejected' ? 'late' : 'pending';
        verifEl.innerHTML = '<span class="status-badge ' + verifCls + '">' + capitalize(st) + '</span>';

        // Foto
        var fotoEl = document.getElementById('detailFotoHariIni');
        fotoEl.innerHTML = data.foto_url
            ? '<img src="' + data.foto_url + '" alt="Foto Presensi" class="foto-image" onerror="this.style.display=\'none\'">'
            : '<span class="text-muted">Tidak ada foto</span>';

        // Koordinat
        var lat = NaN, lng = NaN;
        if (data.lokasi) {
            var parts = data.lokasi.trim().split(',');
            if (parts.length === 2) {
                lat = parseFloat(parts[0].trim());
                lng = parseFloat(parts[1].trim());
            }
        }

        hariIniCoords = { lat: lat, lng: lng, lokasi: data.lokasi || 'Lokasi tidak tersedia' };
        openModal('modalDetailHariIni');
    }

    // ─── Modal Helpers ────────────────────────────────────────────────────────
    function openModal(id) {
        var modal = document.getElementById(id);
        if (!modal) return;

        modal.style.display    = 'flex';
        document.body.style.overflow = 'hidden';

        if (id === 'modalPresensiPending') renderMap('presensiMap', 'mapLoading', 'mapError', pendingCoords, 'pending');
        if (id === 'modalDetailHariIni')   renderMap('hariIniMap', 'hariIniMapLoading', 'hariIniMapError', hariIniCoords, 'hariIni');
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        if (!modal) return;

        modal.style.display          = 'none';
        document.body.style.overflow = 'auto';

        if (id === 'modalPresensiPending') destroyMap('pending');
        if (id === 'modalDetailHariIni')   destroyMap('hariIni');
    }

    // ─── Map ──────────────────────────────────────────────────────────────────
    function renderMap(mapElId, loadingId, errorId, coords, type) {
        var mapLoading = document.getElementById(loadingId);
        var mapError   = document.getElementById(errorId);
        var mapEl      = document.getElementById(mapElId);

        mapLoading.style.display = 'flex';
        mapError.style.display   = 'none';

        destroyMap(type);

        if (!coords || isNaN(coords.lat) || isNaN(coords.lng)) {
            mapLoading.style.display = 'none';
            mapError.style.display   = 'flex';
            return;
        }

        var lat    = coords.lat;
        var lng    = coords.lng;
        var lokasi = coords.lokasi;

        setTimeout(function () {
            if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
                mapLoading.style.display = 'none';
                mapError.style.display   = 'flex';
                return;
            }

            try {
                var map = L.map(mapElId, { zoomControl: true }).setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap', maxZoom: 19
                }).addTo(map);

                var marker = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup('<b>Lokasi Presensi</b><br>' + lokasi)
                    .openPopup();

                map.invalidateSize();
                mapLoading.style.display = 'none';

                if (type === 'pending') { presensiMap = map; currentMarker = marker; }
                else { hariIniMap = map; hariIniMarker = marker; }
            } catch (err) {
                console.error('Map error:', err);
                mapLoading.style.display = 'none';
                mapError.style.display   = 'flex';
            }
        }, 200);
    }

    function destroyMap(type) {
        if (type === 'pending' && presensiMap) {
            presensiMap.remove();
            presensiMap = null;
            currentMarker = null;
        }
        if (type === 'hariIni' && hariIniMap) {
            hariIniMap.remove();
            hariIniMap = null;
            hariIniMarker = null;
        }
    }

    // ─── Utilities ────────────────────────────────────────────────────────────
    function capitalize(str) {
        if (!str) return '-';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function setFormAction(formId, url) {
        var form = document.getElementById(formId);
        if (form && url) form.action = url;
    }

    // ===== TABLE SORT + PAGINATION =====
    var tableInstances = {};

    function initTable(tbodyId) {
        var tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        var perPage = parseInt(tbody.getAttribute('data-paginate')) || 10;
        var rows = Array.from(tbody.querySelectorAll('tr'));
        if (rows.length === 0 || (rows.length === 1 && rows[0].querySelector('.empty-state'))) return;

        var table = tbody.closest('table');
        var container = table.parentElement;
        var currentPage = 1;

        var paginationDiv = document.createElement('div');
        paginationDiv.className = 'table-pagination';
        container.appendChild(paginationDiv);

        var instance = { rows: rows, currentPage: 1 };
        tableInstances[tbodyId] = instance;

        function render() {
            var totalRows = instance.rows.length;
            var totalPages = Math.ceil(totalRows / perPage);
            if (instance.currentPage > totalPages) instance.currentPage = 1;

            instance.rows.forEach(function(row, i) {
                var start = (instance.currentPage - 1) * perPage;
                var end = start + perPage;
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });

            // Update nomor urut
            instance.rows.forEach(function(row, i) {
                var noCell = row.querySelector('td:first-child');
                if (noCell) noCell.textContent = i + 1;
            });

            if (totalRows <= perPage) {
                paginationDiv.style.display = 'none';
                return;
            }
            paginationDiv.style.display = '';

            var start = (instance.currentPage - 1) * perPage + 1;
            var end = Math.min(instance.currentPage * perPage, totalRows);

            var html = '<span class="pagination-info">' + start + '-' + end + ' dari ' + totalRows + '</span>';
            html += '<div class="pagination-buttons">';
            html += '<button data-page="prev" ' + (instance.currentPage === 1 ? 'disabled' : '') + '><i class="fas fa-chevron-left"></i></button>';

            var sp = Math.max(1, instance.currentPage - 2);
            var ep = Math.min(totalPages, sp + 4);
            if (ep - sp < 4) sp = Math.max(1, ep - 4);

            for (var p = sp; p <= ep; p++) {
                html += '<button data-page="' + p + '" class="' + (p === instance.currentPage ? 'active' : '') + '">' + p + '</button>';
            }

            html += '<button data-page="next" ' + (instance.currentPage === totalPages ? 'disabled' : '') + '><i class="fas fa-chevron-right"></i></button>';
            html += '</div>';
            paginationDiv.innerHTML = html;
        }

        paginationDiv.addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (!btn || btn.disabled) return;
            var page = btn.getAttribute('data-page');
            if (page === 'prev') instance.currentPage--;
            else if (page === 'next') instance.currentPage++;
            else instance.currentPage = parseInt(page);
            render();
        });

        // Sorting
        var ths = table.querySelectorAll('th[data-sort]');
        ths.forEach(function(th) {
            th.addEventListener('click', function() {
                var colIndex = Array.from(th.parentElement.children).indexOf(th);
                var sortType = th.getAttribute('data-sort');
                var dir = th.classList.contains('sort-asc') ? 'desc' : 'asc';

                // Reset all headers in this table
                ths.forEach(function(h) {
                    h.classList.remove('sort-asc', 'sort-desc');
                    var icon = h.querySelector('.sort-icon');
                    if (icon) icon.className = 'fas fa-sort sort-icon';
                });

                th.classList.add('sort-' + dir);
                var icon = th.querySelector('.sort-icon');
                if (icon) icon.className = 'fas fa-sort-' + (dir === 'asc' ? 'up' : 'down') + ' sort-icon';

                var monthMap = {Jan:1,Feb:2,Mar:3,Apr:4,Mei:5,Jun:6,Jul:7,Agu:8,Sep:9,Okt:10,Nov:11,Des:12};

                instance.rows.sort(function(a, b) {
                    var aCell = a.children[colIndex];
                    var bCell = b.children[colIndex];
                    if (!aCell || !bCell) return 0;

                    var aVal = aCell.textContent.trim();
                    var bVal = bCell.textContent.trim();

                    if (sortType === 'date') {
                        var aParts = aVal.split(' ');
                        var bParts = bVal.split(' ');
                        var aDate = new Date(parseInt(aParts[2]), (monthMap[aParts[1]] || 1) - 1, parseInt(aParts[0]));
                        var bDate = new Date(parseInt(bParts[2]), (monthMap[bParts[1]] || 1) - 1, parseInt(bParts[0]));
                        return dir === 'asc' ? aDate - bDate : bDate - aDate;
                    }

                    return dir === 'asc'
                        ? aVal.localeCompare(bVal, 'id')
                        : bVal.localeCompare(aVal, 'id');
                });

                instance.rows.forEach(function(row) { tbody.appendChild(row); });
                render();
            });
        });

        render();
    }

    document.addEventListener('DOMContentLoaded', function() {
        initTable('presensiPendingTable');
        initTable('pengajuanPendingTable');
        initTable('presensiHariIniTable');
        initTable('lemburHariIniTable');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('attendanceChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Hadir',
                        data: @json($chartHadir),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Terlambat',
                        data: @json($chartTelat),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Lembur',
                        data: @json($chartLembur),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, boxHeight: 10, borderRadius: 5, useBorderRadius: true, padding: 20, font: { size: 12, weight: '500' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30,41,59,0.9)',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: true,
                        boxWidth: 8, boxHeight: 8, boxPadding: 4
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 }, color: '#94a3b8' },
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        border: { display: false }
                    },
                    x: {
                        ticks: { font: { size: 10 }, color: '#94a3b8' },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    });
</script>

@endsection