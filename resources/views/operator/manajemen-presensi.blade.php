@extends('layouts.operator')
@section('title', 'Database Presensi')

@section('content')
<style>
    .pres-card { background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden; }
    .pres-table { width:100%; border-collapse:collapse; }
    .pres-table th { font-size:10px; text-transform:uppercase; letter-spacing:.4px; color:var(--dm-muted,#64748b); text-align:left; padding:10px 14px; background:var(--dm-bg,#f8fafc); white-space:nowrap; }
    .pres-table td { font-size:12px; color:var(--dm-text,#1e293b); padding:10px 14px; border-top:1px solid var(--dm-border,#f1f5f9); }
    .pres-table tbody tr:hover { background:var(--dm-bg,#f8fafc); }
    [data-theme="dark"] .pres-table th { background:rgba(255,255,255,0.02); }
    [data-theme="dark"] .pres-table tbody tr:hover { background:rgba(255,255,255,0.02); }
    .filter-row { display:flex; gap:10px; flex-wrap:wrap; align-items:end; }
    .filter-group label { font-size:11px; font-weight:600; color:var(--dm-muted,#64748b); display:block; margin-bottom:3px; }
    .filter-group input, .filter-group select {
        padding:7px 10px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:8px; font-size:12px;
        background:var(--dm-input,#fff); color:var(--dm-text); min-width:120px;
    }
    .pagination-wrap { padding:12px 14px; border-top:1px solid var(--dm-border,#e2e8f0); display:flex; justify-content:center; }

    .edit-modal {
        display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,0.5);
        align-items:center; justify-content:center;
    }
    .edit-modal.active { display:flex; }
    .edit-modal-content {
        background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:16px;
        width:90%; max-width:420px; padding:24px; animation:acSlideUp 0.25s ease;
    }
    .edit-modal-content h3 { font-size:16px; font-weight:700; color:var(--dm-text,#1e293b); margin:0 0 16px; }
    .form-group { margin-bottom:12px; }
    .form-group label { font-size:12px; font-weight:600; color:var(--dm-text,#374151); display:block; margin-bottom:4px; }
    .form-group input, .form-group select {
        width:100%; padding:9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:10px;
        font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text);
    }
</style>

<div class="page-header-glass">
    <h1>Database Presensi</h1>
    <p>Kelola data presensi pegawai &mdash; ubah tanggal, jam, jenis, dan status</p>
</div>

<!-- Filters -->
<div class="pres-card" style="margin-bottom:14px;">
    <div style="padding:14px 18px;">
        <form method="GET" action="{{ route('operator.presensi.index') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Cari Pegawai</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NIP...">
                </div>
                <div class="filter-group">
                    <label>Pegawai</label>
                    <select name="user_id">
                        <option value="">Semua</option>
                        @foreach($pegawai as $p)
                        <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Jenis</label>
                    <select name="jenis">
                        <option value="">Semua</option>
                        <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                        <option value="pulang" {{ request('jenis') == 'pulang' ? 'selected' : '' }}>Pulang</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label>Sampai</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="filter-group" style="display:flex; gap:6px; padding-bottom:1px;">
                    <button type="submit" class="btn-primary" style="padding:7px 14px; font-size:12px;"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('operator.presensi.index') }}" class="btn-secondary" style="padding:7px 14px; font-size:12px;"><i class="fas fa-rotate"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="pres-card">
    <div style="overflow-x:auto;">
        <table class="pres-table">
            <thead>
                <tr>
                    <th>Pegawai</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Lembur</th>
                    <th>Darurat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensi as $p)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:28px; height:28px; border-radius:7px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0;">
                                {{ substr($p->user->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $p->user->name ?? '-' }}</div>
                                <div style="font-size:10px; color:var(--dm-muted,#94a3b8);">{{ $p->user->nip ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                    <td style="font-weight:700;">{{ $p->jam }}</td>
                    <td>
                        <span class="badge {{ $p->jenis === 'masuk' ? 'badge-success' : 'badge-primary' }}">
                            <i class="fas {{ $p->jenis === 'masuk' ? 'fa-right-to-bracket' : 'fa-right-from-bracket' }}"></i>
                            {{ ucfirst($p->jenis) }}
                        </span>
                    </td>
                    <td>
                        @if($p->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($p->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </td>
                    <td>{{ $p->is_lembur ? 'Ya' : '-' }}</td>
                    <td>{{ $p->is_darurat ? 'Ya' : '-' }}</td>
                    <td>
                        <div style="display:flex; gap:4px;">
                            <button type="button" class="btn-edit" onclick="openEditModal({{ json_encode($p) }})" title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button type="button" class="btn-delete" onclick="deletePresensi({{ $p->id }})" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8"><div style="padding:30px; text-align:center; color:var(--dm-muted,#94a3b8); font-size:13px;"><i class="fas fa-database" style="font-size:24px; display:block; margin-bottom:8px;"></i> Tidak ada data presensi</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('operator.partials.pagination', ['paginator' => $presensi])
</div>

<!-- Edit Modal -->
<div class="edit-modal" id="editModal">
    <div class="edit-modal-content">
        <h3><i class="fas fa-pen" style="color:#2E97D4; margin-right:6px;"></i> Edit Presensi</h3>
        <form method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" id="editTanggal" required>
            </div>
            <div class="form-group">
                <label>Jam</label>
                <input type="time" name="jam" id="editJam" step="1" required>
            </div>
            <div class="form-group">
                <label>Jenis</label>
                <select name="jenis" id="editJenis">
                    <option value="masuk">Masuk</option>
                    <option value="pulang">Pulang</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="editStatus">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div style="display:flex; gap:10px; margin-top:18px;">
                <button type="button" class="btn-secondary" style="flex:1;" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-primary" style="flex:1;"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Forms -->
@foreach($presensi as $p)
<form method="POST" action="{{ route('operator.presensi.destroy', $p->id) }}" id="deleteForm{{ $p->id }}" style="display:none;">
    @csrf @method('DELETE')
</form>
@endforeach

@push('scripts')
<script>
    function openEditModal(data) {
        document.getElementById('editForm').action = '/operator/presensi/' + data.id;
        document.getElementById('editTanggal').value = data.tanggal;
        document.getElementById('editJam').value = data.jam;
        document.getElementById('editJenis').value = data.jenis;
        document.getElementById('editStatus').value = data.status;
        document.getElementById('editModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    function deletePresensi(id) {
        showConfirm({
            type: 'danger',
            title: 'Hapus Data Presensi?',
            message: 'Data presensi yang dihapus tidak dapat dikembalikan.',
            confirmText: 'Ya, Hapus',
            onConfirm: function() {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }
</script>
@endpush
@endsection
