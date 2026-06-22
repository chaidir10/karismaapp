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
    @if(session('success'))
    <div style="background:var(--success-light); border:1px solid var(--success); color:var(--success); padding:12px 16px; border-radius:12px; margin-bottom:14px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:var(--danger-light); border:1px solid var(--danger); color:var(--danger); padding:12px 16px; border-radius:12px; margin-bottom:14px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div style="background:var(--danger-light); border:1px solid var(--danger); color:var(--danger); padding:12px 16px; border-radius:12px; margin-bottom:14px; font-size:13px; font-weight:500;">
        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
    </div>
    @endif

    <!-- Tabs -->
    <div style="display:flex; gap:6px; margin-bottom:16px; background:rgba(0,0,0,0.03); border-radius:14px; padding:5px; border:1px solid var(--card-border); backdrop-filter:blur(10px);">
        <button type="button" class="list-tab active" data-list="presensi" onclick="switchListTab('presensi')" style="flex:1; padding:11px 14px; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; box-shadow:0 3px 10px rgba(90,182,234,0.3), inset 0 1px 1px rgba(255,255,255,0.2);">
            <i class="fas fa-clock"></i> Presensi <span style="font-size:11px; opacity:0.8;">({{ $pengajuan->count() }})</span>
        </button>
        <button type="button" class="list-tab" data-list="cuti" onclick="switchListTab('cuti')" style="flex:1; padding:11px 14px; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; background:transparent; color:var(--gray); box-shadow:none;">
            <i class="fas fa-calendar-minus"></i> Cuti/DL <span style="font-size:11px; opacity:0.7;">({{ ($cutiList ?? collect())->count() }})</span>
        </button>
    </div>

    <!-- Tab Presensi -->
    <div id="listPresensi" class="pengajuan-list">
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

    <!-- Tab Cuti / DL -->
    <div id="listCuti" class="pengajuan-list" style="display:none;">
        @forelse(($cutiList ?? collect()) as $c)
        @php
            $cIcon = $c->jenis === 'dinas_luar'
                ? ['cls' => 'fa-briefcase', 'color' => 'var(--primary-dark)', 'bg' => 'var(--primary-soft)']
                : ['cls' => 'fa-calendar-minus', 'color' => '#f59e0b', 'bg' => '#fef3c7'];
            $cStatus = $c->status === 'approved' ? ['bg' => 'var(--success-light)', 'color' => 'var(--success)', 'text' => 'Disetujui']
                : ($c->status === 'rejected' ? ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)', 'text' => 'Ditolak']
                : ['bg' => '#fef3c7', 'color' => '#d97706', 'text' => 'Menunggu']);
        @endphp
        <div class="p-card" onclick="openCutiDetail(this)"
            data-cuti-label="{{ $c->label }}"
            data-cuti-jenis="{{ $c->jenis }}"
            data-cuti-mulai="{{ $c->tanggal_mulai->translatedFormat('d F Y') }}"
            data-cuti-selesai="{{ $c->tanggal_selesai->translatedFormat('d F Y') }}"
            data-cuti-hari="{{ $c->jumlah_hari }}"
            data-cuti-keterangan="{{ $c->keterangan ?? '' }}"
            data-cuti-status="{{ $c->status }}"
            data-cuti-status-text="{{ $cStatus['text'] }}"
            data-cuti-bukti="{{ $c->bukti_surat ? asset('public/storage/' . $c->bukti_surat) : '' }}">
            <div class="p-icon" style="background:{{ $cIcon['bg'] }}; color:{{ $cIcon['color'] }};"><i class="fas {{ $cIcon['cls'] }}"></i></div>
            <div class="p-body">
                <div class="p-title">{{ $c->label }}</div>
                <div class="p-date">{{ $c->tanggal_mulai->format('d M Y') }}@if($c->tanggal_mulai != $c->tanggal_selesai) - {{ $c->tanggal_selesai->format('d M Y') }}@endif <span style="font-weight:600;">({{ $c->jumlah_hari }} hari)</span></div>
                @if($c->keterangan)<div class="p-alasan">{{ $c->keterangan }}</div>@endif
            </div>
            <div class="p-status">
                <span class="s-dot s-dot-{{ $c->status }}"></span>
                <span class="s-text">{{ $cStatus['text'] }}</span>
            </div>
        </div>
        @empty
        <div class="empty-box">
            <i class="fas fa-calendar-minus"></i>
            <p>Belum ada pengajuan cuti/DL</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Detail Cuti Modal — Fullscreen -->
