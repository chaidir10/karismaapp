@extends('layouts.admin')
@section('title', 'Pengumuman')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 180px; font-size: 14px; }
    .ql-toolbar { border-radius: 12px 12px 0 0; border-color: #d1d5db; }
    .ql-container { border-radius: 0 0 12px 12px; border-color: #d1d5db; }
    .pengumuman-card { background:#fff; border-radius:14px; padding:16px; margin-bottom:12px; box-shadow:0 1px 6px rgba(0,0,0,0.05); border:1px solid #e2e8f0; display:flex; gap:14px; align-items:flex-start; }
    .pengumuman-card .pc-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:18px; color:#fff; flex-shrink:0; }
    .pengumuman-card .pc-body { flex:1; min-width:0; }
    .pengumuman-card .pc-title { font-size:15px; font-weight:700; color:#1e293b; margin-bottom:2px; }
    .pengumuman-card .pc-meta { font-size:11px; color:#94a3b8; margin-bottom:4px; }
    .pengumuman-card .pc-preview { font-size:12px; color:#64748b; line-height:1.4; max-height:40px; overflow:hidden; }
    .pengumuman-card .pc-actions { flex-shrink:0; display:flex; gap:6px; }
    .pengumuman-card .pc-actions button { width:34px; height:34px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:13px; transition:all 0.15s; }
    .pengumuman-card .pc-actions button:hover { background:#f1f5f9; }
    .toggle-active { color:#10b981; }
    .toggle-inactive { color:#ef4444; }
    .date-options { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px; }
    .date-options label { font-size:12px; display:flex; align-items:center; gap:4px; cursor:pointer; color:#475569; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-6 mb-8 shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Pengumuman</h1>
                <p class="text-blue-100 mt-1">Kelola pengumuman dan informasi untuk pegawai</p>
            </div>
            <button onclick="openModal()" class="bg-white text-blue-600 px-5 py-2 rounded-xl font-semibold text-sm hover:bg-blue-50 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Pengumuman
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <div id="pengumumanList">
        @forelse($pengumumans as $p)
        <div class="pengumuman-card">
            <div class="pc-icon" style="background:{{ $jenisOptions[$p->jenis]['color'] ?? '#64748b' }}">
                <i class="fas {{ $jenisOptions[$p->jenis]['icon'] ?? 'fa-bell' }}"></i>
            </div>
            <div class="pc-body">
                <div class="pc-title">{{ $p->judul }}</div>
                <div class="pc-meta">
                    <span style="background:{{ $jenisOptions[$p->jenis]['color'] ?? '#64748b' }}20; color:{{ $jenisOptions[$p->jenis]['color'] ?? '#64748b' }}; padding:2px 8px; border-radius:6px; font-weight:600; font-size:10px; text-transform:uppercase;">{{ $jenisOptions[$p->jenis]['label'] ?? $p->jenis }}</span>
                    @if($p->tanggal_mulai)
                        &middot; {{ $p->tanggal_mulai->format('d M Y') }}@if($p->tanggal_selesai && $p->tanggal_selesai != $p->tanggal_mulai) - {{ $p->tanggal_selesai->format('d M Y') }}@endif
                    @endif
                    @if($p->waktu) &middot; {{ \Carbon\Carbon::parse($p->waktu)->format('H:i') }} @endif
                    &middot; {{ $p->created_at->diffForHumans() }}
                    @if(!$p->is_active) &middot; <span style="color:#ef4444; font-weight:600;">Nonaktif</span> @endif
                </div>
                <div class="pc-preview">{!! strip_tags($p->isi) !!}</div>
            </div>
            <div class="pc-actions">
                <button onclick="toggleActive({{ $p->id }})" title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" class="{{ $p->is_active ? 'toggle-active' : 'toggle-inactive' }}" id="toggleBtn{{ $p->id }}">
                    <i class="fas {{ $p->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                </button>
                <button onclick="editPengumuman({{ $p->id }})" title="Edit" style="color:#3b82f6;">
                    <i class="fas fa-pen"></i>
                </button>
                <button onclick="hapusPengumuman({{ $p->id }})" title="Hapus" style="color:#ef4444;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:60px 20px; color:#94a3b8;">
            <i class="fas fa-bullhorn" style="font-size:40px; opacity:0.3; display:block; margin-bottom:12px;"></i>
            <p style="font-size:14px;">Belum ada pengumuman</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:20px; width:95%; max-width:700px; max-height:90vh; overflow-y:auto; padding:28px; position:relative;">
        <button onclick="closeModal()" style="position:absolute; top:16px; right:16px; background:none; border:none; font-size:20px; cursor:pointer; color:#94a3b8;"><i class="fas fa-times"></i></button>
        <h3 id="modalTitle" style="font-size:18px; font-weight:700; margin-bottom:20px; color:#1e293b;">Tambah Pengumuman</h3>

        <form id="pengumumanForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Judul</label>
                <input type="text" name="judul" id="inputJudul" required style="width:100%; border:1px solid #d1d5db; border-radius:12px; padding:10px 14px; font-size:14px; outline:none;" placeholder="Judul pengumuman">
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Jenis</label>
                <select name="jenis" id="inputJenis" style="width:100%; border:1px solid #d1d5db; border-radius:12px; padding:10px 14px; font-size:14px; outline:none;">
                    @foreach($jenisOptions as $key => $opt)
                    <option value="{{ $key }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Tanggal & Waktu <span style="font-weight:400; color:#94a3b8;">(opsional)</span></label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <input type="date" name="tanggal_mulai" id="inputTglMulai" style="flex:1; min-width:140px; border:1px solid #d1d5db; border-radius:12px; padding:10px 14px; font-size:14px; outline:none;" placeholder="Tanggal mulai">
                    <input type="date" name="tanggal_selesai" id="inputTglSelesai" style="flex:1; min-width:140px; border:1px solid #d1d5db; border-radius:12px; padding:10px 14px; font-size:14px; outline:none;" placeholder="Tanggal selesai">
                    <input type="time" name="waktu" id="inputWaktu" style="width:130px; border:1px solid #d1d5db; border-radius:12px; padding:10px 14px; font-size:14px; outline:none;">
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Gambar Cover <span style="font-weight:400; color:#94a3b8;">(opsional)</span></label>
                <input type="file" name="gambar" id="inputGambar" accept="image/*" style="width:100%; border:1px solid #d1d5db; border-radius:12px; padding:10px 14px; font-size:14px;">
                <label style="font-size:12px; margin-top:6px; display:flex; align-items:center; gap:4px; cursor:pointer; color:#64748b;">
                    <input type="checkbox" name="hapus_gambar" id="inputHapusGambar" value="1"> Hapus gambar saat ini
                </label>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Isi</label>
                <div id="quillEditor"></div>
                <input type="hidden" name="isi" id="inputIsi">
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeModal()" style="padding:10px 20px; border-radius:12px; border:1px solid #d1d5db; background:#fff; font-size:14px; cursor:pointer; font-weight:500;">Batal</button>
                <button type="submit" style="padding:10px 24px; border-radius:12px; border:none; background:linear-gradient(135deg,#3b82f6,#2563eb); color:#fff; font-size:14px; cursor:pointer; font-weight:600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    var quill = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Tulis isi pengumuman...',
        modules: {
            toolbar: {
                container: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ],
                handlers: {
                    image: function() {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.click();
                        input.onchange = function() {
                            var file = input.files[0];
                            if (!file) return;
                            var fd = new FormData();
                            fd.append('image', file);
                            fd.append('_token', '{{ csrf_token() }}');
                            fetch("{{ route('admin.pengumuman.upload-image') }}", { method: 'POST', body: fd })
                                .then(function(r) { return r.json(); })
                                .then(function(data) {
                                    var range = quill.getSelection(true);
                                    quill.insertEmbed(range.index, 'image', data.url);
                                });
                        };
                    }
                }
            }
        }
    });

    document.getElementById('pengumumanForm').addEventListener('submit', function() {
        document.getElementById('inputIsi').value = quill.root.innerHTML;
    });

    function openModal(id) {
        document.getElementById('formModal').style.display = 'flex';
        if (!id) {
            document.getElementById('modalTitle').textContent = 'Tambah Pengumuman';
            document.getElementById('pengumumanForm').action = "{{ route('admin.pengumuman.store') }}";
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('inputJudul').value = '';
            document.getElementById('inputJenis').value = 'pengumuman';
            document.getElementById('inputTglMulai').value = '';
            document.getElementById('inputTglSelesai').value = '';
            document.getElementById('inputWaktu').value = '';
            document.getElementById('inputHapusGambar').checked = false;
            quill.root.innerHTML = '';
        }
    }

    function closeModal() {
        document.getElementById('formModal').style.display = 'none';
    }

    function editPengumuman(id) {
        fetch("{{ url('/admin/pengumuman') }}/" + id)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.getElementById('modalTitle').textContent = 'Edit Pengumuman';
                document.getElementById('pengumumanForm').action = "{{ url('/admin/pengumuman') }}/" + id;
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('inputJudul').value = data.judul;
                document.getElementById('inputJenis').value = data.jenis;
                document.getElementById('inputTglMulai').value = data.tanggal_mulai ? data.tanggal_mulai.substring(0, 10) : '';
                document.getElementById('inputTglSelesai').value = data.tanggal_selesai ? data.tanggal_selesai.substring(0, 10) : '';
                document.getElementById('inputWaktu').value = data.waktu ? data.waktu.substring(0, 5) : '';
                document.getElementById('inputHapusGambar').checked = false;
                quill.root.innerHTML = data.isi;
                document.getElementById('formModal').style.display = 'flex';
            });
    }

    function hapusPengumuman(id) {
        if (!confirm('Yakin ingin menghapus pengumuman ini?')) return;
        var form = document.getElementById('deleteForm');
        form.action = "{{ url('/admin/pengumuman') }}/" + id;
        form.submit();
    }

    function toggleActive(id) {
        fetch("{{ url('/admin/pengumuman') }}/" + id + "/toggle", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var btn = document.getElementById('toggleBtn' + id);
            btn.className = data.is_active ? 'toggle-active' : 'toggle-inactive';
            btn.innerHTML = '<i class="fas ' + (data.is_active ? 'fa-eye' : 'fa-eye-slash') + '"></i>';
            btn.title = data.is_active ? 'Nonaktifkan' : 'Aktifkan';
            location.reload();
        });
    }
</script>
@endpush
