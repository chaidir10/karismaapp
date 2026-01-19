<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>KARISMA | @yield('title')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon-48x48.png') }}" type="image/png">

    <!-- External CSS -->
    <!-- Manifest -->
    <link rel="manifest" href="public/pwa/manifest.json">

    <!-- iOS Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="KARISMA">

    <!-- iOS Icons -->
    <link rel="apple-touch-icon" href="public/pwa/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="public/pwa/icons/icon-512x512.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <style>
        :root {
            --primary: #5AB6EA;
            --primary-dark: #2E97D4;
            --accent: #FEAA2B;
            --light: #f8fafc;
            --gray-light: #f1f5f9;
            --gray: #94a3b8;
            --gray-dark: #64748b;
            --dark: #1e293b;
            --white: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #E6F4F9;
            color: var(--dark);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* Container utama - di desktop lebarnya seperti HP, di mobile fit ke layar */
        .container {
            width: 100%;
            max-width: 500px;
            background-color: var(--white);
            min-height: 100vh;
            margin: 0 auto;
            padding: 0;
            box-shadow: 0 0 30px rgba(90, 182, 234, 0.1);
            position: relative;
        }

        /* Desktop: tetap di tengah dengan lebar HP */
        @media (min-width: 768px) {
            body {
                background-color: #d1e8f5;
                padding: 20px 0;
            }
            
            .container {
                border-radius: 20px;
                overflow: hidden;
                min-height: calc(100vh - 40px);
            }
        }

        /* Mobile: full width tanpa padding */
        @media (max-width: 767px) {
            body {
                background-color: #E6F4F9;
            }
            
            .container {
                max-width: 100%;
                box-shadow: none;
            }
        }

        /* Header */
        .app-header {
            padding: 25px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-bottom-right-radius: 30px;
            border-bottom-left-radius: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .greeting {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 3px;
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Main Content */
        .main-content {
            padding-bottom: 80px;
        }

        /* Attendance Card */
        .attendance-card {
            background-color: var(--white);
            margin: -20px 20px 25px;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(90, 182, 234, 0.1);
            border: 1px solid rgba(90, 182, 234, 0.1);
            position: relative;
            z-index: 2;
        }

        .attendance-date,
        .attendance-time {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .date-icon,
        .time-icon {
            color: var(--primary);
        }

        .attendance-time {
            color: var(--primary-dark);
            font-weight: 600;
        }

        .attendance-actions {
            display: flex;
            background: #FFE4BC;
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(254, 170, 43, 0.2);
            height: 56px;
        }

        .attendance-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            background-color: var(--accent);
            transition: all 0.3s;
            font-size: 15px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        #clock-in-btn {
            border-radius: 50px 0 0 50px;
        }

        #clock-out-btn {
            border-radius: 0 50px 50px 0;
        }

        .attendance-btn:hover:not(:disabled) {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .attendance-btn:disabled {
            background-color: var(--gray);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .attendance-icon {
            font-size: 20px;
            margin-right: 8px;
        }

        /* Attendance History */
        .attendance-history {
            background-color: var(--white);
            margin: 0 20px 25px;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(90, 182, 234, 0.1);
            border: 1px solid rgba(90, 182, 234, 0.1);
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .history-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--dark);
        }

        .view-all {
            color: var(--primary);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-light);
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-time {
            font-weight: 600;
            font-size: 14px;
            color: var(--dark);
        }

        .history-type {
            font-size: 12px;
            color: var(--gray-dark);
        }

        .history-status {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .status-on-time {
            background-color: #d1fae5;
            color: var(--success);
        }

        .status-late {
            background-color: #fef3c7;
            color: var(--warning);
        }

        .empty-state {
            text-align: center;
            padding: 20px 0;
            color: var(--gray);
            font-size: 14px;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            max-width: 500px;
            display: flex;
            justify-content: space-around;
            background-color: var(--white);
            padding: 15px 0;
            box-shadow: 0 -10px 30px rgba(90, 182, 234, 0.1);
            z-index: 10;
            border-top: 1px solid var(--gray-light);
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        /* Desktop: posisikan bottom-nav di dalam container */
        @media (min-width: 768px) {
            .bottom-nav {
                left: 50%;
                transform: translateX(-50%);
                border-radius: 20px;
            }
        }

        /* Mobile: full width bottom-nav */
        @media (max-width: 767px) {
            .bottom-nav {
                max-width: 100%;
                border-radius: 0;
            }
        }

        .nav-item {
            text-align: center;
            color: var(--gray-dark);
            font-size: 10px;
            width: 20%;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .nav-icon {
            font-size: 20px;
            margin-bottom: 0px;
            transition: all 0.3s;
        }

        .nav-item.active {
            color: var(--primary);
        }

        .nav-item.active .nav-icon {
            transform: scale(1.1);
        }

        .nav-item:hover {
            color: var(--primary);
        }

        /* Loading Spinner Styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner-full {
            width: 60px;
            height: 60px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            margin-top: 15px;
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 14px;
        }

        .loading-content {
            text-align: center;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(90, 182, 234, 0.2);
        }

        /* Page transition */
        .page-transition {
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Prevent loading on active nav items */
        .nav-item.active {
            pointer-events: none;
        }

        /* Smooth transitions for all interactive elements */
        a, button, .nav-item {
            transition: all 0.3s ease;
        }

        /* Modal Styles - Full Screen Mobile */
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
            margin: 0;
            background-color: var(--white);
            box-shadow: none;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 100px;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.0);
            z-index: 10;
            border-bottom: none;
            padding: 15px 20px;
        }

        .modal-title {
            font-weight: 100;
            color: var(--primary-dark);
            font-size: 18px;

        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .close-btn:hover {
            background-color: var(--gray-light);
        }

        /* Camera Container Full Screen - Fixed */
        .camera-container-full {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            margin: 0;
            padding: 0;
        }

        .camera-container-full video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            margin: 0;
            padding: 0;
        }

        /* Mini Map Container */
        .mini-map-container {
            position: absolute;
            top: 70px;
            right: 15px;
            width: 300px;
            height: auto;
            z-index: 10;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .mini-map {
            width: 100%;
            height: 110px;
        }

        .location-info-mini {
            padding: 5px;
            margin-top: 0 !important;
            font-size: 10px;
            text-align: center;
            background: var(--primary);
            color: white;
        }

        .location-info-mini i {
            margin-right: 3px;
        }

        /* Submit Button Container */
        .submit-btn-container {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            padding: 0 20px;
            z-index: 10;
            display: flex;
            justify-content: center;
        }

        .submit-btn-large {
            width: 90%;
            max-width: 300px;
            padding: 16px 24px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(90, 182, 234, 0.4);
            z-index: 11;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .submit-btn-large:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(90, 182, 234, 0.6);
        }

        .submit-btn-large:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Detail Modal Fullscreen dengan Layout 1:1 */
        .detail-modal .modal-fullscreen-mobile .modal-content {
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .detail-content-container {
            display: flex;
            flex: 1;
            width: 100%;
            margin: 0;
            gap: 0;
        }

        .detail-image-container,
        .detail-map-container {
            flex: 1;
            height: 100%;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .detail-image-container {
            border-right: 1px solid #e0e0e0;
        }

        .detail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .detail-map {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        .detail-info-section {
            padding: 15px 20px;
            background: white;
            border-top: 1px solid #e0e0e0;
        }

        .detail-location-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .detail-location-address {
            font-size: 14px;
            color: var(--gray-dark);
            margin-bottom: 15px;
        }

        .detail-back-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .detail-back-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(90, 182, 234, 0.3);
        }

        /* Perfect 1:1 ratio for detail modal */
        .detail-modal .modal-dialog {
            margin: 0;
            max-width: none;
        }

        .detail-modal .modal-content {
            border-radius: 0;
            border: none;
        }

        /* Loading states */
        .detail-image-container,
        .detail-map-container {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .detail-image-container .fas,
        .detail-map-container .fas {
            opacity: 0.5;
        }

        /* Regular Modal */
        .modal-content {
            background-color: var(--white);
            margin: 20px auto;
            padding: 25px;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }

        .camera-container {
            width: 100%;
            height: 300px;
            background-color: var(--gray-light);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
            border: 2px solid var(--primary);
        }

        #camera-view,
        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .location-info {
            background-color: var(--gray-light);
            padding: 15px;
            border-radius: 16px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid var(--primary);
        }

        .location-info i {
            color: var(--primary);
            margin-right: 8px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .submit-btn:hover:not(:disabled) {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(90, 182, 234, 0.3);
        }

        .submit-btn:disabled {
            background-color: var(--gray);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .attendance-card,
        .attendance-history {
            animation: fadeIn 0.5s ease-out forwards;
        }

        /* Modal Backdrop Fix */
        .modal-backdrop {
            z-index: 1055 !important;
        }

        .modal {
            z-index: 1060 !important;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .container {
                border-radius: 0;
            }

            .app-header {
                border-bottom-right-radius: 20px;
                border-bottom-left-radius: 20px;
            }

            .detail-content-container {
                flex-direction: column;
                height: 70vh;
            }

            .detail-image-container,
            .detail-map-container {
                flex: 1;
                min-height: 50%;
            }

            .detail-image-container {
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
            }

            .mini-map-container {
                top: 60px;
                right: 10px;
                width: 370px;
                height: 130px;
            }

            .mini-map {
                height: 85px;
            }

            .submit-btn-large {
                font-size: 16px;
                padding: 14px 20px;
            }
        }

        /* Full screen mobile camera fixes */
        @media (max-width: 768px) {
            .modal-fullscreen-mobile .modal-content {
                padding: 0;
                margin: 0;
                border: none;
            }

            .camera-container-full {
                width: 100vw;
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container">
        <!-- App Header -->
        @if(!request()->is('pegawai/akun*') && !request()->is('akun*'))
        <div class="app-header">
            <div class="header-content">
                <div>
                    <div class="greeting" id="greeting">Selamat pagi</div>
                    <h1 class="user-name" id="user-name">{{ Auth::user()->name ?? 'User' }}</h1>
                </div>

                <div class="user-avatar">
                    @if(Auth::user()->foto_profil && Storage::disk('public')->exists('foto_profil/' . Auth::user()->foto_profil))
                    <img src="{{ asset('public/storage/foto_profil/' . Auth::user()->foto_profil) }}"
                        alt="Foto Profil {{ Auth::user()->name }}"
                        class="w-full h-full object-cover"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&size=128'">
                    @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&size=128"
                        alt="Avatar {{ Auth::user()->name }}"
                        class="w-full h-full object-cover">
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <a href="{{ route('pegawai.dashboard') }}" class="nav-item {{ Route::is('pegawai.dashboard') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fas fa-home"></i></div>
                <div>Home</div>
            </a>

            <a href="{{ route('pegawai.riwayat') }}" class="nav-item {{ Route::is('pegawai.riwayat') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fas fa-history"></i></div>
                <div>Riwayat</div>
            </a>

            <a href="{{ route('pegawai.pengajuan.index') }}" class="nav-item {{ Route::is('pegawai.pengajuan.index') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fas fa-file-upload"></i></div>
                <div>Pengajuan</div>
            </a>

            <a href="{{ route('pegawai.daftar') }}" class="nav-item {{ Route::is('pegawai.daftar') ? 'active' : '' }}">
                <div class="nav-icon">
                    <div class="nav-icon"><i class="fas fa-users"></i></div>
                </div>
                <div>Pegawai</div>
            </a>

            <a href="{{ route('pegawai.akun.index') }}" class="nav-item {{ Route::is('pegawai.akun.index') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fas fa-user"></i></div>
                <div>Akun</div>
            </a>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner-full"></div>
            <div class="loading-text">Memuat...</div>
        </div>
    </div>

    <!-- External JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register("{{ asset('public/pwa/service-worker.js') }}")
                    .then(function(registration) {
                        console.log('Service Worker registered with scope:', registration.scope);
                    })
                    .catch(function(error) {
                        console.error('Service Worker registration failed:', error);
                    });
            });
        }

        // Loading handler
        class LoadingManager {
            constructor() {
                this.loadingOverlay = document.getElementById('loadingOverlay');
                this.isLoading = false;
                this.init();
            }

            init() {
                // Intercept all link clicks
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (link && this.shouldShowLoading(link)) {
                        e.preventDefault();
                        this.show();
                        
                        // Navigate after showing loading
                        setTimeout(() => {
                            window.location.href = link.href;
                        }, 100);
                    }
                });

                // Handle browser back/forward buttons
                window.addEventListener('popstate', () => {
                    this.show();
                });

                // Show loading when page is about to unload
                window.addEventListener('beforeunload', () => {
                    this.show();
                });

                // Hide loading when page is fully loaded
                window.addEventListener('load', () => {
                    this.hide();
                });

                // Also hide loading when DOM is ready
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => this.hide(), 500);
                });

                // Intercept form submissions
                document.addEventListener('submit', (e) => {
                    this.show('Mengirim data...');
                });

                // Intercept AJAX requests
                this.interceptAjax();
            }

            shouldShowLoading(link) {
                // Don't show loading for same page anchors, external links, or links with no-href
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || 
                    link.target === '_blank' || link.hasAttribute('download')) {
                    return false;
                }
                
                // Only show for internal links
                try {
                    const url = new URL(href, window.location.origin);
                    return url.origin === window.location.origin;
                } catch {
                    return true; // Assume internal for relative URLs
                }
            }

            interceptAjax() {
                const originalFetch = window.fetch;
                const originalXHROpen = XMLHttpRequest.prototype.open;
                const originalXHRSend = XMLHttpRequest.prototype.send;

                let activeRequests = 0;

                // Intercept fetch
                window.fetch = (...args) => {
                    activeRequests++;
                    this.show();
                    
                    return originalFetch.apply(this, args)
                        .then(response => {
                            activeRequests--;
                            if (activeRequests === 0) {
                                setTimeout(() => this.hide(), 300);
                            }
                            return response;
                        })
                        .catch(error => {
                            activeRequests--;
                            if (activeRequests === 0) {
                                this.hide();
                            }
                            throw error;
                        });
                };

                // Intercept XMLHttpRequest
                XMLHttpRequest.prototype.open = function(...args) {
                    this._url = args[1];
                    return originalXHROpen.apply(this, args);
                };

                XMLHttpRequest.prototype.send = function(...args) {
                    // Don't show loading for specific endpoints if needed
                    if (this._url && !this._url.includes('/api/')) {
                        activeRequests++;
                        this.show();
                    }

                    this.addEventListener('loadend', () => {
                        if (this._url && !this._url.includes('/api/')) {
                            activeRequests--;
                            if (activeRequests === 0) {
                                setTimeout(() => this.hide(), 300);
                            }
                        }
                    });

                    return originalXHRSend.apply(this, args);
                };
            }

            show(message = 'Memuat...') {
                if (this.isLoading) return;
                
                this.isLoading = true;
                const textElement = this.loadingOverlay.querySelector('.loading-text');
                if (textElement) {
                    textElement.textContent = message;
                }
                
                this.loadingOverlay.classList.add('active');
                
                // Auto hide after 10 seconds as fallback
                this.timeout = setTimeout(() => {
                    this.hide();
                }, 10000);
            }

            hide() {
                this.isLoading = false;
                clearTimeout(this.timeout);
                this.loadingOverlay.classList.remove('active');
            }
        }

        // Initialize loading manager when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            window.loadingManager = new LoadingManager();
            
            // Add page transition class to main content
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.classList.add('page-transition');
            }

            // Update greeting based on time
            updateGreeting();
        });

        // Function to show loading manually (can be called from other scripts)
        function showLoading(message = 'Memuat...') {
            if (window.loadingManager) {
                window.loadingManager.show(message);
            }
        }

        // Function to hide loading manually
        function hideLoading() {
            if (window.loadingManager) {
                window.loadingManager.hide();
            }
        }

        // Update greeting based on time
        function updateGreeting() {
            const hour = new Date().getHours();
            const greetingElement = document.getElementById('greeting');
            
            if (greetingElement) {
                let greeting = 'Selamat pagi';
                if (hour >= 12 && hour < 15) {
                    greeting = 'Selamat siang';
                } else if (hour >= 15 && hour < 19) {
                    greeting = 'Selamat sore';
                } else if (hour >= 19 || hour < 4) {
                    greeting = 'Selamat malam';
                }
                greetingElement.textContent = greeting;
            }
        }

        // Handle Turbolinks if used (for Laravel Livewire, Inertia.js, etc.)
        if (typeof Turbolinks !== 'undefined') {
            document.addEventListener('turbolinks:click', () => {
                showLoading();
            });
            
            document.addEventListener('turbolinks:load', () => {
                setTimeout(hideLoading, 500);
            });
        }

        // Handle Livewire if used
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('request', ({ uri, options }) => {
                showLoading('Memproses...');
            });

            Livewire.hook('response', ({ component, success }) => {
                setTimeout(hideLoading, 300);
            });
        }

        // Example usage for custom buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Attach loading to all buttons with data-loading attribute
            document.querySelectorAll('[data-loading]').forEach(button => {
                button.addEventListener('click', function() {
                    const message = this.getAttribute('data-loading-message') || 'Memproses...';
                    showLoading(message);
                });
            });
        });
    </script>

    <!-- Push scripts dari child blade -->
    @stack('scripts')
</body>

</html>