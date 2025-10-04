<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download KARISMA</title>

    {{-- Manifest & PWA --}}
    <link rel="manifest" href="{{ asset('public/pwa/manifest.json') }}">
    <meta name="theme-color" content="#2E97D4">
    <link rel="apple-touch-icon" href="{{ asset('public/pwa/icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-xl rounded-2xl p-8 max-w-md w-full text-center">
        <img src="{{ asset('public/images/favicon-512x512.png') }}" alt="KARISMA" class="mx-auto w-24 h-24 mb-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">KARISMA</h1>
        <p class="text-gray-600 mb-6">
            Aplikasi Presensi ASN berbasis PWA.<br>
            Klik tombol di bawah untuk menginstal aplikasi.
        </p>

        <button id="installBtn"
            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl shadow-md transition">
            📲 Download / Install Aplikasi
        </button>

        <p class="mt-4 text-sm text-gray-500">
            Jika tombol install tidak muncul, silakan gunakan fitur browser<br>
            <span class="font-semibold">"Tambahkan ke layar utama"</span>.
        </p>
    </div>

    <script>
        let deferredPrompt;

        window.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });

        document.getElementById("installBtn").addEventListener("click", async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log("User choice:", outcome);
                deferredPrompt = null;
            } else {
                alert("Aplikasi mungkin sudah terpasang, atau browser tidak mendukung. Gunakan menu browser → 'Tambahkan ke layar utma'.");
            }
        });
    </script>
</body>
</html>
