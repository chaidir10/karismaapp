@extends('layouts.operator')

@section('title', 'Dashboard Operator')

@section('content')
<style>
    .op-grid-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:16px; }
    @media(max-width:1100px){ .op-grid-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:640px){ .op-grid-stats { grid-template-columns:1fr; } }

    .op-stat {
        background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:14px;
        display:flex; align-items:center; gap:12px;
    }
    .op-stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
    .op-stat-title { font-size:11px; color:#64748b; font-weight:600; margin-bottom:2px; }
    .op-stat-value { font-size:20px; font-weight:800; color:#0f172a; line-height:1; }

    .op-grid-main { display:grid; grid-template-columns:1.3fr .9fr; gap:14px; margin-bottom:14px; }
    @media(max-width:1024px){ .op-grid-main { grid-template-columns:1fr; } }

    .op-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; }
    .op-card-head {
        padding:12px 14px; border-bottom:1px solid #eef2f7; background:#f8fafc;
        display:flex; align-items:center; justify-content:space-between;
    }
    .op-card-title { font-size:13px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px; }
    .op-card-badge { font-size:10px; padding:3px 8px; border-radius:999px; font-weight:700; background:#e2e8f0; color:#334155; }

    .op-table { width:100%; border-collapse:collapse; }
    .op-table th { font-size:10px; text-transform:uppercase; letter-spacing:.4px; color:#64748b; text-align:left; padding:10px 12px; }
    .op-table td { font-size:12px; color:#0f172a; padding:10px 12px; border-top:1px solid #f1f5f9; }
    .op-empty { padding:22px 14px; text-align:center; color:#94a3b8; font-size:12px; }

    .op-tools { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
    @media(max-width:1024px){ .op-tools { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:640px){ .op-tools { grid-template-columns:1fr; } }

    .op-tool {
        background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:14px;
        text-decoration:none; color:inherit; display:block; transition:all .15s;
    }
    .op-tool:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(15,23,42,.08); }
    .op-tool-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
    .op-tool-icon {
        width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center;
        font-size:15px;
    }
    .op-tool-title { font-size:14px; font-weight:700; color:#0f172a; margin-bottom:4px; }
    .op-tool-desc { font-size:12px; color:#64748b; line-height:1.4; min-height:34px; }
    .op-tool-link { margin-top:10px; font-size:11px; font-weight:700; color:#2563eb; }

    .op-kv { display:grid; grid-template-columns:1fr auto; gap:8px; padding:10px 12px; border-top:1px solid #f1f5f9; font-size:12px; }
    .op-kv .k { color:#64748b; font-weight:600; }
    .op-kv .v { color:#0f172a; font-weight:700; }
</style>

<div style="margin-bottom:16px;">
    <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">Operator Aplikasi / IT Console</h1>
    <p style="font-size:13px; color:#64748b; margin-top:4px;">Pusat kontrol operasional aplikasi KARISMA secara menyeluruh.</p>
</div>

<div class="op-grid-stats">
    <div class="op-stat">
        <div class="op-stat-icon" style="background:rgba(59,130,246,.12); color:#2563eb;"><i class="fas fa-users"></i></div>
        <div><div class="op-stat-title">Total Pegawai</div><div class="op-stat-value">{{ $totalPegawai }}</div></div>
    </div>
    <div class="op-stat">
        <div class="op-stat-icon" style="background:rgba(16,185,129,.12); color:#059669;"><i class="fas fa-user-shield"></i></div>
        <div><div class="op-stat-title">Admin + Operator</div><div class="op-stat-value">{{ $totalAdmin + $totalOperator }}</div></div>
    </div>
    <div class="op-stat">
        <div class="op-stat-icon" style="background:rgba(245,158,11,.12); color:#d97706;"><i class="fas fa-clock"></i></div>
        <div><div class="op-stat-title">Presensi Hari Ini</div><div class="op-stat-value">{{ $presensiHariIni }}</div></div>
    </div>
    <div class="op-stat">
        <div class="op-stat-icon" style="background:rgba(239,68,68,.12); color:#dc2626;"><i class="fas fa-triangle-exclamation"></i></div>
        <div><div class="op-stat-title">Issue Perangkat Aktif</div><div class="op-stat-value">{{ $deviceIssuesOpen }}</div></div>
    </div>
</div>

<div class="op-grid-main">
    <div class="op-card">
        <div class="op-card-head">
            <div class="op-card-title"><i class="fas fa-screwdriver-wrench"></i> Toolset Operasional Lengkap</div>
            <span class="op-card-badge">Akses Cepat</span>
        </div>
        <div style="padding:12px;">
            <div class="op-tools">
                <a href="{{ route('admin.dashboard') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(59,130,246,.12); color:#2563eb;"><i class="fas fa-chart-line"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Monitoring Dashboard</div>
                    <div class="op-tool-desc">Memantau presensi pending, status approval, dan ringkasan operasional harian.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>

                <a href="{{ route('admin.manajemenpegawai.index') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(14,165,233,.12); color:#0284c7;"><i class="fas fa-users-cog"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Manajemen Pengguna</div>
                    <div class="op-tool-desc">Kelola data pegawai, status akun, akses shift, reset password, dan pemetaan wilayah kerja.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>

                <a href="{{ route('admin.lokasi.index') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(34,197,94,.12); color:#16a34a;"><i class="fas fa-location-dot"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Lokasi & Radius</div>
                    <div class="op-tool-desc">Atur titik lokasi kerja, radius validasi GPS, dan akurasi cakupan area presensi.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>

                <a href="{{ route('admin.jamkerja.index') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(245,158,11,.12); color:#d97706;"><i class="fas fa-business-time"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Jam Kerja & Shift</div>
                    <div class="op-tool-desc">Konfigurasi jam kerja reguler/shift, sinkronisasi jadwal, serta kontrol hari libur nasional.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>

                <a href="{{ route('admin.pengumuman.index') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(168,85,247,.12); color:#7e22ce;"><i class="fas fa-bullhorn"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Pengumuman Sistem</div>
                    <div class="op-tool-desc">Publikasi informasi penting, pemeliharaan, dan instruksi operasional ke seluruh pengguna.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>

                <a href="{{ route('admin.device-issues.index') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(239,68,68,.12); color:#dc2626;"><i class="fas fa-screwdriver-wrench"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Device Issues</div>
                    <div class="op-tool-desc">Investigasi kendala perangkat pengguna, tindak lanjut insiden, dan penyelesaian ticket.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>

                <a href="{{ route('admin.laporan.index') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(14,116,144,.12); color:#0e7490;"><i class="fas fa-file-chart-column"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Laporan & Audit</div>
                    <div class="op-tool-desc">Audit data presensi, ekspor PDF/Excel, dan validasi historis aktivitas sistem.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>

                <a href="{{ route('admin.pengaturan.index') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(71,85,105,.12); color:#334155;"><i class="fas fa-sliders"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Pengaturan Aplikasi</div>
                    <div class="op-tool-desc">Kontrol fitur global: face detection, work timer, darurat, dan kebijakan presensi.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>

                <a href="{{ route('superadmin.manajemenadmin.index') }}" class="op-tool">
                    <div class="op-tool-top">
                        <div class="op-tool-icon" style="background:rgba(37,99,235,.12); color:#1d4ed8;"><i class="fas fa-user-lock"></i></div>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:11px; color:#94a3b8;"></i>
                    </div>
                    <div class="op-tool-title">Role & Akses</div>
                    <div class="op-tool-desc">Koordinasi pengelolaan role admin/operator bersama superadmin untuk governance akses.</div>
                    <div class="op-tool-link">Buka Modul</div>
                </a>
            </div>
        </div>
    </div>

    <div style="display:grid; gap:14px;">
        <div class="op-card">
            <div class="op-card-head">
                <div class="op-card-title"><i class="fas fa-heart-pulse"></i> Status Konfigurasi Inti</div>
                <span class="op-card-badge">{{ count($appSettings ?? []) }} item</span>
            </div>

            <div class="op-kv">
                <div class="k">Disable Presensi Libur</div>
                <div class="v">{{ ($appSettings['disable_presensi_hari_libur'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' }}</div>
            </div>
            <div class="op-kv">
                <div class="k">Face Detection</div>
                <div class="v">{{ ($appSettings['enable_face_detection'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' }}</div>
            </div>
            <div class="op-kv">
                <div class="k">Mode Face Detection</div>
                <div class="v">{{ strtoupper($appSettings['face_detection_mode'] ?? '-') }}</div>
            </div>
            <div class="op-kv">
                <div class="k">Require Masuk Before Pulang</div>
                <div class="v">{{ ($appSettings['require_masuk_before_pulang'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' }}</div>
            </div>
            <div class="op-kv">
                <div class="k">Work Timer</div>
                <div class="v">{{ ($appSettings['enable_work_timer'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' }}</div>
            </div>
            <div class="op-kv">
                <div class="k">Absen Darurat</div>
                <div class="v">{{ ($appSettings['enable_absen_darurat'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' }}</div>
            </div>
            <div class="op-kv">
                <div class="k">Mode Darurat</div>
                <div class="v">{{ strtoupper($appSettings['absen_darurat_mode'] ?? '-') }}</div>
            </div>
        </div>

        <div class="op-card">
            <div class="op-card-head">
                <div class="op-card-title"><i class="fas fa-layer-group"></i> Ringkasan Infrastruktur Aplikasi</div>
                <span class="op-card-badge">Resource</span>
            </div>
            <div class="op-kv"><div class="k">Lokasi Kerja</div><div class="v">{{ $wilayahCount }}</div></div>
            <div class="op-kv"><div class="k">Template Jam Kerja</div><div class="v">{{ $jamKerjaCount }}</div></div>
            <div class="op-kv"><div class="k">Shift Aktif</div><div class="v">{{ $shiftCount }}</div></div>
            <div class="op-kv"><div class="k">Pengumuman</div><div class="v">{{ $pengumumanAktif }}</div></div>
            <div class="op-kv"><div class="k">Presensi Pending</div><div class="v">{{ $presensiPending }}</div></div>
            <div class="op-kv"><div class="k">Pengajuan Pending</div><div class="v">{{ $pengajuanPending }}</div></div>
        </div>
    </div>
</div>

<div class="op-grid-main">
    <div class="op-card">
        <div class="op-card-head">
            <div class="op-card-title"><i class="fas fa-list-check"></i> Pengajuan Terbaru</div>
            <span class="op-card-badge">{{ $pengajuanTerbaru->count() }} item</span>
        </div>
        <table class="op-table">
            <thead>
                <tr><th>Pegawai</th><th>Tanggal</th><th>Jenis</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($pengajuanTerbaru as $p)
                <tr>
                    <td>{{ $p->user->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal ?? now())->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($p->jenis ?? '-') }}</td>
                    <td>{{ ucfirst($p->status ?? '-') }}</td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="op-empty">Belum ada data pengajuan.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="op-card">
        <div class="op-card-head">
            <div class="op-card-title"><i class="fas fa-user-shield"></i> Admin/Operator Terbaru</div>
            <span class="op-card-badge">{{ $adminTerbaru->count() }} akun</span>
        </div>
        <table class="op-table">
            <thead>
                <tr><th>Nama</th><th>Role</th><th>Dibuat</th></tr>
            </thead>
            <tbody>
                @forelse($adminTerbaru as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ strtoupper($u->role) }}</td>
                    <td>{{ optional($u->created_at)->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="op-empty">Belum ada data admin/operator.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="op-card">
    <div class="op-card-head">
        <div class="op-card-title"><i class="fas fa-mobile-screen-button"></i> Device Issues Terbaru</div>
        <span class="op-card-badge">{{ $deviceIssuesTerbaru->count() }} laporan</span>
    </div>
    <table class="op-table">
        <thead>
            <tr><th>User</th><th>Jenis</th><th>Detail</th><th>Waktu</th></tr>
        </thead>
        <tbody>
            @forelse($deviceIssuesTerbaru as $d)
            <tr>
                <td>{{ $d->user->name ?? '-' }}</td>
                <td>{{ $d->type ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($d->detail ?? '-', 60) }}</td>
                <td>{{ optional($d->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="4"><div class="op-empty">Belum ada laporan perangkat.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
