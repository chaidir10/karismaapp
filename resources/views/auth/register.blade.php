<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - KARISMA</title>
    <link rel="icon" type="image/png" href="{{ $appLogoUrl ?? asset('public/images/favicon-48x48.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2E97D4">
    <link rel="apple-touch-icon" href="{{ $appLogoUrl ?? asset('public/pwa/icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg: #f0f4f8;
            --bg-pattern: rgba(46,151,212,0.03);
            --card: #ffffff;
            --card-border: rgba(0,0,0,0.06);
            --card-shadow: 0 8px 32px rgba(0,0,0,0.08);
            --text: #1e293b;
            --text-sub: #64748b;
            --text-muted: #94a3b8;
            --input-bg: #f8fafc;
            --input-border: #e2e8f0;
            --input-focus: #2E97D4;
            --primary: #2E97D4;
            --primary-hover: #2580b8;
            --primary-soft: rgba(46,151,212,0.08);
            --error: #ef4444;
            --error-bg: rgba(239,68,68,0.06);
            --success: #10b981;
            --success-bg: rgba(16,185,129,0.06);
            --divider: #e2e8f0;
        }
        [data-theme="dark"] {
            --bg: #0a0e14;
            --bg-pattern: rgba(90,182,234,0.02);
            --card: #111820;
            --card-border: rgba(255,255,255,0.06);
            --card-shadow: 0 8px 32px rgba(0,0,0,0.3);
            --text: #e2e8f0;
            --text-sub: #8b9ab5;
            --text-muted: #4b5c73;
            --input-bg: #0d1219;
            --input-border: rgba(255,255,255,0.08);
            --input-focus: #5AB6EA;
            --primary: #5AB6EA;
            --primary-hover: #4aa3d5;
            --primary-soft: rgba(90,182,234,0.1);
            --error: #f87171;
            --error-bg: rgba(248,113,113,0.08);
            --success: #34d399;
            --success-bg: rgba(52,211,153,0.08);
            --divider: rgba(255,255,255,0.06);
        }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color:transparent; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 16px;
            transition: background 0.3s;
            overflow-y: auto;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 20% 20%, var(--bg-pattern) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, var(--bg-pattern) 0%, transparent 50%);
            pointer-events: none;
        }

        .auth-wrapper { width:100%; max-width:420px; position:relative; z-index:1; margin:auto; }

        .auth-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: background 0.3s, border-color 0.3s;
        }

        .auth-header {
            padding: 24px 20px 16px;
            text-align: center;
        }
        .auth-logo {
            width: 52px; height: 52px;
            border-radius: 14px;
            margin: 0 auto 12px;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .auth-logo img { width:100%; height:100%; object-fit:contain; }
        .auth-logo-default {
            width:52px; height:52px; border-radius:14px;
            background: linear-gradient(135deg,#5AB6EA,#2E97D4);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-weight:800; font-size:20px;
        }
        .auth-title { font-size:20px; font-weight:700; color:var(--text); margin-bottom:4px; }
        .auth-subtitle { font-size:13px; color:var(--text-sub); }

        .auth-body { padding: 0 20px 24px; }

        .form-row { display:flex; gap:10px; }
        .form-row .form-group { flex:1; min-width:0; }

        .form-group { margin-bottom: 14px; }
        .form-label {
            display:block; font-size:11px; font-weight:600; color:var(--text-sub);
            margin-bottom:5px; text-transform:uppercase; letter-spacing:0.3px;
        }
        .input-wrap {
            position:relative; display:flex; align-items:center;
        }
        .input-icon {
            position:absolute; left:14px; color:var(--text-muted); font-size:14px;
            pointer-events:none; transition: color 0.2s;
        }
        .form-input {
            width:100%; padding:13px 14px 13px 42px;
            background:var(--input-bg); border:1.5px solid var(--input-border);
            border-radius:12px; font-size:16px; color:var(--text);
            outline:none; transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
            -webkit-appearance: none;
        }
        .form-input::placeholder { color:var(--text-muted); }
        .form-input:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }
        .form-input:focus ~ .input-icon { color: var(--input-focus); }
        input[type="number"].form-input { -moz-appearance:textfield; }
        input[type="number"].form-input::-webkit-inner-spin-button,
        input[type="number"].form-input::-webkit-outer-spin-button { -webkit-appearance:none; margin:0; }
        .input-toggle {
            position:absolute; right:10px;
            background:none; border:none; color:var(--text-muted);
            cursor:pointer; padding:8px; font-size:15px;
            min-width:44px; min-height:44px;
            display:flex; align-items:center; justify-content:center;
        }
        .input-toggle:hover { color:var(--text-sub); }

        .form-error {
            display:flex; align-items:center; gap:4px;
            font-size:11px; color:var(--error); margin-top:4px;
        }

        .status-msg {
            display:flex; align-items:center; gap:8px;
            padding:10px 14px; border-radius:10px; font-size:13px;
            margin-bottom:16px; background:var(--success-bg); color:var(--success);
            border-left:3px solid var(--success);
        }

        .btn-submit {
            width:100%; padding:14px; border:none; border-radius:12px;
            background:var(--primary); color:#fff; font-size:16px; font-weight:600;
            cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            font-family: inherit; margin-top:6px;
            min-height:50px;
            -webkit-appearance: none;
        }
        .btn-submit:hover { background:var(--primary-hover); box-shadow:0 4px 16px rgba(46,151,212,0.3); }
        .btn-submit:active { transform:scale(0.97); }

        .auth-footer {
            text-align:center; padding:16px 20px;
            border-top:1px solid var(--divider);
        }
        .auth-footer a {
            color:var(--primary); text-decoration:none; font-size:14px; font-weight:600;
            display:inline-block; padding:4px 0;
        }
        .auth-footer a:hover { text-decoration:underline; }

        .auth-copyright {
            text-align:center; margin-top:16px; padding-bottom:8px;
            font-size:11px; color:var(--text-muted);
        }

        .theme-toggle {
            position:fixed; top:12px; right:12px; z-index:50;
            width:40px; height:40px; border-radius:12px;
            background:var(--card); border:1px solid var(--card-border);
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
            display:flex; align-items:center; justify-content:center;
            cursor:pointer; color:var(--text-sub); font-size:16px;
            transition: background 0.2s, color 0.2s;
        }
        .theme-toggle:hover { color:var(--primary); }

        .section-divider {
            display:flex; align-items:center; gap:12px;
            margin:4px 0 12px; font-size:10px; font-weight:700;
            color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;
        }
        .section-divider::after {
            content:''; flex:1; height:1px; background:var(--divider);
        }

        /* Mobile-first: small phones */
        @media (max-width:380px) {
            body { padding:0; }
            .auth-wrapper { max-width:100%; }
            .auth-card { border-radius:0; border-left:0; border-right:0; }
        }
        /* Medium phones */
        @media (min-width:381px) and (max-width:480px) {
            body { padding:12px; }
            .auth-card { border-radius:16px; }
        }
        /* Always stack password row on phones */
        @media (max-width:480px) {
            .form-row { flex-direction:column; gap:0; }
        }
    </style>
    <script>
        (function(){var t=localStorage.getItem('karisma-theme')||'light';document.documentElement.setAttribute('data-theme',t);})();
    </script>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()" title="Tema">
        <i class="fas fa-moon"></i>
    </button>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    @if($appLogoUrl)
                        <img src="{{ $appLogoUrl }}" alt="Logo">
                    @else
                        <div class="auth-logo-default">K</div>
                    @endif
                </div>
                <div class="auth-title">Daftar Akun</div>
                <div class="auth-subtitle">Buat akun KARISMA untuk mulai presensi</div>
            </div>

            <div class="auth-body">
                @if(session('status'))
                <div class="status-msg">
                    <i class="fas fa-circle-check"></i> {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="section-divider">Data Pegawai</div>

                    <div class="form-group">
                        <label class="form-label" for="nip">NIP</label>
                        <div class="input-wrap">
                            <input id="nip" class="form-input" type="number" name="nip" value="{{ old('nip') }}" required autofocus placeholder="Masukkan NIP">
                            <i class="input-icon fas fa-id-card"></i>
                        </div>
                        @if($errors->has('nip'))
                        <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('nip') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <div class="input-wrap">
                            <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                            <i class="input-icon fas fa-user"></i>
                        </div>
                        @if($errors->has('name'))
                        <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('name') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="jabatan">Jabatan</label>
                        <div class="input-wrap">
                            <input id="jabatan" class="form-input" type="text" name="jabatan" value="{{ old('jabatan') }}" required placeholder="Masukkan jabatan">
                            <i class="input-icon fas fa-briefcase"></i>
                        </div>
                        @if($errors->has('jabatan'))
                        <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('jabatan') }}</div>
                        @endif
                    </div>

                    <div class="section-divider">Akun</div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-wrap">
                            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com">
                            <i class="input-icon fas fa-envelope"></i>
                        </div>
                        @if($errors->has('email'))
                        <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-wrap">
                                <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 karakter" style="padding-right:36px;">
                                <i class="input-icon fas fa-lock"></i>
                                <button type="button" class="input-toggle" onclick="togglePw('password',this)"><i class="fas fa-eye"></i></button>
                            </div>
                            @if($errors->has('password'))
                            <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('password') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Konfirmasi</label>
                            <div class="input-wrap">
                                <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" style="padding-right:36px;">
                                <i class="input-icon fas fa-lock"></i>
                                <button type="button" class="input-toggle" onclick="togglePw('password_confirmation',this)"><i class="fas fa-eye"></i></button>
                            </div>
                            @if($errors->has('password_confirmation'))
                            <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('password_confirmation') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="kode_instansi">Kode Instansi</label>
                        <div class="input-wrap">
                            <input id="kode_instansi" class="form-input" type="text" name="kode_instansi" value="{{ old('kode_instansi') }}" required placeholder="Masukkan kode instansi">
                            <i class="input-icon fas fa-building"></i>
                        </div>
                        @if($errors->has('kode_instansi'))
                        <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('kode_instansi') }}</div>
                        @endif
                    </div>

                    <button type="submit" class="btn-submit">
                        Daftar <i class="fas fa-user-plus"></i>
                    </button>
                </form>
            </div>

            <div class="auth-footer">
                <a href="{{ route('login') }}">Sudah punya akun? Masuk</a>
            </div>
        </div>

        <div class="auth-copyright">&copy; {{ date('Y') }} KARISMA &mdash; Sistem Presensi Digital</div>
    </div>

    <script>
        function toggleTheme() {
            var t = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', t);
            localStorage.setItem('karisma-theme', t);
            updateThemeIcon();
        }
        function updateThemeIcon() {
            var icon = document.querySelector('.theme-toggle i');
            icon.className = document.documentElement.getAttribute('data-theme') === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
        updateThemeIcon();

        function togglePw(id, btn) {
            var inp = document.getElementById(id);
            var ico = btn.querySelector('i');
            if (inp.type === 'password') { inp.type = 'text'; ico.className = 'fas fa-eye-slash'; }
            else { inp.type = 'password'; ico.className = 'fas fa-eye'; }
        }

        // Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(function(){});
        }
    </script>
</body>
</html>
