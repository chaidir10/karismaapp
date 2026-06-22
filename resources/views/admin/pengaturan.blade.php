@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div>
    <div class="page-header-glass">
        <h1>Pengaturan</h1>
        <p>Konfigurasi sistem aplikasi</p>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2" style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); color:var(--dm-text,#15803d);">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

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
                    $faceUserIds = json_decode($settings['face_detection_users'] ?? '[]', true) ?: [];
                    $faceMode = $settings['face_detection_mode'] ?? 'all';
                @endphp
                <div id="faceDetectUserSection" style="margin-top:12px; {{ ($settings['enable_face_detection'] ?? '1') !== '1' ? 'display:none;' : '' }}">
                    <select name="face_detection_mode" id="faceDetectMode" style="width:100%; padding:9px 12px; border:1px solid var(--dm-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-card,#fff); color:var(--dm-text); outline:none; margin-bottom:8px;">
                        <option value="all" {{ $faceMode === 'all' ? 'selected' : '' }}>Semua pegawai wajib face detection</option>
                        <option value="except" {{ $faceMode === 'except' ? 'selected' : '' }}>Aktifkan kecuali...</option>
                        <option value="only" {{ $faceMode === 'only' ? 'selected' : '' }}>Aktifkan hanya untuk...</option>
                    </select>
                    <div id="faceUserBtn" style="display:{{ $faceMode !== 'all' ? 'block' : 'none' }};">
                        <button type="button" onclick="openUserModal('face')" style="width:100%; padding:9px 12px; border:1px dashed var(--dm-border,#d1d5db); border-radius:8px; font-size:12px; color:var(--dm-muted,#64748b); background:var(--dm-bg,#f9fafb); cursor:pointer; text-align:left;">
                            <i class="fas fa-user-group" style="margin-right:6px;"></i>
                            <span id="faceUserCount">{{ count($faceUserIds) }}</span> pegawai dipilih — <span style="color:var(--dm-text,#2E97D4);">Ubah</span>
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
                            <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Aktifkan Absen Darurat</div>
                            <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Halaman absen alternatif tanpa cache dan face detection. Aktifkan jika sistem utama mengalami gangguan.</div>
                        </div>
                        <div class="relative ml-4 flex-shrink-0">
                            <input type="checkbox" name="enable_absen_darurat" value="1" class="sr-only peer" id="toggleDarurat" {{ ($settings['enable_absen_darurat'] ?? '0') === '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                        </div>
                    </label>

                    @php
                        $allowedIds = json_decode($settings['absen_darurat_users'] ?? '[]', true) ?: [];
                        $daruratMode = $settings['absen_darurat_mode'] ?? 'all';
                    @endphp
                    <div id="daruratUserSection" style="margin-top:12px; {{ ($settings['enable_absen_darurat'] ?? '0') !== '1' ? 'display:none;' : '' }}">
                        <select name="absen_darurat_mode" id="daruratMode" style="width:100%; padding:9px 12px; border:1px solid var(--dm-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-card,#fff); color:var(--dm-text); outline:none; margin-bottom:8px;">
                            <option value="all" {{ $daruratMode === 'all' ? 'selected' : '' }}>Semua pegawai boleh akses</option>
                            <option value="except" {{ $daruratMode === 'except' ? 'selected' : '' }}>Aktifkan kecuali...</option>
                            <option value="only" {{ $daruratMode === 'only' ? 'selected' : '' }}>Aktifkan hanya untuk...</option>
                        </select>
                        <div id="daruratUserBtn" style="display:{{ $daruratMode !== 'all' ? 'block' : 'none' }};">
                            <button type="button" onclick="openUserModal('darurat')" style="width:100%; padding:9px 12px; border:1px dashed var(--dm-border,#d1d5db); border-radius:8px; font-size:12px; color:var(--dm-muted,#64748b); background:var(--dm-bg,#f9fafb); cursor:pointer; text-align:left;">
                                <i class="fas fa-user-group" style="margin-right:6px;"></i>
                                <span id="daruratUserCount">{{ count($allowedIds) }}</span> pegawai dipilih — <span style="color:var(--dm-text,#2E97D4);">Ubah</span>
                            </button>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for selected users -->
        <div id="faceHiddenInputs">
            @foreach($faceUserIds as $uid)
            <input type="hidden" name="face_detection_users[]" value="{{ $uid }}">
            @endforeach
        </div>
        <div id="daruratHiddenInputs">
            @foreach($allowedIds as $uid)
            <input type="hidden" name="absen_darurat_users[]" value="{{ $uid }}">
            @endforeach
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Simpan Pengaturan
        </button>
    </form>

    <!-- User Picker Modal -->
    <div id="userPickerModal" style="display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,0.4); align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s ease;" onclick="if(event.target===this)closeUserModal()">
        <div id="userPickerInner" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:16px; width:90%; max-width:480px; max-height:80vh; display:flex; flex-direction:column; overflow:hidden; transform:translateY(12px); opacity:0; transition:transform 0.2s ease, opacity 0.2s ease;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--dm-border,#e2e8f0);">
                <h3 style="font-size:15px; font-weight:700; color:var(--dm-text,#1e293b); margin:0;" id="userModalTitle">Pilih Pegawai</h3>
                <button onclick="closeUserModal()" style="width:32px;height:32px;border-radius:8px;border:none;background:var(--dm-bg,#f1f5f9);color:var(--dm-muted,#64748b);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;"><i class="fas fa-times"></i></button>
            </div>
            <div style="padding:12px 20px 8px;">
                <input type="text" id="userModalSearch" placeholder="Cari nama pegawai..." oninput="filterUserModal()" style="width:100%; padding:8px 12px; border:1px solid var(--dm-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-card,#fff); color:var(--dm-text); outline:none;">
            </div>
            <div style="flex:1; overflow-y:auto; padding:8px 20px;" id="userModalList"></div>
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--dm-border,#e2e8f0);">
                <div style="font-size:12px; color:var(--dm-muted,#64748b);"><span id="userModalSelected">0</span> dipilih</div>
                <div style="display:flex; gap:8px;">
                    <button type="button" onclick="closeUserModal()" class="btn-secondary" style="padding:8px 16px;">Batal</button>
                    <button type="button" onclick="confirmUserModal()" class="btn-primary" style="padding:8px 16px;">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('toggleDarurat').addEventListener('change', function() {
        document.getElementById('daruratUserSection').style.display = this.checked ? '' : 'none';
    });
    document.getElementById('toggleFaceDetect').addEventListener('change', function() {
        document.getElementById('faceDetectUserSection').style.display = this.checked ? '' : 'none';
    });
    document.getElementById('faceDetectMode').addEventListener('change', function() {
        document.getElementById('faceUserBtn').style.display = this.value !== 'all' ? 'block' : 'none';
    });
    document.getElementById('daruratMode').addEventListener('change', function() {
        document.getElementById('daruratUserBtn').style.display = this.value !== 'all' ? 'block' : 'none';
    });

    // User picker modal
    var allUsers = @json(\App\Models\User::where('role','!=','superadmin')->orderBy('name')->get(['id','name','nip']));
    var currentTarget = '';
    var tempSelected = [];

    function openUserModal(target) {
        currentTarget = target;
        var container = document.getElementById(target === 'face' ? 'faceHiddenInputs' : 'daruratHiddenInputs');
        tempSelected = Array.from(container.querySelectorAll('input')).map(function(i) { return parseInt(i.value); });
        document.getElementById('userModalTitle').textContent = target === 'face' ? 'Pilih Pegawai (Face Detection)' : 'Pilih Pegawai (Absen Darurat)';
        document.getElementById('userModalSearch').value = '';
        renderUserList('');
        var modal = document.getElementById('userPickerModal');
        var inner = document.getElementById('userPickerInner');
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
        setTimeout(function() { modal.style.display = 'none'; }, 200);
    }

    function filterUserModal() { renderUserList(document.getElementById('userModalSearch').value.toLowerCase()); }

    function renderUserList(q) {
        var html = '';
        var count = 0;
        allUsers.forEach(function(u) {
            if (q && u.name.toLowerCase().indexOf(q) === -1 && (u.nip || '').indexOf(q) === -1) return;
            var checked = tempSelected.indexOf(u.id) !== -1;
            if (checked) count++;
            html += '<label style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--dm-border,#f1f5f9);cursor:pointer;">' +
                '<input type="checkbox" ' + (checked ? 'checked' : '') + ' onchange="toggleUser(' + u.id + ',this.checked)">' +
                '<div><div style="font-size:13px;font-weight:500;color:var(--dm-text,#1e293b);">' + u.name + '</div>' +
                '<div style="font-size:10px;color:var(--dm-muted,#94a3b8);">' + (u.nip || '-') + '</div></div></label>';
        });
        document.getElementById('userModalList').innerHTML = html || '<div style="padding:20px;text-align:center;color:var(--dm-muted,#94a3b8);font-size:13px;">Tidak ditemukan</div>';
        updateSelectedCount();
    }

    function toggleUser(id, checked) {
        if (checked && tempSelected.indexOf(id) === -1) tempSelected.push(id);
        if (!checked) tempSelected = tempSelected.filter(function(x) { return x !== id; });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        document.getElementById('userModalSelected').textContent = tempSelected.length;
    }

    function confirmUserModal() {
        var containerId = currentTarget === 'face' ? 'faceHiddenInputs' : 'daruratHiddenInputs';
        var countId = currentTarget === 'face' ? 'faceUserCount' : 'daruratUserCount';
        var inputName = currentTarget === 'face' ? 'face_detection_users[]' : 'absen_darurat_users[]';
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
</script>
@endpush
@endsection
