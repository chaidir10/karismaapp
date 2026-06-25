@extends('layouts.admin')
@section('title', 'Hari Libur')

@php
    $bulanNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $currentMonth = (int) date('m');
    $grouped = $holidays->groupBy(function($h) { return (int) $h->date->format('m'); });
@endphp

@section('content')
<style>
    .month-tabs { display:flex; gap:3px; margin:12px 16px; padding:3px; background:rgba(0,0,0,0.03); border-radius:10px; border:1px solid var(--dm-border,#e2e8f0); overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width:none; }
    .month-tabs::-webkit-scrollbar { display:none; }
    .month-tab { flex:1; min-width:0; padding:8px 4px; border:none; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap; background:transparent; color:var(--dm-muted,#94a3b8); transition:all 0.15s; text-align:center; -webkit-tap-highlight-color:transparent; }
    .month-tab:hover { background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#1e293b); }
    .month-tab.active { background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; box-shadow:0 2px 8px rgba(90,182,234,0.25), inset 0 1px 1px rgba(255,255,255,0.2); }
    .month-tab .tab-count { font-size:8px; font-weight:700; background:rgba(0,0,0,0.08); padding:1px 4px; border-radius:4px; margin-left:2px; }
    .month-tab.active .tab-count { background:rgba(255,255,255,0.25); }
    .month-tab-all { flex:none !important; padding:8px 12px !important; border-left:1px solid var(--dm-border,#e2e8f0); margin-left:2px; }
    .month-panel { display:none; }
    .month-panel.active { display:block; }
    .holiday-row { transition:opacity 0.2s; }
</style>

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
                    <button type="submit" class="btn-header"><i class="fas fa-rotate"></i> Sync {{ $year }}</button>
                </form>
                <select onchange="window.location.href='?year='+this.value" style="padding:8px 32px 8px 12px; border:1px solid rgba(90,182,234,0.25); border-radius:10px; font-size:13px; font-weight:600; background:rgba(90,182,234,0.1); color:#2E97D4; outline:none; cursor:pointer; -webkit-appearance:none; appearance:none; background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%222E97D4%22 stroke-width=%222.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M6 9l6 6 6-6%22/></svg>'); background-repeat:no-repeat; background-position:right 10px center;">
                    @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button onclick="openModal('modalAddHoliday')" class="btn-header" style="background:rgba(239,68,68,0.1); color:#ef4444; border-color:rgba(239,68,68,0.25);"><i class="fas fa-plus"></i> Tambah</button>
            </div>
        </div>
    </div>

    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        {{-- Tab Bulan --}}
        <div class="month-tabs">
            @for($m = 1; $m <= 12; $m++)
            @php $count = isset($grouped[$m]) ? $grouped[$m]->count() : 0; @endphp
            <button class="month-tab {{ ($year == date('Y') && $m == $currentMonth) || ($year != date('Y') && $m == 1) ? 'active' : '' }}" onclick="switchMonth({{ $m }}, this)">
                {{ $bulanNames[$m-1] }}
                @if($count > 0)<span class="tab-count">{{ $count }}</span>@endif
            </button>
            @endfor
            <button class="month-tab month-tab-all" onclick="switchMonth(0, this)">
                <i class="fas fa-list" style="margin-right:3px; font-size:10px;"></i> Semua
            </button>
        </div>

        {{-- Panel per bulan --}}
        @for($m = 0; $m <= 12; $m++)
        @php
            $panelData = $m === 0 ? $holidays : ($grouped[$m] ?? collect());
            $isDefault = ($year == date('Y') && $m == $currentMonth) || ($year != date('Y') && $m == 1);
        @endphp
        <div class="month-panel {{ $isDefault ? 'active' : '' }}" id="monthPanel{{ $m }}">
            <div class="overflow-x-auto">
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
                    <tbody class="divide-y" style="background:var(--dm-card,#fff); border-color:var(--dm-border,#e2e8f0);">
                        @forelse($panelData as $i => $h)
                        <tr id="holidayRow{{ $h->id }}" class="holiday-row" style="{{ !$h->is_active ? 'opacity:0.5;' : '' }}">
                            <td class="px-4 py-3 text-sm" style="color:var(--dm-muted,#64748b);">{{ $loop->iteration }}</td>
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
                                <button onclick="toggleHoliday({{ $h->id }}, this)" style="background:none; border:none; cursor:pointer; font-size:16px;">
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
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center" style="color:var(--dm-muted,#94a3b8);">
                                <i class="fas fa-calendar-check" style="font-size:28px; opacity:0.2; display:block; margin-bottom:8px;"></i>
                                <p class="text-sm">{{ $m === 0 ? 'Belum ada data libur untuk ' . $year : 'Tidak ada libur di bulan ' . ($m > 0 ? $bulanNames[$m-1] : '') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endfor

        {{-- Summary --}}
        <div style="padding:10px 16px; border-top:1px solid var(--dm-border,#e2e8f0); display:flex; align-items:center; justify-content:space-between;">
            <span class="text-xs" style="color:var(--dm-muted,#64748b);">Total: <strong>{{ $holidays->count() }}</strong> hari libur di {{ $year }} &middot; <strong>{{ $holidays->where('is_active', true)->count() }}</strong> aktif</span>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modalAddHoliday" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-md p-6 relative mx-4 my-auto" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <button onclick="closeModal('modalAddHoliday')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl"><i class="fas fa-times"></i></button>
        <h2 class="text-xl font-bold mb-1" style="color:var(--dm-text,#1e293b);">Tambah Hari Libur</h2>
        <p class="mb-4 text-sm" style="color:var(--dm-muted,#64748b);">Tambahkan libur custom</p>
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
        <p class="mb-4 text-sm" style="color:var(--dm-muted,#64748b);">Ubah tanggal atau nama</p>
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
function openModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.style.display = 'flex';
}
function closeModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    el.style.display = '';
}

function switchMonth(m, btn) {
    document.querySelectorAll('.month-panel').forEach(function(p) { p.classList.remove('active'); });
    document.querySelectorAll('.month-tab').forEach(function(t) { t.classList.remove('active'); });
    document.getElementById('monthPanel' + m).classList.add('active');
    btn.classList.add('active');
}

function editHoliday(id, date, name) {
    document.getElementById('editHolidayForm').action = '{{ url("admin/jamkerja/holiday") }}/' + id;
    document.getElementById('editHolidayDate').value = date;
    document.getElementById('editHolidayName').value = name;
    openModal('modalEditHoliday');
}

function deleteHoliday(id) {
    showConfirm({
        type: 'danger', icon: 'fa-calendar-xmark', title: 'Hapus Hari Libur', message: 'Yakin ingin menghapus?', confirmText: 'Ya, Hapus',
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
            document.querySelectorAll('#holidayRow' + id).forEach(function(row) {
                var icon = row.querySelector('.fa-toggle-on, .fa-toggle-off');
                if(d.is_active) { icon.className = 'fas fa-toggle-on'; icon.style.color = '#10b981'; row.style.opacity = '1'; }
                else { icon.className = 'fas fa-toggle-off'; icon.style.color = '#94a3b8'; row.style.opacity = '0.5'; }
            });
        }
    });
}

// Scroll tab bulan aktif ke view
document.addEventListener('DOMContentLoaded', function() {
    var activeTab = document.querySelector('.month-tab.active');
    if (activeTab) activeTab.scrollIntoView({ inline:'center', block:'nearest' });
});
</script>
@endpush
@endsection
