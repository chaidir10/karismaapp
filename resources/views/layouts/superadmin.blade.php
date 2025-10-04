<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Superadmin | @yield('title')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon-48x48.png') }}" type="image/png">


    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-X9+2K1+rK1Uow+rOqvN12L1B1VX8GzI4G9Y6cQyVg5zvH3U5rv6Y/sPuPHzQF7ZB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        /* Sidebar styling */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            border-right: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            margin: 6px 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            cursor: pointer;
        }

        .sidebar-item:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .sidebar-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 12px;
        }

        .sidebar-title {
            font-size: 12px;
            font-weight: 600;
            padding: 0 24px;
            margin: 24px 0 12px 0;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Topbar styling */
        .topbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            padding: 1rem 2rem;
            border-bottom: 1px solid #f1f5f9;
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            border-color: #4f46e5;
            transform: scale(1.05);
        }

        /* Mobile sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 280px;
            height: 100vh;
            background: white;
            z-index: 1000;
            transition: left 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .mobile-sidebar.active {
            left: 0;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Logo styling */
        .logo-container {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #4f46e5 0%, #9691fdff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #4f46e5;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: #f1f5f9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .sidebar {
                display: none;
            }

            .topbar {
                padding: 1rem;
            }

            .user-info {
                display: none;
            }
        }

        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Main content area */
        .main-content {
            min-height: calc(100vh - 80px);
            background: #f8fafc;
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
    </style>

    <!-- Stack untuk tambahan CSS halaman -->
    @stack('styles')
</head>

<body class="bg-gray-50">
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

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="logo-container">
            <div class="logo">Superadmin Panel</div>
        </div>
        <div class="flex-1 p-2 overflow-y-auto">
            <div class="sidebar-title">Menu Utama</div>
            <a href="{{ route('superadmin.dashboard') }}" class="sidebar-item @if(request()->routeIs('superadmin.dashboard')) active @endif">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="{{ route('superadmin.manajemenadmin.index') }}" class="sidebar-item @if(request()->routeIs('superadmin.manajemenadmin.*')) active @endif">
                <i class="fas fa-users-cog"></i> Manajemen Admin
            </a>

            <div class="sidebar-title mt-6">Pengaturan</div>
            <a href="#" class="sidebar-item logout-trigger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="min-h-screen flex">
        <!-- Desktop Sidebar -->
        <div class="sidebar hidden lg:flex flex-col">
            <div class="logo-container">
                <h2 class="logo">Superadmin Panel</h2>
            </div>
            <div class="flex-1 p-2 overflow-y-auto">
                <div class="sidebar-title">Menu Utama</div>
                <a href="{{ route('superadmin.dashboard') }}" class="sidebar-item @if(request()->routeIs('superadmin.dashboard')) active @endif">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('superadmin.manajemenadmin.index') }}" class="sidebar-item @if(request()->routeIs('superadmin.manajemenadmin.*')) active @endif">
                    <i class="fas fa-users-cog"></i> Manajemen Admin
                </a>

                <div class="sidebar-title mt-6">Pengaturan</div>
                <a href="#" class="sidebar-item logout-trigger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <!-- User info in sidebar -->
            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center space-x-3">
                    @if(Auth::user()->foto_profil && Storage::disk('public')->exists('foto_profil/' . Auth::user()->foto_profil))
                    <img src="{{ url('public/storage/foto_profil/' . Auth::user()->foto_profil) }}"
                        class="user-avatar"
                        alt="Avatar"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&size=128'">
                    @else
                    <div class="user-avatar bg-gradient-to-r from-blue-500 to-purple-600 text-white flex items-center justify-center font-semibold text-sm">
                        {{ substr(Auth::user()->name,0,1) }}
                    </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">Superadmin</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <div class="topbar flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button class="mobile-menu-btn lg:hidden" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="font-semibold text-lg text-gray-800">@yield('title')</span>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Desktop user info -->
                    <div class="hidden lg:flex items-center space-x-3 user-info">
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">Superadmin</div>
                        </div>
                        @if(Auth::user()->foto_profil)
                        <img src="{{ asset('public/storage/foto_profil/' . Auth::user()->foto_profil) }}" class="user-avatar" alt="Avatar">
                        @else
                        <div class="user-avatar bg-gradient-to-r from-blue-500 to-purple-600 text-white flex items-center justify-center font-semibold">
                            {{ substr(Auth::user()->name,0,1) }}
                        </div>
                        @endif
                    </div>

                    <!-- Mobile user avatar only -->
                    <div class="lg:hidden">
                        @if(Auth::user()->foto_profil)
                        <img src="{{ asset('public/storage/foto_profil/' . Auth::user()->foto_profil) }}" class="user-avatar" alt="Avatar">
                        @else
                        <div class="user-avatar bg-gradient-to-r from-blue-500 to-purple-600 text-white flex items-center justify-center font-semibold text-sm">
                            {{ substr(Auth::user()->name,0,1) }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="main-content flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap JS + Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-vSTU3CwV+jB7F0jaO3eLgFJg7L02vJ4O9WgkW0T+N+6Jb+4ejl0qN/+Zp5sfp3+2" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-cF5V6eqG0kYqgM8mv/jVtTgTF4sRUZCJ9RlR9xF8u0rj0K0+O5dGv+Z8QYc5Q1Pv" crossorigin="anonymous"></script>

    <script>
        // Mobile sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Logout modal elements
            const logoutModal = document.getElementById('logoutModal');
            const logoutCancelBtn = document.getElementById('logoutCancelBtn');
            const logoutConfirmBtn = document.getElementById('logoutConfirmBtn');
            const logoutTriggers = document.querySelectorAll('.logout-trigger');
            const logoutForm = document.getElementById('logout-form');

            function openSidebar() {
                mobileSidebar.classList.add('active');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                mobileSidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            function openLogoutModal() {
                logoutModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeLogoutModal() {
                logoutModal.classList.remove('active');
                document.body.style.overflow = '';
            }

            // Mobile sidebar events
            mobileMenuBtn.addEventListener('click', openSidebar);
            sidebarOverlay.addEventListener('click', closeSidebar);

            // Logout modal events
            logoutTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    openLogoutModal();
                    closeSidebar(); // Close mobile sidebar if open
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
            const sidebarLinks = document.querySelectorAll('.mobile-sidebar .sidebar-item:not(.logout-trigger)');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', closeSidebar);
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1024) {
                    closeSidebar();
                }
            });

            // Handle escape key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeLogoutModal();
                }
            });
        });
    </script>

    <!-- Stack untuk tambahan JS halaman -->
    @stack('scripts')
</body>

</html>