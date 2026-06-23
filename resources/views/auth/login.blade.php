<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KARISMA</title>
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
            align-items: center;
            justify-content: center;
            padding: 16px;
            transition: background 0.3s;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 20% 20%, var(--bg-pattern) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, var(--bg-pattern) 0%, transparent 50%);
            pointer-events: none;
        }

        .auth-wrapper { width:100%; max-width:400px; position:relative; z-index:1; }

        .auth-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: background 0.3s, border-color 0.3s;
        }

        .auth-header {
            padding: 32px 24px 20px;
            text-align: center;
        }
        .auth-logo {
            width: 60px; height: 60px;
            border-radius: 16px;
            margin: 0 auto 14px;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .auth-logo img { width:100%; height:100%; object-fit:contain; }
        .auth-logo-default {
            width:60px; height:60px; border-radius:16px;
            background: linear-gradient(135deg,#5AB6EA,#2E97D4);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-weight:800; font-size:24px;
        }
        .auth-title { font-size:22px; font-weight:700; color:var(--text); margin-bottom:4px; }
        .auth-subtitle { font-size:13px; color:var(--text-sub); }

        .auth-body { padding: 0 24px 28px; }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display:block; font-size:12px; font-weight:600; color:var(--text-sub);
            margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px;
        }
        .input-wrap {
            position:relative; display:flex; align-items:center;
        }
        .input-icon {
            position:absolute; left:14px; color:var(--text-muted); font-size:15px;
            pointer-events:none; transition: color 0.2s;
        }
        .form-input {
            width:100%; padding:14px 14px 14px 44px;
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
        .input-toggle {
            position:absolute; right:12px;
            background:none; border:none; color:var(--text-muted);
            cursor:pointer; padding:8px; font-size:16px;
            min-width:44px; min-height:44px;
            display:flex; align-items:center; justify-content:center;
        }
        .input-toggle:hover { color:var(--text-sub); }

        .form-error {
            display:flex; align-items:center; gap:5px;
            font-size:12px; color:var(--error); margin-top:6px;
        }

        .btn-submit {
            width:100%; padding:15px; border:none; border-radius:12px;
            background:var(--primary); color:#fff; font-size:16px; font-weight:600;
            cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            font-family: inherit;
            min-height:50px;
            -webkit-appearance: none;
        }
        .btn-submit:hover { background:var(--primary-hover); box-shadow:0 4px 16px rgba(46,151,212,0.3); }
        .btn-submit:active { transform:scale(0.97); }

        .auth-footer {
            text-align:center; padding:18px 24px;
            border-top:1px solid var(--divider);
        }
        .auth-footer a {
            color:var(--primary); text-decoration:none; font-size:14px; font-weight:600;
            display:inline-block; padding:4px 0;
        }
        .auth-footer a:hover { text-decoration:underline; }

        .auth-copyright {
            text-align:center; margin-top:16px;
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

        /* Success modal */
        .modal-overlay {
            position:fixed; inset:0; z-index:100;
            background:rgba(0,0,0,0.4); display:flex;
            align-items:flex-end; justify-content:center; padding:0;
            opacity:0; transition: opacity 0.2s;
        }
        .modal-overlay.active { opacity:1; }
        .modal-box {
            background:var(--card); border:1px solid var(--card-border);
            border-radius:20px 20px 0 0; padding:28px 24px 32px;
            padding-bottom: calc(32px + env(safe-area-inset-bottom, 0px));
            max-width:480px; width:100%;
            text-align:center; transform:translateY(100%); opacity:0;
            transition: transform 0.25s ease-out, opacity 0.2s;
        }
        .modal-overlay.active .modal-box { transform:translateY(0); opacity:1; }
        .modal-handle {
            width:36px; height:4px; border-radius:2px; background:var(--divider);
            margin:0 auto 20px;
        }
        .modal-icon {
            width:56px; height:56px; border-radius:50%; margin:0 auto 14px;
            background:var(--success-bg); color:var(--success);
            display:flex; align-items:center; justify-content:center; font-size:24px;
        }
        .modal-title { font-size:18px; font-weight:700; color:var(--text); margin-bottom:6px; }
        .modal-desc { font-size:13px; color:var(--text-sub); margin-bottom:24px; }

        /* Mobile-first: small phones */
        @media (max-width:380px) {
            body { padding:0; }
            .auth-wrapper { max-width:100%; }
            .auth-card { border-radius:0; border-left:0; border-right:0; min-height:100dvh; display:flex; flex-direction:column; justify-content:center; }
            .auth-copyright { position:fixed; bottom:12px; left:0; right:0; }
        }
        /* Medium phones */
        @media (min-width:381px) and (max-width:480px) {
            body { padding:12px; }
            .auth-card { border-radius:16px; }
        }
        /* Desktop */
        @media (min-width:481px) {
            .modal-overlay { align-items:center; padding:20px; }
            .modal-box { border-radius:20px; max-width:360px; transform:translateY(12px); padding-bottom:32px; }
            .modal-handle { display:none; }
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
                <div class="auth-title">KARISMA</div>
                <div class="auth-subtitle">Presensi Digital BKK Tarakan</div>
            </div>

            <div class="auth-body">
                @if(session('status'))
                <div id="successMessage" style="display:none;">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-wrap">
                            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
                            <i class="input-icon fas fa-envelope"></i>
                        </div>
                        @if($errors->has('email'))
                        <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrap">
                            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password" style="padding-right:42px;">
                            <i class="input-icon fas fa-lock"></i>
                            <button type="button" class="input-toggle" onclick="togglePw('password',this)"><i class="fas fa-eye"></i></button>
                        </div>
                        @if($errors->has('password'))
                        <div class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    <button type="submit" class="btn-submit">
                        Masuk <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <div class="auth-footer">
                <a href="{{ route('register') }}">Belum punya akun? Daftar</a>
            </div>
        </div>

        <div class="auth-copyright">&copy; {{ date('Y') }} KARISMA &mdash; Sistem Presensi Digital</div>
    </div>

    <!-- PWA Install -->
    <button id="installBtn" style="display:none; position:fixed; bottom:16px; right:16px; z-index:50; padding:10px 18px; border-radius:12px; border:none; background:var(--primary); color:#fff; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 4px 16px rgba(46,151,212,0.3);">
        <i class="fas fa-download" style="margin-right:6px;"></i> Install App
    </button>

    <!-- Success Modal -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-handle"></div>
            <div class="modal-icon"><i class="fas fa-check"></i></div>
            <div class="modal-title">Pendaftaran Berhasil!</div>
            <div class="modal-desc" id="modalMessage">Akun Anda berhasil dibuat. Silakan masuk.</div>
            <button onclick="closeSuccessModal()" class="btn-submit">Lanjutkan</button>
        </div>
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

        // Success modal
        var msgEl = document.getElementById('successMessage');
        if (msgEl) {
            document.getElementById('modalMessage').textContent = msgEl.textContent;
            var modal = document.getElementById('successModal');
            modal.style.display = 'flex';
            requestAnimationFrame(function(){ modal.classList.add('active'); });
        }
        function closeSuccessModal() {
            var modal = document.getElementById('successModal');
            modal.classList.remove('active');
            setTimeout(function(){ modal.style.display='none'; }, 200);
        }
        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) closeSuccessModal();
        });

        // Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(function(){});
        }

        // PWA Install
        var deferredPrompt;
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('installBtn').style.display = 'block';
        });
        document.getElementById('installBtn').addEventListener('click', function() {
            if (deferredPrompt) { deferredPrompt.prompt(); deferredPrompt = null; }
            this.style.display = 'none';
        });
    </script>
</body>
</html>
