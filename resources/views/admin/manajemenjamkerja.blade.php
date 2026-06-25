@extends('layouts.admin')

@section('title', 'Manajemen Jam Kerja & Shift')

@section('content')
<div>
    <div class="page-header-glass">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1>Manajemen Jam Kerja & Shift</h1>
                <p>Kelola jam kerja dan shift pegawai</p>
            </div>
            <div class="header-actions">
                <button onclick="openModal('modalAddJam')" class="btn-header"><i class="fas fa-plus"></i> Tambah Jam Kerja</button>
                <button onclick="openModal('modalAddShift')" class="btn-header"><i class="fas fa-plus"></i> Tambah Shift</button>
            </div>
        </div>
    </div>

    <!-- Grid dua kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tabel Jam Kerja -->
        <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
            <div class="px-6 py-3 font-semibold" style="background:var(--dm-bg,#f9fafb); color:var(--dm-text,#374151);">Jam Kerja Normal</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color:var(--dm-border,#e2e8f0);">
                    <thead style="background:var(--dm-bg,#f9fafb);">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Hari</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Jam Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Jam Pulang</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="background:var(--dm-card,#fff); border-color:var(--dm-border,#e2e8f0);">
                        @foreach($jamKerja as $i => $jam)
                        <tr>
                            <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium" style="color:var(--dm-text,#1e293b);">{{ $jam->hari }}</td>
                            <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">{{ $jam->jam_masuk }}</td>
                            <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">{{ $jam->jam_pulang }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="action-buttons">
                                    <button onclick="openEditModal({{ $jam->id }})"
                                        class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteJam({{ $jam->id }})"
                                        class="btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Jam Shift -->
        <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
            <div class="px-6 py-3 font-semibold" style="background:var(--dm-bg,#f9fafb); color:var(--dm-text,#374151);">Jam Shift</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color:var(--dm-border,#e2e8f0);">
                    <thead style="background:var(--dm-bg,#f9fafb);">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Nama Shift</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Jam Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Jam Pulang</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase" style="color:var(--dm-text,#374151);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="background:var(--dm-card,#fff); border-color:var(--dm-border,#e2e8f0);">
                        @foreach($jamShift as $i => $shift)
                        <tr>
                            <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium" style="color:var(--dm-text,#1e293b);">{{ $shift->nama }}</td>
                            <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">{{ $shift->jam_masuk }}</td>
                            <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">{{ $shift->jam_pulang }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="action-buttons">
                                    <button onclick="openEditShiftModal({{ $shift->id }})"
                                        class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteShift({{ $shift->id }})"
                                        class="btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===================== HARI LIBUR ===================== --}}
<div class="mt-6" id="libur">
    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        <div class="px-6 py-4" style="background:var(--dm-bg,#f9fafb); border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-base font-bold" style="color:var(--dm-text,#1e293b);">
                        <i class="fas fa-calendar-xmark" style="color:#ef4444; margin-right:6px;"></i>Hari Libur Nasional & Custom
                    </h2>
                    <p class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Data dari API libur nasional + jadwal custom admin</p>
                </div>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <form action="{{ route('admin.jamkerja.holiday.sync') }}" method="POST" class="inline" data-no-loading="true">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" style="padding:8px 14px; border:1px solid rgba(59,130,246,0.25); border-radius:10px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; background:rgba(59,130,246,0.08); color:#3b82f6; transition:all 0.15s; -webkit-tap-highlight-color:transparent;" onmouseover="this.style.background='rgba(59,130,246,0.16)'" onmouseout="this.style.background='rgba(59,130,246,0.08)'">
                            <i class="fas fa-rotate"></i> Sync API {{ $year }}
                        </button>
                    </form>
                    <select onchange="window.location.href='?year='+this.value+'#libur'" style="padding:8px 32px 8px 12px; border:1px solid var(--dm-border,#e2e8f0); border-radius:10px; font-size:12px; font-weight:600; background:var(--dm-card,#fff); color:var(--dm-text,#1e293b); outline:none; cursor:pointer; -webkit-appearance:none; appearance:none; background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2394a3b8%22 stroke-width=%222.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M6 9l6 6 6-6%22/></svg>'); background-repeat:no-repeat; background-position:right 10px center;">
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button onclick="openModal('modalAddHoliday')" style="padding:8px 14px; border:1px solid rgba(239,68,68,0.25); border-radius:10px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; background:rgba(239,68,68,0.08); color:#ef4444; transition:all 0.15s; -webkit-tap-highlight-color:transparent;" onmouseover="this.style.background='rgba(239,68,68,0.16)'" onmouseout="this.style.background='rgba(239,68,68,0.08)'">
                        <i class="fas fa-plus"></i> Tambah Libur
                    </button>
                </div>
            </div>
        </div>
        <div style="padding:8px 16px 0;">
            <div style="position:relative;">
                <i class="fas fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--dm-muted,#94a3b8); font-size:12px;"></i>
                <input type="text" id="holidaySearch" placeholder="Cari libur..." oninput="filterHolidays()" style="width:100%; padding:9px 12px 9px 34px; border:1px solid var(--dm-border,#e2e8f0); border-radius:10px; font-size:12px; background:var(--dm-bg,#f9fafb); color:var(--dm-text,#1e293b); outline:none;">
            </div>
        </div>
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
        <div id="holidayPager" style="display:none; padding:10px 16px; border-top:1px solid var(--dm-border,#e2e8f0); display:flex; align-items:center; justify-content:space-between;">
            <span id="holidayInfo" class="text-xs" style="color:var(--dm-muted,#64748b);"></span>
            <div id="holidayPages" style="display:flex; gap:4px;"></div>
        </div>
    </div>
</div>

{{-- Modal Tambah Libur --}}
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

{{-- Modal Edit Libur --}}
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

{{-- ===================== MODAL JAM KERJA ===================== --}}
<!-- Tambah Jam Kerja -->
<div id="modalAddJam" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-md p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <button onclick="closeModal('modalAddJam')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="text-2xl font-bold" style="color:var(--dm-text,#1e293b); mb-1">Tambah Jam Kerja</h2>
        <p class="mb-4" style="color:var(--dm-muted,#64748b);">Tambahkan jadwal kerja baru</p>
        <div id="addJamErrors" class="mb-4 text-red-500 text-sm hidden"></div>
        <form id="formAddJam" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Hari</label>
                <select name="hari" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jam Masuk</label>
                    <input type="time" name="jam_masuk" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jam Pulang</label>
                    <input type="time" name="jam_pulang" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
            </div>
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalAddJam')" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Jam Kerja -->
<div id="modalEditJam" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-md p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <button onclick="closeModal('modalEditJam')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="text-2xl font-bold" style="color:var(--dm-text,#1e293b); mb-1">Edit Jam Kerja</h2>
        <p class="mb-4" style="color:var(--dm-muted,#64748b);">Perbarui jam masuk dan pulang</p>
        <div id="editJamErrors" class="mb-4 text-red-500 text-sm hidden"></div>
        <form id="formEditJam" class="space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editJamId">
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Hari</label>
                <select name="hari" id="editHari" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jam Masuk</label>
                    <input type="time" name="jam_masuk" id="editJamMasuk" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jam Pulang</label>
                    <input type="time" name="jam_pulang" id="editJamPulang" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
            </div>
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalEditJam')" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sync-alt mr-2"></i> Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL JAM SHIFT ===================== --}}
<!-- Tambah Shift -->
<div id="modalAddShift" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-md p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <button onclick="closeModal('modalAddShift')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="text-2xl font-bold" style="color:var(--dm-text,#1e293b); mb-1">Tambah Jam Shift</h2>
        <p class="mb-4" style="color:var(--dm-muted,#64748b);">Tambahkan jadwal shift baru</p>
        <div id="addShiftErrors" class="mb-4 text-red-500 text-sm hidden"></div>
        <form id="formAddShift" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Nama Shift</label>
                <input type="text" name="nama" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jam Masuk</label>
                    <input type="time" name="jam_masuk" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jam Pulang</label>
                    <input type="time" name="jam_pulang" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
            </div>
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalAddShift')" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i> Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Shift -->
<div id="modalEditShift" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-md p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <button onclick="closeModal('modalEditShift')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="text-2xl font-bold" style="color:var(--dm-text,#1e293b); mb-1">Edit Jam Shift</h2>
        <p class="mb-4" style="color:var(--dm-muted,#64748b);">Perbarui jadwal shift</p>
        <div id="editShiftErrors" class="mb-4 text-red-500 text-sm hidden"></div>
        <form id="formEditShift" class="space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editShiftId">
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Nama Shift</label>
                <input type="text" name="nama" id="editShiftNama" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jam Masuk</label>
                    <input type="time" name="jam_masuk" id="editShiftJamMasuk" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jam Pulang</label>
                    <input type="time" name="jam_pulang" id="editShiftJamPulang" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
            </div>
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalEditShift')" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sync-alt mr-2"></i> Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Action Buttons - Konsisten dengan Dashboard */
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
    }

    .btn-edit,
    .btn-delete {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12px;
    }

    .btn-edit {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .btn-edit:hover {
        background: #3b82f6;
        color: white;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: white;
    }

</style>
@endsection

@push('scripts')
<script>
function openModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('div');
    modal.classList.remove('hidden');
    setTimeout(() => content.classList.remove('scale-95'), 10);
}

function closeModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('div');
    content.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

// ===================== JAM KERJA =====================
// Tambah Jam Kerja
document.getElementById('formAddJam').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const errorsDiv = document.getElementById('addJamErrors');
    errorsDiv.classList.add('hidden');
    
    fetch(`{{ route('admin.jamkerja.store') }}`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: formData
    })
    .then(async res => {
        if(res.ok) return res.json();
        const err = await res.json();
        throw err;
    })
    .then(data => { 
        if(data.success) {
            closeModal('modalAddJam');
            location.reload();
        }
    })
    .catch(err => {
        errorsDiv.innerHTML = Object.values(err.errors || {}).flat().join('<br>');
        errorsDiv.classList.remove('hidden');
    });
});

// Edit Jam Kerja
function openEditModal(id){
    const errorsDiv = document.getElementById('editJamErrors');
    errorsDiv.classList.add('hidden');
    
    fetch(`{{ url('admin/jamkerja') }}/${id}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('editJamId').value = data.id;
        document.getElementById('editHari').value = data.hari;
        document.getElementById('editJamMasuk').value = data.jam_masuk.slice(0,5);
        document.getElementById('editJamPulang').value = data.jam_pulang.slice(0,5);
        openModal('modalEditJam');
    })
    .catch(function(){ showError('Gagal mengambil data jam kerja.'); });
}

document.getElementById('formEditJam').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('editJamId').value;
    const hari = document.getElementById('editHari').value.trim();
    const jamMasuk = document.getElementById('editJamMasuk').value.trim().slice(0,5);
    const jamPulang = document.getElementById('editJamPulang').value.trim().slice(0,5);
    const errorsDiv = document.getElementById('editJamErrors');
    errorsDiv.classList.add('hidden');

    fetch(`{{ url('admin/jamkerja') }}/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ hari, jam_masuk: jamMasuk, jam_pulang: jamPulang })
    })
    .then(async res => {
        if(res.ok) return res.json();
        const err = await res.json();
        throw err;
    })
    .then(data => { 
        if(data.success) {
            closeModal('modalEditJam');
            location.reload();
        }
    })
    .catch(err => {
        errorsDiv.innerHTML = Object.values(err.errors || {}).flat().join('<br>');
        errorsDiv.classList.remove('hidden');
    });
});

// Hapus Jam Kerja
function deleteJam(id){
    showConfirm({
        type: 'danger', title: 'Hapus Jam Kerja', message: 'Yakin ingin menghapus jam kerja ini?', confirmText: 'Ya, Hapus',
        onConfirm: function() {
            fetch(`{{ url('admin/jamkerja') }}/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'} })
            .then(r => r.json()).then(d => { if(d.success) location.reload(); else showError('Gagal menghapus jam kerja.'); })
            .catch(function() { showError('Terjadi kesalahan.'); });
        }
    });
}

// ===================== JAM SHIFT =====================
// Tambah Shift
document.getElementById('formAddShift').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const errorsDiv = document.getElementById('addShiftErrors');
    errorsDiv.classList.add('hidden');
    
    fetch(`{{ route('admin.jamkerja.shift.store') }}`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: formData
    })
    .then(async res => {
        if(res.ok) return res.json();
        const err = await res.json();
        throw err;
    })
    .then(data => { 
        if(data.success) {
            closeModal('modalAddShift');
            location.reload();
        }
    })
    .catch(err => {
        errorsDiv.innerHTML = Object.values(err.errors || {}).flat().join('<br>');
        errorsDiv.classList.remove('hidden');
    });
});

// Edit Shift
function openEditShiftModal(id){
    const errorsDiv = document.getElementById('editShiftErrors');
    errorsDiv.classList.add('hidden');
    
    fetch(`{{ url('admin/jamkerja/shift') }}/${id}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('editShiftId').value = data.id;
        document.getElementById('editShiftNama').value = data.nama;
        document.getElementById('editShiftJamMasuk').value = data.jam_masuk.slice(0,5);
        document.getElementById('editShiftJamPulang').value = data.jam_pulang.slice(0,5);
        openModal('modalEditShift');
    })
    .catch(function(){ showError('Gagal mengambil data shift.'); });
}

document.getElementById('formEditShift').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('editShiftId').value;
    const nama = document.getElementById('editShiftNama').value.trim();
    const jamMasuk = document.getElementById('editShiftJamMasuk').value.trim().slice(0,5);
    const jamPulang = document.getElementById('editShiftJamPulang').value.trim().slice(0,5);
    const errorsDiv = document.getElementById('editShiftErrors');
    errorsDiv.classList.add('hidden');

    fetch(`{{ url('admin/jamkerja/shift') }}/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ nama, jam_masuk: jamMasuk, jam_pulang: jamPulang })
    })
    .then(async res => {
        if(res.ok) return res.json();
        const err = await res.json();
        throw err;
    })
    .then(data => { 
        if(data.success) {
            closeModal('modalEditShift');
            location.reload();
        }
    })
    .catch(err => {
        errorsDiv.innerHTML = Object.values(err.errors || {}).flat().join('<br>');
        errorsDiv.classList.remove('hidden');
    });
});

// Hapus Shift
function deleteShift(id){
    showConfirm({
        type: 'danger', title: 'Hapus Shift', message: 'Yakin ingin menghapus shift ini?', confirmText: 'Ya, Hapus',
        onConfirm: function() {
            fetch(`{{ url('admin/jamkerja/shift') }}/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'} })
            .then(r => r.json()).then(d => { if(d.success) location.reload(); });
        }
    });
}

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
            .then(function(d) { if(d.success) { var row = document.getElementById('holidayRow'+id); if(row) { row.style.opacity='0'; setTimeout(function(){ location.reload(); }, 300); } } });
        }
    });
}

