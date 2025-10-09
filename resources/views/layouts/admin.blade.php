<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon-48x48.png') }}" type="image/png">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <title>Admin | @yield('title')</title>
    <!-- Google Fonts + Tailwind + Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    @stack('head')
    @stack('styles')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-text-size-adjust: 100%;
        }

        /* Loading Spinner Styles */
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .loading-spinner.active {
            opacity: 1;
            visibility: visible;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(79, 70, 229, 0.1);
            border-left: 4px solid #2099dfff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 16px;
        }

        .spinner-text {
            font-size: 16px;
            color: #4b5563;
            font-weight: 500;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Pulse animation for a modern alternative */
        .pulse-spinner {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2099dfff, #4f46e5);
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
            margin-bottom: 16px;
            box-shadow: 0 0 20px rgba(79, 70, 229, 0.3);
        }

        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.8; }
            50% { transform: scale(1); opacity: 1; }
            100% { transform: scale(0.8); opacity: 0.8; }
        }

        /* Dots animation for another modern alternative */
        .dots-spinner {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2099dfff;
            animation: bounce 1.4s ease-in-out infinite both;
        }

        .dot:nth-child(1) { animation-delay: -0.32s; }
        .dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            } 40% {
                transform: scale(1);
            }
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

            .loading-spinner {
                padding: 20px;
            }
            
            .spinner, .pulse-spinner {
                width: 50px;
                height: 50px;
            }
            
            .spinner-text {
                font-size: 14px;
            }
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        /* Topbar styling */
        .topbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1rem 1.5rem;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
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
</head>

<body class="bg-gray-100 text-gray-800 text-sm">
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <!-- Pilih salah satu dari tiga spinner di bawah ini -->
        
        <!-- Spinner 1: Classic Spinner -->
        <div class="spinner"></div>
        
        <!-- Spinner 2: Pulse Spinner -->
        <!-- <div class="pulse-spinner"></div> -->
        
        <!-- Spinner 3: Dots Spinner -->
        <!-- <div class="dots-spinner">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div> -->
        
        <div class="spinner-text">Memuat...</div>
    </div>

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

    <div class="min-h-screen flex flex-col sm:flex-row">
        <!-- Desktop Sidebar -->
        <div class="sidebar hidden sm:flex flex-col">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-blue-400">Admin Panel</h2>
            </div>
            <div class="flex-1 p-2">
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


                <div class="sidebar-title mt-6">Pengaturan</div>
                <a href="" class="sidebar-item">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
                <a href="#" class="sidebar-item logout-trigger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>

            </div>
        </div>

        <!-- Mobile sidebar toggle -->
        <button id="sidebarToggle" class="sm:hidden fixed bottom-4 right-4 bg-indigo-600 text-white p-3 rounded-full shadow-lg z-50">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <div class="topbar flex justify-between items-center">
                <div class="flex items-center">
                    <span class="font-semibold">@yield('title')</span>
                </div>
                <div class="flex items-center space-x-4">
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
            <main class="flex-1 overflow-y-auto p-2 sm:p-4 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 sm:hidden"></div>

    {{-- Mobile Sidebar --}}
    <div id="mobileSidebar" class="hidden fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50 transform -translate-x-full sm:hidden transition-transform duration-300 ease-in-out">
        <div class="p-4 border-b">
            <h2 class="text-xl font-bold text-indigo-600">Admin Panel</h2>
        </div>
        <div class="flex-1 p-2">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loading spinner element
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            // Function to show loading spinner
            function showLoading(message = 'Memuat...') {
                const spinnerText = loadingSpinner.querySelector('.spinner-text');
                if (spinnerText) {
                    spinnerText.textContent = message;
                }
                loadingSpinner.classList.add('active');
            }
            
            // Function to hide loading spinner
            function hideLoading() {
                loadingSpinner.classList.remove('active');
            }
            
            // Enhanced function for download operations
            function handleDownload(button, url, method = 'GET', data = null) {
                showLoading('Mempersiapkan download...');
                
                // Disable button to prevent multiple clicks
                if (button) {
                    button.disabled = true;
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                }
                
                // Create form for POST requests
                if (method.toUpperCase() === 'POST') {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';
                    
                    // Add CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                    
                    // Add data if provided
                    if (data) {
                        for (const key in data) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = data[key];
                            form.appendChild(input);
                        }
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } else {
                    // For GET requests, use fetch to detect completion
                    fetch(url, {
                        method: method,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.blob();
                        }
                        throw new Error('Network response was not ok');
                    })
                    .then(blob => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        
                        // Extract filename from response headers or use default
                        const contentDisposition = response.headers.get('content-disposition');
                        let filename = 'laporan.pdf';
                        if (contentDisposition) {
                            const filenameMatch = contentDisposition.match(/filename="?(.+)"?/);
                            if (filenameMatch.length === 2) {
                                filename = filenameMatch[1];
                            }
                        }
                        a.download = filename;
                        
                        document.body.appendChild(a);
                        a.click();
                        
                        // Cleanup
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        
                        hideLoading();
                        if (button) {
                            button.disabled = false;
                            button.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Download error:', error);
                        hideLoading();
                        if (button) {
                            button.disabled = false;
                            button.innerHTML = originalText;
                        }
                        alert('Terjadi kesalahan saat mendownload laporan');
                    });
                }
                
                // Fallback: hide loading after timeout (safety measure)
                setTimeout(() => {
                    hideLoading();
                    if (button) {
                        button.disabled = false;
                        const originalText = button.dataset.originalText || 'Download';
                        button.innerHTML = originalText;
                    }
                }, 30000); // 30 seconds timeout
            }
            
            // Auto-hide loading when page is fully loaded
            window.addEventListener('load', function() {
                setTimeout(hideLoading, 500);
            });
            
            // Hide loading when navigating away
            window.addEventListener('beforeunload', function() {
                hideLoading();
            });
            
            // Detect download completion by monitoring network requests
            let activeRequests = 0;
            
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                activeRequests++;
                showLoading('Memproses permintaan...');
                
                return originalFetch.apply(this, args)
                    .then(response => {
                        activeRequests--;
                        if (activeRequests === 0) {
                            setTimeout(hideLoading, 1000);
                        }
                        return response;
                    })
                    .catch(error => {
                        activeRequests--;
                        hideLoading();
                        throw error;
                    });
            };
            
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

            function closeMobileSidebar() {
                mobileSidebar.classList.add('hidden');
                mobileSidebar.classList.remove('translate-x-0');
                mobileSidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            }

            // Mobile sidebar events
            sidebarToggle.addEventListener('click', function() {
                mobileSidebar.classList.toggle('hidden');
                mobileSidebar.classList.toggle('-translate-x-full');
                mobileSidebar.classList.toggle('translate-x-0');
                sidebarOverlay.classList.toggle('hidden');
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
                showLoading('Logging out...');
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
                }
            });
            
            // Expose functions globally for use in other scripts
            window.showLoading = showLoading;
            window.hideLoading = hideLoading;
            window.handleDownload = handleDownload;
        });
        
        // Additional event listener to catch any unhandled download completions
        window.addEventListener('focus', function() {
            setTimeout(() => {
                const loadingSpinner = document.getElementById('loadingSpinner');
                if (loadingSpinner && loadingSpinner.classList.contains('active')) {
                    // If spinner is still active after window regains focus, 
                    // assume download is complete and hide spinner
                    loadingSpinner.classList.remove('active');
                }
            }, 2000);
        });
    </script>

    @stack('scripts')
</body>

</html>