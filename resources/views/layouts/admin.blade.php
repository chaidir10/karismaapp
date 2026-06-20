<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon-48x48.png') }}" type="image/png">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <title>Admin | @yield('title')</title>
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
            --dm-bg: #0f172a;
            --dm-card: #1e293b;
            --dm-sidebar: #0f172a;
            --dm-topbar: #1e293b;
            --dm-border: #334155;
            --dm-text: #f1f5f9;
            --dm-text2: #cbd5e1;
            --dm-muted: #94a3b8;
            --dm-input: #1e293b;
            --dm-input-border: #475569;
        }

        body {
            font-family: 'Poppins', sans-serif;
            -webkit-text-size-adjust: 100%;
        }

        /* Mobile optimizations */
        @media (max-width: 640px) {
            .mobile-tooltip {
                position: relative;
                display: inline-block;
            }

            .mobile-tooltip:active:after {
                content: attr(data-tooltip);
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                bottom: 100%;
                background: #333;
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 100;
                margin-bottom: 5px;
            }

            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .mobile-stack {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .mobile-text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }

            .mobile-p-2 {
                padding: 0.5rem;
            }
        }

        /* Layout utama */
        .main-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar styling - FIXED */
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 40;
            display: none; /* Default hidden untuk mobile */
        }

        /* Sidebar untuk desktop */
        @media (min-width: 640px) {
            .sidebar {
                display: flex;
                flex-direction: column;
            }
        }

        .sidebar-content {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow-y: auto; /* Scroll sendiri untuk sidebar */
        }

        .sidebar-scrollable {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 1rem;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
            border-radius: 8px;
            margin: 4px 10px;
            cursor: pointer;
        }

        .sidebar-item:hover,
        .sidebar-item.active {
            background-color: rgba(79, 70, 229, 0.1);
            color: #2099dfff;
        }

        .sidebar-item i {
            width: 20px;
            margin-right: 12px;
        }

        .sidebar-title {
            font-size: 14px;
            font-weight: 600;
            padding: 0 20px;
            margin: 20px 0 8px 0;
            color: #6b7280;
            text-transform: uppercase;
        }

        /* Content area */
        .content-area {
            flex: 1;
            margin-left: 0; /* Default untuk mobile */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Untuk desktop, beri margin kiri sesuai lebar sidebar */
        @media (min-width: 640px) {
            .content-area {
                margin-left: 250px;
            }
        }

        /* Topbar styling */
        .topbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Main content area */
        .main-content {
            flex: 1;
            overflow-y: auto; /* Scroll sendiri untuk konten */
            background: #f9fafb;
        }

        /* Modal Konfirmasi Logout */
        .logout-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1100;
            align-items: center;
            justify-content: center;
        }

        .logout-modal.active {
            display: flex;
        }

        .logout-modal-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            text-align: center;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .logout-icon {
            font-size: 3rem;
            color: #ef4444;
            margin-bottom: 1rem;
        }

        .logout-modal h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .logout-modal p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }

        .logout-modal-buttons {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .logout-btn-cancel {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn-cancel:hover {
            background: #e5e7eb;
        }

        .logout-btn-confirm {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
        }

        .logout-btn-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(239, 68, 68, 0.4);
        }

        /* Mobile sidebar styles */
        .mobile-sidebar {
            position: fixed;
            inset: 0;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-sidebar.open {
            transform: translateX(0);
        }

        .mobile-sidebar-content {
            width: 250px;
            height: 100%;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .mobile-sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }
        /* ─── Dark Mode ─── */
        [data-theme="dark"] body { background-color: var(--dm-bg) !important; color: var(--dm-text) !important; }
        [data-theme="dark"] .sidebar { background: var(--dm-sidebar) !important; border-color: var(--dm-border) !important; }
        [data-theme="dark"] .sidebar-item { color: var(--dm-muted) !important; }
        [data-theme="dark"] .sidebar-item:hover, [data-theme="dark"] .sidebar-item.active { background-color: rgba(90,182,234,0.1) !important; color: #5AB6EA !important; }
        [data-theme="dark"] .sidebar-title { color: var(--dm-muted) !important; }
        [data-theme="dark"] .topbar { background: var(--dm-topbar) !important; box-shadow: none !important; border-bottom: 1px solid var(--dm-border) !important; }
        [data-theme="dark"] .main-content { background: var(--dm-bg) !important; }
        [data-theme="dark"] .mobile-sidebar-content { background: var(--dm-sidebar) !important; border-color: var(--dm-border) !important; }
        [data-theme="dark"] .logout-modal-content { background: var(--dm-card) !important; color: var(--dm-text) !important; }
        [data-theme="dark"] .logout-modal h3 { color: var(--dm-text) !important; }
        [data-theme="dark"] .logout-modal p { color: var(--dm-muted) !important; }
        [data-theme="dark"] .logout-btn-cancel { background: var(--dm-border) !important; color: var(--dm-text) !important; }

        [data-theme="dark"] .bg-white { background-color: var(--dm-card) !important; }
        [data-theme="dark"] .bg-gray-50, [data-theme="dark"] .bg-gray-100 { background-color: #1e293b !important; }
        [data-theme="dark"] .bg-green-50 { background-color: #064e3b !important; }
        [data-theme="dark"] .bg-red-50, [data-theme="dark"] .bg-red-100 { background-color: #4a1c1c !important; }
        [data-theme="dark"] .bg-yellow-50 { background-color: #78350f !important; }
        [data-theme="dark"] .bg-blue-50, [data-theme="dark"] .bg-blue-100 { background-color: #1e3a5f !important; }

        [data-theme="dark"] .text-gray-800, [data-theme="dark"] .text-gray-900 { color: var(--dm-text) !important; }
        [data-theme="dark"] .text-gray-700 { color: var(--dm-text2) !important; }
        [data-theme="dark"] .text-gray-600, [data-theme="dark"] .text-gray-500, [data-theme="dark"] .text-gray-400 { color: var(--dm-muted) !important; }
        [data-theme="dark"] .text-green-700 { color: #6ee7b7 !important; }
        [data-theme="dark"] .text-red-500, [data-theme="dark"] .text-red-700 { color: #fca5a5 !important; }
        [data-theme="dark"] .text-blue-600 { color: #93c5fd !important; }

        [data-theme="dark"] .border-gray-100, [data-theme="dark"] .border-gray-200, [data-theme="dark"] .border-gray-300 { border-color: var(--dm-border) !important; }
        [data-theme="dark"] .border-green-200 { border-color: #065f46 !important; }
        [data-theme="dark"] .border-red-200 { border-color: #7f1d1d !important; }
        [data-theme="dark"] .divide-gray-200 > * + * { border-color: var(--dm-border) !important; }
        [data-theme="dark"] .shadow-xl, [data-theme="dark"] .shadow-lg, [data-theme="dark"] .shadow-md, [data-theme="dark"] .shadow-sm { box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important; }
        [data-theme="dark"] .hover\:bg-gray-50:hover { background-color: #334155 !important; }

        [data-theme="dark"] input, [data-theme="dark"] textarea, [data-theme="dark"] select {
            background-color: var(--dm-input) !important; border-color: var(--dm-input-border) !important; color: var(--dm-text) !important;
        }
        [data-theme="dark"] .modal-content { background-color: var(--dm-card) !important; color: var(--dm-text) !important; }
        [data-theme="dark"] .modal-overlay .modal-container { background-color: var(--dm-card) !important; }

        [data-theme="dark"] .data-table th { color: var(--dm-muted) !important; background: #1e293b !important; border-color: var(--dm-border) !important; }
        [data-theme="dark"] .data-table td { color: var(--dm-text) !important; border-color: var(--dm-border) !important; }
        [data-theme="dark"] .data-table tbody tr:hover { background: #334155 !important; }
        [data-theme="dark"] .user-name { color: var(--dm-text) !important; }
        [data-theme="dark"] .time-cell { color: var(--dm-text) !important; }
        [data-theme="dark"] .date-cell { color: var(--dm-muted) !important; }
        [data-theme="dark"] .card-title { color: var(--dm-text) !important; }
        [data-theme="dark"] .card-badge { background: #334155 !important; color: var(--dm-muted) !important; }
        [data-theme="dark"] .stat-card { background: var(--dm-card) !important; border-color: var(--dm-border) !important; }
        [data-theme="dark"] .stat-value { color: var(--dm-text) !important; }
        [data-theme="dark"] .stat-label { color: var(--dm-muted) !important; }
        [data-theme="dark"] .content-card { background: var(--dm-card) !important; border-color: var(--dm-border) !important; }
        [data-theme="dark"] .card-header { border-color: var(--dm-border) !important; }
        [data-theme="dark"] .table-pagination { border-color: var(--dm-border) !important; color: var(--dm-muted) !important; }
        [data-theme="dark"] .pagination-buttons button { background: var(--dm-card) !important; border-color: var(--dm-border) !important; color: var(--dm-muted) !important; }
        [data-theme="dark"] .pagination-buttons button:hover:not(:disabled) { background: #334155 !important; }
        [data-theme="dark"] .detail-grid label { color: var(--dm-muted) !important; }
        [data-theme="dark"] .detail-grid span { color: var(--dm-text) !important; }
        [data-theme="dark"] .text-muted { color: var(--dm-muted) !important; }
        [data-theme="dark"] .modal-title { color: var(--dm-text) !important; }
        [data-theme="dark"] .map-container { border-color: var(--dm-border) !important; background: #1e293b !important; }
        [data-theme="dark"] .foto-wrapper { background: #1e293b !important; border-color: var(--dm-border) !important; }
        [data-theme="dark"] .rounded-xl, [data-theme="dark"] .rounded-2xl { border-color: var(--dm-border) !important; }
        [data-theme="dark"] .border { border-color: var(--dm-border) !important; }
        [data-theme="dark"] .hidden.fixed { background-color: rgba(0,0,0,0.7) !important; }
        [data-theme="dark"] .hidden.fixed > div { background-color: var(--dm-card) !important; }

        .admin-theme-btn {
            width: 34px; height: 34px; border-radius: 8px; border: 1px solid #e5e7eb;
            background: transparent; color: #6b7280; cursor: pointer;
            display: flex; align-items: center; justify-content: center; font-size: 14px;
        }
        [data-theme="dark"] .admin-theme-btn { border-color: var(--dm-border); color: var(--dm-muted); }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 text-sm">
    <!-- Modal Konfirmasi Logout -->
    <div class="logout-modal" id="logoutModal">
        <div class="logout-modal-content">
            <div class="logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3>Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin keluar dari sistem? Pastikan semua pekerjaan Anda sudah disimpan.</p>
            <div class="logout-modal-buttons">
                <button type="button" class="logout-btn-cancel" id="logoutCancelBtn">
                    Batal
                </button>
                <button type="button" class="logout-btn-confirm" id="logoutConfirmBtn">
                    Ya, Logout
                </button>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- Desktop Sidebar -->
        <div class="sidebar">
            <div class="sidebar-content">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-bold text-blue-400">Admin Panel</h2>
                </div>
                <div class="sidebar-scrollable">
                    <div class="p-2">
                        <div class="sidebar-title">Menu Utama</div>
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-item @if(request()->routeIs('admin.dashboard')) active @endif">
                            <i class="fas fa-home"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.manajemenpegawai.index') }}" class="sidebar-item @if(request()->routeIs('admin.manajemenpegawai.index')) active @endif">
                            <i class="fas fa-users"></i>
                            Pegawai
                        </a>
                        <a href="{{ route('admin.jamkerja.index') }}" class="sidebar-item @if(request()->routeIs('admin.jamkerja.*')) active @endif">
                            <i class="fas fa-clock"></i>
                            Jam Kerja
                        </a>

                        <a href="{{ route('admin.lokasi.index') }}" class="sidebar-item @if(request()->routeIs('admin.lokasi.*')) active @endif">
                            <i class="fas fa-map-marker-alt"></i>
                            Lokasi
                        </a>

                        <a href="{{ route('admin.laporan.index') }}" class="sidebar-item @if(request()->routeIs('admin.laporan.*')) active @endif">
                            <i class="fas fa-chart-bar"></i>
                            Laporan
                        </a>
                        <a href="{{ route('admin.performa.index') }}" class="sidebar-item @if(request()->routeIs('admin.performa.*')) active @endif">
                            <i class="fas fa-trophy"></i>
                            Performa
                        </a>
                        <a href="{{ route('admin.pengumuman.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengumuman.*')) active @endif">
                            <i class="fas fa-bullhorn"></i>
                            Pengumuman
                        </a>

                        <div class="sidebar-title mt-6">Pengaturan</div>
                        <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengaturan.*')) active @endif">
                            <i class="fas fa-cog"></i>
                            Pengaturan
                        </a>
                        <a href="#" class="sidebar-item logout-trigger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Topbar -->
            <div class="topbar flex justify-between items-center">
                <div class="flex items-center">
                    <span class="font-semibold">@yield('title')</span>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" class="admin-theme-btn" onclick="toggleAdminTheme()" title="Ubah tema">
                        <i class="fas fa-sun" id="admin-sun"></i>
                        <i class="fas fa-moon" id="admin-moon" style="display:none;"></i>
                    </button>
                    <div class="text-sm text-gray-600">{{ Auth::user()->name }}</div>
                    @if(Auth::user()->foto_profil)
                    <img src="{{ asset('public/storage/foto_profil/' . Auth::user()->foto_profil) }}" class="user-avatar" alt="Avatar">
                    @else
                    <div class="user-avatar bg-gradient-to-r from-blue-500 to-purple-600 text-white flex items-center justify-center font-semibold">
                        {{ substr(Auth::user()->name,0,1) }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Main Content -->
            <main class="main-content p-2 sm:p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar toggle -->
    <button id="sidebarToggle" class="sm:hidden fixed bottom-4 right-4 bg-indigo-600 text-white p-3 rounded-full shadow-lg z-50">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Mobile Sidebar -->
    <div id="mobileSidebar" class="mobile-sidebar sm:hidden">
        <div class="mobile-sidebar-content">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-indigo-600">Admin Panel</h2>
            </div>
            <div class="sidebar-scrollable">
                <div class="p-2">
                    <div class="sidebar-title">Menu Utama</div>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item @if(request()->routeIs('admin.dashboard')) active @endif">
                        <i class="fas fa-home"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.manajemenpegawai.index') }}" class="sidebar-item @if(request()->routeIs('admin.manajemenpegawai.index')) active @endif">
                        <i class="fas fa-users"></i>
                        Pegawai
                    </a>
                    <a href="{{ route('admin.jamkerja.index') }}" class="sidebar-item @if(request()->routeIs('admin.jamkerja.*')) active @endif">
                        <i class="fas fa-clock"></i>
                        Jam Kerja
                    </a>
                    <a href="{{ route('admin.lokasi.index') }}" class="sidebar-item @if(request()->routeIs('admin.lokasi.*')) active @endif">
                        <i class="fas fa-map-marker-alt"></i>
                        Lokasi
                    </a>
                    <a href="{{ route('admin.laporan.index') }}" class="sidebar-item @if(request()->routeIs('admin.laporan.*')) active @endif">
                        <i class="fas fa-chart-bar"></i>
                        Laporan
                    </a>
                    <a href="{{ route('admin.performa.index') }}" class="sidebar-item @if(request()->routeIs('admin.performa.*')) active @endif">
                        <i class="fas fa-trophy"></i>
                        Performa
                    </a>
                    <a href="{{ route('admin.pengumuman.index') }}" class="sidebar-item @if(request()->routeIs('admin.pengumuman.*')) active @endif">
                        <i class="fas fa-bullhorn"></i>
                        Pengumuman
                    </a>

                    <div class="sidebar-title mt-6">Pengaturan</div>
                    <a href="" class="sidebar-item">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                    <a href="#" class="sidebar-item logout-trigger">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
        <div class="mobile-sidebar-overlay" id="sidebarOverlay"></div>
    </div>

    <script>
        function toggleAdminTheme() {
            var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('karisma-admin-theme', next);
            syncAdminThemeIcons();
        }
        function syncAdminThemeIcons() {
            var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            var s = document.getElementById('admin-sun'), m = document.getElementById('admin-moon');
            if (s) s.style.display = isDark ? 'none' : 'inline';
            if (m) m.style.display = isDark ? 'inline' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            syncAdminThemeIcons();

            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Logout modal elements
            const logoutModal = document.getElementById('logoutModal');
            const logoutCancelBtn = document.getElementById('logoutCancelBtn');
            const logoutConfirmBtn = document.getElementById('logoutConfirmBtn');
            const logoutTriggers = document.querySelectorAll('.logout-trigger');
            const logoutForm = document.getElementById('logout-form');

            function openLogoutModal() {
                logoutModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                // Close mobile sidebar if open
                closeMobileSidebar();
            }

            function closeLogoutModal() {
                logoutModal.classList.remove('active');
                document.body.style.overflow = '';
            }

            function openMobileSidebar() {
                mobileSidebar.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileSidebar() {
                mobileSidebar.classList.remove('open');
                document.body.style.overflow = '';
            }

            // Mobile sidebar events
            sidebarToggle.addEventListener('click', function() {
                openMobileSidebar();
            });

            sidebarOverlay.addEventListener('click', closeMobileSidebar);

            // Logout modal events
            logoutTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    openLogoutModal();
                });
            });

            logoutCancelBtn.addEventListener('click', closeLogoutModal);

            logoutConfirmBtn.addEventListener('click', function() {
                logoutForm.submit();
            });

            // Close modal when clicking outside
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    closeLogoutModal();
                }
            });

            // Close sidebar when clicking on a link (mobile)
            document.querySelectorAll('#mobileSidebar a:not(.logout-trigger)').forEach(link => {
                link.addEventListener('click', closeMobileSidebar);
            });

            // Prevent zoom on input focus for mobile
            document.querySelectorAll('input, select, textarea').forEach(el => {
                el.addEventListener('focus', function() {
                    window.scrollTo(0, 0);
                    document.body.style.zoom = "1.0";
                });
            });

            // Handle escape key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeLogoutModal();
                    closeMobileSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>