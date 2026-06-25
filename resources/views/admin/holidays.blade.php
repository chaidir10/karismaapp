@extends('layouts.admin')
@section('title', 'Hari Libur')

@section('content')
<div>
    <div class="page-header-glass">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1>Hari Libur Nasional & Custom</h1>
                <p>Data dari API libur nasional + jadwal custom admin</p>
            </div>
            <div class="header-actions" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <form action="{{ route('admin.jamkerja.holiday.sync') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="btn-header"><i class="fas fa-rotate"></i> Sync API {{ $year }}</button>
                </form>
                <select onchange="window.location.href='?year='+this.value" style="padding:8px 32px 8px 12px; border:1px solid rgba(90,182,234,0.25); border-radius:10px; font-size:13px; font-weight:600; background:rgba(90,182,234,0.1); color:#2E97D4; outline:none; cursor:pointer; -webkit-appearance:none; appearance:none; background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%222E97D4%22 stroke-width=%222.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M6 9l6 6 6-6%22/></svg>'); background-repeat:no-repeat; background-position:right 10px center;">
                    @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button onclick="openModal('modalAddHoliday')" class="btn-header" style="background:rgba(239,68,68,0.1); color:#ef4444; border-color:rgba(239,68,68,0.25);"><i class="fas fa-plus"></i> Tambah Libur</button>
            </div>
        </div>
    </div>

    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        <div style="padding:14px 16px 10px;">
            <div style="position:relative;">
                <i class="fas fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--dm-muted,#94a3b8); font-size:12px;"></i>
                <input type="text" id="holidaySearch" placeholder="Cari libur..." oninput="filterHolidays()" style="width:100%; padding:9px 12px 9px 34px; border:1px solid var(--dm-border,#e2e8f0); border-radius:10px; font-size:12px; background:var(--dm-bg,#f9fafb); color:var(--dm-text,#1e293b); outline:none;">
            </div>
        </div>
        <div class="overflow-x-auto" style="min-height:380px;">
            <table class="min-w-full divide-y" style="border-color:var(--dm-border,#e2e8f0);">
                <thead style="background:var(--dm-bg,#f9fafb);">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151); width:50px;">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Nama Libur</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase" style="color:var(--dm-text,#374151); width:80px;">Sumber</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase" style="color:var(--dm-text,#374151); width:70px;">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase" style="color:var(--dm-text,#374151); width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="holidayBody" class="divide-y" style="background:var(--dm-card,#fff); border-color:var(--dm-border,#e2e8f0);">
                    @forelse($holidays as $i => $h)
                    <tr id="holidayRow{{ $h->id }}" style="{{ !$h->is_active ? 'opacity:0.5;' : '' }}" data-search="{{ strtolower($h->name . ' ' . $h->date->translatedFormat('d M Y l')) }}">
                        <td class="px-4 py-3 text-sm hnum" style="color:var(--dm-muted,#64748b);">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 text-sm font-medium" style="color:var(--dm-text,#1e293b);">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="font-variant-numeric:tabular-nums;">{{ $h->date->translatedFormat('d M Y') }}</span>
                                <span class="text-xs" style="color:var(--dm-muted,#94a3b8);">({{ $h->date->translatedFormat('l') }})</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm" style="color:var(--dm-text,#1e293b);">{{ $h->name }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($h->source === 'api')
                            <span style="font-size:10px; font-weight:600; padding:2px 8px; border-radius:6px; background:rgba(59,130,246,0.1); color:#3b82f6;">API</span>
                            @else
                            <span style="font-size:10px; font-weight:600; padding:2px 8px; border-radius:6px; background:rgba(245,158,11,0.1); color:#d97706;">Manual</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="toggleHoliday({{ $h->id }}, this)" title="{{ $h->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" style="background:none; border:none; cursor:pointer; font-size:16px;">
                                <i class="fas {{ $h->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}" style="color:{{ $h->is_active ? '#10b981' : '#94a3b8' }};"></i>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div style="display:flex; justify-content:flex-end; gap:6px;">
                                <button onclick="editHoliday({{ $h->id }}, '{{ $h->date->format('Y-m-d') }}', '{{ addslashes($h->name) }}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:rgba(59,130,246,0.1); color:#3b82f6; border:none; cursor:pointer;">
                                    <i class="fas fa-pen" style="font-size:10px;"></i> Edit
                                </button>
                                <button onclick="deleteHoliday({{ $h->id }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:rgba(239,68,68,0.1); color:#ef4444; border:none; cursor:pointer;">
                                    <i class="fas fa-trash" style="font-size:10px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="holiday-empty">
                        <td colspan="6" class="px-6 py-12 text-center" style="color:var(--dm-muted,#94a3b8);">
                            <i class="fas fa-calendar-xmark" style="font-size:28px; opacity:0.3; display:block; margin-bottom:8px;"></i>
                            <p class="text-sm">Belum ada data libur untuk {{ $year }}</p>
                            <p class="text-xs mt-1">Klik "Sync API {{ $year }}" untuk mengambil dari API</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="holidayPager" style="display:none; padding:10px 16px; border-top:1px solid var(--dm-border,#e2e8f0); align-items:center; justify-content:space-between;">
            <span id="holidayInfo" class="text-xs" style="color:var(--dm-muted,#64748b);"></span>
            <div id="holidayPages" style="display:flex; gap:4px;"></div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modalAddHoliday" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-md p-6 relative mx-4 my-auto" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <button onclick="closeModal('modalAddHoliday')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl"><i class="fas fa-times"></i></button>
        <h2 class="text-xl font-bold mb-1" style="color:var(--dm-text,#1e293b);">Tambah Hari Libur</h2>
        <p class="mb-4 text-sm" style="color:var(--dm-muted,#64748b);">Tambahkan libur custom di luar API</p>
        <form action="{{ route('admin.jamkerja.holiday.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1" style="color:var(--dm-text,#374151);">Tanggal</label>
                <input type="date" name="date" required class="w-full px-4 py-2.5 rounded-xl text-sm" style="border:1px solid var(--dm-border,#e2e8f0); background:var(--dm-bg,#f9fafb); color:var(--dm-text,#1e293b); outline:none;">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1" style="color:var(--dm-text,#374151);">Nama Libur</label>
                <input type="text" name="name" required placeholder="Contoh: Libur Kantor" class="w-full px-4 py-2.5 rounded-xl text-sm" style="border:1px solid var(--dm-border,#e2e8f0); background:var(--dm-bg,#f9fafb); color:var(--dm-text,#1e293b); outline:none;">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('modalAddHoliday')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold" style="border:1px solid var(--dm-border,#e2e8f0); background:var(--dm-card,#fff); color:var(--dm-text,#1e293b); cursor:pointer;">Batal</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#ef4444; border:none; cursor:pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditHoliday" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-md p-6 relative mx-4 my-auto" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <button onclick="closeModal('modalEditHoliday')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl"><i class="fas fa-times"></i></button>
        <h2 class="text-xl font-bold mb-1" style="color:var(--dm-text,#1e293b);">Edit Hari Libur</h2>
        <p class="mb-4 text-sm" style="color:var(--dm-muted,#64748b);">Ubah tanggal atau nama libur</p>
        <form id="editHolidayForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1" style="color:var(--dm-text,#374151);">Tanggal</label>
                <input type="date" name="date" id="editHolidayDate" required class="w-full px-4 py-2.5 rounded-xl text-sm" style="border:1px solid var(--dm-border,#e2e8f0); background:var(--dm-bg,#f9fafb); color:var(--dm-text,#1e293b); outline:none;">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1" style="color:var(--dm-text,#374151);">Nama Libur</label>
                <input type="text" name="name" id="editHolidayName" required class="w-full px-4 py-2.5 rounded-xl text-sm" style="border:1px solid var(--dm-border,#e2e8f0); background:var(--dm-bg,#f9fafb); color:var(--dm-text,#1e293b); outline:none;">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('modalEditHoliday')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold" style="border:1px solid var(--dm-border,#e2e8f0); background:var(--dm-card,#fff); color:var(--dm-text,#1e293b); cursor:pointer;">Batal</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#3b82f6; border:none; cursor:pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
var _hPage = 1, _hPerPage = 7, _hRows = [], _hFiltered = [];

function initHolidayPager() {
    var tbody = document.getElementById('holidayBody');
    if (!tbody) return;
    _hRows = Array.from(tbody.querySelectorAll('tr:not(.holiday-empty)'));
    _hFiltered = _hRows.slice();
    renderHolidayPage();
}

function filterHolidays() {
    var q = (document.getElementById('holidaySearch').value || '').toLowerCase();
    _hFiltered = _hRows.filter(function(r) {
        return !q || (r.getAttribute('data-search') || '').indexOf(q) !== -1;
    });
    _hPage = 1;
    renderHolidayPage();
}

function renderHolidayPage() {
    var total = _hFiltered.length;
    var totalPages = Math.max(1, Math.ceil(total / _hPerPage));
    if (_hPage > totalPages) _hPage = totalPages;
    var start = (_hPage - 1) * _hPerPage;
    var end = start + _hPerPage;

    _hRows.forEach(function(r) { r.style.display = 'none'; });
    _hFiltered.forEach(function(r, i) {
        r.style.display = (i >= start && i < end) ? '' : 'none';
        var numCell = r.querySelector('.hnum');
        if (numCell) numCell.textContent = i + 1;
    });

    var pager = document.getElementById('holidayPager');
    var info = document.getElementById('holidayInfo');
    var pages = document.getElementById('holidayPages');
    if (total <= _hPerPage) { pager.style.display = 'none'; return; }
    pager.style.display = 'flex';
    info.textContent = (start + 1) + '–' + Math.min(end, total) + ' dari ' + total;

    var html = '';
    var btnStyle = 'padding:4px 10px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid var(--dm-border,#e2e8f0); background:var(--dm-card,#fff); color:var(--dm-text,#1e293b);';
    var btnDisabled = 'padding:4px 10px; border-radius:8px; font-size:11px; border:1px solid var(--dm-border,#e2e8f0); color:var(--dm-muted,#94a3b8); cursor:default;';
    var btnActive = 'padding:4px 10px; border-radius:8px; font-size:11px; font-weight:700; cursor:pointer; border:1px solid #3b82f6; background:#3b82f6; color:#fff; box-shadow:0 2px 8px rgba(59,130,246,0.35);';

    html += _hPage > 1 ? '<button style="' + btnStyle + '" onclick="goHolidayPage(' + (_hPage-1) + ')">&lsaquo;</button>' : '<span style="' + btnDisabled + '">&lsaquo;</span>';
    for (var p = 1; p <= totalPages; p++) {
        html += '<button style="' + (p === _hPage ? btnActive : btnStyle) + '" onclick="goHolidayPage(' + p + ')">' + p + '</button>';
    }
    html += _hPage < totalPages ? '<button style="' + btnStyle + '" onclick="goHolidayPage(' + (_hPage+1) + ')">&rsaquo;</button>' : '<span style="' + btnDisabled + '">&rsaquo;</span>';
    pages.innerHTML = html;
}

function goHolidayPage(p) { _hPage = p; renderHolidayPage(); }

function editHoliday(id, date, name) {
    document.getElementById('editHolidayForm').action = '{{ url("admin/jamkerja/holiday") }}/' + id;
    document.getElementById('editHolidayDate').value = date;
    document.getElementById('editHolidayName').value = name;
    openModal('modalEditHoliday');
}

function deleteHoliday(id) {
    showConfirm({
        type: 'danger', icon: 'fa-calendar-xmark', title: 'Hapus Hari Libur', message: 'Yakin ingin menghapus hari libur ini?', confirmText: 'Ya, Hapus',
        onConfirm: function() {
            fetch('{{ url("admin/jamkerja/holiday") }}/' + id, { method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'} })
            .then(function(r) { return r.json(); })
            .then(function(d) { if(d.success) location.reload(); });
        }
    });
}

function toggleHoliday(id, btn) {
    fetch('{{ url("admin/jamkerja/holiday") }}/' + id + '/toggle', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'} })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if(d.success) {
            var icon = btn.querySelector('i');
            var row = document.getElementById('holidayRow'+id);
            if(d.is_active) { icon.className = 'fas fa-toggle-on'; icon.style.color = '#10b981'; if(row) row.style.opacity = '1'; }
            else { icon.className = 'fas fa-toggle-off'; icon.style.color = '#94a3b8'; if(row) row.style.opacity = '0.5'; }
        }
    });
}

document.addEventListener('DOMContentLoaded', initHolidayPager);
</script>
@endpush
@endsection
