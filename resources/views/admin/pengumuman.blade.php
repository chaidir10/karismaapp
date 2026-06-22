@extends('layouts.admin')
@section('title', 'Pengumuman')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 200px; font-size: 14px; color: var(--dark); }
    .ql-toolbar { border-radius: 12px 12px 0 0 !important; border-color: var(--input-border) !important; background: var(--card-bg); }
    .ql-container { border-radius: 0 0 12px 12px !important; border-color: var(--input-border) !important; background: var(--card-bg); }
    .ql-toolbar .ql-stroke { stroke: var(--text-muted) !important; }
    .ql-toolbar .ql-fill { fill: var(--text-muted) !important; }
    .ql-toolbar .ql-picker-label { color: var(--text-muted) !important; }
    .ql-editor.ql-blank::before { color: var(--gray) !important; }

    .p-card {
        background: #fff; border-radius: 16px; margin-bottom: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04); border: 1px solid #f1f5f9;
        overflow: hidden; transition: box-shadow 0.2s, transform 0.15s;
        cursor: default;
    }
    .p-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    .p-card.dragging { opacity:0.5; transform:scale(0.98); }
    .p-card.drag-over { border-top:2px solid #3b82f6; }
    .p-card-inner { display: flex; gap: 12px; padding: 16px 20px; align-items: center; }
    .drag-handle {
        cursor: grab; color: #cbd5e1; font-size: 16px; flex-shrink: 0;
        display: flex; align-items: center; padding: 4px;
        -webkit-tap-highlight-color: transparent;
    }
    .drag-handle:active { cursor: grabbing; color: #94a3b8; }
    .p-card .p-thumb {
        width: 180px; height: 90px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; color: #fff; background-size: cover; background-position: center; background-repeat: no-repeat;
        background-color: var(--dm-card, #f1f5f9); overflow: hidden;
    }
    .p-card .p-info { flex: 1; min-width: 0; }
    .p-card .p-title { font-size: 15px; font-weight: 700; color: var(--dm-text, #1e293b); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .p-card .p-meta { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; font-size: 11px; color: #94a3b8; margin-bottom: 6px; }
    .p-card .p-meta .p-tag { padding: 2px 10px; border-radius: 8px; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px; color: #fff; }
    .p-card .p-excerpt { font-size: 12px; color: var(--dm-muted, #64748b); line-height: 1.4; max-height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .p-card .p-badge-inactive { display: inline-block; background: #fef2f2; color: #dc2626; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 6px; }
    .p-card .p-actions { flex-shrink: 0; display: flex; gap: 4px; }
    .p-card .p-actions button {
        width: 36px; height: 36px; border-radius: 10px; border: none;
        background: var(--dm-bg, #f8fafc); cursor: pointer; display: flex; align-items: center;
        justify-content: center; font-size: 14px; transition: all 0.15s;
    }
    .p-card .p-actions button:hover { background: #e2e8f0; }
    .btn-toggle-on { color: #10b981; }
    .btn-toggle-off { color: #ef4444; }
    .btn-edit { color: #3b82f6; }
    .btn-delete { color: #ef4444; }

    /* Form field */
    .form-label { font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px; }
    .form-label .optional { font-weight: 400; color: #94a3b8; }
    .form-input {
        width: 100%; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 10px 14px; font-size: 14px; outline: none; transition: border-color 0.2s;
    }
    .form-input:focus { border-color: #3b82f6; }

    /* Preview gambar */
    .cover-preview {
        margin-top: 10px; border-radius: 12px; overflow: hidden;
        border: 2px dashed #e2e8f0; position: relative; display: none;
    }
    .cover-preview img {
        width: 100%; height: auto; display: block;
    }
    .cover-preview .preview-remove {
        position: absolute; top: 8px; right: 8px; width: 28px; height: 28px;
        border-radius: 50%; background: rgba(0,0,0,0.5); color: #fff; border: none;
        cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-6 mb-8 shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Pengumuman</h1>
                <p class="text-blue-100 mt-1">Kelola pengumuman dan informasi untuk pegawai</p>
            </div>
            <button onclick="openModal()" class="btn-primary">
                <i class="fas fa-plus"></i> Tambah Pengumuman
            </button>
        </div>
    </div>

    <!-- Note -->
    <div style="background:var(--dm-card, #f0f9ff); border:1px solid var(--dm-border, #bae6fd); border-radius:12px; padding:10px 14px; margin-bottom:16px; display:flex; align-items:center; gap:10px; font-size:12px; color:var(--dm-muted, #0369a1);">
        <i class="fas fa-grip-vertical" style="font-size:14px; opacity:0.5;"></i>
        <span>Geser card untuk mengatur urutan. Urutan paling atas akan muncul pertama di slider pegawai.</span>
    </div>

    <!-- List -->
    <div id="pengumumanSortable">
        @forelse($pengumumans as $p)
        @php
            $opt = $jenisOptions[$p->jenis] ?? ['icon' => 'fa-bell', 'color' => '#64748b', 'label' => $p->jenis];
        @endphp
        <div class="p-card" data-id="{{ $p->id }}" draggable="true">
            <div class="p-card-inner">
                <div class="drag-handle" title="Geser untuk ubah urutan">
                    <i class="fas fa-grip-vertical"></i>
                </div>

                @if($p->gambar)
                <div class="p-thumb" style="background-image:url('{{ asset('public/storage/'.$p->gambar) }}');"></div>
                @else
                <div class="p-thumb" style="background:{{ $opt['color'] }};">
                    <i class="fas {{ $opt['icon'] }}"></i>
                </div>
                @endif

                <div class="p-info">
                    <div class="p-title">{{ $p->judul }}</div>
                    <div class="p-meta">
                        <span class="p-tag" style="background:{{ $opt['color'] }};">{{ $opt['label'] }}</span>
                        @if($p->tanggal_mulai)
                        <span><i class="far fa-calendar-alt" style="margin-right:2px;"></i> {{ $p->tanggal_mulai->format('d M Y') }}@if($p->tanggal_selesai && $p->tanggal_selesai->ne($p->tanggal_mulai)) - {{ $p->tanggal_selesai->format('d M Y') }}@endif</span>
                        @endif
                        @if($p->waktu)
                        <span><i class="far fa-clock" style="margin-right:2px;"></i> {{ \Carbon\Carbon::parse($p->waktu)->format('H:i') }}</span>
                        @endif
                        <span>{{ $p->created_at->diffForHumans() }}</span>
                        @if(!$p->is_active)
                        <span class="p-badge-inactive"><i class="fas fa-eye-slash" style="margin-right:2px;"></i> Nonaktif</span>
                        @endif
                    </div>
                    <div class="p-excerpt">{!! strip_tags($p->isi) !!}</div>
                </div>

                <div class="p-actions">
                    <button onclick="toggleActive({{ $p->id }})" title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" class="{{ $p->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}" id="toggleBtn{{ $p->id }}">
                        <i class="fas {{ $p->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                    </button>
                    <button onclick="editPengumuman({{ $p->id }})" title="Edit" class="btn-edit">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button onclick="hapusPengumuman({{ $p->id }})" title="Hapus" class="btn-delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl" style="text-align:center; padding:60px 20px;">
            <div style="width:64px; height:64px; border-radius:16px; background:var(--dm-card, #f1f5f9); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:24px; color:var(--dm-muted, #94a3b8);">
                <i class="fas fa-bullhorn"></i>
            </div>
            <p style="font-size:15px; font-weight:600; color:var(--dm-muted, #64748b); margin:0 0 4px;">Belum ada pengumuman</p>
            <p style="font-size:13px; color:var(--dm-muted, #94a3b8); margin:0;">Klik tombol "Tambah Pengumuman" untuk membuat yang baru</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
    <div style="background:var(--dm-card, #fff); border-radius:20px; width:95%; max-width:700px; max-height:90vh; overflow-y:auto; padding:28px; position:relative;">
        <button onclick="closeModal()" style="position:absolute; top:16px; right:16px; background:none; border:none; font-size:20px; cursor:pointer; color:var(--dm-muted, #94a3b8); width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:10px;"
            onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
            <i class="fas fa-times"></i>
        </button>
        <h3 id="modalTitle" style="font-size:18px; font-weight:700; margin-bottom:24px; color:var(--dm-text, #1e293b);">Tambah Pengumuman</h3>

        <form id="pengumumanForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <!-- Judul -->
            <div style="margin-bottom:16px;">
                <label class="form-label">Judul</label>
                <input type="text" name="judul" id="inputJudul" required class="form-input" placeholder="Judul pengumuman">
            </div>

            <!-- Jenis -->
            <div style="margin-bottom:16px;">
                <label class="form-label">Jenis</label>
                <select name="jenis" id="inputJenis" class="form-input">
                    @foreach($jenisOptions as $key => $opt)
                    <option value="{{ $key }}" data-icon="{{ $opt['icon'] }}" data-color="{{ $opt['color'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal & Waktu -->
            <div style="margin-bottom:16px;">
                <label class="form-label">Tanggal & Waktu <span class="optional">(opsional, isi sesuai kebutuhan)</span></label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:140px;">
                        <div style="font-size:11px; color:var(--dm-muted, #94a3b8); margin-bottom:4px;">Tanggal Mulai</div>
                        <input type="date" name="tanggal_mulai" id="inputTglMulai" class="form-input">
                    </div>
                    <div style="flex:1; min-width:140px;">
                        <div style="font-size:11px; color:var(--dm-muted, #94a3b8); margin-bottom:4px;">Tanggal Selesai</div>
                        <input type="date" name="tanggal_selesai" id="inputTglSelesai" class="form-input">
                    </div>
                    <div style="width:130px;">
                        <div style="font-size:11px; color:var(--dm-muted, #94a3b8); margin-bottom:4px;">Waktu</div>
                        <input type="time" name="waktu" id="inputWaktu" class="form-input">
                    </div>
                </div>
            </div>

            <!-- Gambar Cover -->
            <div style="margin-bottom:16px;">
                <label class="form-label">Gambar Cover <span class="optional">(opsional)</span></label>
                <div style="font-size:11px; color:var(--dm-muted, #94a3b8); margin-bottom:8px;">
                    <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                    Ukuran yang disarankan: <strong>800 x 300 px</strong> (rasio 2.7:1). Format: JPG, PNG, GIF, WebP. Maks 5MB.
                </div>
                <input type="file" name="gambar" id="inputGambar" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.bmp" class="form-input" style="padding:8px 14px;" onchange="previewCover(this)">
                <div class="cover-preview" id="coverPreview">
                    <img id="coverPreviewImg" src="" alt="Preview">
                    <button type="button" class="preview-remove" onclick="removeCoverPreview()"><i class="fas fa-times"></i></button>
                </div>
                <label style="font-size:12px; margin-top:8px; display:flex; align-items:center; gap:6px; cursor:pointer; color:#64748b;">
                    <input type="checkbox" name="hapus_gambar" id="inputHapusGambar" value="1" style="accent-color:#ef4444;"> Hapus gambar saat ini
                </label>
            </div>

            <!-- Opsi Tampilan -->
            <div style="margin-bottom:16px; background:#f8fafc; border-radius:12px; padding:14px 16px; border:1px solid #e2e8f0;">
                <label style="font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--dm-text, #374151); font-weight:500;">
                    <input type="checkbox" name="sembunyikan_detail" id="inputSembunyikan" value="1" style="accent-color:#3b82f6; width:16px; height:16px;">
                    Sembunyikan judul & label di slider
                    <span style="font-size:11px; color:var(--dm-muted, #94a3b8); font-weight:400;">(hanya tampil gambar, detail tetap muncul saat diklik)</span>
                </label>
            </div>

            <!-- Isi -->
            <div style="margin-bottom:20px;">
                <label class="form-label">Isi Pengumuman</label>
                <div id="quillEditor"></div>
                <input type="hidden" name="isi" id="inputIsi">
            </div>

            <!-- Actions -->
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save" style="margin-right:6px;"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>

<!-- Toast -->
<div id="toastNotif" style="position:fixed; top:20px; right:20px; z-index:999; transform:translateX(120%); transition:transform 0.3s ease; max-width:360px;">
    <div style="background:var(--dm-card, #fff); border:1px solid var(--dm-border, #e2e8f0); border-radius:14px; padding:14px 18px; box-shadow:0 8px 24px rgba(0,0,0,0.12); display:flex; align-items:center; gap:12px;">
        <div id="toastIcon" style="width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;"></div>
        <div style="flex:1; min-width:0;">
            <div id="toastMsg" style="font-size:13px; font-weight:600; color:var(--dm-text, #1e293b);"></div>
            <div style="margin-top:6px; height:3px; border-radius:2px; background:var(--dm-border, #e2e8f0); overflow:hidden;">
                <div id="toastTimer" style="height:100%; border-radius:2px; width:100%; transition:width linear;"></div>
            </div>
        </div>
        <button onclick="hideToast()" style="background:none;border:none;color:var(--dm-muted,#94a3b8);font-size:14px;cursor:pointer;padding:4px;"><i class="fas fa-xmark"></i></button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    // Toast notification
    var _toastTimeout;
    function showToast(msg, type) {
        var el = document.getElementById('toastNotif');
        var icon = document.getElementById('toastIcon');
        var msgEl = document.getElementById('toastMsg');
        var timer = document.getElementById('toastTimer');
        msgEl.textContent = msg;
        var color = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
        icon.style.background = color + '15';
        icon.style.color = color;
        icon.innerHTML = type === 'success' ? '<i class="fas fa-check"></i>' : type === 'error' ? '<i class="fas fa-xmark"></i>' : '<i class="fas fa-info"></i>';
        timer.style.background = color;
        timer.style.width = '100%';
        timer.style.transitionDuration = '0s';
        el.style.transform = 'translateX(0)';
        setTimeout(function() { timer.style.transitionDuration = '3s'; timer.style.width = '0%'; }, 50);
        if (_toastTimeout) clearTimeout(_toastTimeout);
        _toastTimeout = setTimeout(hideToast, 3200);
    }
    function hideToast() {
        document.getElementById('toastNotif').style.transform = 'translateX(120%)';
        if (_toastTimeout) { clearTimeout(_toastTimeout); _toastTimeout = null; }
    }

    @if(session('success'))
    document.addEventListener('DOMContentLoaded', function() { showToast(@json(session('success')), 'success'); });
    @endif
</script>
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
                        input.setAttribute('accept', '.jpg,.jpeg,.png,.gif,.webp');
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

    function previewCover(input) {
        var preview = document.getElementById('coverPreview');
        var img = document.getElementById('coverPreviewImg');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeCoverPreview() {
        document.getElementById('inputGambar').value = '';
        document.getElementById('coverPreview').style.display = 'none';
    }

    document.getElementById('pengumumanForm').addEventListener('submit', function() {
        document.getElementById('inputIsi').value = quill.root.innerHTML;
    });

    function openModal() {
        document.getElementById('formModal').style.display = 'flex';
        document.getElementById('modalTitle').textContent = 'Tambah Pengumuman';
        document.getElementById('pengumumanForm').action = "{{ route('admin.pengumuman.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('inputJudul').value = '';
        document.getElementById('inputJenis').value = 'pengumuman';
        document.getElementById('inputTglMulai').value = '';
        document.getElementById('inputTglSelesai').value = '';
        document.getElementById('inputWaktu').value = '';
        document.getElementById('inputGambar').value = '';
        document.getElementById('inputHapusGambar').checked = false;
        document.getElementById('inputSembunyikan').checked = false;
        document.getElementById('coverPreview').style.display = 'none';
        quill.root.innerHTML = '';
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
                document.getElementById('inputGambar').value = '';
                document.getElementById('inputHapusGambar').checked = false;
                document.getElementById('inputSembunyikan').checked = !!data.sembunyikan_detail;
                quill.root.innerHTML = data.isi;

                var preview = document.getElementById('coverPreview');
                if (data.gambar) {
                    document.getElementById('coverPreviewImg').src = "{{ asset('public/storage') }}/" + data.gambar;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }

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
        .then(function(data) { location.reload(); });
    }

    // Drag & Drop Reorder
    (function() {
        var container = document.getElementById('pengumumanSortable');
        if (!container) return;
        var dragItem = null;

        container.addEventListener('dragstart', function(e) {
            var card = e.target.closest('.p-card');
            if (!card) return;
            dragItem = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        container.addEventListener('dragend', function(e) {
            var card = e.target.closest('.p-card');
            if (card) card.classList.remove('dragging');
            container.querySelectorAll('.p-card').forEach(function(c) { c.classList.remove('drag-over'); });
            dragItem = null;
            saveOrder();
        });

        container.addEventListener('dragover', function(e) {
            e.preventDefault();
            var card = e.target.closest('.p-card');
            if (!card || card === dragItem) return;
            container.querySelectorAll('.p-card').forEach(function(c) { c.classList.remove('drag-over'); });
            var rect = card.getBoundingClientRect();
            var mid = rect.top + rect.height / 2;
            if (e.clientY < mid) {
                card.classList.add('drag-over');
                container.insertBefore(dragItem, card);
            } else {
                container.insertBefore(dragItem, card.nextSibling);
            }
        });

        function saveOrder() {
            var ids = [];
            container.querySelectorAll('.p-card[data-id]').forEach(function(c) { ids.push(c.dataset.id); });
            fetch("{{ route('admin.pengumuman.reorder') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ ids: ids })
            });
        }
    })();
</script>
@endpush
