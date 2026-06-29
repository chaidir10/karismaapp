@extends('layouts.operator')
@section('title', 'Tracking User')

@section('content')
<style>
    .track-card { background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden; }
    .user-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:12px; }
    @media(max-width:640px){ .user-grid { grid-template-columns:1fr; } }

    .user-tile {
        background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;
        padding:16px; display:flex; gap:12px; transition:all .15s; text-decoration:none; color:inherit;
    }
    .user-tile:hover { border-color:rgba(90,182,234,0.4); box-shadow:0 4px 12px rgba(0,0,0,0.05); }

    .user-avatar-lg {
        width:44px; height:44px; border-radius:12px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px;
    }

    .status-dot { width:10px; height:10px; border-radius:50%; border:2px solid var(--dm-card,#fff); position:absolute; bottom:-1px; right:-1px; }
    .status-online { background:#10b981; }
    .status-offline { background:#94a3b8; }

    .meta-row { display:flex; align-items:center; gap:6px; font-size:11px; color:var(--dm-muted,#64748b); margin-top:4px; }
    .meta-row i { width:14px; text-align:center; font-size:10px; }

    .filter-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:end; margin-bottom:14px; }
    .filter-bar input, .filter-bar select {
        padding:7px 10px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:8px; font-size:12px;
        background:var(--dm-input,#fff); color:var(--dm-text);
    }
    .stat-mini { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:10px; background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); font-size:12px; }
    .stat-mini-value { font-weight:800; color:var(--dm-text,#1e293b); }
    .stat-mini-label { color:var(--dm-muted,#64748b); font-weight:500; }

    .pagination-wrap { padding:12px 0; display:flex; justify-content:center; }
    .pagination-wrap nav { font-size:12px; }
</style>

<div class="page-header-glass">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h1>Tracking User</h1>
            <p>Pantau status online, perangkat, dan aktivitas terakhir user</p>
        </div>
        <div style="display:flex; gap:8px;">
            @php
                $onlineCount = \App\Models\ActivityLog::where('created_at', '>=', now()->subMinutes(15))->distinct('user_id')->count('user_id');
                $totalCount = \App\Models\User::count();
            @endphp
            <div class="stat-mini"><span class="stat-mini-value" style="color:#10b981;">{{ $onlineCount }}</span><span class="stat-mini-label">Online</span></div>
            <div class="stat-mini"><span class="stat-mini-value">{{ $totalCount }}</span><span class="stat-mini-label">Total</span></div>
        </div>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('operator.tracking.index') }}">
    <div class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, email...">
        <select name="role">
            <option value="">Semua Role</option>
            <option value="pegawai" {{ request('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="operator" {{ request('role') == 'operator' ? 'selected' : '' }}>Operator</option>
            <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
        </select>
        <select name="status">
            <option value="">Semua Status</option>
            <option value="online" {{ request('status') == 'online' ? 'selected' : '' }}>Online</option>
            <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
        </select>
        <button type="submit" class="btn-primary" style="padding:7px 14px; font-size:12px;"><i class="fas fa-search"></i> Filter</button>
        <a href="{{ route('operator.tracking.index') }}" class="btn-secondary" style="padding:7px 14px; font-size:12px;"><i class="fas fa-rotate"></i></a>
    </div>
</form>

<!-- User Grid -->
<div class="user-grid">
    @forelse($users as $user)
    @php
        $lastSeen = $lastActivities[$user->id] ?? null;
        $isOnline = $lastSeen && \Carbon\Carbon::parse($lastSeen)->gte($onlineThreshold);
        $detail = $lastDetails[$user->id] ?? null;
    @endphp
    <a href="{{ route('operator.tracking.detail', $user->id) }}" class="user-tile">
        <div style="position:relative;">
            @if($user->foto_profil)
                <img src="{{ asset('public/storage/foto_profil/' . $user->foto_profil) }}" class="user-avatar-lg" style="object-fit:cover;" alt="">
            @else
                <div class="user-avatar-lg" style="background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff;">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif
            <div class="status-dot {{ $isOnline ? 'status-online' : 'status-offline' }}"></div>
        </div>
        <div style="flex:1; min-width:0;">
            <div style="display:flex; align-items:center; gap:6px; margin-bottom:2px;">
                <span style="font-size:13px; font-weight:700; color:var(--dm-text,#1e293b); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $user->name }}</span>
                <span class="badge {{ $isOnline ? 'badge-success' : 'badge-neutral' }}" style="font-size:9px; padding:2px 6px;">{{ $isOnline ? 'Online' : 'Offline' }}</span>
            </div>
            <div style="font-size:11px; color:var(--dm-muted,#94a3b8);">{{ $user->nip ?? '-' }} &middot; {{ ucfirst($user->role) }}</div>

            @if($detail)
            <div class="meta-row">
                <i class="fas {{ $detail->device_type === 'mobile' ? 'fa-mobile-screen' : 'fa-desktop' }}"></i>
                {{ ucfirst($detail->device_type ?? '-') }}
                &middot;
                <i class="fas fa-globe"></i>
                {{ $detail->ip_address ?? '-' }}
            </div>
            <div class="meta-row">
                <i class="fas fa-clock"></i>
                @if($lastSeen)
                    {{ \Carbon\Carbon::parse($lastSeen)->diffForHumans() }}
                @else
                    Belum ada aktivitas
                @endif
            </div>
            @else
            <div class="meta-row"><i class="fas fa-clock"></i> Belum ada aktivitas</div>
            @endif
        </div>
        <div style="display:flex; align-items:center;">
            <i class="fas fa-chevron-right" style="font-size:11px; color:var(--dm-muted,#94a3b8);"></i>
        </div>
    </a>
    @empty
    <div style="grid-column:1/-1; padding:40px; text-align:center; color:var(--dm-muted,#94a3b8);">
        <i class="fas fa-users-slash" style="font-size:28px; display:block; margin-bottom:8px;"></i>
        Tidak ada user ditemukan
    </div>
    @endforelse
</div>

@include('operator.partials.pagination', ['paginator' => $users])
@endsection
