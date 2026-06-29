@extends('layouts.operator')
@section('title', 'Log Aktivitas')

@section('content')
<style>
    .log-card { background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden; }
    .log-table { width:100%; border-collapse:collapse; }
    .log-table th { font-size:10px; text-transform:uppercase; letter-spacing:.4px; color:var(--dm-muted,#64748b); text-align:left; padding:10px 14px; background:var(--dm-bg,#f8fafc); white-space:nowrap; }
    .log-table td { font-size:12px; color:var(--dm-text,#1e293b); padding:10px 14px; border-top:1px solid var(--dm-border,#f1f5f9); }
    .log-table tbody tr:hover { background:var(--dm-bg,#f8fafc); }
    [data-theme="dark"] .log-table th { background:rgba(255,255,255,0.02); }
    [data-theme="dark"] .log-table tbody tr:hover { background:rgba(255,255,255,0.02); }
    .filter-row { display:flex; gap:10px; flex-wrap:wrap; align-items:end; }
    .filter-group label { font-size:11px; font-weight:600; color:var(--dm-muted,#64748b); display:block; margin-bottom:3px; }
    .filter-group input, .filter-group select {
        padding:7px 10px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:8px; font-size:12px;
        background:var(--dm-input,#fff); color:var(--dm-text); min-width:130px;
    }
    .action-badge { padding:3px 8px; border-radius:6px; font-size:10px; font-weight:700; display:inline-flex; align-items:center; gap:4px; }
    .device-info { display:flex; align-items:center; gap:6px; }
    .device-icon { width:24px; height:24px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:10px; flex-shrink:0; }
    .pagination-wrap { padding:12px 14px; border-top:1px solid var(--dm-border,#e2e8f0); display:flex; justify-content:center; }
    .pagination-wrap nav { font-size:12px; }
    .pagination-wrap .page-link { padding:6px 10px; border:1px solid var(--dm-border,#e2e8f0); border-radius:6px; margin:0 2px; color:var(--dm-text,#374151); text-decoration:none; font-size:11px; }
    .pagination-wrap .page-item.active .page-link { background:#2E97D4; color:#fff; border-color:#2E97D4; }
</style>

<div class="page-header-glass">
    <h1>Log Aktivitas User</h1>
    <p>Audit trail lengkap aktivitas pengguna termasuk perangkat, IP, dan browser</p>
</div>

<!-- Filters -->
<div class="log-card" style="margin-bottom:14px;">
    <div style="padding:14px 18px;">
        <form method="GET" action="{{ route('operator.activity-logs.index') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, URL, IP...">
                </div>
                <div class="filter-group">
                    <label>User</label>
                    <select name="user_id">
                        <option value="">Semua User</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Aksi</label>
                    <select name="action">
                        <option value="">Semua</option>
                        @foreach($actions as $a)
                        <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Perangkat</label>
                    <select name="device_type">
                        <option value="">Semua</option>
                        <option value="desktop" {{ request('device_type') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                        <option value="mobile" {{ request('device_type') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                        <option value="tablet" {{ request('device_type') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label>Sampai</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="filter-group" style="display:flex; gap:6px; padding-bottom:1px;">
                    <button type="submit" class="btn-primary" style="padding:7px 14px; font-size:12px;"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('operator.activity-logs.index') }}" class="btn-secondary" style="padding:7px 14px; font-size:12px;"><i class="fas fa-rotate"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="log-card">
    <div style="overflow-x:auto;">
        <table class="log-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>Perangkat</th>
                    <th>IP</th>
                    <th>Browser / OS</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:28px; height:28px; border-radius:7px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0;">
                                {{ substr($log->user->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:12px;">{{ $log->user->name ?? '-' }}</div>
                                <div style="font-size:10px; color:var(--dm-muted,#94a3b8);">{{ $log->user->role ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $actionColors = [
                                'login' => ['bg' => 'rgba(16,185,129,0.1)', 'color' => '#10b981', 'icon' => 'fa-right-to-bracket'],
                                'logout' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => '#ef4444', 'icon' => 'fa-right-from-bracket'],
                                'presensi' => ['bg' => 'rgba(59,130,246,0.1)', 'color' => '#3b82f6', 'icon' => 'fa-fingerprint'],
                                'page_view' => ['bg' => 'rgba(100,116,139,0.08)', 'color' => '#64748b', 'icon' => 'fa-eye'],
                                'create' => ['bg' => 'rgba(16,185,129,0.1)', 'color' => '#10b981', 'icon' => 'fa-plus'],
                                'update' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => '#d97706', 'icon' => 'fa-pen'],
                                'delete' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => '#ef4444', 'icon' => 'fa-trash'],
                            ];
                            $ac = $actionColors[$log->action] ?? ['bg' => 'rgba(100,116,139,0.08)', 'color' => '#64748b', 'icon' => 'fa-circle-dot'];
                        @endphp
                        <span class="action-badge" style="background:{{ $ac['bg'] }}; color:{{ $ac['color'] }};">
                            <i class="fas {{ $ac['icon'] }}"></i> {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td>
                        <div style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $log->description }}">
                            {{ $log->description }}
                        </div>
                    </td>
                    <td>
                        <div class="device-info">
                            @if($log->device_type === 'mobile')
                                <div class="device-icon" style="background:rgba(168,85,247,0.1); color:#7c3aed;"><i class="fas fa-mobile-screen"></i></div>
                            @elseif($log->device_type === 'tablet')
                                <div class="device-icon" style="background:rgba(245,158,11,0.1); color:#d97706;"><i class="fas fa-tablet-screen-button"></i></div>
                            @else
                                <div class="device-icon" style="background:rgba(59,130,246,0.1); color:#3b82f6;"><i class="fas fa-desktop"></i></div>
                            @endif
                            <span style="font-size:11px;">{{ ucfirst($log->device_type ?? '-') }}</span>
                        </div>
                    </td>
                    <td><code style="font-size:11px; background:var(--dm-bg,#f1f5f9); padding:2px 6px; border-radius:4px;">{{ $log->ip_address ?? '-' }}</code></td>
                    <td>
                        <div style="font-size:11px;">{{ $log->browser ?? '-' }}</div>
                        <div style="font-size:10px; color:var(--dm-muted,#94a3b8);">{{ $log->platform ?? '-' }}</div>
                    </td>
                    <td>
                        <div style="font-size:11px; white-space:nowrap;">{{ $log->created_at->format('d/m/Y') }}</div>
                        <div style="font-size:10px; color:var(--dm-muted,#94a3b8);">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div style="padding:30px; text-align:center; color:var(--dm-muted,#94a3b8); font-size:13px;"><i class="fas fa-inbox" style="font-size:24px; display:block; margin-bottom:8px;"></i> Belum ada log aktivitas</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="pagination-wrap">
        {{ $logs->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
