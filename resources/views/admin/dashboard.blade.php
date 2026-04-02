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

    .dashboard-header { margin-bottom: 30px; }

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
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 4px solid var(--primary);
    }

    .stat-content { flex: 1; }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 5px 0;
        line-height: 1;
    }

    .stat-label { color: var(--gray-500); font-size: 14px; margin: 0; }

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

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 1024px) {
        .content-grid { grid-template-columns: 1fr; }
    }

    .content-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid var(--gray-200);
    }

    .card-title { font-size: 16px; font-weight: 600; color: var(--dark); margin: 0; }

    .card-badge {
        padding: 4px 10px;
        background: var(--gray-300);
        color: var(--gray-700);
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .card-content { padding: 0; }
    .table-container { overflow-x: auto; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead { background: var(--gray-100); }

    .data-table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-600);
        font-size: 12px;
        text-transform: uppercase;
        border-bottom: 1px solid var(--gray-200);
    }

    .data-table th.text-center { text-align: center; }

    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .data-table td.text-center { text-align: center; }
    .data-table tbody tr:hover { background: var(--gray-100); }
    .user-name { font-size: 12px; font-weight: 500; color: var(--dark); }

    .badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .jenis-badge {
        background: rgba(59,130,246,0.1);
        color: var(--primary);
        border: 1px solid rgba(59,130,246,0.2);
    }

    .status-badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; }
    .status-badge.on-time { background: rgba(16,185,129,0.1); color: var(--success); }
    .status-badge.late    { background: rgba(239,68,68,0.1);  color: var(--danger); }
    .status-badge.neutral { background: rgba(100,116,139,0.1); color: var(--gray-500); }
    .status-badge.pending { background: rgba(245,158,11,0.1); color: var(--warning); }

    .date-cell { font-size: 13px; color: var(--gray-600); }
    .time-cell { font-family: 'Courier New', monospace; font-size: 13px; color: var(--dark); }

    .action-buttons { display: flex; gap: 5px; justify-content: center; }
    .inline-form { display: inline; }

    .btn-success, .btn-danger {
        width: 30px; height: 30px;
        border: none; border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s ease; font-size: 12px;
    }

    .btn-success { background: rgba(16,185,129,0.1); color: var(--success); }
    .btn-success:hover { background: var(--success); color: var(--white); }
    .btn-danger  { background: rgba(239,68,68,0.1);  color: var(--danger); }
    .btn-danger:hover  { background: var(--danger);  color: var(--white); }

    .empty-state { text-align: center; padding: 30px 20px; }
    .empty-content { display: flex; flex-direction: column; align-items: center; gap: 10px; color: var(--gray-400); }
    .empty-content i { font-size: 32px; }
    .empty-content p { margin: 0; font-size: 13px; }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed; top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center; justify-content: center;
    }

    .modal-container {
        background: var(--white);
        border-radius: 12px;
        width: 90%; max-width: 500px; max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 10000; position: relative;
    }

    .modal-large { max-width: 700px; }

    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 20px; border-bottom: 1px solid var(--gray-200);
    }

    .modal-title { font-size: 18px; font-weight: 600; color: var(--dark); margin: 0; }

    .modal-close {
        background: none; border: none; font-size: 18px;
        color: var(--gray-500); cursor: pointer; padding: 5px;
        border-radius: 4px; transition: all 0.2s ease;
    }

    .modal-close:hover { background: var(--gray-100); color: var(--danger); }
    .modal-content { padding: 20px; }

    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
    .detail-item { display: flex; flex-direction: column; gap: 5px; }
    .detail-item.full-width { grid-column: 1 / -1; }

    .detail-item label {
        font-size: 12px; font-weight: 600;
        color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px;
    }

    .detail-item span { font-size: 14px; color: var(--dark); word-break: break-word; }

    .modal-actions {
        display: flex; gap: 10px; justify-content: flex-end;
        flex-wrap: wrap; border-top: 1px solid var(--gray-200); padding-top: 20px;
    }

    .btn-secondary {
        padding: 8px 16px; background: var(--gray-300); color: var(--gray-700);
        border: none; border-radius: 6px; cursor: pointer;
        font-size: 12px; font-weight: 500; transition: all 0.2s ease;
    }

    .btn-secondary:hover { background: var(--gray-400); }
    .text-muted { color: var(--gray-400); font-style: italic; }

    .clickable-row { cursor: pointer; transition: background-color 0.2s ease; }
    .clickable-row:hover { background: var(--gray-50) !important; }

    .bukti-image, .foto-image {
        max-width: 100%; max-height: 200px;
        border-radius: 6px; border: 1px solid var(--gray-200);
    }

    /* Map */
    .map-container {
        position: relative; height: 300px;
        border-radius: 8px; overflow: hidden;
        border: 1px solid var(--gray-200); background: var(--gray-100);
    }

    .map { width: 100%; height: 100%; }

    .map-loading, .map-error {
        position: absolute; top: 0; left: 0;
        width: 100%; height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 10px; z-index: 1000;
    }

    .map-loading { background: rgba(255,255,255,0.9); color: var(--gray-500); }
    .map-error   { background: var(--gray-100); color: var(--gray-500); }

    @media (max-width: 768px) {
        .admin-dashboard { padding: 15px; }
        .stats-grid { grid-template-columns: 1fr; gap: 15px; }
        .content-grid { gap: 15px; }
        .data-table th, .data-table td { padding: 10px 12px; font-size: 12px; }
        .stat-card { padding: 15px; }
        .stat-value { font-size: 24px; }
        .stat-icon { width: 40px; height: 40px; font-size: 16px; }
        .modal-container { width: 95%; margin: 20px; }
        .modal-large { max-width: 95%; }
        .detail-grid { grid-template-columns: 1fr; gap: 12px; }
        .modal-actions { flex-direction: column; }
        .modal-actions .inline-form { width: 100%; }
        .modal-actions button { width: 100%; }
        .map-container { height: 250px; }
    }
