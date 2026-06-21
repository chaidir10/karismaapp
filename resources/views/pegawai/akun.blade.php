@extends('layouts.pegawai')

@section('title', 'Akun Saya')

@section('content')
<style>
    .akun-page { padding: 20px; padding-bottom: 100px; }

    .notif-success { background:var(--success-light); border:1px solid var(--success); color:var(--success); padding:12px 16px; border-radius:14px; margin-bottom:16px; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; }
    .notif-error { background:var(--danger-light); border:1px solid var(--danger); color:var(--danger); padding:12px 16px; border-radius:14px; margin-bottom:16px; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; }

    /* Profile Hero */
    .profile-hero {
        background:linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius:20px; padding:28px 20px; text-align:center; margin-bottom:20px;
        position:relative; overflow:hidden;
    }
    .profile-hero::before {
        content:''; position:absolute; top:-40px; right:-40px;
        width:120px; height:120px; border-radius:50%;
        background:rgba(255,255,255,0.08);
    }
    .profile-hero::after {
        content:''; position:absolute; bottom:-30px; left:-30px;
        width:90px; height:90px; border-radius:50%;
        background:rgba(255,255,255,0.06);
    }

    .profile-avatar {
        width:88px; height:88px; border-radius:50%; margin:0 auto 14px;
        overflow:hidden; border:3px solid rgba(255,255,255,0.4);
        cursor:pointer; position:relative; z-index:1;
        -webkit-tap-highlight-color:transparent;
    }
    .profile-avatar:active { opacity:0.9; }
    .profile-avatar img { width:100%; height:100%; object-fit:cover; display:block; }
    .profile-avatar-placeholder {
        width:100%; height:100%;
        background:rgba(255,255,255,0.15);
        display:flex; align-items:center; justify-content:center;
        color:#fff; font-weight:700; font-size:28px;
    }

    .profile-name { font-size:20px; font-weight:700; color:#fff; margin:0 0 4px; position:relative; z-index:1; }
    .profile-nip { font-size:12px; color:rgba(255,255,255,0.7); margin:0 0 10px; position:relative; z-index:1; }
    .profile-tag {
        display:inline-block; background:rgba(255,255,255,0.15); color:#fff;
        font-size:11px; font-weight:600; padding:4px 14px; border-radius:20px;
        position:relative; z-index:1;
    }

    /* Info Cards */
    .info-list { display:flex; flex-direction:column; gap:10px; margin-bottom:20px; }
    .info-item {
        display:flex; align-items:center; gap:14px; padding:14px 16px;
        background:var(--card-bg); border-radius:14px; border:1px solid var(--card-border);
    }
    .info-icon {
        width:44px; height:44px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-size:18px; flex-shrink:0;
        background:var(--primary-soft); color:var(--primary-dark);
    }
    .info-content { flex:1; min-width:0; }
    .info-label { font-size:11px; color:var(--gray); font-weight:500; margin:0 0 2px; }
    .info-value { font-size:14px; font-weight:600; color:var(--dark); margin:0; word-break:break-word; }

    /* Actions */
    .action-list { display:flex; flex-direction:column; gap:10px; margin-bottom:20px; }
    .action-item {
        display:flex; align-items:center; gap:14px; padding:14px 16px;
        background:var(--card-bg); border-radius:14px; border:1px solid var(--card-border);
        cursor:pointer; -webkit-tap-highlight-color:transparent; width:100%; text-align:left;
    }
    .action-item:active { opacity:0.85; }
    .action-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
    .action-icon-edit { background:var(--primary-soft); color:var(--primary-dark); }
    .action-icon-theme { background:var(--primary-soft); color:var(--primary-dark); }
    .action-icon-logout { background:var(--danger-light); color:var(--danger); }
    .action-content { flex:1; min-width:0; }
    .action-title { font-size:14px; font-weight:600; color:var(--dark); margin:0 0 2px; }
    .action-subtitle { font-size:12px; color:var(--gray); margin:0; }
    .action-arrow { color:var(--gray); font-size:12px; flex-shrink:0; }

    /* Photo Preview Modal */
    .photo-preview-overlay {
        display:none; position:fixed; inset:0; background:rgba(0,0,0,0.9);
        z-index:100; align-items:center; justify-content:center; flex-direction:column;
    }
    .photo-preview-overlay.visible { display:flex; }
    .photo-preview-overlay img { max-width:90%; max-height:75vh; border-radius:12px; object-fit:contain; }
    .photo-preview-close {
        position:absolute; top:16px; right:16px; width:40px; height:40px; border-radius:50%;
        background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:18px;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
    }

    /* Modal Overlay */
    .modal-overlay {
        display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
        z-index:50; padding:16px; overflow-y:auto; align-items:center; justify-content:center;
    }
    .modal-overlay.visible { display:flex; }
    .modal-box {
        background:var(--card-bg); border-radius:20px; width:100%; max-width:420px;
        padding:24px; position:relative; max-height:90vh; overflow-y:auto;
        border:1px solid var(--card-border); animation:modalSlideIn 0.3s ease-out;
    }
    .modal-close {
        position:absolute; top:16px; right:16px; background:none; border:none;
        font-size:18px; color:var(--gray); cursor:pointer; width:32px; height:32px;
        display:flex; align-items:center; justify-content:center; border-radius:8px;
    }
    .modal-close:active { background:var(--gray-light); }
    .modal-heading { font-size:18px; font-weight:700; color:var(--dark); margin:0 0 20px; }

    .logout-icon-box { width:56px; height:56px; border-radius:14px; background:var(--danger-light); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:24px; color:var(--danger); }
    .logout-title { font-size:18px; font-weight:700; color:var(--dark); text-align:center; margin:0 0 8px; }
    .logout-desc { font-size:14px; color:var(--gray); text-align:center; margin:0 0 24px; }

    .modal-actions { display:flex; gap:10px; }
    .btn-modal-cancel { flex:1; padding:14px; border-radius:14px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-size:14px; font-weight:600; cursor:pointer; }
    .btn-modal-cancel:active { opacity:0.85; }
    .btn-modal-danger { flex:1; padding:14px; border-radius:14px; border:none; background:var(--danger); color:#fff; font-size:14px; font-weight:600; cursor:pointer; }
    .btn-modal-danger:active { opacity:0.85; }
    .btn-modal-primary { flex:1; padding:14px; border-radius:14px; border:none; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; font-size:14px; font-weight:600; cursor:pointer; }
    .btn-modal-primary:active { opacity:0.85; }

    /* Form */
    .form-field { margin-bottom:16px; }
    .form-field label { display:block; font-size:13px; font-weight:600; color:var(--dark); margin-bottom:6px; }
    .form-field input, .form-field textarea { width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; background:var(--card-bg); color:var(--dark); outline:none; }
    .form-field input:focus, .form-field textarea:focus { border-color:var(--primary); }
    .form-field input[type="file"] { padding:10px; font-size:13px; }
    .field-error { font-size:12px; color:var(--danger); margin-top:4px; }

    /* Photo Crop Modal (WA-style) */
    .crop-modal {
        display:none; position:fixed; inset:0; background:#000; z-index:200;
        flex-direction:column;
    }
    .crop-modal.visible { display:flex; }
    .crop-header {
        display:flex; align-items:center; justify-content:space-between;
        padding:14px 16px; flex-shrink:0; z-index:2;
    }
    .crop-header button { background:none; border:none; color:#fff; font-size:14px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; }
    .crop-area {
        position:relative; width:100%; flex:1;
        overflow:hidden; touch-action:none;
    }
    .crop-area img {
        position:absolute; max-width:none; -webkit-user-drag:none; user-select:none; cursor:move;
    }
    .crop-overlay {
        position:absolute; inset:0; z-index:1; pointer-events:none;
    }
    .crop-footer {
        display:flex; align-items:center; justify-content:center; gap:16px;
        padding:16px 16px 34px; flex-shrink:0; z-index:2;
    }
    .crop-footer span { color:rgba(255,255,255,0.5); font-size:14px; }
    .crop-footer input[type="range"] { width:180px; accent-color:#fff; }

    @keyframes modalSlideIn {
        from { opacity:0; transform:translateY(30px); }
        to { opacity:1; transform:translateY(0); }
    }
</style>

<div class="akun-page">
    @if(session('success'))
    <div class="notif-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="notif-error"><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan. Silakan periksa form di bawah.</div>
    @endif

    <!-- Profile Hero -->
    <div class="profile-hero">
        <button type="button" onclick="openModal('editModal')" style="position:absolute; top:14px; right:14px; z-index:2; width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; -webkit-tap-highlight-color:transparent;">
            <i class="fas fa-pen-to-square"></i>
        </button>
        <div class="profile-avatar" onclick="openPhotoPreview()">
            @if($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil))
            <img src="{{ asset('public/storage/foto_profil/' . $user->foto_profil) }}" alt="Foto" id="mainAvatar">
            @else
            <div class="profile-avatar-placeholder">{{ collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->take(2)->join('') }}</div>
            @endif
        </div>
        <h2 class="profile-name">{{ $user->name ?? 'Nama Pengguna' }}</h2>
        <p class="profile-nip">NIP. {{ $user->nip ?? '-' }}</p>
        <span class="profile-tag">{{ $user->jabatan ?? 'Pegawai' }}</span>
    </div>

    <!-- Info -->
    <div class="info-list">
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-id-card"></i></div>
            <div class="info-content">
                <p class="info-label">NIP</p>
                <p class="info-value">{{ $user->nip ?? '-' }}</p>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-phone"></i></div>
            <div class="info-content">
                <p class="info-label">No HP</p>
                <p class="info-value">{{ $user->no_hp ?? '-' }}</p>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-envelope"></i></div>
            <div class="info-content">
                <p class="info-label">Email</p>
                <p class="info-value">{{ $user->email ?? '-' }}</p>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-location-dot"></i></div>
            <div class="info-content">
                <p class="info-label">Alamat</p>
                <p class="info-value">{{ $user->alamat ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="action-list">
        <button type="button" onclick="toggleTheme()" class="action-item">
            <div class="action-icon action-icon-theme">
                <i class="fas fa-sun" id="theme-icon-sun"></i>
                <i class="fas fa-moon" id="theme-icon-moon" style="display:none;"></i>
            </div>
            <div class="action-content">
                <p class="action-title">Tampilan</p>
                <p class="action-subtitle">Ubah ke mode gelap / terang</p>
            </div>
            <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
        </button>

        <form action="{{ route('pegawai.akun.logout') }}" method="POST" id="logoutForm">
            @csrf
            <button type="button" onclick="openLogoutModal()" class="action-item">
                <div class="action-icon action-icon-logout"><i class="fas fa-arrow-right-from-bracket"></i></div>
                <div class="action-content">
                    <p class="action-title">Keluar</p>
                    <p class="action-subtitle">Logout dari aplikasi</p>
                </div>
                <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
            </button>
        </form>
    </div>
</div>

<!-- Photo Preview -->
<div id="photoPreview" class="photo-preview-overlay">
    <button class="photo-preview-close" onclick="closePhotoPreview()"><i class="fas fa-xmark"></i></button>
    @if($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil))
    <img src="{{ asset('public/storage/foto_profil/' . $user->foto_profil) }}" alt="Foto Profil">
    @else
    <div style="color:#fff; font-size:80px; width:200px; height:200px; border-radius:50%; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); display:flex; align-items:center; justify-content:center; font-weight:700;">
        {{ collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->take(2)->join('') }}
    </div>
    @endif
</div>

<!-- Logout Modal -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-box" style="text-align:center;">
        <div class="logout-icon-box"><i class="fas fa-arrow-right-from-bracket"></i></div>
        <h3 class="logout-title">Konfirmasi Logout</h3>
        <p class="logout-desc">Apakah Anda yakin ingin keluar dari aplikasi?</p>
        <div class="modal-actions">
            <button type="button" id="logoutCancelBtn" class="btn-modal-cancel">Batal</button>
            <button type="button" id="logoutConfirmBtn" class="btn-modal-danger">Ya, Logout</button>
        </div>
    </div>
</div>

<!-- Edit Modal (Fullscreen) -->
<div id="editModal" class="modal-overlay">
    <div style="background:var(--card-bg); width:100%; max-width:500px; height:100vh; display:flex; flex-direction:column; animation:modalSlideIn 0.3s ease-out;">
        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
            <button type="button" onclick="closeModal('editModal')" style="background:none; border:none; color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px; font-weight:500; -webkit-tap-highlight-color:transparent;">
                <i class="fas fa-chevron-left"></i> Batal
            </button>
            <span style="font-size:15px; font-weight:700; color:var(--dark);">Edit Profil</span>
            <button type="submit" form="editForm" style="background:none; border:none; color:var(--primary-dark); font-size:14px; font-weight:700; cursor:pointer; -webkit-tap-highlight-color:transparent;">
                Simpan
            </button>
        </div>

        <!-- Scrollable Content -->
        <div style="flex:1; overflow-y:auto; padding:20px;">
            <form action="{{ route('pegawai.akun.update') }}" method="POST" enctype="multipart/form-data" id="editForm" data-turbo="false">
                @csrf

                <!-- Foto Section -->
                <div style="text-align:center; margin-bottom:24px;">
                    <div style="position:relative; display:inline-block;">
                        <div id="editAvatarPreview" style="width:96px; height:96px; border-radius:50%; overflow:hidden; border:3px solid var(--primary); margin:0 auto; background:var(--primary-soft); display:flex; align-items:center; justify-content:center;">
                            @if($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil))
                            <img src="{{ asset('public/storage/foto_profil/' . $user->foto_profil) }}" style="width:100%; height:100%; object-fit:cover; display:block;" id="editAvatarImg">
                            @else
                            <div style="color:var(--primary); font-size:32px; font-weight:700;">{{ collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->take(2)->join('') }}</div>
                            @endif
                        </div>
                        <label for="fotoInput" style="position:absolute; bottom:0; right:0; width:32px; height:32px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; cursor:pointer; border:2px solid var(--card-bg);">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                    <input type="file" name="foto_profil" id="fotoInput" accept="image/*" onchange="onPhotoSelected(this)" style="display:none;">
                    @error('foto_profil')<div class="field-error" style="margin-top:8px;">{{ $message }}</div>@enderror
                </div>

                <!-- Form Fields -->
                <div style="background:var(--light); border-radius:14px; padding:4px 0; margin-bottom:16px; border:1px solid var(--card-border);">
                    <div style="padding:12px 16px; border-bottom:1px solid var(--card-border);">
                        <div style="font-size:11px; color:var(--gray); font-weight:500; margin-bottom:4px;">Nama</div>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" style="width:100%; border:none; background:transparent; font-size:15px; font-weight:600; color:var(--dark); outline:none; padding:0;">
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div style="padding:12px 16px; border-bottom:1px solid var(--card-border);">
                        <div style="font-size:11px; color:var(--gray); font-weight:500; margin-bottom:4px;">Email</div>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" style="width:100%; border:none; background:transparent; font-size:15px; font-weight:600; color:var(--dark); outline:none; padding:0;">
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div style="padding:12px 16px; border-bottom:1px solid var(--card-border);">
                        <div style="font-size:11px; color:var(--gray); font-weight:500; margin-bottom:4px;">No HP</div>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" style="width:100%; border:none; background:transparent; font-size:15px; font-weight:600; color:var(--dark); outline:none; padding:0;">
                        @error('no_hp')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div style="padding:12px 16px;">
                        <div style="font-size:11px; color:var(--gray); font-weight:500; margin-bottom:4px;">Alamat</div>
                        <textarea name="alamat" rows="2" style="width:100%; border:none; background:transparent; font-size:15px; font-weight:600; color:var(--dark); outline:none; padding:0; resize:none;">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Password Section -->
                <div style="background:var(--light); border-radius:14px; padding:4px 0; border:1px solid var(--card-border);">
                    <div style="padding:12px 16px; border-bottom:1px solid var(--card-border);">
                        <div style="font-size:11px; color:var(--gray); font-weight:500; margin-bottom:4px;">Password Baru</div>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" style="width:100%; border:none; background:transparent; font-size:15px; color:var(--dark); outline:none; padding:0;">
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div style="padding:12px 16px;">
                        <div style="font-size:11px; color:var(--gray); font-weight:500; margin-bottom:4px;">Konfirmasi Password</div>
                        <input type="password" name="password_confirmation" style="width:100%; border:none; background:transparent; font-size:15px; color:var(--dark); outline:none; padding:0;">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- WA-style Crop Modal -->
<div id="cropModal" class="crop-modal">
    <div class="crop-header">
        <button onclick="cancelCrop()"><i class="fas fa-chevron-left" style="margin-right:6px;"></i> Batal</button>
        <button onclick="confirmCrop()" style="background:var(--primary); padding:8px 20px; border-radius:10px;">Selesai</button>
    </div>
    <div class="crop-area" id="cropArea">
        <img id="cropImg" src="" alt="" draggable="false">
        <canvas id="cropOverlay" class="crop-overlay"></canvas>
    </div>
    <div class="crop-footer">
        <span><i class="fas fa-magnifying-glass-minus"></i></span>
        <input type="range" id="zoomSlider" min="100" max="300" value="100" oninput="onZoomChange(this.value)">
        <span><i class="fas fa-magnifying-glass-plus"></i></span>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.add('visible'); document.body.style.overflow='hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('visible'); document.body.style.overflow='auto'; }
    function openLogoutModal() { openModal('logoutModal'); }
    function closeLogoutModal() { closeModal('logoutModal'); }

    function openPhotoPreview() { document.getElementById('photoPreview').classList.add('visible'); }
    function closePhotoPreview() { document.getElementById('photoPreview').classList.remove('visible'); }
    document.getElementById('photoPreview').addEventListener('click', function(e) { if (e.target === this) closePhotoPreview(); });

    // Logout
    document.addEventListener('turbo:load', function() {
        document.getElementById('logoutCancelBtn').addEventListener('click', closeLogoutModal);
        document.getElementById('logoutConfirmBtn').addEventListener('click', function() { document.getElementById('logoutForm').submit(); });
        document.getElementById('logoutModal').addEventListener('click', function(e) { if (e.target === this) closeLogoutModal(); });
        document.getElementById('editModal').addEventListener('click', function(e) { if (e.target === this) closeModal('editModal'); });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (document.getElementById('photoPreview').classList.contains('visible')) closePhotoPreview();
                else if (document.getElementById('logoutModal').classList.contains('visible')) closeLogoutModal();
                else if (document.getElementById('editModal').classList.contains('visible')) closeModal('editModal');
            }
        });

        // Theme icon sync
        var theme = document.documentElement.getAttribute('data-theme');
        document.getElementById('theme-icon-sun').style.display = theme === 'dark' ? 'none' : 'inline';
        document.getElementById('theme-icon-moon').style.display = theme === 'dark' ? 'inline' : 'none';
    });

    // Crop system — accurate circle crop with canvas
    var C = { drag:false, sx:0, sy:0, ox:0, oy:0, zoom:100, baseScale:1, pinchDist:0, circleR:0 };
    var _cropArea, _cropImg, _cropOverlay;

    function _initCropRefs() {
        _cropArea = document.getElementById('cropArea');
        _cropImg = document.getElementById('cropImg');
        _cropOverlay = document.getElementById('cropOverlay');
    }

    function drawMask() {
        var rect = _cropArea.getBoundingClientRect();
        var w = rect.width, h = rect.height;
        _cropOverlay.width = w;
        _cropOverlay.height = h;
        C.circleR = Math.min(w, h) * 0.38;
        var cx = w / 2, cy = h / 2;
        var ctx = _cropOverlay.getContext('2d');
        ctx.fillStyle = 'rgba(0,0,0,0.55)';
        ctx.fillRect(0, 0, w, h);
        ctx.globalCompositeOperation = 'destination-out';
        ctx.beginPath();
        ctx.arc(cx, cy, C.circleR, 0, Math.PI * 2);
        ctx.fill();
        ctx.globalCompositeOperation = 'source-over';
        ctx.strokeStyle = 'rgba(255,255,255,0.5)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(cx, cy, C.circleR, 0, Math.PI * 2);
        ctx.stroke();
    }

    function onPhotoSelected(input) {
        if (!input.files || !input.files[0]) return;
        _initCropRefs();
        var reader = new FileReader();
        reader.onload = function(e) {
            _cropImg.onload = function() {
                document.getElementById('cropModal').classList.add('visible');
                setTimeout(function() {
                    drawMask();
                    var rect = _cropArea.getBoundingClientRect();
                    var diameter = C.circleR * 2;
                    C.baseScale = diameter / Math.min(_cropImg.naturalWidth, _cropImg.naturalHeight);
                    C.zoom = 100;
                    C.ox = 0; C.oy = 0;
                    document.getElementById('zoomSlider').value = 100;
                    positionImg();
                }, 50);
            };
            _cropImg.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }

    function positionImg() {
        var rect = _cropArea.getBoundingClientRect();
        var s = C.baseScale * (C.zoom / 100);
        var iw = _cropImg.naturalWidth * s;
        var ih = _cropImg.naturalHeight * s;
        var x = (rect.width - iw) / 2 + C.ox;
        var y = (rect.height - ih) / 2 + C.oy;
        _cropImg.style.width = iw + 'px';
        _cropImg.style.height = ih + 'px';
        _cropImg.style.left = x + 'px';
        _cropImg.style.top = y + 'px';
    }

    function onZoomChange(val) {
        C.zoom = parseInt(val);
        positionImg();
    }

    function cancelCrop() {
        document.getElementById('cropModal').classList.remove('visible');
        document.getElementById('fotoInput').value = '';
    }

    function confirmCrop() {
        var rect = _cropArea.getBoundingClientRect();
        var s = C.baseScale * (C.zoom / 100);
        var iw = _cropImg.naturalWidth * s;
        var ih = _cropImg.naturalHeight * s;
        var imgX = (rect.width - iw) / 2 + C.ox;
        var imgY = (rect.height - ih) / 2 + C.oy;
        var cx = rect.width / 2;
        var cy = rect.height / 2;

        // Source rect in natural image coords
        var srcX = (cx - C.circleR - imgX) / s;
        var srcY = (cy - C.circleR - imgY) / s;
        var srcW = (C.circleR * 2) / s;
        var srcH = (C.circleR * 2) / s;

        var outSize = 512;
        var canvas = document.createElement('canvas');
        canvas.width = outSize; canvas.height = outSize;
        var ctx = canvas.getContext('2d');

        // Clip to circle
        ctx.beginPath();
        ctx.arc(outSize / 2, outSize / 2, outSize / 2, 0, Math.PI * 2);
        ctx.closePath();
        ctx.clip();
        ctx.drawImage(_cropImg, srcX, srcY, srcW, srcH, 0, 0, outSize, outSize);

        var dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('cropModal').classList.remove('visible');

        // Preview
        var box = document.getElementById('editAvatarPreview');
        box.innerHTML = '<img src="' + dataUrl + '" style="width:100%;height:100%;object-fit:cover;display:block;">';

        // Set cropped file to input
        canvas.toBlob(function(blob) {
            var f = new File([blob], 'profile.jpg', { type:'image/jpeg' });
            var dt = new DataTransfer();
            dt.items.add(f);
            document.getElementById('fotoInput').files = dt.files;
        }, 'image/jpeg', 0.9);
    }

    // Drag — mouse
    document.addEventListener('mousedown', function(e) {
        if (!_cropArea || !_cropArea.contains(e.target) || e.target === _cropOverlay) return;
        e.preventDefault();
        C.drag = true; C.sx = e.clientX - C.ox; C.sy = e.clientY - C.oy;
    });
    document.addEventListener('mousemove', function(e) {
        if (!C.drag) return;
        C.ox = e.clientX - C.sx; C.oy = e.clientY - C.sy;
        positionImg();
    });
    document.addEventListener('mouseup', function() { C.drag = false; });

    // Drag — touch
    document.addEventListener('touchstart', function(e) {
        if (!_cropArea || !_cropArea.contains(e.target)) return;
        if (e.touches.length === 1) {
            C.drag = true;
            C.sx = e.touches[0].clientX - C.ox;
            C.sy = e.touches[0].clientY - C.oy;
        } else if (e.touches.length === 2) {
            C.drag = false;
            C.pinchDist = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY);
        }
    }, { passive:true });
    document.addEventListener('touchmove', function(e) {
        if (!_cropArea || !_cropArea.contains(e.target)) return;
        if (e.touches.length === 1 && C.drag) {
            C.ox = e.touches[0].clientX - C.sx;
            C.oy = e.touches[0].clientY - C.sy;
            positionImg();
        } else if (e.touches.length === 2) {
            var dist = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY);
            var delta = dist - C.pinchDist;
            C.zoom = Math.max(100, Math.min(300, C.zoom + delta * 0.3));
            C.pinchDist = dist;
            document.getElementById('zoomSlider').value = Math.round(C.zoom);
            positionImg();
        }
    }, { passive:true });
    document.addEventListener('touchend', function() { C.drag = false; });
</script>
@endsection
