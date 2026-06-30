@extends(Auth::user()->role === 'operator' ? 'layouts.operator' : 'layouts.admin')

@section('title', 'Shift Pegawai')

@push('styles')
<style>
    .modal-sp { opacity:0; transition:opacity 0.2s ease; }
    .modal-sp.show { opacity:1; }
    .modal-sp .modal-inner { transform:translateY(12px); opacity:0; transition:transform 0.2s ease, opacity 0.2s ease; }
    .modal-sp.show .modal-inner { transform:translateY(0); opacity:1; }
    .sp-row.hidden-row { display:none; }
    .kandidat-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; border:1px solid var(--dm-border,#e2e8f0); margin-bottom:6px; }
    .kandidat-item.added { opacity:0.35; pointer-events:none; }
</style>
@endpush

@section('content')
<div>
    <div class="page-header-glass">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1><i class="fas fa-business-time" style="margin-right:8px;"></i>Shift Pegawai</h1>
                <p>Pegawai yang diizinkan menggunakan sistem shift</p>
            </div>
            <div class="header-actions">
                <button onclick="openTambahModal()" class="btn-header">
                    <i class="fas fa-plus"></i> Tambah Pegawai
                </button>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="p-5 mb-6 text-sm" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-7">
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
                <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Filter Unit</label>
                <select id="filterUnit" class="w-full px-4 py-2.5 rounded-xl outline-none"
                    style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    <option value="">Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->nama }}</option>
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
                        <th colspan="5" class="px-6 py-4 text-right text-sm font-semibold" style="color:var(--dm-text,#374151);">
                            Total: <span id="countVisible">{{ $users->count() }}</span> pegawai shift
                        </th>
                    </tr>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151); width:45px;">No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Pegawai</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Jabatan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Unit</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y" style="background:var(--dm-card,#fff); border-color:var(--dm-border,#e2e8f0);">
                    @forelse($users as $i => $user)
                    <tr class="sp-row"
                        data-id="{{ $user->id }}"
                        data-name="{{ strtolower($user->name) }}"
                        data-nip="{{ $user->nip }}"
                        data-unit-id="{{ $user->unit_id ?? '' }}">
                        <td class="px-5 py-3 text-sm row-num" style="color:var(--dm-muted,#64748b);">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($user->foto_profil)
                                <img src="{{ asset('public/storage/foto_profil/'.$user->foto_profil) }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0" style="border:2px solid var(--dm-border,#e2e8f0);">
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
                        <td class="px-5 py-3 text-sm" style="color:var(--dm-text,#475569);">{{ $user->jabatan ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm" style="color:var(--dm-text,#475569);">{{ $user->wilayahKerja->nama ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">
                            <div class="action-buttons justify-center">
                                <button onclick="removeShift({{ $user->id }})"
                                    class="btn-delete" title="Hapus dari Shift">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="5" class="px-6 py-16 text-center" style="color:var(--dm-muted,#94a3b8);">
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

<!-- ─── Modal Tambah ──────────────────────────────────────────── -->
<div id="modalTambah" class="modal-sp fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(0,0,0,0.5); display:none;">
    <div class="modal-inner w-full max-w-lg rounded-2xl shadow-2xl flex flex-col" style="background:var(--dm-card,#fff); max-height:85vh;">
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <h3 class="text-base font-bold" style="color:var(--dm-text,#1e293b);">
                <i class="fas fa-user-plus mr-2" style="color:#2E97D4;"></i>Pilih Pegawai yang Bisa Shift
            </h3>
            <button onclick="closeTambahModal()" style="color:var(--dm-muted,#94a3b8); font-size:20px; line-height:1;">&times;</button>
        </div>

        <div class="px-6 py-3" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <div class="relative">
                <input type="text" id="kandidatSearch" placeholder="Cari nama atau NIP..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl outline-none text-sm"
                    style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text); border:1px solid var(--dm-border,#e2e8f0); border-radius:10px;"
                    oninput="filterKandidat()">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color:var(--dm-muted,#94a3b8);"></i>
            </div>
        </div>

        <div id="kandidatList" class="flex-1 overflow-y-auto px-6 py-4" style="min-height:200px;">
            <div class="text-center py-10" style="color:var(--dm-muted,#94a3b8);">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>

        <div class="px-6 py-4 flex justify-end" style="border-top:1px solid var(--dm-border,#e2e8f0);">
            <button onclick="closeTambahModal()" class="px-5 py-2 rounded-xl text-sm font-medium"
                style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">Tutup</button>
        </div>
    </div>
</div>

<!-- ─── Modal Konfirmasi Hapus ────────────────────────────────── -->
<div id="modalKonfirmasi" class="modal-sp fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(0,0,0,0.5); display:none;">
    <div class="modal-inner w-full max-w-sm rounded-2xl shadow-2xl p-6" style="background:var(--dm-card,#fff);">
        <div class="text-center mb-5">
            <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3" style="background:#fee2e2;">
                <i class="fas fa-user-minus text-xl" style="color:#ef4444;"></i>
            </div>
            <h3 class="text-base font-bold mb-1" style="color:var(--dm-text,#1e293b);">Hapus dari Shift?</h3>
            <p class="text-sm" style="color:var(--dm-muted,#64748b);">
                <span id="konfirmasiNama" class="font-semibold" style="color:var(--dm-text,#1e293b);"></span>
                tidak akan bisa menggunakan sistem shift lagi.
            </p>
        </div>
        <div class="flex gap-3">
            <button onclick="confirmRemove()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#ef4444;">
                <i class="fas fa-trash mr-1"></i> Ya, Hapus
            </button>
            <button onclick="closeKonfirmasi()" class="flex-1 py-2.5 rounded-xl text-sm font-medium"
                style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">Batal</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
var csrfToken   = '{{ csrf_token() }}';
var urlBase     = '{{ url("admin/shift-pegawai") }}';
var urlNonShift = '{{ route("admin.shift-pegawai.non-shift-users") }}';
var urlAssign   = '{{ route("admin.shift-pegawai.assign") }}';
var allKandidat = [];

// ─── Filter tabel utama ────────────────────────────────────────
function applyFilter() {
    var search  = document.getElementById('searchInput').value.toLowerCase().trim();
    var unitId  = document.getElementById('filterUnit').value;
    var rows    = document.querySelectorAll('.sp-row');
    var visible = 0;

    rows.forEach(function(row) {
        var matchSearch = !search || row.dataset.name.includes(search) || row.dataset.nip.includes(search);
        var matchUnit   = !unitId || row.dataset.unitId === unitId;
        if (matchSearch && matchUnit) {
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
document.getElementById('filterUnit').addEventListener('change', applyFilter);
document.getElementById('resetFilter').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterUnit').value = '';
    applyFilter();
});

// ─── Modal Tambah ─────────────────────────────────────────────
function openTambahModal() {
    allKandidat = [];
    document.getElementById('kandidatList').innerHTML =
        '<div class="text-center py-10" style="color:var(--dm-muted,#94a3b8);">' +
        '<i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i><p class="text-sm">Memuat data...</p></div>';
    document.getElementById('kandidatSearch').value = '';

    var m = document.getElementById('modalTambah');
    m.style.display = 'flex';
    setTimeout(function() { m.classList.add('show'); }, 10);

    fetch(urlNonShift)
        .then(function(r) { return r.json(); })
        .then(function(data) { allKandidat = data; renderKandidat(data); });
}

function closeTambahModal() {
    var m = document.getElementById('modalTambah');
    m.classList.remove('show');
    setTimeout(function() { m.style.display = 'none'; }, 200);
}

function filterKandidat() {
    var q = document.getElementById('kandidatSearch').value.toLowerCase().trim();
    renderKandidat(q ? allKandidat.filter(function(u) {
        return u.name.toLowerCase().includes(q) || u.nip.includes(q);
    }) : allKandidat);
}

function renderKandidat(list) {
    var container = document.getElementById('kandidatList');
    if (list.length === 0) {
        container.innerHTML = '<div class="text-center py-10" style="color:var(--dm-muted,#94a3b8);">' +
            '<i class="fas fa-users text-3xl mb-2 block opacity-20"></i><p class="text-sm">Tidak ada pegawai ditemukan</p></div>';
        return;
    }

    var html = '';
    list.forEach(function(u) {
        var avatar = u.foto
            ? '<img src="' + u.foto + '" class="w-9 h-9 rounded-full object-cover flex-shrink-0" style="border:2px solid var(--dm-border,#e2e8f0);">'
            : '<div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:linear-gradient(135deg,#2E97D4,#1a6fa0);">' + u.inisial + '</div>';

        html += '<div class="kandidat-item" data-uid="' + u.id + '">' +
            avatar +
            '<div style="flex:1; min-width:0;">' +
                '<div class="text-sm font-semibold" style="color:var(--dm-text,#1e293b); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + u.name + '</div>' +
                '<div class="text-xs" style="color:var(--dm-muted,#64748b);">' + u.nip + (u.unit !== '-' ? ' &bull; ' + u.unit : '') + '</div>' +
            '</div>' +
            '<button onclick="assignShift(' + u.id + ', this)" class="btn-primary flex-shrink-0" style="padding:6px 16px; font-size:12px;">' +
                '<i class="fas fa-plus mr-1"></i>Tambah' +
            '</button></div>';
    });
    container.innerHTML = html;
}

function assignShift(userId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(urlAssign, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ user_id: userId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus mr-1"></i>Tambah';
            return;
        }
        var item = document.querySelector('.kandidat-item[data-uid="' + userId + '"]');
        if (item) {
            item.classList.add('added');
            item.querySelector('button').innerHTML = '<i class="fas fa-check" style="color:#10b981;"></i>';
        }
        allKandidat = allKandidat.filter(function(u) { return u.id != userId; });
        addRowToTable(data.user);
    });
}

function addRowToTable(user) {
    var tbody   = document.getElementById('tableBody');
    var emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    var rowCount = tbody.querySelectorAll('.sp-row').length + 1;
    var avatar = user.foto
        ? '<img src="' + user.foto + '" class="w-9 h-9 rounded-full object-cover flex-shrink-0" style="border:2px solid var(--dm-border,#e2e8f0);">'
        : '<div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:linear-gradient(135deg,#2E97D4,#1a6fa0);">' + user.inisial + '</div>';

    var tr = document.createElement('tr');
    tr.className = 'sp-row';
    tr.dataset.id     = user.id;
    tr.dataset.name   = user.name.toLowerCase();
    tr.dataset.nip    = user.nip;
    tr.dataset.unitId = '';

    tr.innerHTML =
        '<td class="px-5 py-3 text-sm row-num" style="color:var(--dm-muted,#64748b);">' + rowCount + '</td>' +
        '<td class="px-5 py-3"><div class="flex items-center gap-3">' + avatar +
            '<div><div class="text-sm font-semibold" style="color:var(--dm-text,#1e293b);">' + user.name + '</div>' +
            '<div class="text-xs" style="color:var(--dm-muted,#64748b);">' + user.nip + '</div></div></div></td>' +
        '<td class="px-5 py-3 text-sm" style="color:var(--dm-text,#475569);">' + user.jabatan + '</td>' +
        '<td class="px-5 py-3 text-sm" style="color:var(--dm-text,#475569);">' + user.unit + '</td>' +
        '<td class="px-5 py-3 text-center"><div class="action-buttons justify-center">' +
            '<button onclick="removeShift(' + user.id + ', this)" class="btn-delete" title="Hapus dari Shift"><i class="fas fa-user-minus"></i></button>' +
        '</div></td>';

    tbody.appendChild(tr);
    document.getElementById('countVisible').textContent = tbody.querySelectorAll('.sp-row').length;
}

// ─── Remove ───────────────────────────────────────────────────
var pendingRemoveId = null;

function removeShift(userId) {
    var row  = document.querySelector('.sp-row[data-id="' + userId + '"]');
    var name = row ? row.querySelector('.text-sm.font-semibold').textContent.trim() : '';
    pendingRemoveId = userId;
    document.getElementById('konfirmasiNama').textContent = name;
    var m = document.getElementById('modalKonfirmasi');
    m.style.display = 'flex';
    setTimeout(function() { m.classList.add('show'); }, 10);
}

function closeKonfirmasi() {
    var m = document.getElementById('modalKonfirmasi');
    m.classList.remove('show');
    setTimeout(function() { m.style.display = 'none'; }, 200);
    pendingRemoveId = null;
}

function confirmRemove() {
    if (!pendingRemoveId) return;
    var userId = pendingRemoveId;
    closeKonfirmasi();

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

        var num = 1;
        document.querySelectorAll('.sp-row').forEach(function(r) {
            r.querySelector('.row-num').textContent = num++;
        });

        if (remaining === 0) {
            document.getElementById('tableBody').innerHTML =
                '<tr id="emptyRow"><td colspan="5" class="px-6 py-16 text-center" style="color:var(--dm-muted,#94a3b8);">' +
                '<i class="fas fa-business-time text-4xl mb-3 block opacity-20"></i>' +
                '<p class="text-sm">Belum ada pegawai yang menggunakan shift</p>' +
                '<button onclick="openTambahModal()" class="btn-primary mt-3" style="padding:8px 18px; font-size:13px;"><i class="fas fa-plus mr-1"></i> Tambah Sekarang</button></td></tr>';
        }
    });
}

document.getElementById('modalTambah').addEventListener('click', function(e) {
    if (e.target === this) closeTambahModal();
});
document.getElementById('modalKonfirmasi').addEventListener('click', function(e) {
    if (e.target === this) closeKonfirmasi();
});
</script>
@endpush
@endsection
