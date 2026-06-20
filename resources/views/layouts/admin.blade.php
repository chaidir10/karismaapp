<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon-48x48.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-cache-control" content="no-preview">
    <title>Admin | @yield('title')</title>

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script>
        (function(){ var t = localStorage.getItem('karisma-admin-theme') || 'light'; document.documentElement.setAttribute('data-theme', t); })();
    </script>

    @stack('head')
    @stack('styles')
    <style>
        :root, [data-theme="light"] {
            --primary: #5AB6EA;
            --primary-light: #87CEEB;
            --primary-dark: #2E97D4;
            --primary-soft: #E6F4F9;
            --accent: #FEAA2B;
            --accent-light: #FFE4BC;
            --light: #f8fafc;
            --gray-light: #f1f5f9;
            --gray: #94a3b8;
            --gray-dark: #64748b;
            --dark: #1e293b;
            --white: #ffffff;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --body-bg: #f0f6fb;
            --card-bg: #ffffff;
            --card-border: #e5e7eb;
            --text-primary: #1e293b;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --input-bg: #ffffff;
            --input-border: #d1d5db;
            --sidebar-bg: #ffffff;
            --topbar-bg: #ffffff;
        }

        [data-theme="dark"] {
            --primary-light: #4a9bc7;
            --primary-soft: #1a2e3d;
            --accent-light: #4a3a1a;
            --light: #1e293b;
            --gray-light: #334155;
            --gray: #94a3b8;
            --gray-dark: #cbd5e1;
            --dark: #f1f5f9;
            --white: #0f172a;
            --danger: #f87171;
            --danger-light: #4a1c1c;
            --success: #34d399;
            --success-light: #064e3b;
            --warning: #fbbf24;
            --warning-light: #78350f;
            --body-bg: #0c1322;
            --card-bg: #1e293b;
            --card-border: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --input-bg: #1e293b;
            --input-border: #475569;
            --sidebar-bg: #0f172a;
            --topbar-bg: #1e293b;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-primary);
            font-size: 14px;
            -webkit-text-size-adjust: 100%;
            transition: background-color 0.2s, color 0.2s;
        }

        /* Layout */
        .main-container { display:flex; min-height:100vh; }

        /* Sidebar */
        .sidebar {
            width:250px; background:var(--sidebar-bg); border-right:1px solid var(--card-border);
            position:fixed; left:0; top:0; height:100vh; z-index:40;
            display:none; transition: background-color 0.2s;
        }
        @media (min-width:640px) { .sidebar { display:flex; flex-direction:column; } }

        .sidebar-content { display:flex; flex-direction:column; height:100%; }
        .sidebar-scrollable { flex:1; overflow-y:auto; padding-bottom:1rem; }

        .sidebar-logo {
            padding:20px; border-bottom:1px solid var(--card-border);
            display:flex; align-items:center; gap:12px;
        }
        .sidebar-logo-icon {
            width:36px; height:36px; border-radius:10px;
            background:linear-gradient(135deg, var(--primary), var(--primary-dark));
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:16px;
        }
        .sidebar-logo-text { font-size:16px; font-weight:700; color:var(--dark); }

        .sidebar-item {
            display:flex; align-items:center; padding:10px 16px; margin:2px 10px;
            color:var(--text-muted); text-decoration:none; border-radius:10px;
            font-size:13px; font-weight:500; transition:all 0.15s;
            -webkit-tap-highlight-color:transparent;
        }
        .sidebar-item:hover { background:var(--primary-soft); color:var(--primary-dark); }
        .sidebar-item.active { background:var(--primary-soft); color:var(--primary-dark); font-weight:600; }
        .sidebar-item i { width:20px; margin-right:10px; font-size:15px; text-align:center; }

        .sidebar-title {
            font-size:11px; font-weight:700; padding:0 16px; margin:20px 0 6px;
            color:var(--gray); text-transform:uppercase; letter-spacing:0.5px;
        }

        /* Content area */
        .content-area { flex:1; margin-left:0; display:flex; flex-direction:column; min-height:100vh; }
        @media (min-width:640px) { .content-area { margin-left:250px; } }

        /* Topbar */
        .topbar {
            background:var(--topbar-bg); border-bottom:1px solid var(--card-border);
            padding:12px 24px; position:sticky; top:0; z-index:30;
            display:flex; justify-content:space-between; align-items:center;
            transition: background-color 0.2s;
        }
        .topbar-title { font-size:15px; font-weight:600; color:var(--dark); }

        .topbar-right { display:flex; align-items:center; gap:12px; }
        .topbar-user { font-size:13px; color:var(--text-muted); font-weight:500; }
        .topbar-avatar {
            width:34px; height:34px; border-radius:50%; object-fit:cover;
            border:2px solid var(--card-border);
        }
        .topbar-avatar-fallback {
            width:34px; height:34px; border-radius:50%;
            background:linear-gradient(135deg, var(--primary), var(--primary-dark));
            color:#fff; display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700;
        }
        .topbar-btn {
            width:34px; height:34px; border-radius:10px; border:1px solid var(--card-border);
            background:var(--card-bg); color:var(--text-muted); font-size:14px;
            display:flex; align-items:center; justify-content:center; cursor:pointer;
            -webkit-tap-highlight-color:transparent;
        }
        .topbar-btn:active { opacity:0.8; }

        /* Main content */
        .main-content { flex:1; background:var(--body-bg); transition: background-color 0.2s; }

        /* Turbo progress bar */
        .turbo-progress-bar { background:linear-gradient(90deg, var(--primary), var(--accent)) !important; height:3px !important; }

        /* Page transition */
        .page-transition { animation:pageFade 0.35s ease-out; }
        @keyframes pageFade { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }

        /* Logout Modal */
        .logout-modal {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
            z-index:1100; align-items:center; justify-content:center;
        }
        .logout-modal.active { display:flex; }
        .logout-modal-content {
            background:var(--card-bg); border-radius:16px; padding:28px;
            max-width:380px; width:90%; text-align:center;
            animation:modalUp 0.3s ease-out; border:1px solid var(--card-border);
        }
        @keyframes modalUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }

        .logout-icon-box {
            width:56px; height:56px; border-radius:14px; background:var(--danger-light);
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 16px; font-size:22px; color:var(--danger);
        }
        .logout-modal h3 { font-size:17px; font-weight:700; color:var(--dark); margin-bottom:6px; }
        .logout-modal p { color:var(--text-muted); margin-bottom:20px; font-size:13px; }
        .logout-modal-buttons { display:flex; gap:10px; }
        .logout-btn-cancel {
            flex:1; padding:12px; border-radius:12px; border:1px solid var(--card-border);
            background:var(--card-bg); color:var(--dark); font-weight:600; font-size:14px; cursor:pointer;
        }
        .logout-btn-cancel:active { opacity:0.85; }
        .logout-btn-confirm {
            flex:1; padding:12px; border-radius:12px; border:none;
            background:var(--danger); color:#fff; font-weight:600; font-size:14px; cursor:pointer;
        }
        .logout-btn-confirm:active { opacity:0.85; }

        /* Mobile sidebar */
        .mobile-sidebar {
            position:fixed; inset:0; z-index:50;
            transform:translateX(-100%); transition:transform 0.3s ease-in-out;
        }
        .mobile-sidebar.open { transform:translateX(0); }
        .mobile-sidebar-content {
            width:250px; height:100%; background:var(--sidebar-bg);
            border-right:1px solid var(--card-border);
            display:flex; flex-direction:column;
        }
        .mobile-sidebar-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:40; }

        /* Mobile toggle */
        .mobile-fab {
            background:linear-gradient(135deg, var(--primary), var(--primary-dark));
            color:#fff; border:none; padding:14px; border-radius:14px;
            box-shadow:0 4px 16px rgba(0,0,0,0.15); cursor:pointer;
        }

        /* Mobile responsive */
        @media (max-width:640px) {
            .table-responsive { display:block; width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
        }

        /* Dark mode overrides for Tailwind classes */
        [data-theme="dark"] .bg-white { background-color:var(--card-bg) !important; }
        [data-theme="dark"] .bg-gray-50, [data-theme="dark"] .bg-gray-100 { background-color:var(--light) !important; }
        [data-theme="dark"] .bg-green-50 { background-color:#064e3b !important; }
        [data-theme="dark"] .bg-red-50, [data-theme="dark"] .bg-red-100 { background-color:#4a1c1c !important; }
        [data-theme="dark"] .bg-yellow-50 { background-color:#78350f !important; }
        [data-theme="dark"] .bg-blue-50, [data-theme="dark"] .bg-blue-100 { background-color:#1e3a5f !important; }
        [data-theme="dark"] .bg-amber-50 { background-color:#78350f !important; }
        [data-theme="dark"] .bg-indigo-50 { background-color:#1e1b4b !important; }

        [data-theme="dark"] .text-gray-800, [data-theme="dark"] .text-gray-900 { color:var(--text-primary) !important; }
        [data-theme="dark"] .text-gray-700 { color:var(--text-secondary) !important; }
        [data-theme="dark"] .text-gray-600, [data-theme="dark"] .text-gray-500, [data-theme="dark"] .text-gray-400 { color:var(--text-muted) !important; }
        [data-theme="dark"] .text-green-700, [data-theme="dark"] .text-green-600 { color:#6ee7b7 !important; }
        [data-theme="dark"] .text-red-500, [data-theme="dark"] .text-red-600, [data-theme="dark"] .text-red-700 { color:#fca5a5 !important; }
        [data-theme="dark"] .text-yellow-500, [data-theme="dark"] .text-yellow-700 { color:#fcd34d !important; }
        [data-theme="dark"] .text-blue-600, [data-theme="dark"] .text-blue-700 { color:#93c5fd !important; }
        [data-theme="dark"] .text-amber-800 { color:#fcd34d !important; }
        [data-theme="dark"] .text-indigo-100 { color:var(--text-muted) !important; }

        [data-theme="dark"] .border-gray-100, [data-theme="dark"] .border-gray-200, [data-theme="dark"] .border-gray-300 { border-color:var(--card-border) !important; }
        [data-theme="dark"] .border-green-200 { border-color:#065f46 !important; }
        [data-theme="dark"] .border-red-200 { border-color:#7f1d1d !important; }
        [data-theme="dark"] .border-amber-200 { border-color:#78350f !important; }

        [data-theme="dark"] .divide-gray-200 > * + * { border-color:var(--card-border) !important; }
        [data-theme="dark"] .shadow-xl, [data-theme="dark"] .shadow-lg, [data-theme="dark"] .shadow-md, [data-theme="dark"] .shadow-sm { box-shadow:0 4px 15px rgba(0,0,0,0.3) !important; }
        [data-theme="dark"] .hover\:bg-gray-50:hover { background-color:var(--gray-light) !important; }

        [data-theme="dark"] input, [data-theme="dark"] textarea, [data-theme="dark"] select {
            background-color:var(--input-bg) !important; border-color:var(--input-border) !important; color:var(--text-primary) !important;
        }
        [data-theme="dark"] .modal-content { background-color:var(--card-bg) !important; color:var(--text-primary) !important; }
        [data-theme="dark"] .rounded-xl, [data-theme="dark"] .rounded-2xl { border-color:var(--card-border); }
    </style>
</head>

<body>
    <!-- Logout Modal -->
    <div class="logout-modal" id="logoutModal">
        <div class="logout-modal-content">
            <div class="logout-icon-box">
                <i class="fas fa-arrow-right-from-bracket"></i>
            </div>
            <h3>Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin keluar dari sistem?</p>
            <div class="logout-modal-buttons">
                <button type="button" class="logout-btn-cancel" id="logoutCancelBtn">Batal</button>
                <button type="button" class="logout-btn-confirm" id="logoutConfirmBtn">Ya, Logout</button>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- Desktop Sidebar -->
        <div class="sidebar">
            <div class="sidebar-content">
                <div class="sidebar-logo">
                    <div class="sidebar-logo-icon"><i class="fas fa-shield-halved"></i></div>
                    <span class="sidebar-logo-text">Admin Panel</span>
                </div>
                <div class="sidebar-scrollable">
                    <div class="p-1">
                        <div class="sidebar-title">Menu Utama</div>
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-item @if(request()->routeIs('admin.dashboard')) active @endif">
                            <i class="fas fa-house"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.manajemenpegawai.index') }}" class="sidebar-item @if(request()->routeIs('admin.manajemenpegawai.*')) active @endif">
                            <i class="fas fa-user-group"></i> Pegawai
                        </a>
                        <a href="{{ route('admin.jamkerja.index') }}" class="sidebar-item @if(request()->routeIs('admin.jamkerja.*')) active @endif">
                            <i class="fas fa-clock"></i> Jam Kerja
                        </a>
                        <a href="{{ route('admin.lokasi.index') }}" class="sidebar-item @if(request()->routeIs('admin.lokasi.*')) active @endif">
                            <i class="fas fa-location-dot"></i> Lokasi
                        </a>
                        <a href="{{ route('admin.laporan.index') }}" class="sidebar-item @if(request()->routeIs('admin.laporan.*')) active @endif">
                            <i class="fas fa-chart-column"></i> Laporan
                        </a>
                        <a href="{{ route('admin.performa.index') }}" class="sidebar-item @if(request()->routeIs('admin.performa.*')) active @endif">
                            <i class="fas fa-trophy"></i> Performa
                        </a>
                        <a href="{{ route('admin.pengumuman.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengumuman.*')) active @endif">
                            <i class="fas fa-bullhorn"></i> Pengumuman
                        </a>

                        <div class="sidebar-title mt-4">Pengaturan</div>
                        <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengaturan.*')) active @endif">
                            <i class="fas fa-gear"></i> Pengaturan
                        </a>
                        <a href="#" class="sidebar-item logout-trigger">
                            <i class="fas fa-arrow-right-from-bracket"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Topbar -->
            <div class="topbar">
                <span class="topbar-title">@yield('title')</span>
                <div class="topbar-right">
                    <button type="button" class="topbar-btn" onclick="toggleAdminTheme()" title="Ubah tema">
                        <i class="fas fa-sun" id="admin-theme-sun"></i>
                        <i class="fas fa-moon" id="admin-theme-moon" style="display:none;"></i>
                    </button>
                    <span class="topbar-user hidden sm:inline">{{ Auth::user()->name }}</span>
                    @if(Auth::user()->foto_profil)
                    <img src="{{ asset('public/storage/foto_profil/' . Auth::user()->foto_profil) }}" class="topbar-avatar" alt="Avatar">
                    @else
                    <div class="topbar-avatar-fallback">{{ substr(Auth::user()->name,0,1) }}</div>
                    @endif
                </div>
            </div>

            <!-- Main Content -->
            <main class="main-content p-3 sm:p-5 page-transition">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar toggle -->
    <button id="sidebarToggle" class="sm:hidden fixed bottom-4 right-4 mobile-fab z-50">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Mobile Sidebar -->
    <div id="mobileSidebar" class="mobile-sidebar sm:hidden">
        <div class="mobile-sidebar-content">
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon"><i class="fas fa-shield-halved"></i></div>
                <span class="sidebar-logo-text">Admin Panel</span>
            </div>
            <div class="sidebar-scrollable">
                <div class="p-1">
                    <div class="sidebar-title">Menu Utama</div>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item @if(request()->routeIs('admin.dashboard')) active @endif">
                        <i class="fas fa-house"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.manajemenpegawai.index') }}" class="sidebar-item @if(request()->routeIs('admin.manajemenpegawai.*')) active @endif">
                        <i class="fas fa-user-group"></i> Pegawai
                    </a>
                    <a href="{{ route('admin.jamkerja.index') }}" class="sidebar-item @if(request()->routeIs('admin.jamkerja.*')) active @endif">
                        <i class="fas fa-clock"></i> Jam Kerja
                    </a>
                    <a href="{{ route('admin.lokasi.index') }}" class="sidebar-item @if(request()->routeIs('admin.lokasi.*')) active @endif">
                        <i class="fas fa-location-dot"></i> Lokasi
                    </a>
                    <a href="{{ route('admin.laporan.index') }}" class="sidebar-item @if(request()->routeIs('admin.laporan.*')) active @endif">
                        <i class="fas fa-chart-column"></i> Laporan
                    </a>
                    <a href="{{ route('admin.performa.index') }}" class="sidebar-item @if(request()->routeIs('admin.performa.*')) active @endif">
                        <i class="fas fa-trophy"></i> Performa
                    </a>
                    <a href="{{ route('admin.pengumuman.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengumuman.*')) active @endif">
                        <i class="fas fa-bullhorn"></i> Pengumuman
                    </a>
                    <div class="sidebar-title mt-4">Pengaturan</div>
                    <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengaturan.*')) active @endif">
                        <i class="fas fa-gear"></i> Pengaturan
                    </a>
                    <a href="#" class="sidebar-item logout-trigger">
                        <i class="fas fa-arrow-right-from-bracket"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        <div class="mobile-sidebar-overlay" id="sidebarOverlay"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8/dist/turbo.es2017-esm.js"></script>
    <script>
        // Theme toggle
        function toggleAdminTheme() {
            var html = document.documentElement;
            var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('karisma-admin-theme', next);
            syncThemeIcons();
        }
        function syncThemeIcons() {
            var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            var sun = document.getElementById('admin-theme-sun');
            var moon = document.getElementById('admin-theme-moon');
            if (sun) sun.style.display = isDark ? 'none' : 'inline';
            if (moon) moon.style.display = isDark ? 'inline' : 'none';
        }

        function initAdmin() {
            syncThemeIcons();

            var sidebarToggle = document.getElementById('sidebarToggle');
            var mobileSidebar = document.getElementById('mobileSidebar');
            var sidebarOverlay = document.getElementById('sidebarOverlay');
            var logoutModal = document.getElementById('logoutModal');
            var logoutCancelBtn = document.getElementById('logoutCancelBtn');
            var logoutConfirmBtn = document.getElementById('logoutConfirmBtn');
            var logoutTriggers = document.querySelectorAll('.logout-trigger');
            var logoutForm = document.getElementById('logout-form');

            function openLogout() { logoutModal.classList.add('active'); document.body.style.overflow='hidden'; closeMobile(); }
            function closeLogout() { logoutModal.classList.remove('active'); document.body.style.overflow=''; }
            function openMobile() { mobileSidebar.classList.add('open'); document.body.style.overflow='hidden'; }
            function closeMobile() { mobileSidebar.classList.remove('open'); document.body.style.overflow=''; }

            if (sidebarToggle) sidebarToggle.addEventListener('click', openMobile);
            if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMobile);

            logoutTriggers.forEach(function(t) { t.addEventListener('click', function(e) { e.preventDefault(); openLogout(); }); });
            if (logoutCancelBtn) logoutCancelBtn.addEventListener('click', closeLogout);
            if (logoutConfirmBtn) logoutConfirmBtn.addEventListener('click', function() { logoutForm.submit(); });
            if (logoutModal) logoutModal.addEventListener('click', function(e) { if (e.target === logoutModal) closeLogout(); });

            document.querySelectorAll('#mobileSidebar a:not(.logout-trigger)').forEach(function(link) {
                link.addEventListener('click', closeMobile);
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') { closeLogout(); closeMobile(); }
            });
        }

        document.addEventListener('turbo:load', initAdmin);
        document.addEventListener('DOMContentLoaded', initAdmin);
    </script>

    @stack('scripts')
</body>

</html>
