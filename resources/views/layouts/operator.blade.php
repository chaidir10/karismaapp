<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Operator | @yield('title')</title>

    <link rel="icon" type="image/png" sizes="48x48" href="{{ $appLogoUrl ?? asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ $appLogoUrl ?? asset('public/images/favicon-48x48.png') }}" type="image/png">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    @stack('styles')
    <style>
        * { font-family: 'Poppins', sans-serif; -webkit-text-size-adjust: 100%; }
        body { background: #f8fafc; color: #0f172a; }

        .main-container { display: flex; min-height: 100vh; }
        .sidebar {
            width: 240px; background: #0f172a; color: #e2e8f0;
            position: fixed; left: 0; top: 0; height: 100vh; z-index: 40; display: none;
            border-right: 1px solid rgba(255,255,255,0.08);
        }
        @media (min-width: 768px) {
            .sidebar { display: flex; flex-direction: column; }
        }
        .sidebar-scroll { flex: 1; overflow-y: auto; padding: 10px 0 16px; }
        .sidebar-title {
            font-size: 10px; text-transform: uppercase; letter-spacing: .8px; font-weight: 700;
            color: #94a3b8; padding: 0 16px; margin: 14px 0 6px;
        }
        .sidebar-item {
            display: flex; align-items: center; gap: 10px; margin: 3px 10px; padding: 10px 12px;
            border-radius: 10px; color: #cbd5e1; text-decoration: none; font-size: 13px; font-weight: 500;
            transition: all .15s;
        }
        .sidebar-item:hover { background: rgba(90,182,234,0.14); color: #fff; }
        .sidebar-item.active { background: linear-gradient(135deg, rgba(90,182,234,0.3), rgba(46,151,212,0.25)); color: #fff; font-weight: 700; }

        .content-area { flex: 1; margin-left: 0; display: flex; flex-direction: column; min-height: 100vh; }
        @media (min-width: 768px) { .content-area { margin-left: 240px; } }

        .topbar {
            height: 58px; padding: 0 18px; background: #fff; border-bottom: 1px solid #e2e8f0;
            position: sticky; top: 0; z-index: 30; display: flex; align-items: center; justify-content: space-between;
        }
        .topbar-title { font-size: 14px; font-weight: 700; color: #0f172a; }

        .main-content { flex: 1; padding: 18px; }
        @media (min-width: 768px) { .main-content { padding: 22px; } }

        .layout-footer {
            margin-top: 14px; padding: 10px 14px; border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: linear-gradient(135deg, rgba(90,182,234,0.08), rgba(46,151,212,0.04));
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            font-size: 11px; color: #64748b;
        }
        .layout-footer strong { color: #0f172a; font-weight: 700; }
        .footer-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 8px; border-radius: 999px; font-size: 10px; font-weight: 600;
            background: rgba(90,182,234,0.15); color: #1d4ed8;
        }

        .mobile-toggle {
            width: 36px; height: 36px; border-radius: 10px; border: 1px solid #e2e8f0;
            background: #fff; color: #334155;
        }
        .mobile-sidebar {
            position: fixed; inset: 0; z-index: 60; display: none;
        }
        .mobile-sidebar.open { display: block; }
        .mobile-sidebar-panel {
            width: 260px; height: 100%; background: #0f172a; color: #e2e8f0; padding: 10px 0 16px;
        }
        .mobile-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.45); }

        .logout-btn {
            width: 100%; text-align: left; border: none; background: transparent; color: inherit;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <aside class="sidebar">
            <div style="height:58px; display:flex; align-items:center; padding:0 14px; border-bottom:1px solid rgba(255,255,255,0.08);">
                <div style="display:flex; align-items:center; gap:10px;">
                    @if($appLogoUrl ?? false)
                        <img src="{{ $appLogoUrl }}" alt="Logo" style="width:34px; height:34px; border-radius:10px; object-fit:contain;">
                    @else
                        <div style="width:34px; height:34px; border-radius:10px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800;">K</div>
                    @endif
                    <div>
                        <div style="font-size:13px; font-weight:800;">KARISMA</div>
                        <div style="font-size:10px; color:#94a3b8;">Operator Console</div>
                    </div>
                </div>
            </div>

            <div class="sidebar-scroll">
                <div class="sidebar-title">Utama</div>
                <a href="{{ route('operator.dashboard') }}" class="sidebar-item @if(request()->routeIs('operator.dashboard')) active @endif">
                    <i class="fas fa-gauge-high"></i> Dashboard Operator
                </a>

                <div class="sidebar-title">Tools Sistem</div>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item"><i class="fas fa-chart-line"></i> Monitor Admin Panel</a>
                <a href="{{ route('admin.manajemenpegawai.index') }}" class="sidebar-item"><i class="fas fa-users-cog"></i> Manajemen Pegawai</a>
                <a href="{{ route('admin.lokasi.index') }}" class="sidebar-item"><i class="fas fa-location-dot"></i> Lokasi & Radius</a>
                <a href="{{ route('admin.jamkerja.index') }}" class="sidebar-item"><i class="fas fa-business-time"></i> Jam Kerja & Shift</a>
                <a href="{{ route('admin.jamkerja.holidays') }}" class="sidebar-item"><i class="fas fa-calendar-days"></i> Hari Libur</a>
                <a href="{{ route('admin.pengumuman.index') }}" class="sidebar-item"><i class="fas fa-bullhorn"></i> Pengumuman</a>
                <a href="{{ route('admin.device-issues.index') }}" class="sidebar-item"><i class="fas fa-screwdriver-wrench"></i> Device Issues</a>
                <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-item"><i class="fas fa-sliders"></i> Pengaturan App</a>

                <div class="sidebar-title">Akun</div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0 10px;">
                    @csrf
                    <button type="submit" class="sidebar-item logout-btn"><i class="fas fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </aside>

        <div class="content-area">
            <header class="topbar">
                <div style="display:flex; align-items:center; gap:10px;">
                    <button class="mobile-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>
                    <div class="topbar-title">@yield('title')</div>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="font-size:12px; color:#64748b; font-weight:600;">{{ Auth::user()->name }}</div>
                </div>
            </header>

            <main class="main-content">
                @yield('content')

                <footer class="layout-footer">
                    <span><strong>KARISMA</strong> <span style="opacity:.75;">&middot; Operator Aplikasi & IT Governance &middot; {{ now()->format('Y') }}</span></span>
                    <span class="footer-chip"><i class="fas fa-shield-halved"></i> Platform Reliability</span>
                </footer>
            </main>
        </div>
    </div>

    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-overlay" id="mobileOverlay"></div>
        <div class="mobile-sidebar-panel">
            <div style="height:58px; display:flex; align-items:center; justify-content:space-between; padding:0 14px; border-bottom:1px solid rgba(255,255,255,0.08);">
                <strong style="font-size:13px;">Menu Operator</strong>
                <button id="mobileClose" style="background:none; border:none; color:#cbd5e1;"><i class="fas fa-xmark"></i></button>
            </div>
            <div style="padding:10px;">
                <a href="{{ route('operator.dashboard') }}" class="sidebar-item @if(request()->routeIs('operator.dashboard')) active @endif"><i class="fas fa-gauge-high"></i> Dashboard</a>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item"><i class="fas fa-chart-line"></i> Monitor Admin</a>
                <a href="{{ route('admin.manajemenpegawai.index') }}" class="sidebar-item"><i class="fas fa-users-cog"></i> Pegawai</a>
                <a href="{{ route('admin.lokasi.index') }}" class="sidebar-item"><i class="fas fa-location-dot"></i> Lokasi</a>
                <a href="{{ route('admin.jamkerja.index') }}" class="sidebar-item"><i class="fas fa-business-time"></i> Jam Kerja</a>
                <a href="{{ route('admin.pengumuman.index') }}" class="sidebar-item"><i class="fas fa-bullhorn"></i> Pengumuman</a>
                <a href="{{ route('admin.device-issues.index') }}" class="sidebar-item"><i class="fas fa-screwdriver-wrench"></i> Device Issues</a>
                <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-item"><i class="fas fa-sliders"></i> Pengaturan</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-item logout-btn"><i class="fas fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var mobileToggle = document.getElementById('mobileToggle');
            var mobileSidebar = document.getElementById('mobileSidebar');
            var mobileClose = document.getElementById('mobileClose');
            var mobileOverlay = document.getElementById('mobileOverlay');

            function openSidebar() { mobileSidebar.classList.add('open'); }
            function closeSidebar() { mobileSidebar.classList.remove('open'); }

            if (mobileToggle) mobileToggle.addEventListener('click', openSidebar);
            if (mobileClose) mobileClose.addEventListener('click', closeSidebar);
            if (mobileOverlay) mobileOverlay.addEventListener('click', closeSidebar);
        })();
    </script>

    @stack('scripts')
</body>
</html>
