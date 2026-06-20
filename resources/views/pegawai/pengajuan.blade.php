@extends('layouts.pegawai')
@section('title', 'Pengajuan')

@section('content')
<style>
    .pengajuan-page { padding: 20px; padding-bottom: 100px; }

    .page-header { margin-bottom:16px; }
    .page-title { font-size:17px; font-weight:700; color:var(--dark); margin:0; }

    .fab-buat {
        position:fixed; bottom:90px; right:15px; z-index:50;
        border:none; border-radius:14px; padding:10px 14px;
        color:#fff; display:flex; align-items:center; gap:10px;
        cursor:pointer; box-shadow:0 4px 20px rgba(0,0,0,0.15);
        background:linear-gradient(135deg, var(--primary), var(--primary-dark));
        -webkit-tap-highlight-color:transparent;
    }
    .fab-buat:active { transform:scale(0.95); }
    .fab-buat-icon {
        width:36px; height:36px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:16px; flex-shrink:0; background:rgba(255,255,255,0.2);
    }
    .fab-buat-text { font-size:13px; font-weight:700; }

    .pengajuan-list { display:flex; flex-direction:column; gap:10px; }

    .p-card {
        background:var(--card-bg); border-radius:14px; padding:14px 16px;
        display:flex; align-items:center; gap:14px;
        box-shadow:0 1px 6px rgba(0,0,0,0.04); border:1px solid var(--card-border);
        cursor:pointer; -webkit-tap-highlight-color:transparent;
    }
    .p-card:active { opacity:0.85; }

    .p-icon {
        width:44px; height:44px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-size:18px; flex-shrink:0;
    }
    .p-icon-masuk { background:var(--primary-soft); color:var(--primary-dark); }
    .p-icon-pulang { background:var(--accent-light); color:var(--accent); }
    .p-icon-keduanya { background:var(--primary-soft); color:var(--primary-dark); }

    .p-body { flex:1; min-width:0; }
    .p-title { font-size:14px; font-weight:600; color:var(--dark); margin-bottom:2px; }
    .p-date { font-size:12px; color:var(--gray); margin-bottom:3px; }
    .p-alasan { font-size:11px; color:var(--gray-dark); display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden; }

    .p-status { flex-shrink:0; text-align:right; }
    .s-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:4px; }
    .s-dot-pending { background:#f59e0b; }
    .s-dot-approved { background:#10b981; }
    .s-dot-rejected { background:#ef4444; }
    .s-text { font-size:11px; font-weight:500; color:var(--gray); }

    .empty-box { text-align:center; padding:60px 20px; color:var(--gray); background:var(--card-bg); border-radius:16px; }
    .empty-box i { font-size:40px; margin-bottom:12px; opacity:0.3; display:block; }
    .empty-box p { font-size:14px; margin:0; }

    /* Detail Modal */
    .detail-modal .modal-content { background:var(--card-bg); border-radius:20px; border:none; box-shadow:0 10px 30px rgba(0,0,0,0.2); }
    .detail-modal .modal-body { padding:24px; }
    .detail-icon-box { width:56px; height:56px; border-radius:14px; background:var(--primary-soft); display:flex; align-items:center; justify-content:center; font-size:24px; margin:0 auto 12px; }
    .detail-title { font-size:16px; font-weight:700; color:var(--dark); text-align:center; margin-bottom:4px; }
    .detail-status-row { text-align:center; margin-bottom:16px; }
    .detail-status-badge { display:inline-block; padding:4px 12px; border-radius:8px; font-size:11px; font-weight:600; }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .detail-grid .full { grid-column:1/-1; }
    .detail-label { font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px; }
    .detail-value { font-size:14px; font-weight:500; color:var(--dark); padding:6px 0; border-bottom:1px solid var(--card-border); }
    .detail-close-btn { width:100%; margin-top:16px; padding:12px; background:var(--gray-light); color:var(--dark); border:none; border-radius:12px; font-weight:600; font-size:14px; cursor:pointer; }

    /* Create Modal */
    .create-overlay {
        display:none; position:fixed; z-index:100; inset:0;
        background:rgba(0,0,0,0.4); align-items:center; justify-content:center; padding:20px;
    }
    .create-overlay.active { display:flex; }
    .create-box {
        background:var(--card-bg); border-radius:20px; width:100%; max-width:450px;
        max-height:90vh; overflow-y:auto; padding:24px; position:relative;
    }
    .create-box h3 { font-size:17px; font-weight:700; color:var(--dark); text-align:center; margin-bottom:20px; }
    .create-close { position:absolute; top:16px; right:16px; background:none; border:none; font-size:20px; cursor:pointer; color:var(--gray); }
    .form-group { margin-bottom:16px; }
    .form-group label { font-size:13px; font-weight:600; color:var(--dark); display:block; margin-bottom:6px; }
    .form-group input, .form-group select, .form-group textarea {
        width:100%; border:1px solid var(--card-border); border-radius:12px;
        padding:12px 14px; font-size:14px; background:var(--card-bg); color:var(--dark); outline:none;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--primary); }
    .form-actions { display:flex; gap:10px; margin-top:20px; }
    .form-actions button {
        flex:1; padding:14px; border-radius:12px; font-size:14px; font-weight:600; cursor:pointer; border:none;
    }
    .btn-submit { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; }
    .btn-cancel { background:var(--gray-light); color:var(--dark); }
</style>

<div class="pengajuan-page">
    <div class="pengajuan-list">
        @forelse($pengajuan as $p)
        @php
            $isMasuk = $p->jenis == 'masuk';
            $isPulang = $p->jenis == 'pulang';
            $iconCls = $isPulang ? 'p-icon-pulang' : ($isMasuk ? 'p-icon-masuk' : 'p-icon-keduanya');
            $iconName = $isPulang ? 'fa-arrow-right-from-bracket' : ($isMasuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-arrow-left');
        @endphp
        <div class="p-card"
             data-pengajuan-id="{{ $p->id }}"
             data-pengajuan-jenis="{{ $p->jenis }}"
             data-pengajuan-tanggal="{{ $p->tanggal }}"
             data-pengajuan-alasan="{{ $p->alasan }}"
             data-pengajuan-bukti="{{ $p->bukti ? asset('public/storage/' . str_replace('public/', '', $p->bukti)) : '' }}"
             data-pengajuan-status="{{ $p->status }}">
            <div class="p-icon {{ $iconCls }}"><i class="fas {{ $iconName }}"></i></div>
            <div class="p-body">
                <div class="p-title">Pengajuan {{ ucfirst($p->jenis) }}</div>
                <div class="p-date">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</div>
                <div class="p-alasan">{{ $p->alasan }}</div>
            </div>
            <div class="p-status">
                <span class="s-dot s-dot-{{ $p->status }}"></span>
                <span class="s-text">{{ $p->status === 'approved' ? 'Disetujui' : ($p->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}</span>
            </div>
        </div>
        @empty
        <div class="empty-box">
            <i class="fas fa-paper-plane"></i>
            <p>Belum ada pengajuan</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Floating Button -->
<button class="fab-buat" onclick="openModal()">
    <div class="fab-buat-icon"><i class="fas fa-pen-to-square"></i></div>
    <div class="fab-buat-text">Buat Pengajuan</div>
</button>

<!-- Detail Modal -->
<div class="modal fade detail-modal" id="pengajuanDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="detail-icon-box" id="modalPengajuanIconBox"></div>
                <div class="detail-title" id="modalPengajuanJenis">-</div>
                <div class="detail-status-row">
                    <span class="detail-status-badge" id="modalPengajuanStatus" style="background:var(--primary-soft); color:var(--primary-dark);">-</span>
                </div>
                <div class="detail-grid">
                    <div>
                        <div class="detail-label">Tanggal</div>
                        <div class="detail-value" id="modalPengajuanTanggal">-</div>
                    </div>
                    <div>
                        <div class="detail-label">Jenis</div>
                        <div class="detail-value" id="modalPengajuanJenisDetail">-</div>
                    </div>
                    <div class="full">
                        <div class="detail-label">Alasan</div>
                        <div class="detail-value" id="modalPengajuanAlasan">-</div>
                    </div>
                    <div class="full">
                        <div class="detail-label">Bukti</div>
                        <div class="detail-value" id="modalPengajuanBukti"><span style="color:var(--gray)">Tidak ada bukti</span></div>
                    </div>
                </div>
                <button type="button" class="detail-close-btn" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="pengajuanModal" class="create-overlay">
    <div class="create-box">
        <button class="create-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        <h3>Buat Pengajuan Presensi</h3>
        <form action="{{ route('pegawai.pengajuan.store') }}" method="POST" enctype="multipart/form-data" data-turbo="false">
            @csrf
            <div class="form-group">
                <label for="jenis">Jenis Pengajuan</label>
                <select name="jenis" id="jenis" required>
                    <option value="">-- Pilih --</option>
                    <option value="masuk">Masuk</option>
                    <option value="pulang">Pulang</option>
                    <option value="keduanya">Keduanya</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required>
            </div>
            <div class="form-group">
                <label for="alasan">Alasan</label>
                <textarea name="alasan" id="alasan" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="bukti">Upload Bukti (jpg/png/pdf, max 2MB)</label>
                <input type="file" name="bukti" id="bukti" accept=".jpg,.jpeg,.png,.pdf,.heic,.heif">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit">Kirim Pengajuan</button>
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('turbo:load', function() {
        var items = document.querySelectorAll('.p-card');
        var detailModal = new bootstrap.Modal(document.getElementById('pengajuanDetailModal'));
        var iconBox = document.getElementById('modalPengajuanIconBox');
        var statusBadge = document.getElementById('modalPengajuanStatus');

        function getIcon(jenis) {
            if (jenis === 'masuk') return { cls: 'fa-arrow-right-to-bracket', color: 'var(--primary-dark)', bg: 'var(--primary-soft)' };
            if (jenis === 'pulang') return { cls: 'fa-arrow-right-from-bracket', color: 'var(--accent)', bg: 'var(--accent-light)' };
            return { cls: 'fa-arrow-right-arrow-left', color: 'var(--primary-dark)', bg: 'var(--primary-soft)' };
        }

        function getStatusStyle(status) {
            if (status === 'approved') return { bg: 'var(--success-light)', color: 'var(--success)' };
            if (status === 'rejected') return { bg: 'var(--danger-light)', color: 'var(--danger)' };
            return { bg: 'var(--warning-light)', color: 'var(--warning)' };
        }

        items.forEach(function(item) {
            item.addEventListener('click', function() {
                var jenis = this.dataset.pengajuanJenis;
                var status = this.dataset.pengajuanStatus;
                var icon = getIcon(jenis);
                var ss = getStatusStyle(status);

                iconBox.style.background = icon.bg;
                iconBox.innerHTML = '<i class="fas ' + icon.cls + '" style="color:' + icon.color + '"></i>';
                statusBadge.style.background = ss.bg;
                statusBadge.style.color = ss.color;
                statusBadge.textContent = status === 'approved' ? 'Disetujui' : (status === 'rejected' ? 'Ditolak' : 'Menunggu');

                document.getElementById('modalPengajuanJenis').textContent = 'Pengajuan ' + jenis.charAt(0).toUpperCase() + jenis.slice(1);
                document.getElementById('modalPengajuanTanggal').textContent = new Date(this.dataset.pengajuanTanggal).toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
                document.getElementById('modalPengajuanJenisDetail').textContent = jenis.charAt(0).toUpperCase() + jenis.slice(1);
                document.getElementById('modalPengajuanAlasan').textContent = this.dataset.pengajuanAlasan;

                var bukti = this.dataset.pengajuanBukti;
                var buktiEl = document.getElementById('modalPengajuanBukti');
                buktiEl.innerHTML = bukti && bukti.trim() ? '<a href="' + bukti + '" target="_blank" style="color:var(--primary); font-weight:600;">Lihat Bukti</a>' : '<span style="color:var(--gray)">Tidak ada bukti</span>';

                detailModal.show();
            });
        });
    });

    function openModal() {
        document.getElementById('pengajuanModal').classList.add('active');
        document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
    }
    function closeModal() {
        document.getElementById('pengajuanModal').classList.remove('active');
    }
    document.getElementById('pengajuanModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
</script>
@endsection
