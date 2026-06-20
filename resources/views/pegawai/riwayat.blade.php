@extends('layouts.pegawai')

@section('title', 'Riwayat Presensi')

@section('content')
<style>
    .riwayat-page { padding: 20px; padding-bottom: 100px; }

    /* Filter */
    .filter-bar {
        display: flex; align-items: center; gap: 12px;
        background: var(--white); border-radius: 16px; padding: 12px 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); margin-bottom: 20px;
    }
    .filter-bar select, .filter-bar input {
        border: 1px solid var(--gray-light); border-radius: 10px;
        padding: 10px 14px; font-size: 14px; background: var(--white);
        outline: none; flex: 1; min-width: 0;
    }
    .filter-bar select:focus, .filter-bar input:focus { border-color: var(--primary); }
    .btn-download {
        width: 42px; height: 42px; border-radius: 10px; border: none; cursor: pointer;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
        flex-shrink: 0; transition: transform 0.2s;
    }
    .btn-download:active { transform: scale(0.93); }

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
        cursor: pointer; transition: all 0.2s;
    }
    .presensi-card:active { transform: scale(0.98); }

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
    .tag-reguler { background: var(--primary-soft); color: var(--primary-dark); }
    .tag-lembur { background: var(--accent-light); color: var(--accent); }
    .card-time { font-size: 22px; font-weight: 800; color: var(--dark); font-variant-numeric: tabular-nums; line-height: 1.2; }
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

    /* Modal */
    .detail-modal .modal-content { background:var(--card-bg); border-radius:0; height:100vh; margin:0; display:flex; flex-direction:column; width:100vw; max-width:none; overflow:hidden; }
    .detail-modal .modal-dialog { margin:0; max-width:none; width:100%; height:100%; }
    .detail-modal .modal-header { position:sticky; top:0; z-index:10; background:var(--card-bg); border-bottom:1px solid var(--card-border); padding:8px 16px; display:flex; justify-content:space-between; align-items:center; min-height:44px; }
    .detail-modal .modal-title { font-weight:700; color:var(--dark); font-size:18px; margin:0; }
    .detail-modal .close-btn { background:none; border:none; font-size:24px; cursor:pointer; color:var(--gray); width:44px; height:44px; display:flex; align-items:center; justify-content:center; border-radius:50%; }
    .detail-content-container { display:flex; flex:1; width:100%; overflow:hidden; min-height:0; }
    .detail-image-container { flex:3; min-height:0; position:relative; border-right:1px solid var(--card-border); }
    .detail-map-container { flex:2; min-height:0; position:relative; }
    .detail-image { width:100%; height:100%; object-fit:cover; display:block; }
    .detail-map { width:100%; height:100%; }
    .no-photo-placeholder, .no-location-placeholder { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; color:var(--gray); gap:12px; background:var(--gray-light); }
    .detail-info-section { padding:12px 16px; background:var(--card-bg); flex-shrink:0; border-top:1px solid var(--card-border); }
    .detail-datetime-info { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; padding:12px 16px; border-radius:12px; margin-bottom:10px; text-align:center; }
    .detail-type-badge { display:inline-block; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; margin-bottom:6px; background:rgba(255,255,255,0.2); }
    .badge-masuk { background:rgba(90,182,234,0.4); }
    .badge-pulang { background:rgba(254,170,43,0.4); }
    .badge-lembur { background:rgba(90,182,234,0.4); }
    .detail-date { font-size:13px; opacity:0.9; margin-bottom:4px; font-weight:500; }
    .detail-location-address { margin-top:6px; font-size:12px; line-height:1.4; color:rgba(255,255,255,0.9); word-break:break-word; }
    .loading-address { display:flex; align-items:center; justify-content:center; gap:8px; color:rgba(255,255,255,0.9); font-size:12px; }
    .detail-back-btn { width:100%; padding:10px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; font-size:14px; min-height:44px; }

    @media (max-width:576px) {
        .detail-content-container { flex-direction:column; }
        .detail-image-container { flex:none; height:45vh; border-right:none; border-bottom:1px solid var(--card-border); }
        .detail-map-container { flex:none; height:30vh; }
    }
</style>

