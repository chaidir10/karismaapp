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
        cursor:pointer; transition:transform 0.15s;
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
    .hc-tag-reguler { background:var(--primary-soft); color:var(--primary-dark); }
    .hc-tag-lembur { background:var(--accent-light); color:var(--accent); }
    .hc-time { font-size:20px; font-weight:800; color:var(--dark); font-variant-numeric:tabular-nums; line-height:1.2; }
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
        color:#fff;
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
        border:1.5px solid var(--primary-light);
    }

    .carousel-dots { display:flex; justify-content:center; gap:6px; margin-top:10px; }
    .dot { width:7px; height:7px; border-radius:50%; background:var(--gray-light); cursor:pointer; transition:all 0.2s; }
    .dot.active { background:var(--primary); width:20px; border-radius:4px; }

    .ql-content img { max-width:100%; border-radius:8px; margin:10px 0; }
    .ql-content { text-align:justify; }
</style>

@section('content')

<!-- Card Absensi -->
<div class="attendance-card" style="padding:0; overflow:hidden; border:none; box-shadow:0 8px 30px rgba(0,0,0,0.08);">
    <!-- Info bar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; background:var(--white);">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:40px; height:40px; border-radius:12px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px;">
                <i class="far fa-calendar-alt"></i>
            </div>
            <div>
                <div style="font-size:11px; color:var(--gray); font-weight:500;">{{ now()->translatedFormat('l') }}</div>
                <div style="font-size:15px; font-weight:700; color:var(--dark); line-height:1.2;">{{ now()->translatedFormat('d F Y') }}</div>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:10px; color:var(--gray); font-weight:500;">Jadwal</div>
            <div style="font-size:13px; font-weight:700; color:var(--primary-dark);">
                @if($shiftHariIni ?? false)
                    {{ \Carbon\Carbon::parse($shiftHariIni->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($shiftHariIni->jam_pulang)->format('H:i') }}
                @else
                    07:30 - 16:00
                @endif
            </div>
            @if($shiftHariIni ?? false)
                <div style="font-size:9px; color:var(--primary); font-weight:600; margin-top:1px;">{{ $shiftHariIni->nama }}</div>
            @endif
        </div>
    </div>

    <!-- Divider -->
    <div style="height:1px; background:var(--gray-light); margin:0 20px;"></div>

    <!-- Buttons -->
    <div style="display:flex; gap:10px; padding:16px 20px;">
        <button class="{{ $sudahPresensiMasuk ? '' : 'absen-btn-active' }}"
            id="clock-in-btn"
            style="flex:1; height:52px; border-radius:14px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; font-size:14px; font-weight:700; transition:all 0.2s;
            {{ $sudahPresensiMasuk ? 'background:var(--gray-light); color:var(--gray); opacity:0.6; cursor:not-allowed;' : 'background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; box-shadow:0 4px 14px rgba(90,182,234,0.3);' }}"
            @if($user->can_shift && $shifts->count() > 0 && !$sudahPresensiMasuk)
                data-bs-toggle="modal" data-bs-target="#shiftPickerModal"
            @else
                data-bs-toggle="modal" data-bs-target="#presensiModal"
            @endif
            onclick="setJenis('masuk'); setLembur(false)"
            {{ $sudahPresensiMasuk ? 'disabled' : '' }}>
            <i class="fas {{ $sudahPresensiMasuk ? 'fa-check-circle' : 'fa-sign-in-alt' }}" style="font-size:18px;"></i>
            {{ $sudahPresensiMasuk ? 'Sudah Masuk' : 'Masuk' }}
        </button>
        <button class="{{ (!$sudahPresensiMasuk || $sudahPresensiPulang) ? '' : 'absen-btn-active' }}"
            id="clock-out-btn"
            style="flex:1; height:52px; border-radius:14px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; font-size:14px; font-weight:700; transition:all 0.2s;
            {{ (!$sudahPresensiMasuk || $sudahPresensiPulang) ? 'background:var(--gray-light); color:var(--gray); opacity:0.6; cursor:not-allowed;' : 'background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; box-shadow:0 4px 14px rgba(245,158,11,0.3);' }}"
            onclick="handlePulangWithCheck()"
            {{ !$sudahPresensiMasuk || $sudahPresensiPulang ? 'disabled' : '' }}>
            <i class="fas {{ $sudahPresensiPulang ? 'fa-check-circle' : 'fa-sign-out-alt' }}" style="font-size:18px;"></i>
            {{ $sudahPresensiPulang ? 'Sudah Pulang' : 'Pulang' }}
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
        <div class="carousel-slide" onclick="openInfoModal({{ $pm->id }})">
            @if($pm->gambar && ($pm->sembunyikan_detail ?? false))
            <div class="slide-image" style="background-image:url('{{ asset('public/storage/'.$pm->gambar) }}'); border-color:{{ $g1 }}40;"></div>
            @elseif($pm->gambar)
            <div class="slide-image" style="background-image:url('{{ asset('public/storage/'.$pm->gambar) }}'); border-color:{{ $g1 }}40;">
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
<div class="modal fade" id="infoModal{{ $pm->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-mobile">
        <div class="modal-content" style="border-radius:0; height:100vh; display:flex; flex-direction:column;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--gray-light); flex-shrink:0;">
                <h5 style="font-weight:700; font-size:16px; margin:0; color:var(--dark);">{{ \App\Models\Pengumuman::jenisOptions()[$pm->jenis]['label'] ?? 'Info' }}</h5>
                <button type="button" data-bs-dismiss="modal" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--gray); width:40px; height:40px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-times"></i></button>
            </div>
            <div style="flex:1; overflow-y:auto; padding:20px;">
                @if($pm->gambar)
                <img src="{{ asset('public/storage/'.$pm->gambar) }}" style="width:100%; border-radius:12px; margin-bottom:16px; object-fit:cover; max-height:200px;" alt="">
                @endif
                <h2 style="font-size:20px; font-weight:800; color:var(--dark); margin-bottom:8px; line-height:1.3;">{{ $pm->judul }}</h2>
                <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; margin-bottom:16px; font-size:12px; color:var(--gray);">
                    <span style="background:{{ \App\Models\Pengumuman::jenisOptions()[$pm->jenis]['color'] ?? '#64748b' }}; color:#fff; padding:3px 10px; border-radius:8px; font-weight:600; font-size:11px;">{{ \App\Models\Pengumuman::jenisOptions()[$pm->jenis]['label'] ?? $pm->jenis }}</span>
                    @if($pm->tanggal_mulai)
                    <span><i class="far fa-calendar-alt" style="margin-right:4px;"></i>{{ $pm->tanggal_mulai->translatedFormat('d F Y') }}@if($pm->tanggal_selesai && $pm->tanggal_selesai != $pm->tanggal_mulai) - {{ $pm->tanggal_selesai->translatedFormat('d F Y') }}@endif</span>
                    @endif
                    @if($pm->waktu)
                    <span><i class="far fa-clock" style="margin-right:4px;"></i>{{ \Carbon\Carbon::parse($pm->waktu)->format('H:i') }}</span>
                    @endif
                </div>
                <div style="font-size:14px; line-height:1.8; color:#334155; text-align:justify;" class="ql-content">{!! $pm->isi !!}</div>
            </div>
            <div style="padding:12px 16px; border-top:1px solid var(--gray-light); flex-shrink:0;">
                <button type="button" data-bs-dismiss="modal" style="width:100%; padding:12px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; border:none; border-radius:12px; font-weight:600; font-size:14px; cursor:pointer;">
                    <i class="fas fa-arrow-left" style="margin-right:6px;"></i> Kembali
                </button>
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
<button class="lembur-fab lembur-active" data-bs-toggle="modal" data-bs-target="#confirmLemburModal">
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
        if ($isLembur) {
            $iconCls = $isMasuk ? 'hc-icon-lembur-masuk' : 'hc-icon-lembur-pulang';
            $iconName = 'fa-bolt';
            $labelText = $isMasuk ? 'Masuk Lembur' : 'Pulang Lembur';
            $tagCls = 'hc-tag-lembur';
            $tagText = 'Lembur';
        } else {
            $iconCls = $isMasuk ? 'hc-icon-masuk' : 'hc-icon-pulang';
            $iconName = $isMasuk ? 'fa-sign-in-alt' : 'fa-sign-out-alt';
            $labelText = $isMasuk ? 'Masuk' : 'Pulang';
            $tagCls = 'hc-tag-reguler';
            $tagText = 'Reguler';
        }
    @endphp
    <div class="history-card" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
        <div class="hc-icon {{ $iconCls }}"><i class="fas {{ $iconName }}"></i></div>
        <div class="hc-body">
            <div class="hc-title-row">
                <span class="hc-label">{{ $labelText }}</span>
                <span class="hc-tag {{ $tagCls }}">{{ $tagText }}</span>
            </div>
            <div class="hc-time">{{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</div>
        </div>
        <div class="hc-right">
            <span class="hc-dot hc-dot-{{ $p->status }}"></span>
            <span class="hc-status">{{ ucfirst($p->status) }}</span>
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
<div class="modal fade detail-modal" id="detailModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-mobile">
        <div class="modal-content">
            <div class="detail-content-container">
                <div class="detail-image-container">
                    @if($p->foto)
                    <img src="{{ asset('public/storage/'.$p->foto) }}" class="detail-image" alt="Foto presensi">
                    @else
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                        <i class="fas fa-camera fa-3x text-muted"></i>
                    </div>
                    @endif
                </div>

                <div class="detail-map-container">
                    @if($p->lokasi)
                    <div id="mapDetail{{ $p->id }}" class="detail-map"></div>
                    @else
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                        <i class="fas fa-map-marker-alt fa-3x text-muted"></i>
                    </div>
                    @endif
                </div>
            </div>

            <div class="detail-info-section">
                <div class="detail-datetime-info">
                    <div class="detail-type-badge fw-bold">{{ $p->is_lembur ? 'Lembur ' : '' }}{{ ucfirst($p->jenis) }} - {{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</div>
                    <div class="detail-day">{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('l') }}, {{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d F Y') }}</div>
                </div>

                <div class="detail-location-info">
                    <div class="detail-location-address" id="locationAddress{{ $p->id }}">
                        <div class="loading-address">
                            <i class="fas fa-spinner fa-spin me-2"></i>Mendeteksi alamat...
                        </div>
                    </div>
                    @if($p->status == 'approved')
                    <div class="text-success mt-1">Presensi berhasil</div>
                    @elseif($p->status == 'pending')
                    <div class="text-warning mt-1">Menunggu persetujuan</div>
                    @else
                    <div class="text-danger mt-1">Presensi ditolak</div>
                    @endif
                </div>

                <button type="button" class="detail-back-btn" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Peringatan Jam Kerja Belum Terpenuhi --}}
<div class="modal fade" id="earlyPulangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-body p-4 text-center">
                <div style="width:56px;height:56px;background:#fef3c7;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <i class="fas fa-exclamation-triangle" style="font-size:24px;color:#f59e0b;"></i>
                </div>
                <h5 style="font-weight:700; font-size:16px; margin-bottom:8px;">Jam Kerja Belum Terpenuhi</h5>
                <p style="font-size:13px; color:var(--gray-dark); margin-bottom:16px;" id="earlyPulangMsg">Apakah Anda yakin ingin absen pulang?</p>
                <div style="display:flex; gap:10px;">
                    <button class="btn-secondary" style="flex:1;" data-bs-dismiss="modal">Batal</button>
                    <button style="flex:1; padding:12px; border:none; border-radius:12px; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-weight:600; font-size:14px; cursor:pointer;"
                        onclick="proceedPulang()">Ya, Pulang</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Selesai Lembur --}}
<div class="modal fade" id="confirmLemburModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-body p-4 text-center">
                <div style="width:56px;height:56px;background:#d1fae5;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <i class="fas fa-bolt" style="font-size:24px;color:#10b981;"></i>
                </div>
                <h5 style="font-weight:700; font-size:16px; margin-bottom:8px;">Selesai Lembur</h5>
                <p style="font-size:13px; color:var(--gray-dark); margin-bottom:16px;">Yakin ingin mengakhiri lembur?</p>
                <div style="display:flex; gap:10px;">
                    <button class="btn-secondary" style="flex:1;" data-bs-dismiss="modal">Batal</button>
                    <button style="flex:1; padding:12px; border:none; border-radius:12px; background:linear-gradient(135deg,#10b981,#059669); color:#fff; font-weight:600; font-size:14px; cursor:pointer;"
                        onclick="proceedSelesaiLembur()">Ya, Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pilih Shift --}}