</style>

<div class="admin-dashboard text-sm">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard Admin</h1>
        <p class="dashboard-subtitle">Ringkasan aktivitas dan statistik sistem</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahHadir ?? 0 }}</h3>
                <p class="stat-label">Hadir Hari Ini</p>
            </div>
            <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPegawai ?? 0 }}</h3>
                <p class="stat-label">Total Pegawai</p>
            </div>
            <div class="stat-icon"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPengajuan ?? 0 }}</h3>
                <p class="stat-label">Pengajuan Pending</p>
            </div>
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
        </div>
    </div>

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
                                <th>Pegawai</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="presensiPendingTable">
                            @forelse($presensiPending ?? [] as $index => $p)
                            <tr class="clickable-row"
                                data-user-name="{{ $p->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $p->jenis ?? '' }}"
                                data-jam="{{ $p->jam ?? '-' }}"
                                data-lokasi="{{ $p->lokasi ?? '' }}"
                                data-foto-url="{{ $p->foto ? asset('storage/' . $p->foto) : '' }}"
                                data-approve-url="{{ route('admin.presensi.approve', $p->id) }}"
                                data-reject-url="{{ route('admin.presensi.reject', $p->id) }}">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}
                                </td>
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
                                <th>Pegawai</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengajuanPendingTable">
                            @forelse($pengajuanPending ?? [] as $index => $peng)
                            <tr class="clickable-row"
                                data-user-name="{{ $peng->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $peng->jenis ?? '' }}"
                                data-alasan="{{ $peng->alasan ?? 'Tidak ada alasan' }}"
                                data-bukti-url="{{ $peng->bukti ? asset('storage/' . $peng->bukti) : '' }}"
                                data-approve-url="{{ route('admin.pengajuan.approve', $peng->id) }}"
                                data-reject-url="{{ route('admin.pengajuan.reject', $peng->id) }}">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $peng->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}
                                </td>
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
                                <th>Nama Pegawai</th>
                                <th>Jenis</th>
                                <th>Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presensiHariIni ?? [] as $index => $p)
                            <tr>
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

    </div>
</div>

{{-- Modal Detail Presensi Pending --}}
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
                    <label>Nama Pegawai</label>
                    <span id="detailPegawaiPresensi">-</span>
                </div>
                <div class="detail-item">
                    <label>Tanggal</label>
                    <span id="detailTanggalPresensi">-</span>
                </div>
                <div class="detail-item">
                    <label>Jenis Presensi</label>
                    <span id="detailJenisPresensi">-</span>
                </div>
                <div class="detail-item">
                    <label>Jam</label>
                    <span id="detailJamPresensi">-</span>
                </div>
                <div class="detail-item full-width">
                    <label>Lokasi</label>
                    <span id="detailLokasiPresensi">-</span>
                </div>
                <div class="detail-item full-width">
                    <label>Peta Lokasi</label>
                    <div id="mapContainer" class="map-container">
                        <div id="presensiMap" class="map"></div>
                        <div id="mapLoading" class="map-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Memuat peta...</span>
                        </div>
                        <div id="mapError" class="map-error" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Koordinat tidak tersedia</span>
                        </div>
                    </div>
                </div>
                <div class="detail-item full-width">
                    <label>Foto</label>
                    <div id="detailFotoPresensi">
                        <span class="text-muted">Tidak ada foto</span>
                    </div>
                </div>
                <div class="detail-item full-width">
                    <label>Status</label>
                    <span class="status-badge pending">Pending</span>
                </div>
            </div>
            <div class="modal-actions">
                @if(Auth::user()->can_approve_pengajuan)
                <form id="formApprovePresensi" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn-success">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </form>
                <form id="formRejectPresensi" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-times"></i> Tolak
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

