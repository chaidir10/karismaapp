@extends('layouts.pegawai')

@section('title', 'Akun Saya')

@section('content')
<style>
    .akun-page { padding: 20px; padding-bottom: 100px; }
    .akun-toggle { position:relative; display:inline-block; width:46px; height:26px; flex-shrink:0; }
    .akun-toggle input { opacity:0; width:0; height:0; position:absolute; }
    .akun-toggle-track { position:absolute; cursor:pointer; inset:0; background:#cbd5e1; border-radius:26px; transition:0.2s; }
    .akun-toggle input:checked + .akun-toggle-track { background:var(--primary); }
    .akun-toggle-thumb { position:absolute; height:20px; width:20px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
    .akun-toggle input:checked + .akun-toggle-track .akun-toggle-thumb { left:23px; }

    .tester-wl-item {
        display:flex; align-items:center; gap:12px; padding:12px; margin-bottom:8px;
        border:1.5px solid var(--card-border); border-radius:12px; cursor:pointer;
        transition: border-color 0.2s, background 0.2s;
        -webkit-tap-highlight-color:transparent;
    }
    .tester-wl-item:active { opacity:0.85; }
    .tester-wl-item.active { border-color:var(--primary); background:rgba(46,151,212,0.06); }
    .tester-wl-check {
        width:24px; height:24px; border-radius:8px; flex-shrink:0;
        border:2px solid var(--card-border); display:flex; align-items:center; justify-content:center;
        font-size:11px; color:transparent; transition: all 0.2s;
    }
    .tester-wl-item.active .tester-wl-check {
        background:var(--primary); border-color:var(--primary); color:#fff;
    }

    .picker-user-item {
        display:flex; align-items:center; gap:10px; padding:10px 12px; margin-bottom:6px;
        border:1.5px solid var(--card-border); border-radius:12px; cursor:pointer;
        transition: border-color 0.2s, background 0.2s;
        -webkit-tap-highlight-color:transparent;
    }
    .picker-user-item:active { opacity:0.85; }
    .picker-user-item.active { border-color:var(--primary); background:rgba(46,151,212,0.05); }
    .picker-user-check {
        width:22px; height:22px; border-radius:7px; flex-shrink:0;
        border:2px solid var(--card-border); display:flex; align-items:center; justify-content:center;
        font-size:10px; color:transparent; transition: all 0.2s;
    }
    .picker-user-item.active .picker-user-check {
        background:var(--primary); border-color:var(--primary); color:#fff;
    }

    .face-mode-tabs {
        display:flex; gap:6px; padding:4px; background:var(--light); border-radius:12px;
        border:1px solid var(--card-border);
    }
    .face-mode-tab {
        flex:1; padding:8px 4px; border:none; border-radius:9px; font-size:11px; font-weight:600;
        background:transparent; color:var(--gray); cursor:pointer; display:flex; align-items:center;
        justify-content:center; gap:5px; transition: all 0.2s;
        -webkit-tap-highlight-color:transparent;
    }
    .face-mode-tab:active { opacity:0.8; }
    .face-mode-tab.active {
        background:var(--primary); color:#fff; box-shadow:0 2px 8px rgba(46,151,212,0.25);
    }
    .face-user-btn {
        width:100%; padding:10px 14px; border:1.5px solid var(--card-border); border-radius:12px;
        font-size:12px; color:var(--dark); background:var(--card-bg); cursor:pointer;
        display:flex; align-items:center; justify-content:space-between;
        transition: border-color 0.2s;
        -webkit-tap-highlight-color:transparent;
    }
    .face-user-btn:active { border-color:var(--primary); }


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

    /* Photo Preview Modal — smooth morph */
    .photo-preview-overlay {
        position:fixed; inset:0; z-index:100;
        display:flex; align-items:center; justify-content:center; flex-direction:column;
        pointer-events:none; opacity:0;
    }
    .photo-preview-overlay.visible { pointer-events:auto; opacity:1; }
    .photo-preview-bg {
        position:absolute; inset:0; background:#000; opacity:0;
        transition: opacity 0.3s ease;
    }
    .photo-preview-overlay.visible .photo-preview-bg { opacity:0.92; }
    .photo-preview-content {
        position:relative; z-index:1;
        transition: transform 0.35s cubic-bezier(0.2, 0.9, 0.3, 1), border-radius 0.35s ease, width 0.35s cubic-bezier(0.2, 0.9, 0.3, 1), height 0.35s cubic-bezier(0.2, 0.9, 0.3, 1);
        overflow:hidden; border-radius:50%;
        width:88px; height:88px;
    }
    .photo-preview-overlay.visible .photo-preview-content {
        border-radius:12px;
        width:100vw; height:100vw;
    }
    .photo-preview-content img, .photo-preview-content .pp-placeholder {
        width:100%; height:100%; object-fit:cover; display:block;
    }
    .photo-preview-close {
        position:absolute; top:16px; right:16px; width:40px; height:40px; border-radius:50%;
        z-index:2; opacity:0; transition: opacity 0.2s 0.15s;
    }
    .photo-preview-overlay.visible .photo-preview-close { opacity:1; }
    .photo-preview-close {
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

    <!-- Profile Hero -->
    <div class="profile-hero">
        <button type="button" onclick="toggleTheme()" style="position:absolute; top:14px; left:14px; z-index:2; width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; -webkit-tap-highlight-color:transparent;">
            <i class="fas fa-sun" id="theme-icon-sun"></i><i class="fas fa-moon" id="theme-icon-moon" style="display:none;"></i>
        </button>
        <button type="button" onclick="openLogoutModal()" style="position:absolute; top:14px; right:14px; z-index:2; width:36px; height:36px; border-radius:10px; background:rgba(239,68,68,0.2); border:none; color:#fff; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; -webkit-tap-highlight-color:transparent;">
            <i class="fas fa-arrow-right-from-bracket"></i>
        </button>
        <button type="button" onclick="openModal('editModal')" style="position:absolute; bottom:14px; right:14px; z-index:2; width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; -webkit-tap-highlight-color:transparent;">
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
    <form action="{{ route('pegawai.akun.logout') }}" method="POST" id="logoutForm" style="display:none;">@csrf</form>

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

    @if($user->is_tester)
    <div style="margin-bottom:20px;">
        <div style="font-size:12px; font-weight:700; color:#ef4444; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; padding:0 4px;">Tester Tools</div>
        <div style="background:var(--card-bg); border:1px solid var(--card-border); border-radius:14px; overflow:hidden;">
            <div style="padding:14px 16px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:40px; height:40px; border-radius:10px; background:rgba(239,68,68,0.08); color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;"><i class="fas fa-clock-rotate-left"></i></div>
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:13px; font-weight:600; color:var(--dark); margin:0;">Reset Presensi Hari Ini</p>
                        <p style="font-size:11px; color:var(--gray); margin:2px 0 0;">Hapus rekam kehadiran hari ini</p>
                    </div>
                </div>
                <div style="display:flex; gap:8px; margin-top:12px;">
                    <button type="button" onclick="resetPresensi('reguler')" style="flex:1; padding:10px; border-radius:10px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-size:12px; font-weight:600; cursor:pointer;"><i class="fas fa-clock" style="color:#3b82f6; margin-right:4px;"></i> Reguler</button>
                    <button type="button" onclick="resetPresensi('lembur')" style="flex:1; padding:10px; border-radius:10px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-size:12px; font-weight:600; cursor:pointer;"><i class="fas fa-bolt" style="color:#f59e0b; margin-right:4px;"></i> Lembur</button>
                    <button type="button" onclick="resetPresensi('all')" style="flex:1; padding:10px; border-radius:10px; border:none; background:#ef4444; color:#fff; font-size:12px; font-weight:600; cursor:pointer;"><i class="fas fa-trash" style="margin-right:4px;"></i> Semua</button>
                </div>
                <div id="resetStatus" style="margin-top:8px; font-size:11px; color:#10b981; text-align:center; display:none;"></div>
            </div>
            <div style="padding:14px 16px; border-top:1px solid var(--card-border);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:40px; height:40px; border-radius:10px; background:rgba(168,85,247,0.08); color:#a855f7; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;"><i class="fas fa-shield-halved"></i></div>
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:13px; font-weight:600; color:var(--dark); margin:0;">Izin & Cache</p>
                        <p style="font-size:11px; color:var(--gray); margin:2px 0 0;">Request ulang izin / bersihkan cache</p>
                    </div>
                </div>
                <div style="display:flex; gap:8px; margin-top:12px;">
                    <button type="button" onclick="reRequestPermissions()" style="flex:1; padding:10px; border-radius:10px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-size:11px; font-weight:600; cursor:pointer;"><i class="fas fa-key" style="color:#a855f7; margin-right:4px;"></i> Request Izin</button>
                    <button type="button" onclick="clearAllCache()" style="flex:1; padding:10px; border-radius:10px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-size:11px; font-weight:600; cursor:pointer;"><i class="fas fa-broom" style="color:#f59e0b; margin-right:4px;"></i> Clear Cache</button>
                </div>
                <div id="permStatus" style="margin-top:6px; font-size:10px; color:var(--gray); text-align:center;"></div>
            </div>
            @php $allWilayah = \App\Models\WilayahKerja::all(); $userWilayahIds = $user->wilayahKerjaList->pluck('id')->toArray(); @endphp
            <div style="padding:14px 16px; border-top:1px solid var(--card-border);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(59,130,246,0.08); color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;"><i class="fas fa-map-location-dot"></i></div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:13px; font-weight:600; color:var(--dark); margin:0;">Wilayah Kerja</p>
                            <p style="font-size:11px; color:var(--gray); margin:2px 0 0;"><span id="wilayahCount">{{ count($userWilayahIds) }}</span> wilayah dipilih</p>
                        </div>
                    </div>
                    <button type="button" onclick="openModal('wilayahModal')" style="padding:7px 14px; border-radius:10px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--primary); font-size:12px; font-weight:600; cursor:pointer;">Ubah</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Wilayah Modal -->
    <div id="wilayahModal" class="modal-overlay">
        <div class="modal-box" style="max-height:75vh; display:flex; flex-direction:column; padding:0;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--card-border);">
                <h3 style="font-size:15px; font-weight:700; color:var(--dark); margin:0;">Pilih Wilayah Kerja</h3>
                <button onclick="closeModal('wilayahModal')" class="modal-close" style="position:static;"><i class="fas fa-xmark"></i></button>
            </div>
            <div style="flex:1; overflow-y:auto; padding:12px 20px;">
                @foreach($allWilayah as $w)
                <label class="tester-wl-item {{ in_array($w->id, $userWilayahIds) ? 'active' : '' }}" data-id="{{ $w->id }}" onclick="toggleWilayahItem(this)">
                    <div class="tester-wl-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:13px; font-weight:600; color:var(--dark);">{{ $w->alamat ?: $w->nama ?? 'Wilayah #'.$w->id }}</div>
                        <div style="font-size:10px; color:var(--gray); margin-top:1px;">Radius: {{ $w->radius ?? 100 }}m &middot; {{ number_format($w->latitude, 5) }}, {{ number_format($w->longitude, 5) }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            <div style="padding:12px 20px; border-top:1px solid var(--card-border); display:flex; gap:8px;">
                <button type="button" onclick="closeModal('wilayahModal')" style="flex:1; padding:12px; border-radius:12px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-size:13px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="button" onclick="confirmWilayah()" style="flex:1; padding:12px; border-radius:12px; border:none; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; font-size:13px; font-weight:600; cursor:pointer;">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    @if(in_array($user->role, ['admin', 'superadmin']))
    @php
        $AS = \App\Models\AppSetting::class;
        $s_libur = $AS::getBool('disable_presensi_hari_libur', true);
        $s_face = $AS::getBool('enable_face_detection', true);
        $s_faceMode = $AS::getValue('face_detection_mode', 'all');
        $s_faceUsers = json_decode($AS::getValue('face_detection_users', '[]'), true) ?: [];
        $s_masukDulu = $AS::getBool('require_masuk_before_pulang', true);
        $s_darurat = $AS::getBool('enable_absen_darurat', false);
        $allPegawai = \App\Models\User::nonTester()->orderBy('name')->get(['id','name','nip']);
    @endphp
    <div style="margin-bottom:20px;">
        <div style="font-size:12px; font-weight:700; color:var(--gray); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; padding:0 4px;">Pengaturan Admin</div>
        <div style="background:var(--card-bg); border:1px solid var(--card-border); border-radius:14px; overflow:hidden;">

            {{-- Nonaktifkan presensi hari libur --}}
            <div style="padding:14px 16px; border-bottom:1px solid var(--card-border);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(59,130,246,0.08); color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;"><i class="fas fa-calendar-xmark"></i></div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:13px; font-weight:600; color:var(--dark); margin:0;">Nonaktifkan presensi hari libur</p>
                            <p style="font-size:11px; color:var(--gray); margin:2px 0 0;">Reguler di-disable saat libur/weekend</p>
                        </div>
                    </div>
                    <label class="akun-toggle"><input type="checkbox" data-key="disable_presensi_hari_libur" {{ $s_libur ? 'checked' : '' }} onchange="saveSetting(this)"><span class="akun-toggle-track"><span class="akun-toggle-thumb"></span></span></label>
                </div>
            </div>

            {{-- Face Detection --}}
            <div style="padding:14px 16px; border-bottom:1px solid var(--card-border);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(16,185,129,0.08); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;"><i class="fas fa-face-smile"></i></div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:13px; font-weight:600; color:var(--dark); margin:0;">Deteksi Wajah</p>
                            <p style="font-size:11px; color:var(--gray); margin:2px 0 0;">Wajib verifikasi wajah saat absen</p>
                        </div>
                    </div>
                    <label class="akun-toggle"><input type="checkbox" data-key="enable_face_detection" {{ $s_face ? 'checked' : '' }} onchange="saveSetting(this)"><span class="akun-toggle-track"><span class="akun-toggle-thumb"></span></span></label>
                </div>
                <input type="hidden" id="faceMode" data-key="face_detection_mode" value="{{ $s_faceMode }}">
                <div id="faceSubSection" style="margin-top:10px; {{ !$s_face ? 'display:none;' : '' }}">
                    <div class="face-mode-tabs">
                        <button type="button" class="face-mode-tab {{ $s_faceMode === 'all' ? 'active' : '' }}" data-val="all" onclick="setFaceMode(this,'all')">
                            <i class="fas fa-users"></i> Semua
                        </button>
                        <button type="button" class="face-mode-tab {{ $s_faceMode === 'except' ? 'active' : '' }}" data-val="except" onclick="setFaceMode(this,'except')">
                            <i class="fas fa-user-minus"></i> Kecuali
                        </button>
                        <button type="button" class="face-mode-tab {{ $s_faceMode === 'only' ? 'active' : '' }}" data-val="only" onclick="setFaceMode(this,'only')">
                            <i class="fas fa-user-check"></i> Hanya
                        </button>
                    </div>
                    <div id="faceUserBtn" style="margin-top:8px; {{ $s_faceMode === 'all' ? 'display:none;' : '' }}">
                        <button type="button" onclick="openUserPicker('face')" class="face-user-btn">
                            <div style="display:flex; align-items:center; gap:8px; flex:1;">
                                <div style="width:28px; height:28px; border-radius:8px; background:var(--primary); display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px;"><i class="fas fa-user-group"></i></div>
                                <span><span id="faceUserCount" style="font-weight:700; color:var(--primary);">{{ count($s_faceUsers) }}</span> pegawai dipilih</span>
                            </div>
                            <i class="fas fa-chevron-right" style="font-size:11px; opacity:0.4;"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Wajib masuk sebelum pulang --}}
            <div style="padding:14px 16px; border-bottom:1px solid var(--card-border);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(245,158,11,0.08); color:#f59e0b; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;"><i class="fas fa-arrow-right-arrow-left"></i></div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:13px; font-weight:600; color:var(--dark); margin:0;">Wajib masuk sebelum pulang</p>
                            <p style="font-size:11px; color:var(--gray); margin:2px 0 0;">Tombol pulang aktif setelah absen masuk</p>
                        </div>
                    </div>
                    <label class="akun-toggle"><input type="checkbox" data-key="require_masuk_before_pulang" {{ $s_masukDulu ? 'checked' : '' }} onchange="saveSetting(this)"><span class="akun-toggle-track"><span class="akun-toggle-thumb"></span></span></label>
                </div>
            </div>

            {{-- Absen Darurat --}}
            <div style="padding:14px 16px;">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(239,68,68,0.08); color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;"><i class="fas fa-bolt"></i></div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:13px; font-weight:600; color:var(--dark); margin:0;">Absen Darurat</p>
                            <p style="font-size:11px; color:var(--gray); margin:2px 0 0;">Halaman absen alternatif</p>
                        </div>
                    </div>
                    <label class="akun-toggle"><input type="checkbox" data-key="enable_absen_darurat" {{ $s_darurat ? 'checked' : '' }} onchange="saveSetting(this)"><span class="akun-toggle-track"><span class="akun-toggle-thumb"></span></span></label>
                </div>
            </div>
        </div>
    </div>

    {{-- User Picker Modal --}}
    <div id="akunUserPicker" class="modal-overlay">
        <div class="modal-box" style="max-height:85vh; display:flex; flex-direction:column; padding:0;">
            <div style="padding:20px 20px 0;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                    <div>
                        <h3 style="font-size:16px; font-weight:700; color:var(--dark); margin:0;" id="pickerTitle">Pilih Pegawai</h3>
                        <p style="font-size:11px; color:var(--gray); margin:2px 0 0;"><span id="pickerCount">0</span> pegawai dipilih</p>
                    </div>
                    <button onclick="closeUserPicker()" style="width:32px; height:32px; border-radius:10px; border:none; background:var(--light); color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center;"><i class="fas fa-xmark"></i></button>
                </div>
                <div style="position:relative; margin-bottom:10px;">
                    <i class="fas fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--gray); font-size:13px;"></i>
                    <input type="text" id="pickerSearch" placeholder="Cari nama atau NIP..." oninput="renderPickerList()" style="width:100%; padding:10px 12px 10px 36px; border:1.5px solid var(--card-border); border-radius:12px; font-size:13px; background:var(--card-bg); color:var(--dark); outline:none;">
                </div>
                <div style="display:flex; gap:8px; margin-bottom:4px;">
                    <button type="button" onclick="pickerCheckAll(true)" style="flex:1; padding:8px; border:1.5px solid rgba(16,185,129,0.2); border-radius:10px; font-size:11px; font-weight:600; background:rgba(16,185,129,0.05); color:#10b981; cursor:pointer;"><i class="fas fa-check-double" style="margin-right:4px;"></i>Pilih Semua</button>
                    <button type="button" onclick="pickerCheckAll(false)" style="flex:1; padding:8px; border:1.5px solid rgba(239,68,68,0.2); border-radius:10px; font-size:11px; font-weight:600; background:rgba(239,68,68,0.05); color:#ef4444; cursor:pointer;"><i class="fas fa-xmark" style="margin-right:4px;"></i>Hapus Semua</button>
                </div>
            </div>
            <div id="pickerList" style="flex:1; overflow-y:auto; padding:8px 16px;"></div>
            <div style="padding:14px 20px; border-top:1px solid var(--card-border); display:flex; gap:10px;">
                <button type="button" onclick="closeUserPicker()" style="flex:1; padding:13px; border-radius:12px; border:1.5px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-size:14px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="button" onclick="confirmUserPicker()" style="flex:1; padding:13px; border-radius:12px; border:none; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; font-size:14px; font-weight:600; cursor:pointer;">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Photo Preview -->
<div id="photoPreview" class="photo-preview-overlay">
    <div class="photo-preview-bg"></div>
    <button class="photo-preview-close" onclick="closePhotoPreview()"><i class="fas fa-xmark"></i></button>
    <div class="photo-preview-content" id="photoPreviewContent">
        @if($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil))
        <img src="{{ asset('public/storage/foto_profil/' . $user->foto_profil) }}" alt="Foto Profil">
        @else
        <div class="pp-placeholder" style="color:#fff; font-size:80px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); display:flex; align-items:center; justify-content:center; font-weight:700;">
            {{ collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->take(2)->join('') }}
        </div>
        @endif
    </div>
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
<div id="editModal" class="modal-overlay" style="padding:0;">
    <div style="background:var(--card-bg); width:100%; height:100%; display:flex; flex-direction:column;">
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

    function openPhotoPreview() {
        var avatar = document.querySelector('.profile-avatar');
        var content = document.getElementById('photoPreviewContent');
        var overlay = document.getElementById('photoPreview');
        if (!avatar || !content) return;

        var r = avatar.getBoundingClientRect();
        var cx = window.innerWidth / 2;
        var cy = window.innerHeight / 2;
        var dx = r.left + r.width / 2 - cx;
        var dy = r.top + r.height / 2 - cy;

        content.style.transition = 'none';
        content.style.transform = 'translate(' + dx + 'px,' + dy + 'px)';
        content.style.width = r.width + 'px';
        content.style.height = r.height + 'px';
        content.style.borderRadius = '50%';

        overlay.style.display = 'flex';
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                content.style.transition = '';
                overlay.classList.add('visible');
                content.style.transform = 'translate(0,0)';
            });
        });
    }
    function closePhotoPreview() {
        var avatar = document.querySelector('.profile-avatar');
        var content = document.getElementById('photoPreviewContent');
        var overlay = document.getElementById('photoPreview');
        if (!avatar || !content) return;

        var r = avatar.getBoundingClientRect();
        var cx = window.innerWidth / 2;
        var cy = window.innerHeight / 2;
        var dx = r.left + r.width / 2 - cx;
        var dy = r.top + r.height / 2 - cy;

        overlay.classList.remove('visible');
        content.style.transform = 'translate(' + dx + 'px,' + dy + 'px)';
        content.style.width = r.width + 'px';
        content.style.height = r.height + 'px';
        content.style.borderRadius = '50%';

        setTimeout(function() { overlay.style.display = 'none'; }, 350);
    }
    document.getElementById('photoPreview').addEventListener('click', function(e) {
        if (e.target === this || e.target.classList.contains('photo-preview-bg')) closePhotoPreview();
    });

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
        var size = C.circleR * 2;
        var ctx = _cropOverlay.getContext('2d');
        ctx.fillStyle = 'rgba(0,0,0,0.55)';
        ctx.fillRect(0, 0, w, h);
        ctx.globalCompositeOperation = 'destination-out';
        ctx.fillRect(cx - C.circleR, cy - C.circleR, size, size);
        ctx.globalCompositeOperation = 'source-over';
        ctx.strokeStyle = 'rgba(255,255,255,0.5)';
        ctx.lineWidth = 2;
        ctx.strokeRect(cx - C.circleR, cy - C.circleR, size, size);
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

    // === Tester: Reset Presensi ===
    function resetPresensi(type) {
        if (!confirm('Reset presensi ' + (type === 'all' ? 'reguler + lembur' : type) + ' hari ini?')) return;
        var statusEl = document.getElementById('resetStatus');
        fetch('/pegawai/akun/reset-presensi', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ type: type })
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (statusEl) {
                statusEl.style.display = 'block';
                statusEl.textContent = d.message || 'Berhasil direset';
                setTimeout(function() { statusEl.style.display = 'none'; }, 3000);
            }
        })
        .catch(function() {
            if (statusEl) { statusEl.style.display = 'block'; statusEl.style.color = '#ef4444'; statusEl.textContent = 'Gagal reset'; }
        });
    }

    // === Face Mode Tabs ===
    function setFaceMode(el, val) {
        document.querySelectorAll('.face-mode-tab').forEach(function(t) { t.classList.remove('active'); });
        el.classList.add('active');
        var hidden = document.getElementById('faceMode');
        hidden.value = val;
        saveSetting(hidden);
        var btn = document.getElementById('faceUserBtn');
        if (btn) btn.style.display = val !== 'all' ? '' : 'none';
    }

    // === Tester: Izin & Cache ===
    function reRequestPermissions() {
        var statusEl = document.getElementById('permStatus');
        var results = [];

        // Kamera
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video:true })
                .then(function(s) { s.getTracks().forEach(function(t){t.stop();}); results.push('Kamera: OK'); updatePermStatus(results); })
                .catch(function() { results.push('Kamera: Ditolak'); updatePermStatus(results); });
        } else { results.push('Kamera: Tidak didukung'); }

        // Lokasi
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function() { results.push('Lokasi: OK'); updatePermStatus(results); },
                function() { results.push('Lokasi: Ditolak'); updatePermStatus(results); },
                { timeout:10000 }
            );
        } else { results.push('Lokasi: Tidak didukung'); }

        // Notifikasi
        if ('Notification' in window) {
            Notification.requestPermission().then(function(r) {
                results.push('Notifikasi: ' + (r === 'granted' ? 'OK' : r === 'denied' ? 'Ditolak' : 'Belum'));
                updatePermStatus(results);
            });
        } else { results.push('Notifikasi: Tidak didukung'); }

        if (statusEl) statusEl.textContent = 'Meminta izin...';
    }

    function updatePermStatus(results) {
        var el = document.getElementById('permStatus');
        if (el) el.textContent = results.join(' · ');
    }

    function clearAllCache() {
        var statusEl = document.getElementById('permStatus');
        if (statusEl) statusEl.textContent = 'Membersihkan...';
        var tasks = [];

        // Clear caches
        if (window.caches) {
            tasks.push(caches.keys().then(function(names) {
                return Promise.all(names.map(function(n) { return caches.delete(n); }));
            }));
        }

        // Unregister SW
        if ('serviceWorker' in navigator) {
            tasks.push(navigator.serviceWorker.getRegistrations().then(function(regs) {
                return Promise.all(regs.map(function(r) { return r.unregister(); }));
            }));
        }

        // Clear localStorage & sessionStorage
        localStorage.clear();
        sessionStorage.clear();

        Promise.all(tasks).then(function() {
            if (statusEl) statusEl.textContent = 'Cache, SW, storage dibersihkan. Reload...';
            setTimeout(function() { location.reload(true); }, 1000);
        }).catch(function() {
            if (statusEl) statusEl.textContent = 'Gagal membersihkan';
        });
    }

    // === Tester: Wilayah Kerja Modal ===
    function toggleWilayahItem(el) {
        el.classList.toggle('active');
    }

    function confirmWilayah() {
        var ids = [];
        document.querySelectorAll('.tester-wl-item.active').forEach(function(el) {
            ids.push(parseInt(el.getAttribute('data-id')));
        });
        fetch('/pegawai/akun/set-wilayah', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ wilayah_ids: ids })
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            document.getElementById('wilayahCount').textContent = ids.length;
            showSuccess(d.message || 'Wilayah diperbarui');
            closeModal('wilayahModal');
        })
        .catch(function() { showError('Gagal menyimpan'); });
    }

    // === Admin Settings (toggle + dropdown + user picker) ===
    function saveSetting(el) {
        var key = el.getAttribute('data-key');
        var val = el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
        fetch('/pegawai/akun/save-setting', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ key: key, value: val })
        }).catch(function() {});

        if (key === 'enable_face_detection') {
            var sec = document.getElementById('faceSubSection');
            if (sec) sec.style.display = el.checked ? '' : 'none';
        }
        if (key === 'face_detection_mode') {
            var btn = document.getElementById('faceUserBtn');
            if (btn) btn.style.display = el.value !== 'all' ? '' : 'none';
        }
    }

    @if(in_array($user->role, ['admin', 'superadmin']))
    var _allUsers = @json($allPegawai);
    var _pickerTarget = '';
    var _pickerSelected = [];
    var _faceUserIds = @json($s_faceUsers);

    function openUserPicker(target) {
        _pickerTarget = target;
        _pickerSelected = _faceUserIds.filter(function(v, i, a) { return a.indexOf(v) === i; });
        var modeEl = document.getElementById('faceMode');
        var modeLabel = modeEl.value === 'except' ? 'Kecuali' : 'Hanya Untuk';
        document.getElementById('pickerTitle').textContent = 'Face Detection — ' + modeLabel;
        document.getElementById('pickerSearch').value = '';
        renderPickerList();
        openModal('akunUserPicker');
    }
    function closeUserPicker() { closeModal('akunUserPicker'); }

    function pickerCheckAll(state) {
        _pickerSelected = state ? _allUsers.map(function(u) { return u.id; }) : [];
        renderPickerList();
    }

    function getInitials(name) {
        return name.split(' ').map(function(w) { return w.charAt(0); }).slice(0, 2).join('').toUpperCase();
    }
    var _avatarColors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#84cc16'];

    function renderPickerList() {
        var q = (document.getElementById('pickerSearch').value || '').toLowerCase();
        var checked = [], unchecked = [];
        _allUsers.forEach(function(u, idx) {
            if (q && u.name.toLowerCase().indexOf(q) === -1 && (u.nip || '').indexOf(q) === -1) return;
            var isSel = _pickerSelected.indexOf(u.id) !== -1;
            var color = _avatarColors[idx % _avatarColors.length];
            var initials = getInitials(u.name);
            var item = '<div class="picker-user-item ' + (isSel ? 'active' : '') + '" data-uid="' + u.id + '" onclick="pickerToggleCard(this,' + u.id + ')">' +
                '<div class="picker-user-check"><i class="fas fa-check"></i></div>' +
                '<div style="width:36px;height:36px;border-radius:10px;background:' + color + ';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">' + initials + '</div>' +
                '<div style="flex:1;min-width:0;">' +
                '<div style="font-size:13px;font-weight:600;color:var(--dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + u.name + '</div>' +
                '<div style="font-size:10px;color:var(--gray);">' + (u.nip || '-') + '</div></div></div>';
            isSel ? checked.push(item) : unchecked.push(item);
        });
        var html = '';
        if (checked.length > 0) {
            html += '<div style="font-size:10px;font-weight:700;color:#10b981;text-transform:uppercase;letter-spacing:0.5px;padding:8px 4px 6px;display:flex;align-items:center;gap:6px;"><i class="fas fa-circle-check" style="font-size:9px;"></i>Dipilih (' + checked.length + ')</div>' + checked.join('');
        }
        if (unchecked.length > 0) {
            if (checked.length > 0) html += '<div style="height:1px;background:var(--card-border);margin:8px 0;"></div>';
            html += '<div style="font-size:10px;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:0.5px;padding:8px 4px 6px;display:flex;align-items:center;gap:6px;"><i class="fas fa-circle" style="font-size:9px;"></i>Belum dipilih (' + unchecked.length + ')</div>' + unchecked.join('');
        }
        document.getElementById('pickerList').innerHTML = html || '<div style="padding:30px;text-align:center;color:var(--gray);font-size:13px;"><i class="fas fa-search" style="font-size:24px;opacity:0.3;display:block;margin-bottom:8px;"></i>Tidak ditemukan</div>';
        document.getElementById('pickerCount').textContent = _pickerSelected.length;
    }

    function pickerToggle(id, chk) {
        if (chk && _pickerSelected.indexOf(id) === -1) _pickerSelected.push(id);
        if (!chk) _pickerSelected = _pickerSelected.filter(function(x) { return x !== id; });
        renderPickerList();
    }

    function pickerToggleCard(el, id) {
        var isSel = _pickerSelected.indexOf(id) !== -1;
        if (isSel) {
            _pickerSelected = _pickerSelected.filter(function(x) { return x !== id; });
        } else {
            if (_pickerSelected.indexOf(id) === -1) _pickerSelected.push(id);
        }
        renderPickerList();
    }

    function confirmUserPicker() {
        _faceUserIds = _pickerSelected.filter(function(v, i, a) { return a.indexOf(v) === i; });
        document.getElementById('faceUserCount').textContent = _faceUserIds.length;
        fetch('/pegawai/akun/save-setting', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ key: 'face_detection_users', value: JSON.stringify(_faceUserIds) })
        }).catch(function() {});
        closeUserPicker();
    }
    @endif
</script>
@endsection
