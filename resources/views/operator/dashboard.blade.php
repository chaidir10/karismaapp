@extends('layouts.operator')
@section('title', 'Dashboard Operator')

@section('content')
<style>
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; }
    @media(max-width:1100px){ .stats-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:640px){ .stats-grid { grid-template-columns:1fr; } }

    .stat-card {
        background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;
        padding:18px; display:flex; align-items:center; gap:14px;
    }
    .stat-icon {
        width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center;
        font-size:18px; flex-shrink:0;
    }
    .stat-title { font-size:11px; color:var(--dm-muted,#64748b); font-weight:600; margin-bottom:2px; }
    .stat-value { font-size:22px; font-weight:800; color:var(--dm-text,#1e293b); line-height:1; }

    .grid-main { display:grid; grid-template-columns:1.3fr .7fr; gap:14px; margin-bottom:14px; }
    @media(max-width:1024px){ .grid-main { grid-template-columns:1fr; } }

    .op-card { background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden; }
    .op-card-head {
        padding:14px 18px; border-bottom:1px solid var(--dm-border,#eef2f7);
        display:flex; align-items:center; justify-content:space-between;
    }
    .op-card-title { font-size:13px; font-weight:700; color:var(--dm-text,#1e293b); display:flex; align-items:center; gap:8px; }
    .op-card-badge { font-size:10px; padding:3px 8px; border-radius:999px; font-weight:700; background:rgba(90,182,234,0.12); color:#2E97D4; }
    [data-theme="dark"] .op-card-badge { background:rgba(90,182,234,0.15); color:#7dd3fc; }

    .op-table { width:100%; border-collapse:collapse; }
    .op-table th { font-size:10px; text-transform:uppercase; letter-spacing:.4px; color:var(--dm-muted,#64748b); text-align:left; padding:10px 14px; background:var(--dm-bg,#f8fafc); }
    .op-table td { font-size:12px; color:var(--dm-text,#1e293b); padding:10px 14px; border-top:1px solid var(--dm-border,#f1f5f9); }
    .op-table tbody tr:hover { background:var(--dm-bg,#f8fafc); }
    [data-theme="dark"] .op-table th { background:rgba(255,255,255,0.02); }
    [data-theme="dark"] .op-table tbody tr:hover { background:rgba(255,255,255,0.02); }

    .op-empty { padding:22px 14px; text-align:center; color:var(--dm-muted,#94a3b8); font-size:12px; }

    .kv-row { display:grid; grid-template-columns:1fr auto; gap:8px; padding:10px 14px; border-top:1px solid var(--dm-border,#f1f5f9); font-size:12px; }
    .kv-row .k { color:var(--dm-muted,#64748b); font-weight:600; }
    .kv-row .v { color:var(--dm-text,#1e293b); font-weight:700; }

    .quick-tools { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:20px; }
    @media(max-width:900px){ .quick-tools { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:500px){ .quick-tools { grid-template-columns:1fr; } }

    .quick-tool {
        background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:12px;
        padding:14px; text-decoration:none; color:inherit; display:flex; align-items:center; gap:12px;
        transition:all .15s;
    }
    .quick-tool:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,0.06); border-color:rgba(90,182,234,0.3); }
    .quick-tool-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
    .quick-tool-title { font-size:12px; font-weight:700; color:var(--dm-text,#1e293b); }
    .quick-tool-desc { font-size:10px; color:var(--dm-muted,#64748b); margin-top:1px; }

    .chart-bar { height:100%; border-radius:4px 4px 0 0; background:linear-gradient(180deg,#5AB6EA,#2E97D4); min-height:2px; transition:height .3s; }
    [data-theme="dark"] .chart-bar { background:linear-gradient(180deg,rgba(90,182,234,0.6),rgba(46,151,212,0.4)); }
</style>

<div class="page-header-glass">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h1>Dashboard Operator</h1>
            <p>Pusat kontrol operasional aplikasi KARISMA &middot; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <div style="display:flex; gap:8px;">
            <span class="badge badge-success"><i class="fas fa-circle" style="font-size:7px;"></i> {{ $onlineUsers }} user online</span>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.12); color:#2563eb;"><i class="fas fa-users"></i></div>
        <div><div class="stat-title">Total Pegawai</div><div class="stat-value">{{ $totalPegawai }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.12); color:#059669;"><i class="fas fa-fingerprint"></i></div>
        <div><div class="stat-title">Presensi Masuk Hari Ini</div><div class="stat-value">{{ $presensiMasukHariIni }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.12); color:#d97706;"><i class="fas fa-hourglass-half"></i></div>
        <div><div class="stat-title">Pending (Presensi + Pengajuan)</div><div class="stat-value">{{ $presensiPending + $pengajuanPending }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.12); color:#dc2626;"><i class="fas fa-triangle-exclamation"></i></div>
        <div><div class="stat-title">Issue Perangkat Aktif</div><div class="stat-value">{{ $deviceIssuesOpen }}</div></div>
    </div>
</div>

<div class="quick-tools">
    <a href="{{ route('admin.manajemenpegawai.index') }}" class="quick-tool">
        <div class="quick-tool-icon" style="background:rgba(14,165,233,0.12); color:#0284c7;"><i class="fas fa-user-group"></i></div>
        <div><div class="quick-tool-title">Pegawai</div><div class="quick-tool-desc">{{ $totalPegawai }} pegawai</div></div>
    </a>
    <a href="{{ route('operator.presensi.index') }}" class="quick-tool">
        <div class="quick-tool-icon" style="background:rgba(34,197,94,0.12); color:#16a34a;"><i class="fas fa-database"></i></div>
        <div><div class="quick-tool-title">Database Presensi</div><div class="quick-tool-desc">Kelola data presensi</div></div>
    </a>
    <a href="{{ route('operator.activity-logs.index') }}" class="quick-tool">
        <div class="quick-tool-icon" style="background:rgba(168,85,247,0.12); color:#7e22ce;"><i class="fas fa-clock-rotate-left"></i></div>
        <div><div class="quick-tool-title">Log Aktivitas</div><div class="quick-tool-desc">Audit trail user</div></div>
    </a>
    <a href="{{ route('operator.tracking.index') }}" class="quick-tool">
        <div class="quick-tool-icon" style="background:rgba(239,68,68,0.12); color:#dc2626;"><i class="fas fa-satellite-dish"></i></div>
        <div><div class="quick-tool-title">Tracking User</div><div class="quick-tool-desc">{{ $onlineUsers }} online</div></div>
    </a>
    <a href="{{ route('admin.jamkerja.index') }}" class="quick-tool">
        <div class="quick-tool-icon" style="background:rgba(245,158,11,0.12); color:#d97706;"><i class="fas fa-clock"></i></div>
        <div><div class="quick-tool-title">Jam Kerja</div><div class="quick-tool-desc">{{ $jamKerjaCount }} hari, {{ $shiftCount }} shift</div></div>
    </a>
    <a href="{{ route('admin.lokasi.index') }}" class="quick-tool">
        <div class="quick-tool-icon" style="background:rgba(16,185,129,0.12); color:#059669;"><i class="fas fa-location-dot"></i></div>
        <div><div class="quick-tool-title">Lokasi</div><div class="quick-tool-desc">{{ $wilayahCount }} lokasi</div></div>
    </a>
    <a href="{{ route('operator.pengaturan.index') }}" class="quick-tool">
        <div class="quick-tool-icon" style="background:rgba(71,85,105,0.12); color:#334155;"><i class="fas fa-gear"></i></div>
        <div><div class="quick-tool-title">Pengaturan</div><div class="quick-tool-desc">Logo, instansi, fitur</div></div>
    </a>
    <a href="{{ route('admin.pengumuman.index') }}" class="quick-tool">
        <div class="quick-tool-icon" style="background:rgba(139,92,246,0.12); color:#7c3aed;"><i class="fas fa-bullhorn"></i></div>
        <div><div class="quick-tool-title">Pengumuman</div><div class="quick-tool-desc">{{ $pengumumanAktif }} aktif</div></div>
    </a>
</div>

<div class="grid-main">
    <div style="display:grid; gap:14px;">
        <!-- Presensi chart -->
        <div class="op-card">
            <div class="op-card-head">
                <div class="op-card-title"><i class="fas fa-chart-bar"></i> Presensi 7 Hari Terakhir</div>
            </div>
            <div style="padding:18px; display:flex; align-items:flex-end; gap:10px; height:160px;">
                @php $maxVal = max(array_column($presensi7Hari, 'masuk')) ?: 1; @endphp
                @foreach($presensi7Hari as $p)
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%; justify-content:flex-end;">
                    <div style="font-size:11px; font-weight:700; color:var(--dm-text,#1e293b); margin-bottom:4px;">{{ $p['masuk'] }}</div>
                    <div style="width:100%; background:var(--dm-bg,#f1f5f9); border-radius:6px; overflow:hidden; flex:1; display:flex; flex-direction:column; justify-content:flex-end;">
                        <div class="chart-bar" style="height:{{ ($p['masuk'] / $maxVal) * 100 }}%;"></div>
                    </div>
                    <div style="font-size:9px; color:var(--dm-muted,#94a3b8); margin-top:4px; font-weight:600;">{{ $p['tanggal'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="op-card">
            <div class="op-card-head">
                <div class="op-card-title"><i class="fas fa-clock-rotate-left"></i> Aktivitas Terbaru</div>
                <a href="{{ route('operator.activity-logs.index') }}" style="font-size:11px; color:#2E97D4; font-weight:600; text-decoration:none;">Lihat Semua</a>
            </div>
            <div style="max-height:300px; overflow-y:auto;">
                @forelse($recentActivities as $act)
                <div style="padding:10px 14px; border-top:1px solid var(--dm-border,#f1f5f9); display:flex; align-items:center; gap:10px;">
                    <div style="width:30px; height:30px; border-radius:8px; background:rgba(90,182,234,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        @if($act->action === 'login')
                            <i class="fas fa-right-to-bracket" style="font-size:11px; color:#10b981;"></i>
                        @elseif($act->action === 'logout')
                            <i class="fas fa-right-from-bracket" style="font-size:11px; color:#ef4444;"></i>
                        @elseif($act->action === 'presensi')
                            <i class="fas fa-fingerprint" style="font-size:11px; color:#2563eb;"></i>
                        @else
                            <i class="fas fa-circle-dot" style="font-size:11px; color:#64748b;"></i>
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:12px; font-weight:600; color:var(--dm-text,#1e293b); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $act->user->name ?? '-' }}
                        </div>
                        <div style="font-size:10px; color:var(--dm-muted,#94a3b8);">
                            {{ $act->description }} &middot; {{ $act->device_type ?? '-' }}
                        </div>
                    </div>
                    <div style="font-size:10px; color:var(--dm-muted,#94a3b8); white-space:nowrap;">
                        {{ $act->created_at->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="op-empty">Belum ada log aktivitas.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div style="display:grid; gap:14px;">
        <!-- Info Instansi -->
        <div class="op-card">
            <div class="op-card-head">
                <div class="op-card-title"><i class="fas fa-building"></i> Info Instansi</div>
                <a href="{{ route('operator.pengaturan.index') }}" style="font-size:11px; color:#2E97D4; font-weight:600; text-decoration:none;">Edit</a>
            </div>
            @if($instansi)
            <div class="kv-row"><div class="k">Nama</div><div class="v">{{ $instansi->nama }}</div></div>
            <div class="kv-row"><div class="k">Kode</div><div class="v">{{ $instansi->kode_instansi }}</div></div>
            <div class="kv-row"><div class="k">Alamat</div><div class="v" style="font-size:11px;">{{ $instansi->alamat ?? '-' }}</div></div>
            @else
            <div class="op-empty">Belum ada data instansi. <a href="{{ route('operator.pengaturan.index') }}" style="color:#2E97D4;">Atur sekarang</a></div>
            @endif
        </div>

        <!-- Status Konfigurasi -->
        <div class="op-card">
            <div class="op-card-head">
                <div class="op-card-title"><i class="fas fa-heart-pulse"></i> Konfigurasi Sistem</div>
                <span class="op-card-badge">{{ count($appSettings) }} item</span>
            </div>
            <div class="kv-row"><div class="k">Presensi Libur</div><div class="v">{{ ($appSettings['disable_presensi_hari_libur'] ?? '0') === '1' ? 'Disable' : 'Aktif' }}</div></div>
            <div class="kv-row"><div class="k">Face Detection</div><div class="v">{{ ($appSettings['enable_face_detection'] ?? '0') === '1' ? 'ON' : 'OFF' }}</div></div>
            <div class="kv-row"><div class="k">Wajib Masuk Dulu</div><div class="v">{{ ($appSettings['require_masuk_before_pulang'] ?? '0') === '1' ? 'ON' : 'OFF' }}</div></div>
            <div class="kv-row"><div class="k">Work Timer</div><div class="v">{{ ($appSettings['enable_work_timer'] ?? '0') === '1' ? 'ON' : 'OFF' }}</div></div>
            <div class="kv-row"><div class="k">Absen Darurat</div><div class="v">{{ ($appSettings['enable_absen_darurat'] ?? '0') === '1' ? 'ON' : 'OFF' }}</div></div>
        </div>

        <!-- Infrastruktur -->
        <div class="op-card">
            <div class="op-card-head">
                <div class="op-card-title"><i class="fas fa-layer-group"></i> Infrastruktur</div>
            </div>
            <div class="kv-row"><div class="k">Lokasi Kerja</div><div class="v">{{ $wilayahCount }}</div></div>
            <div class="kv-row"><div class="k">Jam Kerja</div><div class="v">{{ $jamKerjaCount }} hari</div></div>
            <div class="kv-row"><div class="k">Shift</div><div class="v">{{ $shiftCount }}</div></div>
            <div class="kv-row"><div class="k">Admin</div><div class="v">{{ $totalAdmin }}</div></div>
            <div class="kv-row"><div class="k">Operator</div><div class="v">{{ $totalOperator }}</div></div>
        </div>
    </div>
</div>
@endsection