{{-- Modal Detail Pengajuan Pending --}}
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
                    <label>Nama Pegawai</label>
                    <span id="detailPegawaiPengajuan">-</span>
                </div>
                <div class="detail-item">
                    <label>Tanggal</label>
                    <span id="detailTanggalPengajuan">-</span>
                </div>
                <div class="detail-item full-width">
                    <label>Jenis Pengajuan</label>
                    <span id="detailJenisPengajuan">-</span>
                </div>
                <div class="detail-item full-width">
                    <label>Alasan</label>
                    <span id="detailAlasanPengajuan">-</span>
                </div>
                <div class="detail-item full-width">
                    <label>Bukti</label>
                    <div id="detailBuktiPengajuan">
                        <span class="text-muted">Tidak ada bukti</span>
                    </div>
                </div>
                <div class="detail-item full-width">
                    <label>Status</label>
                    <span class="status-badge pending">Pending</span>
                </div>
            </div>
            <div class="modal-actions">
                @if(Auth::user()->can_approve_pengajuan)
                <form id="formApprovePengajuan" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn-success">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </form>
                <form id="formRejectPengajuan" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-times"></i> Tolak
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

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
// ============================================================
// GLOBAL STATE
// ============================================================
var presensiMap   = null;
var currentMarker = null;

// ============================================================
// DOM READY
// ============================================================
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('#presensiPendingTable tbody tr.clickable-row').forEach(function (row) {
        row.addEventListener('click', handlePresensiClick);
    });

    document.querySelectorAll('#pengajuanPendingTable tbody tr.clickable-row').forEach(function (row) {
        row.addEventListener('click', handlePengajuanClick);
    });

    // Klik backdrop tutup modal
    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // ESC tutup modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay').forEach(function (m) {
                closeModal(m.id);
            });
        }
    });
});

// ============================================================
// CLICK HANDLERS
// ============================================================
function handlePresensiClick(e) {
    if (e.target.closest('.action-buttons')) return;

    openPresensiModal({
        user_name:   this.getAttribute('data-user-name'),
        tanggal:     this.getAttribute('data-tanggal'),
        jenis:       this.getAttribute('data-jenis'),
        jam:         this.getAttribute('data-jam'),
        lokasi:      this.getAttribute('data-lokasi'),      // "lat,lng" — satu-satunya sumber koordinat
        foto_url:    this.getAttribute('data-foto-url'),
        approve_url: this.getAttribute('data-approve-url'),
        reject_url:  this.getAttribute('data-reject-url'),
    });
}

function handlePengajuanClick(e) {
    if (e.target.closest('.action-buttons')) return;

    openPengajuanModal({
        user_name:   this.getAttribute('data-user-name'),
        tanggal:     this.getAttribute('data-tanggal'),
        jenis:       this.getAttribute('data-jenis'),
        alasan:      this.getAttribute('data-alasan'),
        bukti_url:   this.getAttribute('data-bukti-url'),
        approve_url: this.getAttribute('data-approve-url'),
        reject_url:  this.getAttribute('data-reject-url'),
    });
}

// ============================================================
// OPEN MODALS
// ============================================================
function openPresensiModal(data) {
    document.getElementById('detailPegawaiPresensi').textContent = data.user_name || 'N/A';
    document.getElementById('detailTanggalPresensi').textContent = data.tanggal   || '-';
    document.getElementById('detailJenisPresensi').textContent   = data.jenis
        ? data.jenis.charAt(0).toUpperCase() + data.jenis.slice(1) : '-';
    document.getElementById('detailJamPresensi').textContent     = data.jam       || '-';
    document.getElementById('detailLokasiPresensi').textContent  = data.lokasi    || 'Tidak ada lokasi';

    // Foto
    var fotoEl = document.getElementById('detailFotoPresensi');
    fotoEl.innerHTML = data.foto_url
        ? '<a href="' + data.foto_url + '" target="_blank">' +
          '<img src="' + data.foto_url + '" alt="Foto Presensi" class="foto-image" onerror="this.style.display=\'none\'">' +
          '</a>'
        : '<span class="text-muted">Tidak ada foto</span>';

    // Form actions
    var fApprove = document.getElementById('formApprovePresensi');
    var fReject  = document.getElementById('formRejectPresensi');
    if (fApprove) fApprove.action = data.approve_url;
    if (fReject)  fReject.action  = data.reject_url;

    // Buka modal dulu agar container sudah visible, baru init map
    openModal('modalPresensiPending');
    initializeMap(data.lokasi);
}

