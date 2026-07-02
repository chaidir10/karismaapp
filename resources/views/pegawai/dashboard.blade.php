@extends('layouts.pegawai')
@section('title', 'Home')

@php
    $masukDisabled = $sudahPresensiMasuk || ($disablePresensiLibur ?? false);
    $pulangDisabled = $sudahPresensiPulang || ($disablePresensiLibur ?? false);
    $enableWorkTimer = \App\Models\AppSetting::getBool('enable_work_timer', true);

    $pulangRec = $riwayatHariIni->where('jenis', 'pulang')->where('is_lembur', false)->first();

    if ($enableWorkTimer && $sudahPresensiMasuk && $jamMasukHariIni) {
        $jadwalMasukTime = \Carbon\Carbon::parse($jadwalKerjaHariIni['jam_masuk']);
        $actualMasukTime = \Carbon\Carbon::parse($jamMasukHariIni);
        $timerStart = $actualMasukTime->gt($jadwalMasukTime) ? $actualMasukTime : $jadwalMasukTime;
        $timerEnd = $pulangRec ? \Carbon\Carbon::parse($pulangRec->jam) : now();
        $elapsedSec = abs($timerEnd->diffInSeconds($timerStart));
        $elapsedStr = sprintf('%02d:%02d:%02d', floor($elapsedSec/3600), floor(($elapsedSec%3600)/60), $elapsedSec%60);
        $targetSec = abs(\Carbon\Carbon::parse($jadwalKerjaHariIni['jam_pulang'])->diffInSeconds($jadwalMasukTime));
        if ($targetSec <= 0) $targetSec = 8 * 3600;
        $isFulfilled = $elapsedSec >= $targetSec;
        $timerColor = $pulangRec ? 'timer-blue' : ($isFulfilled ? 'timer-green' : 'timer-yellow');
    }

    $profilePhotoUrl = (Auth::user()->foto_profil && Storage::disk('public')->exists('foto_profil/' . Auth::user()->foto_profil))
        ? asset('public/storage/foto_profil/' . Auth::user()->foto_profil)
        : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=5AB6EA&color=fff&size=80';
@endphp

