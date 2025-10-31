@extends('layouts.admin')

@section('title', 'Dashboard ')

@section('content')
<div class="admin-dashboard text-sm">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">Dashboard Admin</h1>
            <p class="dashboard-subtitle">Ringkasan aktivitas dan statistik sistem</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahHadir ?? 0 }}</h3>
                <p class="stat-label">Hadir Hari Ini</p>
            </div>
            <div class="stat-icon">
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

        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPengajuan ?? 0 }}</h3>
                <p class="stat-label">Pengajuan Pending</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>

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
                                <th>Pegawai</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="presensiPendingTable">
                            @forelse($presensiPending ?? [] as $index => $p)
                            <tr class="clickable-row" 
                                data-presensi-id="{{ $p->id }}"
                                data-user-name="{{ $p->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $p->jenis ?? '' }}"
                                data-jam="{{ $p->jam ?? '-' }}"
                                data-lokasi="{{ $p->lokasi ?? 'Tidak ada lokasi' }}"
                                data-approve-url="{{ route('admin.presensi.approve', $p->id) }}"
                                data-reject-url="{{ route('admin.presensi.reject', $p->id) }}">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge jenis-badge {{ $p->jenis ?? '' }}">
                                        {{ ucfirst($p->jenis ?? '') }}
                                    </span>
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
                                data-pengajuan-id="{{ $peng->id }}"
                                data-user-name="{{ $peng->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $peng->jenis ?? '' }}"
                                data-alasan="{{ $peng->alasan ?? 'Tidak ada alasan' }}"
                                data-bukti="{{ $peng->bukti ?? '' }}"
                                data-bukti-url="{{ $peng->bukti ? asset('storage/' . $peng->bukti) : '' }}"
                                data-approve-url="{{ route('admin.pengajuan.approve', $peng->id) }}"
                                data-reject-url="{{ route('admin.pengajuan.reject', $peng->id) }}">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $peng->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge jenis-badge {{ $peng->jenis ?? '' }}">
                                        {{ ucfirst($peng->jenis ?? '') }}
                                    </span>
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
                                    <span class="badge jenis-badge {{ $p->jenis ?? '' }}">
                                        {{ ucfirst($p->jenis ?? '') }}
                                    </span>
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
    <div class="modal-container">
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
                <div class="detail-item">
                    <label>Lokasi</label>
                    <span id="detailLokasiPresensi">-</span>
                </div>
                <div class="detail-item full-width">
                    <label>Foto</label>
                    <div id="detailFotoPresensi">
                        <span class="text-muted">Tidak ada foto</span>
                    </div>
                </div>
                <div class="detail-item full-width">
                    <label>Status</label>
                    <span id="detailStatusPresensi" class="status-badge pending">Pending</span>
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
                <div class="detail-item">
                    <label>Jenis Pengajuan</label>
                    <span id="detailJenisPengajuan">-</span>
                </div>
                <div class="detail-item full-width">
                    <label>Alasan</label>
                    <span id="detailAlasanPengajuan">-</span>
                </div>
                <div class="detail-item full-width" id="buktiContainer">
                    <label>Bukti</label>
                    <div id="detailBuktiPengajuan">
                        <span class="text-muted">Tidak ada bukti</span>
                    </div>
                </div>
                <div class="detail-item">
                    <label>Status</label>
                    <span id="detailStatusPengajuan" class="status-badge pending">Pending</span>
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

    /* User Name */
    .user-name {
        font-size: 12px;
        font-weight: 500;
        color: var(--dark);
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

    /* Date Cell */
    .date-cell {
        font-size: 13px;
        color: var(--gray-600);
    }

    /* Time Cell */
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
        padding: 20px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-item label {
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-item span {
        font-size: 14px;
        color: var(--dark);
        word-break: break-word;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
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

    /* Row clickable style */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-row:hover {
        background: var(--gray-50) !important;
    }

    /* Bukti image */
    .bukti-image {
        max-width: 100%;
        max-height: 200px;
        border-radius: 6px;
        border: 1px solid var(--gray-200);
    }

    .foto-image {
        max-width: 100%;
        max-height: 200px;
        border-radius: 6px;
        border: 1px solid var(--gray-200);
    }

    .bukti-placeholder {
        padding: 20px;
        text-align: center;
        background: var(--gray-100);
        border-radius: 6px;
        color: var(--gray-500);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .admin-dashboard {
            padding: 15px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .content-grid {
            gap: 15px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 12px;
            font-size: 12px;
        }

        .stat-card {
            padding: 15px;
        }

        .stat-value {
            font-size: 24px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .modal-container {
            width: 95%;
            margin: 20px;
        }

        .detail-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .modal-actions {
            flex-direction: column;
        }

        .modal-actions .inline-form {
            width: 100%;
        }

        .modal-actions button {
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Klik pada baris presensi pending
    document.querySelectorAll('#presensiPendingTable .clickable-row').forEach(row => {
        row.addEventListener('click', function() {
            document.getElementById('detailPegawaiPresensi').textContent = this.dataset.userName;
            document.getElementById('detailTanggalPresensi').textContent = this.dataset.tanggal;
            document.getElementById('detailJenisPresensi').textContent = this.dataset.jenis;
            document.getElementById('detailJamPresensi').textContent = this.dataset.jam;
            document.getElementById('detailLokasiPresensi').textContent = this.dataset.lokasi;

            // Update form action
            document.getElementById('formApprovePresensi').action = this.dataset.approveUrl;
            document.getElementById('formRejectPresensi').action = this.dataset.rejectUrl;

            // Tampilkan modal
            openModal('modalPresensiPending');
        });
    });

    // Klik pada baris pengajuan pending
    document.querySelectorAll('#pengajuanPendingTable .clickable-row').forEach(row => {
        row.addEventListener('click', function() {
            document.getElementById('detailPegawaiPengajuan').textContent = this.dataset.userName;
            document.getElementById('detailTanggalPengajuan').textContent = this.dataset.tanggal;
            document.getElementById('detailJenisPengajuan').textContent = this.dataset.jenis;
            document.getElementById('detailAlasanPengajuan').textContent = this.dataset.alasan;

            const buktiContainer = document.getElementById('detailBuktiPengajuan');
            buktiContainer.innerHTML = this.dataset.buktiUrl
                ? `<a href="${this.dataset.buktiUrl}" target="_blank"><img src="${this.dataset.buktiUrl}" class="bukti-image"></a>`
                : '<span class="text-muted">Tidak ada bukti</span>';

            document.getElementById('formApprovePengajuan').action = this.dataset.approveUrl;
            document.getElementById('formRejectPengajuan').action = this.dataset.rejectUrl;

            openModal('modalPengajuanPending');
        });
    });
});

// Fungsi buka/tutup modal
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'flex';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
}
</script>

<script>
    // Debugging
    console.log('✅ Script modal loaded');

    // Fungsi untuk membuka modal presensi pending
    function openPresensiModal(presensi) {
        console.log('🟡 Opening presensi modal:', presensi);
        
        // Update modal content
        document.getElementById('detailPegawaiPresensi').textContent = presensi.user_name || 'N/A';
        document.getElementById('detailTanggalPresensi').textContent = presensi.tanggal_formatted || '-';
        document.getElementById('detailJenisPresensi').textContent = presensi.jenis ? presensi.jenis.charAt(0).toUpperCase() + presensi.jenis.slice(1) : '-';
        document.getElementById('detailJamPresensi').textContent = presensi.jam || '-';
        document.getElementById('detailLokasiPresensi').textContent = presensi.lokasi || 'Tidak ada lokasi';
        
        // Handle foto
        const fotoContainer = document.getElementById('detailFotoPresensi');
        if (presensi.foto_url) {
            fotoContainer.innerHTML = `<img src="${presensi.foto_url}" alt="Foto Presensi" class="foto-image" onerror="this.style.display='none'">`;
        } else {
            fotoContainer.innerHTML = '<span class="text-muted">Tidak ada foto</span>';
        }
        
        // Set form action URLs
        const approveForm = document.getElementById('formApprovePresensi');
        const rejectForm = document.getElementById('formRejectPresensi');
        
        if (approveForm) approveForm.action = presensi.approve_url;
        if (rejectForm) rejectForm.action = presensi.reject_url;
        
        openModal('modalPresensiPending');
    }

    // Fungsi untuk membuka modal pengajuan pending
    function openPengajuanModal(pengajuan) {
        console.log('🟡 Opening pengajuan modal:', pengajuan);
        
        // Update modal content
        document.getElementById('detailPegawaiPengajuan').textContent = pengajuan.user_name || 'N/A';
        document.getElementById('detailTanggalPengajuan').textContent = pengajuan.tanggal_formatted || '-';
        document.getElementById('detailJenisPengajuan').textContent = pengajuan.jenis ? pengajuan.jenis.charAt(0).toUpperCase() + pengajuan.jenis.slice(1) : '-';
        document.getElementById('detailAlasanPengajuan').textContent = pengajuan.alasan || 'Tidak ada alasan';
        
        // Handle bukti
        const buktiContainer = document.getElementById('detailBuktiPengajuan');
        if (pengajuan.bukti_url) {
            buktiContainer.innerHTML = `<img src="${pengajuan.bukti_url}" alt="Bukti" class="bukti-image" onerror="this.style.display='none'">`;
        } else {
            buktiContainer.innerHTML = '<span class="text-muted">Tidak ada bukti</span>';
        }
        
        // Set form action URLs
        const approveForm = document.getElementById('formApprovePengajuan');
        const rejectForm = document.getElementById('formRejectPengajuan');
        
        if (approveForm) approveForm.action = pengajuan.approve_url;
        if (rejectForm) rejectForm.action = pengajuan.reject_url;
        
        openModal('modalPengajuanPending');
    }

    // Fungsi umum untuk membuka modal
    function openModal(modalId) {
        console.log('🟡 Opening modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } else {
            console.error('❌ Modal not found:', modalId);
        }
    }

    // Fungsi untuk menutup modal
    function closeModal(modalId) {
        console.log('🟡 Closing modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Initialize event listeners
    function initializeEventListeners() {
        console.log('🔄 Initializing event listeners...');
        
        // Handle klik pada baris presensi pending
        const presensiRows = document.querySelectorAll('#presensiPendingTable tbody tr.clickable-row');
        console.log('🔍 Found presensi rows:', presensiRows.length);
        
        presensiRows.forEach((row, index) => {
            row.removeEventListener('click', handlePresensiClick); // Remove existing listeners
            row.addEventListener('click', handlePresensiClick);
        });

        // Handle klik pada baris pengajuan pending
        const pengajuanRows = document.querySelectorAll('#pengajuanPendingTable tbody tr.clickable-row');
        console.log('🔍 Found pengajuan rows:', pengajuanRows.length);
        
        pengajuanRows.forEach((row, index) => {
            row.removeEventListener('click', handlePengajuanClick); // Remove existing listeners
            row.addEventListener('click', handlePengajuanClick);
        });

        console.log('✅ Event listeners initialized');
    }

    // Event handler untuk presensi
    function handlePresensiClick(e) {
        console.log('🟡 Presensi row clicked', this);
        
        // Jangan trigger jika klik pada tombol aksi
        if (e.target.closest('.action-buttons')) {
            console.log('⏹️ Click on action buttons, ignoring');
            return;
        }
        
        const presensiData = {
            user_name: this.getAttribute('data-user-name'),
            tanggal_formatted: this.getAttribute('data-tanggal'),
            jenis: this.getAttribute('data-jenis'),
            jam: this.getAttribute('data-jam'),
            lokasi: this.getAttribute('data-lokasi'),
            foto_url: this.getAttribute('data-foto-url'),
            approve_url: this.getAttribute('data-approve-url'),
            reject_url: this.getAttribute('data-reject-url')
        };
        
        console.log('📦 Presensi data:', presensiData);
        openPresensiModal(presensiData);
    }

    // Event handler untuk pengajuan
    function handlePengajuanClick(e) {
        console.log('🟡 Pengajuan row clicked', this);
        
        // Jangan trigger jika klik pada tombol aksi
        if (e.target.closest('.action-buttons')) {
            console.log('⏹️ Click on action buttons, ignoring');
            return;
        }
        
        const pengajuanData = {
            user_name: this.getAttribute('data-user-name'),
            tanggal_formatted: this.getAttribute('data-tanggal'),
            jenis: this.getAttribute('data-jenis'),
            alasan: this.getAttribute('data-alasan'),
            bukti: this.getAttribute('data-bukti'),
            bukti_url: this.getAttribute('data-bukti-url'),
            approve_url: this.getAttribute('data-approve-url'),
            reject_url: this.getAttribute('data-reject-url')
        };
        
        console.log('📦 Pengajuan data:', pengajuanData);
        openPengajuanModal(pengajuanData);
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ DOM Content Loaded');
        initializeEventListeners();

        // Tutup modal ketika klik di luar konten modal
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });
    });

    // Auto refresh dashboard (optional)
    /*
    const dashboardContainer = document.querySelector('.admin-dashboard');
    function refreshDashboard() {
        fetch('{{ route("admin.dashboard.data") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("✅ Dashboard data updated");
                // Update your dashboard elements here
            }
        })
        .catch(err => console.error('❌ Gagal memperbarui dashboard:', err));
    }
    // setInterval(refreshDashboard, 30000);
    */
</script>

@endsection