<div id="cutiDetailModal" style="display:none; position:fixed; inset:0; z-index:100; background:var(--card-bg);">
    <div style="display:flex; flex-direction:column; height:100%;">
        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
            <button onclick="document.getElementById('cutiDetailModal').style.display='none'" style="background:none; border:none; color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px; font-weight:500; -webkit-tap-highlight-color:transparent;">
                <i class="fas fa-chevron-left"></i> Kembali
            </button>
            <span style="font-size:15px; font-weight:700; color:var(--dark);" id="cdmTitle">Detail Cuti</span>
            <div id="cdmStatusBadge" style="font-size:11px; font-weight:700; padding:4px 12px; border-radius:8px;">-</div>
        </div>
        <!-- Body -->
        <div style="flex:1; overflow-y:auto; padding:20px;">
            <!-- Icon + Label -->
            <div style="display:flex; align-items:center; gap:14px; margin-bottom:20px;">
                <div id="cdmIcon" style="width:52px; height:52px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;"></div>
                <div>
                    <div id="cdmLabel" style="font-size:18px; font-weight:700; color:var(--dark);"></div>
                    <div id="cdmHari" style="font-size:13px; color:var(--gray); margin-top:2px;"></div>
                </div>
            </div>

            <!-- Info Cards -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
                <div style="background:var(--light); border-radius:14px; padding:12px 14px; border:1px solid var(--card-border);">
                    <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:4px;">Mulai</div>
                    <div id="cdmMulai" style="font-size:14px; font-weight:600; color:var(--dark);"></div>
                </div>
                <div style="background:var(--light); border-radius:14px; padding:12px 14px; border:1px solid var(--card-border);">
                    <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:4px;">Selesai</div>
                    <div id="cdmSelesai" style="font-size:14px; font-weight:600; color:var(--dark);"></div>
                </div>
            </div>

            <!-- Keterangan -->
            <div id="cdmKeteranganSection" style="margin-bottom:16px;">
                <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:6px;">Keterangan</div>
                <div id="cdmKeterangan" style="font-size:14px; color:var(--dark); line-height:1.6; background:var(--light); border-radius:14px; padding:12px 14px; border:1px solid var(--card-border);"></div>
            </div>

            <!-- Bukti Surat -->
            <div id="cdmBuktiSection">
                <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:6px;">Bukti Surat</div>
                <div id="cdmBukti"></div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Button -->
<button class="fab-buat" onclick="openModal()">
    <div class="fab-buat-icon"><i class="fas fa-pen-to-square"></i></div>
    <div class="fab-buat-text">Buat Pengajuan</div>
</button>

