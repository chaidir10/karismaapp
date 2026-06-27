<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Superadmin | @yield('title')</title>
    <link rel="icon" type="image/png" sizes="48x48" href="{{ $appLogoUrl ?? asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ $appLogoUrl ?? asset('public/images/favicon-48x48.png') }}" type="image/png">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    @stack('styles')
    <style>
        * { font-family:'Poppins',sans-serif; -webkit-text-size-adjust:100%; }
        body {
            font-family:'Poppins',sans-serif;
            -webkit-text-size-adjust:100%;
            background:#f9fafb;
            color:#1e293b;
        }

        .main-container { display:flex; min-height:100vh; }

        .sidebar {
            width:230px; background:#fff; border-right:1px solid #e2e8f0;
            position:fixed; left:0; top:0; height:100vh; z-index:40; display:none;
        }
        @media (min-width:640px) { .sidebar { display:flex; flex-direction:column; } }

        .sidebar-content { display:flex; flex-direction:column; height:100%; }
        .sidebar-scrollable { flex:1; overflow-y:auto; padding:8px 0 16px; }

        .sidebar-item {
            display:flex; align-items:center; gap:10px; padding:9px 14px;
            color:#64748b; text-decoration:none; font-size:13px; font-weight:500;
            border-radius:10px; margin:2px 10px; transition:all .15s; position:relative;
        }
        .sidebar-item:hover { background:rgba(90,182,234,0.08); color:#1e293b; }
        .sidebar-item.active { background:rgba(90,182,234,0.12); color:#2E97D4; font-weight:600; }
        .sidebar-item.active::before {
            content:''; position:absolute; left:-10px; top:0; bottom:0; width:3px;
            border-radius:0 3px 3px 0; background:#2E97D4;
        }
        .sidebar-item i { width:18px; font-size:14px; text-align:center; flex-shrink:0; }

        .sidebar-title {
            font-size:10px; font-weight:700; padding:0 16px; margin:16px 0 6px;
            color:#94a3b8; text-transform:uppercase; letter-spacing:.8px;
        }

        .content-area { flex:1; margin-left:0; display:flex; flex-direction:column; min-height:100vh; }
        @media (min-width:640px) { .content-area { margin-left:230px; } }

        .topbar {
            background:#fff; border-bottom:1px solid #e2e8f0; padding:0 24px; height:56px;
            display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:30;
        }
        .topbar-title { font-size:14px; font-weight:600; color:#1e293b; }

        .topbar-user {
            display:flex; align-items:center; gap:10px; padding:4px 10px 4px 4px;
            border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0;
        }
        .user-avatar { width:32px; height:32px; border-radius:8px; object-fit:cover; flex-shrink:0; }
        .topbar-user-name { font-size:12px; font-weight:600; color:#1e293b; }

        .main-content { flex:1; overflow-y:auto; background:#f9fafb; }
        .main-content > div { max-width:100%; }

        .layout-footer {
            margin-top: 14px;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: linear-gradient(135deg, rgba(90,182,234,0.08), rgba(46,151,212,0.04));
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 11px;
            color: #64748b;
        }
        .layout-footer strong { color:#1e293b; font-weight:700; }
        .layout-footer .footer-chip {
            display:inline-flex; align-items:center; gap:6px;
            padding:4px 8px; border-radius:999px;
            background:rgba(90,182,234,0.12); color:#2E97D4;
            font-weight:600; font-size:10px; white-space:nowrap;
        }
        @media (max-width: 640px) {
            .layout-footer { padding:8px 10px; font-size:10px; gap:8px; }
            .layout-footer .footer-chip { padding:3px 7px; font-size:9px; }
        }

        .logout-modal {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1100;
            align-items:center; justify-content:center;
        }
        .logout-modal.active { display:flex; }
        .logout-modal-content {
            background:#fff; border-radius:16px; padding:2rem; max-width:400px; width:90%;
            box-shadow:0 20px 25px -5px rgba(0,0,0,.1),0 10px 10px -5px rgba(0,0,0,.04); text-align:center;
        }
        .logout-icon { font-size:3rem; color:#ef4444; margin-bottom:1rem; }
        .logout-modal h3 { font-size:1.25rem; font-weight:600; color:#1f2937; margin-bottom:.5rem; }
        .logout-modal p { color:#6b7280; margin-bottom:1.5rem; }
        .logout-modal-buttons { display:flex; gap:.75rem; justify-content:center; }
        .logout-btn-cancel {
            background:#f3f4f6; color:#374151; border:none; padding:.75rem 1.5rem; border-radius:8px;
            font-weight:500; cursor:pointer;
        }
        .logout-btn-confirm {
            background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%); color:#fff; border:none;
            padding:.75rem 1.5rem; border-radius:8px; font-weight:500; cursor:pointer;
        }

        .mobile-sidebar {
            position:fixed; inset:0; z-index:50; transform:translateX(-100%);
            transition:transform .3s ease-in-out;
        }
        .mobile-sidebar.open { transform:translateX(0); }
        .mobile-sidebar-content {
            width:250px; height:100%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,.1);
            display:flex; flex-direction:column;
        }
        .mobile-sidebar-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:40; }

        .sidebar-scrollable::-webkit-scrollbar { width:4px; height:4px; }
        .sidebar-scrollable::-webkit-scrollbar-thumb { background:rgba(0,0,0,.12); border-radius:20px; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 text-sm">
    <div class="logout-modal" id="logoutModal">
        <div class="logout-modal-content">
            <div class="logout-icon"><i class="fas fa-sign-out-alt"></i></div>
            <h3>Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin keluar dari sistem? Pastikan semua pekerjaan Anda sudah disimpan.</p>
            <div class="logout-modal-buttons">
                <button type="button" class="logout-btn-cancel" id="logoutCancelBtn">Batal</button>
                <button type="button" class="logout-btn-confirm" id="logoutConfirmBtn">Ya, Logout</button>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <div class="sidebar-content">
                <div style="height:56px; padding:0 16px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        @if($appLogoUrl)
                            <img src="{{ $appLogoUrl }}" alt="Logo" style="width:32px; height:32px; border-radius:10px; object-fit:contain;">
                        @else
                            <div style="width:32px; height:32px; border-radius:10px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:14px;">K</div>
                        @endif
                        <div>
                            <div style="font-size:14px; font-weight:700; color:#1e293b;">KARISMA</div>
                            <div style="font-size:10px; color:#94a3b8;">Superadmin Panel</div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-scrollable">
                    <div style="padding:4px 0;">
                        <div class="sidebar-title">Menu Utama</div>
                        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-item @if(request()->routeIs('superadmin.dashboard')) active @endif">
                            <i class="fas fa-house"></i> Dashboard
                        </a>
                        <a href="{{ route('superadmin.manajemenadmin.index') }}" class="sidebar-item @if(request()->routeIs('superadmin.manajemenadmin.*')) active @endif">
                            <i class="fas fa-user-group"></i> Manajemen Admin
                        </a>

                        <div class="sidebar-title">Sistem</div>
                        <a href="#" class="sidebar-item logout-trigger">
                            <i class="fas fa-arrow-right-from-bracket"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="topbar">
                <div class="topbar-title">@yield('title')</div>
                <div class="topbar-user">
                    @php
                        $fotoProfil = Auth::user()->foto_profil ?? null;
                        $fotoPath = $fotoProfil ? 'foto_profil/' . ltrim($fotoProfil, '/') : null;
                        $fotoExists = $fotoPath ? Storage::disk('public')->exists($fotoPath) : false;
                    @endphp
                    @if($fotoExists)
                        <img src="{{ asset('public/storage/' . $fotoPath) }}" class="user-avatar" alt="Avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="user-avatar" style="display:none; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; align-items:center; justify-content:center; font-size:12px; font-weight:700;">
                            {{ substr(Auth::user()->name,0,1) }}
                        </div>
                    @else
                        <div class="user-avatar" style="background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700;">
                            {{ substr(Auth::user()->name,0,1) }}
                        </div>
                    @endif
                    <span class="topbar-user-name">{{ Auth::user()->name }}</span>
                </div>
            </div>

            <main class="main-content" style="padding:20px 24px;">
                @yield('content')
                <footer class="layout-footer">
                    <span><strong>KARISMA</strong> <span style="opacity:.75;">&middot; Pusat Kendali Superadmin &middot; {{ now()->format('Y') }}</span></span>
                    <span class="footer-chip"><i class="fas fa-crown"></i> Tata Kelola Sistem Presensi</span>
                </footer>
            </main>
        </div>
    </div>

    <button id="sidebarToggle" class="sm:hidden fixed bottom-4 right-4 bg-indigo-600 text-white p-3 rounded-full shadow-lg z-50">
        <i class="fas fa-bars"></i>
    </button>

    <div id="mobileSidebar" class="mobile-sidebar sm:hidden">
        <div class="mobile-sidebar-content">
            <div style="padding:20px 16px 16px; border-bottom:1px solid #e2e8f0;">
                <div style="display:flex; align-items:center; gap:10px;">
                    @if($appLogoUrl)
                        <img src="{{ $appLogoUrl }}" alt="Logo" style="width:32px; height:32px; border-radius:10px; object-fit:contain;">
                    @else
                        <div style="width:32px; height:32px; border-radius:10px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:14px;">K</div>
                    @endif
                    <div>
                        <div style="font-size:14px; font-weight:700; color:#1e293b;">KARISMA</div>
                        <div style="font-size:10px; color:#94a3b8;">Superadmin Panel</div>
                    </div>
                </div>
            </div>
            <div class="sidebar-scrollable">
                <div style="padding:4px 0;">
                    <div class="sidebar-title">Menu Utama</div>
                    <a href="{{ route('superadmin.dashboard') }}" class="sidebar-item @if(request()->routeIs('superadmin.dashboard')) active @endif">
                        <i class="fas fa-house"></i> Dashboard
                    </a>
                    <a href="{{ route('superadmin.manajemenadmin.index') }}" class="sidebar-item @if(request()->routeIs('superadmin.manajemenadmin.*')) active @endif">
                        <i class="fas fa-user-group"></i> Manajemen Admin
                    </a>

                    <div class="sidebar-title">Sistem</div>
                    <a href="#" class="sidebar-item logout-trigger">
                        <i class="fas fa-arrow-right-from-bracket"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        <div class="mobile-sidebar-overlay" id="sidebarOverlay"></div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            const logoutModal = document.getElementById('logoutModal');
            const logoutCancelBtn = document.getElementById('logoutCancelBtn');
            const logoutConfirmBtn = document.getElementById('logoutConfirmBtn');
            const logoutTriggers = document.querySelectorAll('.logout-trigger');
            const logoutForm = document.getElementById('logout-form');

            function openLogoutModal() {
                logoutModal.classList.add('active');
                document.body.style.overflow = 'hidden';
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

            sidebarToggle.addEventListener('click', openMobileSidebar);
            sidebarOverlay.addEventListener('click', closeMobileSidebar);

            logoutTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    openLogoutModal();
                });
            });

            logoutCancelBtn.addEventListener('click', closeLogoutModal);
            logoutConfirmBtn.addEventListener('click', function() { logoutForm.submit(); });

            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) closeLogoutModal();
            });

            document.querySelectorAll('#mobileSidebar a:not(.logout-trigger)').forEach(link => {
                link.addEventListener('click', closeMobileSidebar);
            });

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
