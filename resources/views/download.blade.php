<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download KARISMA - Aplikasi Presensi ASN</title>

    {{-- Manifest & PWA --}}
    <link rel="manifest" href="{{ asset('public/pwa/manifest.json') }}">
    <meta name="theme-color" content="#0EA5E9">
    <link rel="apple-touch-icon" href="{{ asset('public/pwa/icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font untuk estetika modern --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .app-store-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .app-icon {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 22px;
        }
        .install-btn {
            background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
            box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.3), 0 2px 4px -1px rgba(14, 165, 233, 0.2);
            transition: all 0.3s ease;
        }
        .install-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3), 0 4px 6px -2px rgba(14, 165, 233, 0.2);
        }
        .feature-item {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        
        /* Custom Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.3s ease;
        }
        .modal-show {
            transform: scale(1);
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-50 to-blue-100 flex items-center justify-center min-h-screen p-4">
    <div class="app-store-card rounded-3xl p-8 max-w-lg w-full">
        {{-- Header dengan logo dan judul --}}
        <div class="flex flex-col items-center mb-8">
            <img src="{{ asset('public/images/icon-512x512.png') }}" alt="Logo KARISMA" class="app-icon w-28 h-28 mb-6">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">KARISMA</h1>
            <p class="text-slate-600 text-lg">Presensi ASN</p>
        </div>

        {{-- Rating dan info --}}
        <div class="flex justify-center items-center mb-8">
            <div class="flex text-amber-400 mr-2">
                <!-- Star icons -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <!-- Repeat 4 more times -->
            </div>
            <span class="text-slate-700 font-medium">4.8 • 500+ unduhan</span>
        </div>

        {{-- Tombol install --}}
        <button id="installBtn" class="install-btn w-full text-white font-semibold py-4 rounded-xl mb-4 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            Download / Install Aplikasi
        </button>
    </div>

    <!-- Custom Install Modal -->
    <div id="installModal" class="modal-overlay">
        <div class="modal-content">
            <div class="text-center mb-6">
                <div class="bg-blue-100 rounded-full p-3 inline-flex mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Install KARISMA</h3>
                <p class="text-slate-600">
                    Tambahkan KARISMA ke perangkat Anda untuk pengalaman yang lebih baik dan akses cepat.
                </p>
            </div>
            
            <div class="bg-slate-50 rounded-xl p-4 mb-6">
                <div class="flex items-center mb-3">
                    <img src="{{ asset('public/images/icon-512x512.png') }}" alt="Logo KARISMA" class="w-10 h-10 rounded-xl mr-3">
                    <div>
                        <h4 class="font-semibold text-slate-800">KARISMA</h4>
                        <p class="text-sm text-slate-600">Presensi ASN</p>
                    </div>
                </div>
                <div class="text-sm text-slate-600 space-y-1">
                    <div class="flex justify-between">
                        <span>Ukuran:</span>
                        <span class="font-medium">~2 MB</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Versi:</span>
                        <span class="font-medium">1.0.0</span>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button id="cancelInstall" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium py-3 rounded-xl transition">
                    Batal
                </button>
                <button id="confirmInstall" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-xl transition">
                    Install
                </button>
            </div>
        </div>
    </div>

    <script>
        let deferredPrompt;
        let installModal = document.getElementById('installModal');
        let installBtn = document.getElementById('installBtn');
        let confirmInstallBtn = document.getElementById('confirmInstall');
        let cancelInstallBtn = document.getElementById('cancelInstall');

        // Fungsi untuk menampilkan modal
        function showModal() {
            installModal.style.display = 'flex';
            setTimeout(() => {
                document.querySelector('.modal-content').classList.add('modal-show');
            }, 10);
        }

        // Fungsi untuk menyembunyikan modal
        function hideModal() {
            document.querySelector('.modal-content').classList.remove('modal-show');
            setTimeout(() => {
                installModal.style.display = 'none';
            }, 300);
        }

        window.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Tampilkan tombol install
            installBtn.style.display = "flex";
        });

        // Ketika tombol install diklik, tampilkan modal custom
        installBtn.addEventListener("click", () => {
            if (deferredPrompt) {
                showModal();
            } else {
                alert("Aplikasi mungkin sudah terpasang, atau browser tidak mendukung. Gunakan menu browser → 'Tambahkan ke layar utama'.");
            }
        });

        // Konfirmasi install dari modal custom
        confirmInstallBtn.addEventListener("click", async () => {
            hideModal();
            
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log("User choice:", outcome);
                
                if (outcome === 'accepted') {
                    installBtn.style.display = "none";
                    // Bisa tambahkan notifikasi sukses di sini
                    showInstallSuccess();
                }
                
                deferredPrompt = null;
            }
        });

        // Batal install
        cancelInstallBtn.addEventListener("click", hideModal);

        // Tutup modal ketika klik di luar
        installModal.addEventListener("click", (e) => {
            if (e.target === installModal) {
                hideModal();
            }
        });

        // Sembunyikan tombol jika aplikasi sudah diinstal
        window.addEventListener('appinstalled', () => {
            installBtn.style.display = "none";
            deferredPrompt = null;
            hideModal();
        });

        // Fungsi untuk menampilkan notifikasi sukses (opsional)
        function showInstallSuccess() {
            // Implementasi notifikasi sukses install
            console.log("Aplikasi berhasil diinstall!");
        }
    </script>
</body>
</html>