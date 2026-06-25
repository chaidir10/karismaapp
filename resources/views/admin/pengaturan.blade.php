@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div>
    <div class="page-header-glass">
        <h1>Pengaturan</h1>
        <p>Konfigurasi sistem aplikasi</p>
    </div>


    <!-- Logo Aplikasi -->
    <div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);">Logo Aplikasi</h2>
        </div>
        <div class="px-6 py-4">
            <div class="text-xs mb-3" style="color:var(--dm-muted,#64748b);">Logo akan tampil di sidebar, tab browser, halaman login, pendaftaran, dan download. Rekomendasi: gambar persegi, minimal 512x512px, format PNG.</div>
            <form method="POST" action="{{ route('admin.pengaturan.upload-logo') }}" enctype="multipart/form-data" id="logoForm">
                @csrf
                <div style="display:flex; align-items:center; gap:16px;">
                    <div id="logoPreviewBox" style="width:64px; height:64px; border-radius:14px; border:2px dashed var(--dm-border,#d1d5db); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; background:var(--dm-bg,#f9fafb);">
                        @if($appLogoUrl)
                            <img src="{{ $appLogoUrl }}" alt="Logo" style="width:100%; height:100%; object-fit:contain;">
                        @else
                            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:16px;">K</div>
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <input type="file" name="app_logo" id="logoInput" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="display:none;">
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            <button type="button" onclick="document.getElementById('logoInput').click()" class="btn-primary" style="padding:7px 14px; font-size:12px;"><i class="fas fa-upload" style="margin-right:4px;"></i> Pilih Logo</button>
                            @if($appLogoUrl)
                            <button type="button" onclick="removeLogo()" class="btn-danger" style="padding:7px 14px; font-size:12px;"><i class="fas fa-trash" style="margin-right:4px;"></i> Hapus</button>
                            @endif
                        </div>
                        <div id="logoFileName" style="font-size:11px; color:var(--dm-muted,#94a3b8); margin-top:6px;"></div>
                    </div>
                </div>
            </form>
            <form method="POST" action="{{ route('admin.pengaturan.remove-logo') }}" id="removeLogoForm" style="display:none;">@csrf @method('DELETE')</form>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.pengaturan.update') }}">
        @csrf

        <div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
            <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
                <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);">Presensi</h2>
            </div>
            <div class="px-6 py-4">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Nonaktifkan presensi reguler di hari libur</div>
                        <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Tombol masuk & pulang reguler akan di-disable saat hari libur/weekend. Lembur tetap bisa digunakan.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="disable_presensi_hari_libur" value="1" class="sr-only peer" {{ ($settings['disable_presensi_hari_libur'] ?? '1') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Face Detection -->
        <div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
            <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
                <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);">Keamanan</h2>
            </div>
            <div class="px-6 py-4 space-y-4">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Aktifkan Face Detection</div>
                        <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Wajah harus terdeteksi sebelum bisa absen. Jika dinonaktifkan, pegawai bisa langsung absen tanpa verifikasi wajah.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="enable_face_detection" value="1" class="sr-only peer" id="toggleFaceDetect" {{ ($settings['enable_face_detection'] ?? '1') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>

                @php
                    $faceMode = $settings['face_detection_mode'] ?? 'all';
                    $faceExceptIds = json_decode($settings['face_detection_users_except'] ?? '[]', true) ?: [];
                    $faceOnlyIds = json_decode($settings['face_detection_users_only'] ?? '[]', true) ?: [];
                    $faceUserIds = $faceMode === 'except' ? $faceExceptIds : $faceOnlyIds;
                @endphp
                <input type="hidden" name="face_detection_mode" id="faceDetectMode" value="{{ $faceMode }}">
                <div id="faceDetectUserSection" style="margin-top:12px; {{ ($settings['enable_face_detection'] ?? '1') !== '1' ? 'display:none;' : '' }}">
                    <div class="setting-tabs" style="margin-bottom:8px;">
                        <button type="button" class="setting-tab {{ $faceMode === 'all' ? 'active' : '' }}" onclick="event.preventDefault();setAdminMode('face',this,'all')"><i class="fas fa-users"></i> Semua</button>
                        <button type="button" class="setting-tab {{ $faceMode === 'except' ? 'active' : '' }}" onclick="event.preventDefault();setAdminMode('face',this,'except')"><i class="fas fa-user-minus"></i> Kecuali</button>
                        <button type="button" class="setting-tab {{ $faceMode === 'only' ? 'active' : '' }}" onclick="event.preventDefault();setAdminMode('face',this,'only')"><i class="fas fa-user-check"></i> Hanya</button>
                    </div>
                    <div id="faceUserBtn" style="display:{{ $faceMode !== 'all' ? 'block' : 'none' }};">
                        <button type="button" onclick="event.preventDefault();openUserModal('face')" class="setting-user-btn">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:26px; height:26px; border-radius:7px; background:#5AB6EA; display:flex; align-items:center; justify-content:center; color:#fff; font-size:10px;"><i class="fas fa-user-group"></i></div>
                                <span><strong id="faceUserCount" style="color:#5AB6EA;">{{ count($faceUserIds) }}</strong> pegawai dipilih</span>
                            </div>
                            <i class="fas fa-chevron-right" style="font-size:10px; opacity:0.3;"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Wajib masuk sebelum pulang</div>
                            <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Tombol pulang reguler hanya aktif jika pegawai sudah presensi masuk reguler di hari tersebut.</div>
                        </div>
                        <div class="relative ml-4 flex-shrink-0">
                            <input type="checkbox" name="require_masuk_before_pulang" value="1" class="sr-only peer" {{ ($settings['require_masuk_before_pulang'] ?? '1') === '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                        </div>
                    </label>
                <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Timer Jam Kerja</div>
                            <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Tampilkan timer jam kerja berjalan di dashboard pegawai. Nonaktifkan jika menyebabkan gangguan pada kamera/lokasi.</div>
                        </div>
                        <div class="relative ml-4 flex-shrink-0">
                            <input type="checkbox" name="enable_work_timer" value="1" class="sr-only peer" {{ ($settings['enable_work_timer'] ?? '1') === '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                        </div>
                    </label>
                </div>

                <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Aktifkan Absen Darurat</div>
                            <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Halaman absen alternatif tanpa cache dan face detection. Aktifkan jika sistem utama mengalami gangguan.</div>
                        </div>
                        <div class="relative ml-4 flex-shrink-0">
                            <input type="checkbox" name="enable_absen_darurat" value="1" class="sr-only peer" id="toggleDarurat" {{ ($settings['enable_absen_darurat'] ?? '0') === '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                        </div>
                    </label>

                    @php
                        $daruratMode = $settings['absen_darurat_mode'] ?? 'all';
                        $daruratExceptIds = json_decode($settings['absen_darurat_users_except'] ?? '[]', true) ?: [];
                        $daruratOnlyIds = json_decode($settings['absen_darurat_users_only'] ?? '[]', true) ?: [];
                        $allowedIds = $daruratMode === 'except' ? $daruratExceptIds : $daruratOnlyIds;
                    @endphp
                    <input type="hidden" name="absen_darurat_mode" id="daruratMode" value="{{ $daruratMode }}">
                    <div id="daruratUserSection" style="margin-top:12px; {{ ($settings['enable_absen_darurat'] ?? '0') !== '1' ? 'display:none;' : '' }}">
                        <div class="setting-tabs" style="margin-bottom:8px;">
                            <button type="button" class="setting-tab {{ $daruratMode === 'all' ? 'active-red' : '' }}" onclick="event.preventDefault();setAdminMode('darurat',this,'all')"><i class="fas fa-users"></i> Semua</button>
                            <button type="button" class="setting-tab {{ $daruratMode === 'except' ? 'active-red' : '' }}" onclick="event.preventDefault();setAdminMode('darurat',this,'except')"><i class="fas fa-user-minus"></i> Kecuali</button>
                            <button type="button" class="setting-tab {{ $daruratMode === 'only' ? 'active-red' : '' }}" onclick="event.preventDefault();setAdminMode('darurat',this,'only')"><i class="fas fa-user-check"></i> Hanya</button>
                        </div>
                        <div id="daruratUserBtn" style="display:{{ $daruratMode !== 'all' ? 'block' : 'none' }};">
                            <button type="button" onclick="event.preventDefault();openUserModal('darurat')" class="setting-user-btn">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:26px; height:26px; border-radius:7px; background:#ef4444; display:flex; align-items:center; justify-content:center; color:#fff; font-size:10px;"><i class="fas fa-user-group"></i></div>
                                    <span><strong id="daruratUserCount" style="color:#ef4444;">{{ count($allowedIds) }}</strong> pegawai dipilih</span>
                                </div>
                                <i class="fas fa-chevron-right" style="font-size:10px; opacity:0.3;"></i>
                            </button>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for selected users (per mode) -->
        <div id="faceExceptInputs">
            @foreach($faceExceptIds as $uid)
            <input type="hidden" name="face_detection_users_except[]" value="{{ $uid }}">
            @endforeach
        </div>
        <div id="faceOnlyInputs">
            @foreach($faceOnlyIds as $uid)
            <input type="hidden" name="face_detection_users_only[]" value="{{ $uid }}">
            @endforeach
        </div>
        <div id="daruratExceptInputs">
            @foreach($daruratExceptIds as $uid)
            <input type="hidden" name="absen_darurat_users_except[]" value="{{ $uid }}">
            @endforeach
        </div>
        <div id="daruratOnlyInputs">
            @foreach($daruratOnlyIds as $uid)
            <input type="hidden" name="absen_darurat_users_only[]" value="{{ $uid }}">
            @endforeach
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Simpan Pengaturan
        </button>
    </form>

    <!-- User Picker Modal -->
    <div id="userPickerModal" style="display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,0.4); align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s ease;">
        <div id="userPickerInner" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:16px; width:90%; max-width:480px; max-height:80vh; display:flex; flex-direction:column; overflow:hidden; transform:translateY(12px); opacity:0; transition:transform 0.2s ease, opacity 0.2s ease;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--dm-border,#e2e8f0);">
                <h3 style="font-size:15px; font-weight:700; color:var(--dm-text,#1e293b); margin:0;" id="userModalTitle">Pilih Pegawai</h3>
                <button onclick="closeUserModal()" style="width:32px;height:32px;border-radius:8px;border:none;background:var(--dm-bg,#f1f5f9);color:var(--dm-muted,#64748b);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;"><i class="fas fa-times"></i></button>
            </div>
            <div style="padding:12px 20px 0;">
                <input type="text" id="userModalSearch" placeholder="Cari nama pegawai..." oninput="filterUserModal()" style="width:100%; padding:8px 12px; border:1px solid var(--dm-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-card,#fff); color:var(--dm-text); outline:none; margin-bottom:8px;">
                <div style="display:flex; gap:8px;">
                    <button type="button" onclick="checkAll(true)" style="flex:1; padding:6px; border:1px solid var(--dm-border,#d1d5db); border-radius:6px; font-size:11px; font-weight:600; background:var(--dm-bg,#f9fafb); color:var(--dm-text,#374151); cursor:pointer;"><i class="fas fa-check-double" style="margin-right:4px;color:#10b981;"></i> Pilih Semua</button>
                    <button type="button" onclick="checkAll(false)" style="flex:1; padding:6px; border:1px solid var(--dm-border,#d1d5db); border-radius:6px; font-size:11px; font-weight:600; background:var(--dm-bg,#f9fafb); color:var(--dm-text,#374151); cursor:pointer;"><i class="fas fa-xmark" style="margin-right:4px;color:#ef4444;"></i> Hapus Semua</button>
                </div>
            </div>
            <div style="flex:1; overflow-y:auto; padding:8px 20px;" id="userModalList"></div>
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--dm-border,#e2e8f0);">
                <div style="font-size:12px; color:var(--dm-muted,#64748b);"><span id="userModalSelected">0</span> dipilih</div>
                <div style="display:flex; gap:8px;">
                    <button type="button" id="modalBtnBatal" class="btn-secondary" style="padding:8px 16px;">Batal</button>
                    <button type="button" id="modalBtnSimpan" class="btn-primary" style="padding:8px 16px;">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
    .sr-only { position:absolute !important; width:1px !important; height:1px !important; overflow:hidden !important; clip:rect(0,0,0,0) !important; white-space:nowrap !important; border:0 !important; padding:0 !important; margin:-1px !important; top:auto !important; left:auto !important; }
    .setting-tabs { display:flex; gap:4px; padding:3px; background:var(--dm-bg,#f1f5f9); border-radius:10px; border:1px solid var(--dm-border,#e2e8f0); }
    .setting-tab { flex:1; padding:7px 6px; border:none; border-radius:8px; font-size:12px; font-weight:600; background:transparent; color:var(--dm-muted,#94a3b8); cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px; transition:all 0.2s; }
    .setting-tab:hover { color:var(--dm-text,#475569); }
    .setting-tab.active { background:#5AB6EA; color:#fff; box-shadow:0 2px 6px rgba(90,182,234,0.25); }
    .setting-tab.active-red { background:#ef4444; color:#fff; box-shadow:0 2px 6px rgba(239,68,68,0.25); }
    .setting-user-btn { display:flex; align-items:center; justify-content:space-between; width:100%; padding:9px 12px; border:1.5px solid var(--dm-border,#e2e8f0); border-radius:10px; font-size:12px; color:var(--dm-text,#374151); background:var(--dm-card,#fff); cursor:pointer; transition:border-color 0.2s; }
    .setting-user-btn:hover { border-color:#5AB6EA; }
    [data-theme="dark"] .setting-tabs { background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.06); }
    [data-theme="dark"] .setting-tab { color:rgba(255,255,255,0.4); }
    [data-theme="dark"] .setting-tab:hover { color:rgba(255,255,255,0.7); }
</style>
@endpush
@push('scripts')
<script>
    // Scroll persistence — save continuously, restore on load
    var mainContent = document.querySelector('.main-content');

    function getScroll() { return mainContent ? mainContent.scrollTop : window.scrollY; }
    function setScroll(pos) {
        if (mainContent) mainContent.scrollTop = pos;
        else window.scrollTo(0, pos);
    }

    // Continuously save scroll position
    (mainContent || window).addEventListener('scroll', function() {
        sessionStorage.setItem('pengaturan-scroll', getScroll());
    }, { passive: true });

    // Also save before unload (form submit, navigation)
    window.addEventListener('beforeunload', function() {
        sessionStorage.setItem('pengaturan-scroll', getScroll());
    });

    // Restore on load
    var savedPos = sessionStorage.getItem('pengaturan-scroll');
    if (savedPos) {
        var pos = parseInt(savedPos);
        setScroll(pos);
        // Retry restore after layout settles
        requestAnimationFrame(function() { setScroll(pos); });
        setTimeout(function() { setScroll(pos); }, 100);
    }

    // Prevent scroll jump on interact — only when modal is NOT open
    function isModalOpen() {
        return document.getElementById('userPickerModal').style.display === 'flex';
    }
    var _lockPos = null;
    document.addEventListener('mousedown', function() {
        if (isModalOpen()) return;
        _lockPos = getScroll();
    }, true);
    document.addEventListener('focus', function() {
        if (isModalOpen()) return;
        var pos = _lockPos !== null ? _lockPos : getScroll();
        _lockPos = null;
        requestAnimationFrame(function() { setScroll(pos); });
    }, true);

    function keepScroll(fn) {
        var pos = getScroll();
        fn();
        requestAnimationFrame(function() { setScroll(pos); });
    }

    document.getElementById('toggleDarurat').addEventListener('change', function() {
        var el = this;
        keepScroll(function() { document.getElementById('daruratUserSection').style.display = el.checked ? '' : 'none'; });
    });
    document.getElementById('toggleFaceDetect').addEventListener('change', function() {
        var el = this;
        keepScroll(function() { document.getElementById('faceDetectUserSection').style.display = el.checked ? '' : 'none'; });
    });
    function setAdminMode(target, btn, val) {
        var tabs = btn.parentElement.querySelectorAll('.setting-tab');
        var activeClass = target === 'darurat' ? 'active-red' : 'active';
        tabs.forEach(function(t) { t.classList.remove('active', 'active-red'); });
        btn.classList.add(activeClass);

        var hiddenId = target === 'face' ? 'faceDetectMode' : 'daruratMode';
        document.getElementById(hiddenId).value = val;

        var btnId = target === 'face' ? 'faceUserBtn' : 'daruratUserBtn';
        document.getElementById(btnId).style.display = val !== 'all' ? 'block' : 'none';

        var countId = target === 'face' ? 'faceUserCount' : 'daruratUserCount';
        var c = document.getElementById(_getContainerId(target, val));
        document.getElementById(countId).textContent = c ? c.querySelectorAll('input').length : 0;
    }

    // User picker modal — separate lists per target+mode
    @php $allPegawai = \App\Models\User::nonTester()->orderBy('name')->get(['id','name','nip']); @endphp
    var allUsers = @json($allPegawai);
    var currentTarget = '';
    var currentMode = '';
    var tempSelected = [];
    function _getContainerId(target, mode) {
        if (target === 'face') return mode === 'except' ? 'faceExceptInputs' : 'faceOnlyInputs';
        return mode === 'except' ? 'daruratExceptInputs' : 'daruratOnlyInputs';
    }

    function openUserModal(target) {
        currentTarget = target;
        var modeEl = document.getElementById(target === 'face' ? 'faceDetectMode' : 'daruratMode');
        currentMode = modeEl.value;
        var container = document.getElementById(_getContainerId(target, currentMode));
        tempSelected = Array.from(container.querySelectorAll('input')).map(function(i) { return parseInt(i.value); });

        var modeLabel = currentMode === 'except' ? 'Kecuali' : 'Hanya Untuk';
        var targetLabel = target === 'face' ? 'Face Detection' : 'Absen Darurat';
        document.getElementById('userModalTitle').textContent = targetLabel + ' — ' + modeLabel;
        document.getElementById('userModalSearch').value = '';
        renderUserList('');
        var modal = document.getElementById('userPickerModal');
        var inner = document.getElementById('userPickerInner');
        document.body.style.overflow = 'hidden';
        modal.style.display = 'flex';
        requestAnimationFrame(function() {
            modal.style.opacity = '1';
            inner.style.transform = 'translateY(0)';
            inner.style.opacity = '1';
        });
        setTimeout(function() { document.getElementById('userModalSearch').focus(); }, 250);
    }

    function closeUserModal() {
        var modal = document.getElementById('userPickerModal');
        var inner = document.getElementById('userPickerInner');
        modal.style.opacity = '0';
        inner.style.transform = 'translateY(12px)';
        inner.style.opacity = '0';
        setTimeout(function() {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 200);
    }

    function filterUserModal() { renderUserList(document.getElementById('userModalSearch').value.toLowerCase()); }

    function checkAll(state) {
        if (state) {
            tempSelected = allUsers.map(function(u) { return u.id; });
        } else {
            tempSelected = [];
        }
        renderUserList(document.getElementById('userModalSearch').value.toLowerCase());
    }

    function renderUserList(q) {
        var checkedItems = [], uncheckedItems = [];
        allUsers.forEach(function(u) {
            if (q && u.name.toLowerCase().indexOf(q) === -1 && (u.nip || '').indexOf(q) === -1) return;
            var isChecked = tempSelected.indexOf(u.id) !== -1;
            var item = '<label style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--dm-border,#f1f5f9);cursor:pointer;">' +
                '<input type="checkbox" ' + (isChecked ? 'checked' : '') + ' onchange="toggleUser(' + u.id + ',this.checked)">' +
                '<div style="flex:1;min-width:0;"><div style="font-size:13px;font-weight:500;color:var(--dm-text,#1e293b);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + u.name + '</div>' +
                '<div style="font-size:10px;color:var(--dm-muted,#94a3b8);">' + (u.nip || '-') + '</div></div></label>';
            isChecked ? checkedItems.push(item) : uncheckedItems.push(item);
        });
        var html = '';
        if (checkedItems.length > 0) {
            html += '<div style="font-size:10px;font-weight:700;color:#10b981;text-transform:uppercase;letter-spacing:0.5px;padding:6px 0 4px;">Dipilih (' + checkedItems.length + ')</div>';
            html += checkedItems.join('');
        }
        if (uncheckedItems.length > 0) {
            if (checkedItems.length > 0) html += '<div style="height:8px;border-bottom:2px solid var(--dm-border,#e2e8f0);margin-bottom:8px;"></div>';
            html += '<div style="font-size:10px;font-weight:700;color:var(--dm-muted,#94a3b8);text-transform:uppercase;letter-spacing:0.5px;padding:6px 0 4px;">Belum dipilih (' + uncheckedItems.length + ')</div>';
            html += uncheckedItems.join('');
        }
        document.getElementById('userModalList').innerHTML = html || '<div style="padding:20px;text-align:center;color:var(--dm-muted,#94a3b8);font-size:13px;">Tidak ditemukan</div>';
        document.getElementById('userModalSelected').textContent = tempSelected.length;
    }

    function toggleUser(id, chk) {
        if (chk && tempSelected.indexOf(id) === -1) tempSelected.push(id);
        if (!chk) tempSelected = tempSelected.filter(function(x) { return x !== id; });
        renderUserList(document.getElementById('userModalSearch').value.toLowerCase());
    }

    function confirmUserModal() {
        var containerId = _getContainerId(currentTarget, currentMode);
        var countId = currentTarget === 'face' ? 'faceUserCount' : 'daruratUserCount';
        var prefix = currentTarget === 'face' ? 'face_detection_users_' : 'absen_darurat_users_';
        var inputName = prefix + currentMode + '[]';
        var container = document.getElementById(containerId);
        container.innerHTML = '';
        tempSelected.forEach(function(id) {
            var inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = inputName; inp.value = id;
            container.appendChild(inp);
        });
        document.getElementById(countId).textContent = tempSelected.length;
        closeUserModal();
    }

    // Bind modal buttons
    document.getElementById('modalBtnSimpan').addEventListener('click', confirmUserModal);
    document.getElementById('modalBtnBatal').addEventListener('click', closeUserModal);

    // Logo upload
    document.getElementById('logoInput').addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            if (typeof adminToast === 'function') adminToast('Ukuran file maksimal 2MB', 'error');
            this.value = '';
            return;
        }
        document.getElementById('logoFileName').textContent = file.name;
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreviewBox').innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:contain;">';
        };
        reader.readAsDataURL(file);
        document.getElementById('logoForm').submit();
    });

    function removeLogo() {
        if (confirm('Hapus logo dan kembali ke default?')) {
            document.getElementById('removeLogoForm').submit();
        }
    }
</script>
@endpush
@endsection
