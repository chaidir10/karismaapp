@extends(Auth::user()->role === 'operator' ? 'layouts.operator' : 'layouts.admin')

@section('title', 'Status Shift Pegawai')

@push('styles')
<style>
    .badge-shift-on  { background:#d1fae5; color:#065f46; }
    .badge-shift-off { background:#f1f5f9; color:#64748b; }
    .shift-row.hidden-row { display:none; }
    .modal-shift { opacity:0; transition:opacity 0.2s ease; }
    .modal-shift.show { opacity:1; }
    .modal-shift .modal-inner { transform:translateY(12px); opacity:0; transition:transform 0.2s ease, opacity 0.2s ease; }
    .modal-shift.show .modal-inner { transform:translateY(0); opacity:1; }
    .cb-row { accent-color:#2E97D4; width:15px; height:15px; cursor:pointer; }
    .bulk-bar { display:none; }
    .bulk-bar.visible { display:flex; }
</style>
@endpush

@section('content')
<div>
    <div class="page-header-glass">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1><i class="fas fa-business-time" style="margin-right:8px;"></i>Status Shift Pegawai</h1>
                <p>Kelola penugasan shift untuk setiap pegawai</p>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="p-5 mb-6 text-sm" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Cari Pegawai</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari nama atau NIP..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl outline-none"
                        style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Status Shift</label>
                <select id="filterStatus" class="w-full px-4 py-2.5 rounded-xl outline-none"
                    style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif Shift</option>
                    <option value="0">Non-Shift</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Filter Shift</label>
                <select id="filterShift" class="w-full px-4 py-2.5 rounded-xl outline-none"
                    style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    <option value="">Semua Shift</option>
                    @foreach($shifts as $shift)
                    <option value="{{ $shift->id }}">{{ $shift->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <button id="resetFilter" class="w-full h-[42px] px-4 py-2 rounded-xl text-sm flex items-center justify-center"
                    style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">
                    <i class="fas fa-sync-alt mr-2"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkBar" class="bulk-bar items-center gap-3 mb-4 px-4 py-3 rounded-xl"
        style="background:#eff6ff; border:1px solid #bfdbfe;">
        <span id="bulkCount" class="text-sm font-semibold" style="color:#1d4ed8;"></span>
        <button onclick="openBulkModal()" class="btn-primary" style="padding:6px 14px; font-size:12px;">
            <i class="fas fa-layer-group mr-1"></i> Atur Shift Massal
        </button>
        <button onclick="clearSelection()" class="text-sm" style="color:#64748b; margin-left:4px;">
            Batal
        </button>
    </div>

    <!-- Table Card -->
    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-color:var(--dm-border,#e2e8f0);">
                <thead style="background:var(--dm-bg,#f9fafb);">
                    <tr>
                        <th colspan="7" class="px-6 py-4 text-right text-sm font-semibold" style="color:var(--dm-text,#374151);">
                            Tampil: <span id="countVisible">{{ $users->count() }}</span> / {{ $users->count() }} pegawai
                        </th>
                    </tr>
                    <tr>
                        <th class="px-4 py-3 text-center" style="width:40px;">
                            <input type="checkbox" class="cb-row" id="cbAll" onchange="toggleAll(this)">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151); width:40px;">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Unit</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Status Shift</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Shift Aktif</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y" style="background:var(--dm-card,#fff); border-color:var(--dm-border,#e2e8f0);">
                    @foreach($users as $i => $user)
                    <tr class="shift-row"
                        data-id="{{ $user->id }}"
                        data-name="{{ strtolower($user->name) }}"
                        data-nip="{{ $user->nip }}"
                        data-can-shift="{{ $user->can_shift ? '1' : '0' }}"
                        data-shift-id="{{ $user->jam_shift_id ?? '' }}">
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="cb-row cb-item" value="{{ $user->id }}" onchange="updateBulkBar()">
                        </td>
                        <td class="px-4 py-3 text-sm row-num" style="color:var(--dm-muted,#64748b);">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($user->foto_profil)
                                <img src="{{ asset('storage/' . $user->foto_profil) }}" class="w-9 h-9 rounded-full object-cover" style="border:2px solid var(--dm-border,#e2e8f0);">
                                @else
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background:linear-gradient(135deg,#2E97D4,#1a6fa0);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-semibold" style="color:var(--dm-text,#1e293b);">{{ $user->name }}</div>
                                    <div class="text-xs" style="color:var(--dm-muted,#64748b);">{{ $user->nip }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm" style="color:var(--dm-text,#475569);">
                            {{ $user->wilayahKerja->nama ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($user->can_shift)
                            <span class="shift-badge px-2 py-1 rounded-full text-xs font-semibold badge-shift-on">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                            @else
                            <span class="shift-badge px-2 py-1 rounded-full text-xs font-semibold badge-shift-off">
                                Non-Shift
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm shift-info" style="color:var(--dm-text,#1e293b);">
                            @if($user->jamShift)
                            <div class="font-medium">{{ $user->jamShift->nama }}</div>
                            <div class="text-xs" style="color:var(--dm-muted,#64748b);">
                                {{ substr($user->jamShift->jam_masuk,0,5) }} – {{ substr($user->jamShift->jam_pulang,0,5) }}
                            </div>
                            @else
                            <span style="color:var(--dm-muted,#94a3b8);">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="action-buttons justify-center">
                                <button onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->can_shift ? 'true' : 'false' }}, {{ $user->jam_shift_id ?? 'null' }})"
                                    class="btn-edit" title="Atur Shift">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($user->can_shift)
                                <button onclick="removeShift({{ $user->id }}, this)"
                                    class="btn-delete" title="Hapus Shift">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                                @else
                                <button disabled class="btn-delete" style="opacity:0.3; cursor:not-allowed;" title="Tidak ada shift aktif">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="emptyState" class="hidden py-16 text-center" style="color:var(--dm-muted,#94a3b8);">
            <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm">Tidak ada pegawai yang cocok dengan filter</p>
        </div>
    </div>
</div>

<!-- Modal Edit Shift -->
<div id="modalEdit" class="modal-shift fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.45); display:none;">
    <div class="modal-inner w-full max-w-md rounded-2xl shadow-2xl p-6" style="background:var(--dm-card,#fff);">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold" style="color:var(--dm-text,#1e293b);">
                <i class="fas fa-edit mr-2" style="color:#2E97D4;"></i>Atur Shift Pegawai
            </h3>
            <button onclick="closeEditModal()" style="color:var(--dm-muted,#94a3b8); font-size:18px;">&times;</button>
        </div>
        <div class="mb-4 px-3 py-2 rounded-lg text-sm font-medium" style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">
            <i class="fas fa-user mr-2" style="color:#2E97D4;"></i><span id="editUserName"></span>
        </div>
        <input type="hidden" id="editUserId">
        <div class="mb-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="editCanShift" class="sr-only" onchange="toggleShiftSelect()">
                    <div id="toggleTrack" class="w-11 h-6 rounded-full transition-colors duration-200" style="background:#d1d5db;"></div>
                    <div id="toggleThumb" class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"></div>
                </div>
                <span class="text-sm font-medium" style="color:var(--dm-text,#374151);">Aktifkan Shift</span>
            </label>
        </div>
        <div id="shiftSelectWrap" class="mb-5 hidden">
            <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Pilih Shift</label>
            <select id="editShiftId" class="w-full px-4 py-2.5 rounded-xl outline-none"
                style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                <option value="">-- Pilih Shift --</option>
                @foreach($shifts as $shift)
                <option value="{{ $shift->id }}">{{ $shift->nama }} ({{ substr($shift->jam_masuk,0,5) }}–{{ substr($shift->jam_pulang,0,5) }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button onclick="saveShift()" class="btn-primary flex-1" style="padding:10px;">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
            <button onclick="closeEditModal()" class="flex-1 py-2 rounded-xl text-sm font-medium"
                style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">
                Batal
            </button>
        </div>
    </div>
</div>

<!-- Modal Bulk -->
<div id="modalBulk" class="modal-shift fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.45); display:none;">
    <div class="modal-inner w-full max-w-md rounded-2xl shadow-2xl p-6" style="background:var(--dm-card,#fff);">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold" style="color:var(--dm-text,#1e293b);">
                <i class="fas fa-layer-group mr-2" style="color:#2E97D4;"></i>Atur Shift Massal
            </h3>
            <button onclick="closeBulkModal()" style="color:var(--dm-muted,#94a3b8); font-size:18px;">&times;</button>
        </div>
        <p id="bulkModalDesc" class="text-sm mb-4" style="color:var(--dm-muted,#64748b);"></p>
        <div class="mb-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="bulkCanShift" class="sr-only" onchange="toggleBulkShiftSelect()">
                    <div id="bulkToggleTrack" class="w-11 h-6 rounded-full transition-colors duration-200" style="background:#d1d5db;"></div>
                    <div id="bulkToggleThumb" class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"></div>
                </div>
                <span class="text-sm font-medium" style="color:var(--dm-text,#374151);">Aktifkan Shift</span>
            </label>
        </div>
        <div id="bulkShiftSelectWrap" class="mb-5 hidden">
            <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Pilih Shift</label>
            <select id="bulkShiftId" class="w-full px-4 py-2.5 rounded-xl outline-none"
                style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                <option value="">-- Pilih Shift --</option>
                @foreach($shifts as $shift)
                <option value="{{ $shift->id }}">{{ $shift->nama }} ({{ substr($shift->jam_masuk,0,5) }}–{{ substr($shift->jam_pulang,0,5) }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button onclick="saveBulkShift()" class="btn-primary flex-1" style="padding:10px;">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
            <button onclick="closeBulkModal()" class="flex-1 py-2 rounded-xl text-sm font-medium"
                style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">
                Batal
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
var shiftData = @json($shifts->keyBy('id'));
var csrfToken = '{{ csrf_token() }}';

// ─── Filter ────────────────────────────────────────────────
function applyFilter() {
    var search  = document.getElementById('searchInput').value.toLowerCase().trim();
    var status  = document.getElementById('filterStatus').value;
    var shiftId = document.getElementById('filterShift').value;

    var rows    = document.querySelectorAll('.shift-row');
    var visible = 0;

    rows.forEach(function(row, i) {
        var name     = row.dataset.name || '';
        var nip      = row.dataset.nip || '';
        var canShift = row.dataset.canShift;
        var rowShift = row.dataset.shiftId;

        var matchSearch = !search || name.includes(search) || nip.includes(search);
        var matchStatus = !status || canShift === status;
        var matchShift  = !shiftId || rowShift === shiftId;

        if (matchSearch && matchStatus && matchShift) {
            row.classList.remove('hidden-row');
            visible++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    // re-number
    var num = 1;
    rows.forEach(function(row) {
        if (!row.classList.contains('hidden-row')) {
            row.querySelector('.row-num').textContent = num++;
        }
    });

    document.getElementById('countVisible').textContent = visible;
    document.getElementById('emptyState').classList.toggle('hidden', visible > 0);
}

document.getElementById('searchInput').addEventListener('input', applyFilter);
document.getElementById('filterStatus').addEventListener('change', applyFilter);
document.getElementById('filterShift').addEventListener('change', applyFilter);
document.getElementById('resetFilter').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterShift').value = '';
    applyFilter();
});

// ─── Checkbox bulk select ──────────────────────────────────
function toggleAll(cb) {
    document.querySelectorAll('.cb-item').forEach(function(c) {
        var row = c.closest('.shift-row');
        if (!row.classList.contains('hidden-row')) c.checked = cb.checked;
    });
    updateBulkBar();
}

function updateBulkBar() {
    var checked = document.querySelectorAll('.cb-item:checked').length;
    var bar = document.getElementById('bulkBar');
    if (checked > 0) {
        bar.classList.add('visible');
        document.getElementById('bulkCount').textContent = checked + ' pegawai dipilih';
    } else {
        bar.classList.remove('visible');
    }
    var allVis = document.querySelectorAll('.shift-row:not(.hidden-row) .cb-item');
    var allChecked = Array.from(allVis).every(c => c.checked);
    document.getElementById('cbAll').checked = allVis.length > 0 && allChecked;
}

function clearSelection() {
    document.querySelectorAll('.cb-item').forEach(c => c.checked = false);
    document.getElementById('cbAll').checked = false;
    updateBulkBar();
}

// ─── Toggle helpers ────────────────────────────────────────
function setToggle(checkboxId, trackId, thumbId, checked) {
    document.getElementById(checkboxId).checked = checked;
    var track = document.getElementById(trackId);
    var thumb = document.getElementById(thumbId);
    if (checked) {
        track.style.background = '#2E97D4';
        thumb.style.transform = 'translateX(20px)';
    } else {
        track.style.background = '#d1d5db';
        thumb.style.transform = 'translateX(0)';
    }
}

// ─── Edit Modal ────────────────────────────────────────────
function openEditModal(userId, userName, canShift, shiftId) {
    document.getElementById('editUserId').value = userId;
    document.getElementById('editUserName').textContent = userName;
    setToggle('editCanShift', 'toggleTrack', 'toggleThumb', canShift);
    document.getElementById('editShiftId').value = shiftId || '';
    document.getElementById('shiftSelectWrap').classList.toggle('hidden', !canShift);

    var m = document.getElementById('modalEdit');
    m.style.display = 'flex';
    setTimeout(function() { m.classList.add('show'); }, 10);
}

function closeEditModal() {
    var m = document.getElementById('modalEdit');
    m.classList.remove('show');
    setTimeout(function() { m.style.display = 'none'; }, 200);
}

function toggleShiftSelect() {
    var checked = document.getElementById('editCanShift').checked;
    setToggle('editCanShift', 'toggleTrack', 'toggleThumb', checked);
    document.getElementById('shiftSelectWrap').classList.toggle('hidden', !checked);
}

function saveShift() {
    var userId    = document.getElementById('editUserId').value;
    var canShift  = document.getElementById('editCanShift').checked;
    var shiftId   = document.getElementById('editShiftId').value;

    if (canShift && !shiftId) {
        alert('Pilih shift terlebih dahulu');
        return;
    }

    fetch('{{ url("admin/shift-pegawai") }}/' + userId, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ can_shift: canShift ? 1 : 0, jam_shift_id: shiftId || null })
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) { alert(data.message || 'Gagal'); return; }
        updateRowUI(userId, canShift, shiftId, data);
        closeEditModal();
    });
}

// ─── Remove Shift ──────────────────────────────────────────
function removeShift(userId, btn) {
    if (!confirm('Hapus penugasan shift untuk pegawai ini?')) return;
    fetch('{{ url("admin/shift-pegawai") }}/' + userId, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ can_shift: 0, jam_shift_id: null })
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) { alert(data.message || 'Gagal'); return; }
        updateRowUI(userId, false, null, data);
    });
}

// ─── Bulk Modal ────────────────────────────────────────────
function openBulkModal() {
    var count = document.querySelectorAll('.cb-item:checked').length;
    document.getElementById('bulkModalDesc').textContent = 'Pengaturan akan diterapkan ke ' + count + ' pegawai yang dipilih.';
    setToggle('bulkCanShift', 'bulkToggleTrack', 'bulkToggleThumb', false);
    document.getElementById('bulkShiftId').value = '';
    document.getElementById('bulkShiftSelectWrap').classList.add('hidden');

    var m = document.getElementById('modalBulk');
    m.style.display = 'flex';
    setTimeout(function() { m.classList.add('show'); }, 10);
}

function closeBulkModal() {
    var m = document.getElementById('modalBulk');
    m.classList.remove('show');
    setTimeout(function() { m.style.display = 'none'; }, 200);
}

function toggleBulkShiftSelect() {
    var checked = document.getElementById('bulkCanShift').checked;
    setToggle('bulkCanShift', 'bulkToggleTrack', 'bulkToggleThumb', checked);
    document.getElementById('bulkShiftSelectWrap').classList.toggle('hidden', !checked);
}

function saveBulkShift() {
    var canShift = document.getElementById('bulkCanShift').checked;
    var shiftId  = document.getElementById('bulkShiftId').value;
    var userIds  = Array.from(document.querySelectorAll('.cb-item:checked')).map(c => c.value);

    if (canShift && !shiftId) { alert('Pilih shift terlebih dahulu'); return; }
    if (userIds.length === 0) { alert('Pilih pegawai terlebih dahulu'); return; }

    fetch('{{ route("admin.shift-pegawai.bulk-update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ user_ids: userIds, can_shift: canShift ? 1 : 0, jam_shift_id: shiftId || null })
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) { alert(data.message || 'Gagal'); return; }
        userIds.forEach(function(uid) {
            updateRowUI(uid, canShift, shiftId, {
                shift_nama: shiftId && shiftData[shiftId] ? shiftData[shiftId].nama : null,
                jam_masuk:  shiftId && shiftData[shiftId] ? shiftData[shiftId].jam_masuk : null,
                jam_pulang: shiftId && shiftData[shiftId] ? shiftData[shiftId].jam_pulang : null,
            });
        });
        clearSelection();
        closeBulkModal();
        alert(data.message);
    });
}

// ─── Update row DOM after save ─────────────────────────────
function updateRowUI(userId, canShift, shiftId, data) {
    var row = document.querySelector('.shift-row[data-id="' + userId + '"]');
    if (!row) return;

    row.dataset.canShift = canShift ? '1' : '0';
    row.dataset.shiftId  = shiftId || '';

    // badge
    var badge = row.querySelector('.shift-badge');
    if (canShift) {
        badge.className = 'shift-badge px-2 py-1 rounded-full text-xs font-semibold badge-shift-on';
        badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Aktif';
    } else {
        badge.className = 'shift-badge px-2 py-1 rounded-full text-xs font-semibold badge-shift-off';
        badge.innerHTML = 'Non-Shift';
    }

    // shift info
    var info = row.querySelector('.shift-info');
    if (canShift && data.shift_nama) {
        var masuk  = (data.jam_masuk  || '').substring(0,5);
        var pulang = (data.jam_pulang || '').substring(0,5);
        info.innerHTML = '<div class="font-medium">' + data.shift_nama + '</div>' +
            '<div class="text-xs" style="color:var(--dm-muted,#64748b);">' + masuk + ' – ' + pulang + '</div>';
    } else {
        info.innerHTML = '<span style="color:var(--dm-muted,#94a3b8);">—</span>';
    }

    // hapus shift btn
    var actionCell = row.querySelector('.action-buttons');
    var delBtn = actionCell.querySelectorAll('button')[1];
    if (canShift) {
        delBtn.disabled = false;
        delBtn.style.opacity = '1';
        delBtn.style.cursor = 'pointer';
        delBtn.setAttribute('onclick', 'removeShift(' + userId + ', this)');
    } else {
        delBtn.disabled = true;
        delBtn.style.opacity = '0.3';
        delBtn.style.cursor = 'not-allowed';
        delBtn.removeAttribute('onclick');
    }

    // edit btn onclick update
    var editBtn = actionCell.querySelectorAll('button')[0];
    editBtn.setAttribute('onclick',
        'openEditModal(' + userId + ', "' + editBtn.getAttribute('onclick').match(/"([^"]+)"/)[1] + '", ' + canShift + ', ' + (shiftId || 'null') + ')'
    );
}

// close modal on backdrop click
document.getElementById('modalEdit').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
document.getElementById('modalBulk').addEventListener('click', function(e) {
    if (e.target === this) closeBulkModal();
});
</script>
@endpush
@endsection