<div class="riwayat-page">
    <!-- Filter Bar -->
    <div class="filter-bar">
        <select id="filterBulan">
            @php $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
            @foreach($namaBulan as $i => $nb)
                <option value="{{ $i+1 }}" {{ (int)\Carbon\Carbon::parse($bulan)->format('m') === $i+1 ? 'selected' : '' }}>{{ $nb }}</option>
            @endforeach
        </select>
        <select id="filterTahun">
            @for($y = now()->year; $y >= 2024; $y--)
                <option value="{{ $y }}" {{ (int)\Carbon\Carbon::parse($bulan)->format('Y') === $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <a id="btnDownloadPdf" class="btn-download" title="Download PDF">
            <i class="fas fa-file-pdf"></i>
        </a>
    </div>

    <!-- Content -->
    <div class="riwayat-content">
        @forelse($riwayat as $tanggal => $items)
        <div class="date-group">
            <div class="date-label">{{ $tanggal }}</div>
            @foreach($items as $p)
            @php
                $isLembur = $p->is_lembur;
                $isMasuk = $p->jenis === 'masuk';
                if ($isLembur) {
                    $iconClass = $isMasuk ? 'icon-lembur-masuk' : 'icon-lembur-pulang';
                    $iconName = $isMasuk ? 'fa-bolt' : 'fa-bolt';
                    $label = $isMasuk ? 'Masuk Lembur' : 'Pulang Lembur';
                    $tag = 'tag-lembur';
                    $tagText = 'Lembur';
                } else {
                    $iconClass = $isMasuk ? 'icon-masuk' : 'icon-pulang';
                    $iconName = $isMasuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket';
                    $label = $isMasuk ? 'Masuk' : 'Pulang';
                    $tag = 'tag-reguler';
                    $tagText = 'Reguler';
                }
            @endphp
            <div class="presensi-card" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
                <div class="card-icon {{ $iconClass }}">
                    <i class="fas {{ $iconName }}"></i>
                </div>
                <div class="card-body">
                    <div class="card-title-row">
                        <span class="card-title">{{ $label }}</span>
                        <span class="card-tag {{ $tag }}">{{ $tagText }}</span>
                    </div>
                    <div class="card-time">{{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</div>
                    <div class="card-meta">{{ $p->lokasi ? 'Lokasi tercatat' : 'Tanpa lokasi' }}</div>
                </div>
                <div class="card-status">
                    <span class="status-dot dot-{{ $p->status }}"></span>
                    <span class="status-text">{{ ucfirst($p->status) }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @empty
        <div class="empty-box">
            <i class="fas fa-calendar-times"></i>
            <p>Belum ada riwayat presensi di bulan ini</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modals -->
@foreach($riwayat as $tanggal => $items)
    @foreach($items as $p)
    <div class="modal fade detail-modal" id="detailModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-mobile">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Presensi</h5>
                    <button type="button" class="close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="detail-content-container">
                    <div class="detail-image-container">
                        @if($p->foto)
                        <img src="{{ asset('public/storage/'.$p->foto) }}" class="detail-image" alt="Foto" loading="lazy">
                        @else
                        <div class="no-photo-placeholder"><i class="fas fa-camera"></i><span>Tidak ada foto</span></div>
                        @endif
                    </div>
                    <div class="detail-map-container">
                        @if($p->lokasi)
                        <div id="mapDetail{{ $p->id }}" class="detail-map"></div>
                        @else
                        <div class="no-location-placeholder"><i class="fas fa-map-marker-alt"></i><span>Lokasi tidak tersedia</span></div>
                        @endif
                    </div>
                </div>
                <div class="detail-info-section">
                    <div class="detail-datetime-info">
                        <div class="detail-type-badge {{ $p->is_lembur ? 'badge-lembur' : ($p->jenis === 'masuk' ? 'badge-masuk' : 'badge-pulang') }}">
                            {{ $p->is_lembur ? 'LEMBUR ' : '' }}{{ strtoupper($p->jenis) }} - {{ $p->jam }}
                        </div>
                        <div class="detail-date">{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('l, d F Y') }}</div>
                        <div class="detail-location-address" id="locationAddress{{ $p->id }}">
                            @if($p->lokasi)
                            <div class="loading-address"><i class="fas fa-spinner fa-spin"></i><span>Mendeteksi alamat...</span></div>
                            @else
                            <span>Lokasi tidak tersedia</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="detail-back-btn" data-bs-dismiss="modal"><i class="fas fa-arrow-left"></i><span>Kembali</span></button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endforeach

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<script>
    const wilayahAlamat = @json($wilayahAlamat);

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

    // Maps
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($riwayat as $tanggal => $items)
            @foreach($items as $p)
                @if($p->lokasi)
                (function() {
                    var modal = document.getElementById('detailModal{{ $p->id }}');
                    var status = @json($p->status);
                    if (!modal) return;

                    modal.addEventListener('shown.bs.modal', function() {
                        var coords = @json($p->lokasi).split(',');
                        var lat = parseFloat(coords[0]);
                        var lng = parseFloat(coords[1]);
                        var addrEl = document.getElementById('locationAddress{{ $p->id }}');

                        if (isNaN(lat) || isNaN(lng)) { if (addrEl) addrEl.innerHTML = '<span>Koordinat tidak valid</span>'; return; }

                        try {
                            var map = L.map('mapDetail{{ $p->id }}').setView([lat, lng], 17);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                            L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Presensi').openPopup();
                            this._map = map;
                            setTimeout(function() { try { map.invalidateSize(); } catch(e){} }, 300);
                        } catch (e) {}

                        if (addrEl) {
                            if (status === 'approved' && wilayahAlamat) {
                                addrEl.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + wilayahAlamat;
                            } else {
                                getAddressFromCoordinates(lat, lng, addrEl);
                            }
                        }
                    });

                    modal.addEventListener('hidden.bs.modal', function() {
                        if (this._map) { try { this._map.remove(); } catch(e){} this._map = null; }
                    });
                })();
                @endif
            @endforeach
        @endforeach
    });

    function getAddressFromCoordinates(lat, lng, el) {
        el.innerHTML = '<div class="loading-address"><i class="fas fa-spinner fa-spin"></i><span>Mendeteksi alamat...</span></div>';
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat='+lat+'&lon='+lng+'&zoom=18&addressdetails=1')
            .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
            .then(function(data) {
                if (data && data.display_name) { el.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + data.display_name; }
                else throw new Error();
            })
            .catch(function() { el.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i> ' + lat.toFixed(6) + ', ' + lng.toFixed(6); });
    }
</script>
@endsection