// ===================== HOLIDAY PAGINATION =====================
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
    if (total <= _hPerPage) {
        pager.style.display = 'none';
        return;
    }
    pager.style.display = 'flex';
    info.textContent = (start + 1) + '–' + Math.min(end, total) + ' dari ' + total;

    var html = '';
    var btnStyle = 'padding:4px 10px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid var(--dm-border,#e2e8f0); background:var(--dm-card,#fff); color:var(--dm-text,#1e293b); text-decoration:none;';
    var btnDisabled = 'padding:4px 10px; border-radius:8px; font-size:11px; border:1px solid var(--dm-border,#e2e8f0); color:var(--dm-muted,#94a3b8); cursor:default;';
    var btnActive = 'padding:4px 10px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid #3b82f6; background:#3b82f6; color:#fff;';

    html += _hPage > 1 ? '<button style="' + btnStyle + '" onclick="goHolidayPage(' + (_hPage-1) + ')">&lsaquo;</button>' : '<span style="' + btnDisabled + '">&lsaquo;</span>';
    for (var p = 1; p <= totalPages; p++) {
        html += '<button style="' + (p === _hPage ? btnActive : btnStyle) + '" onclick="goHolidayPage(' + p + ')">' + p + '</button>';
    }
    html += _hPage < totalPages ? '<button style="' + btnStyle + '" onclick="goHolidayPage(' + (_hPage+1) + ')">&rsaquo;</button>' : '<span style="' + btnDisabled + '">&rsaquo;</span>';
    pages.innerHTML = html;
}

function goHolidayPage(p) {
    _hPage = p;
    renderHolidayPage();
}

document.addEventListener('DOMContentLoaded', initHolidayPager);

function toggleHoliday(id, btn) {
    fetch('{{ url("admin/jamkerja/holiday") }}/' + id + '/toggle', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'} })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if(d.success) {
            var icon = btn.querySelector('i');
            var row = document.getElementById('holidayRow'+id);
            if(d.is_active) {
                icon.className = 'fas fa-toggle-on'; icon.style.color = '#10b981';
                if(row) row.style.opacity = '1';
            } else {
                icon.className = 'fas fa-toggle-off'; icon.style.color = '#94a3b8';
                if(row) row.style.opacity = '0.5';
            }
        }
    });
}
</script>
@endpush