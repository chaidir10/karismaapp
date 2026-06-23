@extends('layouts.pegawai')
@section('title', 'Home')

<style>
    .btn-secondary {
        background: var(--gray-light);
        color: var(--dark);
        border: none;
        border-radius: 10px;
        padding: 20px 15px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        flex: 1;
        transition: all 0.2s ease;
    }

    .btn-secondary:hover {
        background: var(--gray);
        color: var(--white);
    }

    .btn-disabled {
        background: var(--gray-light) !important;
        color: var(--gray) !important;
        cursor: not-allowed !important;
        opacity: 0.6;
    }

    .btn-disabled:hover {
        background: var(--gray-light) !important;
        color: var(--gray) !important;
    }

    /* Work Timer Card */
    .work-timer-card {
        margin: -70px 20px 15px;
        padding: 60px 14px 18px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    .work-timer-card.timer-yellow {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #fff;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.25);
    }

    .work-timer-card.timer-green {
        background: linear-gradient(135deg, #34d399, #10b981);
        color: #fff;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
    }

    .work-timer-card.timer-blue {
        background: linear-gradient(135deg, #60a5fa, #3b82f6);
        color: #fff;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.25);
    }

    .timer-icon-circle {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .timer-info {
        flex: 1;
        min-width: 0;
    }

    .timer-label {
        font-size: 11px;
        font-weight: 500;
        opacity: 0.9;
    }

    .timer-clock-text {
        font-size: 20px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        letter-spacing: 1px;
        line-height: 1.2;
    }

    .timer-badge {
        font-size: 10px;
        font-weight: 600;
        background: rgba(255,255,255,0.25);
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    /* History Section */
    .history-section { margin: 0 20px 100px; }
    .history-section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .history-section-title { font-size:16px; font-weight:700; color:var(--dark); margin:0; }
    .history-section-link { font-size:12px; font-weight:600; color:var(--primary); text-decoration:none; }

    .history-card {
        background:var(--white); border-radius:14px; padding:14px 16px; margin-bottom:10px;
        display:flex; align-items:center; gap:14px;
        box-shadow:0 1px 6px rgba(0,0,0,0.04); border:1px solid var(--gray-light);
        cursor:pointer;
    }
    .history-card:active { transform:scale(0.98); }
    .hc-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
    .hc-icon-masuk { background:var(--primary-soft); color:var(--primary-dark); }
    .hc-icon-pulang { background:var(--accent-light); color:var(--accent); }
    .hc-icon-lembur-masuk { background:var(--primary-soft); color:var(--primary-dark); }
    .hc-icon-lembur-pulang { background:var(--accent-light); color:var(--accent); }
    .hc-body { flex:1; min-width:0; }
    .hc-title-row { display:flex; align-items:center; gap:8px; margin-bottom:2px; }
    .hc-label { font-size:13px; font-weight:600; color:var(--dark); }
    .hc-tag { font-size:9px; font-weight:700; padding:2px 7px; border-radius:6px; text-transform:uppercase; letter-spacing:0.5px; }
    .hc-tag-success { background:var(--success-light); color:var(--success); }
    .hc-tag-danger { background:var(--danger-light); color:var(--danger); }
    .hc-tag-warning { background:var(--warning-light); color:var(--warning); }
    .hc-time { font-size:18px; font-weight:800; color:var(--dark); font-variant-numeric:tabular-nums; line-height:1.2; }
    .hc-right { flex-shrink:0; }
    .hc-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:4px; }
    .hc-dot-approved { background:#10b981; }
    .hc-dot-pending { background:#f59e0b; }
    .hc-dot-rejected { background:#ef4444; }
    .hc-status { font-size:11px; color:var(--gray); font-weight:500; }
    .history-empty { text-align:center; padding:40px 20px; color:var(--gray); background:var(--white); border-radius:14px; }
    .history-empty i { font-size:32px; opacity:0.25; display:block; margin-bottom:10px; }
    .history-empty p { font-size:13px; margin:0; }

    /* Carousel */
    .info-carousel { margin:0 0 20px; position:relative; overflow:hidden; }
    .carousel-track { display:flex; will-change:transform; user-select:none; -webkit-user-select:none; }
    .carousel-slide { min-width:100%; cursor:pointer; padding:0 20px; box-sizing:border-box; }

    .slide-content {
        border-radius:16px; padding:16px; display:flex; gap:14px; align-items:center;
        color:#fff; box-shadow:0 2px 12px rgba(0,0,0,0.06);
    }
    .slide-icon {
        width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center;
        font-size:18px; flex-shrink:0; background:rgba(255,255,255,0.2); color:#fff;
    }
    .slide-body { flex:1; min-width:0; }
    .slide-tag-row { display:flex; align-items:center; gap:8px; margin-bottom:4px; flex-wrap:wrap; }
    .slide-tag { font-size:9px; font-weight:700; padding:3px 8px; border-radius:6px; text-transform:uppercase; letter-spacing:0.3px; background:rgba(255,255,255,0.2); color:#fff; }
    .slide-date { font-size:10px; color:rgba(255,255,255,0.7); }
    .slide-title { font-size:14px; font-weight:700; color:#fff; margin-bottom:3px; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .slide-desc { font-size:12px; color:rgba(255,255,255,0.8); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .slide-link { font-size:11px; font-weight:600; margin-top:6px; color:rgba(255,255,255,0.9); }

    .slide-image {
        width:100%; height:140px; border-radius:16px; background-size:cover; background-position:center;
        position:relative; overflow:hidden;
        box-shadow:0 2px 12px rgba(0,0,0,0.06);
    }

    .carousel-dots { display:flex; justify-content:center; gap:6px; margin-top:10px; }
    .dot { width:7px; height:7px; border-radius:50%; background:var(--gray-light); cursor:pointer; transition:all 0.2s; }
    .dot.active { background:var(--primary); width:20px; border-radius:4px; }

    /* Quill content styling */
    .ql-content {
        font-size:14px; line-height:1.8; color:var(--dark);
        word-wrap:break-word; overflow-wrap:break-word; word-break:break-word;
    }
    .ql-content a {
        color:var(--primary); text-decoration:underline;
        word-break:break-all;
    }
    .ql-content ul, .ql-content ol {
        padding-left:20px; margin:8px 0;
    }
    .ql-content ul { list-style:disc; }
    .ql-content ol { list-style:decimal; }
    .ql-content li { margin-bottom:4px; padding-left:4px; }
    .ql-content p { margin-bottom:8px; }
    .ql-content h1, .ql-content h2, .ql-content h3 {
        font-weight:700; color:var(--dark); margin:12px 0 6px;
    }
    .ql-content h1 { font-size:20px; }
    .ql-content h2 { font-size:17px; }
    .ql-content h3 { font-size:15px; }
    .ql-content blockquote {
        border-left:3px solid var(--primary);
        padding:6px 12px; margin:8px 0;
        color:var(--gray); font-style:italic;
    }
    .ql-content img { max-width:100%; border-radius:8px; margin:8px 0; }
    .ql-content strong { font-weight:700; }
    .ql-content em { font-style:italic; }

    .badge-baru {
        position:absolute; top:8px; right:8px; z-index:2;
        background:var(--accent); color:#fff;
        font-size:9px; font-weight:700;
        padding:4px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px;
        animation:baruBlink 1s ease-in-out infinite;
    }
    @keyframes baruBlink {
        0%, 100% { opacity:1; transform:scale(1); }
        50% { opacity:0.5; transform:scale(0.92); }
    }

    .ql-content img { max-width:100%; border-radius:8px; margin:10px 0; }
    .ql-content { text-align:justify; color:var(--dark); }
    [data-theme="dark"] .ql-content, [data-theme="dark"] .ql-content * { color:var(--dark) !important; }
</style>

@section('content')

<!-- Card Absensi -->
<div class="attendance-card" style="padding:0; overflow:hidden; border:none; box-shadow:0 8px 30px rgba(0,0,0,0.08);">
    <!-- Info bar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; background:var(--white);">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:44px; height:44px; border-radius:12px; background:var(--primary-soft); display:flex; align-items:center; justify-content:center; color:var(--primary-dark); font-size:18px;">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <div style="font-size:11px; color:var(--gray); font-weight:500;">{{ now()->translatedFormat('l') }}</div>
                <div style="font-size:15px; font-weight:700; color:var(--dark); line-height:1.2;">{{ now()->translatedFormat('d F Y') }}</div>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:10px; color:var(--gray); font-weight:500;">Jadwal</div>
            @if($isLiburHariIni ?? false)
                <div style="font-size:13px; font-weight:700; color:var(--accent);">{{ $namaLibur }}</div>
            @elseif($shiftHariIni ?? false)
                <div style="font-size:13px; font-weight:700; color:var(--primary-dark);">{{ \Carbon\Carbon::parse($shiftHariIni->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($shiftHariIni->jam_pulang)->format('H:i') }}</div>
                <div style="font-size:9px; color:var(--primary); font-weight:600; margin-top:1px;">{{ $shiftHariIni->nama }}</div>
            @else
                <div style="font-size:13px; font-weight:700; color:var(--primary-dark);">{{ \Carbon\Carbon::parse($jadwalKerjaHariIni['jam_masuk'])->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalKerjaHariIni['jam_pulang'])->format('H:i') }}</div>
            @endif
        </div>
    </div>

    <!-- Divider -->
    <div style="height:1px; background:var(--gray-light); margin:0 20px;"></div>

    <!-- Buttons -->
    @php
        $masukDisabled = $sudahPresensiMasuk || ($disablePresensiLibur ?? false);
        $pulangDisabled = $sudahPresensiPulang || ($disablePresensiLibur ?? false);
    @endphp
    <div style="display:flex; gap:10px; padding:16px 20px;">
        <button class="{{ !$masukDisabled ? 'absen-btn-active' : '' }}"
            id="clock-in-btn"
            style="flex:1; height:60px; border-radius:16px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; font-size:15px; font-weight:700;
            {{ $masukDisabled ? 'background:var(--gray-light); color:var(--gray); opacity:0.6; cursor:not-allowed;' : 'background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; box-shadow:0 4px 14px rgba(90,182,234,0.3);' }}"
            @if(!$masukDisabled)
                @if($user->can_shift && $shifts->count() > 0)
                    onclick="setJenis('masuk'); setLembur(false); openSimpleModal('shiftPickerModal')"
                @else
                    data-bs-toggle="modal" data-bs-target="#presensiModal"
                    onclick="setJenis('masuk'); setLembur(false)"
                @endif
            @endif
            {{ $masukDisabled ? 'disabled' : '' }}>
            <i class="fas {{ $sudahPresensiMasuk ? 'fa-check-circle' : 'fa-arrow-right-to-bracket' }}" style="font-size:20px;"></i>
            @if($disablePresensiLibur ?? false)
                Libur
            @else
                {{ $sudahPresensiMasuk ? 'Sudah Masuk' : 'Masuk' }}
            @endif
        </button>
        <button class="{{ !$pulangDisabled ? 'absen-btn-active' : '' }}"
            id="clock-out-btn"
            style="flex:1; height:60px; border-radius:16px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; font-size:15px; font-weight:700;
            {{ $pulangDisabled ? 'background:var(--gray-light); color:var(--gray); opacity:0.6; cursor:not-allowed;' : 'background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; box-shadow:0 4px 14px rgba(245,158,11,0.3);' }}"
            @if(!$pulangDisabled) onclick="handlePulangWithCheck()" @endif
            {{ $pulangDisabled ? 'disabled' : '' }}>
            <i class="fas {{ $sudahPresensiPulang ? 'fa-check-circle' : 'fa-arrow-right-from-bracket' }}" style="font-size:20px;"></i>
            @if($disablePresensiLibur ?? false)
                Libur
            @else
                {{ $sudahPresensiPulang ? 'Sudah Pulang' : 'Pulang' }}
            @endif
        </button>
    </div>
</div>

{{-- Timer Jam Kerja --}}
@if($sudahPresensiMasuk && $jamMasukHariIni)
@php
    $pulangRec = $riwayatHariIni->where('jenis', 'pulang')->where('is_lembur', false)->first();
@endphp
<div class="work-timer-card {{ $pulangRec ? 'timer-blue' : 'timer-yellow' }}" id="workTimerBanner"
    data-stopped="{{ $pulangRec ? '1' : '0' }}"
    data-pulang-jam="{{ $pulangRec->jam ?? '' }}">
    <div class="timer-icon-circle">
        <i class="fas {{ $pulangRec ? 'fa-check' : 'fa-stopwatch' }}"></i>
    </div>
    <div class="timer-info">
        <div class="timer-label" id="workTimerLabel">{{ $pulangRec ? 'Total jam kerja hari ini' : 'Jam kerja berjalan' }}</div>
        <div class="timer-clock-text" id="workTimerClock">00:00:00</div>
    </div>
    <div class="timer-badge" id="workTimerBadge">{{ $pulangRec ? 'Selesai' : '...' }}</div>
</div>
@endif

{{-- Carousel Pengumuman --}}
@if(isset($pengumumans) && $pengumumans->count() > 0)
<div class="info-carousel" id="infoCarousel">
    <div class="carousel-track" id="carouselTrack">
        @foreach($pengumumans as $pm)
        @php
            $pmOpt = \App\Models\Pengumuman::jenisOptions()[$pm->jenis] ?? ['icon'=>'fa-circle-info','color'=>'#64748b','label'=>$pm->jenis,'gradient'=>['#64748b','#475569']];
            $g1 = $pmOpt['gradient'][0]; $g2 = $pmOpt['gradient'][1];
        @endphp
        <div class="carousel-slide" onclick="openInfoModal({{ $pm->id }})" style="position:relative;">
            @if($pm->created_at->diffInHours(now()) < 42)
            <span class="badge-baru" id="badgeBaru{{ $pm->id }}">Baru</span>
            @endif
            @if($pm->gambar && ($pm->sembunyikan_detail ?? false))
            <div class="slide-image" style="background-image:url('{{ asset('public/storage/'.$pm->gambar) }}');"></div>
            @elseif($pm->gambar)
            <div class="slide-image" style="background-image:url('{{ asset('public/storage/'.$pm->gambar) }}');">
                <div style="position:absolute; top:10px; left:10px;">
                    <span class="slide-tag" style="background:{{ $g1 }}; opacity:0.8;">{{ $pmOpt['label'] }}</span>
                </div>
            </div>
            @else
            <div class="slide-content" style="background:linear-gradient(135deg, {{ $g1 }}, {{ $g2 }});">
                <div class="slide-icon">
                    <i class="fas {{ $pmOpt['icon'] }}"></i>
                </div>
                <div class="slide-body">
                    <div class="slide-tag-row">
                        <span class="slide-tag">{{ $pmOpt['label'] }}</span>
                        @if($pm->tanggal_mulai)
                        <span class="slide-date">{{ $pm->tanggal_mulai->format('d M Y') }}@if($pm->tanggal_selesai && $pm->tanggal_selesai != $pm->tanggal_mulai) - {{ $pm->tanggal_selesai->format('d M Y') }}@endif</span>
                        @endif
                    </div>
                    <div class="slide-title">{{ $pm->judul }}</div>
                    <div class="slide-desc">{!! \Illuminate\Support\Str::limit(strip_tags($pm->isi), 80) !!}</div>
                    <div class="slide-link">Baca selengkapnya <i class="fas fa-chevron-right" style="font-size:9px"></i></div>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @if($pengumumans->count() > 1)
    <div class="carousel-dots" id="carouselDots">
        @foreach($pengumumans as $i => $pm)
        <span class="dot {{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></span>
        @endforeach
    </div>
    @endif
</div>

{{-- Modal Info Detail --}}
@foreach($pengumumans as $pm)
<div id="infoModal{{ $pm->id }}" style="display:none; position:fixed; inset:0; z-index:100; background:var(--card-bg);">
    <div style="display:flex; flex-direction:column; height:100%;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
            <button onclick="closeInfoModal({{ $pm->id }})" style="background:none; border:none; color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px; font-weight:500; -webkit-tap-highlight-color:transparent;">
                <i class="fas fa-chevron-left"></i> Kembali
            </button>
            <span style="font-size:13px; font-weight:600; color:var(--gray);">{{ \App\Models\Pengumuman::jenisOptions()[$pm->jenis]['label'] ?? 'Info' }}</span>
            <div style="width:60px;"></div>
        </div>
        <div style="flex:1; overflow-y:auto;">
            @if($pm->gambar)
            <img src="{{ asset('public/storage/'.$pm->gambar) }}" style="width:100%; object-fit:cover; max-height:220px;" alt="">
            @endif
            <div style="padding:20px;">
                <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; margin-bottom:12px;">
                    <span style="background:{{ \App\Models\Pengumuman::jenisOptions()[$pm->jenis]['color'] ?? '#64748b' }}; color:#fff; padding:4px 12px; border-radius:8px; font-weight:600; font-size:11px;">{{ \App\Models\Pengumuman::jenisOptions()[$pm->jenis]['label'] ?? $pm->jenis }}</span>
                    @if($pm->tanggal_mulai)
                    <span style="font-size:12px; color:var(--gray);"><i class="far fa-calendar-alt" style="margin-right:4px;"></i>{{ $pm->tanggal_mulai->translatedFormat('d F Y') }}@if($pm->tanggal_selesai && $pm->tanggal_selesai != $pm->tanggal_mulai) - {{ $pm->tanggal_selesai->translatedFormat('d F Y') }}@endif</span>
                    @endif
                    @if($pm->waktu)
                    <span style="font-size:12px; color:var(--gray);"><i class="far fa-clock" style="margin-right:4px;"></i>{{ \Carbon\Carbon::parse($pm->waktu)->format('H:i') }}</span>
                    @endif
                </div>
                <h2 style="font-size:20px; font-weight:800; color:var(--dark); margin-bottom:16px; line-height:1.3;">{{ $pm->judul }}</h2>
                <div class="ql-content">{!! $pm->isi !!}</div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

{{-- Floating Lembur Button --}}
@if(!$sudahLemburMasuk)
<button class="lembur-fab lembur-idle" data-bs-toggle="modal" data-bs-target="#presensiModal"
    onclick="setJenis('masuk'); setLembur(true)">
    <div class="lembur-fab-icon"><i class="fas fa-bolt"></i></div>
    <div class="lembur-fab-text">Mulai Lembur</div>
</button>
@elseif(!$sudahLemburPulang)
@php
    $lemburMasukRecord = \App\Models\Presensi::where('user_id', Auth::id())
        ->where('tanggal', now()->format('Y-m-d'))
        ->where('jenis', 'masuk')->where('is_lembur', true)->first();
@endphp
<button class="lembur-fab lembur-active" onclick="openLemburConfirm()">
    <div class="lembur-fab-icon pulse"><i class="fas fa-bolt"></i></div>
    <div class="lembur-fab-text">
        <span class="lembur-fab-label">Selesai Lembur</span>
        <span class="lembur-fab-timer" id="lemburTimer" data-start="{{ $lemburMasukRecord->jam ?? '' }}">00:00:00</span>
    </div>
</button>
@else
<div class="lembur-fab lembur-done">
    <div class="lembur-fab-icon"><i class="fas fa-check"></i></div>
    <div class="lembur-fab-text">Lembur Selesai</div>
</div>
@endif

<!-- Riwayat Hari Ini -->
<div class="history-section">
    <div class="history-section-header">
        <h5 class="history-section-title">Riwayat Hari Ini</h5>
        <a href="{{ route('pegawai.riwayat') }}" class="history-section-link">Lihat Semua <i class="fas fa-chevron-right" style="font-size:10px"></i></a>
    </div>
    @forelse($riwayatHariIni as $p)
    @php
        $isLembur = $p->is_lembur;
        $isMasuk = $p->jenis === 'masuk';
        $iconCls = $isLembur
            ? ($isMasuk ? 'hc-icon-lembur-masuk' : 'hc-icon-lembur-pulang')
            : ($isMasuk ? 'hc-icon-masuk' : 'hc-icon-pulang');
        $iconName = $isLembur ? 'fa-bolt' : ($isMasuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket');
        $labelText = ($isLembur ? 'Lembur ' : '') . ($isMasuk ? 'Masuk' : 'Pulang');
    @endphp
    <div class="history-card" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
        <div class="hc-icon {{ $iconCls }}"><i class="fas {{ $iconName }}"></i></div>
        <div class="hc-body">
            <span class="hc-label">{{ $labelText }}</span>
            <div class="hc-time">{{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</div>
        </div>
        <div class="hc-right">
            @if($p->status === 'pending')
                <span class="hc-dot hc-dot-pending"></span>
            @elseif($p->badge_text)
                <span class="hc-tag hc-tag-{{ $p->badge_type }}">{{ $p->badge_text }}</span>
            @endif
        </div>
    </div>
    @empty
    <div class="history-empty">
        <i class="fas fa-clock"></i>
        <p>Belum ada riwayat hari ini</p>
    </div>
    @endforelse
</div>

<!-- Modal Detail -->
@foreach($riwayatHariIni as $p)
@php
    $dIsMasuk = $p->jenis === 'masuk';
    $dIsLembur = $p->is_lembur;
    $dIconBg = $dIsMasuk ? 'var(--primary-soft)' : 'var(--accent-light)';
    $dIconColor = $dIsMasuk ? 'var(--primary-dark)' : 'var(--accent)';
    $dIconName = $dIsLembur ? 'fa-bolt' : ($dIsMasuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket');
@endphp
<div class="modal fade" id="detailModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-mobile" style="margin:0; max-width:none; width:100%; height:100%;">
        <div class="modal-content" style="border-radius:0; border:none; height:100vh; background:var(--card-bg); display:flex; flex-direction:column;">
            <!-- Header -->
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:{{ $dIconBg }}; display:flex; align-items:center; justify-content:center; color:{{ $dIconColor }}; font-size:16px;">
                        <i class="fas {{ $dIconName }}"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:700; color:var(--dark);">{{ $dIsLembur ? 'Lembur ' : '' }}{{ ucfirst($p->jenis) }}</div>
                        <div style="font-size:11px; color:var(--gray);">{{ \Carbon\Carbon::parse($p->jam)->format('H:i') }} &middot; {{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') }}</div>
                    </div>
                </div>
                <button type="button" data-bs-dismiss="modal" style="background:none; border:none; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; color:var(--gray); cursor:pointer;">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div style="flex:1; overflow-y:auto; padding:16px;">
                <!-- Foto -->
                <div style="border-radius:16px; overflow:hidden; margin-bottom:12px; aspect-ratio:4/3; background:var(--gray-light);">
                    @if($p->foto)
                    <img src="{{ asset('public/storage/'.$p->foto) }}" style="width:100%; height:100%; object-fit:cover; display:block;" alt="Foto">
                    @else
                    <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; color:var(--gray); gap:8px;">
                        <i class="fas fa-camera" style="font-size:28px; opacity:0.3;"></i>
                        <span style="font-size:12px;">Tidak ada foto</span>
                    </div>
                    @endif
                </div>

                <!-- Maps -->
                <div style="border-radius:16px; overflow:hidden; margin-bottom:12px; height:180px; background:var(--gray-light);">
                    @if($p->lokasi)
                    <div id="mapDetail{{ $p->id }}" style="width:100%; height:100%;"></div>
                    @else
                    <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; color:var(--gray); gap:8px;">
                        <i class="fas fa-location-dot" style="font-size:28px; opacity:0.3;"></i>
                        <span style="font-size:12px;">Lokasi tidak tersedia</span>
                    </div>
                    @endif
                </div>

                <!-- Info Card -->
                <div style="background:var(--light); border-radius:14px; padding:14px 16px; border:1px solid var(--card-border);">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                        <div style="width:44px; height:44px; border-radius:12px; background:{{ $dIconBg }}; display:flex; align-items:center; justify-content:center; color:{{ $dIconColor }}; font-size:18px; flex-shrink:0;">
                            <i class="fas {{ $dIconName }}"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:15px; font-weight:700; color:var(--dark);">{{ $dIsLembur ? 'Lembur ' : '' }}{{ ucfirst($p->jenis) }} - {{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</div>
                            <div style="font-size:12px; color:var(--gray);">{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('l, d F Y') }}</div>
                        </div>
                    </div>

                    <div style="display:flex; gap:10px; margin-bottom:10px;">
                        <div style="flex:1; background:var(--card-bg); border-radius:10px; padding:10px 12px; border:1px solid var(--card-border);">
                            <div style="font-size:9px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Status</div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:{{ $p->status == 'approved' ? '#10b981' : ($p->status == 'pending' ? '#f59e0b' : '#ef4444') }};"></span>
                                <span style="font-size:13px; font-weight:600; color:var(--dark);">{{ $p->status === 'approved' ? 'Disetujui' : ($p->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}</span>
                            </div>
                        </div>
                        <div style="flex:1; background:var(--card-bg); border-radius:10px; padding:10px 12px; border:1px solid var(--card-border);">
                            <div style="font-size:9px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Jenis</div>
                            <div style="font-size:13px; font-weight:600; color:var(--dark);">{{ $dIsLembur ? 'Lembur' : 'Reguler' }}</div>
                        </div>
                    </div>

                    <div style="background:var(--card-bg); border-radius:10px; padding:10px 12px; border:1px solid var(--card-border);">
                        <div style="font-size:9px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Lokasi</div>
                        <div style="font-size:12px; color:var(--dark); line-height:1.4;" id="locationAddress{{ $p->id }}">
                            @if($p->lokasi)
                            <div style="display:flex; align-items:center; gap:6px; color:var(--gray);"><i class="fas fa-spinner fa-spin" style="font-size:11px;"></i> <span>Mendeteksi alamat...</span></div>
                            @else
                            <span style="color:var(--gray);">Tidak tersedia</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Button -->
            <div style="padding:12px 16px; border-top:1px solid var(--card-border); flex-shrink:0;">
                <button type="button" data-bs-dismiss="modal" style="width:100%; padding:14px; background:var(--gray-light); color:var(--dark); border:none; border-radius:14px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <i class="fas fa-chevron-left" style="font-size:12px;"></i> Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Peringatan Jam Kerja Belum Terpenuhi --}}
<div id="earlyPulangModal" style="display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)closeSimpleModal('earlyPulangModal')">
    <div style="background:var(--card-bg); border-radius:20px; padding:24px; width:90%; max-width:340px; text-align:center;">
        <div style="width:56px;height:56px;background:var(--warning-light);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <i class="fas fa-exclamation-triangle" style="font-size:24px;color:var(--warning);"></i>
        </div>
        <h5 style="font-weight:700; font-size:16px; margin-bottom:8px; color:var(--dark);">Jam Kerja Belum Terpenuhi</h5>
        <p style="font-size:13px; color:var(--gray); margin-bottom:16px;" id="earlyPulangMsg">Apakah Anda yakin ingin absen pulang?</p>
        <div style="display:flex; gap:10px;">
            <button onclick="closeSimpleModal('earlyPulangModal')" style="flex:1; padding:12px; border-radius:12px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-weight:600; font-size:14px; cursor:pointer;">Batal</button>
            <button onclick="proceedPulang()" style="flex:1; padding:12px; border:none; border-radius:12px; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">Ya, Pulang</button>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Selesai Lembur --}}
<div id="confirmLemburModal" style="display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)closeLemburConfirm()">
    <div style="background:var(--card-bg); border-radius:20px; padding:24px; width:90%; max-width:340px; text-align:center;">
        <div style="width:56px;height:56px;background:var(--success-light);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <i class="fas fa-bolt" style="font-size:24px;color:var(--success);"></i>
        </div>
        <h5 style="font-weight:700; font-size:16px; margin-bottom:8px; color:var(--dark);">Selesai Lembur</h5>
        <p style="font-size:13px; color:var(--gray); margin-bottom:16px;">Yakin ingin mengakhiri lembur?</p>
        <div style="display:flex; gap:10px;">
            <button onclick="closeLemburConfirm()" style="flex:1; padding:12px; border-radius:12px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-weight:600; font-size:14px; cursor:pointer;">Batal</button>
            <button onclick="proceedSelesaiLembur()" style="flex:1; padding:12px; border:none; border-radius:12px; background:linear-gradient(135deg,#10b981,#059669); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">Ya, Selesai</button>
        </div>
    </div>
</div>

{{-- Modal Pilih Shift --}}
@if($user->can_shift && $shifts->count() > 0)
<div id="shiftPickerModal" style="display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)closeSimpleModal('shiftPickerModal')">
    <div style="background:var(--card-bg); border-radius:20px; padding:24px; width:90%; max-width:380px;">
                <h5 style="font-weight:700; font-size:16px; text-align:center; margin-bottom:16px; color:var(--dark);">Pilih Jadwal Kerja</h5>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <button class="shift-pick-btn" onclick="pickShift('')"
                        style="padding:14px; border-radius:12px; border:2px solid var(--gray-light); background:var(--white); font-size:14px; font-weight:500; cursor:pointer; text-align:left; transition:all 0.2s;">
                        <div style="font-weight:600;">Jam Kerja Normal</div>
                        <div style="font-size:12px; color:var(--gray); margin-top:2px;">07:30 - 16:00</div>
                    </button>
                    @foreach($shifts as $s)
                    <button class="shift-pick-btn" onclick="pickShift('{{ $s->id }}')"
                        style="padding:14px; border-radius:12px; border:2px solid var(--primary); background:rgba(90,182,234,0.05); font-size:14px; font-weight:500; cursor:pointer; text-align:left; transition:all 0.2s;">
                        <div style="font-weight:600; color:var(--primary-dark);">{{ $s->nama }}</div>
                        <div style="font-size:12px; color:var(--gray); margin-top:2px;">{{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }}</div>
                    </button>
                    @endforeach
                </div>
    </div>
</div>
@endif

<!-- Modal Presensi -->
<div class="modal fade" id="presensiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-mobile" style="margin:0; max-width:none; width:100%; height:100%;">
        <div class="modal-content" style="border:none; border-radius:0; height:100vh; background:#000; display:flex; flex-direction:column; overflow:hidden;">
            <form id="formPresensi" method="POST" action="{{ route('pegawai.presensi.store') }}" enctype="multipart/form-data" data-turbo="false" style="display:flex; flex-direction:column; height:100%;">
                @csrf
                <input type="hidden" name="jenis" id="jenisPresensi">
                <input type="hidden" name="foto" id="fotoInput">
                <input type="hidden" name="lokasi" id="lokasiInput">
                <input type="hidden" name="is_lembur" id="isLemburInput" value="0">
                <input type="hidden" name="jam_shift_id" id="jamShiftIdInput" value="">

                <!-- Camera Area -->
                <div style="flex:1; position:relative; overflow:hidden; background:#000;">
                    <video id="video" autoplay playsinline style="width:100%; height:100%; object-fit:cover;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>

                    <!-- Close button -->
                    <button type="button" data-bs-dismiss="modal" style="position:absolute; top:12px; left:12px; z-index:20; width:40px; height:40px; border-radius:50%; background:rgba(0,0,0,0.4); border:none; color:#fff; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-xmark"></i>
                    </button>

                    <!-- Face guide -->
                    <div class="face-guide">
                        <div class="face-guide-oval" id="faceGuideOval"></div>
                    </div>

                    <!-- Face status -->
                    <div id="faceStatus" class="face-status no-face">
                        <i class="fas fa-user-slash"></i> Wajah tidak terdeteksi
                    </div>
                </div>

                <!-- Bottom Panel -->
                <div style="background:var(--card-bg); flex-shrink:0; border-top-left-radius:20px; border-top-right-radius:20px; margin-top:-20px; position:relative; z-index:10; padding:16px 20px 24px;">
                    <!-- Location -->
                    <div style="border-radius:14px; overflow:hidden; border:1px solid var(--card-border);">
                        <div id="mini-map" style="width:100%; height:100px; background:var(--gray-light);"></div>
                        <div style="padding:10px 14px; background:var(--light); text-align:center;">
                            <div id="location-address-mini" style="font-size:12px; color:var(--dark); font-weight:500; line-height:1.4;">Mendeteksi lokasi...</div>
                            <div id="locationRadiusInfo" style="font-size:10px; margin-top:4px;"></div>
                        </div>
                    </div>
                
                    <!-- Submit button (atas, mudah dijangkau) -->
                    <button type="button" class="submit-btn-large" onclick="captureAndProcess()" style="width:100%; max-width:none; border-radius:14px; padding:16px; font-size:15px; box-shadow:0 4px 14px rgba(90,182,234,0.3); margin-bottom:14px;">
                        <i class="fas fa-camera" style="margin-right:8px;"></i> Ambil Foto & Absen
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Presensi (Luar Radius) -->
<div id="confirmationModal" style="display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)closeSimpleModal('confirmationModal')">
    <div style="background:var(--card-bg); border-radius:20px; width:90%; max-width:380px; overflow:hidden;">
        <div style="padding:24px 24px 0; text-align:center;">
            <div style="width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg,#f59e0b,#d97706); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fas fa-location-dot" style="font-size:22px; color:#fff;"></i>
            </div>
            <h5 style="font-weight:700; font-size:17px; color:var(--dark); margin-bottom:6px;">Di Luar Wilayah Kerja</h5>
            <p style="font-size:13px; color:var(--gray); margin-bottom:16px; line-height:1.5;">Presensi di luar radius memerlukan persetujuan admin</p>
            <div style="background:var(--light); border-radius:14px; padding:14px; text-align:left; margin-bottom:16px; border:1px solid var(--card-border);">
                <div style="display:flex; gap:16px; margin-bottom:10px;">
                    <div style="flex:1;">
                        <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Jenis</div>
                        <div id="confirmationJenis" style="font-size:14px; font-weight:700; color:var(--dark);"></div>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Waktu</div>
                        <div id="confirmationWaktu" style="font-size:14px; font-weight:700; color:var(--dark);"></div>
                    </div>
                </div>
                <div>
                    <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Lokasi</div>
                    <div id="confirmationLokasi" style="font-size:12px; color:var(--gray-dark); line-height:1.4;"></div>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:10px; padding:0 24px 24px;">
            <button type="button" onclick="closeSimpleModal('confirmationModal')" style="flex:1; padding:14px; border-radius:14px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-weight:600; font-size:14px; cursor:pointer;">Batal</button>
            <button type="button" onclick="prosesPresensi()" id="confirmPresensiBtn" style="flex:1; padding:14px; border-radius:14px; border:none; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">Ya, Presensi</button>
        </div>
    </div>
</div>

<!-- Modal Presensi Berhasil (Dalam Radius - Auto Close) -->
<!-- <div class="modal fade" id="successConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl auto-close-modal">
            <div class="modal-body p-0">
                <div class="text-center p-6">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Presensi Berhasil</h3>
                    <p class="text-gray-600 mb-4">Presensi Anda berhasil dicatat.</p>
                    
                    <div class="confirmation-details bg-gray-50 rounded-xl p-4 mb-4">
                        <div class="grid grid-cols-2 gap-4 text-left">
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Jenis Presensi</div>
                                <div id="successConfirmationJenis" class="font-semibold text-gray-800"></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Waktu</div>
                                <div id="successConfirmationWaktu" class="font-semibold text-gray-800"></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-xs text-gray-500 mb-1">Lokasi Saat Ini</div>
                            <div id="successConfirmationLokasi" class="font-semibold text-gray-800 text-sm"></div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-3">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                                <div class="text-xs text-green-700">
                                    <strong>Lokasi Valid:</strong> Anda berada di dalam radius wilayah kerja.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200">
                    <button type="button" class="w-full py-4 bg-green-500 text-white font-medium hover:bg-green-600 rounded-b-2xl transition-colors" data-bs-dismiss="modal">
                        <i class="fas fa-check mr-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- Modal Peringatan Belum Presensi Masuk -->
<div id="warningModal" style="display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)closeSimpleModal('warningModal')">
    <div style="background:var(--card-bg); border-radius:20px; padding:24px; width:90%; max-width:340px; text-align:center;">
        <div style="width:56px;height:56px;background:var(--warning-light);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <i class="fas fa-exclamation-triangle" style="font-size:24px;color:var(--warning);"></i>
        </div>
        <h5 style="font-weight:700; font-size:16px; margin-bottom:8px; color:var(--dark);">Belum Presensi Masuk</h5>
        <p style="font-size:13px; color:var(--gray); margin-bottom:16px; line-height:1.5;">Silakan lakukan presensi masuk terlebih dahulu sebelum presensi pulang.</p>
        <button onclick="closeSimpleModal('warningModal')" style="width:100%; padding:12px; border:none; border-radius:12px; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">Mengerti</button>
    </div>
</div>

<!-- Toast Notifikasi -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2 fs-5"></i>
                    <div>
                        <div class="fw-semibold">Presensi Berhasil</div>
                        <div class="small opacity-90" id="successToastMessage">Presensi berhasil dicatat</div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                    <div>
                        <div class="fw-semibold">Presensi Gagal</div>
                        <div class="small opacity-90" id="errorToastMessage">Terjadi kesalahan saat presensi</div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    <div id="warningToast" class="toast align-items-center text-white bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                    <div>
                        <div class="fw-semibold">Perhatian</div>
                        <div class="small opacity-90" id="warningToastMessage">Anda berada di luar radius</div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modal Konfirmasi Styles */
    .confirmation-icon {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }


    /* Toast Styles */
    .toast {
        min-width: 320px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .toast.bg-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }

    .toast.bg-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    }

    .toast.bg-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }

    /* Modal Styles */
    .modal-content.rounded-2xl {
        border-radius: 16px !important;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-body .flex button:first-child {
        border-bottom-left-radius: 16px;
    }

    .modal-body .flex button:last-child {
        border-bottom-right-radius: 16px;
    }

    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
    }

    .success-modal-content {
        border: 2px solid #10b981;
    }

    /* Face Guide */
    .face-guide {
        position:absolute; inset:0; z-index:2; pointer-events:none;
        display:flex; flex-direction:column; align-items:center; justify-content:center;
    }
    .face-guide-oval {
        width:60%; max-width:240px; aspect-ratio:3/4;
        border:2px solid rgba(255,255,255,0.35); border-radius:50%;
        transition:border-color 0.4s, box-shadow 0.4s;
    }
    .face-guide-oval.detected {
        border-color:var(--primary); border-width:3px;
        box-shadow:0 0 0 4px rgba(90,182,234,0.2), 0 0 30px rgba(90,182,234,0.15);
    }
    .face-guide-text {
        color:rgba(255,255,255,0.7); font-size:11px; margin-top:10px;
        text-shadow:0 1px 4px rgba(0,0,0,0.5); font-weight:500;
    }

    /* Face Status Badge */
    .face-status {
        position:absolute; bottom:100px; left:50%; transform:translateX(-50%);
        z-index:3; padding:8px 16px; border-radius:12px;
        font-size:12px; font-weight:600; display:flex; align-items:center; gap:8px;
        white-space:nowrap; backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);
        transition:all 0.3s;
    }
    .face-status.no-face {
        background:rgba(30,30,30,0.7); color:rgba(255,255,255,0.8);
    }
    .face-status.face-ok {
        background:rgba(16,185,129,0.2); color:#6ee7b7;
        border:1px solid rgba(16,185,129,0.3);
    }

    .submit-btn-large:disabled {
        opacity:0.4; cursor:not-allowed;
    }

    /* Floating Lembur FAB */
    .lembur-fab {
        position: fixed;
        bottom: 90px;
        right: 15px;
        z-index: 50;
        border: none;
        border-radius: 14px;
        padding: 10px 14px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .lembur-fab:active { transform: scale(0.95); }

    .lembur-fab-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        background: rgba(255,255,255,0.2);
    }

    .lembur-fab-text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .lembur-fab-label {
        font-size: 12px;
        font-weight: 700;
    }

    .lembur-fab-timer {
        font-size: 11px;
        font-weight: 600;
        opacity: 0.85;
        font-variant-numeric: tabular-nums;
    }

    .lembur-idle {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .lembur-idle .lembur-fab-text {
        font-size: 13px;
        font-weight: 700;
    }

    .lembur-active {
        background: linear-gradient(135deg, #10b981, #059669);
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
    }

    .lembur-done {
        background: #94a3b8;
        cursor: default;
        pointer-events: none;
        opacity: 0.6;
    }

    .lembur-done .lembur-fab-text {
        font-size: 12px;
        font-weight: 600;
    }

    .pulse {
        animation: fabPulse 2s infinite;
    }

    @keyframes fabPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,0.3); }
        50% { box-shadow: 0 0 0 6px rgba(255,255,255,0); }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/face_detection.js" crossorigin="anonymous" defer></script>
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/dist/face-api.js" defer></script>
<script>
    // Network detection — disable presensi buttons when offline
    (function() {
        var offlineBanner = document.createElement('div');
        offlineBanner.id = 'offlineBanner';
        offlineBanner.style.cssText = 'display:none;position:fixed;top:0;left:0;right:0;z-index:999;background:#ef4444;color:#fff;text-align:center;padding:10px 16px;font-size:13px;font-weight:600;';
        offlineBanner.innerHTML = '<i class="fas fa-wifi" style="margin-right:6px;"></i> Tidak ada jaringan — presensi dinonaktifkan';
        document.body.appendChild(offlineBanner);

        var presensiButtons = ['clock-in-btn', 'clock-out-btn'];
        var lemburFabs;
        var savedStates = {};

        function disablePresensi() {
            offlineBanner.style.display = 'block';
            presensiButtons.forEach(function(id) {
                var btn = document.getElementById(id);
                if (btn) { savedStates[id] = btn.disabled; btn.disabled = true; btn.style.opacity = '0.4'; btn.style.pointerEvents = 'none'; }
            });
            lemburFabs = document.querySelectorAll('.lembur-fab');
            lemburFabs.forEach(function(fab) { fab.style.opacity = '0.4'; fab.style.pointerEvents = 'none'; });
        }

        function enablePresensi() {
            offlineBanner.style.display = 'none';
            presensiButtons.forEach(function(id) {
                var btn = document.getElementById(id);
                if (btn) {
                    btn.disabled = savedStates[id] || false;
                    btn.style.opacity = btn.disabled ? '0.6' : '1';
                    btn.style.pointerEvents = '';
                }
            });
            if (lemburFabs) lemburFabs.forEach(function(fab) { fab.style.opacity = '1'; fab.style.pointerEvents = ''; });
        }

        if (!navigator.onLine) disablePresensi();
        window.addEventListener('offline', disablePresensi);
        window.addEventListener('online', enablePresensi);
    })();

    // Carousel with live drag
    var currentSlide = 0;
    var track, totalSlides, autoSlideTimer, isDragging, dragStartX, dragCurrentX, dragBaseOffset;

    function getTrackWidth() { return track ? track.parentElement.offsetWidth : 1; }
    function setTrackPos(px, animate) {
        if (!track) return;
        track.style.transition = animate ? 'transform 0.3s ease' : 'none';
        track.style.transform = 'translateX(' + px + 'px)';
    }
    function goToSlide(i, animate) {
        currentSlide = Math.max(0, Math.min(i, totalSlides - 1));
        setTrackPos(-currentSlide * getTrackWidth(), animate !== false);
        document.querySelectorAll('#carouselDots .dot').forEach(function(d, idx) { d.classList.toggle('active', idx === currentSlide); });
        resetAutoSlide();
    }
    function nextSlide() { goToSlide((currentSlide + 1) % totalSlides); }
    function resetAutoSlide() {
        if (autoSlideTimer) clearInterval(autoSlideTimer);
        if (totalSlides > 1) autoSlideTimer = setInterval(nextSlide, 5000);
    }
    function onDragStart(x) { isDragging=true; dragStartX=x; dragCurrentX=x; dragBaseOffset=-currentSlide*getTrackWidth(); if(autoSlideTimer) clearInterval(autoSlideTimer); }
    function onDragMove(x) { if(!isDragging) return; dragCurrentX=x; setTrackPos(dragBaseOffset+(dragCurrentX-dragStartX), false); }
    function onDragEnd() {
        if(!isDragging) return; isDragging=false;
        var diff=dragCurrentX-dragStartX, threshold=getTrackWidth()*0.2;
        if(diff<-threshold && currentSlide<totalSlides-1) goToSlide(currentSlide+1);
        else if(diff>threshold && currentSlide>0) goToSlide(currentSlide-1);
        else goToSlide(currentSlide);
    }

    function initCarousel() {
        if(autoSlideTimer) clearInterval(autoSlideTimer);
        currentSlide=0; isDragging=false;
        track = document.getElementById('carouselTrack');
        totalSlides = track ? track.children.length : 0;
        if(!track || totalSlides <= 1) return;

        setTrackPos(0, false);
        resetAutoSlide();

        track.ontouchstart = function(e) { onDragStart(e.touches[0].clientX); };
        track.ontouchmove = function(e) { onDragMove(e.touches[0].clientX); };
        track.ontouchend = onDragEnd;
        track.ontouchcancel = onDragEnd;
        track.onmousedown = function(e) { e.preventDefault(); onDragStart(e.clientX); track.style.cursor='grabbing'; };
        document.onmousemove = function(e) { if(isDragging) onDragMove(e.clientX); };
        document.onmouseup = function() { onDragEnd(); if(track) track.style.cursor='grab'; };
        track.style.cursor = 'grab';
        track.onclick = function(e) { if(Math.abs(dragCurrentX-dragStartX)>10) e.stopPropagation(); };
    }

    document.addEventListener('turbo:load', function() {
        initCarousel();
        // Hide badges for already-read pengumuman
        var read = JSON.parse(localStorage.getItem('karisma-read-pengumuman') || '[]');
        read.forEach(function(id) {
            var badge = document.getElementById('badgeBaru' + id);
            if (badge) badge.style.display = 'none';
        });
    });
    initCarousel();

    function openInfoModal(id) {
        document.getElementById('infoModal' + id).style.display = 'block';
        // Mark as read — hide badge
        var read = JSON.parse(localStorage.getItem('karisma-read-pengumuman') || '[]');
        if (read.indexOf(id) === -1) {
            read.push(id);
            localStorage.setItem('karisma-read-pengumuman', JSON.stringify(read));
        }
        var badge = document.getElementById('badgeBaru' + id);
        if (badge) badge.style.display = 'none';
    }
    function closeInfoModal(id) {
        document.getElementById('infoModal' + id).style.display = 'none';
    }

    // Profile photo map marker
    var _profilePhoto = @json(
        (Auth::user()->foto_profil && Storage::disk('public')->exists('foto_profil/' . Auth::user()->foto_profil))
            ? asset('public/storage/foto_profil/' . Auth::user()->foto_profil)
            : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=5AB6EA&color=fff&size=80'
    );

    function profileMarkerIcon() {
        return L.divIcon({
            className: '',
            html: '<div style="width:40px;height:40px;border-radius:50%;border:3px solid var(--primary);overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.2);"><img src="' + _profilePhoto + '" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.src=\'https://ui-avatars.com/api/?name=U&background=5AB6EA&color=fff&size=80\'"></div>',
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });
    }

    // Push Notification Reminders
    (function() {
        if (!('Notification' in window)) return;

        var jadwalMasuk = @json($jadwalKerjaHariIni['jam_masuk'] ?? '07:30:00');
        var jadwalPulang = @json($jadwalKerjaHariIni['jam_pulang'] ?? '16:00:00');
        var sudahMasuk = @json($sudahPresensiMasuk);
        var sudahPulang = @json($sudahPresensiPulang);
        var jamMasukUser = @json($jamMasukHariIni ?? '');
        var isLibur = @json($isLiburHariIni ?? false);

        if (isLibur) return;

        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }

        function sendNotif(title, body, tag) {
            if (Notification.permission !== 'granted') return;
            if (sessionStorage.getItem('notif-' + tag)) return;
            new Notification(title, { body: body, icon: '{{ asset("public/pwa/icons/icon-192x192.png") }}', tag: tag });
            sessionStorage.setItem('notif-' + tag, '1');
        }

        function parseTime(str) {
            var p = str.split(':');
            var d = new Date();
            d.setHours(parseInt(p[0]), parseInt(p[1]), parseInt(p[2] || 0), 0);
            return d;
        }

        var now = new Date();
        var masukTime = parseTime(jadwalMasuk);
        var pulangTime = parseTime(jadwalPulang);
        var reminderMasuk = new Date(masukTime.getTime() - 10 * 60000);
        var reminderPulang = new Date(pulangTime.getTime() - 10 * 60000);

        // Hitung jam pulang berdasarkan durasi kerja
        var durasiKerja = pulangTime.getTime() - masukTime.getTime();
        var jamPulangAktual = pulangTime;
        if (sudahMasuk && jamMasukUser) {
            var actualStart = parseTime(jamMasukUser);
            if (actualStart > masukTime) {
                jamPulangAktual = new Date(actualStart.getTime() + durasiKerja);
            }
        }

        function checkReminders() {
            var n = new Date();
            var today = n.toISOString().split('T')[0];

            if (!sudahMasuk && n >= reminderMasuk && n < masukTime) {
                sendNotif('Reminder Presensi Masuk', 'Waktu masuk kerja 10 menit lagi (' + jadwalMasuk.substring(0,5) + ')', today + '-masuk-reminder');
            }

            if (sudahMasuk && !sudahPulang && n >= new Date(jamPulangAktual.getTime() - 10 * 60000) && n < jamPulangAktual) {
                sendNotif('Reminder Presensi Pulang', 'Waktu pulang kerja 10 menit lagi', today + '-pulang-reminder');
            }

            if (sudahMasuk && !sudahPulang && n >= jamPulangAktual) {
                sendNotif('Waktunya Pulang', 'Jam kerja Anda sudah terpenuhi. Jangan lupa presensi pulang!', today + '-pulang-now');
            }
        }

        checkReminders();
        setInterval(checkReminders, 30000);
    })();

    let videoStream = null;
    let mapInstance = null;
    let currentPosition = null;
    let isOutsideRadius = false;
    let capturedPhotoData = null;
    let autoCloseTimer = null;
    let faceDetected = false;
    let mpFaceDetector = null;

    window._enableFaceDetection = @json($enableFaceDetection);

    const sudahPresensiMasuk = @json($sudahPresensiMasuk);
    const sudahPresensiPulang = @json($sudahPresensiPulang);

    function setJenis(jenis) {
        const el = document.getElementById('jenisPresensi');
        if (el) el.value = jenis;
    }

    function setLembur(val) {
        const el = document.getElementById('isLemburInput');
        if (el) el.value = val ? '1' : '0';
    }

    function openSimpleModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeSimpleModal(id) { document.getElementById(id).style.display = 'none'; }

    function pickShift(shiftId) {
        document.getElementById('jamShiftIdInput').value = shiftId;
        closeSimpleModal('shiftPickerModal');
        setTimeout(function() {
            new bootstrap.Modal(document.getElementById('presensiModal')).show();
        }, 300);
    }

    // Timer lembur
    (function() {
        var timerEl = document.getElementById('lemburTimer');
        if (!timerEl) return;
        var startTime = timerEl.getAttribute('data-start');
        if (!startTime) return;

        var parts = startTime.split(':');
        var now = new Date();
        var start = new Date(now.getFullYear(), now.getMonth(), now.getDate(),
            parseInt(parts[0]), parseInt(parts[1]), parseInt(parts[2] || 0));

        function update() {
            var diff = Math.floor((new Date() - start) / 1000);
            if (diff < 0) diff = 0;
            var h = String(Math.floor(diff / 3600)).padStart(2, '0');
            var m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            var s = String(diff % 60).padStart(2, '0');
            timerEl.textContent = h + ':' + m + ':' + s;
        }

        update();
        setInterval(update, 1000);
    })();

    // Timer jam kerja reguler + cek pulang
    var workTimerFulfilled = false;

    (function() {
        var card = document.getElementById('workTimerBanner');
        var clockEl = document.getElementById('workTimerClock');
        var labelEl = document.getElementById('workTimerLabel');
        var badgeEl = document.getElementById('workTimerBadge');
        if (!card || !clockEl) return;

        var stopped = card.getAttribute('data-stopped') === '1';
        var pulangJam = card.getAttribute('data-pulang-jam') || '';

        var jamMasuk = @json($jamMasukHariIni ?? '');
        var jamPulang = @json($jamPulangTarget ?? '16:00:00');
        var jadwalMasuk = @json($jadwalKerjaHariIni['jam_masuk'] ?? '07:30:00');
        if (!jamMasuk) return;

        var now = new Date();
        var mParts = jamMasuk.split(':');
        var jParts = jadwalMasuk.split(':');
        var pParts = jamPulang.split(':');

        var jadwalStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(),
            parseInt(jParts[0]), parseInt(jParts[1]), parseInt(jParts[2] || 0));
        var actualStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(),
            parseInt(mParts[0]), parseInt(mParts[1]), parseInt(mParts[2] || 0));
        var startTime = actualStart > jadwalStart ? actualStart : jadwalStart;

        var endTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(),
            parseInt(pParts[0]), parseInt(pParts[1]), parseInt(pParts[2] || 0));
        var totalTarget = Math.floor((endTime - jadwalStart) / 1000);
        if (totalTarget <= 0) totalTarget = 8 * 3600;

        function formatTime(sec) {
            return String(Math.floor(sec / 3600)).padStart(2, '0') + ':' +
                String(Math.floor((sec % 3600) / 60)).padStart(2, '0') + ':' +
                String(sec % 60).padStart(2, '0');
        }

        if (stopped && pulangJam) {
            var ppParts = pulangJam.split(':');
            var pulangTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(),
                parseInt(ppParts[0]), parseInt(ppParts[1]), parseInt(ppParts[2] || 0));
            var elapsed = Math.max(0, Math.floor((pulangTime - startTime) / 1000));
            clockEl.textContent = formatTime(elapsed);
            workTimerFulfilled = elapsed >= totalTarget;
            if (badgeEl) badgeEl.textContent = workTimerFulfilled ? '✓ Terpenuhi' : 'Kurang';
            return;
        }

        function update() {
            var elapsed = Math.max(0, Math.floor((new Date() - startTime) / 1000));
            clockEl.textContent = formatTime(elapsed);

            if (elapsed >= totalTarget) {
                card.classList.remove('timer-yellow');
                card.classList.add('timer-green');
                if (labelEl) labelEl.textContent = 'Jam kerja terpenuhi';
                if (badgeEl) badgeEl.textContent = '✓ Terpenuhi';
                workTimerFulfilled = true;
            } else {
                var sisa = totalTarget - elapsed;
                var sh = Math.floor(sisa / 3600);
                var sm = Math.floor((sisa % 3600) / 60);
                if (labelEl) labelEl.textContent = 'Sisa ' + (sh > 0 ? sh + 'j ' : '') + sm + 'm';
                if (badgeEl) badgeEl.textContent = 'Berjalan';
                workTimerFulfilled = false;
            }
        }

        update();
        setInterval(update, 1000);
    })();

    var requireMasukFirst = @json($requireMasukBeforePulang);
    function handlePulangWithCheck() {
        if (requireMasukFirst && !sudahPresensiMasuk) {
            openSimpleModal('warningModal');
            return;
        }

        setJenis('pulang');
        setLembur(false);
        @if($shiftHariIni ?? false)
        document.getElementById('jamShiftIdInput').value = '{{ $shiftHariIni->id ?? '' }}';
        @endif

        if (!workTimerFulfilled) {
            var clockEl = document.getElementById('workTimerClock');
            var msg = document.getElementById('earlyPulangMsg');
            if (msg) {
                msg.textContent = 'Jam kerja hari ini belum terpenuhi (' + (clockEl ? clockEl.textContent : '') + '). Apakah yakin ingin absen pulang?';
            }
            openSimpleModal('earlyPulangModal');
        } else {
            new bootstrap.Modal(document.getElementById('presensiModal')).show();
        }
    }

    function openLemburConfirm() {
        document.getElementById('confirmLemburModal').style.display = 'flex';
    }
    function closeLemburConfirm() {
        document.getElementById('confirmLemburModal').style.display = 'none';
    }

    function proceedSelesaiLembur() {
        setJenis('pulang');
        setLembur(true);
        closeLemburConfirm();

        setTimeout(function() {
            new bootstrap.Modal(document.getElementById('presensiModal')).show();
        }, 300);
    }

    function proceedPulang() {
        closeSimpleModal('earlyPulangModal');
        setTimeout(function() {
            new bootstrap.Modal(document.getElementById('presensiModal')).show();
        }, 300);
    }

    function handlePulangClick() {
        if (!sudahPresensiMasuk) {
            openSimpleModal('warningModal');
            return false;
        }
        setJenis('pulang');
        return true;
    }

    // Cleanup kamera saat Turbo cache halaman
    document.addEventListener('turbo:before-cache', function() {
        cleanupPresensiModal();
        var video = document.getElementById('video');
        if (video) { video.srcObject = null; video.load(); }
    });

    // Re-init kamera saat kembali dari bfcache
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            var modal = document.getElementById('presensiModal');
            if (modal && modal.classList.contains('show')) {
                initializePresensiModal();
            }
        }
    });

    // Bind SEKALI saja — tidak di dalam turbo:load agar tidak menumpuk
    (function() {
        var presensiModal = document.getElementById('presensiModal');
        if (presensiModal && !presensiModal._boundPresensi) {
            presensiModal._boundPresensi = true;
            presensiModal.addEventListener('shown.bs.modal', initializePresensiModal);
            presensiModal.addEventListener('hidden.bs.modal', cleanupPresensiModal);
        }
    })();

    document.addEventListener('turbo:load', function() {
        // Re-bind jika DOM diganti oleh Turbo
        var presensiModal = document.getElementById('presensiModal');
        if (presensiModal && !presensiModal._boundPresensi) {
            presensiModal._boundPresensi = true;
            presensiModal.addEventListener('shown.bs.modal', initializePresensiModal);
            presensiModal.addEventListener('hidden.bs.modal', cleanupPresensiModal);
        }

        // Detail map modals
        initializeDetailModals();

        // Inisialisasi toast
        if (window.bootstrap?.Toast) {
            const toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                });
            });
        }

        // Handle response dari server
        @if(session('success'))
            showSuccess(@json(session('success')));
        @endif

        @if(session('error'))
            showError(@json(session('error')));
        @endif

        @if(session('warning'))
            showWarning(@json(session('warning')));
        @endif
    });

    function initializePresensiModal() {
        initializeCamera();
        initializeLocation();
    }

    function cleanupPresensiModal() {
        stopFaceDetection();

        if (videoStream) {
            videoStream.getTracks().forEach(t => t.stop());
            videoStream = null;
        }

        if (mapInstance) {
            try { mapInstance.remove(); } catch (e) {}
            mapInstance = null;
        }

        var submitBtn = document.querySelector('.submit-btn-large');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Ambil Foto & Absen';
            submitBtn.disabled = true;
        }

        capturedPhotoData = null;
        currentPosition = null;
        isOutsideRadius = false;

        // Jangan reset is_lembur di sini — form.submit() mungkin belum selesai
        // Reset dilakukan oleh onclick tombol reguler (setLembur(false))

        if (autoCloseTimer) {
            clearTimeout(autoCloseTimer);
            autoCloseTimer = null;
        }

        // reset teks lokasi
        const loc = document.getElementById('location-address-mini');
        if (loc) loc.textContent = 'Mendeteksi lokasi...';

        const infoEl = document.getElementById('locationRadiusInfo');
        if (infoEl) infoEl.textContent = '';
    }

    var _faceDetectTimer = null;
    var _faceDetecting = false;

    var _cameraInitId = 0;
    function initializeCamera() {
        var video = document.getElementById('video');
        if (!video) return;

        var myId = ++_cameraInitId;

        // Full cleanup first — stop everything
        stopFaceDetection();
        if (videoStream) {
            videoStream.getTracks().forEach(function(t) { t.stop(); });
            videoStream = null;
        }
        video.srcObject = null;
        video.muted = true;

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError("Browser tidak mendukung akses kamera.");
            return;
        }

        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            audio: false
        })
        .then(function(stream) {
            if (myId !== _cameraInitId) { stream.getTracks().forEach(function(t){t.stop();}); return Promise.reject('stale'); }
            videoStream = stream;
            video.srcObject = stream;
            return video.play().catch(function(){});
        })
        .then(function() {
            if (myId !== _cameraInitId) return;
            if (window._enableFaceDetection) {
                initFaceDetection();
            } else {
                var submitBtn = document.querySelector('.submit-btn-large');
                if (submitBtn) submitBtn.disabled = false;
                faceDetected = true;
                var statusEl = document.getElementById('faceStatus');
                if (statusEl) { statusEl.className = 'face-status no-face'; statusEl.style.background = 'rgba(255,255,255,0.15)'; statusEl.style.color = 'rgba(255,255,255,0.7)'; statusEl.innerHTML = '<i class="fas fa-user"></i> Letakkan wajah di dalam lingkaran'; }
            }
        })
        .catch(function(err) {
            if (err === 'stale') return;
            console.error(err);
            var statusEl = document.getElementById('faceStatus');
            if (statusEl) {
                statusEl.className = 'face-status no-face';
                statusEl.innerHTML = '<i class="fas fa-camera-rotate"></i> Kamera gagal';
            }
            showCameraError();
        });
    }

    function showCameraError() {
        var video = document.getElementById('video');
        if (!video) return;
        var parent = video.parentElement;
        var existing = document.getElementById('cameraErrorOverlay');
        if (existing) existing.remove();
        var overlay = document.createElement('div');
        overlay.id = 'cameraErrorOverlay';
        overlay.style.cssText = 'position:absolute;inset:0;z-index:15;background:rgba(0,0,0,0.85);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;color:#fff;';
        overlay.innerHTML = '<i class="fas fa-camera" style="font-size:32px;opacity:0.3;"></i>' +
            '<div style="font-size:14px;font-weight:600;">Kamera tidak dapat diakses</div>' +
            '<div style="font-size:11px;color:rgba(255,255,255,0.6);text-align:center;padding:0 20px;">Pastikan izin kamera diaktifkan, lalu coba lagi</div>' +
            '<button onclick="retryCamera()" style="margin-top:8px;padding:10px 24px;border-radius:12px;border:none;background:var(--primary);color:#fff;font-weight:600;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;"><i class="fas fa-rotate-right"></i> Coba Lagi</button>';
        parent.appendChild(overlay);
    }

    function retryCamera() {
        var overlay = document.getElementById('cameraErrorOverlay');
        if (overlay) overlay.remove();
        initializeCamera();
    }

    // Face Detection — graceful: works if available, user can still submit if not
    var _faceDetectionActive = false;

    function initFaceDetection() {
        var video = document.getElementById('video');
        var submitBtn = document.querySelector('.submit-btn-large');
        var statusEl = document.getElementById('faceStatus');

        faceDetected = false;
        _faceDetectionActive = false;

        // Cleanup previous
        if (_faceDetectTimer) { clearInterval(_faceDetectTimer); _faceDetectTimer = null; }
        if (mpFaceDetector) { try { mpFaceDetector.close(); } catch(e) {} mpFaceDetector = null; }

        // Disable button while loading
        if (submitBtn) submitBtn.disabled = true;
        if (statusEl) { statusEl.className = 'face-status no-face'; statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...'; }

        // MediaPipe timeout — if no result after 10s, try face-api.js
        var mpTimeout = null;

        // Try MediaPipe
        if (typeof FaceDetection !== 'undefined') {
            try {
                mpFaceDetector = new FaceDetection({
                    locateFile: function(file) { return 'https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/' + file; }
                });
                mpFaceDetector.setOptions({ model: 'short', minDetectionConfidence: 0.6 });

                mpTimeout = setTimeout(function() {
                    if (!_faceDetectionActive) {
                        if (mpFaceDetector) { try { mpFaceDetector.close(); } catch(e) {} mpFaceDetector = null; }
                        if (_faceDetectTimer) { clearInterval(_faceDetectTimer); _faceDetectTimer = null; }
                        tryFaceApi(video, submitBtn, statusEl);
                    }
                }, 10000);

                mpFaceDetector.onResults(function(results) {
                    if (!_faceDetectionActive) { _faceDetectionActive = true; if (mpTimeout) clearTimeout(mpTimeout); }
                    _faceDetecting = false;
                    if (!videoStream) return;
                    var found = results.detections && results.detections.length > 0 && isFaceInGuide(results.detections[0].boundingBox, video);
                    if (found !== faceDetected) { faceDetected = found; updateFaceStatus(found); }
                });

                _faceDetectTimer = setInterval(function() {
                    if (!videoStream || !mpFaceDetector || _faceDetecting) return;
                    if (video.readyState < 2 || video.paused) return;
                    _faceDetecting = true;
                    mpFaceDetector.send({ image: video }).catch(function() { _faceDetecting = false; });
                }, 300);
                return;
            } catch(e) {}
        }

        // MediaPipe not available, try face-api.js directly
        tryFaceApi(video, submitBtn, statusEl);
    }

    function tryFaceApi(video, submitBtn, statusEl) {
        if (statusEl) { statusEl.className = 'face-status no-face'; statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat model...'; }

        if (typeof faceapi !== 'undefined') {
            (async function() {
                try {
                    await faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/model/');
                    _faceDetectionActive = true;
                    var opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.3 });
                    _faceDetectTimer = setInterval(async function() {
                        if (!videoStream || _faceDetecting) return;
                        if (video.readyState < 2 || video.paused) return;
                        _faceDetecting = true;
                        try {
                            var dets = await faceapi.detectAllFaces(video, opts);
                            var found = dets.length > 0 && isFaceInGuide(dets[0].box, video);
                            if (found !== faceDetected) { faceDetected = found; updateFaceStatus(found); }
                        } catch(e) {}
                        _faceDetecting = false;
                    }, 500);
                } catch(e) {
                    showFaceDetectionError(statusEl);
                }
            })();
            return;
        }

        // Both engines unavailable
        showFaceDetectionError(statusEl);
    }

    function showFaceDetectionError(statusEl) {
        if (_faceDetectTimer) { clearInterval(_faceDetectTimer); _faceDetectTimer = null; }
        if (mpFaceDetector) { try { mpFaceDetector.close(); } catch(e) {} mpFaceDetector = null; }
        faceDetected = false;
        var submitBtn = document.querySelector('.submit-btn-large');
        if (submitBtn) submitBtn.disabled = true;
        if (statusEl) {
            statusEl.className = 'face-status no-face';
            statusEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Face detection gagal. <span onclick="location.reload()" style="text-decoration:underline;cursor:pointer;font-weight:700;">Refresh</span>';
        }
    }

    function isFaceInGuide(bb, video) {
        var oval = document.getElementById('faceGuideOval');
        if (!oval || !video.videoWidth) return true;
        var gr = oval.getBoundingClientRect();
        var vr = video.getBoundingClientRect();

        var faceX, faceY, faceW, faceH;
        if (bb.xCenter !== undefined) {
            // MediaPipe format (relative 0-1, centered)
            faceW = bb.width * vr.width;
            faceH = bb.height * vr.height;
            faceX = vr.left + bb.xCenter * vr.width - faceW / 2;
            faceY = vr.top + bb.yCenter * vr.height - faceH / 2;
        } else {
            // face-api.js format (absolute pixels)
            var sx = vr.width / video.videoWidth;
            var sy = vr.height / video.videoHeight;
            faceW = bb.width * sx;
            faceH = bb.height * sy;
            faceX = vr.left + bb.x * sx;
            faceY = vr.top + bb.y * sy;
        }

        var m = 20;
        return faceX >= (gr.left - m) && faceY >= (gr.top - m) &&
               (faceX + faceW) <= (gr.right + m) && (faceY + faceH) <= (gr.bottom + m);
    }

    function updateFaceStatus(detected) {
        var statusEl = document.getElementById('faceStatus');
        var submitBtn = document.querySelector('.submit-btn-large');
        var ovalEl = document.getElementById('faceGuideOval');
        if (statusEl) {
            statusEl.className = detected ? 'face-status face-ok' : 'face-status no-face';
            statusEl.innerHTML = detected ? '<i class="fas fa-user-check"></i> Wajah terdeteksi' : '<i class="fas fa-user-slash"></i> Posisikan wajah dalam lingkaran';
        }
        if (ovalEl) ovalEl.classList.toggle('detected', detected);
        if (submitBtn) submitBtn.disabled = !detected;
    }

    function stopFaceDetection() {
        if (_faceDetectTimer) { clearInterval(_faceDetectTimer); _faceDetectTimer = null; }
        _faceDetecting = false;
        if (mpFaceDetector) { try { mpFaceDetector.close(); } catch(e) {} mpFaceDetector = null; }
        faceDetected = false;
        var ovalEl = document.getElementById('faceGuideOval');
        if (ovalEl) ovalEl.classList.remove('detected');
        var submitBtn = document.querySelector('.submit-btn-large');
        if (submitBtn) submitBtn.disabled = true;
    }

    function retryLocation() {
        var addrEl = document.getElementById('location-address-mini');
        if (addrEl) addrEl.textContent = 'Mendeteksi lokasi...';
        var infoEl = document.getElementById('locationRadiusInfo');
        if (infoEl) infoEl.innerHTML = '';
        initializeLocation();
    }

    var _locInitId = 0;
    function initializeLocation() {
        var myId = ++_locInitId;
        var addrEl = document.getElementById('location-address-mini');

        if (mapInstance) {
            try { mapInstance.remove(); } catch(e) {}
            mapInstance = null;
        }

        if (!navigator.geolocation) {
            if (addrEl) addrEl.innerHTML = '<span style="color:var(--danger);">Browser tidak mendukung geolokasi</span>';
            var infoEl = document.getElementById('locationRadiusInfo');
            if (infoEl) infoEl.innerHTML = '<button onclick="location.reload()" style="margin-top:6px;padding:6px 16px;border-radius:10px;border:none;background:var(--primary);color:#fff;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i class="fas fa-rotate-right"></i> Refresh Halaman</button>';
            initializeMiniMapWithDefault();
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                if (myId !== _locInitId) return;
                currentPosition = pos;
                updateLocationInfo(pos);
                initializeMiniMap(pos);
            },
            function(err) {
                if (myId !== _locInitId) return;
                console.error(err);
                if (addrEl) addrEl.innerHTML = '<span style="color:var(--danger);">Lokasi gagal dideteksi</span>';
                var infoEl = document.getElementById('locationRadiusInfo');
                if (infoEl) infoEl.innerHTML = '<button onclick="retryLocation()" style="margin-top:6px;padding:6px 16px;border-radius:10px;border:none;background:var(--primary);color:#fff;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i class="fas fa-rotate-right"></i> Coba Lagi</button>';
                initializeMiniMapWithDefault();
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 }
        );
    }

    const wilayahList = @json($wilayahJson);

    function updateLocationInfo(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        const lokasiInput = document.getElementById('lokasiInput');
        if (lokasiInput) lokasiInput.value = `${lat},${lng}`;

        const infoEl = document.getElementById('locationRadiusInfo');
        const submitBtn = document.querySelector('.submit-btn-large');
        const addrEl = document.getElementById('location-address-mini');

        var matched = null;
        for (var i = 0; i < wilayahList.length; i++) {
            if (haversineDistance(lat, lng, wilayahList[i].lat, wilayahList[i].lng) <= wilayahList[i].radius) {
                matched = wilayahList[i];
                break;
            }
        }

        if (matched) {
            if (infoEl) infoEl.innerHTML = '<div style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:8px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);"><i class="fas fa-check-circle" style="color:#10b981;font-size:11px;"></i><span style="font-size:11px;font-weight:600;color:#10b981;">Di dalam wilayah kerja</span></div>';
            if (addrEl) addrEl.textContent = matched.alamat || 'Lokasi terverifikasi';
            isOutsideRadius = false;
        } else {
            if (infoEl) infoEl.innerHTML = '<div style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:8px;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);"><i class="fas fa-triangle-exclamation" style="color:#f59e0b;font-size:11px;"></i><span style="font-size:11px;font-weight:600;color:#f59e0b;">Di luar radius wilayah kerja</span></div>';
            isOutsideRadius = true;
            if (addrEl) getAddressFromCoordinates(lat, lng, addrEl);
        }
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = x => x * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) ** 2;
        return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function initializeMiniMap(position) {
        const mapEl = document.getElementById('mini-map');
        if (!mapEl) return;

        if (!window.L) {
            setTimeout(function(){ initializeMiniMap(position); }, 500);
            return;
        }

        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        if (!mapInstance) {
            mapInstance = L.map(mapEl, {
                zoomControl: false,
                attributionControl: false
            }).setView([lat, lng], 17);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(mapInstance);

            L.marker([lat, lng], { icon: profileMarkerIcon() }).addTo(mapInstance);

            ['dragging','touchZoom','doubleClickZoom','scrollWheelZoom','boxZoom','keyboard']
                .forEach(f => mapInstance[f] && mapInstance[f].disable());
        } else {
            mapInstance.setView([lat, lng], 17);
        }

        // penting: setelah modal tampil, map perlu invalidateSize
        setTimeout(() => {
            try { mapInstance.invalidateSize(); } catch (e) {}
        }, 300);
    }

    function initializeMiniMapWithDefault() {
        const mapEl = document.getElementById('mini-map');
        if (!mapEl) return;

        if (!window.L) { setTimeout(initializeMiniMapWithDefault, 500); return; }

        if (!mapInstance) {
            mapInstance = L.map(mapEl, {
                zoomControl: false,
                attributionControl: false
            }).setView([-6.2088, 106.8456], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(mapInstance);

            setTimeout(() => {
                try { mapInstance.invalidateSize(); } catch (e) {}
            }, 300);
        }
    }

    const detailWilayahAlamat = @json($wilayahJson[0]['alamat'] ?? '');

    function initializeDetailModals() {
        if (!window.L) return;

        @foreach($riwayatHariIni as $p)
            @if($p->lokasi)
            (function() {
                const modal = document.getElementById('detailModal{{ $p->id }}');
                const status = @json($p->status);
                if (!modal) return;

                modal.addEventListener('shown.bs.modal', function() {
                    const coords = @json($p->lokasi).split(',');
                    const lat = parseFloat(coords[0]);
                    const lng = parseFloat(coords[1]);
                    const addrEl = document.getElementById('locationAddress{{ $p->id }}');

                    if (isNaN(lat) || isNaN(lng)) {
                        if (addrEl) addrEl.innerHTML = '<span>Koordinat tidak valid</span>';
                        return;
                    }

                    try {
                        const map = L.map('mapDetail{{ $p->id }}').setView([lat, lng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                        L.marker([lat, lng], { icon: profileMarkerIcon() }).addTo(map);
                        this._map = map;
                        setTimeout(() => { try { map.invalidateSize(); } catch(e){} }, 300);
                    } catch (e) {
                        console.error('Map error:', e);
                    }

                    if (addrEl) {
                        if (status === 'approved' && detailWilayahAlamat) {
                            addrEl.textContent = detailWilayahAlamat;
                        } else {
                            getAddressFromCoordinates(lat, lng, addrEl);
                        }
                    }
                });

                modal.addEventListener('hidden.bs.modal', function() {
                    if (this._map) {
                        try { this._map.remove(); } catch(e){}
                        this._map = null;
                    }
                });
            })();
            @endif
        @endforeach
    }

    function getAddressFromCoordinates(lat, lng, el) {
        el.textContent = 'Mendeteksi alamat...';

        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1')
            .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
            .then(function(data) {
                if (data && data.display_name) {
                    el.textContent = data.display_name;
                } else if (data && data.address) {
                    var parts = [];
                    if (data.address.road) parts.push(data.address.road);
                    if (data.address.suburb) parts.push(data.address.suburb);
                    if (data.address.city || data.address.town) parts.push(data.address.city || data.address.town);
                    el.textContent = parts.length > 0 ? parts.join(', ') : 'Alamat tidak ditemukan';
                } else {
                    el.textContent = 'Alamat tidak ditemukan';
                }
            })
            .catch(function() {
                el.textContent = 'Alamat tidak dapat dideteksi';
            });
    }

    function captureAndProcess() {
        if (!videoStream || !currentPosition) {
            showError("Kamera atau lokasi belum siap. Pastikan izin kamera & lokasi diizinkan.");
            return;
        }

        if (!faceDetected) {
            showError("Wajah belum terdeteksi. Posisikan wajah dalam lingkaran.");
            return;
        }

        const jenis = document.getElementById('jenisPresensi')?.value || '';
        const lemburVal = document.getElementById('isLemburInput')?.value;
        if (jenis === 'pulang' && !sudahPresensiMasuk && lemburVal !== '1') {
            showError("Anda belum melakukan presensi masuk hari ini.");
            return;
        }

        capturePhoto()
            .then(photoData => {
                capturedPhotoData = photoData;

                if (isOutsideRadius) {
                    showConfirmationModal();
                } else {
                    langsungProsesPresensi();
                }
            })
            .catch(error => {
                console.error('Error capturing photo:', error);
                showError("Gagal mengambil foto.");
            });
    }

    function capturePhoto() {
        return new Promise((resolve, reject) => {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            if (!video || !canvas) return reject(new Error('Video/canvas tidak ditemukan'));

            if (!video.videoWidth || !video.videoHeight) {
                return reject(new Error('Video belum siap'));
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            const photoData = canvas.toDataURL('image/jpeg', 0.8);
            resolve(photoData);
        });
    }

    function showConfirmationModal() {
        const jenis = document.getElementById('jenisPresensi')?.value || '';
        const waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        document.getElementById('confirmationJenis').textContent = jenis.toUpperCase();
        document.getElementById('confirmationWaktu').textContent = waktu;
        document.getElementById('confirmationLokasi').textContent = document.getElementById('location-address-mini')?.textContent || '-';

        const confirmBtn = document.getElementById('confirmPresensiBtn');
        if (confirmBtn) {
            confirmBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Ya, Presensi';
            confirmBtn.disabled = false;
        }

        openSimpleModal('confirmationModal');
    }

    function langsungProsesPresensi() {
        if (!capturedPhotoData) {
            showError("Foto belum diambil.");
            return;
        }

        const fotoInput = document.getElementById('fotoInput');
        if (fotoInput) fotoInput.value = capturedPhotoData;

        const form = document.getElementById('formPresensi');
        if (form) form.submit();

        const presensiModal = window.bootstrap?.Modal?.getInstance(document.getElementById('presensiModal'));
        if (presensiModal) presensiModal.hide();
    }

    function prosesPresensi() {
        if (!capturedPhotoData) {
            showError("Foto belum diambil.");
            return;
        }

        const confirmBtn = document.getElementById('confirmPresensiBtn');
        if (confirmBtn) {
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            confirmBtn.disabled = true;
        }

        const fotoInput = document.getElementById('fotoInput');
        if (fotoInput) fotoInput.value = capturedPhotoData;

        setTimeout(() => {
            const form = document.getElementById('formPresensi');
            if (form) form.submit();

            closeSimpleModal('confirmationModal');

            const presensiModal = window.bootstrap?.Modal?.getInstance(document.getElementById('presensiModal'));
            if (presensiModal) presensiModal.hide();
        }, 800);
    }

    // Fungsi Notifikasi
    function showSuccess(message) {
        const toast = document.getElementById('successToast');
        const messageEl = document.getElementById('successToastMessage');
        if (message && messageEl) messageEl.textContent = message;
        if (toast && window.bootstrap?.Toast) bootstrap.Toast.getOrCreateInstance(toast).show();
    }

    function showError(message) {
        const toast = document.getElementById('errorToast');
        const messageEl = document.getElementById('errorToastMessage');
        if (message && messageEl) messageEl.textContent = message;
        if (toast && window.bootstrap?.Toast) bootstrap.Toast.getOrCreateInstance(toast).show();
        else alert(message || 'Terjadi kesalahan');
    }

    function showWarning(message) {
        const toast = document.getElementById('warningToast');
        const messageEl = document.getElementById('warningToastMessage');
        if (message && messageEl) messageEl.textContent = message;
        if (toast && window.bootstrap?.Toast) bootstrap.Toast.getOrCreateInstance(toast).show();
    }
</script>
@endpush