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

    /* Detail & Create Modal — fullscreen */
    .detail-icon-box { width:44px; height:44px; border-radius:12px; background:var(--primary-soft); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
    .detail-title { font-size:15px; font-weight:700; color:var(--dark); }
    .detail-subtitle { font-size:11px; color:var(--gray); }
    .detail-status-badge { display:inline-block; padding:4px 12px; border-radius:8px; font-size:11px; font-weight:600; }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .detail-grid .full { grid-column:1/-1; }
    .detail-label { font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px; }
    .detail-value { font-size:14px; font-weight:500; color:var(--dark); padding:6px 0; border-bottom:1px solid var(--card-border); word-break:break-word; }
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
            <p>Belum ada pengajuan presensi</p>
        </div>
        @endforelse
    </div>

    <!-- Cuti / DL Section -->
    @if(isset($cutiList))
    <div style="font-size:14px; font-weight:700; color:var(--dark); margin:20px 0 10px;">Cuti / Dinas Luar</div>
    <div class="pengajuan-list">
        @forelse($cutiList as $c)
        @php
            $cIcon = $c->jenis === 'dinas_luar'
                ? ['cls' => 'fa-briefcase', 'color' => 'var(--primary-dark)', 'bg' => 'var(--primary-soft)']
                : ['cls' => 'fa-calendar-minus', 'color' => '#f59e0b', 'bg' => '#fef3c7'];
            $cStatus = $c->status === 'approved' ? ['bg' => 'var(--success-light)', 'color' => 'var(--success)', 'text' => 'Disetujui']
                : ($c->status === 'rejected' ? ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)', 'text' => 'Ditolak']
                : ['bg' => '#fef3c7', 'color' => '#d97706', 'text' => 'Menunggu']);
        @endphp
        <div class="pengajuan-card" style="cursor:default;">
            <div class="detail-icon-box" style="background:{{ $cIcon['bg'] }};"><i class="fas {{ $cIcon['cls'] }}" style="color:{{ $cIcon['color'] }};"></i></div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:14px; font-weight:700; color:var(--dark); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $c->label }}</div>
                <div style="font-size:11px; color:var(--gray); margin-top:2px;">
                    {{ $c->tanggal_mulai->format('d M Y') }}
                    @if($c->tanggal_mulai != $c->tanggal_selesai)
                     - {{ $c->tanggal_selesai->format('d M Y') }}
                    @endif
                    <span style="margin-left:4px; font-weight:600;">({{ $c->jumlah_hari }} hari)</span>
                </div>
                @if($c->keterangan)
                <div style="font-size:11px; color:var(--gray); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $c->keterangan }}</div>
                @endif
            </div>
            <span style="background:{{ $cStatus['bg'] }}; color:{{ $cStatus['color'] }}; padding:4px 10px; border-radius:8px; font-size:10px; font-weight:700; flex-shrink:0;">{{ $cStatus['text'] }}</span>
        </div>
        @empty
        <div class="empty-box">
            <i class="fas fa-calendar-minus"></i>
            <p>Belum ada pengajuan cuti/DL</p>
        </div>
        @endforelse
    </div>
    @endif
</div>

<!-- Floating Button -->
<button class="fab-buat" onclick="openModal()">
    <div class="fab-buat-icon"><i class="fas fa-pen-to-square"></i></div>
    <div class="fab-buat-text">Buat Pengajuan</div>
</button>

<!-- Detail Modal — Fullscreen -->
<div id="pengajuanDetailModal" style="display:none; position:fixed; inset:0; z-index:100; background:var(--card-bg);">
    <div style="display:flex; flex-direction:column; height:100%;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="detail-icon-box" id="modalPengajuanIconBox"></div>
                <div>
                    <div class="detail-title" id="modalPengajuanJenis">-</div>
                    <div class="detail-subtitle"><span class="detail-status-badge" id="modalPengajuanStatus" style="background:var(--primary-soft); color:var(--primary-dark);">-</span></div>
                </div>
            </div>
            <button onclick="closeDetailModal()" style="background:none; border:none; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; color:var(--gray); cursor:pointer;">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="flex:1; overflow-y:auto; padding:16px;">
            <!-- Bukti Preview -->
            <div id="modalBuktiPreview" style="display:none; border-radius:16px; overflow:hidden; margin-bottom:12px; background:var(--gray-light);">
                <img id="modalBuktiImg" src="" style="width:100%; display:block; object-fit:contain; max-height:300px;" alt="Bukti">
            </div>
            <div id="modalBuktiPdf" style="display:none; margin-bottom:12px;">
                <a id="modalBuktiPdfLink" href="#" target="_blank" style="display:flex; align-items:center; gap:10px; padding:14px 16px; background:var(--light); border-radius:14px; border:1px solid var(--card-border); text-decoration:none; color:var(--dark);">
                    <div style="width:44px; height:44px; border-radius:12px; background:var(--danger-light); color:var(--danger); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:600;">Lihat Bukti PDF</div>
                        <div style="font-size:11px; color:var(--gray);">Ketuk untuk membuka</div>
                    </div>
                </a>
            </div>

            <!-- Info Card -->
            <div style="background:var(--light); border-radius:14px; padding:14px 16px; border:1px solid var(--card-border);">
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
                </div>
            </div>
        </div>
        <div style="padding:12px 16px; border-top:1px solid var(--card-border); flex-shrink:0;">
            <button onclick="closeDetailModal()" style="width:100%; padding:14px; background:var(--gray-light); color:var(--dark); border:none; border-radius:14px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                <i class="fas fa-chevron-left" style="font-size:12px;"></i> Kembali
            </button>
        </div>
    </div>