function openPengajuanModal(data) {
    document.getElementById('detailPegawaiPengajuan').textContent = data.user_name || 'N/A';
    document.getElementById('detailTanggalPengajuan').textContent = data.tanggal   || '-';
    document.getElementById('detailJenisPengajuan').textContent   = data.jenis
        ? data.jenis.charAt(0).toUpperCase() + data.jenis.slice(1) : '-';
    document.getElementById('detailAlasanPengajuan').textContent  = data.alasan   || 'Tidak ada alasan';

    // Bukti
    var buktiEl = document.getElementById('detailBuktiPengajuan');
    buktiEl.innerHTML = data.bukti_url
        ? '<a href="' + data.bukti_url + '" target="_blank">' +
          '<img src="' + data.bukti_url + '" alt="Bukti" class="bukti-image" onerror="this.style.display=\'none\'">' +
          '</a>'
        : '<span class="text-muted">Tidak ada bukti</span>';

    // Form actions
    var fApprove = document.getElementById('formApprovePengajuan');
    var fReject  = document.getElementById('formRejectPengajuan');
    if (fApprove) fApprove.action = data.approve_url;
    if (fReject)  fReject.action  = data.reject_url;

    openModal('modalPengajuanPending');
}

// ============================================================
// MAP — FIX UTAMA
// ============================================================

/**
 * Destroy instance Leaflet lama + rebuild elemen #presensiMap
 * agar tidak throw "Map container is already initialized"
 */
function cleanupMap() {
    if (presensiMap) {
        presensiMap.remove();
        presensiMap = null;
    }
    currentMarker = null;

    var wrapper = document.getElementById('mapContainer');
    var oldMap  = document.getElementById('presensiMap');
    if (oldMap) oldMap.remove();

    var newDiv     = document.createElement('div');
    newDiv.id        = 'presensiMap';
    newDiv.className = 'map';
    if (wrapper) wrapper.insertBefore(newDiv, wrapper.firstChild);
}

/**
 * initializeMap — menerima 1 parameter: string lokasi "lat,lng"
 *
 * PENYEBAB BUG:
 *   Kolom `presensis`.`lokasi` di DB menyimpan koordinat sebagai
 *   satu string, contoh: "2.1477755,117.4334728"
 *   Tidak ada kolom latitude / longitude terpisah.
 *   Kode lama meneruskan data-latitude="" (kosong) → parseFloat("") = NaN
 *   → map tidak pernah bisa diinisialisasi.
 *
 * FIX:
 *   Parse koordinat langsung dari string lokasi dengan split(',').
 */
function initializeMap(lokasi) {
    cleanupMap();

    var mapLoading = document.getElementById('mapLoading');
    var mapError   = document.getElementById('mapError');
    mapLoading.style.display = 'flex';
    mapError.style.display   = 'none';

    var lat = NaN, lng = NaN;

    if (lokasi && lokasi.trim() !== '' && lokasi !== 'Tidak ada lokasi') {
        var parts = lokasi.split(',');
        if (parts.length === 2) {
            lat = parseFloat(parts[0].trim());
            lng = parseFloat(parts[1].trim());
        }
    }

    if (isNaN(lat) || isNaN(lng)) {
        mapLoading.style.display = 'none';
        mapError.style.display   = 'flex';
        return;
    }

    // Delay 200ms: tunggu modal & container selesai render
    setTimeout(function () {
        try {
            presensiMap = L.map('presensiMap').setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(presensiMap);

            currentMarker = L.marker([lat, lng])
                .addTo(presensiMap)
                .bindPopup('<b>Lokasi Presensi</b><br>' + (lokasi || '-'))
                .openPopup();

            mapLoading.style.display = 'none';

            // Paksa Leaflet recalculate ukuran container
            presensiMap.invalidateSize();

        } catch (err) {
            console.error('Map error:', err);
            mapLoading.style.display = 'none';
            mapError.style.display   = 'flex';
        }
    }, 200);
}

// ============================================================
// MODAL HELPERS
// ============================================================
function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        if (modalId === 'modalPresensiPending') cleanupMap();
    }
}
</script>

@endsection