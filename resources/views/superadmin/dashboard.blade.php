@extends('layouts.admin')

@section('title', 'Dashboard Superadmin')

@section('content')
<style>
    :root, [data-theme="light"] {
        --primary: #3b82f6; --primary-light: #60a5fa; --primary-dark: #2563eb;
        --success: #10b981; --danger: #ef4444; --warning: #f59e0b;
        --light: #f8fafc; --dark: #1e293b;
        --white: #ffffff; --gray-100: #f1f5f9; --gray-200: #e2e8f0;
        --gray-400: #94a3b8; --gray-500: #64748b; --gray-600: #475569;
    }
    [data-theme="dark"] {
        --primary: #60a5fa; --primary-light: #93c5fd; --primary-dark: #3b82f6;
        --success: #34d399; --danger: #f87171; --warning: #fbbf24;
        --light: #0f172a; --dark: #e2e8f0;
        --white: #141b2d; --gray-100: #1e293b; --gray-200: rgba(255,255,255,0.06);
        --gray-400: #4b5c73; --gray-500: #8b9ab5; --gray-600: #94a3b8;
    }
    .sa-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:24px; }
    @media(max-width:768px) { .sa-stats { grid-template-columns:1fr; } }
    .sa-stat {
        background:var(--white); border:1px solid var(--gray-200); border-radius:14px;
        padding:20px; display:flex; align-items:center; gap:16px;
        transition:all 0.2s; text-decoration:none;
    }
    .sa-stat:hover { box-shadow:0 4px 16px rgba(0,0,0,0.08); transform:translateY(-1px); }
    .sa-stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
    .sa-stat-value { font-size:28px; font-weight:800; color:var(--dark); line-height:1; }
    .sa-stat-label { font-size:12px; color:var(--gray-500); font-weight:500; margin-top:2px; }
    .sa-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
    @media(max-width:900px) { .sa-grid { grid-template-columns:1fr; } }
    .sa-card { background:var(--white); border:1px solid var(--gray-200); border-radius:14px; overflow:hidden; }
    .sa-card-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--gray-200); }
    .sa-card-title { font-size:15px; font-weight:700; color:var(--dark); display:flex; align-items:center; gap:10px; }
    .sa-card-badge { font-size:11px; font-weight:600; padding:4px 12px; border-radius:8px; }
    .sa-table { width:100%; border-collapse:collapse; }
    .sa-table thead { background:var(--gray-100); }
    .sa-table th { padding:10px 16px; font-size:10px; font-weight:600; color:var(--gray-500); text-transform:uppercase; letter-spacing:0.5px; text-align:left; }
    .sa-table td { padding:12px 16px; font-size:13px; color:var(--dark); border-bottom:1px solid var(--gray-200); }
    .sa-table tbody tr:hover { background:var(--gray-100); }
    .sa-table .user-cell { display:flex; align-items:center; gap:10px; }
    .sa-avatar { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:12px; flex-shrink:0; }
    .sa-badge { font-size:10px; font-weight:600; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px; }
    .sa-empty { padding:40px 20px; text-align:center; color:var(--gray-400); }
    .sa-empty i { font-size:32px; opacity:0.3; display:block; margin-bottom:8px; }
    .sa-actions { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
    @media(max-width:768px) { .sa-actions { grid-template-columns:1fr; } }
    .sa-action { border-radius:14px; padding:20px; color:#fff; display:flex; flex-direction:column; gap:12px; text-decoration:none; transition:all 0.2s; }
    .sa-action:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.15); }
    .sa-action-title { font-size:15px; font-weight:700; }
    .sa-action-desc { font-size:12px; opacity:0.8; }
    .sa-action-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:rgba(255,255,255,0.2); border:none; border-radius:8px; color:#fff; font-size:12px; font-weight:600; cursor:pointer; align-self:flex-start; transition:background 0.15s; }
    .sa-action-btn:hover { background:rgba(255,255,255,0.3); }
</style>