<style>
    .btn-secondary { background:var(--gray-light); color:var(--dark); border:none; border-radius:10px; padding:20px 15px; cursor:pointer; font-size:14px; font-weight:600; flex:1; transition:all 0.2s ease; }
    .btn-secondary:hover { background:var(--gray); color:var(--white); }
    .btn-disabled { background:var(--gray-light)!important; color:var(--gray)!important; cursor:not-allowed!important; opacity:0.6; }
    .btn-disabled:hover { background:var(--gray-light)!important; color:var(--gray)!important; }

    .work-timer-card { margin:-70px 20px 15px; padding:60px 14px 18px; border-radius:16px; display:flex; align-items:center; gap:12px; position:relative; z-index:1; }
    .work-timer-card.timer-yellow { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff; box-shadow:0 4px 15px rgba(245,158,11,0.25); }
    .work-timer-card.timer-green { background:linear-gradient(135deg,#34d399,#10b981); color:#fff; box-shadow:0 4px 15px rgba(16,185,129,0.25); }
    .work-timer-card.timer-blue { background:linear-gradient(135deg,#60a5fa,#3b82f6); color:#fff; box-shadow:0 4px 15px rgba(59,130,246,0.25); }
    .timer-icon-circle { width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
    .timer-info { flex:1; min-width:0; }
    .timer-label { font-size:11px; font-weight:500; opacity:0.9; }
    .timer-clock-text { font-size:20px; font-weight:800; font-variant-numeric:tabular-nums; letter-spacing:1px; line-height:1.2; }
    .timer-badge { font-size:10px; font-weight:600; background:rgba(255,255,255,0.25); padding:4px 10px; border-radius:20px; white-space:nowrap; }

    .history-section { margin:0 20px 100px; }
    .history-section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .history-section-title { font-size:16px; font-weight:700; color:var(--dark); margin:0; }
    .history-section-link { font-size:12px; font-weight:600; color:var(--primary); text-decoration:none; }
    .history-card { background:var(--white); border-radius:14px; padding:14px 16px; margin-bottom:10px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 6px rgba(0,0,0,0.04); border:1px solid var(--gray-light); cursor:pointer; }
    .history-card:active { transform:scale(0.98); }
    .hc-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
    .hc-icon-masuk { background:var(--primary-soft); color:var(--primary-dark); }
    .hc-icon-pulang { background:var(--accent-light); color:var(--accent); }
    .hc-icon-lembur-masuk { background:var(--primary-soft); color:var(--primary-dark); }
    .hc-icon-lembur-pulang { background:var(--accent-light); color:var(--accent); }
    .hc-body { flex:1; min-width:0; }
    .hc-label { font-size:13px; font-weight:600; color:var(--dark); }
    .hc-tag { font-size:9px; font-weight:700; padding:2px 7px; border-radius:6px; text-transform:uppercase; letter-spacing:0.5px; }
    .hc-tag-success { background:var(--success-light); color:var(--success); }
    .hc-tag-danger { background:var(--danger-light); color:var(--danger); }
    .hc-tag-warning { background:var(--warning-light); color:var(--warning); }
    .hc-time { font-size:18px; font-weight:800; color:var(--dark); font-variant-numeric:tabular-nums; line-height:1.2; }
    .hc-right { flex-shrink:0; }
    .hc-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:4px; }
    .hc-dot-pending { background:#f59e0b; }
    .history-empty { text-align:center; padding:40px 20px; color:var(--gray); background:var(--white); border-radius:14px; }
    .history-empty i { font-size:32px; opacity:0.25; display:block; margin-bottom:10px; }
    .history-empty p { font-size:13px; margin:0; }

    .info-carousel { margin:0 0 20px; position:relative; overflow:hidden; }
    .carousel-track { display:flex; will-change:transform; user-select:none; -webkit-user-select:none; padding-left:16px; }
    .carousel-slide { min-width:calc(100% - 32px); flex-shrink:0; cursor:pointer; padding-right:12px; box-sizing:border-box; align-self:flex-start; aspect-ratio:8/3; overflow:hidden; position:relative; }
    .slide-content { position:absolute; top:0; left:0; right:12px; bottom:0; border-radius:16px; padding:16px; display:flex; gap:14px; align-items:center; color:#fff; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow:hidden; box-sizing:border-box; }
    .slide-icon { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; background:rgba(255,255,255,0.2); color:#fff; }
    .slide-body { flex:1; min-width:0; }
    .slide-tag-row { display:flex; align-items:center; gap:8px; margin-bottom:3px; flex-wrap:wrap; }
    .slide-tag { font-size:9px; font-weight:700; padding:2px 7px; border-radius:6px; text-transform:uppercase; letter-spacing:0.3px; background:rgba(255,255,255,0.2); color:#fff; }
    .slide-date { font-size:10px; color:rgba(255,255,255,0.7); }
    .slide-title { font-size:13px; font-weight:700; color:#fff; margin-bottom:2px; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .slide-desc { font-size:11px; color:rgba(255,255,255,0.8); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .slide-link { font-size:11px; font-weight:600; margin-top:4px; color:rgba(255,255,255,0.9); }
    .slide-image { position:absolute; top:0; left:0; right:12px; bottom:0; border-radius:16px; background-size:cover; background-position:center; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.06); }
    .carousel-dots { display:flex; justify-content:center; gap:6px; margin-top:10px; }
    .dot { width:7px; height:7px; border-radius:50%; background:var(--gray-light); cursor:pointer; transition:all 0.2s; }
    .dot.active { background:var(--primary); width:20px; border-radius:4px; }

    .ql-content { font-size:14px; line-height:1.8; color:var(--dark); word-wrap:break-word; overflow-wrap:break-word; word-break:break-word; text-align:justify; }
    .ql-content a { color:var(--primary); text-decoration:underline; word-break:break-all; }
    .ql-content ul, .ql-content ol { padding-left:20px; margin:8px 0; }
    .ql-content ul { list-style:disc; }
    .ql-content ol { list-style:decimal; }
    .ql-content li { margin-bottom:4px; padding-left:4px; }
    .ql-content p { margin-bottom:8px; }
    .ql-content h1, .ql-content h2, .ql-content h3 { font-weight:700; color:var(--dark); margin:12px 0 6px; }
    .ql-content h1 { font-size:20px; }
    .ql-content h2 { font-size:17px; }
    .ql-content h3 { font-size:15px; }
    .ql-content blockquote { border-left:3px solid var(--primary); padding:6px 12px; margin:8px 0; color:var(--gray); font-style:italic; }
    .ql-content img { max-width:100%; border-radius:8px; margin:10px 0; }
    .ql-content strong { font-weight:700; }
    .ql-content em { font-style:italic; }
    [data-theme="dark"] .ql-content, [data-theme="dark"] .ql-content * { color:var(--dark)!important; }

    .badge-baru { position:absolute; top:8px; right:8px; z-index:2; background:var(--accent); color:#fff; font-size:9px; font-weight:700; padding:4px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px; animation:baruBlink 1s ease-in-out infinite; }
    @keyframes baruBlink { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.92)} }
</style>

@section('content')

{{-- ═══════════ CARD ABSENSI ═══════════ --}}
<div class="attendance-card" style="padding:0; overflow:hidden; border:none; box-shadow:0 8px 30px rgba(0,0,0,0.08);">
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
    <div style="height:1px; background:var(--gray-light); margin:0 20px;"></div>
    <div style="display:flex; gap:10px; padding:16px 20px;">
        <button class="{{ !$masukDisabled ? 'absen-btn-active' : '' }}" id="btnMasuk"
            style="flex:1; height:60px; border-radius:16px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; font-size:15px; font-weight:700;
            {{ $masukDisabled ? 'background:var(--gray-light); color:var(--gray); opacity:0.6; cursor:not-allowed;' : 'background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; box-shadow:0 4px 14px rgba(90,182,234,0.3);' }}"
            @if(!$masukDisabled)
                @if($user->can_shift && $shifts->count() > 0)
                    onclick="App.setJenis('masuk'); App.setLembur(false); App.openModal('shiftPickerModal')"
                @else
                    onclick="App.setJenis('masuk'); App.setLembur(false); App.openPresensi()"
                @endif
            @endif
            {{ $masukDisabled ? 'disabled' : '' }}>
            <i class="fas {{ $sudahPresensiMasuk ? 'fa-check-circle' : 'fa-arrow-right-to-bracket' }}" style="font-size:20px;"></i>
            @if($disablePresensiLibur ?? false) Libur @else {{ $sudahPresensiMasuk ? 'Masuk' : 'Masuk' }} @endif
        </button>
        <button class="{{ !$pulangDisabled ? 'absen-btn-active' : '' }}" id="btnPulang"
            style="flex:1; height:60px; border-radius:16px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; font-size:15px; font-weight:700;
            {{ $pulangDisabled ? 'background:var(--gray-light); color:var(--gray); opacity:0.6; cursor:not-allowed;' : 'background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; box-shadow:0 4px 14px rgba(245,158,11,0.3);' }}"
            @if(!$pulangDisabled) onclick="App.handlePulang()" @endif
            {{ $pulangDisabled ? 'disabled' : '' }}>
            <i class="fas {{ $sudahPresensiPulang ? 'fa-check-circle' : 'fa-arrow-right-from-bracket' }}" style="font-size:20px;"></i>
            @if($disablePresensiLibur ?? false) Libur @else {{ $sudahPresensiPulang ? 'Pulang' : 'Pulang' }} @endif
        </button>
    </div>
</div>

{{-- ═══════════ TIMER JAM KERJA ═══════════ --}}
@if($enableWorkTimer && $sudahPresensiMasuk && $jamMasukHariIni)
<div class="work-timer-card {{ $timerColor }}" id="workTimerBanner"
    data-stopped="{{ $pulangRec ? '1' : '0' }}"
    data-pulang-jam="{{ $pulangRec->jam ?? '' }}">
    <div class="timer-icon-circle">
        <i class="fas {{ $pulangRec ? 'fa-check' : ($isFulfilled ? 'fa-circle-check' : 'fa-stopwatch') }}"></i>
    </div>
    <div class="timer-info">
        <div class="timer-label" id="workTimerLabel">{{ $pulangRec ? 'Total jam kerja hari ini' : ($isFulfilled ? 'Jam kerja terpenuhi' : 'Jam kerja berjalan') }}</div>
        <div class="timer-clock-text" id="workTimerClock">{{ $elapsedStr }}</div>
    </div>
    <div class="timer-badge" id="workTimerBadge">{{ $pulangRec ? 'Selesai' : ($isFulfilled ? '✓ Terpenuhi' : 'Berjalan') }}</div>
</div>
@endif

{{-- ═══════════ CAROUSEL PENGUMUMAN ═══════════ --}}
@if(isset($pengumumans) && $pengumumans->count() > 0)
<div class="info-carousel" id="infoCarousel">
    <div class="carousel-track" id="carouselTrack">
        @foreach($pengumumans as $pm)
        @php
            $pmOpt = \App\Models\Pengumuman::jenisOptions()[$pm->jenis] ?? ['icon'=>'fa-circle-info','color'=>'#64748b','label'=>$pm->jenis,'gradient'=>['#64748b','#475569']];
            $g1 = $pmOpt['gradient'][0]; $g2 = $pmOpt['gradient'][1];
        @endphp
        <div class="carousel-slide" onclick="App.openInfoModal({{ $pm->id }})" style="position:relative;">
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
                <div class="slide-icon"><i class="fas {{ $pmOpt['icon'] }}"></i></div>
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
        <span class="dot {{ $i === 0 ? 'active' : '' }}" onclick="Carousel.goTo({{ $i }})"></span>
        @endforeach
    </div>
    @endif
</div>

{{-- Modal Info Detail --}}
@foreach($pengumumans as $pm)
<div id="infoModal{{ $pm->id }}" style="display:none; position:fixed; inset:0; z-index:100; background:var(--card-bg);">
    <div style="display:flex; flex-direction:column; height:100%;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
            <button onclick="App.closeInfoModal({{ $pm->id }})" style="background:none; border:none; color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px; font-weight:500;">
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

{{-- ═══════════ FLOATING LEMBUR ═══════════ --}}
@if(!$sudahLemburMasuk)
<button class="lembur-fab lembur-idle" onclick="App.setJenis('masuk'); App.setLembur(true); App.openPresensi()">
    <div class="lembur-fab-icon"><i class="fas fa-bolt"></i></div>
    <div class="lembur-fab-text">Mulai Lembur</div>
</button>
@elseif(!$sudahLemburPulang)
@php
    $lemburMasukRecord = \App\Models\Presensi::where('user_id', Auth::id())
        ->where('tanggal', now()->format('Y-m-d'))
        ->where('jenis', 'masuk')->where('is_lembur', true)->first();
@endphp
<button class="lembur-fab lembur-active" onclick="App.openLemburConfirm()">
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

{{-- ═══════════ RIWAYAT HARI INI ═══════════ --}}
<div class="history-section">
    <div class="history-section-header">
        <h5 class="history-section-title">Riwayat Hari Ini</h5>
        <a href="{{ route('pegawai.riwayat') }}" class="history-section-link">Lihat Semua <i class="fas fa-chevron-right" style="font-size:10px"></i></a>
    </div>
    @forelse($riwayatHariIni as $p)
    @php
        $isLembur = $p->is_lembur;
        $isMasuk = $p->jenis === 'masuk';
        $iconCls = $isLembur ? ($isMasuk ? 'hc-icon-lembur-masuk' : 'hc-icon-lembur-pulang') : ($isMasuk ? 'hc-icon-masuk' : 'hc-icon-pulang');
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

{{-- ═══════════ MODAL DETAIL RIWAYAT ═══════════ --}}
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
            <div style="flex:1; overflow-y:auto; padding:16px;">
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
            <div style="padding:12px 16px; border-top:1px solid var(--card-border); flex-shrink:0;">
                <button type="button" data-bs-dismiss="modal" style="width:100%; padding:14px; background:var(--gray-light); color:var(--dark); border:none; border-radius:14px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <i class="fas fa-chevron-left" style="font-size:12px;"></i> Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ═══════════ MODAL KONFIRMASI LEMBUR ═══════════ --}}
<div id="confirmLemburModal" style="display:none; position:fixed; inset:0; z-index:1060; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)App.closeModal('confirmLemburModal')">
    <div style="background:var(--card-bg); border-radius:20px; padding:24px; width:90%; max-width:340px; text-align:center;">
        <div style="width:56px;height:56px;background:rgba(16,185,129,0.12);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <i class="fas fa-bolt" style="font-size:24px;color:#10b981;"></i>
        </div>
        <h5 style="font-weight:700; font-size:16px; margin-bottom:6px; color:var(--dark);">Selesai Lembur?</h5>
        <p style="font-size:13px; color:var(--gray); margin-bottom:6px; line-height:1.5;">Anda akan mengakhiri sesi lembur dan melakukan presensi pulang lembur.</p>
        <div id="lemburDurasiInfo" style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:10px; background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); margin-bottom:16px;">
            <i class="fas fa-stopwatch" style="font-size:11px; color:#10b981;"></i>
            <span style="font-size:12px; font-weight:600; color:#10b981;" id="lemburDurasiText">Durasi: -</span>
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="App.closeModal('confirmLemburModal')" style="flex:1; padding:12px; border-radius:12px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-weight:600; font-size:14px; cursor:pointer;">Batal</button>
            <button onclick="App.confirmSelesaiLembur()" style="flex:1; padding:12px; border:none; border-radius:12px; background:linear-gradient(135deg,#10b981,#059669); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">Ya, Selesai</button>
        </div>
    </div>
</div>

{{-- ═══════════ MODAL PILIH SHIFT ═══════════ --}}
@if($user->can_shift && $shifts->count() > 0)
<div id="shiftPickerModal" style="display:none; position:fixed; inset:0; z-index:1060; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)App.closeModal('shiftPickerModal')">
    <div style="background:var(--card-bg); border-radius:20px; padding:24px; width:90%; max-width:380px;">
        <h5 style="font-weight:700; font-size:16px; text-align:center; margin-bottom:16px; color:var(--dark);">Pilih Jadwal Kerja</h5>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <button onclick="App.pickShift('')" style="padding:14px; border-radius:12px; border:2px solid var(--gray-light); background:var(--white); font-size:14px; font-weight:500; cursor:pointer; text-align:left;">
                <div style="font-weight:600;">Jam Kerja Normal</div>
                <div style="font-size:12px; color:var(--gray); margin-top:2px;">07:30 - 16:00</div>
            </button>
            @foreach($shifts as $s)
            <button onclick="App.pickShift('{{ $s->id }}')" style="padding:14px; border-radius:12px; border:2px solid var(--primary); background:rgba(90,182,234,0.05); font-size:14px; font-weight:500; cursor:pointer; text-align:left;">
                <div style="font-weight:600; color:var(--primary-dark);">{{ $s->nama }}</div>
                <div style="font-size:12px; color:var(--gray); margin-top:2px;">{{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }}</div>
            </button>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ═══════════ MODAL PRESENSI (KAMERA) ═══════════ --}}
<div class="modal fade" id="presensiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-mobile" style="margin:0; max-width:none; width:100%; height:100%;">
        <div class="modal-content" style="border:none; border-radius:0; height:100vh; background:#000; display:flex; flex-direction:column; overflow:hidden;">
            <form id="formPresensi" method="POST" action="{{ route('pegawai.presensi.store') }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; height:100%;">
                @csrf
                <input type="hidden" name="jenis" id="jenisPresensi">
                <input type="hidden" name="foto" id="fotoInput">
                <input type="hidden" name="lokasi" id="lokasiInput">
                <input type="hidden" name="is_lembur" id="isLemburInput" value="0">
                <input type="hidden" name="jam_shift_id" id="jamShiftIdInput" value="">

                <div style="flex:1; position:relative; overflow:hidden; background:#000;">
                    <video id="video" autoplay playsinline muted style="width:100%; height:100%; object-fit:cover;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <button type="button" data-bs-dismiss="modal" style="position:absolute; top:12px; left:12px; z-index:20; width:40px; height:40px; border-radius:50%; background:rgba(0,0,0,0.4); border:none; color:#fff; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-xmark"></i>
                    </button>
                    <div class="face-guide"><div class="face-guide-oval" id="faceGuideOval"></div></div>
                    <div id="faceStatus" class="face-status no-face"><i class="fas fa-user-slash"></i> Wajah tidak terdeteksi</div>
                </div>

                <div style="background:var(--card-bg); flex-shrink:0; border-top-left-radius:20px; border-top-right-radius:20px; margin-top:-20px; position:relative; z-index:10; padding:16px 20px 24px;">
                    <div style="border-radius:14px; overflow:hidden; border:1px solid var(--card-border);">
                        <div id="mini-map" style="width:100%; height:100px; background:var(--gray-light);"></div>
                        <div style="padding:10px 14px; background:var(--light); text-align:center;">
                            <div id="location-address-mini" style="font-size:12px; color:var(--dark); font-weight:500; line-height:1.4;">Mendeteksi lokasi...</div>
                            <div id="locationRadiusInfo" style="font-size:10px; margin-top:4px;"></div>
                        </div>
                    </div>
                    <button type="button" class="submit-btn-large" id="btnCapture" disabled onclick="Presensi.capture()" style="width:100%; max-width:none; border-radius:14px; padding:16px; font-size:15px; box-shadow:0 4px 14px rgba(90,182,234,0.3); margin-bottom:14px;">
                        <i class="fas fa-camera" style="margin-right:8px;"></i> Ambil Foto & Absen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════ MODAL KONFIRMASI LUAR RADIUS ═══════════ --}}
<div id="confirmationModal" style="display:none; position:fixed; inset:0; z-index:1060; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)App.closeModal('confirmationModal')">
    <div style="background:var(--card-bg); border-radius:20px; width:90%; max-width:380px; overflow:hidden;">
        <div style="padding:24px 24px 0; text-align:center;">
            <div style="width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg,#f59e0b,#d97706); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fas fa-location-dot" style="font-size:22px; color:#fff;"></i>
            </div>
            <h5 style="font-weight:700; font-size:17px; color:var(--dark); margin-bottom:6px;">Di Luar Wilayah Kerja</h5>
            <p style="font-size:13px; color:var(--gray); margin-bottom:16px; line-height:1.5;">Presensi di luar radius memerlukan persetujuan admin</p>
            <div style="background:var(--light); border-radius:14px; padding:14px; text-align:left; margin-bottom:16px; border:1px solid var(--card-border);">
                <div style="display:flex; gap:16px; margin-bottom:10px;">
                    <div style="flex:1;"><div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Jenis</div><div id="confirmationJenis" style="font-size:14px; font-weight:700; color:var(--dark);"></div></div>
                    <div style="flex:1;"><div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Waktu</div><div id="confirmationWaktu" style="font-size:14px; font-weight:700; color:var(--dark);"></div></div>
                </div>
                <div><div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Lokasi</div><div id="confirmationLokasi" style="font-size:12px; color:var(--gray-dark); line-height:1.4;"></div></div>
            </div>
        </div>
        <div style="display:flex; gap:10px; padding:0 24px 24px;">
            <button type="button" onclick="App.closeModal('confirmationModal')" style="flex:1; padding:14px; border-radius:14px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-weight:600; font-size:14px; cursor:pointer;">Batal</button>
            <button type="button" onclick="Presensi.submit()" id="confirmPresensiBtn" style="flex:1; padding:14px; border-radius:14px; border:none; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">Ya, Presensi</button>
        </div>
    </div>
</div>

{{-- ═══════════ MODAL BELUM MASUK ═══════════ --}}
<div id="warningModal" style="display:none; position:fixed; inset:0; z-index:1060; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;" onclick="if(event.target===this)App.closeModal('warningModal')">
    <div style="background:var(--card-bg); border-radius:20px; padding:24px; width:90%; max-width:340px; text-align:center;">
        <div style="width:56px;height:56px;background:var(--warning-light);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <i class="fas fa-exclamation-triangle" style="font-size:24px;color:var(--warning);"></i>
        </div>
        <h5 style="font-weight:700; font-size:16px; margin-bottom:8px; color:var(--dark);">Belum Presensi Masuk</h5>
        <p style="font-size:13px; color:var(--gray); margin-bottom:16px; line-height:1.5;">Silakan lakukan presensi masuk terlebih dahulu sebelum presensi pulang.</p>
        <button onclick="App.closeModal('warningModal')" style="width:100%; padding:12px; border:none; border-radius:12px; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">Mengerti</button>
    </div>
</div>

@endsection

@push('styles')
<style>
    @keyframes pulse { 0%{transform:scale(1)} 50%{transform:scale(1.1)} 100%{transform:scale(1)} }

    .face-guide { position:absolute; inset:0; z-index:2; pointer-events:none; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .face-guide-oval { width:60%; max-width:240px; aspect-ratio:3/4; border:2px solid rgba(255,255,255,0.35); border-radius:50%; transition:border-color 0.4s, box-shadow 0.4s; }
    .face-guide-oval.detected { border-color:var(--primary); border-width:3px; box-shadow:0 0 0 4px rgba(90,182,234,0.2), 0 0 30px rgba(90,182,234,0.15); }
    .face-status { position:absolute; bottom:100px; left:50%; transform:translateX(-50%); z-index:3; padding:8px 16px; border-radius:12px; font-size:12px; font-weight:600; display:flex; align-items:center; gap:8px; white-space:nowrap; backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); transition:all 0.3s; }
    .face-status.no-face { background:rgba(30,30,30,0.7); color:rgba(255,255,255,0.8); }
    .face-status.face-ok { background:rgba(16,185,129,0.2); color:#6ee7b7; border:1px solid rgba(16,185,129,0.3); }
    .submit-btn-large:disabled { opacity:0.4; cursor:not-allowed; }

    .lembur-fab { position:fixed; bottom:90px; right:15px; z-index:50; border:none; border-radius:14px; padding:10px 14px; color:#fff; display:flex; align-items:center; gap:10px; cursor:pointer; box-shadow:0 4px 20px rgba(0,0,0,0.15); transition:transform 0.2s, box-shadow 0.2s; }
    .lembur-fab:active { transform:scale(0.95); }
    .lembur-fab-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; background:rgba(255,255,255,0.2); }
    .lembur-fab-text { display:flex; flex-direction:column; line-height:1.2; }
    .lembur-fab-label { font-size:12px; font-weight:700; }
    .lembur-fab-timer { font-size:11px; font-weight:600; opacity:0.85; font-variant-numeric:tabular-nums; }
    .lembur-idle { background:linear-gradient(135deg,#f59e0b,#d97706); }
    .lembur-idle .lembur-fab-text { font-size:13px; font-weight:700; }
    .lembur-active { background:linear-gradient(135deg,#10b981,#059669); box-shadow:0 4px 20px rgba(16,185,129,0.3); }
    .lembur-done { background:#94a3b8; cursor:default; pointer-events:none; opacity:0.6; }
    .lembur-done .lembur-fab-text { font-size:12px; font-weight:600; }
    .pulse { animation:fabPulse 2s infinite; }
    @keyframes fabPulse { 0%,100%{box-shadow:0 0 0 0 rgba(255,255,255,0.3)} 50%{box-shadow:0 0 0 6px rgba(255,255,255,0)} }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/face_detection.js" crossorigin="anonymous" defer></script>
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/dist/face-api.js" defer></script>
<script>
(function() {
    'use strict';

    // ══════════════════════════════════════════════════════════════
    // CONFIG (dari server)
    // ══════════════════════════════════════════════════════════════
    var CFG = {
        enableFace    : @json($enableFaceDetection),
        requireMasuk  : @json($requireMasukBeforePulang),
        sudahMasuk    : @json($sudahPresensiMasuk),
        sudahPulang   : @json($sudahPresensiPulang),
        jamMasuk      : @json($jamMasukHariIni ?? ''),
        jadwalMasuk   : @json($jadwalKerjaHariIni['jam_masuk'] ?? '07:30:00'),
        jadwalPulang  : @json($jadwalKerjaHariIni['jam_pulang'] ?? '16:00:00'),
        jamPulangTarget: @json($jamPulangTarget ?? '16:00:00'),
        isLibur       : @json($isLiburHariIni ?? false),
        wilayah       : @json($wilayahJson),
        profilePhoto  : @json($profilePhotoUrl),
        wilayahAlamat : @json($wilayahJson[0]['alamat'] ?? ''),
        shiftId       : @json($shiftHariIni->id ?? '')
    };

    // ══════════════════════════════════════════════════════════════
    // STATE
    // ══════════════════════════════════════════════════════════════
    var state = {
        videoStream     : null,
        mapInstance     : null,
        currentPosition : null,
        isOutsideRadius : false,
        capturedPhoto   : null,
        faceDetected    : false,
        faceDetector    : null,
        faceTimer       : null,
        faceDetecting   : false,
        faceActive      : false,
        locationWatch   : null,
        locationMarker  : null,
        workTimerInterval: null,
        workTimerFulfilled: @json(!$enableWorkTimer || (($sudahPresensiMasuk && $jamMasukHariIni) ? ($isFulfilled ?? false) : false))
    };

    // ══════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════
    function $(id) { return document.getElementById(id); }

    function parseTime(str) {
        var p = str.split(':');
        var d = new Date();
        d.setHours(parseInt(p[0]), parseInt(p[1]), parseInt(p[2] || 0), 0);
        return d;
    }

    function formatSec(sec) {
        return String(Math.floor(sec / 3600)).padStart(2, '0') + ':' +
               String(Math.floor((sec % 3600) / 60)).padStart(2, '0') + ':' +
               String(sec % 60).padStart(2, '0');
    }

    function haversine(lat1, lng1, lat2, lng2) {
        var R = 6371000, dLat = (lat2 - lat1) * Math.PI / 180, dLng = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLng/2) * Math.sin(dLng/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function profileMarkerIcon() {
        return L.divIcon({
            className: '',
            html: '<div style="display:flex;flex-direction:column;align-items:center;">' +
                '<div style="width:36px;height:36px;border-radius:50%;border:3px solid #2E97D4;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.25);background:#fff;">' +
                '<img src="' + CFG.profilePhoto + '" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.src=\'https://ui-avatars.com/api/?name=U&background=5AB6EA&color=fff&size=80\'">' +
                '</div>' +
                '<div style="width:0;height:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:8px solid #2E97D4;margin-top:-2px;"></div>' +
                '<div style="width:8px;height:8px;border-radius:50%;background:#2E97D4;opacity:0.4;margin-top:1px;"></div>' +
                '</div>',
            iconSize: [36, 54],
            iconAnchor: [18, 27]
        });
    }

    var _reportedIssues = {};
    function reportDeviceIssue(type) {
        if (_reportedIssues[type]) return;
        _reportedIssues[type] = true;
        fetch('/pegawai/report-device-issue', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ type: type })
        }).catch(function() {});
    }

    function reverseGeocode(lat, lng, el) {
        el.textContent = 'Mendeteksi alamat...';
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && data.display_name) el.textContent = data.display_name;
                else el.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
            })
            .catch(function() { el.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6); });
    }

    // ══════════════════════════════════════════════════════════════
    // APP — fungsi utama UI
    // ══════════════════════════════════════════════════════════════
    window.App = {
        openModal: function(id) { $(id).style.display = 'flex'; },
        closeModal: function(id) { $(id).style.display = 'none'; },

        setJenis: function(jenis) { var el = $('jenisPresensi'); if (el) el.value = jenis; },
        setLembur: function(val) { var el = $('isLemburInput'); if (el) el.value = val ? '1' : '0'; },

        pickShift: function(shiftId) {
            $('jamShiftIdInput').value = shiftId;
            App.closeModal('shiftPickerModal');
            setTimeout(function() { App.openPresensi(); }, 200);
        },

        openPresensi: function() {
            Presensi.checkPermissions(function() {
                var el = $('presensiModal');
                if (el) new bootstrap.Modal(el).show();
            });
        },

        handlePulang: function() {
            if (CFG.requireMasuk && !CFG.sudahMasuk) {
                App.openModal('warningModal');
                return;
            }
            App.setJenis('pulang');
            App.setLembur(false);
            if (CFG.shiftId) $('jamShiftIdInput').value = CFG.shiftId;

            if (!state.workTimerFulfilled) {
                var clockEl = $('workTimerClock');
                var waktu = clockEl ? clockEl.textContent : '';
                showPgToast('Jam kerja belum terpenuhi (' + waktu + ')', 'warning', 'Peringatan');
            }

            App.openPresensi();
        },

        openLemburConfirm: function() {
            var timerEl = $('lemburTimer');
            var durasiEl = $('lemburDurasiText');
            if (timerEl && durasiEl) durasiEl.textContent = 'Durasi: ' + timerEl.textContent;
            App.openModal('confirmLemburModal');
        },

        confirmSelesaiLembur: function() {
            App.setJenis('pulang');
            App.setLembur(true);
            App.closeModal('confirmLemburModal');
            setTimeout(function() { App.openPresensi(); }, 350);
        },

        openInfoModal: function(id) {
            $('infoModal' + id).style.display = 'block';
            var read = JSON.parse(localStorage.getItem('karisma-read-pengumuman') || '[]');
            if (read.indexOf(id) === -1) { read.push(id); localStorage.setItem('karisma-read-pengumuman', JSON.stringify(read)); }
            var badge = $('badgeBaru' + id);
            if (badge) badge.style.display = 'none';
        },

        closeInfoModal: function(id) { $('infoModal' + id).style.display = 'none'; }
    };

    // ══════════════════════════════════════════════════════════════
    // CAROUSEL
    // ══════════════════════════════════════════════════════════════
    window.Carousel = (function() {
        var current = 0, track, total, autoTimer, dragging = false, startX, currentX, baseOffset;

        function width() { return (track && track.children[0]) ? track.children[0].offsetWidth : (track ? track.parentElement.offsetWidth : 1); }
        function setPos(px, anim) {
            if (!track) return;
            track.style.transition = anim ? 'transform 0.3s ease' : 'none';
            track.style.transform = 'translateX(' + px + 'px)';
        }
        function updateDots() {
            document.querySelectorAll('#carouselDots .dot').forEach(function(d, i) { d.classList.toggle('active', i === current); });
        }
        function resetAuto() {
            if (autoTimer) clearInterval(autoTimer);
            if (total > 1) autoTimer = setInterval(function() { Carousel.goTo((current + 1) % total); }, 5000);
        }

        return {
            goTo: function(i, anim) {
                current = Math.max(0, Math.min(i, total - 1));
                setPos(-current * width(), anim !== false);
                updateDots();
                resetAuto();
            },
            init: function() {
                track = $('carouselTrack');
                total = track ? track.children.length : 0;
                if (!track || total <= 1) return;
                current = 0;
                setPos(0, false);
                resetAuto();
                track.ontouchstart = function(e) { dragging = true; startX = currentX = e.touches[0].clientX; baseOffset = -current * width(); if (autoTimer) clearInterval(autoTimer); };
                track.ontouchmove = function(e) { if (!dragging) return; currentX = e.touches[0].clientX; setPos(baseOffset + (currentX - startX), false); };
                track.ontouchend = track.ontouchcancel = function() {
                    if (!dragging) return; dragging = false;
                    var diff = currentX - startX, threshold = width() * 0.2;
                    if (diff < -threshold && current < total - 1) Carousel.goTo(current + 1);
                    else if (diff > threshold && current > 0) Carousel.goTo(current - 1);
                    else Carousel.goTo(current);
                };
                track.onmousedown = function(e) { e.preventDefault(); dragging = true; startX = currentX = e.clientX; baseOffset = -current * width(); if (autoTimer) clearInterval(autoTimer); track.style.cursor = 'grabbing'; };
                document.onmousemove = function(e) { if (dragging) { currentX = e.clientX; setPos(baseOffset + (currentX - startX), false); } };
                document.onmouseup = function() { if (!dragging) return; dragging = false; var diff = currentX - startX, threshold = width() * 0.2; if (diff < -threshold && current < total - 1) Carousel.goTo(current + 1); else if (diff > threshold && current > 0) Carousel.goTo(current - 1); else Carousel.goTo(current); if (track) track.style.cursor = 'grab'; };
                track.style.cursor = 'grab';
                track.onclick = function(e) { if (Math.abs(currentX - startX) > 10) e.stopPropagation(); };
            }
        };
    })();

    // ══════════════════════════════════════════════════════════════
    // WORK TIMER
    // ══════════════════════════════════════════════════════════════
    var _workTimerTickFn = null;

    function pauseWorkTimer() {
        if (state.workTimerInterval) { clearInterval(state.workTimerInterval); state.workTimerInterval = null; }
    }

    function resumeWorkTimer() {
        if (!_workTimerTickFn || state.workTimerInterval) return;
        state.workTimerInterval = setInterval(_workTimerTickFn, 1000);
    }

    function startWorkTimer() {
        if (state.workTimerInterval) { clearInterval(state.workTimerInterval); state.workTimerInterval = null; }

        var card = $('workTimerBanner'), clockEl = $('workTimerClock');
        if (!card || !clockEl || !CFG.jamMasuk) return;

        var stopped = card.getAttribute('data-stopped') === '1';
        var pulangJam = card.getAttribute('data-pulang-jam') || '';

        var jadwalStart = parseTime(CFG.jadwalMasuk);
        var actualStart = parseTime(CFG.jamMasuk);
        var startTime = actualStart > jadwalStart ? actualStart : jadwalStart;
        var endTime = parseTime(CFG.jadwalPulang);
        var totalTarget = Math.floor((endTime - jadwalStart) / 1000);
        if (totalTarget <= 0) totalTarget = 8 * 3600;

        if (stopped && pulangJam) {
            var pulangTime = parseTime(pulangJam);
            var elapsed = Math.max(0, Math.floor((pulangTime - startTime) / 1000));
            clockEl.textContent = formatSec(elapsed);
            state.workTimerFulfilled = elapsed >= totalTarget;
            var bdg = $('workTimerBadge');
            if (bdg) bdg.textContent = state.workTimerFulfilled ? '✓ Terpenuhi' : 'Kurang';
            return;
        }

        _workTimerTickFn = function() {
            var el = $('workTimerClock'), c = $('workTimerBanner'), lbl = $('workTimerLabel'), bdg = $('workTimerBadge');
            if (!el || !c) return;
            var elapsed = Math.max(0, Math.floor((new Date() - startTime) / 1000));
            el.textContent = formatSec(elapsed);
            if (elapsed >= totalTarget) {
                c.classList.remove('timer-yellow'); c.classList.add('timer-green');
                if (lbl) lbl.textContent = 'Jam kerja terpenuhi';
                if (bdg) bdg.textContent = '✓ Terpenuhi';
                state.workTimerFulfilled = true;
            } else {
                var sisa = totalTarget - elapsed;
                var sh = Math.floor(sisa / 3600), sm = Math.floor((sisa % 3600) / 60);
                if (lbl) lbl.textContent = 'Sisa ' + (sh > 0 ? sh + 'j ' : '') + sm + 'm';
                if (bdg) bdg.textContent = 'Berjalan';
                state.workTimerFulfilled = false;
            }
        };
        _workTimerTickFn();
        state.workTimerInterval = setInterval(_workTimerTickFn, 1000);
    }

    function startLemburTimer() {
        var el = $('lemburTimer');
        if (!el) return;
        var startStr = el.getAttribute('data-start');
        if (!startStr) return;
        var start = parseTime(startStr);
        function tick() {
            var diff = Math.max(0, Math.floor((new Date() - start) / 1000));
            el.textContent = formatSec(diff);
        }
        tick();
        setInterval(tick, 1000);
    }

    // ══════════════════════════════════════════════════════════════
    // PRESENSI — kamera, lokasi, face detection, submit
    // ══════════════════════════════════════════════════════════════
    window.Presensi = {
        checkPermissions: function(onReady) {
            var issues = [], checks = 0, total = 2;
            function done() {
                if (++checks < total) return;
                if (issues.length === 0) { onReady(); return; }
                issues.forEach(function(i) { reportDeviceIssue(i === 'camera' ? 'camera_blocked' : 'location_blocked'); });
                Presensi.showPermissionError(issues, onReady);
            }
            if (navigator.permissions && navigator.permissions.query) {
                navigator.permissions.query({ name: 'camera' }).then(function(r) { if (r.state === 'denied') issues.push('camera'); done(); }).catch(done);
                navigator.permissions.query({ name: 'geolocation' }).then(function(r) { if (r.state === 'denied') issues.push('location'); done(); }).catch(done);
            } else { done(); done(); }
            if ('Notification' in window && Notification.permission === 'default') Notification.requestPermission();
        },

        showPermissionError: function(issues, onReady) {
            var existing = $('permissionModal');
            if (existing) existing.remove();
            var items = '';
            if (issues.indexOf('camera') !== -1)
                items += '<div style="display:flex;align-items:center;gap:12px;padding:12px;border:1.5px solid rgba(239,68,68,0.2);border-radius:12px;background:rgba(239,68,68,0.04);"><div style="width:40px;height:40px;border-radius:10px;background:rgba(239,68,68,0.1);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;"><i class="fas fa-camera"></i></div><div><div style="font-size:13px;font-weight:600;color:var(--dark);">Kamera diblokir</div><div style="font-size:11px;color:var(--gray);">Izinkan akses kamera di pengaturan browser</div></div></div>';
            if (issues.indexOf('location') !== -1)
                items += '<div style="display:flex;align-items:center;gap:12px;padding:12px;border:1.5px solid rgba(245,158,11,0.2);border-radius:12px;background:rgba(245,158,11,0.04);margin-top:8px;"><div style="width:40px;height:40px;border-radius:10px;background:rgba(245,158,11,0.1);color:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;"><i class="fas fa-location-dot"></i></div><div><div style="font-size:13px;font-weight:600;color:var(--dark);">Lokasi diblokir</div><div style="font-size:11px;color:var(--gray);">Izinkan akses lokasi di pengaturan browser</div></div></div>';
            var modal = document.createElement('div');
            modal.id = 'permissionModal';
            modal.style.cssText = 'display:flex;position:fixed;inset:0;z-index:1060;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:16px;';
            modal.innerHTML = '<div style="background:var(--card-bg);border-radius:20px;width:100%;max-width:380px;overflow:hidden;"><div style="padding:24px 24px 0;text-align:center;"><div style="width:56px;height:56px;border-radius:14px;background:rgba(239,68,68,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;"><i class="fas fa-shield-halved" style="font-size:22px;color:#ef4444;"></i></div><h5 style="font-weight:700;font-size:17px;color:var(--dark);margin-bottom:6px;">Izin Diperlukan</h5><p style="font-size:13px;color:var(--gray);margin-bottom:16px;line-height:1.5;">Untuk absen, aplikasi membutuhkan akses berikut:</p></div><div style="padding:0 24px;">' + items + '</div><div style="padding:16px 24px 24px;display:flex;gap:10px;margin-top:8px;"><button onclick="document.getElementById(\'permissionModal\').remove()" style="flex:1;padding:14px;border-radius:14px;border:1px solid var(--card-border);background:var(--card-bg);color:var(--dark);font-weight:600;font-size:14px;cursor:pointer;">Tutup</button><button onclick="document.getElementById(\'permissionModal\').remove();Presensi.retryPermissions()" style="flex:1;padding:14px;border-radius:14px;border:none;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;font-weight:600;font-size:14px;cursor:pointer;">Coba Lagi</button></div></div>';
            document.body.appendChild(modal);
        },

        retryPermissions: function() {
            var promises = [];
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
                promises.push(navigator.mediaDevices.getUserMedia({ video: true }).then(function(s) { s.getTracks().forEach(function(t) { t.stop(); }); }).catch(function() {}));
            if (navigator.geolocation)
                promises.push(new Promise(function(resolve) { navigator.geolocation.getCurrentPosition(resolve, resolve, { timeout: 5000 }); }));
            Promise.all(promises).then(function() { App.openPresensi(); });
        },

        initModal: function() {
            pauseWorkTimer();
            Presensi.initCamera();
            Presensi.initLocation();
            setTimeout(function() { if (state.mapInstance) try { state.mapInstance.invalidateSize(); } catch(e) {} }, 500);
            setTimeout(function() { if (state.mapInstance) try { state.mapInstance.invalidateSize(); } catch(e) {} }, 1500);
        },

        cleanupModal: function() {
            Presensi.stopFace();
            if (state.videoStream) {
                state.videoStream.getTracks().forEach(function(t) { t.stop(); });
                state.videoStream = null;
            }
            if (state.locationWatch !== null) {
                navigator.geolocation.clearWatch(state.locationWatch);
                state.locationWatch = null;
            }
            if (state.mapInstance) {
                try { state.mapInstance.remove(); } catch(e) {}
                state.mapInstance = null;
            }
            state.locationMarker = null;
            state.currentPosition = null;
            state.capturedPhoto = null;
            state.isOutsideRadius = false;
            var btn = $('btnCapture');
            if (btn) { btn.innerHTML = '<i class="fas fa-camera" style="margin-right:8px;"></i> Ambil Foto & Absen'; btn.disabled = true; }
            resumeWorkTimer();
        },

        initCamera: function() {
            var video = $('video');
            if (!video) return;

            Presensi.stopFace();
            if (state.videoStream) { state.videoStream.getTracks().forEach(function(t) { t.stop(); }); state.videoStream = null; }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { Presensi.showCameraError(); return; }

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }, audio: false })
                .then(function(stream) {
                    state.videoStream = stream;
                    video.srcObject = stream;
                    video.muted = true;
                    return video.play();
                })
                .then(function() {
                    if (CFG.enableFace) {
                        Presensi.initFace();
                    } else {
                        var btn = $('btnCapture');
                        if (btn) btn.disabled = false;
                        state.faceDetected = true;
                        var st = $('faceStatus');
                        if (st) { st.className = 'face-status no-face'; st.style.background = 'rgba(255,255,255,0.15)'; st.style.color = 'rgba(255,255,255,0.7)'; st.innerHTML = '<i class="fas fa-user"></i> Letakkan wajah di dalam lingkaran'; }
                    }
                })
                .catch(function(err) {
                    console.error('Camera error:', err);
                    reportDeviceIssue('camera_error');
                    Presensi.showCameraError();
                });
        },

        showCameraError: function() {
            var video = $('video');
            if (!video) return;
            var parent = video.parentElement;
            var existing = $('cameraErrorOverlay');
            if (existing) existing.remove();
            var overlay = document.createElement('div');
            overlay.id = 'cameraErrorOverlay';
            overlay.style.cssText = 'position:absolute;inset:0;z-index:15;background:rgba(0,0,0,0.85);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;color:#fff;';
            overlay.innerHTML = '<i class="fas fa-camera" style="font-size:32px;opacity:0.3;"></i><div style="font-size:14px;font-weight:600;">Kamera tidak dapat diakses</div><div style="font-size:11px;color:rgba(255,255,255,0.6);text-align:center;padding:0 20px;">Pastikan izin kamera diaktifkan, lalu coba lagi</div><button onclick="document.getElementById(\'cameraErrorOverlay\').remove();Presensi.initCamera()" style="margin-top:8px;padding:10px 24px;border-radius:12px;border:none;background:var(--primary);color:#fff;font-weight:600;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;"><i class="fas fa-rotate-right"></i> Coba Lagi</button>';
            parent.appendChild(overlay);
        },

        initFace: function() {
            var video = $('video'), btn = $('btnCapture'), statusEl = $('faceStatus');
            state.faceDetected = false;
            state.faceActive = false;
            if (state.faceTimer) { clearInterval(state.faceTimer); state.faceTimer = null; }
            if (state.faceDetector) { try { state.faceDetector.close(); } catch(e) {} state.faceDetector = null; }
            if (btn) btn.disabled = true;
            if (statusEl) { statusEl.className = 'face-status no-face'; statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...'; }

            var mpTimeout = null;
            if (typeof FaceDetection !== 'undefined') {
                try {
                    state.faceDetector = new FaceDetection({ locateFile: function(f) { return 'https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/' + f; } });
                    state.faceDetector.setOptions({ model: 'short', minDetectionConfidence: 0.6 });
                    mpTimeout = setTimeout(function() {
                        if (!state.faceActive) {
                            if (state.faceDetector) { try { state.faceDetector.close(); } catch(e) {} state.faceDetector = null; }
                            if (state.faceTimer) { clearInterval(state.faceTimer); state.faceTimer = null; }
                            Presensi.tryFaceApi(video, btn, statusEl);
                        }
                    }, 10000);
                    state.faceDetector.onResults(function(results) {
                        if (!state.faceActive) { state.faceActive = true; if (mpTimeout) clearTimeout(mpTimeout); }
                        state.faceDetecting = false;
                        if (!state.videoStream) return;
                        var found = results.detections && results.detections.length > 0 && Presensi.isFaceInGuide(results.detections[0].boundingBox, video);
                        if (found !== state.faceDetected) { state.faceDetected = found; Presensi.updateFaceUI(found); }
                    });
                    state.faceTimer = setInterval(function() {
                        if (!state.videoStream || !state.faceDetector || state.faceDetecting) return;
                        if (video.readyState < 2 || video.paused) return;
                        state.faceDetecting = true;
                        state.faceDetector.send({ image: video }).catch(function() { state.faceDetecting = false; });
                    }, 300);
                    return;
                } catch(e) {}
            }
            Presensi.tryFaceApi(video, btn, statusEl);
        },

        tryFaceApi: function(video, btn, statusEl) {
            if (statusEl) { statusEl.className = 'face-status no-face'; statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat model...'; }
            if (typeof faceapi !== 'undefined') {
                var faceApiTimeout = setTimeout(function() {
                    if (!state.faceActive) Presensi.showFaceError(statusEl);
                }, 15000);
                (async function() {
                    try {
                        await faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/model/');
                        clearTimeout(faceApiTimeout);
                        state.faceActive = true;
                        var opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.3 });
                        state.faceTimer = setInterval(async function() {
                            if (!state.videoStream || state.faceDetecting) return;
                            if (video.readyState < 2 || video.paused) return;
                            state.faceDetecting = true;
                            try {
                                var dets = await faceapi.detectAllFaces(video, opts);
                                var found = dets.length > 0 && Presensi.isFaceInGuide(dets[0].box, video);
                                if (found !== state.faceDetected) { state.faceDetected = found; Presensi.updateFaceUI(found); }
                            } catch(e) {}
                            state.faceDetecting = false;
                        }, 500);
                    } catch(e) { clearTimeout(faceApiTimeout); Presensi.showFaceError(statusEl); }
                })();
                return;
            }
            Presensi.showFaceError(statusEl);
        },

        showFaceError: function(statusEl) {
            Presensi.stopFace();
            reportDeviceIssue('face_detection_error');
            var btn = $('btnCapture');
            if (btn) btn.disabled = true;
            if (statusEl) { statusEl.className = 'face-status no-face'; statusEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Face detection gagal. <span onclick="location.reload()" style="text-decoration:underline;cursor:pointer;font-weight:700;">Refresh</span>'; }
        },

        isFaceInGuide: function(bb, video) {
            var oval = $('faceGuideOval');
            if (!oval || !video.videoWidth) return true;
            var gr = oval.getBoundingClientRect(), vr = video.getBoundingClientRect();
            var fX, fY, fW, fH;
            if (bb.xCenter !== undefined) {
                fW = bb.width * vr.width; fH = bb.height * vr.height;
                fX = vr.left + bb.xCenter * vr.width - fW / 2;
                fY = vr.top + bb.yCenter * vr.height - fH / 2;
            } else {
                var sx = vr.width / video.videoWidth, sy = vr.height / video.videoHeight;
                fW = bb.width * sx; fH = bb.height * sy;
                fX = vr.left + bb.x * sx; fY = vr.top + bb.y * sy;
            }
            var m = 20;
            return fX >= (gr.left - m) && fY >= (gr.top - m) && (fX + fW) <= (gr.right + m) && (fY + fH) <= (gr.bottom + m);
        },

        updateFaceUI: function(detected) {
            var st = $('faceStatus'), btn = $('btnCapture'), ov = $('faceGuideOval');
            if (st) { st.className = detected ? 'face-status face-ok' : 'face-status no-face'; st.innerHTML = detected ? '<i class="fas fa-user-check"></i> Wajah terdeteksi' : '<i class="fas fa-user-slash"></i> Posisikan wajah dalam lingkaran'; }
            if (ov) ov.classList.toggle('detected', detected);
            if (btn) btn.disabled = !detected;
        },

        stopFace: function() {
            if (state.faceTimer) { clearInterval(state.faceTimer); state.faceTimer = null; }
            state.faceDetecting = false;
            if (state.faceDetector) { try { state.faceDetector.close(); } catch(e) {} state.faceDetector = null; }
            state.faceDetected = false;
            var ov = $('faceGuideOval');
            if (ov) ov.classList.remove('detected');
            var btn = $('btnCapture');
            if (btn) btn.disabled = true;
        },

        initLocation: function() {
            var mapEl = $('mini-map');
            if (mapEl && window.L && !state.mapInstance) {
                var dLat = CFG.wilayah.length > 0 ? CFG.wilayah[0].lat : 3.3;
                var dLng = CFG.wilayah.length > 0 ? CFG.wilayah[0].lng : 117.6;
                state.mapInstance = L.map(mapEl, { zoomControl: false, attributionControl: false, dragging: false, scrollWheelZoom: false });
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(state.mapInstance);
                state.mapInstance.setView([dLat, dLng], 15);
                setTimeout(function() { if (state.mapInstance) try { state.mapInstance.invalidateSize(); } catch(e) {} }, 200);
                setTimeout(function() { if (state.mapInstance) try { state.mapInstance.invalidateSize(); } catch(e) {} }, 800);
            }

            if (!navigator.geolocation) {
                var el = $('location-address-mini');
                if (el) el.innerHTML = '<i class="fas fa-times-circle" style="color:#ef4444;"></i> GPS tidak tersedia';
                return;
            }
            if (state.locationWatch !== null) return;

            state.locationWatch = navigator.geolocation.watchPosition(function(pos) {
                var lat = pos.coords.latitude, lng = pos.coords.longitude;
                state.currentPosition = pos;
                var inp = $('lokasiInput');
                if (inp) inp.value = lat + ',' + lng;
                if (state.mapInstance) {
                    state.mapInstance.setView([lat, lng], 17);
                    if (state.locationMarker) state.mapInstance.removeLayer(state.locationMarker);
                    state.locationMarker = L.marker([lat, lng], { icon: profileMarkerIcon() }).addTo(state.mapInstance);
                    try { state.mapInstance.invalidateSize(); } catch(e) {}
                }
                var inRadius = false, nearestDist = Infinity;
                for (var i = 0; i < CFG.wilayah.length; i++) {
                    var d = haversine(lat, lng, CFG.wilayah[i].lat, CFG.wilayah[i].lng);
                    if (d < nearestDist) nearestDist = d;
                    if (d <= CFG.wilayah[i].radius) { inRadius = true; break; }
                }
                state.isOutsideRadius = !inRadius;
                var addrEl = $('location-address-mini'), infoEl = $('locationRadiusInfo');
                if (inRadius) {
                    var matched = null;
                    for (var j = 0; j < CFG.wilayah.length; j++) { if (haversine(lat, lng, CFG.wilayah[j].lat, CFG.wilayah[j].lng) <= CFG.wilayah[j].radius) { matched = CFG.wilayah[j]; break; } }
                    if (addrEl) addrEl.textContent = (matched && matched.alamat) ? matched.alamat : 'Lokasi terverifikasi';
                    if (infoEl) infoEl.innerHTML = '<div style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:8px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);margin-top:4px;"><i class="fas fa-check-circle" style="color:#10b981;font-size:10px;"></i><span style="font-size:10px;font-weight:600;color:#10b981;">Di dalam wilayah kerja</span></div>';
                } else {
                    if (addrEl) reverseGeocode(lat, lng, addrEl);
                    var distText = nearestDist !== Infinity ? ' (' + Math.round(nearestDist) + 'm)' : '';
                    if (infoEl) infoEl.innerHTML = '<div style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:8px;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);margin-top:4px;"><i class="fas fa-triangle-exclamation" style="color:#f59e0b;font-size:10px;"></i><span style="font-size:10px;font-weight:600;color:#f59e0b;">Anda berada di luar radius' + distText + '</span></div>';
                }
            }, function() {
                if (!state.currentPosition) {
                    reportDeviceIssue('location_error');
                    var el = $('location-address-mini');
                    if (el) el.innerHTML = '<i class="fas fa-times-circle" style="color:#ef4444;margin-right:4px;"></i> Gagal deteksi lokasi — <span onclick="Presensi.retryLocation()" style="text-decoration:underline;cursor:pointer;font-weight:600;">Coba lagi</span>';
                }
            }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 10000 });
        },

        retryLocation: function() {
            if (state.locationWatch !== null) { navigator.geolocation.clearWatch(state.locationWatch); state.locationWatch = null; }
            if (state.mapInstance) { try { state.mapInstance.remove(); } catch(e) {} state.mapInstance = null; }
            state.locationMarker = null;
            var el = $('location-address-mini');
            if (el) el.textContent = 'Mendeteksi lokasi...';
            Presensi.initLocation();
        },

        capture: function() {
            if (!state.videoStream) {
                showPgToast('Kamera belum siap. Pastikan izin kamera diaktifkan.', 'error');
                return;
            }
            if (!state.currentPosition) {
                showPgToast('Lokasi sedang dideteksi, tunggu sebentar...', 'warning');
                return;
            }
            if (!state.faceDetected) {
                showPgToast('Wajah belum terdeteksi. Posisikan wajah dalam lingkaran.', 'warning');
                return;
            }
            var jenis = $('jenisPresensi') ? $('jenisPresensi').value : '';
            var lemburVal = $('isLemburInput') ? $('isLemburInput').value : '';
            if (CFG.requireMasuk && jenis === 'pulang' && !CFG.sudahMasuk && lemburVal !== '1') {
                showPgToast('Anda belum melakukan presensi masuk hari ini.', 'error');
                return;
            }
            var video = $('video'), canvas = $('canvas');
            if (!video || !canvas || !video.videoWidth) { showPgToast('Gagal mengambil foto. Coba lagi.', 'error'); return; }
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            state.capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);

            if (state.isOutsideRadius) {
                var waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                $('confirmationJenis').textContent = jenis.toUpperCase();
                $('confirmationWaktu').textContent = waktu;
                $('confirmationLokasi').textContent = $('location-address-mini') ? $('location-address-mini').textContent : '-';
                var btn = $('confirmPresensiBtn');
                if (btn) { btn.innerHTML = '<i class="fas fa-check" style="margin-right:4px;"></i>Ya, Presensi'; btn.disabled = false; }
                App.openModal('confirmationModal');
            } else {
                Presensi.submit();
            }
        },

        submit: function() {
            if (!state.capturedPhoto) { showPgToast('Foto belum diambil.', 'error'); return; }
            var fotoInput = $('fotoInput');
            if (fotoInput) fotoInput.value = state.capturedPhoto;
            var confirmBtn = $('confirmPresensiBtn');
            if (confirmBtn) { confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:4px;"></i>Memproses...'; confirmBtn.disabled = true; }
            var form = $('formPresensi');
            if (form) form.submit();
            App.closeModal('confirmationModal');
            var presensiModal = bootstrap.Modal.getInstance($('presensiModal'));
            if (presensiModal) presensiModal.hide();
        }
    };

    // ══════════════════════════════════════════════════════════════
    // DETAIL MAP MODALS
    // ══════════════════════════════════════════════════════════════
    function initDetailModals() {
        if (!window.L) return;
        @foreach($riwayatHariIni as $p)
            @if($p->lokasi)
            (function() {
                var modal = $('detailModal{{ $p->id }}');
                var status = @json($p->status);
                if (!modal) return;
                modal.addEventListener('shown.bs.modal', function() {
                    var coords = @json($p->lokasi).split(',');
                    var lat = parseFloat(coords[0]), lng = parseFloat(coords[1]);
                    var addrEl = $('locationAddress{{ $p->id }}');
                    if (isNaN(lat) || isNaN(lng)) { if (addrEl) addrEl.innerHTML = '<span>Koordinat tidak valid</span>'; return; }
                    try {
                        var map = L.map('mapDetail{{ $p->id }}').setView([lat, lng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                        L.marker([lat, lng], { icon: profileMarkerIcon() }).addTo(map);
                        this._map = map;
                        setTimeout(function() { try { map.invalidateSize(); } catch(e) {} }, 300);
                    } catch(e) {}
                    if (addrEl) {
                        if (status === 'approved' && CFG.wilayahAlamat) addrEl.textContent = CFG.wilayahAlamat;
                        else reverseGeocode(lat, lng, addrEl);
                    }
                });
                modal.addEventListener('hidden.bs.modal', function() {
                    if (this._map) { try { this._map.remove(); } catch(e) {} this._map = null; }
                });
            })();
            @endif
        @endforeach
    }

    // ══════════════════════════════════════════════════════════════
    // NETWORK DETECTION
    // ══════════════════════════════════════════════════════════════
    function initNetworkDetection() {
        var banner = document.createElement('div');
        banner.id = 'offlineBanner';
        banner.style.cssText = 'display:none;position:fixed;top:0;left:0;right:0;z-index:999;background:#ef4444;color:#fff;text-align:center;padding:10px 16px;font-size:13px;font-weight:600;';
        banner.innerHTML = '<i class="fas fa-wifi" style="margin-right:6px;"></i> Tidak ada jaringan — presensi dinonaktifkan';
        document.body.appendChild(banner);

        var btns = ['btnMasuk', 'btnPulang'];
        var saved = {};

        function disable() {
            banner.style.display = 'block';
            btns.forEach(function(id) { var b = $(id); if (b) { saved[id] = b.disabled; b.disabled = true; b.style.opacity = '0.4'; b.style.pointerEvents = 'none'; } });
            document.querySelectorAll('.lembur-fab').forEach(function(f) { f.style.opacity = '0.4'; f.style.pointerEvents = 'none'; });
        }
        function enable() {
            banner.style.display = 'none';
            btns.forEach(function(id) { var b = $(id); if (b) { b.disabled = saved[id] || false; b.style.opacity = b.disabled ? '0.6' : '1'; b.style.pointerEvents = ''; } });
            document.querySelectorAll('.lembur-fab').forEach(function(f) { f.style.opacity = '1'; f.style.pointerEvents = ''; });
        }

        if (!navigator.onLine) disable();
        window.addEventListener('offline', disable);
        window.addEventListener('online', enable);
    }

    // ══════════════════════════════════════════════════════════════
    // PUSH NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════
    function initReminders() {
        if (!('Notification' in window) || CFG.isLibur) return;
        if (Notification.permission === 'default') Notification.requestPermission();

        var masukTime = parseTime(CFG.jadwalMasuk);
        var pulangTime = parseTime(CFG.jadwalPulang);
        var reminderMasuk = new Date(masukTime.getTime() - 10 * 60000);
        var durasiKerja = pulangTime.getTime() - masukTime.getTime();
        var jamPulangAktual = pulangTime;
        if (CFG.sudahMasuk && CFG.jamMasuk) {
            var actualStart = parseTime(CFG.jamMasuk);
            if (actualStart > masukTime) jamPulangAktual = new Date(actualStart.getTime() + durasiKerja);
        }

        function sendNotif(title, body, tag) {
            if (Notification.permission !== 'granted') return;
            if (sessionStorage.getItem('notif-' + tag)) return;
            new Notification(title, { body: body, icon: '{{ asset("public/pwa/icons/icon-192x192.png") }}', tag: tag });
            sessionStorage.setItem('notif-' + tag, '1');
        }

        function check() {
            var n = new Date(), today = n.toISOString().split('T')[0];
            if (!CFG.sudahMasuk && n >= reminderMasuk && n < masukTime)
                sendNotif('Reminder Presensi Masuk', 'Waktu masuk kerja 10 menit lagi (' + CFG.jadwalMasuk.substring(0, 5) + ')', today + '-masuk-reminder');
            if (CFG.sudahMasuk && !CFG.sudahPulang && n >= new Date(jamPulangAktual.getTime() - 10 * 60000) && n < jamPulangAktual)
                sendNotif('Reminder Presensi Pulang', 'Waktu pulang kerja 10 menit lagi', today + '-pulang-reminder');
            if (CFG.sudahMasuk && !CFG.sudahPulang && n >= jamPulangAktual)
                sendNotif('Waktunya Pulang', 'Jam kerja Anda sudah terpenuhi. Jangan lupa presensi pulang!', today + '-pulang-now');
        }
        check();
        setInterval(check, 30000);
    }

    // ══════════════════════════════════════════════════════════════
    // INIT — satu kali saat DOM ready
    // ══════════════════════════════════════════════════════════════
    var _initialized = false;
    function init() {
        if (_initialized) return;
        _initialized = true;

        // Bind presensi modal PERTAMA — ini yang paling kritis
        var presensiModal = $('presensiModal');
        if (presensiModal) {
            presensiModal.addEventListener('shown.bs.modal', Presensi.initModal);
            presensiModal.addEventListener('hidden.bs.modal', Presensi.cleanupModal);
        }

        // Timer & fitur lain dibungkus try-catch — jangan sampai crash menghalangi presensi
        try { Carousel.init(); } catch(e) { console.error('Carousel init error:', e); }
        try { startWorkTimer(); } catch(e) { console.error('WorkTimer error:', e); }
        try { startLemburTimer(); } catch(e) { console.error('LemburTimer error:', e); }
        try { initDetailModals(); } catch(e) { console.error('DetailModals error:', e); }
        try { initNetworkDetection(); } catch(e) { console.error('Network error:', e); }
        try { initReminders(); } catch(e) { console.error('Reminders error:', e); }

        // Hide badges pengumuman yang sudah dibaca
        var read = JSON.parse(localStorage.getItem('karisma-read-pengumuman') || '[]');
        read.forEach(function(id) { var b = $('badgeBaru' + id); if (b) b.style.display = 'none'; });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
</script>
@endpush
