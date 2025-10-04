<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KARISMA</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('public/images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon-48x48.png') }}" type="image/png">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('public/images/favicon-512x512.png') }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon-512x512.png') }}" type="image/png">

    <!-- PWA -->
    <link rel="manifest" href="{{ asset('public/pwa/manifest.json') }}">
    <meta name="theme-color" content="#5AB6EA">
    <link rel="apple-touch-icon" href="{{ asset('pwa/icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%); min-height: 100vh; }
    </style>
</head>

<body class="flex items-center justify-center p-5">

    <!-- Login Container -->
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-400 text-white p-8 text-center">
            <div class="flex items-center justify-center mb-1">
                <div class="w-12 h-12 bg-white/600 flex items-center justify-center mr-3 overflow-hidden">
                    <img src="{{ asset('public/images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div class="text-2xl font-bold">KARISMA</div>
            </div>
            <p class="text-sm font-bold opacity-90">Presnsi Digital BKK Tarakan</p>
        </div>

        <!-- Body -->
        <div class="p-8">
            @if(session('status'))
                <div id="successMessage" class="hidden">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email -->
                <div class="mb-5">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="Masukkan email Anda"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    @if($errors->has('email'))
                        <div class="text-red-500 text-xs mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $errors->first('email') }}
                        </div>
                    @endif
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="Masukkan password Anda"
                               class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <button type="button" id="password-toggle" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @if($errors->has('password'))
                        <div class="text-red-500 text-xs mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $errors->first('password') }}
                        </div>
                    @endif
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center transition transform hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                    Masuk <i class="fas fa-arrow-right ml-2"></i>
                </button>

                <a href="{{ route('register') }}" class="block text-center text-blue-600 hover:text-blue-800 font-semibold mt-4 transition">
                    Daftar Akun Baru
                </a>
            </form>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-xs mt-6">
                &copy; {{ date('Y') }} KARISMA - Sistem Presensi Digital
            </div>
        </div>
    </div>

    <!-- PWA Install Button -->
    <button id="installBtn" class="hidden fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg">
        Install App
    </button>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 transform transition-all duration-300 scale-95">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Pendaftaran Berhasil!</h3>
                <p id="modalMessage" class="text-gray-600 mb-6">Akun Anda telah berhasil dibuat. Silakan masuk dengan kredensial Anda.</p>
                <button id="closeModal" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold transition">
                    Lanjutkan ke Login
                </button>
            </div>
        </div>
    </div>

    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset("pwa/service-worker.js") }}')
                    .then(registration => console.log('ServiceWorker registered:', registration))
                    .catch(err => console.log('ServiceWorker registration failed:', err));
            });
        }

        // Toggle password visibility
        document.getElementById('password-toggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Show success modal
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('successMessage');
            const modal = document.getElementById('successModal');
            const modalMessage = document.getElementById('modalMessage');
            const closeModal = document.getElementById('closeModal');

            if (successMessage) {
                modalMessage.textContent = successMessage.textContent;
                modal.classList.remove('hidden');
                setTimeout(() => modal.querySelector('.transform').classList.remove('scale-95'), 10);
            }

            closeModal.addEventListener('click', () => {
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(() => modal.classList.add('hidden'), 300);
            });
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.querySelector('.transform').classList.add('scale-95');
                    setTimeout(() => modal.classList.add('hidden'), 300);
                }
            });
        });

        // PWA Install Prompt
        let deferredPrompt;
        const installBtn = document.getElementById('installBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installBtn.classList.remove('hidden');

            installBtn.addEventListener('click', async () => {
                installBtn.classList.add('hidden');
                deferredPrompt.prompt();
                const choice = await deferredPrompt.userChoice;
                console.log('User choice:', choice.outcome);
                deferredPrompt = null;
            });
        });
    </script>
</body>

</html>
