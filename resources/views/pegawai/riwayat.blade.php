@extends('layouts.pegawai')

@section('title', 'Riwayat Presensi')

@section('content')
<style>
    .riwayat-page { padding: 20px; padding-bottom: 160px; }

    /* Floating Filter */
    .filter-bar {
        position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%);
        width: calc(100% - 40px); max-width: 460px; z-index: 9;
        display: flex; align-items: center; gap: 8px;
        background: var(--card-bg); border-radius: 16px; padding: 10px 12px;
        box-shadow: 0 -2px 20px rgba(0,0,0,0.1); border: 1px solid var(--card-border);
    }
    .filter-bar select {
        border: 1px solid var(--card-border); border-radius: 10px;
        padding: 10px 12px; font-size: 13px; background: var(--card-bg); color: var(--dark);
        outline: none; flex: 1; min-width: 0;
    }
    .filter-bar select:focus { border-color: var(--primary); }
    .btn-download {
        width: 42px; height: 42px; border-radius: 10px; border: none; cursor: pointer;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
        flex-shrink: 0;
    }
    .btn-download:active { opacity: 0.85; }

    /* Date group */
    .date-group { margin-bottom: 20px; }
    .date-label {
        font-size: 13px; font-weight: 700; color: var(--gray);
        padding: 0 4px 8px; display: flex; align-items: center; gap: 8px;
    }
    .date-label::after {
        content: ''; flex: 1; height: 1px; background: var(--gray-light);
    }

    /* Cards */
    .presensi-card {
        background: var(--white); border-radius: 14px; padding: 14px 16px;
        margin-bottom: 10px; display: flex; align-items: center; gap: 14px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.04); border: 1px solid transparent;
        cursor: pointer; -webkit-tap-highlight-color: transparent;
    }
    .presensi-card:active { opacity: 0.85; }

    .card-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .icon-masuk { background: var(--primary-soft); color: var(--primary-dark); }
    .icon-pulang { background: var(--accent-light); color: var(--accent); }
    .icon-lembur-masuk { background: var(--primary-soft); color: var(--primary-dark); }
    .icon-lembur-pulang { background: var(--accent-light); color: var(--accent); }

    .card-body { flex: 1; min-width: 0; }
    .card-title-row { display: flex; align-items: center; gap: 8px; margin-bottom: 2px; }
    .card-title { font-size: 14px; font-weight: 600; color: var(--dark); }
    .card-tag {
        font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 6px;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .tag-success { background: var(--success-light); color: var(--success); }
    .tag-danger { background: var(--danger-light); color: var(--danger); }
    .tag-warning { background: var(--warning-light); color: var(--warning); }
    .card-time { font-size: 18px; font-weight: 800; color: var(--dark); font-variant-numeric: tabular-nums; line-height: 1.2; }
    .card-meta { font-size: 11px; color: var(--gray); margin-top: 2px; }

    .card-status { flex-shrink: 0; text-align: right; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }
    .dot-approved { background: #10b981; }
    .dot-pending { background: #f59e0b; }
    .dot-rejected { background: #ef4444; }
    .status-text { font-size: 11px; font-weight: 500; color: var(--gray); }

    /* Empty */
    .empty-box {
        text-align: center; padding: 60px 20px; color: var(--gray);
        background: var(--white); border-radius: 16px;
    }
    .empty-box i { font-size: 40px; margin-bottom: 12px; opacity: 0.3; display: block; }
    .empty-box p { font-size: 14px; margin: 0; }

</style>

<!-- Floating Filter Bar -->
<div class="filter-bar">
    <select id="filterBulan" style="flex:1;">
        @php $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
        @foreach($namaBulan as $i => $nb)
            <option value="{{ $i+1 }}" {{ (int)\Carbon\Carbon::parse($bulan)->format('m') === $i+1 ? 'selected' : '' }}>{{ $nb }}</option>
        @endforeach
    </select>
    <select id="filterTahun" style="flex:1;">
        @for($y = now()->year; $y >= 2024; $y--)
            <option value="{{ $y }}" {{ (int)\Carbon\Carbon::parse($bulan)->format('Y') === $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
    </select>
    <a id="btnDownloadPdf" class="btn-download" title="Download PDF" style="width:64px; min-width:64px;">
        <i class="fas fa-file-pdf"></i>
    </a>
</div>

<div class="riwayat-page">
    <!-- Tabs -->
    @php
        $regulerCount = 0; $lemburCount = 0;
        foreach ($riwayat as $items) { foreach ($items as $p) { $p->is_lembur ? $lemburCount++ : $regulerCount++; } }
    @endphp
    <div style="display:flex; gap:6px; margin-bottom:16px; background:rgba(0,0,0,0.03); border-radius:14px; padding:5px; border:1px solid var(--card-border); backdrop-filter:blur(10px);">
        <button type="button" class="riwayat-tab active" data-rtab="reguler" onclick="switchRiwayatTab('reguler')" style="flex:1; padding:11px 14px; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; box-shadow:0 3px 10px rgba(90,182,234,0.3), inset 0 1px 1px rgba(255,255,255,0.2);">
            <i class="fas fa-clock"></i> Reguler <span style="font-size:11px; opacity:0.8;">({{ $regulerCount }})</span>
        </button>
        <button type="button" class="riwayat-tab" data-rtab="lembur" onclick="switchRiwayatTab('lembur')" style="flex:1; padding:11px 14px; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; -webkit-tap-highlight-color:transparent; background:transparent; color:var(--gray); box-shadow:none;">
            <i class="fas fa-bolt"></i> Lembur <span style="font-size:11px; opacity:0.7;">({{ $lemburCount }})</span>
        </button>
    </div>

    <!-- Tab Reguler -->
    <div id="riwayatReguler" class="riwayat-content">
        @php $hasReguler = false; @endphp
        @foreach($riwayat as $tanggal => $items)
            @php $regulerItems = $items->where('is_lembur', false); @endphp
            @if($regulerItems->count())
            @php $hasReguler = true; @endphp
            <div class="date-group">
                <div class="date-label">{{ $tanggal }}</div>
                @foreach($regulerItems as $p)
                @php
                    $isMasuk = $p->jenis === 'masuk';
                    $iconClass = $isMasuk ? 'icon-masuk' : 'icon-pulang';
                    $iconName = $isMasuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket';
                    $label = $isMasuk ? 'Masuk' : 'Pulang';
                @endphp
                <div class="presensi-card" onclick="document.getElementById('detailModal{{ $p->id }}').style.display='block'" style="cursor:pointer;">
                    <div class="card-icon {{ $iconClass }}"><i class="fas {{ $iconName }}"></i></div>
                    <div class="card-body">
                        <div class="card-title">{{ $label }}@if($p->is_darurat) <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#ef4444;margin-left:4px;vertical-align:middle;" title="Absen Darurat"></span>@endif</div>
                        <div class="card-time">{{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</div>
                    </div>
                    <div class="card-status">
                        @if($p->status === 'pending')
                            <span class="status-dot dot-pending"></span>
                        @elseif($p->badge_text)
                            <span class="card-tag tag-{{ $p->badge_type }}">{{ $p->badge_text }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        @endforeach
        @if(!$hasReguler)
        <div class="empty-box"><i class="fas fa-calendar-times"></i><p>Belum ada riwayat reguler di bulan ini</p></div>
        @endif
    </div>

    <!-- Tab Lembur -->
    <div id="riwayatLembur" class="riwayat-content" style="display:none;">
        @php $hasLembur = false; @endphp
        @foreach($riwayat as $tanggal => $items)
            @php $lemburItems = $items->where('is_lembur', true); @endphp
            @if($lemburItems->count())
            @php $hasLembur = true; @endphp
            <div class="date-group">
                <div class="date-label">{{ $tanggal }}</div>
                @foreach($lemburItems as $p)
                @php
                    $isMasuk = $p->jenis === 'masuk';
                    $label = 'Lembur ' . ($isMasuk ? 'Masuk' : 'Pulang');
                @endphp
                <div class="presensi-card" onclick="document.getElementById('detailModal{{ $p->id }}').style.display='block'" style="cursor:pointer;">
                    <div class="card-icon {{ $isMasuk ? 'icon-lembur-masuk' : 'icon-lembur-pulang' }}"><i class="fas fa-bolt"></i></div>
                    <div class="card-body">
                        <div class="card-title">{{ $label }}</div>
                        <div class="card-time">{{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</div>
                    </div>
                    <div class="card-status">
                        @if($p->badge_text)
                            <span class="card-tag tag-{{ $p->badge_type }}">{{ $p->badge_text }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        @endforeach
        @if(!$hasLembur)
        <div class="empty-box"><i class="fas fa-bolt"></i><p>Belum ada riwayat lembur di bulan ini</p></div>
        @endif
    </div>
</div>

<!-- Modals -->
@foreach($riwayat as $tanggal => $items)
    @foreach($items as $p)
    @php
        $dIsMasuk = $p->jenis === 'masuk';
        $dIsLembur = $p->is_lembur;
        $dIconBg = $dIsMasuk ? 'var(--primary-soft)' : 'var(--accent-light)';
        $dIconColor = $dIsMasuk ? 'var(--primary-dark)' : 'var(--accent)';
        $dIconName = $dIsLembur ? 'fa-bolt' : ($dIsMasuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket');
    @endphp
    <div id="detailModal{{ $p->id }}" style="display:none; position:fixed; inset:0; z-index:100; background:var(--card-bg);">
        <div style="display:flex; flex-direction:column; height:100%;">
            <!-- Header -->
            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
                <button onclick="document.getElementById('detailModal{{ $p->id }}').style.display='none'" style="background:none; border:none; color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px; font-weight:500; -webkit-tap-highlight-color:transparent;">
                    <i class="fas fa-chevron-left"></i> Kembali
                </button>
                <span style="font-size:15px; font-weight:700; color:var(--dark);">{{ $dIsLembur ? 'Lembur ' : '' }}{{ ucfirst($p->jenis) }}</span>
                <div style="font-size:12px; color:var(--gray);">{{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</div>
            </div>

            <!-- Body -->
            <div style="flex:1; overflow-y:auto; padding:16px;">
                <!-- Foto -->
                <div style="border-radius:16px; overflow:hidden; margin-bottom:12px; aspect-ratio:4/3; background:var(--gray-light);">
                    @if($p->foto)
                    <img src="{{ asset('public/storage/'.$p->foto) }}" style="width:100%; height:100%; object-fit:cover; display:block;" alt="Foto" loading="lazy">
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
        </div>
    </div>
    @endforeach
@endforeach

<script>
    const wilayahAlamat = @json($wilayahAlamat);

    // Riwayat tabs with persistence
    function switchRiwayatTab(tab) {
        document.getElementById('riwayatReguler').style.display = tab === 'reguler' ? '' : 'none';
        document.getElementById('riwayatLembur').style.display = tab === 'lembur' ? '' : 'none';
        document.querySelectorAll('.riwayat-tab').forEach(function(btn) {
            if (btn.dataset.rtab === tab) {
                btn.style.background = 'linear-gradient(135deg,#5AB6EA,#2E97D4)';
                btn.style.color = '#fff';
                btn.style.boxShadow = '0 3px 10px rgba(90,182,234,0.3), inset 0 1px 1px rgba(255,255,255,0.2)';
            } else {
                btn.style.background = 'transparent';
                btn.style.color = 'var(--gray)';
                btn.style.boxShadow = 'none';
            }
        });
        localStorage.setItem('riwayat-active-tab', tab);
    }
    (function() {
        var saved = localStorage.getItem('riwayat-active-tab');
        if (saved === 'lembur') switchRiwayatTab('lembur');
    })();

    // Auto-filter on change
    function applyFilter() {
        var b = document.getElementById('filterBulan').value;
        var t = document.getElementById('filterTahun').value;
        var bulan = t + '-' + String(b).padStart(2, '0');
        window.location.href = "{{ route('pegawai.riwayat') }}?bulan=" + bulan;
    }
    document.getElementById('filterBulan').addEventListener('change', applyFilter);
    document.getElementById('filterTahun').addEventListener('change', applyFilter);

    // Download PDF
    document.getElementById('btnDownloadPdf').addEventListener('click', function(e) {
        e.preventDefault();
        var b = document.getElementById('filterBulan').value;
        var t = document.getElementById('filterTahun').value;
        var bulan = t + '-' + String(b).padStart(2, '0');
        window.open("{{ route('pegawai.riwayat.pdf') }}?bulan=" + bulan, '_blank');
    });

    // Profile photo map marker
    var _profilePhoto = @json(
        (Auth::user()->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists('foto_profil/' . Auth::user()->foto_profil))
            ? asset('public/storage/foto_profil/' . Auth::user()->foto_profil)
            : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=5AB6EA&color=fff&size=80'
    );

    function profileMarkerIcon() {
        return L.divIcon({
            className: '',
            html: '<div style="width:40px;height:40px;border-radius:50%;border:3px solid #2E97D4;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.2);"><img src="' + _profilePhoto + '" style="width:100%;height:100%;object-fit:cover;display:block;"></div>',
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });
    }

    function getAddressFromCoordinates(lat, lng, el) {
        if (!el) return;
        el.innerHTML = '<div class="loading-address"><i class="fas fa-spinner fa-spin"></i><span>Mendeteksi alamat...</span></div>';
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat='+lat+'&lon='+lng+'&zoom=18&addressdetails=1')
            .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
            .then(function(data) {
                if (data && data.display_name) { el.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + data.display_name; }
                else throw new Error();
            })
            .catch(function() { el.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + lat.toFixed(6) + ', ' + lng.toFixed(6); });
    }

    function initRiwayatDetailModals() {
        @foreach($riwayat as $tanggal => $items)
            @foreach($items as $p)
                @if($p->lokasi)
                (function() {
                    var modal = document.getElementById('detailModal{{ $p->id }}');
                    var status = @json($p->status);
                    if (!modal) return;

                    var map = null;
                    var isOpen = false;

                    var observer = new MutationObserver(function() {
                        var opened = modal.style.display !== 'none';
                        if (opened && !isOpen) {
                            isOpen = true;

                            var coords = @json($p->lokasi).split(',');
                            var lat = parseFloat(coords[0]);
                            var lng = parseFloat(coords[1]);
                            var addrEl = document.getElementById('locationAddress{{ $p->id }}');

                            if (isNaN(lat) || isNaN(lng)) {
                                if (addrEl) addrEl.innerHTML = '<span>Koordinat tidak valid</span>';
                                return;
                            }

                            try {
                                if (map) { try { map.remove(); } catch(e) {} map = null; }
                                map = L.map('mapDetail{{ $p->id }}').setView([lat, lng], 17);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                                L.marker([lat, lng], { icon: profileMarkerIcon() }).addTo(map);
                                setTimeout(function() { try { map.invalidateSize(); } catch(e){} }, 300);
                            } catch (e) {}

                            if (addrEl) {
                                if (status === 'approved' && wilayahAlamat) {
                                    addrEl.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + wilayahAlamat;
                                } else {
                                    getAddressFromCoordinates(lat, lng, addrEl);
                                }
                            }
                        }

                        if (!opened && isOpen) {
                            isOpen = false;
                            if (map) { try { map.remove(); } catch(e) {} map = null; }
                        }
                    });

                    observer.observe(modal, { attributes: true, attributeFilter: ['style'] });
                })();
                @endif
            @endforeach
        @endforeach
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRiwayatDetailModals);
    } else {
        initRiwayatDetailModals();
    }

    document.addEventListener('turbo:load', initRiwayatDetailModals);
</script>
@endsection
