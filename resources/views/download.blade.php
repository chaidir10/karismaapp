<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download KARISMA - Aplikasi Presensi ASN</title>

    {{-- Manifest & PWA --}}
    <link rel="manifest" href="{{ asset('public/pwa/manifest.json') }}">
    <meta name="theme-color" content="#5AB6EA">
    <link rel="icon" type="image/png" href="{{ $appLogoUrl ?? asset('public/images/favicon-48x48.png') }}">
    <link rel="apple-touch-icon" href="{{ $appLogoUrl ?? asset('public/pwa/icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins',sans-serif;
            min-height:100vh;
            background:#0b0f1a;
            color:#e2e8f0;
            display:flex;
            flex-direction:column;
            align-items:center;
            overflow-x:hidden;
        }

        /* Hero */
        .hero {
            width:100%;
            padding:60px 24px 40px;
            text-align:center;
            position:relative;
            overflow:hidden;
        }
        .hero::before {
            content:'';
            position:absolute; inset:0;
            background:radial-gradient(ellipse 600px 400px at 50% 0%, rgba(90,182,234,0.15), transparent);
            pointer-events:none;
        }
        .app-icon {
            width:100px; height:100px;
            border-radius:24px;
            box-shadow:0 20px 40px rgba(90,182,234,0.25), 0 0 0 1px rgba(255,255,255,0.08);
            margin:0 auto 20px;
            position:relative;
        }
        .hero h1 {
            font-size:32px; font-weight:800;
            background:linear-gradient(135deg,#5AB6EA,#FEAA2B);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            background-clip:text;
            margin-bottom:6px;
        }
        .hero p { font-size:14px; color:#94a3b8; font-weight:500; }
        .hero .version {
            display:inline-block; margin-top:10px;
            padding:4px 14px; border-radius:20px;
            background:rgba(90,182,234,0.1); border:1px solid rgba(90,182,234,0.2);
            font-size:11px; font-weight:600; color:#5AB6EA;
        }

        /* Install Button */
        .install-btn {
            display:flex; align-items:center; justify-content:center; gap:10px;
            width:calc(100% - 48px); max-width:400px;
            margin:0 auto 32px;
            padding:16px 24px;
            border:none; border-radius:16px;
            background:linear-gradient(135deg,#5AB6EA,#2E97D4);
            color:#fff; font-size:16px; font-weight:700;
            cursor:pointer;
            box-shadow:0 8px 24px rgba(90,182,234,0.3);
            transition:all 0.3s;
            -webkit-tap-highlight-color:transparent;
        }
        .install-btn:active { transform:scale(0.97); }
        .install-btn:disabled { opacity:0.6; cursor:default; }
        .install-btn i { font-size:18px; }

        /* Features */
        .features {
            width:100%; max-width:440px;
            padding:0 24px;
            display:grid; grid-template-columns:1fr 1fr; gap:12px;
            margin-bottom:32px;
        }
        .feat {
            background:rgba(255,255,255,0.04);
            border:1px solid rgba(255,255,255,0.06);
            border-radius:14px;
            padding:16px 14px;
            text-align:center;
        }
        .feat i {
            font-size:20px; margin-bottom:8px; display:block;
            color:#5AB6EA;
        }
        .feat span { font-size:11px; font-weight:600; color:#94a3b8; }

        /* Guide Section */
        .guide-section {
            width:100%; max-width:440px;
            padding:0 24px 40px;
        }
        .guide-title {
            font-size:13px; font-weight:700; color:#64748b;
            text-transform:uppercase; letter-spacing:1px;
            margin-bottom:16px; text-align:center;
        }

        .guide-tabs {
            display:flex; gap:8px; margin-bottom:16px;
            background:rgba(255,255,255,0.04);
            border-radius:12px; padding:4px;
        }
        .guide-tab {
            flex:1; padding:10px; border:none; border-radius:10px;
            background:transparent; color:#64748b;
            font-size:13px; font-weight:600; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:8px;
            transition:all 0.2s;
            -webkit-tap-highlight-color:transparent;
        }
        .guide-tab.active {
            background:rgba(90,182,234,0.12);
            color:#5AB6EA;
        }
        .guide-tab i { font-size:16px; }

        .guide-panel { display:none; }
        .guide-panel.active { display:block; }

        .step {
            display:flex; gap:14px; align-items:flex-start;
            padding:14px 0;
            border-bottom:1px solid rgba(255,255,255,0.04);
        }
        .step:last-child { border-bottom:none; }
        .step-num {
            width:32px; height:32px; border-radius:10px;
            background:linear-gradient(135deg,#5AB6EA,#2E97D4);
            color:#fff; font-size:13px; font-weight:700;
            display:flex; align-items:center; justify-content:center;
            flex-shrink:0;
        }
        .step-text { flex:1; }
        .step-text strong { color:#e2e8f0; font-weight:600; }
        .step-text p { font-size:13px; color:#94a3b8; line-height:1.5; margin:0; }
        .step-text .hint {
            display:inline-flex; align-items:center; gap:4px;
            margin-top:6px; padding:3px 10px;
            background:rgba(255,255,255,0.05);
            border-radius:8px; font-size:11px; color:#64748b;
        }
        .step-text .hint i { font-size:12px; }

        /* Footer */
        .footer {
            text-align:center; padding:20px 24px 40px;
            font-size:11px; color:#334155;
        }
        .footer a { color:#5AB6EA; text-decoration:none; }

        /* Modal */
        .modal-overlay {
            display:none; position:fixed; inset:0; z-index:1000;
            background:rgba(0,0,0,0.6); backdrop-filter:blur(6px);
            align-items:center; justify-content:center;
        }
        .modal-card {
            background:#141b2d; border:1px solid rgba(255,255,255,0.06);
            border-radius:20px; padding:28px; width:90%; max-width:360px;
            text-align:center;
            transform:scale(0.9); opacity:0;
            transition:all 0.3s;
        }
        .modal-card.show { transform:scale(1); opacity:1; }
        .modal-card h3 { font-size:18px; font-weight:700; color:#e2e8f0; margin-bottom:8px; }
        .modal-card p { font-size:13px; color:#94a3b8; margin-bottom:20px; line-height:1.5; }
        .modal-btns { display:flex; gap:10px; }
        .modal-btns button {
            flex:1; padding:12px; border-radius:12px; border:none;
            font-size:14px; font-weight:600; cursor:pointer;
        }
        .btn-cancel { background:rgba(255,255,255,0.06); color:#94a3b8; }
        .btn-confirm { background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; }
    </style>
</head>

<body>
    <!-- Hero -->
    <div class="hero">
        <img src="{{ $appLogoUrl ?? asset('public/images/icon-512x512.png') }}" alt="KARISMA" class="app-icon">
        <h1>KARISMA</h1>
        <p>Aplikasi Presensi ASN</p>
        <span class="version">Balai Kekarantinaan Kesehatan Kelas I Tarakan</span>
    </div>

    <!-- Install Button -->
    <button id="installBtn" class="install-btn">
        <i class="fas fa-download"></i>
        Install Aplikasi
    </button>

    <!-- Features -->
    <div class="features">
        <div class="feat"><i class="fas fa-camera"></i><span>Face Detection</span></div>
        <div class="feat"><i class="fas fa-location-dot"></i><span>GPS Presensi</span></div>
        <div class="feat"><i class="fas fa-bolt"></i><span>Lembur Otomatis</span></div>
        <div class="feat"><i class="fas fa-bell"></i><span>Notifikasi</span></div>
    </div>

    <!-- Installation Guide -->
    <div class="guide-section">
        <div class="guide-title">Panduan Instalasi</div>

        <div class="guide-tabs">
            <button class="guide-tab active" onclick="switchGuide('android')">
                <i class="fab fa-android"></i> Android
            </button>
            <button class="guide-tab" onclick="switchGuide('iphone')">
                <i class="fab fa-apple"></i> iPhone
            </button>
        </div>

        <!-- Android Guide -->
        <div class="guide-panel active" id="guideAndroid">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-text">
                    <p>Buka halaman ini di <strong>Google Chrome</strong> atau <strong>Samsung Internet</strong></p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-text">
                    <p>Tekan tombol <strong>"Install Aplikasi"</strong> di atas, atau tekan ikon menu <strong>( &#8942; )</strong> di pojok kanan atas browser</p>
                    <span class="hint"><i class="fas fa-ellipsis-vertical"></i> Tiga titik vertikal</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-text">
                    <p>Pilih <strong>"Instal aplikasi"</strong> atau <strong>"Tambahkan ke Layar utama"</strong></p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-text">
                    <p>Tekan <strong>"Instal"</strong> untuk konfirmasi. Aplikasi akan muncul di layar utama</p>
                </div>
            </div>
        </div>

        <!-- iPhone Guide -->
        <div class="guide-panel" id="guideIphone">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-text">
                    <p>Buka halaman ini di <strong>Safari</strong></p>
                    <span class="hint"><i class="fas fa-circle-info"></i> Harus Safari, bukan Chrome</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-text">
                    <p>Tekan ikon <strong>Bagikan</strong> di bagian bawah layar</p>
                    <span class="hint"><i class="fas fa-arrow-up-from-bracket"></i> Kotak dengan panah ke atas</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-text">
                    <p>Gulir ke bawah dan pilih <strong>"Tambahkan ke Layar Utama"</strong></p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-text">
                    <p>Tekan <strong>"Tambahkan"</strong> di pojok kanan atas. Aplikasi akan muncul di home screen</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2026 KARISMA &mdash; Balai Kekarantinaan Kesehatan Kelas I Tarakan
    </div>

    <!-- Modal Install (native prompt) -->
    <div id="installModal" class="modal-overlay" onclick="if(event.target===this)hideModal(installModal)">
        <div class="modal-card">
            <div style="width:52px;height:52px;border-radius:14px;background:rgba(90,182,234,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="fas fa-download" style="font-size:22px;color:#5AB6EA;"></i>
            </div>
            <h3>Install KARISMA</h3>
            <p>Tambahkan aplikasi ke perangkat Anda untuk akses cepat tanpa perlu buka browser.</p>
            <div class="modal-btns">
                <button class="btn-cancel" id="cancelInstall">Batal</button>
                <button class="btn-confirm" id="confirmInstall">Install</button>
            </div>
        </div>
    </div>

    <script>
        // Guide tabs
        function switchGuide(type) {
            document.querySelectorAll('.guide-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.guide-panel').forEach(p => p.classList.remove('active'));
            if (type === 'android') {
                document.querySelectorAll('.guide-tab')[0].classList.add('active');
                document.getElementById('guideAndroid').classList.add('active');
            } else {
                document.querySelectorAll('.guide-tab')[1].classList.add('active');
                document.getElementById('guideIphone').classList.add('active');
            }
        }

        // Auto-detect device and show relevant tab
        var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        if (isIOS) switchGuide('iphone');

        // Modal
        const installModal = document.getElementById('installModal');
        function showModal(m) {
            m.style.display = 'flex';
            setTimeout(() => m.querySelector('.modal-card').classList.add('show'), 10);
        }
        function hideModal(m) {
            m.querySelector('.modal-card').classList.remove('show');
            setTimeout(() => m.style.display = 'none', 300);
        }

        // PWA Install
        let deferredPrompt;
        const installBtn = document.getElementById('installBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });

        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    installBtn.innerHTML = '<i class="fas fa-check-circle"></i> Aplikasi Terinstal';
                    installBtn.disabled = true;
                }
                deferredPrompt = null;
            } else {
                document.querySelector('.guide-section').scrollIntoView({ behavior:'smooth', block:'start' });
            }
        });

        document.getElementById('cancelInstall').addEventListener('click', () => hideModal(installModal));

        window.addEventListener('appinstalled', () => {
            installBtn.innerHTML = '<i class="fas fa-check-circle"></i> Aplikasi Terinstal';
            installBtn.disabled = true;
            hideModal(installModal);
        });

        // Register SW
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register("/sw.js", { scope: '/' });
        }
    </script>
</body>
</html>
