@extends('layouts.pegawai')

@section('title', 'Akun Saya')

@section('content')
<style>
    .akun-page { padding: 20px; padding-bottom: 100px; }

    /* Notifications */
    .notif-success {
        background: var(--success-light);
        border: 1px solid var(--success);
        color: var(--success);
        padding: 12px 16px;
        border-radius: 14px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
    }
    .notif-error {
        background: var(--danger-light);
        border: 1px solid var(--danger);
        color: var(--danger);
        padding: 12px 16px;
        border-radius: 14px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
    }

    /* Profile Section */
    .profile-section {
        text-align: center;
        padding: 24px 0 20px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 18px;
        margin: 0 auto 14px;
        overflow: hidden;
        border: 3px solid var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-soft);
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .profile-avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 26px;
    }

    .profile-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 8px;
    }

    .profile-tag {
        display: inline-block;
        background: rgba(90, 182, 234, 0.1);
        color: var(--primary);
        font-size: 12px;
        font-weight: 600;
        padding: 4px 14px;
        border-radius: 20px;
    }

    /* Info List */
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 20px 0;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        background: var(--card-bg);
        border-radius: 14px;
        border: 1px solid var(--card-border);
    }

    .info-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        background: rgba(90, 182, 234, 0.1);
        color: var(--primary);
    }

    .info-content {
        flex: 1;
        min-width: 0;
    }
    .info-label {
        font-size: 12px;
        color: var(--gray);
        font-weight: 500;
        margin: 0 0 2px;
    }
    .info-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--dark);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Edit Profile Button */
    .btn-edit-profile {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        border: none;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        margin-bottom: 16px;
    }
    .btn-edit-profile:active { opacity: 0.85; }

    /* Action Cards */
    .action-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .action-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        background: var(--card-bg);
        border-radius: 14px;
        border: 1px solid var(--card-border);
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        width: 100%;
        text-align: left;
    }
    .action-item:active { opacity: 0.85; }

    .action-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .action-icon-theme {
        background: rgba(90, 182, 234, 0.1);
        color: var(--primary);
    }
    .action-icon-logout {
        background: var(--danger-light);
        color: var(--danger);
    }

    .action-content {
        flex: 1;
        min-width: 0;
    }
    .action-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--dark);
        margin: 0 0 2px;
    }
    .action-subtitle {
        font-size: 12px;
        color: var(--gray);
        margin: 0;
    }

    .action-arrow {
        color: var(--gray);
        font-size: 14px;
        flex-shrink: 0;
    }

    /* Modal Overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 50;
        padding: 16px;
        overflow-y: auto;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.visible {
        display: flex;
    }

    .modal-box {
        background: var(--card-bg);
        border-radius: 20px;
        width: 100%;
        max-width: 420px;
        padding: 24px;
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
        border: 1px solid var(--card-border);
        animation: modalSlideIn 0.3s ease-out;
    }

    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: none;
        border: none;
        font-size: 18px;
        color: var(--gray);
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        -webkit-tap-highlight-color: transparent;
    }
    .modal-close:active { background: var(--gray-light); }

    .modal-heading {
        font-size: 18px;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 20px;
    }

    /* Logout modal */
    .logout-icon-box {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: var(--danger-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 24px;
        color: var(--danger);
    }

    .logout-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--dark);
        text-align: center;
        margin: 0 0 8px;
    }

    .logout-desc {
        font-size: 14px;
        color: var(--gray);
        text-align: center;
        margin: 0 0 24px;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
    }

    .btn-modal-cancel {
        flex: 1;
        padding: 12px;
        border-radius: 14px;
        border: 1px solid var(--card-border);
        background: var(--card-bg);
        color: var(--dark);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }
    .btn-modal-cancel:active { opacity: 0.85; }

    .btn-modal-danger {
        flex: 1;
        padding: 12px;
        border-radius: 14px;
        border: none;
        background: var(--danger);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }
    .btn-modal-danger:active { opacity: 0.85; }

    .btn-modal-primary {
        flex: 1;
        padding: 12px;
        border-radius: 14px;
        border: none;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }
    .btn-modal-primary:active { opacity: 0.85; }

    /* Form fields */
    .form-field {
        margin-bottom: 16px;
    }
    .form-field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 6px;
    }
    .form-field input,
    .form-field textarea,
    .form-field select {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        font-size: 14px;
        background: var(--card-bg);
        color: var(--dark);
        outline: none;
    }
    .form-field input:focus,
    .form-field textarea:focus,
    .form-field select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(90, 182, 234, 0.15);
    }
    .form-field input[type="file"] {
        padding: 10px;
        font-size: 13px;
    }
    .field-error {
        font-size: 12px;
        color: var(--danger);
        margin-top: 4px;
    }
    .field-hint {
        font-size: 11px;
        color: var(--gray);
        margin-top: 4px;
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="akun-page">
    {{-- Notifikasi sukses --}}
    @if(session('success'))
    <div class="notif-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Notifikasi error --}}
    @if($errors->any())
    <div class="notif-error">
        <i class="fas fa-exclamation-circle"></i>
        Terjadi kesalahan. Silakan periksa form di bawah.
    </div>
    @endif

    {{-- Profile Section --}}
    <div class="profile-section">
        <div class="profile-avatar">
            @if($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil))
            <img src="{{ asset('public/storage/foto_profil/' . $user->foto_profil) }}"
                alt="Foto Profil {{ $user->name }}"
                onerror="this.style.display='none'; this.parentNode.innerHTML='<div class=\'profile-avatar-placeholder\'>{{ collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->join('') }}</div>'">
            @else
            <div class="profile-avatar-placeholder">{{ collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->join('') }}</div>
            @endif
        </div>
        <h2 class="profile-name">{{ $user->name ?? 'Nama Pengguna' }}</h2>
        <span class="profile-tag">{{ $user->jabatan ?? 'Pegawai' }}</span>
    </div>

    {{-- Info Items --}}
    <div class="info-list">
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
                <p class="info-label">No HP</p>
                <p class="info-value">{{ $user->no_hp ?? '-' }}</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
                <p class="info-label">Email</p>
                <p class="info-value">{{ $user->email ?? '-' }}</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
                <p class="info-label">Alamat</p>
                <p class="info-value">{{ $user->alamat ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Edit Button --}}
    <button onclick="openModal('editModal')" class="btn-edit-profile">
        <i class="fas fa-edit"></i>
        Edit Profil
    </button>

    {{-- Action Cards --}}
    <div class="action-list">
        {{-- Dark/Light Mode --}}
        <button type="button" onclick="toggleTheme()" class="action-item">
            <div class="action-icon action-icon-theme">
                <svg id="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/>
                    <line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/>
                    <line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
                <svg id="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </div>
            <div class="action-content">
                <p class="action-title">Tampilan</p>
                <p class="action-subtitle">Ubah ke mode gelap / terang</p>
            </div>
            <div class="action-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </button>

        {{-- Logout --}}
        <form action="{{ route('pegawai.akun.logout') }}" method="POST" id="logoutForm">
            @csrf
            <button type="button" onclick="openLogoutModal()" class="action-item">
                <div class="action-icon action-icon-logout">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="action-content">
                    <p class="action-title">Keluar</p>
                    <p class="action-subtitle">Logout dari aplikasi</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </button>
        </form>
    </div>