</div>

<!-- Create Modal — Fullscreen -->
<div id="pengajuanModal" style="display:none; position:fixed; inset:0; z-index:100; background:var(--card-bg);">
    <div style="display:flex; flex-direction:column; height:100%;">
        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
            <button onclick="closeModal()" style="background:none; border:none; color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px; font-weight:500; -webkit-tap-highlight-color:transparent;">
                <i class="fas fa-chevron-left"></i> Batal
            </button>
            <span style="font-size:15px; font-weight:700; color:var(--dark);">Buat Pengajuan</span>
            <button type="button" id="btnKirimPengajuan" onclick="submitActiveForm()" style="background:none; border:none; color:var(--primary-dark); font-size:14px; font-weight:700; cursor:pointer; -webkit-tap-highlight-color:transparent;">
                Kirim
            </button>
        </div>

        <!-- Tabs -->
        <div style="display:flex; gap:4px; padding:12px 16px 0; flex-shrink:0;">
            <button type="button" class="pengajuan-tab active" data-tab="presensi" onclick="switchPengajuanTab('presensi')" style="flex:1; padding:10px; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; background:var(--primary-soft); color:var(--primary-dark);">
                <i class="fas fa-clock"></i> Presensi
            </button>
            <button type="button" class="pengajuan-tab" data-tab="cuti" onclick="switchPengajuanTab('cuti')" style="flex:1; padding:10px; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; background:var(--light); color:var(--gray);">
                <i class="fas fa-calendar-minus"></i> Cuti / DL
            </button>
        </div>

        <!-- Body -->
        <div style="flex:1; overflow-y:auto; padding:20px;">

            <!-- Tab Presensi -->
            <div id="tabPresensi">
                <form action="{{ route('pegawai.pengajuan.store') }}" method="POST" enctype="multipart/form-data" data-turbo="false" id="createForm">
                    @csrf
                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Jenis Pengajuan</label>
                        <select name="jenis" id="jenis" required style="width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; color:var(--dark); background:var(--card-bg); outline:none; -webkit-appearance:none; appearance:none;">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="masuk">Masuk</option>
                            <option value="pulang">Pulang</option>
                        </select>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" required style="width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; color:var(--dark); background:var(--card-bg); outline:none;">
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Waktu</label>
                        <input type="time" name="waktu" id="waktuPengajuan" required style="width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; color:var(--dark); background:var(--card-bg); outline:none;">
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Alasan</label>
                        <textarea name="alasan" id="alasan" rows="3" required placeholder="Jelaskan alasan pengajuan..." style="width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; color:var(--dark); background:var(--card-bg); outline:none; resize:none; font-family:inherit;"></textarea>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Upload Bukti <span style="color:var(--danger);">*</span></label>
                        <div id="buktiDropZone" style="border:1px dashed var(--card-border); border-radius:12px; padding:16px; text-align:center; background:var(--light);">
                            <i class="fas fa-cloud-arrow-up" style="font-size:24px; color:var(--gray); margin-bottom:8px; display:block;"></i>
                            <div style="font-size:12px; color:var(--gray); margin-bottom:10px;">Foto otomatis dikompresi. PDF maks 2MB</div>
                            <input type="file" id="buktiOriginal" accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,.tiff,.heic,.heif,.pdf" required style="font-size:12px; color:var(--dark); width:100%;">
                            <input type="file" name="bukti" id="buktiCompressed" style="display:none;">
                            <div id="buktiInfo" style="display:none; margin-top:10px; font-size:11px; color:var(--gray);"></div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tab Cuti / DL -->
            <div id="tabCuti" style="display:none;">
                <form action="{{ route('pegawai.cuti.store') }}" method="POST" enctype="multipart/form-data" data-turbo="false" id="cutiForm">
                    @csrf
                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Jenis</label>
                        <select name="jenis_cuti" required style="width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; color:var(--dark); background:var(--card-bg); outline:none; -webkit-appearance:none; appearance:none;">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="cuti_tahunan">Cuti Tahunan</option>
                            <option value="cuti_sakit">Cuti Sakit</option>
                            <option value="cuti_melahirkan">Cuti Melahirkan</option>
                            <option value="cuti_besar">Cuti Besar</option>
                            <option value="cuti_alasan_penting">Cuti Alasan Penting</option>
                            <option value="dinas_luar">Dinas Luar (DL)</option>
                        </select>
                    </div>
                    <div style="display:flex; gap:10px; margin-bottom:14px;">
                        <div style="flex:1;">
                            <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" required style="width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; color:var(--dark); background:var(--card-bg); outline:none;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" required style="width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; color:var(--dark); background:var(--card-bg); outline:none;">
                        </div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Keterangan <span style="font-weight:400; color:var(--gray);">(opsional)</span></label>
                        <textarea name="keterangan" rows="3" placeholder="Keterangan tambahan..." style="width:100%; padding:12px 14px; border:1px solid var(--card-border); border-radius:12px; font-size:14px; color:var(--dark); background:var(--card-bg); outline:none; resize:none; font-family:inherit;"></textarea>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--gray); display:block; margin-bottom:6px;">Surat Cuti / Surat Tugas <span style="color:var(--danger);">*</span></label>
                        <div style="border:1px dashed var(--card-border); border-radius:12px; padding:16px; text-align:center; background:var(--light);">
                            <i class="fas fa-file-pdf" style="font-size:24px; color:var(--danger); margin-bottom:8px; display:block;"></i>
                            <div style="font-size:12px; color:var(--gray); margin-bottom:10px;">PDF, JPG, PNG (maks 2MB)</div>
                            <input type="file" name="bukti_surat" accept=".pdf,.jpg,.jpeg,.png" required style="font-size:12px; color:var(--dark); width:100%;">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
