<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>KARISMA | @yield('title')</title>
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon-48x48.png') }}" type="image/png">
    <link rel="manifest" href="public/pwa/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="KARISMA">
    <link rel="apple-touch-icon" href="public/pwa/icons/icon-192x192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #1D4ED8;
            --primary-light: #EFF6FF;
            --secondary: #6366F1;
            --accent: #F59E0B;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --surface: #F8FAFC;
            --surface-2: #F1F5F9;
            --border: #E2E8F0;
            --text-primary: #0F172A;
            --text-secondary: #64748B;
            --text-muted: #94A3B8;
            --white: #FFFFFF;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow: 0 4px 16px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 32px rgba(0,0,0,0.1);
            --radius: 16px;
            --radius-sm: 10px;
            --radius-xs: 6px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #E8EFF9;
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        .app-shell {
            width: 100%;
            max-width: 480px;
            background: var(--surface);
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            body { padding: 24px 0; background: #D6E4FA; }
            .app-shell {
                border-radius: 28px;
                overflow: hidden;
                min-height: calc(100vh - 48px);
                box-shadow: 0 24px 64px rgba(0,0,0,0.15);
            }
        }

        @media (max-width: 767px) {
            .app-shell { max-width: 100%; }
        }

        /* ── Header ─────────────────────────── */
        .app-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px 20px 36px;
            position: relative;
            overflow: hidden;
        }

        .app-header::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 160px; height: 160px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .app-header::after {
            content: '';
            position: absolute;
            bottom: -20px; left: -20px;
            width: 100px; height: 100px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            color: white;
            letter-spacing: -0.5px;
        }

        .brand-text {
            color: rgba(255,255,255,0.9);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header-greeting {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
            margin-bottom: 2px;
        }

        .header-name {
            font-size: 16px;
            font-weight: 700;
            color: white;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-notif {
            width: 38px;
            height: 38px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .header-notif:hover { background: rgba(255,255,255,0.25); }

        .header-avatar {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            border: 2px solid rgba(255,255,255,0.4);
            overflow: hidden;
            cursor: pointer;
        }

        .header-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* ── Content ─────────────────────────── */
        .main-content {
            flex: 1;
            padding-bottom: 90px;
            margin-top: -20px;
            position: relative;
            z-index: 1;
        }

        /* ── Bottom Nav ──────────────────────── */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            max-width: 480px;
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 8px 4px 12px;
            z-index: 100;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        @media (min-width: 768px) {
            .bottom-nav { left: 50%; transform: translateX(-50%); border-radius: 0 0 28px 28px; }
        }

        @media (max-width: 767px) {
            .bottom-nav { max-width: 100%; padding-bottom: calc(12px + env(safe-area-inset-bottom)); }
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 9px;
            font-weight: 500;
            letter-spacing: 0.3px;
            padding: 6px 14px;
            border-radius: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .nav-item i { font-size: 18px; transition: all 0.2s; }

        .nav-item.active {
            color: var(--primary);
            background: var(--primary-light);
        }

        .nav-item.active i { transform: scale(1.1); }
        .nav-item:hover { color: var(--primary); }

        /* ── Modals – Camera Full Screen ─────── */
        .modal-fullscreen-mobile {
            width: 100vw;
            height: 100vh;
            margin: 0;
            max-width: none;
            padding: 0;
        }

        .modal-fullscreen-mobile .modal-content {
            width: 100%;
            height: 100%;
            border-radius: 0;
            border: none;
            background: #000;
        }

        .camera-container-full {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .camera-container-full video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Camera UI overlay elements */
        .camera-overlay {
            position: absolute;
            inset: 0;
            z-index: 5;
            pointer-events: none;
        }

        /* Corner guides */
        .camera-overlay::before, .camera-overlay::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
        }

        .camera-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            padding: 16px 20px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.5), transparent);
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 10;
            pointer-events: all;
        }

        .camera-close-btn {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .camera-title {
            color: white;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Mini map inside camera */
        .mini-map-wrapper {
            position: absolute;
            top: 72px;
            right: 16px;
            z-index: 10;
        }

        .mini-map-container {
            width: 160px;
            background: white;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .mini-map { width: 100%; height: 90px; }

        .location-info-mini {
            padding: 6px 8px;
            font-size: 9px;
            color: white;
            background: var(--primary);
            line-height: 1.3;
        }

        .location-info-mini i { margin-right: 3px; }

        /* Camera bottom action bar */
        .camera-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            z-index: 10;
        }

        .capture-btn {
            width: 100%;
            height: 58px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 18px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(59,130,246,0.5);
        }

        .capture-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(59,130,246,0.6);
        }

        .capture-btn:disabled { opacity: 0.7; cursor: not-allowed; }

        /* ── Detail Modal ─────────────────────── */
        .detail-modal .modal-fullscreen-mobile .modal-content {
            background: white;
            display: flex;
            flex-direction: column;
        }

        .detail-media-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            flex: 1;
        }

        .detail-photo, .detail-map-pane {
            position: relative;
            overflow: hidden;
        }

        .detail-photo { border-right: 1px solid var(--border); }
        .detail-photo img { width: 100%; height: 100%; object-fit: cover; }
        .detail-map-pane .detail-map { width: 100%; height: 100%; }

        .detail-info-bar {
            padding: 16px 20px;
            background: white;
            border-top: 1px solid var(--border);
        }

        .detail-status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .detail-type-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .detail-time {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .detail-address {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .detail-back-btn {
            width: 100%;
            height: 48px;
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .detail-back-btn:hover { background: var(--primary); color: white; }

        /* ── Loading ─────────────────────────── */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s, visibility 0.25s;
            gap: 12px;
        }

        .loading-overlay.active { opacity: 1; visibility: visible; }

        .loading-ring {
            width: 44px;
            height: 44px;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .loading-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-transition { animation: fadeUp 0.3s ease-out; }

        /* ── Generic Modal ───────────────────── */
        .modal-content {
            border: none;
            border-radius: var(--radius);
            overflow: hidden;
        }

        @media (max-width: 576px) {
            .detail-media-grid { grid-template-rows: 1fr 1fr; grid-template-columns: 1fr; height: 70dvh; }
            .detail-photo { border-right: none; border-bottom: 1px solid var(--border); }
            .mini-map-container { width: 140px; }
        }

        @media (max-width: 768px) {
            .modal-fullscreen-mobile .modal-content { border-radius: 0; }
        }

        @stack('layout-styles')
    </style>

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="app-shell">
        <!-- Header -->
        @if(!request()->is('pegawai/akun*') && !request()->is('akun*'))
        <div class="app-header">
            <div class="header-inner">
                <div class="header-brand">
                    <div class="brand-logo">K</div>
                    <div class="brand-text">Karisma</div>
                </div>
                <div>
                    <div class="header-greeting" id="greeting">Selamat pagi</div>
                    <div class="header-name">{{ Auth::user()->name ?? 'User' }}</div>
                </div>
                <div class="header-right">
                    <div class="header-notif">
                        <i class="fas fa-bell"></i>
                    </div>
                    <a href="{{ route('pegawai.akun.index') }}" class="header-avatar">
                        @if(Auth::user()->foto_profil && Storage::disk('public')->exists('foto_profil/' . Auth::user()->foto_profil))
                        <img src="{{ asset('public/storage/foto_profil/' . Auth::user()->foto_profil) }}"
                            alt="{{ Auth::user()->name }}"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3B82F6&color=fff&size=128'">
                        @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3B82F6&color=fff&size=128"
                            alt="{{ Auth::user()->name }}">
                        @endif
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <a href="{{ route('pegawai.dashboard') }}" class="nav-item {{ Route::is('pegawai.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('pegawai.riwayat') }}" class="nav-item {{ Route::is('pegawai.riwayat') ? 'active' : '' }}">
                <i class="fas fa-clock-rotate-left"></i>
                <span>Riwayat</span>
            </a>
            <a href="{{ route('pegawai.pengajuan.index') }}" class="nav-item {{ Route::is('pegawai.pengajuan.index') ? 'active' : '' }}">
                <i class="fas fa-paper-plane"></i>
                <span>Pengajuan</span>
            </a>
            <a href="{{ route('pegawai.daftar') }}" class="nav-item {{ Route::is('pegawai.daftar') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Pegawai</span>
            </a>
            <a href="{{ route('pegawai.akun.index') }}" class="nav-item {{ Route::is('pegawai.akun.index') ? 'active' : '' }}">
                <i class="fas fa-circle-user"></i>
                <span>Akun</span>
            </a>
        </nav>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-ring"></div>
        <div class="loading-label" id="loadingText">Memuat...</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('public/pwa/service-worker.js') }}")
                    .catch(err => console.warn('SW failed:', err));
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Greeting
            const h = new Date().getHours();
            const g = document.getElementById('greeting');
            if (g) {
                g.textContent = h < 12 ? 'Selamat pagi,' : h < 15 ? 'Selamat siang,' : h < 19 ? 'Selamat sore,' : 'Selamat malam,';
            }

            // Loading manager
            const overlay = document.getElementById('loadingOverlay');
            const loadingText = document.getElementById('loadingText');

            document.querySelectorAll('a[href]').forEach(a => {
                const href = a.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || a.target === '_blank' || a.dataset.noLoading) return;
                try {
                    const u = new URL(href, window.location.origin);
                    if (u.origin !== window.location.origin) return;
                } catch { return; }
                a.addEventListener('click', () => {
                    overlay.classList.add('active');
                    setTimeout(() => { window.location.href = a.href; }, 80);
                });
            });

            document.addEventListener('submit', () => {
                if (loadingText) loadingText.textContent = 'Mengirim...';
                overlay.classList.add('active');
            });

            window.addEventListener('pageshow', () => overlay.classList.remove('active'));
        });

        function showLoading(msg) {
            document.getElementById('loadingText').textContent = msg || 'Memuat...';
            document.getElementById('loadingOverlay').classList.add('active');
        }
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }
    </script>

    @stack('scripts')
</body>
</html>