@if($user->can_shift && $shifts->count() > 0)
<div class="modal fade" id="shiftPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-body p-4">
                <h5 style="font-weight:700; font-size:16px; text-align:center; margin-bottom:16px;">Pilih Jadwal Kerja</h5>
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
    </div>
</div>
@endif

<!-- Modal Presensi -->
<div class="modal fade" id="presensiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-mobile">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"> </h3>
                <button type="button" class="close-btn" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body p-0 position-relative">
                <form id="formPresensi" method="POST" action="{{ route('pegawai.presensi.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="jenis" id="jenisPresensi">
                    <input type="hidden" name="foto" id="fotoInput">
                    <input type="hidden" name="lokasi" id="lokasiInput">
                    <input type="hidden" name="is_lembur" id="isLemburInput" value="0">
                    <input type="hidden" name="jam_shift_id" id="jamShiftIdInput" value="">

                    <div class="camera-container-full">
                        <video id="video" autoplay playsinline></video>
                        <canvas id="canvas" class="d-none"></canvas>
                        <div class="face-guide">
                            <div class="face-guide-oval" id="faceGuideOval"></div>
                            <div class="face-guide-text">Posisikan wajah di dalam lingkaran</div>
                        </div>
                        <div id="faceStatus" class="face-status no-face">
                            <i class="fas fa-user-slash"></i> Wajah tidak terdeteksi
                        </div>
                    </div>

                    <div class="mini-map-wrapper">
                        <div class="mini-map-container">
                            <div id="mini-map" class="mini-map"></div>

                            <div class="location-info-mini">
                                <i class="fas fa-map-marker-alt"></i>
                                <span id="location-address-mini">Mendeteksi lokasi...</span>
                                <div id="locationRadiusInfo" class="text-sm mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="submit-btn-container">
                        <button type="button" class="submit-btn-large" onclick="captureAndProcess()">
                            <i class="fas fa-camera me-2"></i>Ambil Foto & Absen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Presensi (Luar Radius) -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none; overflow:hidden; background:var(--card-bg);">
            <div style="padding:24px 24px 0; text-align:center;">
                <div style="width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg,#f59e0b,#d97706); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                    <i class="fas fa-map-marker-alt" style="font-size:22px; color:#fff;"></i>
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
                <button type="button" data-bs-dismiss="modal" style="flex:1; padding:14px; border-radius:14px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--dark); font-weight:600; font-size:14px; cursor:pointer;">
                    Batal
                </button>
                <button type="button" onclick="prosesPresensi()" id="confirmPresensiBtn" style="flex:1; padding:14px; border-radius:14px; border:none; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">
                    Ya, Presensi
                </button>
            </div>
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
<div class="modal fade" id="warningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl">
            <div class="modal-body p-0">
                <div class="text-center p-6">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Presensi Masuk</h3>
                    <p class="text-gray-600 mb-4">Anda belum melakukan presensi masuk hari ini. Silakan lakukan presensi masuk terlebih dahulu sebelum presensi pulang.</p>
                </div>

                <div class="border-t border-gray-200">
                    <button type="button" class="w-full py-4 bg-yellow-500 text-white font-medium hover:bg-yellow-600 rounded-b-2xl transition-colors" data-bs-dismiss="modal">
                        <i class="fas fa-check mr-2"></i>Mengerti
                    </button>
                </div>
            </div>
        </div>
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

    /* Auto Close Modal Animation */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
    }

    .auto-close-modal {
        animation: slideDown 0.5s ease-in-out;
    }

    @keyframes slideDown {
        0% {
            transform: translateY(-20px);
            opacity: 0;
        }

        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .success-modal-content {
        border: 2px solid #10b981;
    }

    /* Face Guide */
    .face-guide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .face-guide-oval {
        width: 55%;
        max-width: 220px;
        aspect-ratio: 3 / 4;
        border: 3px dashed rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .face-guide-oval.detected {
        border-color: #10b981;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
    }

    .face-guide-text {
        color: rgba(255, 255, 255, 0.8);
        font-size: 11px;
        margin-top: 8px;
        text-shadow: 0 1px 3px rgba(0,0,0,0.6);
    }

    .face-status {
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        transition: all 0.3s;
    }

    .face-status.no-face {
        background: rgba(239, 68, 68, 0.85);
        color: #fff;
    }

    .face-status.face-ok {
        background: rgba(16, 185, 129, 0.85);
        color: #fff;
    }

    .submit-btn-large:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        filter: grayscale(0.5);
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
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/dist/face-api.js"></script>
<script>
    // Carousel with live drag
    var currentSlide = 0;
    var track = document.getElementById('carouselTrack');
    var totalSlides = track ? track.children.length : 0;
    var autoSlideTimer = null;
    var isDragging = false;
    var dragStartX = 0;
    var dragCurrentX = 0;
    var dragBaseOffset = 0;

    function getTrackWidth() { return track ? track.parentElement.offsetWidth : 1; }

    function setTrackPos(px, animate) {
        if (!track) return;
        track.style.transition = animate ? 'transform 0.3s ease' : 'none';
        track.style.transform = 'translateX(' + px + 'px)';
    }

    function goToSlide(i, animate) {
        currentSlide = Math.max(0, Math.min(i, totalSlides - 1));
        setTrackPos(-currentSlide * getTrackWidth(), animate !== false);
        var dots = document.querySelectorAll('#carouselDots .dot');
        dots.forEach(function(d, idx) { d.classList.toggle('active', idx === currentSlide); });
        resetAutoSlide();
    }

    function nextSlide() { goToSlide((currentSlide + 1) % totalSlides); }

    function resetAutoSlide() {
        if (autoSlideTimer) clearInterval(autoSlideTimer);
        if (totalSlides > 1) autoSlideTimer = setInterval(nextSlide, 5000);
    }

    function onDragStart(x) {
        isDragging = true;
        dragStartX = x;
        dragCurrentX = x;
        dragBaseOffset = -currentSlide * getTrackWidth();
        if (autoSlideTimer) clearInterval(autoSlideTimer);
    }

    function onDragMove(x) {
        if (!isDragging) return;
        dragCurrentX = x;
        var diff = dragCurrentX - dragStartX;
        setTrackPos(dragBaseOffset + diff, false);
    }

    function onDragEnd() {
        if (!isDragging) return;
        isDragging = false;
        var diff = dragCurrentX - dragStartX;
        var threshold = getTrackWidth() * 0.2;
        if (diff < -threshold && currentSlide < totalSlides - 1) {
            goToSlide(currentSlide + 1);
        } else if (diff > threshold && currentSlide > 0) {
            goToSlide(currentSlide - 1);
        } else {
            goToSlide(currentSlide);
        }
    }

    if (track && totalSlides > 1) {
        resetAutoSlide();

        track.addEventListener('touchstart', function(e) { onDragStart(e.touches[0].clientX); }, { passive: true });
        track.addEventListener('touchmove', function(e) { onDragMove(e.touches[0].clientX); }, { passive: true });
        track.addEventListener('touchend', onDragEnd);
        track.addEventListener('touchcancel', onDragEnd);

        track.addEventListener('mousedown', function(e) { e.preventDefault(); onDragStart(e.clientX); });
        document.addEventListener('mousemove', function(e) { if (isDragging) onDragMove(e.clientX); });
        document.addEventListener('mouseup', onDragEnd);

        track.style.cursor = 'grab';
        track.addEventListener('mousedown', function() { track.style.cursor = 'grabbing'; });
        document.addEventListener('mouseup', function() { if (track) track.style.cursor = 'grab'; });

        track.addEventListener('click', function(e) {
            if (Math.abs(dragCurrentX - dragStartX) > 10) e.stopPropagation();
        }, true);
    }

    function openInfoModal(id) {
        if (window.bootstrap && window.bootstrap.Modal) {
            new bootstrap.Modal(document.getElementById('infoModal' + id)).show();
        }
    }

    let videoStream = null;
    let mapInstance = null;
    let currentPosition = null;
    let isOutsideRadius = false;
    let capturedPhotoData = null;
    let autoCloseTimer = null;
    let faceDetectionLoop = null;
    let faceDetected = false;
    let faceModelLoaded = false;

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

    function pickShift(shiftId) {
        document.getElementById('jamShiftIdInput').value = shiftId;
        var shiftModal = bootstrap.Modal.getInstance(document.getElementById('shiftPickerModal'));
        if (shiftModal) shiftModal.hide();
        setTimeout(function() {
            var presensiModal = new bootstrap.Modal(document.getElementById('presensiModal'));
            presensiModal.show();
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

    function handlePulangWithCheck() {
        if (!sudahPresensiMasuk) {
            if (window.bootstrap?.Modal) {
                new bootstrap.Modal(document.getElementById('warningModal')).show();
            }
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
            new bootstrap.Modal(document.getElementById('earlyPulangModal')).show();
        } else {
            new bootstrap.Modal(document.getElementById('presensiModal')).show();
        }
    }

    function proceedSelesaiLembur() {
        setJenis('pulang');
        setLembur(true);

        var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmLemburModal'));
        if (confirmModal) confirmModal.hide();

        setTimeout(function() {
            new bootstrap.Modal(document.getElementById('presensiModal')).show();
        }, 300);
    }

    function proceedPulang() {
        var modal = bootstrap.Modal.getInstance(document.getElementById('earlyPulangModal'));
        if (modal) modal.hide();
        setTimeout(function() {
            new bootstrap.Modal(document.getElementById('presensiModal')).show();
        }, 300);
    }

    function handlePulangClick() {
        if (!sudahPresensiMasuk) {
            if (window.bootstrap?.Modal) {
                const warningModal = new bootstrap.Modal(document.getElementById('warningModal'));
                warningModal.show();
            } else {
                alert('Bootstrap JS belum termuat.');
            }
            return false;
        }
        setJenis('pulang');
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const presensiModal = document.getElementById('presensiModal');
        if (presensiModal) {
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

        const submitBtn = document.querySelector('.submit-btn-large');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Ambil Foto & Absen';
            submitBtn.disabled = false;
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

    function initializeCamera() {
        const video = document.getElementById('video');
        if (!video) return;

        video.muted = true;

        if (!navigator.mediaDevices?.getUserMedia) {
            showError("Browser tidak mendukung akses kamera.");
            return;
        }

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'user',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        })
        .then(stream => {
            videoStream = stream;
            video.srcObject = stream;
            return video.play().catch(() => {});
        })
        .then(() => {
            initFaceDetection();
        })
        .catch(err => {
            console.error(err);
            showError("Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan.");
        });
    }

    async function initFaceDetection() {
        const submitBtn = document.querySelector('.submit-btn-large');
        const statusEl = document.getElementById('faceStatus');

        if (typeof faceapi === 'undefined') {
            console.warn('face-api.js tidak termuat, face detection dinonaktifkan');
            if (statusEl) statusEl.style.display = 'none';
            return;
        }

        if (submitBtn) submitBtn.disabled = true;
        updateFaceStatus(false);

        if (!faceModelLoaded) {
            try {
                if (statusEl) {
                    statusEl.className = 'face-status no-face';
                    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat model...';
                }
                const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/model/';
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                faceModelLoaded = true;
            } catch (e) {
                console.error('Gagal memuat model:', e);
                if (statusEl) statusEl.style.display = 'none';
                if (submitBtn) submitBtn.disabled = false;
                return;
            }
        }

        startFaceDetectionLoop();
    }

    function startFaceDetectionLoop() {
        const video = document.getElementById('video');
        if (!video) return;

        const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.35 });
        let detecting = false;

        async function detect() {
            if (!videoStream) return;
            if (video.readyState < 2 || video.paused || detecting) {
                faceDetectionLoop = setTimeout(detect, 500);
                return;
            }

            detecting = true;
            try {
                const detections = await faceapi.detectAllFaces(video, options);
                const found = detections.length > 0;

                if (found !== faceDetected) {
                    faceDetected = found;
                    updateFaceStatus(found);
                }
            } catch (e) {
                console.error('Detect error:', e);
            }
            detecting = false;

            if (videoStream) faceDetectionLoop = setTimeout(detect, 500);
        }

        faceDetectionLoop = setTimeout(detect, 1000);
    }

    function updateFaceStatus(detected) {
        const statusEl = document.getElementById('faceStatus');
        const submitBtn = document.querySelector('.submit-btn-large');
        const ovalEl = document.getElementById('faceGuideOval');

        if (statusEl) {
            statusEl.className = detected ? 'face-status face-ok' : 'face-status no-face';
            statusEl.innerHTML = detected
                ? '<i class="fas fa-user-check"></i> Wajah terdeteksi'
                : '<i class="fas fa-user-slash"></i> Wajah tidak terdeteksi';
        }

        if (ovalEl) {
            ovalEl.classList.toggle('detected', detected);
        }

        if (submitBtn) submitBtn.disabled = !detected;
    }

    function stopFaceDetection() {
        if (faceDetectionLoop) {
            clearTimeout(faceDetectionLoop);
            faceDetectionLoop = null;
        }
        faceDetected = false;

        const ovalEl = document.getElementById('faceGuideOval');
        if (ovalEl) ovalEl.classList.remove('detected');
    }

    function initializeLocation() {
        const addrEl = document.getElementById('location-address-mini');

        if (!navigator.geolocation) {
            if (addrEl) addrEl.textContent = "Browser tidak mendukung geolokasi";
            initializeMiniMapWithDefault();
            return;
        }

        navigator.geolocation.getCurrentPosition(
            pos => {
                currentPosition = pos;
                updateLocationInfo(pos);
                initializeMiniMap(pos);
            },
            err => {
                console.error(err);
                if (addrEl) addrEl.textContent = "Gagal mendapatkan lokasi (izin ditolak / GPS mati)";
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

        if (infoEl) infoEl.style.fontSize = "10px";

        let matched = null;
        for (const w of wilayahList) {
            const distance = haversineDistance(lat, lng, w.lat, w.lng);
            if (distance <= w.radius) {
                matched = w;
                break;
            }
        }

        if (matched) {
            if (infoEl) {
                infoEl.innerHTML = '<span class="badge bg-success">✔ Anda berada di dalam wilayah kerja</span>';
                infoEl.classList.remove('text-danger', 'text-warning');
                infoEl.classList.add('text-success');
            }
            if (submitBtn) submitBtn.disabled = false;
            if (addrEl) addrEl.textContent = matched.alamat || 'Di dalam wilayah kerja';
            isOutsideRadius = false;
        } else {
            if (infoEl) {
                infoEl.innerHTML = '<span class="badge bg-warning">⚠ Anda berada di luar radius wilayah kerja</span>';
                infoEl.classList.remove('text-success');
                infoEl.classList.add('text-warning');
            }
            if (submitBtn) submitBtn.disabled = false;
            isOutsideRadius = true;

            const miniEl = document.getElementById('location-address-mini');
            if (miniEl) getAddressFromCoordinates(lat, lng, miniEl);
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
            console.error('Leaflet belum dimuat. Pastikan leaflet.js ada di layout.');
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

            L.marker([lat, lng]).addTo(mapInstance);

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

        if (!window.L) return;

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
                        L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Presensi').openPopup();
                        this._map = map;
                        setTimeout(() => { try { map.invalidateSize(); } catch(e){} }, 300);
                    } catch (e) {
                        console.error('Map error:', e);
                    }

                    if (addrEl) {
                        if (status === 'approved' && detailWilayahAlamat) {
                            addrEl.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + detailWilayahAlamat;
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
        el.innerHTML = '<div class="loading-address"><i class="fas fa-spinner fa-spin"></i><span>Mendeteksi alamat...</span></div>';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
            .then(r => {
                if (!r.ok) throw new Error(r.status);
                return r.json();
            })
            .then(data => {
                if (data && data.display_name) {
                    el.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + data.display_name;
                } else {
                    throw new Error('no data');
                }
            })
            .catch(() => {
                el.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
            });
    }

    function captureAndProcess() {
        if (!videoStream || !currentPosition) {
            showError("Kamera atau lokasi belum siap. Pastikan izin kamera & lokasi diizinkan.");
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

        if (window.bootstrap?.Modal) {
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            confirmationModal.show();
        }
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

            const confirmationModal = window.bootstrap?.Modal?.getInstance(document.getElementById('confirmationModal'));
            if (confirmationModal) confirmationModal.hide();

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