<script>
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

    function initPengajuan() {
        document.querySelectorAll('.p-card').forEach(function(item) {
            item.onclick = function() {
                var jenis = this.dataset.pengajuanJenis;
                var status = this.dataset.pengajuanStatus;
                var icon = getIcon(jenis);
                var ss = getStatusStyle(status);
                var iconBox = document.getElementById('modalPengajuanIconBox');
                var statusBadge = document.getElementById('modalPengajuanStatus');

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
                var imgPreview = document.getElementById('modalBuktiPreview');
                var pdfPreview = document.getElementById('modalBuktiPdf');
                imgPreview.style.display = 'none';
                pdfPreview.style.display = 'none';
                if (bukti && bukti.trim()) {
                    if (bukti.match(/\.(pdf)$/i)) {
                        document.getElementById('modalBuktiPdfLink').href = bukti;
                        pdfPreview.style.display = 'block';
                    } else {
                        document.getElementById('modalBuktiImg').src = bukti;
                        imgPreview.style.display = 'block';
                    }
                }

                document.getElementById('pengajuanDetailModal').style.display = 'block';
            };
        });
    }

    function closeDetailModal() { document.getElementById('pengajuanDetailModal').style.display = 'none'; }
    function openModal() {
        document.getElementById('pengajuanModal').style.display = 'block';
        document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
        switchPengajuanTab('presensi');
    }
    function closeModal() { document.getElementById('pengajuanModal').style.display = 'none'; }

    var _activeTab = 'presensi';
    function switchPengajuanTab(tab) {
        _activeTab = tab;
        document.getElementById('tabPresensi').style.display = tab === 'presensi' ? 'block' : 'none';
        document.getElementById('tabCuti').style.display = tab === 'cuti' ? 'block' : 'none';

        // Toggle required agar hidden form tidak block submit
        document.querySelectorAll('#tabPresensi [required]').forEach(function(el) { el.required = (tab === 'presensi'); });
        document.querySelectorAll('#tabCuti [required]').forEach(function(el) { el.required = (tab === 'cuti'); });

        document.querySelectorAll('.pengajuan-tab').forEach(function(btn) {
            if (btn.dataset.tab === tab) {
                btn.style.background = 'var(--primary-soft)';
                btn.style.color = 'var(--primary-dark)';
            } else {
                btn.style.background = 'var(--light)';
                btn.style.color = 'var(--gray)';
            }
        });
    }
    function submitActiveForm() {
        var form = _activeTab === 'presensi' ? document.getElementById('createForm') : document.getElementById('cutiForm');
        if (form.requestSubmit) {
            form.requestSubmit();
        } else {
            form.submit();
        }
    }

    // Auto-compress image before upload
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(0) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function compressImage(file, maxWidth, quality) {
        return new Promise(function(resolve) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.onload = function() {
                    var w = img.width, h = img.height;
                    if (w > maxWidth) { h = Math.round(h * maxWidth / w); w = maxWidth; }
                    var canvas = document.createElement('canvas');
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    canvas.toBlob(function(blob) { resolve(blob); }, 'image/jpeg', quality);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    document.getElementById('buktiOriginal').addEventListener('change', async function() {
        var file = this.files[0];
        var info = document.getElementById('buktiInfo');
        var compressed = document.getElementById('buktiCompressed');
        if (!file) { info.style.display = 'none'; return; }

        // PDF — no compress, just pass through
        if (file.type === 'application/pdf') {
            if (file.size > 2097152) {
                info.style.display = 'block';
                info.style.color = 'var(--danger)';
                info.textContent = 'PDF terlalu besar (' + formatSize(file.size) + '). Maks 2MB.';
                compressed.value = '';
                return;
            }
            var dt = new DataTransfer();
            dt.items.add(file);
            compressed.files = dt.files;
            info.style.display = 'block';
            info.style.color = 'var(--success)';
            info.textContent = 'PDF — ' + formatSize(file.size);
            return;
        }

        // Image — auto compress
        info.style.display = 'block';
        info.style.color = 'var(--gray)';
        info.textContent = 'Mengompresi foto...';

        // Convert HEIC/HEIF to JPEG first
        var imgFile = file;
        var isHeic = /\.(heic|heif)$/i.test(file.name) || file.type === 'image/heic' || file.type === 'image/heif' || file.type === '';
        if (isHeic && typeof heic2any !== 'undefined') {
            try {
                info.textContent = 'Mengonversi HEIC...';
                var jpegBlob = await heic2any({ blob: file, toType: 'image/jpeg', quality: 0.8 });
                if (Array.isArray(jpegBlob)) jpegBlob = jpegBlob[0];
                imgFile = new File([jpegBlob], file.name.replace(/\.(heic|heif)$/i, '.jpg'), { type: 'image/jpeg' });
                info.textContent = 'Mengompresi foto...';
            } catch(he) {
                info.style.color = 'var(--danger)';
                info.innerHTML = '<strong>Format HEIC tidak dapat diproses.</strong><br>Silakan konversi ke JPG/PNG terlebih dahulu.';
                compressed.value = '';
                this.value = '';
                return;
            }
        }

        var maxW = 800;
        var quality = 0.4;

        try {
            var blob = await compressImage(imgFile, maxW, quality);
            if (blob.size > 102400) blob = await compressImage(file, 640, 0.25);
            if (blob.size > 102400) blob = await compressImage(file, 480, 0.15);
            if (blob.size > 2097152) {
                info.style.color = 'var(--danger)';
                info.innerHTML = '<strong>Foto terlalu besar</strong> (' + formatSize(blob.size) + ' setelah kompresi). Maks 2MB.<br>Silakan kompres foto terlebih dahulu sebelum diunggah.';
                compressed.value = '';
                this.value = '';
                return;
            }
            var compFile = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' });
            var dt = new DataTransfer();
            dt.items.add(compFile);
            compressed.files = dt.files;
            info.style.color = 'var(--success)';
            info.textContent = formatSize(file.size) + ' → ' + formatSize(compFile.size) + ' (terkompresi)';
        } catch(e) {
            if (file.size > 2097152) {
                info.style.color = 'var(--danger)';
                info.innerHTML = '<strong>File terlalu besar</strong> (' + formatSize(file.size) + '). Maks 2MB.<br>Silakan kompres file terlebih dahulu sebelum diunggah.';
                compressed.value = '';
                this.value = '';
                return;
            }
            var dt = new DataTransfer();
            dt.items.add(file);
            compressed.files = dt.files;
            info.style.color = 'var(--gray)';
            info.textContent = formatSize(file.size);
        }
    });

    document.addEventListener('turbo:load', initPengajuan);
    initPengajuan();
</script>
@endsection