<!-- Detail Modal Presensi — Fullscreen Modern -->
<div id="pengajuanDetailModal" style="display:none; position:fixed; inset:0; z-index:100; background:var(--card-bg);">
    <div style="display:flex; flex-direction:column; height:100%;">
        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
            <button onclick="closeDetailModal()" style="background:none; border:none; color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px; font-weight:500; -webkit-tap-highlight-color:transparent;">
                <i class="fas fa-chevron-left"></i> Kembali
            </button>
            <span style="font-size:15px; font-weight:700; color:var(--dark);" id="modalPengajuanJenis">Detail Pengajuan</span>
            <div id="modalPengajuanStatus" style="font-size:11px; font-weight:700; padding:4px 12px; border-radius:8px;">-</div>
        </div>
        <!-- Body -->
        <div style="flex:1; overflow-y:auto; padding:20px;">
            <!-- Icon + Label -->
            <div style="display:flex; align-items:center; gap:14px; margin-bottom:20px;">
                <div id="modalPengajuanIconBox" style="width:52px; height:52px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;"></div>
                <div>
                    <div id="modalPengajuanJenisLabel" style="font-size:18px; font-weight:700; color:var(--dark);"></div>
                    <div id="modalPengajuanTanggalSub" style="font-size:13px; color:var(--gray); margin-top:2px;"></div>
                </div>
            </div>

            <!-- Info Cards -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
                <div style="background:var(--light); border-radius:14px; padding:12px 14px; border:1px solid var(--card-border);">
                    <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:4px;">Tanggal</div>
                    <div id="modalPengajuanTanggal" style="font-size:14px; font-weight:600; color:var(--dark);">-</div>
                </div>
                <div style="background:var(--light); border-radius:14px; padding:12px 14px; border:1px solid var(--card-border);">
                    <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:4px;">Jenis</div>
                    <div id="modalPengajuanJenisDetail" style="font-size:14px; font-weight:600; color:var(--dark);">-</div>
                </div>
            </div>

            <!-- Alasan -->
            <div style="margin-bottom:16px;">
                <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:6px;">Alasan</div>
                <div id="modalPengajuanAlasan" style="font-size:14px; color:var(--dark); line-height:1.6; background:var(--light); border-radius:14px; padding:12px 14px; border:1px solid var(--card-border);">-</div>
            </div>

            <!-- Bukti -->
            <div id="modalBuktiSection">
                <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:6px;">Bukti</div>
                <div id="modalBuktiPreview" style="display:none; border-radius:14px; overflow:hidden; border:1px solid var(--card-border);">
                    <img id="modalBuktiImg" src="" style="width:100%; display:block; object-fit:contain; max-height:300px;" alt="Bukti">
                </div>
                <div id="modalBuktiPdf" style="display:none;">
                    <a id="modalBuktiPdfLink" href="#" target="_blank" style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:var(--light); border-radius:14px; border:1px solid var(--card-border); text-decoration:none; color:var(--dark);">
                        <div style="width:44px; height:44px; border-radius:12px; background:var(--danger-light); color:var(--danger); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <div style="font-size:14px; font-weight:600;">Lihat Bukti (PDF)</div>
                            <div style="font-size:11px; color:var(--gray);">Ketuk untuk membuka</div>
                        </div>
                        <i class="fas fa-external-link-alt" style="margin-left:auto; color:var(--gray); font-size:12px;"></i>
                    </a>
                </div>
                <div id="modalBuktiNone" style="display:none; font-size:12px; color:var(--gray); padding:12px 14px; background:var(--light); border-radius:14px; border:1px solid var(--card-border); text-align:center;">Tidak ada bukti</div>
            </div>
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
        <div style="display:flex; gap:6px; padding:14px 16px 0; flex-shrink:0;">
            <button type="button" class="pengajuan-tab active" data-tab="presensi" onclick="switchPengajuanTab('presensi')" style="flex:1; padding:11px 14px; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; box-shadow:0 3px 10px rgba(90,182,234,0.3), inset 0 1px 1px rgba(255,255,255,0.2);">
                <i class="fas fa-clock"></i> Presensi
            </button>
            <button type="button" class="pengajuan-tab" data-tab="cuti" onclick="switchPengajuanTab('cuti')" style="flex:1; padding:11px 14px; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; background:transparent; color:var(--gray); box-shadow:none;">
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
                        <div id="buktiDropZone" onclick="document.getElementById('buktiOriginal').click()" style="border:1px dashed var(--card-border); border-radius:14px; padding:20px 16px; text-align:center; background:var(--light); cursor:pointer; -webkit-tap-highlight-color:transparent;">
                            <div style="width:48px; height:48px; border-radius:14px; background:var(--primary-soft); display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
                                <i class="fas fa-cloud-arrow-up" style="font-size:20px; color:var(--primary);"></i>
                            </div>
                            <div id="buktiFileName" style="font-size:13px; font-weight:600; color:var(--dark); margin-bottom:4px;">Ketuk untuk pilih file</div>
                            <div style="font-size:11px; color:var(--gray);">Foto otomatis dikompresi &middot; PDF maks 2MB</div>
                            <input type="file" id="buktiOriginal" accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,.tiff,.heic,.heif,.pdf" required style="display:none;" onchange="updateFileName(this, 'buktiFileName')">
                            <input type="file" name="bukti" id="buktiCompressed" style="display:none;">
                            <div id="buktiInfo" style="display:none; margin-top:10px; font-size:11px; color:var(--gray);"></div>
                        </div>
                    </div>
                    <button type="submit" id="createFormSubmit" style="display:none;"></button>
                </form>
            </div>

            <!-- Tab Cuti / DL -->
            <div id="tabCuti" style="display:none;">
            </div>
        </div>

        <!-- Cuti form OUTSIDE the scrollable area to avoid display:none blocking -->
        <div id="cutiFormWrapper" style="display:none; flex:1; overflow-y:auto; padding:20px;">
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
                        <div onclick="document.getElementById('cutiFileInput').click()" style="border:1px dashed var(--card-border); border-radius:14px; padding:20px 16px; text-align:center; background:var(--light); cursor:pointer; -webkit-tap-highlight-color:transparent;">
                            <div style="width:48px; height:48px; border-radius:14px; background:var(--danger-light); display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
                                <i class="fas fa-file-arrow-up" style="font-size:20px; color:var(--danger);"></i>
                            </div>
                            <div id="cutiFileName" style="font-size:13px; font-weight:600; color:var(--dark); margin-bottom:4px;">Ketuk untuk pilih file</div>
                            <div style="font-size:11px; color:var(--gray);">PDF, JPG, PNG &middot; Maks 2MB</div>
                            <input type="file" name="bukti_surat" id="cutiFileInput" accept=".pdf,.jpg,.jpeg,.png" required style="display:none;" onchange="updateFileName(this, 'cutiFileName')">
                        </div>
                    </div>
                    <button type="submit" id="cutiFormSubmit" style="display:none;"></button>
                </form>
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
        document.querySelectorAll('#listPresensi .p-card').forEach(function(item) {
            item.onclick = function() {
                var jenis = this.dataset.pengajuanJenis;
                var status = this.dataset.pengajuanStatus;
                var icon = getIcon(jenis);
                var ss = getStatusStyle(status);

                // Icon + label
                var iconBox = document.getElementById('modalPengajuanIconBox');
                iconBox.style.background = icon.bg;
                iconBox.style.color = icon.color;
                iconBox.innerHTML = '<i class="fas ' + icon.cls + '"></i>';

                var jenisLabel = jenis.charAt(0).toUpperCase() + jenis.slice(1);
                document.getElementById('modalPengajuanJenis').textContent = 'Pengajuan ' + jenisLabel;
                document.getElementById('modalPengajuanJenisLabel').textContent = 'Pengajuan ' + jenisLabel;
                document.getElementById('modalPengajuanJenisDetail').textContent = jenisLabel;

                // Status badge
                var statusBadge = document.getElementById('modalPengajuanStatus');
                statusBadge.style.background = ss.bg;
                statusBadge.style.color = ss.color;
                statusBadge.textContent = status === 'approved' ? 'Disetujui' : (status === 'rejected' ? 'Ditolak' : 'Menunggu');

                // Tanggal
                var tgl = new Date(this.dataset.pengajuanTanggal).toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
                document.getElementById('modalPengajuanTanggal').textContent = tgl;
                document.getElementById('modalPengajuanTanggalSub').textContent = tgl;

                // Alasan
                document.getElementById('modalPengajuanAlasan').textContent = this.dataset.pengajuanAlasan;

                // Bukti
                var bukti = this.dataset.pengajuanBukti;
                var imgPreview = document.getElementById('modalBuktiPreview');
                var pdfPreview = document.getElementById('modalBuktiPdf');
                var nonePreview = document.getElementById('modalBuktiNone');
                imgPreview.style.display = 'none';
                pdfPreview.style.display = 'none';
                nonePreview.style.display = 'none';
                if (bukti && bukti.trim()) {
                    if (bukti.match(/\.(pdf)$/i)) {
                        document.getElementById('modalBuktiPdfLink').href = bukti;
                        pdfPreview.style.display = 'block';
                    } else {
                        document.getElementById('modalBuktiImg').src = bukti;
                        imgPreview.style.display = 'block';
                    }
                } else {
                    nonePreview.style.display = 'block';
                }

                document.getElementById('pengajuanDetailModal').style.display = 'block';
            };
        });
    }

    function closeDetailModal() { document.getElementById('pengajuanDetailModal').style.display = 'none'; }

    function updateFileName(input, labelId) {
        var label = document.getElementById(labelId);
        if (input.files && input.files[0]) {
            var name = input.files[0].name;
            if (name.length > 30) name = name.substring(0, 27) + '...';
            label.textContent = name;
            label.style.color = 'var(--primary-dark)';
        } else {
            label.textContent = 'Ketuk untuk pilih file';
            label.style.color = 'var(--dark)';
        }
    }

    // Cuti detail modal
    function openCutiDetail(el) {
        var d = el.dataset;
        var isDL = d.cutiJenis === 'dinas_luar';
        var iconBg = isDL ? 'var(--primary-soft)' : '#fef3c7';
        var iconColor = isDL ? 'var(--primary-dark)' : '#f59e0b';
        var iconCls = isDL ? 'fa-briefcase' : 'fa-calendar-minus';

        document.getElementById('cdmIcon').style.background = iconBg;
        document.getElementById('cdmIcon').style.color = iconColor;
        document.getElementById('cdmIcon').innerHTML = '<i class="fas ' + iconCls + '"></i>';
        document.getElementById('cdmLabel').textContent = d.cutiLabel;
        document.getElementById('cdmTitle').textContent = d.cutiLabel;
        document.getElementById('cdmHari').textContent = d.cutiHari + ' hari';
        document.getElementById('cdmMulai').textContent = d.cutiMulai;
        document.getElementById('cdmSelesai').textContent = d.cutiSelesai;

        var ket = d.cutiKeterangan;
        var ketSection = document.getElementById('cdmKeteranganSection');
        if (ket) {
            document.getElementById('cdmKeterangan').textContent = ket;
            ketSection.style.display = '';
        } else {
            ketSection.style.display = 'none';
        }

        var status = d.cutiStatus;
        var badge = document.getElementById('cdmStatusBadge');
        if (status === 'approved') { badge.style.background = 'var(--success-light)'; badge.style.color = 'var(--success)'; }
        else if (status === 'rejected') { badge.style.background = 'var(--danger-light)'; badge.style.color = 'var(--danger)'; }
        else { badge.style.background = '#fef3c7'; badge.style.color = '#d97706'; }
        badge.textContent = d.cutiStatusText;

        var buktiEl = document.getElementById('cdmBukti');
        var buktiSection = document.getElementById('cdmBuktiSection');
        var buktiUrl = d.cutiBukti;
        if (buktiUrl && buktiUrl.match(/\.pdf$/i)) {
            buktiEl.innerHTML = '<a href="' + buktiUrl + '" target="_blank" style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:var(--light); border-radius:14px; border:1px solid var(--card-border); text-decoration:none; color:var(--dark);">' +
                '<div style="width:44px;height:44px;border-radius:12px;background:var(--danger-light);color:var(--danger);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;"><i class="fas fa-file-pdf"></i></div>' +
                '<div><div style="font-size:14px;font-weight:600;">Lihat Surat (PDF)</div><div style="font-size:11px;color:var(--gray);">Ketuk untuk membuka</div></div>' +
                '<i class="fas fa-external-link-alt" style="margin-left:auto;color:var(--gray);font-size:12px;"></i></a>';
            buktiSection.style.display = '';
        } else if (buktiUrl) {
            buktiEl.innerHTML = '<img src="' + buktiUrl + '" style="width:100%;border-radius:14px;border:1px solid var(--card-border);" onerror="this.parentElement.parentElement.style.display=\'none\'">';
            buktiSection.style.display = '';
        } else {
            buktiSection.style.display = 'none';
        }

        document.getElementById('cutiDetailModal').style.display = 'block';
    }

    // List tabs with persistence
    function switchListTab(tab) {
        document.getElementById('listPresensi').style.display = tab === 'presensi' ? '' : 'none';
        document.getElementById('listCuti').style.display = tab === 'cuti' ? '' : 'none';
        document.querySelectorAll('.list-tab').forEach(function(btn) {
            if (btn.dataset.list === tab) {
                btn.style.background = 'linear-gradient(135deg,#5AB6EA,#2E97D4)';
                btn.style.color = '#fff';
                btn.style.boxShadow = '0 3px 10px rgba(90,182,234,0.3), inset 0 1px 1px rgba(255,255,255,0.2)';
            } else {
                btn.style.background = 'transparent';
                btn.style.color = 'var(--gray)';
                btn.style.boxShadow = 'none';
            }
        });
        localStorage.setItem('pengajuan-active-tab', tab);
    }

    // Restore tab on load
    (function() {
        var saved = localStorage.getItem('pengajuan-active-tab');
        if (saved === 'cuti') switchListTab('cuti');
    })();
    function openModal() {
        document.getElementById('pengajuanModal').style.display = 'block';
        document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
        switchPengajuanTab('presensi');
    }
    function closeModal() { document.getElementById('pengajuanModal').style.display = 'none'; }

    var _activeTab = 'presensi';
    function switchPengajuanTab(tab) {
        _activeTab = tab;
        // Presensi body (contains tabPresensi inside scrollable div)
        var presensiBody = document.getElementById('tabPresensi').parentElement;
        presensiBody.style.display = tab === 'presensi' ? '' : 'none';
        // Cuti form wrapper (separate scrollable div)
        document.getElementById('cutiFormWrapper').style.display = tab === 'cuti' ? '' : 'none';

        document.querySelectorAll('.pengajuan-tab').forEach(function(btn) {
            if (btn.dataset.tab === tab) {
                btn.style.background = 'linear-gradient(135deg,#5AB6EA,#2E97D4)';
                btn.style.color = '#fff';
                btn.style.boxShadow = '0 3px 10px rgba(90,182,234,0.3), inset 0 1px 1px rgba(255,255,255,0.2)';
            } else {
                btn.style.background = 'var(--light)';
                btn.style.color = 'var(--gray)';
                btn.style.boxShadow = 'none';
                btn.style.background = 'transparent';
            }
        });
    }
    function submitActiveForm() {
        if (_activeTab === 'presensi') {
            document.getElementById('createFormSubmit').click();
        } else {
            document.getElementById('cutiFormSubmit').click();
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