</div>

{{-- Modal Konfirmasi Logout --}}
<div id="logoutModal" class="modal-overlay">
    <div class="modal-box" style="text-align:center;">
        <div class="logout-icon-box">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h3 class="logout-title">Konfirmasi Logout</h3>
        <p class="logout-desc">Apakah Anda yakin ingin keluar dari aplikasi?</p>

        <div class="modal-actions">
            <button type="button" id="logoutCancelBtn" class="btn-modal-cancel">
                Batal
            </button>
            <button type="button" id="logoutConfirmBtn" class="btn-modal-danger">
                Ya, Logout
            </button>
        </div>
    </div>
</div>

{{-- Modal Edit Akun --}}
<div id="editModal" class="modal-overlay">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('editModal')">
            <i class="fas fa-times"></i>
        </button>

        <h3 class="modal-heading">Edit Profil</h3>

        <form action="{{ route('pegawai.akun.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nama --}}
            <div class="form-field">
                <label for="name">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
                @error('name')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}">
                @error('email')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- No HP --}}
            <div class="form-field">
                <label for="no_hp">No HP</label>
                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $user->no_hp) }}">
                @error('no_hp')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="form-field">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3">{{ old('alamat', $user->alamat) }}</textarea>
                @error('alamat')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-field">
                <label for="password">Password Baru</label>
                <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin diubah">
                @error('password')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="form-field">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation">
            </div>

            {{-- Foto Profil --}}
            <div class="form-field">
                <label for="foto_profil">Foto Profil</label>
                <input type="file" name="foto_profil" id="foto_profil" accept="image/*">
                @error('foto_profil')
                <div class="field-error">{{ $message }}</div>
                @enderror
                @if($user->foto_profil)
                <div class="field-hint">Foto profil saat ini: {{ $user->foto_profil }}</div>
                @endif
            </div>

            {{-- Tombol Aksi --}}
            <div class="modal-actions" style="margin-top:20px;">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('editModal')">
                    Batal
                </button>
                <button type="submit" class="btn-modal-primary">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('visible');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('visible');
        document.body.style.overflow = 'auto';
    }

    function openLogoutModal() {
        openModal('logoutModal');
    }

    function closeLogoutModal() {
        closeModal('logoutModal');
    }

    // Initialize logout modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        const logoutModal = document.getElementById('logoutModal');
        const logoutCancelBtn = document.getElementById('logoutCancelBtn');
        const logoutConfirmBtn = document.getElementById('logoutConfirmBtn');
        const logoutForm = document.getElementById('logoutForm');

        // Cancel logout
        logoutCancelBtn.addEventListener('click', closeLogoutModal);

        // Confirm logout
        logoutConfirmBtn.addEventListener('click', function() {
            logoutForm.submit();
        });

        // Close modal when clicking outside
        logoutModal.addEventListener('click', function(e) {
            if (e.target === logoutModal) {
                closeLogoutModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (logoutModal.classList.contains('visible')) {
                    closeLogoutModal();
                }
                if (document.getElementById('editModal').classList.contains('visible')) {
                    closeModal('editModal');
                }
            }
        });
    });

    // Close modal when clicking outside for edit modal
    document.addEventListener('click', (e) => {
        if (e.target.id === 'editModal') {
            closeModal('editModal');
        }
    });

    // Prevent modal close when clicking inside modal content
    document.querySelectorAll('#editModal > div').forEach(modalContent => {
        modalContent.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Prevent modal close when clicking inside logout modal content
    document.querySelectorAll('#logoutModal > div').forEach(modalContent => {
        modalContent.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Sync theme icon on load
    document.addEventListener('DOMContentLoaded', function() {
        var theme = document.documentElement.getAttribute('data-theme');
        var sunIcon = document.getElementById('theme-icon-sun');
        var moonIcon = document.getElementById('theme-icon-moon');
        if (sunIcon && moonIcon) {
            sunIcon.style.display = theme === 'dark' ? 'none' : 'block';
            moonIcon.style.display = theme === 'dark' ? 'block' : 'none';
        }
    });

    // Handle image loading errors
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            img.addEventListener('error', function() {
                this.src = 'https://avatar.iran.liara.run/public/48';
            });
        });
    });
</script>
@endsection