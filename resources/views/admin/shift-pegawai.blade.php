@extends(Auth::user()->role === 'operator' ? 'layouts.operator' : 'layouts.admin')

@section('title', 'Shift Pegawai')

@push('styles')
<style>
    .modal-sp { opacity:0; transition:opacity 0.2s ease; }
    .modal-sp.show { opacity:1; }
    .modal-sp .modal-inner { transform:translateY(12px); opacity:0; transition:transform 0.2s ease, opacity 0.2s ease; }
    .modal-sp.show .modal-inner { transform:translateY(0); opacity:1; }
    .sp-row.hidden-row { display:none; }
    .kandidat-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; border:1px solid var(--dm-border,#e2e8f0); margin-bottom:6px; background:var(--dm-card,#fff); }
    .kandidat-item:hover { border-color:#2E97D4; }
    .kandidat-item.added { opacity:0.4; pointer-events:none; }
</style>
@endpush

@section('content')
<div>
    <div class="page-header-glass">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1><i class="fas fa-business-time" style="margin-right:8px;"></i>Shift Pegawai</h1>
                <p>Daftar pegawai yang menggunakan sistem shift</p>
            </div>
            <div class="header-actions">
                <button onclick="openTambahModal()" class="btn-header">
                    <i class="fas fa-plus"></i> Tambah Pegawai Shift
                </button>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="p-5 mb-6 text-sm" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-5">
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
            <div class="md:col-span-5">
                <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Filter Shift</label>
                <select id="filterShift" class="w-full px-4 py-2.5 rounded-xl outline-none"
                    style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    <option value="">Semua Shift</option>
                    @foreach($shifts as $shift)
                    <option value="{{ $shift->id }}">{{ $shift->nama }} ({{ substr($shift->jam_masuk,0,5) }}–{{ substr($shift->jam_pulang,0,5) }})</option>
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

    <!-- Tabel -->
    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-color:var(--dm-border,#e2e8f0);">
                <thead style="background:var(--dm-bg,#f9fafb);">
                    <tr>
                        <th colspan="6" class="px-6 py-4 text-right text-sm font-semibold" style="color:var(--dm-text,#374151);">
                            Total: <span id="countVisible">{{ $users->count() }}</span> pegawai shift
                        </th>
                    </tr>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151); width:45px;">No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Pegawai</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Unit</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Nama Shift</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Jam</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y" style="background:var(--dm-card,#fff); border-color:var(--dm-border,#e2e8f0);">
                    @forelse($users as $i => $user)
                    <tr class="sp-row"
                        data-id="{{ $user->id }}"
                        data-name="{{ strtolower($user->name) }}"
                        data-nip="{{ $user->nip }}"
                        data-shift-id="{{ $user->jam_shift_id ?? '' }}">
                        <td class="px-5 py-3 text-sm row-num" style="color:var(--dm-muted,#64748b);">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($user->foto_profil)
                                <img src="{{ asset('public/storage/foto_profil/'.$user->foto_profil) }}" class="w-9 h-9 rounded-full object-cover" style="border:2px solid var(--dm-border,#e2e8f0);">
                                @else
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:linear-gradient(135deg,#2E97D4,#1a6fa0);">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-semibold" style="color:var(--dm-text,#1e293b);">{{ $user->name }}</div>
                                    <div class="text-xs" style="color:var(--dm-muted,#64748b);">{{ $user->nip }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-sm" style="color:var(--dm-text,#475569);">{{ $user->wilayahKerja->nama ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-semibold shift-nama-cell" style="color:var(--dm-text,#1e293b);">
                            {{ $user->jamShift->nama ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-sm shift-jam-cell" style="color:var(--dm-muted,#64748b);">
                            @if($user->jamShift)
                            {{ substr($user->jamShift->jam_masuk,0,5) }} – {{ substr($user->jamShift->jam_pulang,0,5) }}
                            @else —
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="action-buttons justify-center">
                                <button onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->jam_shift_id ?? 'null' }})"
                                    class="btn-edit" title="Ganti Shift">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="removeShift({{ $user->id }}, this)"
                                    class="btn-delete" title="Hapus dari Shift">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="6" class="px-6 py-16 text-center" style="color:var(--dm-muted,#94a3b8);">
                            <i class="fas fa-business-time text-4xl mb-3 block opacity-20"></i>
                            <p class="text-sm">Belum ada pegawai yang menggunakan shift</p>
                            <button onclick="openTambahModal()" class="btn-primary mt-3" style="padding:8px 18px; font-size:13px;">
                                <i class="fas fa-plus mr-1"></i> Tambah Sekarang
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="emptyFilter" class="hidden py-14 text-center" style="color:var(--dm-muted,#94a3b8);">
            <i class="fas fa-search text-3xl mb-3 block opacity-20"></i>
            <p class="text-sm">Tidak ada hasil yang cocok</p>
        </div>
    </div>
</div>

<!-- ─── Modal Tambah Pegawai Shift ──────────────────────────────── -->
<div id="modalTambah" class="modal-sp fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(0,0,0,0.5); display:none;">
    <div class="modal-inner w-full max-w-lg rounded-2xl shadow-2xl flex flex-col" style="background:var(--dm-card,#fff); max-height:85vh;">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <h3 class="text-base font-bold" style="color:var(--dm-text,#1e293b);">
                <i class="fas fa-user-plus mr-2" style="color:#2E97D4;"></i>Tambah Pegawai ke Shift
            </h3>
            <button onclick="closeTambahModal()" style="color:var(--dm-muted,#94a3b8); font-size:20px; line-height:1;">&times;</button>
        </div>

        <!-- Pilih Shift -->
        <div class="px-6 py-3" style="border-bottom:1px solid var(--dm-border,#e2e8f0); background:var(--dm-bg,#f9fafb);">
            <label class="block text-xs font-semibold mb-1" style="color:var(--dm-muted,#64748b);">SHIFT YANG AKAN DITUGASKAN</label>
            <select id="tambahShiftId" class="w-full px-3 py-2 rounded-xl outline-none text-sm font-medium"
                style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;"
                onchange="updateKandidatButtons()">
                <option value="">-- Pilih shift dulu --</option>
                @foreach($shifts as $shift)
                <option value="{{ $shift->id }}">{{ $shift->nama }} &nbsp;({{ substr($shift->jam_masuk,0,5) }} – {{ substr($shift->jam_pulang,0,5) }})</option>
                @endforeach
            </select>
        </div>

        <!-- Search -->
        <div class="px-6 py-3" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <div class="relative">
                <input type="text" id="kandidatSearch" placeholder="Cari nama atau NIP..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl outline-none text-sm"
                    style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text); border:1px solid var(--dm-border,#e2e8f0); border-radius:10px;"
                    oninput="filterKandidat()">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color:var(--dm-muted,#94a3b8);"></i>
            </div>
        </div>

        <!-- List kandidat -->
        <div id="kandidatList" class="flex-1 overflow-y-auto px-6 py-4" style="min-height:200px;">
            <div id="kandidatLoading" class="text-center py-10" style="color:var(--dm-muted,#94a3b8);">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 flex justify-end" style="border-top:1px solid var(--dm-border,#e2e8f0);">
            <button onclick="closeTambahModal()" class="px-5 py-2 rounded-xl text-sm font-medium"
                style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">Tutup</button>
        </div>
    </div>
</div>

<!-- ─── Modal Edit Shift ─────────────────────────────────────────── -->
<div id="modalEdit" class="modal-sp fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(0,0,0,0.5); display:none;">
    <div class="modal-inner w-full max-w-sm rounded-2xl shadow-2xl p-6" style="background:var(--dm-card,#fff);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold" style="color:var(--dm-text,#1e293b);">
                <i class="fas fa-edit mr-2" style="color:#2E97D4;"></i>Ganti Shift
            </h3>
            <button onclick="closeEditModal()" style="color:var(--dm-muted,#94a3b8); font-size:20px; line-height:1;">&times;</button>
        </div>
        <div class="mb-4 px-3 py-2 rounded-lg text-sm font-medium" style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">
            <i class="fas fa-user mr-2" style="color:#2E97D4;"></i><span id="editUserName"></span>
        </div>
        <input type="hidden" id="editUserId">
        <div class="mb-5">
            <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Pilih Shift</label>
            <select id="editShiftId" class="w-full px-4 py-2.5 rounded-xl outline-none text-sm"
                style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                <option value="">-- Pilih Shift --</option>
                @foreach($shifts as $shift)
                <option value="{{ $shift->id }}">{{ $shift->nama }} ({{ substr($shift->jam_masuk,0,5) }}–{{ substr($shift->jam_pulang,0,5) }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button onclick="saveEdit()" class="btn-primary flex-1" style="padding:10px;">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
            <button onclick="closeEditModal()" class="flex-1 py-2 rounded-xl text-sm font-medium"
                style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">Batal</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
var csrfToken  = '{{ csrf_token() }}';
var urlBase    = '{{ url("admin/shift-pegawai") }}';
var urlNonShift= '{{ route("admin.shift-pegawai.non-shift-users") }}';
var urlAssign  = '{{ route("admin.shift-pegawai.assign") }}';
var shiftMap   = @json($shifts->map(fn($s) => ['id'=>$s->id,'nama'=>$s->nama,'jam_masuk'=>$s->jam_masuk,'jam_pulang'=>$s->jam_pulang])->keyBy('id'));

var allKandidat = [];

// ─── Filter tabel utama ────────────────────────────────────────
function applyFilter() {
    var search  = document.getElementById('searchInput').value.toLowerCase().trim();
    var shiftId = document.getElementById('filterShift').value;
    var rows    = document.querySelectorAll('.sp-row');
    var visible = 0;

    rows.forEach(function(row) {
        var matchSearch = !search || row.dataset.name.includes(search) || row.dataset.nip.includes(search);
        var matchShift  = !shiftId || row.dataset.shiftId === shiftId;
        if (matchSearch && matchShift) {
            row.classList.remove('hidden-row');
            visible++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    var num = 1;
    rows.forEach(function(row) {
        if (!row.classList.contains('hidden-row')) row.querySelector('.row-num').textContent = num++;
    });

    document.getElementById('countVisible').textContent = visible;
    document.getElementById('emptyFilter').classList.toggle('hidden', visible > 0 || rows.length === 0);
}

document.getElementById('searchInput').addEventListener('input', applyFilter);
document.getElementById('filterShift').addEventListener('change', applyFilter);
document.getElementById('resetFilter').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterShift').value = '';
    applyFilter();
});

// ─── Modal Tambah ─────────────────────────────────────────────
function openTambahModal() {
    allKandidat = [];
    document.getElementById('kandidatList').innerHTML = '<div id="kandidatLoading" class="text-center py-10" style="color:var(--dm-muted,#94a3b8);"><i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i><p class="text-sm">Memuat data...</p></div>';
    document.getElementById('tambahShiftId').value = '';
    document.getElementById('kandidatSearch').value = '';

    var m = document.getElementById('modalTambah');
    m.style.display = 'flex';
    setTimeout(function() { m.classList.add('show'); }, 10);

    fetch(urlNonShift)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            allKandidat = data;
            renderKandidat(data);
        });
}

function closeTambahModal() {
    var m = document.getElementById('modalTambah');
    m.classList.remove('show');
    setTimeout(function() { m.style.display = 'none'; }, 200);
}

function filterKandidat() {
    var q = document.getElementById('kandidatSearch').value.toLowerCase().trim();
    var filtered = q ? allKandidat.filter(function(u) {
        return u.name.toLowerCase().includes(q) || u.nip.includes(q);
    }) : allKandidat;
    renderKandidat(filtered);
}

function updateKandidatButtons() {
    renderKandidat(allKandidat);
}

function renderKandidat(list) {
    var shiftId = document.getElementById('tambahShiftId').value;
    var container = document.getElementById('kandidatList');

    if (list.length === 0) {
        container.innerHTML = '<div class="text-center py-10" style="color:var(--dm-muted,#94a3b8);"><i class="fas fa-users text-3xl mb-2 block opacity-20"></i><p class="text-sm">Tidak ada pegawai ditemukan</p></div>';
        return;
    }

    var html = '';
    list.forEach(function(u) {
        var avatar = u.foto
            ? '<img src="' + u.foto + '" class="w-9 h-9 rounded-full object-cover flex-shrink-0" style="border:2px solid var(--dm-border,#e2e8f0);">'
            : '<div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:linear-gradient(135deg,#2E97D4,#1a6fa0);">' + u.inisial + '</div>';

        var addBtn = shiftId
            ? '<button onclick="assignShift(' + u.id + ', this)" class="btn-primary flex-shrink-0" style="padding:6px 14px; font-size:12px; white-space:nowrap;"><i class="fas fa-plus mr-1"></i>Tambah</button>'
            : '<button disabled class="flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-medium" style="background:var(--dm-bg,#f1f5f9); color:var(--dm-muted,#94a3b8); cursor:not-allowed;">Pilih shift dulu</button>';

        html += '<div class="kandidat-item" data-uid="' + u.id + '">' +
            avatar +
            '<div style="flex:1; min-width:0;">' +
                '<div class="text-sm font-semibold" style="color:var(--dm-text,#1e293b); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + u.name + '</div>' +
                '<div class="text-xs" style="color:var(--dm-muted,#64748b);">' + u.nip + ' &bull; ' + u.unit + '</div>' +
            '</div>' + addBtn + '</div>';
    });

    container.innerHTML = html;
}

function assignShift(userId, btn) {
    var shiftId = document.getElementById('tambahShiftId').value;
    if (!shiftId) { alert('Pilih shift terlebih dahulu'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(urlAssign, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ user_id: userId, jam_shift_id: shiftId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-plus mr-1"></i>Tambah'; return; }

        // tandai sudah ditambah di modal
        var item = document.querySelector('.kandidat-item[data-uid="' + userId + '"]');
        if (item) {
            item.classList.add('added');
            item.querySelector('button').innerHTML = '<i class="fas fa-check mr-1" style="color:#10b981;"></i>Ditambahkan';
        }

        // hapus dari allKandidat
        allKandidat = allKandidat.filter(function(u) { return u.id != userId; });

        // tambah baris ke tabel utama
        addRowToTable(data.user, shiftId);
    });
}

function addRowToTable(user, shiftId) {
    var tbody  = document.getElementById('tableBody');
    var emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    var shift  = shiftMap[shiftId] || {};
    var masuk  = (shift.jam_masuk  || '').substring(0, 5);
    var pulang = (shift.jam_pulang || '').substring(0, 5);
    var rowCount = tbody.querySelectorAll('.sp-row').length + 1;

    var avatar = user.foto
        ? '<img src="' + user.foto + '" class="w-9 h-9 rounded-full object-cover" style="border:2px solid var(--dm-border,#e2e8f0);">'
        : '<div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:linear-gradient(135deg,#2E97D4,#1a6fa0);">' + user.inisial + '</div>';

    var tr = document.createElement('tr');
    tr.className = 'sp-row';
    tr.dataset.id      = user.id;
    tr.dataset.name    = user.name.toLowerCase();
    tr.dataset.nip     = user.nip;
    tr.dataset.shiftId = shiftId;

    tr.innerHTML =
        '<td class="px-5 py-3 text-sm row-num" style="color:var(--dm-muted,#64748b);">' + rowCount + '</td>' +
        '<td class="px-5 py-3"><div class="flex items-center gap-3">' + avatar +
            '<div><div class="text-sm font-semibold" style="color:var(--dm-text,#1e293b);">' + user.name + '</div>' +
            '<div class="text-xs" style="color:var(--dm-muted,#64748b);">' + user.nip + '</div></div></div></td>' +
        '<td class="px-5 py-3 text-sm" style="color:var(--dm-text,#475569);">' + user.unit + '</td>' +
        '<td class="px-5 py-3 text-sm font-semibold shift-nama-cell" style="color:var(--dm-text,#1e293b);">' + user.shift_nama + '</td>' +
        '<td class="px-5 py-3 text-sm shift-jam-cell" style="color:var(--dm-muted,#64748b);">' + masuk + ' – ' + pulang + '</td>' +
        '<td class="px-5 py-3 text-center"><div class="action-buttons justify-center">' +
            '<button onclick="openEditModal(' + user.id + ', \'' + user.name.replace(/'/g, "\\'") + '\', ' + shiftId + ')" class="btn-edit" title="Ganti Shift"><i class="fas fa-edit"></i></button>' +
            '<button onclick="removeShift(' + user.id + ', this)" class="btn-delete" title="Hapus dari Shift"><i class="fas fa-user-minus"></i></button>' +
        '</div></td>';

    tbody.appendChild(tr);
    document.getElementById('countVisible').textContent = tbody.querySelectorAll('.sp-row:not(.hidden-row)').length;
}

// ─── Modal Edit ───────────────────────────────────────────────
function openEditModal(userId, userName, shiftId) {
    document.getElementById('editUserId').value  = userId;
    document.getElementById('editUserName').textContent = userName;
    document.getElementById('editShiftId').value = shiftId || '';

    var m = document.getElementById('modalEdit');
    m.style.display = 'flex';
    setTimeout(function() { m.classList.add('show'); }, 10);
}

function closeEditModal() {
    var m = document.getElementById('modalEdit');
    m.classList.remove('show');
    setTimeout(function() { m.style.display = 'none'; }, 200);
}

function saveEdit() {
    var userId  = document.getElementById('editUserId').value;
    var shiftId = document.getElementById('editShiftId').value;
    if (!shiftId) { alert('Pilih shift terlebih dahulu'); return; }

    fetch(urlBase + '/' + userId, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ jam_shift_id: shiftId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success) return;

        var row = document.querySelector('.sp-row[data-id="' + userId + '"]');
        if (row) {
            row.dataset.shiftId = shiftId;
            row.querySelector('.shift-nama-cell').textContent = data.shift_nama;
            var masuk  = (data.jam_masuk  || '').substring(0, 5);
            var pulang = (data.jam_pulang || '').substring(0, 5);
            row.querySelector('.shift-jam-cell').textContent = masuk + ' – ' + pulang;

            var editBtn = row.querySelector('.btn-edit');
            editBtn.setAttribute('onclick', 'openEditModal(' + userId + ', \'' + row.dataset.name.replace(/'/g,"\\'")+'\', ' + shiftId + ')');
        }
        closeEditModal();
    });
}

// ─── Remove Shift ─────────────────────────────────────────────
function removeShift(userId, btn) {
    if (!confirm('Hapus pegawai ini dari shift?')) return;

    fetch(urlBase + '/' + userId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success) return;
        var row = document.querySelector('.sp-row[data-id="' + userId + '"]');
        if (row) row.remove();

        var remaining = document.querySelectorAll('.sp-row').length;
        document.getElementById('countVisible').textContent = remaining;

        // re-number
        var num = 1;
        document.querySelectorAll('.sp-row').forEach(function(r) {
            r.querySelector('.row-num').textContent = num++;
        });

        if (remaining === 0) {
            document.getElementById('tableBody').innerHTML =
                '<tr id="emptyRow"><td colspan="6" class="px-6 py-16 text-center" style="color:var(--dm-muted,#94a3b8);">' +
                '<i class="fas fa-business-time text-4xl mb-3 block opacity-20"></i>' +
                '<p class="text-sm">Belum ada pegawai yang menggunakan shift</p>' +
                '<button onclick="openTambahModal()" class="btn-primary mt-3" style="padding:8px 18px; font-size:13px;"><i class="fas fa-plus mr-1"></i> Tambah Sekarang</button></td></tr>';
        }
    });
}

// backdrop close
document.getElementById('modalTambah').addEventListener('click', function(e) { if (e.target === this) closeTambahModal(); });
document.getElementById('modalEdit').addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });
</script>
@endpush
@endsection
