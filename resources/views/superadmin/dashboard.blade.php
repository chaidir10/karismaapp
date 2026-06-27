@extends('layouts.superadmin')

@section('title', 'Dashboard Superadmin')

@push('styles')
<style>
    .sa-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px; }
    @media(max-width:768px) { .sa-stats { grid-template-columns:1fr; } }
    .sa-stat {
        background:#fff; border:1px solid #e2e8f0; border-radius:14px;
        padding:20px; display:flex; align-items:center; gap:16px;
        transition:all 0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.04);
    }
    .sa-stat:hover { box-shadow:0 4px 16px rgba(0,0,0,0.08); transform:translateY(-1px); }
    .sa-stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
    .sa-stat-value { font-size:28px; font-weight:800; color:#1e293b; line-height:1; }
    .sa-stat-label { font-size:12px; color:#64748b; font-weight:500; margin-top:2px; }

    .sa-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
    @media(max-width:900px) { .sa-grid { grid-template-columns:1fr; } }
    .sa-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
    .sa-card-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
    .sa-card-title { font-size:15px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:10px; }
    .sa-card-badge { font-size:11px; font-weight:600; padding:4px 12px; border-radius:8px; }

    .sa-table { width:100%; border-collapse:collapse; }
    .sa-table thead { background:#f8fafc; }
    .sa-table th { padding:10px 16px; font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; text-align:left; }
    .sa-table td { padding:12px 16px; font-size:13px; color:#1e293b; border-bottom:1px solid #f1f5f9; }
    .sa-table tbody tr:hover { background:#f8fafc; }
    .sa-avatar { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:12px; flex-shrink:0; }
    .sa-badge { font-size:10px; font-weight:600; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px; }
    .sa-empty { padding:40px 20px; text-align:center; color:#94a3b8; }
    .sa-empty i { font-size:32px; opacity:0.3; display:block; margin-bottom:8px; }

    .sa-actions { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
    @media(max-width:768px) { .sa-actions { grid-template-columns:1fr; } }
    .sa-action { border-radius:14px; padding:20px; color:#fff; display:flex; flex-direction:column; gap:12px; text-decoration:none; transition:all 0.2s; }
    .sa-action:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.15); color:#fff; }
    .sa-action-title { font-size:15px; font-weight:700; }
    .sa-action-desc { font-size:12px; opacity:0.8; }
    .sa-action-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:rgba(255,255,255,0.2); border:none; border-radius:8px; color:#fff; font-size:12px; font-weight:600; cursor:pointer; align-self:flex-start; }
    .sa-action-btn:hover { background:rgba(255,255,255,0.3); }
</style>
@endpush

@section('content')
<div style="max-width:1200px; margin:0 auto;">
    <div style="margin-bottom:24px;">
        <h1 style="font-size:22px; font-weight:800; color:#1e293b; margin:0;">Dashboard Superadmin</h1>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Ringkasan sistem dan manajemen administrator</p>
    </div>

    {{-- Stat Cards --}}
    <div class="sa-stats">
        <div class="sa-stat">
            <div class="sa-stat-icon" style="background:rgba(59,130,246,0.1); color:#3b82f6;"><i class="fas fa-user-shield"></i></div>
            <div><div class="sa-stat-value">{{ $totalAdmin ?? 0 }}</div><div class="sa-stat-label">Total Admin</div></div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;"><i class="fas fa-users"></i></div>
            <div><div class="sa-stat-value">{{ $totalPegawai ?? 0 }}</div><div class="sa-stat-label">Total Pegawai</div></div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i class="fas fa-paper-plane"></i></div>
            <div><div class="sa-stat-value">{{ $pengajuanPending ?? 0 }}</div><div class="sa-stat-label">Pengajuan Pending</div></div>
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
                        <td style="width:40px; color:#94a3b8;">{{ $i + 1 }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                @php
                                    $adminFotoPath = $admin->foto_profil ? 'foto_profil/' . ltrim($admin->foto_profil, '/') : null;
                                    $adminFotoExists = $adminFotoPath ? Storage::disk('public')->exists($adminFotoPath) : false;
                                @endphp
                                @if($adminFotoExists)
                                    <img src="{{ asset('public/storage/' . $adminFotoPath) }}" alt="{{ $admin->name }}" class="sa-avatar" style="object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="sa-avatar" style="display:none; background:linear-gradient(135deg,#3b82f6,#8b5cf6);">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                                @else
                                    <div class="sa-avatar" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                                @endif
                                <span style="font-weight:600;">{{ $admin->name }}</span>
                            </div>
                        </td>
                        <td style="color:#64748b;">{{ $admin->email }}</td>
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
                        <td style="width:40px; color:#94a3b8;">{{ $i + 1 }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                @php
                                    $userFotoPath = (isset($peng->user) && $peng->user->foto_profil) ? 'foto_profil/' . ltrim($peng->user->foto_profil, '/') : null;
                                    $userFotoExists = $userFotoPath ? Storage::disk('public')->exists($userFotoPath) : false;
                                @endphp
                                @if($userFotoExists)
                                    <img src="{{ asset('public/storage/' . $userFotoPath) }}" alt="{{ $peng->user->name ?? '-' }}" class="sa-avatar" style="object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="sa-avatar" style="display:none; background:linear-gradient(135deg,#f59e0b,#ef4444);">{{ strtoupper(substr($peng->user->name ?? 'U', 0, 1)) }}</div>
                                @else
                                    <div class="sa-avatar" style="background:linear-gradient(135deg,#f59e0b,#ef4444);">{{ strtoupper(substr($peng->user->name ?? 'U', 0, 1)) }}</div>
                                @endif
                                <span style="font-weight:600;">{{ $peng->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td style="color:#64748b;">{{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}</td>
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
                <div><div class="sa-action-title">Kelola Admin</div><div class="sa-action-desc">Tambah atau edit administrator</div></div>
                <i class="fas fa-user-cog" style="font-size:24px; opacity:0.6;"></i>
            </div>
            <span class="sa-action-btn"><i class="fas fa-arrow-right"></i> Kelola</span>
        </a>
        <a href="{{ route('superadmin.manajemenadmin.index') }}" class="sa-action" style="background:linear-gradient(135deg,#10b981,#059669);">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div><div class="sa-action-title">Review Pengajuan</div><div class="sa-action-desc">Proses pengajuan presensi</div></div>
                <i class="fas fa-clipboard-check" style="font-size:24px; opacity:0.6;"></i>
            </div>
            <span class="sa-action-btn"><i class="fas fa-arrow-right"></i> Review</span>
        </a>
        <a href="{{ route('superadmin.dashboard') }}" class="sa-action" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div><div class="sa-action-title">Laporan Sistem</div><div class="sa-action-desc">Lihat statistik lengkap</div></div>
                <i class="fas fa-chart-bar" style="font-size:24px; opacity:0.6;"></i>
            </div>
            <span class="sa-action-btn"><i class="fas fa-arrow-right"></i> Lihat</span>
        </a>
    </div>
</div>
@endsection
