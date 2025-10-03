<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KARISMA</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon-48x48.png') }}" type="image/png">

    <link rel="manifest" href="{{ asset('pwa/manifest.json') }}">
    <meta name="theme-color" content="#5AB6EA">
    <link rel="apple-touch-icon" href="{{ asset('pwa/icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --success: #4bb543;
            --error: #f44336;
            --border-radius: 8px;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 22px;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
        }

        .tagline {
            font-size: 14px;
            opacity: 0.9;
        }

        .register-body {
            padding: 30px;
        }

        .status-message {
            padding: 12px 16px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .status-success {
            background: rgba(75, 181, 67, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .status-error {
            background: rgba(244, 67, 54, 0.1);
            color: var(--error);
            border-left: 4px solid var(--error);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: var(--dark);
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 18px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 1.5px solid var(--gray-light);
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
            background-color: white;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 18px;
        }

        .register-button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .register-button i {
            margin-left: 8px;
        }

        .input-error {
            color: var(--error);
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
        }

        .input-error i {
            margin-right: 6px;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: var(--gray);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .login-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .register-container {
                max-width: 100%;
            }

            .register-header {
                padding: 25px 20px;
            }

            .register-body {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <!-- Header -->
        <div class="register-header">
            <div class="logo">
                <div class="w-12 h-12 bg-white/600 flex items-center justify-center mr-3 overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div class="logo-text">KARISMA</div>
            </div>
            <p class="tagline">Daftar akun baru KARISMA
</p>
        </div>

        <!-- Body -->
        <div class="register-body">
            <!-- Session Status -->
            @if(session('status'))
            <div class="status-message status-success">
                <i class="fas fa-info-circle" style="margin-right: 10px;"></i>
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- NIP -->
                <div class="form-group">
                    <label class="form-label" for="nip">NIP</label>
                    <div class="input-with-icon">
                        <i class="input-icon fas fa-id-card"></i>
                        <input id="nip" class="form-input" type="number" name="nip" value="{{ old('nip') }}" required autofocus placeholder="Masukkan NIP">
                    </div>
                    @if($errors->has('nip'))
                    <div class="input-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('nip') }}
                    </div>
                    @endif
                </div>

                <!-- Name -->
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <div class="input-with-icon">
                        <i class="input-icon fas fa-user"></i>
                        <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                    </div>
                    @if($errors->has('name'))
                    <div class="input-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('name') }}
                    </div>
                    @endif
                </div>

                <!-- Jabatan -->
                <div class="form-group">
                    <label class="form-label" for="jabatan">Jabatan</label>
                    <div class="input-with-icon">
                        <i class="input-icon fas fa-briefcase"></i>
                        <input id="jabatan" class="form-input" type="text" name="jabatan" value="{{ old('jabatan') }}" required placeholder="Masukkan jabatan">
                    </div>
                    @if($errors->has('jabatan'))
                    <div class="input-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('jabatan') }}
                    </div>
                    @endif
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-with-icon">
                        <i class="input-icon fas fa-envelope"></i>
                        <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Masukkan email">
                    </div>
                    @if($errors->has('email'))
                    <div class="input-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('email') }}
                    </div>
                    @endif
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="input-icon fas fa-lock"></i>
                        <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" placeholder="Masukkan password">
                        <button type="button" class="password-toggle" id="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @if($errors->has('password'))
                    <div class="input-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('password') }}
                    </div>
                    @endif
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-with-icon">
                        <i class="input-icon fas fa-lock"></i>
                        <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi password">
                        <button type="button" class="password-toggle" id="password-confirm-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @if($errors->has('password_confirmation'))
                    <div class="input-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('password_confirmation') }}
                    </div>
                    @endif
                </div>

                <!-- Register Button -->
                <button type="submit" class="register-button">
                    Daftar <i class="fas fa-user-plus"></i>
                </button>

                <!-- Login Link -->
                <div class="login-link">
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
                </div>
            </form>

            <!-- Footer -->
            <div class="footer">
                &copy; {{ date('Y') }} KARISMA - Sistem Presensi Digital
            </div>
        </div>
    </div>


    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset("pwa/service-worker.js") }}')
                    .then(registration => {
                        console.log('ServiceWorker registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('ServiceWorker registration failed: ', registrationError);
                    });
            });
        }
    </script>

    <script>
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

        // Toggle confirm password visibility
        document.getElementById('password-confirm-toggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password_confirmation');
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
    </script>
</body>

</html>