<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <link rel="icon" type="image/png" sizes="48x48" href="{{ $appLogoUrl ?? asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ $appLogoUrl ?? asset('public/images/favicon-48x48.png') }}" type="image/png">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <title>Operator | @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <script>(function(){ var t=localStorage.getItem('karisma-admin-theme')||'light'; document.documentElement.setAttribute('data-theme',t); })();</script>

    @stack('head')
    @stack('styles')
    <style>
        :root, [data-theme="light"] {
            --dm-bg: #f9fafb;
            --dm-card: #ffffff;
            --dm-sidebar: #ffffff;
            --dm-topbar: #ffffff;
            --dm-border: #e5e7eb;
            --dm-text: #1e293b;
            --dm-text2: #4b5563;
            --dm-muted: #6b7280;
            --dm-input: #ffffff;
            --dm-input-border: #d1d5db;
        }
        [data-theme="dark"] {
            --dm-bg: #0b0f19;
            --dm-card: #141b2d;
            --dm-sidebar: #0b0f19;
            --dm-topbar: #141b2d;
            --dm-border: #1e293b;
            --dm-text: #e2e8f0;
            --dm-text2: #94a3b8;
            --dm-muted: #64748b;
            --dm-input: #141b2d;
            --dm-input-border: #1e293b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            -webkit-text-size-adjust: 100%;
        }

        @media (max-width: 640px) {
            .table-responsive {
                display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;
            }
        }

        .btn-primary, .btn-submit {
            padding:8px 18px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;
            display:inline-flex; align-items:center; justify-content:center; gap:6px;
            background:#2E97D4; color:#fff; border:1.5px solid #1a7ab5;
            -webkit-tap-highlight-color:transparent; transition:all 0.15s;
        }
        .btn-primary:hover, .btn-submit:hover { background:#2688bf; border-color:#2688bf; }
        .btn-primary:active, .btn-submit:active { transform:scale(0.96); }
        .btn-primary:disabled { opacity:0.4; cursor:not-allowed; }
        [data-theme="dark"] .btn-primary, [data-theme="dark"] .btn-submit {
            background:rgba(90,182,234,0.08); color:#93c5fd; border-width:1.5px; border-color:#5AB6EA;
        }
        [data-theme="dark"] .btn-primary:hover, [data-theme="dark"] .btn-submit:hover { background:rgba(90,182,234,0.2); }

        .btn-success {
            padding:6px 12px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer;
            display:inline-flex; align-items:center; justify-content:center; gap:5px;
            background:#10b981; color:#fff; border:1.5px solid #059669;
            -webkit-tap-highlight-color:transparent; transition:all 0.15s;
        }
        .btn-success:hover { background:#059669; }
        [data-theme="dark"] .btn-success { background:rgba(16,185,129,0.08); color:#6ee7b7; border-color:#34d399; }

        .btn-danger {
            padding:6px 12px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer;
            display:inline-flex; align-items:center; justify-content:center; gap:5px;
            background:#ef4444; color:#fff; border:1.5px solid #dc2626;
            -webkit-tap-highlight-color:transparent; transition:all 0.15s;
        }
        .btn-danger:hover { background:#dc2626; }
        [data-theme="dark"] .btn-danger { background:rgba(239,68,68,0.08); color:#fca5a5; border-color:#f87171; }

        .btn-warning {
            padding:6px 12px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer;
            display:inline-flex; align-items:center; justify-content:center; gap:5px;
            background:#f59e0b; color:#fff; border:1.5px solid #d97706;
        }
        [data-theme="dark"] .btn-warning { background:rgba(245,158,11,0.08); color:#fde68a; border-color:#fbbf24; }

        .btn-secondary {
            padding:8px 18px; border:1px solid var(--dm-border,#d1d5db); border-radius:10px;
            font-size:13px; font-weight:600; cursor:pointer;
            display:inline-flex; align-items:center; gap:6px;
            background:var(--dm-card,#fff); color:var(--dm-text,#374151);
            transition:all 0.15s;
        }
        .btn-secondary:hover { background:var(--dm-bg,#f1f5f9); }
        [data-theme="dark"] .btn-secondary { background:rgba(255,255,255,0.04); color:#e2e8f0; border-color:rgba(255,255,255,0.25); }

        .badge { padding:4px 10px; border-radius:8px; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:4px; }
        .badge-primary { background:rgba(90,182,234,0.12); color:#2E97D4; border:1px solid rgba(90,182,234,0.2); }
        .badge-success { background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.15); }
        .badge-danger { background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.15); }
        .badge-warning { background:rgba(245,158,11,0.1); color:#d97706; border:1px solid rgba(245,158,11,0.15); }
        .badge-info { background:rgba(139,92,246,0.1); color:#7c3aed; border:1px solid rgba(139,92,246,0.15); }
        .badge-neutral { background:rgba(100,116,139,0.08); color:#64748b; border:1px solid rgba(100,116,139,0.12); }
        [data-theme="dark"] .badge-primary { background:rgba(90,182,234,0.15); color:#7dd3fc; }
        [data-theme="dark"] .badge-success { background:rgba(16,185,129,0.15); color:#34d399; }
        [data-theme="dark"] .badge-danger { background:rgba(239,68,68,0.15); color:#fca5a5; }
        [data-theme="dark"] .badge-warning { background:rgba(245,158,11,0.15); color:#fbbf24; }

        .page-header-glass {
            background: linear-gradient(135deg, rgba(90,182,234,0.15), rgba(46,151,212,0.08));
            border: 1px solid rgba(90,182,234,0.15); border-radius: 16px;
            padding: 24px 28px; margin-bottom: 24px; backdrop-filter: blur(12px);
            position: relative; overflow: hidden;
        }
        .page-header-glass::before {
            content:''; position:absolute; top:-50%; right:-20%;
            width:300px; height:300px; border-radius:50%;
            background:radial-gradient(circle, rgba(90,182,234,0.1), transparent 70%);
            pointer-events:none;
        }
        .page-header-glass h1 { font-size:22px; font-weight:700; color:var(--dm-text); margin:0 0 4px; position:relative; z-index:1; }
        .page-header-glass p { font-size:13px; color:var(--dm-muted); margin:0; position:relative; z-index:1; }
        [data-theme="dark"] .page-header-glass {
            background: linear-gradient(135deg, rgba(90,182,234,0.08), rgba(46,151,212,0.04));
            border-color: rgba(90,182,234,0.1);
        }

        .btn-detail, .btn-edit, .btn-delete {
            width:30px; height:30px; border:none; border-radius:8px;
            display:flex; align-items:center; justify-content:center;
            cursor:pointer; font-size:12px; transition:all 0.15s;
        }
        .btn-detail { background:rgba(16,185,129,0.1); color:#10b981; }
        .btn-detail:hover { background:#10b981; color:#fff; }
        .btn-edit { background:rgba(59,130,246,0.1); color:#3b82f6; }
        .btn-edit:hover { background:#3b82f6; color:#fff; }
        .btn-delete { background:rgba(239,68,68,0.1); color:#ef4444; }
        .btn-delete:hover { background:#ef4444; color:#fff; }
        [data-theme="dark"] .btn-detail { background:rgba(16,185,129,0.15); color:#34d399; }
        [data-theme="dark"] .btn-edit { background:rgba(59,130,246,0.15); color:#60a5fa; }
        [data-theme="dark"] .btn-delete { background:rgba(239,68,68,0.15); color:#f87171; }

        .main-container { display: flex; min-height: 100vh; }

        * { scrollbar-width:thin; scrollbar-color:rgba(0,0,0,0.12) transparent; }
        ::-webkit-scrollbar { width:4px; height:4px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.12); border-radius:20px; }
        [data-theme="dark"] * { scrollbar-color:rgba(255,255,255,0.08) transparent; }
        [data-theme="dark"] ::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.08); }

        .sidebar {
            width: 230px; background: var(--dm-card, #fff);
            border-right: 1px solid var(--dm-border, #e2e8f0);
            position: fixed; left: 0; top: 0; height: 100vh; z-index: 40; display: none;
        }
        [data-theme="dark"] .sidebar { background: #0d1117; border-color: rgba(255,255,255,0.06); }
        @media (min-width: 640px) { .sidebar { display: flex; flex-direction: column; } }

        .sidebar-content { display: flex; flex-direction: column; height: 100%; }
        .sidebar-scrollable { flex: 1; overflow-y: auto; padding: 8px 0 16px; }

        .sidebar-item {
            display: flex; align-items: center; gap: 10px; padding: 9px 14px;
            color: var(--dm-muted, #64748b); text-decoration: none;
            font-size: 13px; font-weight: 500; border-radius: 10px;
            margin: 2px 10px; cursor: pointer; transition: all 0.15s; position: relative;
        }
        .sidebar-item:hover { background: rgba(90,182,234,0.08); color: var(--dm-text, #1e293b); }
        .sidebar-item.active { background: rgba(90,182,234,0.12); color: #2E97D4; font-weight: 600; }
        .sidebar-item.active::before {
            content: ''; position: absolute; left: -10px; top: 0; bottom: 0;
            width: 3px; border-radius: 0 3px 3px 0; background: #2E97D4;
        }
        .sidebar-item i { width: 18px; font-size: 14px; text-align: center; flex-shrink: 0; }

        .sidebar-title {
            font-size: 10px; font-weight: 700; padding: 0 16px; margin: 16px 0 6px;
            color: var(--dm-muted, #94a3b8); text-transform: uppercase; letter-spacing: 0.8px;
        }

        [data-theme="dark"] .sidebar-item { color: #8b9cb8; }
        [data-theme="dark"] .sidebar-item:hover { background: rgba(90,182,234,0.08); color: #e2e8f0; }
        [data-theme="dark"] .sidebar-item.active { background: rgba(90,182,234,0.12); color: #7dd3fc; }
        [data-theme="dark"] .sidebar-item.active::before { background: #5AB6EA; }

        .content-area { flex: 1; margin-left: 0; display: flex; flex-direction: column; min-height: 100vh; }
        @media (min-width: 640px) { .content-area { margin-left: 230px; } }

        .topbar {
            background: var(--dm-card, #fff); border-bottom: 1px solid var(--dm-border, #e2e8f0);
            padding: 0 24px; height: 56px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 30;
        }
        [data-theme="dark"] .topbar { background: #0d1117; border-color: rgba(255,255,255,0.06); }

        .topbar-title { font-size: 14px; font-weight: 600; color: var(--dm-text, #1e293b); }
        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .topbar-btn {
            width: 36px; height: 36px; border-radius: 10px; border: none;
            background: var(--dm-bg, #f1f5f9); color: var(--dm-muted, #64748b);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 14px; transition: all 0.15s;
        }
        .topbar-btn:hover { background: var(--dm-border, #e2e8f0); color: var(--dm-text, #1e293b); }
        [data-theme="dark"] .topbar-btn { background: rgba(255,255,255,0.06); color: #94a3b8; }
        [data-theme="dark"] .topbar-btn:hover { background: rgba(255,255,255,0.1); color: #e2e8f0; }

        .topbar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 4px 10px 4px 4px; border-radius: 12px;
            background: var(--dm-bg, #f8fafc); border: 1px solid var(--dm-border, #e2e8f0);
        }
        [data-theme="dark"] .topbar-user { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.06); }

        .user-avatar {
            width: 32px; height: 32px; border-radius: 8px; object-fit: cover; flex-shrink: 0;
        }
        .topbar-user-name { font-size: 12px; font-weight: 600; color: var(--dm-text, #1e293b); }

        .main-content { flex: 1; overflow-y: auto; background: var(--dm-bg, #f9fafb); }
        [data-theme="dark"] .main-content { background: #0b0f19; }

        .layout-footer {
            margin-top: 14px; padding: 10px 14px; border-radius: 12px;
            border: 1px solid var(--dm-border, #e2e8f0);
            background: linear-gradient(135deg, rgba(90,182,234,0.08), rgba(46,151,212,0.04));
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            font-size: 11px; color: var(--dm-muted, #64748b);
        }
        .layout-footer strong { color: var(--dm-text, #1e293b); font-weight: 700; }
        .layout-footer .footer-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 8px; border-radius: 999px;
            background: rgba(90,182,234,0.12); color: #2E97D4; font-weight: 600; font-size: 10px;
        }
        [data-theme="dark"] .layout-footer {
            background: linear-gradient(135deg, rgba(90,182,234,0.12), rgba(46,151,212,0.05));
            border-color: rgba(255,255,255,0.08); color: #94a3b8;
        }
        [data-theme="dark"] .layout-footer strong { color: #e2e8f0; }
        [data-theme="dark"] .layout-footer .footer-chip { background: rgba(90,182,234,0.18); color: #7dd3fc; }

        .logout-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1100; align-items: center; justify-content: center; }
        .logout-modal.active { display: flex; }
        .logout-modal-content {
            background: var(--dm-card,#fff); border-radius: 16px; padding: 2rem; max-width: 400px; width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); text-align: center; animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn { from { opacity:0; transform:translateY(-20px) scale(0.95); } to { opacity:1; transform:translateY(0) scale(1); } }
        .logout-icon { font-size: 3rem; color: #ef4444; margin-bottom: 1rem; }
        .logout-modal h3 { font-size: 1.25rem; font-weight: 600; color: var(--dm-text,#1f2937); margin-bottom: 0.5rem; }
        .logout-modal p { color: var(--dm-muted,#6b7280); margin-bottom: 1.5rem; }
        .logout-modal-buttons { display: flex; gap: 0.75rem; justify-content: center; }
        .logout-btn-cancel { background: var(--dm-bg,#f3f4f6); color: var(--dm-text,#374151); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; cursor: pointer; }
        .logout-btn-confirm { background: linear-gradient(135deg,#ef4444,#dc2626); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; cursor: pointer; box-shadow: 0 4px 6px rgba(239,68,68,0.3); }
        .logout-btn-confirm:hover { transform: translateY(-1px); }
        [data-theme="dark"] .logout-modal-content { background: var(--dm-card); }
        [data-theme="dark"] .logout-btn-cancel { background: #1e293b; color: var(--dm-text); }

        .mobile-sidebar { position: fixed; inset: 0; z-index: 50; transform: translateX(-100%); transition: transform 0.3s ease-in-out; }
        .mobile-sidebar.open { transform: translateX(0); }
        .mobile-sidebar-content { width: 250px; height: 100%; background: var(--dm-card,#fff); box-shadow: 0 0 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        .mobile-sidebar-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 40; }
        [data-theme="dark"] .mobile-sidebar-content { background: var(--dm-sidebar); border-color: var(--dm-border); }

        [data-theme="dark"] body { background-color: var(--dm-bg) !important; color: var(--dm-text) !important; }
        [data-theme="dark"] .bg-white { background-color: var(--dm-card) !important; }
        [data-theme="dark"] .bg-gray-50, [data-theme="dark"] .bg-gray-100 { background-color: #0f1626 !important; }
        [data-theme="dark"] .text-gray-800, [data-theme="dark"] .text-gray-900 { color: var(--dm-text) !important; }
        [data-theme="dark"] .text-gray-700 { color: var(--dm-text2) !important; }
        [data-theme="dark"] .text-gray-600, [data-theme="dark"] .text-gray-500, [data-theme="dark"] .text-gray-400 { color: var(--dm-muted) !important; }
        [data-theme="dark"] .border-gray-100, [data-theme="dark"] .border-gray-200, [data-theme="dark"] .border-gray-300 { border-color: var(--dm-border) !important; }
        [data-theme="dark"] .divide-gray-200 > * + * { border-color: var(--dm-border) !important; }
        [data-theme="dark"] .shadow-xl, [data-theme="dark"] .shadow-lg, [data-theme="dark"] .shadow-md, [data-theme="dark"] .shadow-sm { box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important; }
        [data-theme="dark"] input, [data-theme="dark"] textarea, [data-theme="dark"] select {
            background-color: var(--dm-input) !important; border-color: var(--dm-input-border) !important; color: var(--dm-text) !important;
        }
        [data-theme="dark"] .hover\:bg-gray-50:hover { background-color: #1a2332 !important; }

        .sr-only { position:absolute !important; width:1px !important; height:1px !important; overflow:hidden !important; clip:rect(0,0,0,0) !important; white-space:nowrap !important; border:0 !important; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 text-sm">
    <div class="logout-modal" id="logoutModal">
        <div class="logout-modal-content">
            <div class="logout-icon"><i class="fas fa-sign-out-alt"></i></div>
            <h3>Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin keluar dari sistem?</p>
            <div class="logout-modal-buttons">
                <button type="button" class="logout-btn-cancel" id="logoutCancelBtn">Batal</button>
                <button type="button" class="logout-btn-confirm" id="logoutConfirmBtn">Ya, Logout</button>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <div class="sidebar-content">
                <div style="height:56px; padding:0 16px; border-bottom:1px solid var(--dm-border,#e2e8f0); display:flex; align-items:center;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        @if($appLogoUrl)
                            <img src="{{ $appLogoUrl }}" alt="Logo" style="width:32px; height:32px; border-radius:10px; object-fit:contain;">
                        @else
                            <div style="width:32px; height:32px; border-radius:10px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:14px;">K</div>
                        @endif
                        <div>
                            <div style="font-size:14px; font-weight:700; color:var(--dm-text,#1e293b);">KARISMA</div>
                            <div style="font-size:10px; color:var(--dm-muted,#94a3b8);">Operator Console</div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-scrollable">
                    <div style="padding:4px 0;">
                        <div class="sidebar-title">Dashboard</div>
                        <a href="{{ route('operator.dashboard') }}" class="sidebar-item @if(request()->routeIs('operator.dashboard')) active @endif">
                            <i class="fas fa-gauge-high"></i> Dashboard
                        </a>

                        <div class="sidebar-title">Manajemen</div>
                        <a href="{{ route('admin.manajemenpegawai.index') }}" class="sidebar-item @if(request()->routeIs('admin.manajemenpegawai.*')) active @endif">
                            <i class="fas fa-user-group"></i> Pegawai
                        </a>
                        <a href="{{ route('admin.jamkerja.index') }}" class="sidebar-item @if(request()->routeIs('admin.jamkerja.index') || request()->routeIs('admin.jamkerja.shift.*')) active @endif">
                            <i class="fas fa-clock"></i> Jam Kerja
                        </a>
                        <a href="{{ route('admin.jamkerja.holidays') }}" class="sidebar-item @if(request()->routeIs('admin.jamkerja.holidays')) active @endif">
                            <i class="fas fa-calendar-xmark"></i> Hari Libur
                        </a>
                        <a href="{{ route('admin.lokasi.index') }}" class="sidebar-item @if(request()->routeIs('admin.lokasi.*')) active @endif">
                            <i class="fas fa-location-dot"></i> Lokasi
                        </a>
                        <a href="{{ route('operator.presensi.index') }}" class="sidebar-item @if(request()->routeIs('operator.presensi.*')) active @endif">
                            <i class="fas fa-database"></i> Database Presensi
                        </a>

                        <div class="sidebar-title">Tools</div>
                        <a href="{{ route('admin.laporan.index') }}" class="sidebar-item @if(request()->routeIs('admin.laporan.*')) active @endif">
                            <i class="fas fa-chart-column"></i> Laporan
                        </a>
                        <a href="{{ route('admin.performa.index') }}" class="sidebar-item @if(request()->routeIs('admin.performa.*')) active @endif">
                            <i class="fas fa-ranking-star"></i> Performa
                        </a>
                        <a href="{{ route('admin.pengumuman.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengumuman.*')) active @endif">
                            <i class="fas fa-bullhorn"></i> Pengumuman
                        </a>
                        @php $diCount = \App\Models\DeviceIssue::whereNull('resolved_at')->count(); @endphp
                        <a href="{{ route('admin.device-issues.index') }}" class="sidebar-item @if(request()->routeIs('admin.device-issues.*')) active @endif">
                            <i class="fas fa-mobile-screen-button"></i> Kendala Perangkat
                            @if($diCount > 0)<span style="margin-left:auto; background:#ef4444; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:10px; min-width:18px; text-align:center;">{{ $diCount }}</span>@endif
                        </a>

                        <div class="sidebar-title">Sistem</div>
                        <a href="{{ route('operator.pengaturan.index') }}" class="sidebar-item @if(request()->routeIs('operator.pengaturan.*')) active @endif">
                            <i class="fas fa-gear"></i> Pengaturan
                        </a>
                        <a href="{{ route('operator.activity-logs.index') }}" class="sidebar-item @if(request()->routeIs('operator.activity-logs.*')) active @endif">
                            <i class="fas fa-clock-rotate-left"></i> Log Aktivitas
                        </a>
                        <a href="{{ route('operator.tracking.index') }}" class="sidebar-item @if(request()->routeIs('operator.tracking.index') || request()->routeIs('operator.tracking.detail')) active @endif">
                            <i class="fas fa-satellite-dish"></i> Tracking User
                        </a>
                        <a href="{{ route('operator.tracking.live-map') }}" class="sidebar-item @if(request()->routeIs('operator.tracking.live-map')) active @endif">
                            <i class="fas fa-map-location-dot"></i> Live Map
                        </a>

                        <div class="sidebar-title">Akun</div>
                        <a href="{{ route('operator.akun') }}" class="sidebar-item @if(request()->routeIs('operator.akun*')) active @endif">
                            <i class="fas fa-user-pen"></i> Akun Saya
                        </a>
                        <a href="#" class="sidebar-item logout-trigger">
                            <i class="fas fa-arrow-right-from-bracket"></i> Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="topbar">
                <div class="topbar-title">@yield('title')</div>
                <div class="topbar-actions">
                    <button type="button" class="topbar-btn" onclick="toggleOperatorTheme()" title="Ubah tema">
                        <i class="fas fa-sun" id="op-sun"></i>
                        <i class="fas fa-moon" id="op-moon" style="display:none;"></i>
                    </button>
                    <div class="topbar-user">
                        @if(Auth::user()->foto_profil)
                        <img src="{{ asset('public/storage/foto_profil/' . Auth::user()->foto_profil) }}" class="user-avatar" alt="Avatar">
                        @else
                        <div class="user-avatar" style="background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700;">
                            {{ substr(Auth::user()->name,0,1) }}
                        </div>
                        @endif
                        <span class="topbar-user-name">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>

            <main class="main-content" style="padding:20px 24px;">
                @yield('content')
                <footer class="layout-footer">
                    <span><strong>KARISMA</strong> <span style="opacity:.75;">&middot; Operator Console &middot; {{ now()->format('Y') }}</span></span>
                    <span class="footer-chip"><i class="fas fa-shield-halved"></i> Operator Sistem</span>
                </footer>
            </main>
        </div>
    </div>

    <div id="operatorToast" style="position:fixed; top:20px; right:20px; z-index:9999; transform:translateX(calc(100% + 30px)); transition:transform 0.3s ease; max-width:380px; pointer-events:none;">
        <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; padding:14px 18px; box-shadow:0 8px 30px rgba(0,0,0,0.12); display:flex; align-items:center; gap:12px; pointer-events:auto;">
            <div id="opToastIcon" style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;"></div>
            <div style="flex:1;min-width:0;">
                <div id="opToastMsg" style="font-size:13px;font-weight:600;color:var(--dm-text,#1e293b);"></div>
                <div style="margin-top:6px;height:3px;border-radius:2px;background:var(--dm-border,#e2e8f0);overflow:hidden;">
                    <div id="opToastTimer" style="height:100%;border-radius:2px;width:100%;"></div>
                </div>
            </div>
            <button onclick="hideOpToast()" style="background:none;border:none;color:var(--dm-muted,#94a3b8);font-size:14px;cursor:pointer;padding:4px;flex-shrink:0;"><i class="fas fa-xmark"></i></button>
        </div>
    </div>

    <script>
        var _opToastTimer;
        function showAdminToast(msg, type) {
            var el=document.getElementById('operatorToast'), icon=document.getElementById('opToastIcon'),
                msgEl=document.getElementById('opToastMsg'), timer=document.getElementById('opToastTimer');
            msgEl.textContent=msg;
            var c = type==='success'?'#10b981':type==='error'?'#ef4444':'#5AB6EA';
            icon.style.background=c+'15'; icon.style.color=c;
            icon.innerHTML = type==='success'?'<i class="fas fa-check"></i>':type==='error'?'<i class="fas fa-xmark"></i>':'<i class="fas fa-info"></i>';
            timer.style.background=c; timer.style.width='100%'; timer.style.transitionDuration='0s';
            el.style.transform='translateX(0)';
            setTimeout(function(){timer.style.transitionDuration='3s';timer.style.width='0%';},50);
            if(_opToastTimer)clearTimeout(_opToastTimer);
            _opToastTimer=setTimeout(hideOpToast,3200);
        }
        function hideOpToast(){
            document.getElementById('operatorToast').style.transform='translateX(calc(100% + 30px))';
            if(_opToastTimer){clearTimeout(_opToastTimer);_opToastTimer=null;}
        }
        function showSuccess(msg){showAdminToast(msg,'success');}
        function showError(msg){showAdminToast(msg,'error');}

        @if(session('success'))
        document.addEventListener('DOMContentLoaded',function(){showAdminToast(@json(session('success')),'success');});
        @endif
        @if(session('error'))
        document.addEventListener('DOMContentLoaded',function(){showAdminToast(@json(session('error')),'error');});
        @endif
    </script>

    <button id="sidebarToggle" class="sm:hidden fixed bottom-4 right-4 bg-indigo-600 text-white p-3 rounded-full shadow-lg z-50">
        <i class="fas fa-bars"></i>
    </button>

    <div id="mobileSidebar" class="mobile-sidebar sm:hidden">
        <div class="mobile-sidebar-content">
            <div style="padding:20px 16px 16px; border-bottom:1px solid var(--dm-border,#e2e8f0);">
                <div style="display:flex; align-items:center; gap:10px;">
                    @if($appLogoUrl)
                        <img src="{{ $appLogoUrl }}" alt="Logo" style="width:32px; height:32px; border-radius:10px; object-fit:contain;">
                    @else
                        <div style="width:32px; height:32px; border-radius:10px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:14px;">K</div>
                    @endif
                    <div>
                        <div style="font-size:14px; font-weight:700; color:var(--dm-text,#1e293b);">KARISMA</div>
                        <div style="font-size:10px; color:var(--dm-muted,#94a3b8);">Operator Console</div>
                    </div>
                </div>
            </div>
            <div class="sidebar-scrollable">
                <div style="padding:4px 0;">
                    <div class="sidebar-title">Dashboard</div>
                    <a href="{{ route('operator.dashboard') }}" class="sidebar-item @if(request()->routeIs('operator.dashboard')) active @endif"><i class="fas fa-gauge-high"></i> Dashboard</a>

                    <div class="sidebar-title">Manajemen</div>
                    <a href="{{ route('admin.manajemenpegawai.index') }}" class="sidebar-item @if(request()->routeIs('admin.manajemenpegawai.*')) active @endif"><i class="fas fa-user-group"></i> Pegawai</a>
                    <a href="{{ route('admin.jamkerja.index') }}" class="sidebar-item @if(request()->routeIs('admin.jamkerja.index') || request()->routeIs('admin.jamkerja.shift.*')) active @endif"><i class="fas fa-clock"></i> Jam Kerja</a>
                    <a href="{{ route('admin.jamkerja.holidays') }}" class="sidebar-item @if(request()->routeIs('admin.jamkerja.holidays') || request()->routeIs('admin.jamkerja.holiday.*')) active @endif"><i class="fas fa-calendar-xmark"></i> Hari Libur</a>
                    <a href="{{ route('admin.lokasi.index') }}" class="sidebar-item @if(request()->routeIs('admin.lokasi.*')) active @endif"><i class="fas fa-location-dot"></i> Lokasi</a>
                    <a href="{{ route('operator.presensi.index') }}" class="sidebar-item @if(request()->routeIs('operator.presensi.*')) active @endif"><i class="fas fa-database"></i> Database Presensi</a>

                    <div class="sidebar-title">Tools</div>
                    <a href="{{ route('admin.laporan.index') }}" class="sidebar-item @if(request()->routeIs('admin.laporan.*')) active @endif"><i class="fas fa-chart-column"></i> Laporan</a>
                    <a href="{{ route('admin.performa.index') }}" class="sidebar-item @if(request()->routeIs('admin.performa.*')) active @endif"><i class="fas fa-ranking-star"></i> Performa</a>
                    <a href="{{ route('admin.pengumuman.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengumuman.*')) active @endif"><i class="fas fa-bullhorn"></i> Pengumuman</a>
                    @php $mdiCount = \App\Models\DeviceIssue::whereNull('resolved_at')->count(); @endphp
                    <a href="{{ route('admin.device-issues.index') }}" class="sidebar-item @if(request()->routeIs('admin.device-issues.*')) active @endif">
                        <i class="fas fa-mobile-screen-button"></i> Kendala Perangkat
                        @if($mdiCount > 0)<span style="margin-left:auto; background:#ef4444; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:10px;">{{ $mdiCount }}</span>@endif
                    </a>

                    <div class="sidebar-title">Sistem</div>
                    <a href="{{ route('operator.pengaturan.index') }}" class="sidebar-item @if(request()->routeIs('operator.pengaturan.*')) active @endif"><i class="fas fa-gear"></i> Pengaturan</a>
                    <a href="{{ route('operator.activity-logs.index') }}" class="sidebar-item @if(request()->routeIs('operator.activity-logs.*')) active @endif"><i class="fas fa-clock-rotate-left"></i> Log Aktivitas</a>
                    <a href="{{ route('operator.tracking.index') }}" class="sidebar-item @if(request()->routeIs('operator.tracking.index') || request()->routeIs('operator.tracking.detail')) active @endif"><i class="fas fa-satellite-dish"></i> Tracking</a>
                    <a href="{{ route('operator.tracking.live-map') }}" class="sidebar-item @if(request()->routeIs('operator.tracking.live-map')) active @endif"><i class="fas fa-map-location-dot"></i> Live Map</a>

                    <div class="sidebar-title">Akun</div>
                    <a href="{{ route('operator.akun') }}" class="sidebar-item @if(request()->routeIs('operator.akun*')) active @endif"><i class="fas fa-user-pen"></i> Akun Saya</a>
                    <a href="#" class="sidebar-item logout-trigger"><i class="fas fa-arrow-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        </div>
        <div class="mobile-sidebar-overlay" id="sidebarOverlay"></div>
    </div>

    <script>
        function toggleOperatorTheme(){
            var next=document.documentElement.getAttribute('data-theme')==='dark'?'light':'dark';
            document.documentElement.setAttribute('data-theme',next);
            localStorage.setItem('karisma-admin-theme',next);
            syncOpThemeIcons();
        }
        function syncOpThemeIcons(){
            var isDark=document.documentElement.getAttribute('data-theme')==='dark';
            var s=document.getElementById('op-sun'),m=document.getElementById('op-moon');
            if(s)s.style.display=isDark?'none':'inline';
            if(m)m.style.display=isDark?'inline':'none';
        }
        document.addEventListener('DOMContentLoaded',function(){
            syncOpThemeIcons();
            var sidebarToggle=document.getElementById('sidebarToggle');
            var mobileSidebar=document.getElementById('mobileSidebar');
            var sidebarOverlay=document.getElementById('sidebarOverlay');
            var logoutModal=document.getElementById('logoutModal');
            var logoutCancelBtn=document.getElementById('logoutCancelBtn');
            var logoutConfirmBtn=document.getElementById('logoutConfirmBtn');
            var logoutTriggers=document.querySelectorAll('.logout-trigger');
            var logoutForm=document.getElementById('logout-form');

            function openLogoutModal(){logoutModal.classList.add('active');document.body.style.overflow='hidden';closeMobileSidebar();}
            function closeLogoutModal(){logoutModal.classList.remove('active');document.body.style.overflow='';}
            function openMobileSidebar(){mobileSidebar.classList.add('open');document.body.style.overflow='hidden';}
            function closeMobileSidebar(){mobileSidebar.classList.remove('open');document.body.style.overflow='';}

            sidebarToggle.addEventListener('click',openMobileSidebar);
            sidebarOverlay.addEventListener('click',closeMobileSidebar);
            logoutTriggers.forEach(function(t){t.addEventListener('click',function(e){e.preventDefault();openLogoutModal();});});
            logoutCancelBtn.addEventListener('click',closeLogoutModal);
            logoutConfirmBtn.addEventListener('click',function(){logoutForm.submit();});
            logoutModal.addEventListener('click',function(e){if(e.target===logoutModal)closeLogoutModal();});
            document.querySelectorAll('#mobileSidebar a:not(.logout-trigger)').forEach(function(l){l.addEventListener('click',closeMobileSidebar);});
            document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeLogoutModal();closeMobileSidebar();}});
        });
    </script>

    <script>
        function showConfirm(opts){
            var old=document.getElementById('opConfirmOverlay');if(old)old.remove();
            var colors={warning:{bg:'rgba(245,158,11,0.1)',color:'#f59e0b',icon:'fa-exclamation-triangle',btn:'linear-gradient(135deg,#f59e0b,#d97706)'},danger:{bg:'rgba(239,68,68,0.1)',color:'#ef4444',icon:'fa-trash',btn:'linear-gradient(135deg,#ef4444,#dc2626)'},success:{bg:'rgba(16,185,129,0.1)',color:'#10b981',icon:'fa-check-circle',btn:'linear-gradient(135deg,#10b981,#059669)'},info:{bg:'rgba(59,130,246,0.1)',color:'#3b82f6',icon:'fa-circle-info',btn:'linear-gradient(135deg,#3b82f6,#2563eb)'}};
            var c=colors[opts.type||'warning']||colors.warning;
            var el=document.createElement('div');el.id='opConfirmOverlay';
            el.style.cssText='display:flex;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:16px;animation:acFadeIn 0.2s ease;';
            el.onclick=function(e){if(e.target===el)el.remove();};
            el.innerHTML='<div style="background:var(--dm-card,#fff);border-radius:16px;padding:24px;width:100%;max-width:360px;text-align:center;animation:acSlideUp 0.25s ease;border:1px solid var(--dm-border,#e2e8f0);">'+'<div style="width:52px;height:52px;border-radius:14px;background:'+c.bg+';color:'+c.color+';display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:22px;"><i class="fas '+(opts.icon||c.icon)+'"></i></div>'+'<div style="font-size:16px;font-weight:700;color:var(--dm-text,#1e293b);margin-bottom:6px;">'+(opts.title||'Konfirmasi')+'</div>'+'<div style="font-size:13px;color:var(--dm-muted,#64748b);margin-bottom:18px;line-height:1.5;">'+(opts.message||'Apakah Anda yakin?')+'</div>'+'<div style="display:flex;gap:10px;">'+'<button class="ac-cancel" style="flex:1;padding:11px;border-radius:10px;border:1px solid var(--dm-border,#e2e8f0);background:var(--dm-card,#fff);color:var(--dm-text,#1e293b);font-weight:600;font-size:13px;cursor:pointer;">'+(opts.cancelText||'Batal')+'</button>'+'<button class="ac-ok" style="flex:1;padding:11px;border-radius:10px;border:none;background:'+c.btn+';color:#fff;font-weight:600;font-size:13px;cursor:pointer;">'+(opts.confirmText||'Ya')+'</button>'+'</div></div>';
            document.body.appendChild(el);
            el.querySelector('.ac-cancel').onclick=function(){el.remove();if(opts.onCancel)opts.onCancel();};
            el.querySelector('.ac-ok').onclick=function(){el.remove();if(opts.onConfirm)opts.onConfirm();};
        }
    </script>
    <style>@keyframes acFadeIn{from{opacity:0}to{opacity:1}}@keyframes acSlideUp{from{opacity:0;transform:translateY(12px) scale(0.96)}to{opacity:1;transform:translateY(0) scale(1)}}</style>

    @stack('scripts')
</body>

</html>