<div class="admin-dashboard text-sm">
    <div class="dashboard-header" style="margin-bottom:20px;">
        <h1 style="font-size:22px; font-weight:800; color:var(--dark); margin:0;">Dashboard Superadmin</h1>
        <p style="font-size:13px; color:var(--gray-500); margin-top:4px;">Ringkasan sistem dan manajemen administrator</p>
    </div>

    {{-- Stat Cards --}}
    <div class="sa-stats">
        <div class="sa-stat">
            <div class="sa-stat-icon" style="background:rgba(59,130,246,0.1); color:#3b82f6;"><i class="fas fa-user-shield"></i></div>
            <div>
                <div class="sa-stat-value">{{ $totalAdmin ?? 0 }}</div>
                <div class="sa-stat-label">Total Admin</div>
            </div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;"><i class="fas fa-users"></i></div>
            <div>
                <div class="sa-stat-value">{{ $totalPegawai ?? 0 }}</div>
                <div class="sa-stat-label">Total Pegawai</div>
            </div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i class="fas fa-paper-plane"></i></div>
            <div>
                <div class="sa-stat-value">{{ $pengajuanPending ?? 0 }}</div>
                <div class="sa-stat-label">Pengajuan Pending</div>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="sa-grid">
        {{-- Daftar Admin --}}
        <div class="sa-card">
            <div class="sa-card-header">
                <div class="sa-card-title"><i class="fas fa-user-shield" style="color:#3b82f6;"></i> Daftar Admin</div>
                <span class="sa-card-badge" style="background:rgba(59,130,246,0.1); color:#3b82f6;">{{ count($admins ?? []) }} admin</span>
            </div>
            <table class="sa-table">
                <thead><tr><th>No</th><th>Nama</th><th>Email</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($admins ?? [] as $i => $admin)
                    <tr>
                        <td style="width:40px; color:var(--gray-400);">{{ $i + 1 }}</td>
                        <td>
                            <div class="sa-table .user-cell" style="display:flex; align-items:center; gap:10px;">
                                <div class="sa-avatar" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                                <span style="font-weight:600;">{{ $admin->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--gray-500);">{{ $admin->email }}</td>
                        <td><span class="sa-badge" style="background:rgba(16,185,129,0.1); color:#10b981;"><i class="fas fa-circle" style="font-size:5px;"></i> Aktif</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4"><div class="sa-empty"><i class="fas fa-user-shield"></i><p>Tidak ada admin</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pengajuan Pending --}}
        <div class="sa-card">
            <div class="sa-card-header">
                <div class="sa-card-title"><i class="fas fa-paper-plane" style="color:#f59e0b;"></i> Pengajuan Pending</div>
                <span class="sa-card-badge" style="background:rgba(245,158,11,0.1); color:#d97706;">{{ count($pengajuanList ?? []) }} menunggu</span>
            </div>
            <table class="sa-table">
                <thead><tr><th>No</th><th>Pegawai</th><th>Tanggal</th><th>Jenis</th></tr></thead>
                <tbody>
                    @forelse($pengajuanList ?? [] as $i => $peng)
                    <tr>
                        <td style="width:40px; color:var(--gray-400);">{{ $i + 1 }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div class="sa-avatar" style="background:linear-gradient(135deg,#f59e0b,#ef4444);">{{ strtoupper(substr($peng->user->name ?? 'U', 0, 1)) }}</div>
                                <span style="font-weight:600;">{{ $peng->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td style="color:var(--gray-500);">{{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}</td>
                        <td>
                            @php $jc = $peng->jenis === 'masuk' ? ['rgba(59,130,246,0.1)','#3b82f6'] : ['rgba(16,185,129,0.1)','#10b981']; @endphp
                            <span class="sa-badge" style="background:{{ $jc[0] }}; color:{{ $jc[1] }};">{{ ucfirst($peng->jenis ?? '-') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4"><div class="sa-empty"><i class="fas fa-inbox"></i><p>Semua pengajuan telah diproses</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="sa-actions">
        <a href="{{ route('superadmin.manajemenadmin.index') }}" class="sa-action" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <div class="sa-action-title">Kelola Admin</div>
                    <div class="sa-action-desc">Tambah atau edit administrator</div>
                </div>
                <i class="fas fa-user-cog" style="font-size:24px; opacity:0.6;"></i>
            </div>
            <span class="sa-action-btn"><i class="fas fa-arrow-right"></i> Kelola</span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="sa-action" style="background:linear-gradient(135deg,#10b981,#059669);">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <div class="sa-action-title">Dashboard Admin</div>
                    <div class="sa-action-desc">Lihat dashboard admin lengkap</div>
                </div>
                <i class="fas fa-chart-line" style="font-size:24px; opacity:0.6;"></i>
            </div>
            <span class="sa-action-btn"><i class="fas fa-arrow-right"></i> Buka</span>
        </a>
        <a href="{{ route('admin.pengaturan.index') }}" class="sa-action" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <div class="sa-action-title">Pengaturan</div>
                    <div class="sa-action-desc">Konfigurasi sistem presensi</div>
                </div>
                <i class="fas fa-gear" style="font-size:24px; opacity:0.6;"></i>
            </div>
            <span class="sa-action-btn"><i class="fas fa-arrow-right"></i> Buka</span>
        </a>
    </div>
</div>
@endsection
