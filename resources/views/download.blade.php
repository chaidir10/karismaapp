<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Aplikasi KARISMA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-6 rounded-2xl shadow-lg text-center w-80">
        <h1 class="text-xl font-bold text-gray-700 mb-4">📲 Download Aplikasi KARISMA</h1>
        
        <p class="text-gray-600 mb-4">Pasang aplikasi untuk pengalaman lebih baik di perangkat Anda.</p>
        
        <button id="btnInstall" 
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-xl hover:bg-blue-700 transition">
            Download Aplikasi
        </button>

        <p id="installStatus" class="text-gray-500 text-sm mt-3"></p>
    </div>

    <script>
        let deferredPrompt;

        // Tangkap event sebelum install
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('installStatus').innerText = "Aplikasi siap dipasang.";
        });

        // Klik tombol download
        document.getElementById('btnInstall').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    document.getElementById('installStatus').innerText = "✅ Aplikasi berhasil dipasang.";
                } else {
                    document.getElementById('installStatus').innerText = "❌ Pemasangan dibatalkan.";
                }
                deferredPrompt = null;
            } else {
                // fallback jika app sudah terpasang
                document.getElementById('installStatus').innerText = "ℹ️ Aplikasi sudah terpasang atau browser tidak mendukung.";
            }
        });
    </script>

</body>
</html>
