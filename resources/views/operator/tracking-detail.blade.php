@extends('layouts.operator')
@section('title', 'Detail User - ' . $user->name)

@section('content')
<style>
    .detail-card { background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden; margin-bottom:14px; }
    .detail-head { padding:14px 18px; border-bottom:1px solid var(--dm-border,#eef2f7); display:flex; align-items:center; justify-content:space-between; }
    .detail-title { font-size:13px; font-weight:700; color:var(--dm-text,#1e293b); display:flex; align-items:center; gap:8px; }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    @media(max-width:768px){ .detail-grid { grid-template-columns:1fr; } }
    .detail-table { width:100%; border-collapse:collapse; }
    .detail-table th { font-size:10px; text-transform:uppercase; letter-spacing:.4px; color:var(--dm-muted,#64748b); text-align:left; padding:10px 14px; background:var(--dm-bg,#f8fafc); }
    .detail-table td { font-size:12px; color:var(--dm-text,#1e293b); padding:10px 14px; border-top:1px solid var(--dm-border,#f1f5f9); }
    .kv { display:grid; grid-template-columns:120px 1fr; gap:4px; padding:8px 14px; border-top:1px solid var(--dm-border,#f1f5f9); font-size:12px; }
    .kv .k { color:var(--dm-muted,#64748b); font-weight:600; }
    .kv .v { color:var(--dm-text,#1e293b); font-weight:500; }
    .activity-bar { display:flex; gap:4px; align-items:flex-end; height:60px; }
    .activity-bar-item { flex:1; border-radius:4px 4px 0 0; background:linear-gradient(180deg,#5AB6EA,#2E97D4); min-height:2px; }
    [data-theme="dark"] .activity-bar-item { background:linear-gradient(180deg,rgba(90,182,234,0.6),rgba(46,151,212,0.4)); }
</style>

<div class="page-header-glass">
    <div style="display:flex; align-items:center; gap:14px;">
        <a href="{{ route('operator.tracking.index') }}" style="width:34px; height:34px; border-radius:10px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; color:var(--dm-text); text-decoration:none; font-size:14px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div style="display:flex; align-items:center; gap:12px;">
            @if($user->foto_profil)
                <img src="{{ asset('public/storage/foto_profil/' . $user->foto_profil) }}" style="width:48px; height:48px; border-radius:14px; object-fit:cover;" alt="">
            @else
                <div style="width:48px; height:48px; border-radius:14px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:700;">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif
            <div>
                <h1 style="margin:0;">{{ $user->name }}</h1>
                <p style="margin:0;">{{ $user->nip ?? '-' }} &middot; {{ ucfirst($user->role) }} &middot; {{ $user->email }}</p>
            </div>
        </div>
    </div>
</div>

<div class="detail-grid">
    <!-- Info User -->
    <div class="detail-card">
        <div class="detail-head"><div class="detail-title"><i class="fas fa-user"></i> Informasi User</div></div>
        <div class="kv"><div class="k">Nama</div><div class="v">{{ $user->name }}</div></div>
        <div class="kv"><div class="k">NIP</div><div class="v">{{ $user->nip ?? '-' }}</div></div>
        <div class="kv"><div class="k">Email</div><div class="v">{{ $user->email }}</div></div>
        <div class="kv"><div class="k">Role</div><div class="v">{{ ucfirst($user->role) }}</div></div>
        <div class="kv"><div class="k">Jabatan</div><div class="v">{{ $user->jabatan ?? '-' }}</div></div>
        <div class="kv"><div class="k">No. HP</div><div class="v">{{ $user->no_hp ?? '-' }}</div></div>
        <div class="kv"><div class="k">Bergabung</div><div class="v">{{ optional($user->created_at)->format('d/m/Y H:i') }}</div></div>
    </div>

    <!-- Aktivitas 7 hari -->
    <div class="detail-card">
        <div class="detail-head"><div class="detail-title"><i class="fas fa-chart-bar"></i> Aktivitas 7 Hari Terakhir</div></div>
        <div style="padding:18px;">
            @php $maxAct = $dailyActivity->max('count') ?: 1; @endphp
            <div class="activity-bar">
                @foreach($dailyActivity as $day)
                <div style="flex:1; text-align:center;">
                    <div style="font-size:10px; font-weight:700; color:var(--dm-text); margin-bottom:4px;">{{ $day->count }}</div>
                    <div style="background:var(--dm-bg,#f1f5f9); border-radius:6px; overflow:hidden; height:60px; display:flex; flex-direction:column; justify-content:flex-end;">
                        <div class="activity-bar-item" style="height:{{ ($day->count / $maxAct) * 100 }}%;"></div>
                    </div>
                    <div style="font-size:9px; color:var(--dm-muted); margin-top:4px;">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</div>
                </div>
                @endforeach
                @if($dailyActivity->isEmpty())
                <div style="flex:1; text-align:center; color:var(--dm-muted); font-size:12px; padding:20px;">Belum ada data</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Perangkat -->
<div class="detail-card">
    <div class="detail-head"><div class="detail-title"><i class="fas fa-laptop-mobile"></i> Perangkat yang Digunakan</div></div>
    <div style="overflow-x:auto;">
        <table class="detail-table">
            <thead><tr><th>Tipe</th><th>Browser</th><th>OS</th><th>IP Address</th><th>Terakhir Digunakan</th><th>Total Akses</th></tr></thead>
            <tbody>
                @forelse($devices as $dev)
                <tr>
                    <td>
                        <span class="badge {{ $dev->device_type === 'mobile' ? 'badge-info' : ($dev->device_type === 'tablet' ? 'badge-warning' : 'badge-primary') }}">
                            <i class="fas {{ $dev->device_type === 'mobile' ? 'fa-mobile-screen' : ($dev->device_type === 'tablet' ? 'fa-tablet-screen-button' : 'fa-desktop') }}"></i>
                            {{ ucfirst($dev->device_type ?? '-') }}
                        </span>
                    </td>
                    <td>{{ $dev->browser ?? '-' }}</td>
                    <td>{{ $dev->platform ?? '-' }}</td>
                    <td><code style="font-size:11px; background:var(--dm-bg,#f1f5f9); padding:2px 6px; border-radius:4px;">{{ $dev->ip_address ?? '-' }}</code></td>
                    <td>{{ \Carbon\Carbon::parse($dev->last_used)->format('d/m/Y H:i') }}</td>
                    <td><span style="font-weight:700;">{{ $dev->usage_count }}x</span></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; color:var(--dm-muted); padding:20px;">Belum ada data perangkat</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- IP Addresses -->
<div class="detail-card">
    <div class="detail-head"><div class="detail-title"><i class="fas fa-globe"></i> Riwayat IP Address</div></div>
    <div style="overflow-x:auto;">
        <table class="detail-table">
            <thead><tr><th>IP Address</th><th>Total Akses</th><th>Terakhir Digunakan</th></tr></thead>
            <tbody>
                @forelse($ipAddresses as $ip)
                <tr>
                    <td><code style="font-size:12px; background:var(--dm-bg,#f1f5f9); padding:3px 8px; border-radius:4px;">{{ $ip->ip_address }}</code></td>
                    <td><span style="font-weight:700;">{{ $ip->count }}x</span></td>
                    <td>{{ \Carbon\Carbon::parse($ip->last_used)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center; color:var(--dm-muted); padding:20px;">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Log Aktivitas Terbaru -->
<div class="detail-card">
    <div class="detail-head">
        <div class="detail-title"><i class="fas fa-clock-rotate-left"></i> 50 Aktivitas Terakhir</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="detail-table">
            <thead><tr><th>Aksi</th><th>Deskripsi</th><th>Perangkat</th><th>IP</th><th>Waktu</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        @php
                            $colors = [
                                'login' => '#10b981', 'logout' => '#ef4444', 'presensi' => '#3b82f6',
                                'page_view' => '#64748b', 'create' => '#10b981', 'update' => '#d97706', 'delete' => '#ef4444',
                            ];
                            $c = $colors[$log->action] ?? '#64748b';
                        @endphp
                        <span style="color:{{ $c }}; font-weight:700; font-size:11px;">{{ ucfirst($log->action) }}</span>
                    </td>
                    <td style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $log->description }}</td>
                    <td>{{ ucfirst($log->device_type ?? '-') }}</td>
                    <td><code style="font-size:10px;">{{ $log->ip_address ?? '-' }}</code></td>
                    <td style="white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; color:var(--dm-muted); padding:20px;">Belum ada log</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
