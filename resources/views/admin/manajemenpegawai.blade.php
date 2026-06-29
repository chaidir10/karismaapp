@extends(Auth::user()->role === 'operator' ? 'layouts.operator' : 'layouts.admin')

@section('title', 'Manajemen Pegawai')

@push('styles')
<style>
    .modal-pg { opacity:0; transition:opacity 0.2s ease; }
    .modal-pg.show { opacity:1; }
    .modal-pg .modal-inner { transform:translateY(12px); opacity:0; transition:transform 0.2s ease, opacity 0.2s ease; }
    .modal-pg.show .modal-inner { transform:translateY(0); opacity:1; }
</style>
@endpush

@section('content')
<div>
    <div class="page-header-glass">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1>Manajemen Pegawai</h1>
                <p>Kelola data seluruh pegawai</p>
            </div>
            <div class="header-actions">
                <button onclick="openModal('modalAdd')" class="btn-header"><i class="fas fa-user-plus"></i> Tambah Pegawai</button>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="p-5 mb-6 text-sm" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Cari Pegawai</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari nama atau NIP..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Filter Unit</label>
                <select id="filterUnit" class="w-full px-4 py-2.5 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    <option value="">Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium mb-1" style="color:var(--dm-text,#374151);">Jenis Pegawai</label>
                <select id="filterJenis" class="w-full px-4 py-2.5 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    <option value="">Semua Jenis</option>
                    <option value="asn">ASN</option>
                    <option value="non_asn">Non ASN</option>
                    <option value="outsourcing">Outsourcing</option>
                </select>
            </div>
            <div class="md:col-span-1">
                <button id="resetFilter" class="w-full h-[42px] px-4 py-2 rounded-xl text-sm transition-colors duration-200 flex items-center justify-center" style="background:var(--dm-bg,#f1f5f9); color:var(--dm-text,#374151);">
                    <i class="fas fa-sync-alt mr-2"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Employee Table Card -->
    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-color:var(--dm-border,#e2e8f0);">
                <thead style="background:var(--dm-bg,#f9fafb);">
                    <tr>
                        <th colspan="8" class="px-6 py-4 text-right text-sm font-semibold" style="color:var(--dm-text,#374151);">
                            Total Pegawai: <span id="totalPegawai">{{ $users->count() }}</span>
                        </th>
                    </tr>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color:var(--dm-text,#374151); width:50px;">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color:var(--dm-text,#374151); width:60px;">Foto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider sortable-th" style="color:var(--dm-text,#374151); cursor:pointer; user-select:none;" data-sort="name" onclick="toggleSort('name')">Nama <i class="fas fa-sort-up" style="font-size:10px; margin-left:4px; color:#2E97D4;"></i></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider sortable-th" style="color:var(--dm-text,#374151); cursor:pointer; user-select:none;" data-sort="jabatan" onclick="toggleSort('jabatan')">Jabatan <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider sortable-th" style="color:var(--dm-text,#374151); cursor:pointer; user-select:none;" data-sort="unit" onclick="toggleSort('unit')">Unit <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color:var(--dm-text,#374151);">Shift</th>
                    </tr>
                </thead>
                <tbody id="pegawaiTableBody" class="divide-y" style="background:var(--dm-card,#fff); border-color:var(--dm-border,#e2e8f0);">
                    @foreach($users as $i => $user)
                    <tr class="pegawai-row transition-colors duration-150" style="cursor:pointer;"
                        onclick="openDetailModal({{ $user->id }})"
                        data-name="{{ strtolower($user->name) }}"
                        data-nip="{{ $user->nip }}"
                        data-unit="{{ $user->unit_id ?? '' }}"
                        data-unit-name="{{ strtolower($user->wilayahKerja->nama ?? '') }}"
                        data-jenis="{{ $user->jenis_pegawai }}"
                        data-jabatan="{{ strtolower($user->jabatan ?? '') }}">
                        <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">{{ $i+1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden border-2 border-white shadow-sm">
                                    <img
                                        src="{{ $user->foto_profil ? asset('public/storage/foto_profil/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0a9396&color=fff' }}"
                                        class="h-full w-full object-cover"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0a9396&color=fff'">
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">
                            {{ $user->name ?? '-' }} <br>
                            <small class="font-semibold text-blue-500">
                                {{ $user->nip ?? 'N/A' }}
                            </small>
                        </td>

                        <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">{{ $user->jabatan ?? '-' }} <br>
                            <small class="font-semibold text-blue-500">{{ ucwords(str_replace('_', ' ', $user->jenis_pegawai)) }}</small>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm" style="color:var(--dm-text,#1e293b);">
                            {{ $user->wilayahKerja->nama ?? '-' }}
                            @if($user->wilayahKerjaList->count() > 1)
                            <br><small class="text-blue-500">+{{ $user->wilayahKerjaList->count() - 1 }} lokasi lain</small>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($user->can_shift)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Shift
                            </span>
                            @else
                            <span class="text-gray-400 text-xs">Normal</span>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div id="modalAdd" class="modal-pg hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-8">
    <div class="modal-inner w-full max-w-2xl p-6 relative mx-4 my-auto" id="modalAddContent" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <button onclick="closeModal('modalAdd')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors duration-200 z-10">
            <i class="fas fa-times"></i>
        </button>
        <div class="mb-6">
            <h2 class="text-2xl font-bold" style="color:var(--dm-text,#1e293b);">Tambah Pegawai Baru</h2>
            <p class="mt-1" style="color:var(--dm-muted,#64748b);">Isi formulir untuk menambahkan pegawai baru</p>
        </div>
        <form id="formAdd" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">NIP</label>
                    <input type="number" name="nip" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jabatan</label>
                    <input type="text" name="jabatan" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Unit Utama</label>
                    <select name="unit_id" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Lokasi Presensi (boleh pilih lebih dari 1)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-3 rounded-xl max-h-40 overflow-y-auto" style="border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                    @foreach($units as $u)
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 px-2 py-1 rounded-lg">
                        <input type="checkbox" name="wilayah_ids[]" value="{{ $u->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm" style="color:var(--dm-text,#374151);">{{ $u->nama }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Jenis Pegawai</label>
                    <select name="jenis_pegawai" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                        <option value="asn">ASN</option>
                        <option value="non_asn">Non ASN</option>
                        <option value="outsourcing">Outsourcing</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">No HP</label>
                    <input type="text" name="no_hp" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Pegawai Shift?</label>
                    <select name="can_shift" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--dm-text,#374151);">Alamat</label>
                <textarea name="alamat" class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" style="background:var(--dm-card,#fff); color:var(--dm-text); border:1px solid var(--dm-border,#d1d5db); border-radius:10px;" rows="3"></textarea>
            </div>


            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalAdd')" class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Detail Employee Modal -->
<div id="modalDetail" class="modal-pg hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-8">
    <div class="modal-inner w-full max-w-3xl relative mx-4 my-auto" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:16px; overflow:hidden;">
        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <h2 style="font-size:16px; font-weight:700; color:var(--dm-text,#1e293b); margin:0;">Detail Pegawai</h2>
            <button onclick="closeModal('modalDetail')" class="modal-close" style="width:32px;height:32px;border-radius:8px;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;background:var(--dm-bg,#f1f5f9);color:var(--dm-muted,#64748b);"><i class="fas fa-times"></i></button>
        </div>

        <!-- Profile Banner -->
        <div style="display:flex; align-items:center; gap:16px; padding:20px 24px; border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <img id="detailFoto" style="width:72px; height:72px; border-radius:50%; object-fit:cover; border:3px solid var(--dm-border,#e2e8f0); flex-shrink:0;">
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:6px; margin-bottom:2px;">
                    <span id="detailNama" style="font-size:18px; font-weight:700; color:var(--dm-text,#1e293b);"></span>
                    <button onclick="copyText('detailNama', this)" title="Copy nama" style="background:none; border:none; cursor:pointer; color:var(--dm-muted,#94a3b8); font-size:12px; padding:2px 4px; border-radius:4px; transition:all 0.15s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='var(--dm-muted,#94a3b8)'"><i class="fas fa-copy"></i></button>
                </div>
                <div id="detailJabatan" style="font-size:13px; color:var(--dm-muted,#64748b); margin-bottom:6px;"></div>
                <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                    <span id="detailJenisPegawai" class="badge badge-primary"></span>
                    <span id="detailNIP" style="font-size:11px; color:var(--dm-muted,#94a3b8);"></span>
                    <button onclick="copyText('detailNIP', this)" title="Copy NIP" style="background:none; border:none; cursor:pointer; color:var(--dm-muted,#94a3b8); font-size:10px; padding:2px 4px; border-radius:4px; transition:all 0.15s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='var(--dm-muted,#94a3b8)'"><i class="fas fa-copy"></i></button>
                </div>
            </div>
            <span class="badge badge-success"><i class="fas fa-circle" style="font-size:6px;"></i> Aktif</span>
        </div>

        <!-- Info Grid -->
        <div style="padding:20px 24px; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div style="background:var(--dm-bg,#f9fafb); border-radius:10px; padding:10px 14px;">
                <div style="font-size:9px; font-weight:600; color:var(--dm-muted,#94a3b8); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:3px;">Email</div>
                <div style="display:flex; align-items:center; gap:5px;">
                    <span id="detailEmail" style="font-size:13px; font-weight:500; color:var(--dm-text,#1e293b);"></span>
                    <button onclick="copyText('detailEmail', this)" title="Copy email" style="background:none; border:none; cursor:pointer; color:var(--dm-muted,#94a3b8); font-size:10px; padding:2px 4px; border-radius:4px; transition:all 0.15s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='var(--dm-muted,#94a3b8)'"><i class="fas fa-copy"></i></button>
                </div>
            </div>
            <div style="background:var(--dm-bg,#f9fafb); border-radius:10px; padding:10px 14px;">
                <div style="font-size:9px; font-weight:600; color:var(--dm-muted,#94a3b8); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:3px;">No. Telepon</div>
                <div id="detailNoHP" style="font-size:13px; font-weight:500; color:var(--dm-text,#1e293b);"></div>
            </div>
            <div style="background:var(--dm-bg,#f9fafb); border-radius:10px; padding:10px 14px;">
                <div style="font-size:9px; font-weight:600; color:var(--dm-muted,#94a3b8); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:3px;">Unit Kerja</div>
                <div id="detailUnit" style="font-size:13px; font-weight:500; color:var(--dm-text,#1e293b);"></div>
            </div>
            <div style="background:var(--dm-bg,#f9fafb); border-radius:10px; padding:10px 14px;">
                <div style="font-size:9px; font-weight:600; color:var(--dm-muted,#94a3b8); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:3px;">Jenis Pegawai</div>
                <div id="detailJenis" style="font-size:13px; font-weight:500; color:var(--dm-text,#1e293b);"></div>
            </div>
            <div style="background:var(--dm-bg,#f9fafb); border-radius:10px; padding:10px 14px;">
                <div style="font-size:9px; font-weight:600; color:var(--dm-muted,#94a3b8); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:3px;">Tanggal Bergabung</div>
                <div id="detailTanggal" style="font-size:13px; font-weight:500; color:var(--dm-text,#1e293b);">-</div>
            </div>
            <div style="background:var(--dm-bg,#f9fafb); border-radius:10px; padding:10px 14px;">
                <div style="font-size:9px; font-weight:600; color:var(--dm-muted,#94a3b8); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:3px;">Jadwal Kerja</div>
                <div id="detailShift" style="font-size:13px; font-weight:500; color:var(--dm-text,#1e293b);">Jam Kerja Normal</div>
            </div>
            <div style="grid-column:1/-1; background:var(--dm-bg,#f9fafb); border-radius:10px; padding:10px 14px;">
                <div style="font-size:9px; font-weight:600; color:var(--dm-muted,#94a3b8); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:3px;">Lokasi Presensi</div>
                <div id="detailLokasiPresensi" style="font-size:13px; font-weight:500; color:var(--dm-text,#1e293b);">-</div>
            </div>
            <div style="grid-column:1/-1; background:var(--dm-bg,#f9fafb); border-radius:10px; padding:10px 14px;">
                <div style="font-size:9px; font-weight:600; color:var(--dm-muted,#94a3b8); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:3px;">Alamat</div>
                <div id="detailAlamat" style="font-size:13px; font-weight:500; color:var(--dm-text,#1e293b); line-height:1.5;">-</div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div style="display:flex; align-items:center; gap:8px; padding:16px 24px; border-top:1px solid var(--dm-border,#e2e8f0);">
            <button onclick="showDeleteConfirmation()" class="btn-danger" style="padding:10px 16px;"><i class="fas fa-trash-alt"></i> Hapus</button>
            <div style="flex:1;"></div>
            <button onclick="showResetPasswordConfirmation()" class="btn-primary" style="padding:10px 16px;"><i class="fas fa-key"></i> Reset Password</button>
            <button onclick="openEditModalFromDetail()" class="btn-warning" style="padding:10px 16px;"><i class="fas fa-edit"></i> Edit</button>
            <button onclick="closeModal('modalDetail')" class="btn-secondary" style="padding:10px 20px;">Tutup</button>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="modalEdit" class="modal-pg hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-4">
    <div class="modal-inner w-full max-w-4xl relative mx-4 my-auto" id="modalEditContent" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:16px; overflow:hidden;">
        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <h2 style="font-size:16px; font-weight:700; color:var(--dm-text,#1e293b); margin:0;">Edit Data Pegawai</h2>
            <button onclick="closeModal('modalEdit')" style="width:32px;height:32px;border-radius:8px;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;background:var(--dm-bg,#f1f5f9);color:var(--dm-muted,#64748b);"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEdit" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id">
            @php $inputStyle = 'width:100%; padding:9px 12px; border:1px solid var(--dm-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-card,#fff); color:var(--dm-text); outline:none;'; $labelStyle = 'display:block; font-size:11px; font-weight:600; color:var(--dm-muted,#64748b); margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;'; @endphp

            <!-- Form Grid -->
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; padding:20px 24px;">
                <div>
                    <label style="{{ $labelStyle }}">NIP</label>
                    <input type="number" name="nip" id="edit_nip" style="{{ $inputStyle }}" required>
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" style="{{ $inputStyle }}" required>
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Jabatan</label>
                    <input type="text" name="jabatan" id="edit_jabatan" style="{{ $inputStyle }}">
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Email</label>
                    <input type="email" name="email" id="edit_email" style="{{ $inputStyle }}">
                </div>
                <div>
                    <label style="{{ $labelStyle }}">No HP</label>
                    <input type="text" name="no_hp" id="edit_no_hp" style="{{ $inputStyle }}">
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Unit Utama</label>
                    <select name="unit_id" id="edit_unit" style="{{ $inputStyle }}">
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Jenis Pegawai</label>
                    <select name="jenis_pegawai" id="edit_jenis_pegawai" style="{{ $inputStyle }}">
                        <option value="asn">ASN</option>
                        <option value="non_asn">Non ASN</option>
                        <option value="outsourcing">Outsourcing</option>
                    </select>
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Pegawai Shift?</label>
                    <select name="can_shift" id="edit_can_shift" style="{{ $inputStyle }}">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Alamat</label>
                    <input type="text" name="alamat" id="edit_alamat" style="{{ $inputStyle }}">
                </div>
                <div style="grid-column:1/-1;">
                    <label style="{{ $labelStyle }}">Lokasi Presensi (boleh lebih dari 1)</label>
                    <div id="editWilayahCheckboxes" style="display:flex; flex-wrap:wrap; gap:6px; padding:10px; border:1px solid var(--dm-border,#d1d5db); border-radius:8px;">
                        @foreach($units as $u)
                        <label style="display:flex; align-items:center; gap:4px; cursor:pointer; font-size:12px; color:var(--dm-text,#374151); padding:3px 8px; border-radius:6px; background:var(--dm-bg,#f9fafb);">
                            <input type="checkbox" name="wilayah_ids[]" value="{{ $u->id }}" class="edit-wilayah-cb" style="accent-color:#2E97D4;">
                            {{ $u->nama }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:16px 24px; border-top:1px solid var(--dm-border,#e2e8f0);">
                <button type="button" onclick="closeModal('modalEdit')" class="btn-secondary" style="padding:10px 20px;">Batal</button>
                <button type="submit" class="btn-primary" style="padding:10px 20px;"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="modalDeleteConfirmation" class="modal-pg hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="modal-inner w-full max-w-md p-6 relative mx-4" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="color:var(--dm-text,#1e293b);">Konfirmasi Hapus</h3>
            <p class="mb-6" style="color:var(--dm-muted,#64748b);">Apakah Anda yakin ingin menghapus data pegawai ini? Tindakan ini tidak dapat dibatalkan.</p>

            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeModal('modalDeleteConfirmation')"
                    class="btn-secondary">
                    Batal
                </button>
                <button type="button" onclick="confirmDelete()"
                    class="btn-danger">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Confirmation Modal -->
<div id="modalResetPasswordConfirmation" class="modal-pg hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="modal-inner w-full max-w-md p-6 relative mx-4" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <i class="fas fa-key text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="color:var(--dm-text,#1e293b);">Reset Password</h3>
            <p class="mb-6" style="color:var(--dm-muted,#64748b);">Password akan direset ke NIP pegawai. Apakah Anda yakin?</p>

            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeModal('modalResetPasswordConfirmation')"
                    class="btn-secondary">
                    Batal
                </button>
                <button type="button" onclick="confirmResetPassword()"
                    class="btn-warning">
                    <i class="fas fa-sync-alt mr-2"></i> Reset Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Notification -->
<div id="successNotification" class="hidden fixed top-4 right-4 z-50">
    <div class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between transform transition-all duration-300 ease-in-out translate-x-full max-w-sm">
        <div class="flex items-center">
            <div class="mr-3">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <div class="font-semibold" id="successTitle">Sukses!</div>
                <div class="text-sm" id="successMessage">Operasi berhasil dilakukan.</div>
            </div>
        </div>
        <button onclick="hideSuccessNotification()" class="ml-4 text-green-100 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Error Notification -->
<div id="errorNotification" class="hidden fixed top-4 right-4 z-50">
    <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between transform transition-all duration-300 ease-in-out translate-x-full max-w-sm">
        <div class="flex items-center">
            <div class="mr-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
            </div>
            <div>
                <div class="font-semibold" id="errorTitle">Error!</div>
                <div class="text-sm" id="errorMessage">Terjadi kesalahan saat memproses permintaan.</div>
            </div>
        </div>
        <button onclick="hideErrorNotification()" class="ml-4 text-red-100 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 ">
    <div class="p-6 flex items-center space-x-3" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="font-medium" style="color:var(--dm-text,#374151);">Memproses...</span>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Store current user ID for detail modal actions
    let currentDetailUserId = null;

    // Current sort state
    var currentSortCol = 'name';
    var currentSortDir = 'asc';

    function toggleSort(col) {
        if (currentSortCol === col) {
            currentSortDir = currentSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortCol = col;
            currentSortDir = 'asc';
        }
        applySortAndFilter();
        updateSortIcons();
        saveFilterState();
    }

    function updateSortIcons() {
        document.querySelectorAll('.sortable-th').forEach(function(th) {
            var icon = th.querySelector('i');
            if (th.dataset.sort === currentSortCol) {
                icon.className = currentSortDir === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                icon.style.opacity = '1';
                icon.style.color = '#2E97D4';
            } else {
                icon.className = 'fas fa-sort';
                icon.style.opacity = '0.3';
                icon.style.color = '';
            }
        });
    }

    function applySortAndFilter() {
        var tbody = document.getElementById('pegawaiTableBody');
        var rows = Array.from(tbody.querySelectorAll('.pegawai-row'));

        rows.sort(function(a, b) {
            var valA, valB;
            switch(currentSortCol) {
                case 'name': valA = a.dataset.name; valB = b.dataset.name; break;
                case 'jabatan': valA = a.dataset.jabatan || ''; valB = b.dataset.jabatan || ''; break;
                case 'unit': valA = a.dataset.unitName || ''; valB = b.dataset.unitName || ''; break;
                default: valA = ''; valB = '';
            }
            var cmp = valA.localeCompare(valB, 'id');
            return currentSortDir === 'desc' ? -cmp : cmp;
        });

        rows.forEach(function(row) { tbody.appendChild(row); });
        filterPegawai();
    }

    // Enhanced Filter Function
    function filterPegawai() {
        var searchValue = document.getElementById('searchInput').value.toLowerCase();
        var unitValue = document.getElementById('filterUnit').value;
        var jenisValue = document.getElementById('filterJenis').value;
        var rows = document.querySelectorAll('.pegawai-row');

        var visibleCount = 0;

        rows.forEach(function(row) {
            var nameMatch = row.dataset.name.includes(searchValue);
            var nipMatch = row.dataset.nip.includes(searchValue);
            var unitMatch = unitValue === '' || row.dataset.unit === unitValue;
            var jenisMatch = jenisValue === '' || row.dataset.jenis === jenisValue;

            if ((nameMatch || nipMatch) && unitMatch && jenisMatch) {
                row.style.display = '';
                visibleCount++;
                row.querySelector('td').textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('totalPegawai').textContent = visibleCount;
    }

    // Persist filter/sort state
    function saveFilterState() {
        sessionStorage.setItem('pgFilter', JSON.stringify({
            search: document.getElementById('searchInput').value,
            unit: document.getElementById('filterUnit').value,
            jenis: document.getElementById('filterJenis').value,
            sortCol: currentSortCol,
            sortDir: currentSortDir
        }));
    }

    function restoreFilterState() {
        var saved = sessionStorage.getItem('pgFilter');
        if (saved) {
            var f = JSON.parse(saved);
            if (f.search) document.getElementById('searchInput').value = f.search;
            if (f.unit) document.getElementById('filterUnit').value = f.unit;
            if (f.jenis) document.getElementById('filterJenis').value = f.jenis;
            if (f.sortCol) currentSortCol = f.sortCol;
            if (f.sortDir) currentSortDir = f.sortDir;
        }
        updateSortIcons();
        applySortAndFilter();
    }

    document.getElementById('searchInput').addEventListener('input', function() { filterPegawai(); saveFilterState(); });
    document.getElementById('filterUnit').addEventListener('change', function() { filterPegawai(); saveFilterState(); });
    document.getElementById('filterJenis').addEventListener('change', function() { filterPegawai(); saveFilterState(); });

    document.getElementById('resetFilter').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterUnit').value = '';
        document.getElementById('filterJenis').value = '';
        currentSortCol = 'name';
        currentSortDir = 'asc';
        updateSortIcons();
        applySortAndFilter();
        sessionStorage.removeItem('pgFilter');
    });

    restoreFilterState();

    // Modal functions with animations
    function copyText(elId, btn) {
        var el = document.getElementById(elId);
        if (!el) return;
        var text = el.textContent.replace(/^NIP:\s*/i, '').trim();
        navigator.clipboard.writeText(text).then(function() {
            var icon = btn.querySelector('i');
            icon.className = 'fas fa-check';
            btn.style.color = '#10b981';
            setTimeout(function() { icon.className = 'fas fa-copy'; btn.style.color = 'var(--dm-muted,#94a3b8)'; }, 1500);
        });
    }

    function openModal(id) {
        var modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(function() {
            modal.classList.add('show');
        });
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('show');
        setTimeout(function() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const modals = ['modalAdd', 'modalDetail', 'modalEdit', 'modalDeleteConfirmation', 'modalResetPasswordConfirmation'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    });

    // Loading functions
    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
    }

    // Notification functions
    function showSuccessNotification(title, message) {
        document.getElementById('successTitle').textContent = title;
        document.getElementById('successMessage').textContent = message;

        const successNotification = document.getElementById('successNotification');
        successNotification.classList.remove('hidden');
        setTimeout(() => {
            successNotification.querySelector('div').classList.remove('translate-x-full');
        }, 10);

        setTimeout(() => {
            hideSuccessNotification();
        }, 5000);
    }

    function hideSuccessNotification() {
        const successNotification = document.getElementById('successNotification');
        successNotification.querySelector('div').classList.add('translate-x-full');
        setTimeout(() => {
            successNotification.classList.add('hidden');
        }, 300);
    }

    function showErrorNotification(title, message) {
        document.getElementById('errorTitle').textContent = title;
        document.getElementById('errorMessage').textContent = message;

        const errorNotification = document.getElementById('errorNotification');
        errorNotification.classList.remove('hidden');
        setTimeout(() => {
            errorNotification.querySelector('div').classList.remove('translate-x-full');
        }, 10);

        setTimeout(() => {
            hideErrorNotification();
        }, 5000);
    }

    function hideErrorNotification() {
        const errorNotification = document.getElementById('errorNotification');
        errorNotification.querySelector('div').classList.add('translate-x-full');
        setTimeout(() => {
            errorNotification.classList.add('hidden');
        }, 300);
    }

    // Confirmation Modal Functions
    function showDeleteConfirmation() {
        if (currentDetailUserId) {
            openModal('modalDeleteConfirmation');
        }
    }

    function showResetPasswordConfirmation() {
        if (currentDetailUserId) {
            openModal('modalResetPasswordConfirmation');
        }
    }

    function confirmDelete() {
        if (currentDetailUserId) {
            closeModal('modalDeleteConfirmation');
            deleteUser(currentDetailUserId);
        }
    }

    function confirmResetPassword() {
        if (currentDetailUserId) {
            closeModal('modalResetPasswordConfirmation');
            resetPassword(currentDetailUserId);
        }
    }

    // Tambah Pegawai
    document.getElementById('formAdd').addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();

        let formData = new FormData(this);
        fetch("{{ route('admin.manajemenpegawai.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessNotification('Sukses!', data.message);
                    closeModal('modalAdd');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorNotification('Error!', data.message || 'Terjadi kesalahan!');
                }
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat mengirim data!');
            });
    });

    // Detail Pegawai
    function openDetailModal(id) {
        currentDetailUserId = id;
        showLoading();

        fetch(`/admin/manajemen-pegawai/${id}`)
            .then(res => res.json())
            .then(user => {
                hideLoading();
                let foto = user.foto_profil ?
                    `{{ asset('public/storage/foto_profil/') }}/${user.foto_profil}` :
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0a9396&color=fff`;

                // Set photo
                document.getElementById('detailFoto').src = foto;
                document.getElementById('detailFoto').onerror = function() {
                    this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0a9396&color=fff`;
                };

                // Set basic info
                document.getElementById('detailNama').textContent = user.name;
                document.getElementById('detailJabatan').textContent = user.jabatan || '-';
                document.getElementById('detailNIP').textContent = `NIP: ${user.nip}`;
                document.getElementById('detailJenisPegawai').textContent =
                    user.jenis_pegawai.replace(/_/g, ' ').toUpperCase();


                // Set contact info
                document.getElementById('detailEmail').textContent = user.email || '-';
                document.getElementById('detailNoHP').textContent = user.no_hp || '-';
                document.getElementById('detailUnit').textContent = user.wilayah_kerja?.nama || '-';
                document.getElementById('detailJenis').textContent = user.jenis_pegawai.replace(/_/g, ' ').toUpperCase();
                document.getElementById('detailAlamat').textContent = user.alamat || '-';

                // Tampilkan lokasi presensi
                const lokasiEl = document.getElementById('detailLokasiPresensi');
                if (lokasiEl) {
                    const list = (user.wilayah_kerja_list || []).map(w => w.nama);
                    lokasiEl.textContent = list.length > 0 ? list.join(', ') : '-';
                }

                // Tampilkan info shift
                const shiftEl = document.getElementById('detailShift');
                if (shiftEl) {
                    shiftEl.textContent = user.can_shift ? 'Pegawai Shift' : 'Jam Kerja Normal';
                }

                openModal('modalDetail');
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat mengambil data!');
            });
    }

    // Action functions from detail modal
    function openEditModalFromDetail() {
        if (currentDetailUserId) {
            closeModal('modalDetail');
            openEditModal(currentDetailUserId);
        }
    }

    // Edit Pegawai
    function openEditModal(id) {
        showLoading();

        fetch(`/admin/manajemen-pegawai/${id}`)
            .then(res => res.json())
            .then(user => {
                hideLoading();
                document.getElementById('edit_id').value = user.id;
                document.getElementById('edit_nip').value = user.nip;
                document.getElementById('edit_name').value = user.name;
                document.getElementById('edit_jabatan').value = user.jabatan ?? '';
                document.getElementById('edit_unit').value = user.unit_id ?? '';
                document.getElementById('edit_jenis_pegawai').value = user.jenis_pegawai;
                document.getElementById('edit_email').value = user.email ?? '';
                document.getElementById('edit_no_hp').value = user.no_hp ?? '';
                document.getElementById('edit_can_shift').value = user.can_shift ? '1' : '0';
                document.getElementById('edit_alamat').value = user.alamat ?? '';

                // Set lokasi presensi checkboxes
                const wilayahIds = user.wilayah_ids || [];
                document.querySelectorAll('.edit-wilayah-cb').forEach(cb => {
                    cb.checked = wilayahIds.includes(parseInt(cb.value));
                });

                openModal('modalEdit');
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat mengambil data!');
            });
    }

    // Submit Edit
    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();

        let id = document.getElementById('edit_id').value;
        let formData = new FormData(this);
        formData.append('_method', 'PUT');

        fetch(`/admin/manajemen-pegawai/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessNotification('Sukses!', data.message);
                    closeModal('modalEdit');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorNotification('Error!', data.message || 'Terjadi kesalahan!');
                }
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat mengirim data!');
            });
    });

    // Reset Password
    function resetPassword(id) {
        showLoading();

        fetch(`/admin/manajemen-pegawai/${id}/reset-password`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessNotification('Sukses!', data.message);
                } else {
                    showErrorNotification('Error!', data.message || 'Terjadi kesalahan!');
                }
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat reset password!');
            });
    }

    // Hapus Pegawai
    function deleteUser(id) {
        showLoading();

        fetch(`/admin/manajemen-pegawai/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessNotification('Sukses!', data.message);
                    closeModal('modalDetail');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorNotification('Error!', data.message || 'Terjadi kesalahan!');
                }
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat menghapus data!');
            });
    }

</script>
@endpush