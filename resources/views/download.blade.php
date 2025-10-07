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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .app-store-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        .app-icon {
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            border-radius: 22px;
        }
        .install-btn {
            background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
            box-shadow: 0 4px 6px -1px rgba(14,165,233,0.3);
            transition: all 0.3s ease;
        }
        .install-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(14,165,233,0.3);
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center; justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.3s ease;
        }
        .modal-show { transform: scale(1); opacity: 1; }
    </style>
</head>

<body class="bg-gradient-to-br from-sky-50 to-blue-100 flex items-center justify-center min-h-screen p-4">
    <div class="app-store-card rounded-3xl p-8 max-w-lg w-full">
        <div class="flex flex-col items-center mb-8">
            <img src="{{ asset('public/images/icon-512x512.png') }}" alt="Logo KARISMA" class="app-icon w-28 h-28 mb-6">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">KARISMA</h1>
            <p class="text-slate-600 text-lg">Presensi ASN</p>
        </div>

        <button id="installBtn" class="install-btn w-full text-white font-semibold py-4 rounded-xl mb-4 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
            Download / Install Aplikasi
        </button>
    </div>

    <!-- Modal Install (Chrome) -->
    <div id="installModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="text-xl font-bold text-slate-800 mb-2">Install KARISMA</h3>
            <p class="text-slate-600 mb-6">
                Tambahkan aplikasi ke perangkat Anda untuk akses cepat dan pengalaman lebih baik.
            </p>
            <div class="flex space-x-3">
                <button id="cancelInstall" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium py-3 rounded-xl transition">Batal</button>
                <button id="confirmInstall" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-xl transition">Install</button>
            </div>
        </div>
    </div>

    <!-- Modal Safari (Manual Instruction) -->
    <div id="safariModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="text-xl font-bold text-slate-800 mb-3">Tambahkan ke Layar Utama</h3>
            <p class="text-slate-600 mb-4">
                Untuk menginstal di iPhone atau iPad:
            </p>
            <ol class="text-left text-slate-700 space-y-2 text-sm">
                <li>1️⃣ Tekan ikon <strong><i class="fa-solid fa-arrow-up-from-bracket"></i> Bagikan</strong> di Safari</li>
                <li>2️⃣ Gulir ke bawah, pilih <strong>Tambahkan ke Layar Utama</strong></li>
                <li>3️⃣ Tekan <strong>Tambahkan</strong> untuk menginstal</li>
            </ol>
            <button id="closeSafariModal" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl transition">Tutup</button>
        </div>
    </div>

    <script>
        let deferredPrompt;
        const installBtn = document.getElementById('installBtn');
        const installModal = document.getElementById('installModal');
        const safariModal = document.getElementById('safariModal');
        const confirmInstallBtn = document.getElementById('confirmInstall');
        const cancelInstallBtn = document.getElementById('cancelInstall');
        const closeSafariModal = document.getElementById('closeSafariModal');

        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

        function showModal(modal) {
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content').classList.add('modal-show'), 10);
        }
        function hideModal(modal) {
            modal.querySelector('.modal-content').classList.remove('modal-show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        if (isSafari) {
            // Safari → tampilkan panduan manual
            installBtn.addEventListener('click', () => showModal(safariModal));
            closeSafariModal.addEventListener('click', () => hideModal(safariModal));
        } else {
            // Chrome / Edge / Brave → gunakan beforeinstallprompt
            window.addEventListener("beforeinstallprompt", (e) => {
                e.preventDefault();
                deferredPrompt = e;
            });

            installBtn.addEventListener("click", () => {
                if (deferredPrompt) showModal(installModal);
                else alert("Gunakan menu browser → 'Tambahkan ke layar utama'");
            });

            confirmInstallBtn.addEventListener("click", async () => {
                hideModal(installModal);
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === "accepted") {
                    installBtn.textContent = "Aplikasi Terinstal ✅";
                    installBtn.disabled = true;
                }
                deferredPrompt = null;
            });

            cancelInstallBtn.addEventListener("click", () => hideModal(installModal));

            window.addEventListener("appinstalled", () => {
                installBtn.textContent = "Aplikasi Terinstal ✅";
                hideModal(installModal);
            });
        }
    </script>
</body>
</html>
