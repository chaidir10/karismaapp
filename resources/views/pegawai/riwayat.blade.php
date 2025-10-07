@extends('layouts.pegawai')

@section('title', 'Riwayat Presensi')

@section('content')
<style>
    /* VARIABLES */
    :root {
        --primary: #5AB6EA;
        --primary-light: #87CEEB;
        --primary-dark: #2E97D4;
        --primary-soft: #E6F4F9;
        --accent: #FEAA2B;
        --accent-light: #FFE4BC;
        --light: #f8fafc;
        --gray-light: #f1f5f9;
        --gray: #94a3b8;
        --gray-dark: #64748b;
        --dark: #1e293b;
        --white: #ffffff;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --success: #10b981;
        --success-light: #d1fae5;
        --warning: #f59e0b;
        --warning-light: #fef3c7;
    }

    /* Main Container */


    /* Riwayat Section - Mirip dengan pengajuan-section */
    .riwayat-section {
        background:none;
        margin: 0;
        position: relative;
        z-index: 2;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(109, 40, 217, 0.1);
        border: 1px solid rgba(109, 40, 217, 0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
        margin-bottom: 100px;
    }

    .riwayat-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(109, 40, 217, 0.2);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .section-title {
        font-weight: 700;
        font-size: 17px;
        color: var(--dark);
        margin: 0;
    }

    /* Filter Form - Updated Style */
    .filter-form {
        width: 100%;
        margin-top: 0;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
        width: 100%;
    }

    .month-input {
        border: 1px solid var(--gray-light);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 10px;
        font-size: 16px;
        background: var(--white);
        transition: all 0.3s;
        width: 100%;
        box-sizing: border-box;
        -webkit-appearance: none;
    }

    .month-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(90, 182, 234, 0.1);
    }

    .filter-btn {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px 20px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 48px;
        box-shadow: 0 4px 8px rgba(90, 182, 234, 0.3);
    }

    .filter-btn:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(90, 182, 234, 0.4);
    }

    /* Riwayat List - Mirip dengan pengajuan-list */
    .riwayat-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .date-section {
        margin-bottom: 5px;
    }

    .date-divider {
        padding: 16px 0 12px 0;
        border-bottom: 2px solid var(--primary);
        margin-bottom: 12px;
        position: relative;
    }

    .date-text {
        font-weight: 700;
        font-size: 16px;
        color: var(--dark);
        display: block;
        padding: 8px 0;
    }

    /* Riwayat Item - Mirip dengan pengajuan-item */
    .riwayat-item {
        display: flex;
        align-items: center;
        padding: 15px;
        margin-bottom: 10px;
        background-color: var(--light);
        border-radius: 16px;
        transition: all 0.2s ease;
        border: 1px solid var(--gray-light);
        cursor: pointer;
    }

    .riwayat-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(109, 40, 217, 0.1);
    }

    /* Icon presensi */
    .riwayat-icon {
        width: 50px;
        height: 50px;
        margin-right: 15px;
        flex-shrink: 0;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--gray-light);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-soft);
        font-size: 20px;
    }

    .riwayat-info {
        flex-grow: 1;
        min-width: 0;
    }

    .riwayat-jenis {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 2px;
        color: var(--dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .riwayat-time {
        font-size: 12px;
        color: var(--gray);
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .riwayat-lokasi {
        font-size: 10px;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        background-color: rgba(100, 116, 139, 0.1);
        color: var(--gray-dark);
        display: inline-block;
    }

    .riwayat-status {
        margin-left: auto;
        flex-shrink: 0;
    }

    .riwayat-status .badge {
        font-size: 11px;
        padding: 5px 10px;
        font-weight: 500;
        border-radius: 6px;
        white-space: nowrap;
    }

    /* Status Colors */
    .status-masuk { 
        background-color: var(--success-light); 
        color: var(--success);
    }
    .status-pulang { 
        background-color: var(--warning-light); 
        color: var(--warning);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray);
        background: var(--white);
        border-radius: 16px;
        margin-top: 20px;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 16px;
        margin: 0;
        font-weight: 500;
    }

    /* MODAL STYLES - IMPROVED */
    .detail-modal .modal-content {
        background: var(--white);
        border-radius: 0;
        height: 100vh;
        margin: 0;
        display: flex;
        flex-direction: column;
        width: 100vw;
        max-width: none;
        overflow: hidden;
    }

    .detail-modal .modal-dialog {
        margin: 0;
        max-width: none;
        width: 100%;
        height: 100%;
    }

    .detail-modal .modal-header {
        position: sticky;
        top: 0;
        z-index: 10;
        margin-bottom: 0;
        background: var(--white);
        border-bottom: 1px solid var(--gray-light);
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 60px;
        box-sizing: border-box;
    }

    .detail-modal .modal-title {
        font-weight: 700;
        color: var(--dark);
        font-size: 18px;
        margin: 0;
    }

    .detail-modal .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--gray);
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background-color 0.3s;
        padding: 0;
    }

    .detail-modal .close-btn:active {
        background-color: var(--gray-light);
    }

    .detail-content-container {
        display: flex;
        flex-direction: column;
        height: 50vh;
        width: 100%;
        margin: 0;
        border: none;
        overflow: hidden;
    }

    .detail-image-container,
    .detail-map-container {
        flex: 1;
        height: 50%;
        position: relative;
        margin: 0;
        border: none;
        padding: 0;
        min-height: 200px;
    }

    .detail-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        margin: 0;
        padding: 0;
    }

    .detail-map {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
    }

    .no-photo-placeholder,
    .no-location-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--gray);
        gap: 12px;
        background: var(--gray-light);
        margin: 0;
        padding: 0;
        text-align: center;
    }

    .no-photo-placeholder i,
    .no-location-placeholder i {
        font-size: 32px;
        opacity: 0.5;
    }

    .detail-info-section {
        padding: 20px;
        background: var(--white);
        flex: 1;
        overflow-y: auto;
        border-top: 1px solid var(--gray-light);
        box-sizing: border-box;
    }

    .detail-datetime-info {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 20px;
        text-align: center;
    }

    .detail-type-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 12px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
    }

    .badge-masuk {
        background: rgba(16, 185, 129, 0.3);
    }

    .badge-pulang {
        background: rgba(239, 68, 68, 0.3);
    }

    .detail-date {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .detail-location-address {
        margin-top: 12px;
        font-size: 14px;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.9);
        word-break: break-word;
    }

    .loading-address {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
    }

    .detail-back-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 16px;
        min-height: 52px;
        box-sizing: border-box;
        box-shadow: 0 4px 8px rgba(90, 182, 234, 0.3);
    }

    .detail-back-btn:active {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(90, 182, 234, 0.4);
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .riwayat-section {
        animation: fadeIn 0.2s 0.1s ease-out forwards;
        margin: 20px;
        opacity: 0;
    }

    .riwayat-item {
        animation: fadeIn 0.2s ease-out forwards;
        opacity: 0;
    }

    .riwayat-item:nth-child(1) { animation-delay: 0.15s; }
    .riwayat-item:nth-child(2) { animation-delay: 0.2s; }
    .riwayat-item:nth-child(3) { animation-delay: 0.25s; }
    .riwayat-item:nth-child(4) { animation-delay: 0.3s; }
    .riwayat-item:nth-child(5) { animation-delay: 0.35s; }

    /* Responsive improvements */
    @media (min-width: 768px) {
        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .input-group {
            flex-direction: row;
            align-items: center;
            gap: 15px;
        }
        
        .month-input {
            flex: 1;
            margin-bottom: 0;
        }
        
        .filter-btn {
            width: auto;
            min-width: 140px;
            flex-shrink: 0;
        }
        
        .detail-content-container {
            flex-direction: row;
            height: 40vh;
        }
        
        .detail-image-container,
        .detail-map-container {
            height: 100%;
            flex: 1;
        }
    }

    @media (max-width: 400px) {
        .riwayat-item {
            padding: 12px;
        }

        .riwayat-icon {
            width: 45px;
            height: 45px;
            margin-right: 12px;
            font-size: 18px;
        }

        .riwayat-jenis {
            font-size: 13px;
        }

        .riwayat-time {
            font-size: 11px;
        }

        .riwayat-status .badge {
            font-size: 10px;
            padding: 4px 8px;
        }
    }
</style>

<div class="container">
    <!-- Riwayat Section -->
    <div class="riwayat-section">
        <div class="section-header">
            <h3 class="section-title">Riwayat Presensi</h3>
        </div>
        
        {{-- Filter per bulan --}}
        <form method="GET" class="filter-form">
            <div >
                <input type="month" id="bulan" name="bulan" value="{{ $bulan }}" class="month-input">
                <button type="submit" class="filter-btn">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>

        <!-- Riwayat Content -->
        <div class="riwayat-list">
            @forelse($riwayat as $tanggal => $items)
            <div class="date-section">
                <div class="date-divider">
                    <span class="date-text">{{ $tanggal }}</span>
                </div>
                
                <div class="presensi-items">
                    @foreach($items as $p)
                    <div class="riwayat-item" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
                        <div class="riwayat-icon">
                            @if($p->jenis === 'masuk')
                            <i class="fas fa-sign-in-alt text-success"></i>
                            @else
                            <i class="fas fa-sign-out-alt text-warning"></i>
                            @endif
                        </div>

                        <div class="riwayat-info">
                            <h5 class="riwayat-jenis">{{ $p->jenis === 'masuk' ? 'Presensi Masuk' : 'Presensi Pulang' }}</h5>
                            <p class="riwayat-time">{{ $p->jam }}</p>
                            <span class="riwayat-lokasi">{{ $p->lokasi ? 'Lokasi tersedia' : 'Lokasi tidak tersedia' }}</span>
                        </div>

                        <div class="riwayat-status">
                            <span class="badge status-{{ $p->jenis }}">
                                <i class="fas fa-circle small me-1" style="font-size: 6px;"></i> 
                                {{ $p->jenis === 'masuk' ? 'Masuk' : 'Pulang' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>Belum ada riwayat presensi di bulan ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Detail untuk setiap presensi -->
@foreach($riwayat as $tanggal => $items)
    @foreach($items as $p)
    <div class="modal fade detail-modal" id="detailModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-mobile">
            <div class="modal-content">
                <!-- Header Modal -->
                <div class="modal-header">
                    <h5 class="modal-title">Detail Presensi</h5>
                    <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Content Container - Foto dan Maps Berdampingan -->
                <div class="detail-content-container">
                    <!-- Foto Section -->
                    <div class="detail-image-container">
                        @if($p->foto)
                        <img src="{{ asset('public/storage/'.$p->foto) }}" class="detail-image" alt="Foto presensi" loading="lazy">
                        @else
                        <div class="no-photo-placeholder">
                            <i class="fas fa-camera"></i>
                            <span>Tidak ada foto</span>
                        </div>
                        @endif
                    </div>

                    <!-- Maps Section -->
                    <div class="detail-map-container">
                        @if($p->lokasi)
                        <div id="mapDetail{{ $p->id }}" class="detail-map"></div>
                        @else
                        <div class="no-location-placeholder">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Lokasi tidak tersedia</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Info Section -->
                <div class="detail-info-section">
                    <!-- Informasi Hari, Tanggal, dan Waktu -->
                    <div class="detail-datetime-info">
                        <div class="detail-type-badge {{ $p->jenis === 'masuk' ? 'badge-masuk' : 'badge-pulang' }}">
                            {{ $p->jenis === 'masuk' ? 'MASUK' : 'PULANG' }} - {{ $p->jam }}
                        </div>
                        <div class="detail-date">
                            {{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('l, d F Y') }}
                        </div>
                        
                        <div class="detail-location-address" id="locationAddress{{ $p->id }}">
                            <div class="loading-address">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Mendeteksi alamat...</span>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="detail-back-btn" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endforeach

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<script>
    // Initialize detail modals when shown
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all detail modals
        @foreach($riwayat as $tanggal => $items)
            @foreach($items as $p)
                @if($p->lokasi)
                const modal{{ $p->id }} = document.getElementById('detailModal{{ $p->id }}');
                if (modal{{ $p->id }}) {
                    modal{{ $p->id }}.addEventListener('shown.bs.modal', function() {
                        const coords = "{{ $p->lokasi }}".split(',');
                        const lat = parseFloat(coords[0]);
                        const lng = parseFloat(coords[1]);
                        
                        // Initialize map
                        const map = L.map('mapDetail{{ $p->id }}').setView([lat, lng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);
                        L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Presensi').openPopup();
                        
                        // Get address from coordinates
                        getAddressFromCoordinates(lat, lng, 'locationAddress{{ $p->id }}');
                        
                        this._map = map;
                    });

                    modal{{ $p->id }}.addEventListener('hidden.bs.modal', function() {
                        if (this._map) {
                            this._map.remove();
                            this._map = null;
                        }
                    });
                }
                @endif
            @endforeach
        @endforeach
    });

    // Function to get address from coordinates
    function getAddressFromCoordinates(lat, lng, elementId) {
        const addressElement = document.getElementById(elementId);
        
        // Show loading state
        addressElement.innerHTML = '<div class="loading-address"><i class="fas fa-spinner fa-spin"></i><span>Mendeteksi alamat...</span></div>';
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    addressElement.innerHTML = data.display_name;
                } else {
                    addressElement.innerHTML = '<span>Alamat tidak dapat ditemukan</span>';
                }
            })
            .catch(error => {
                console.error('Error getting address:', error);
                addressElement.innerHTML = '<span>Gagal mendapatkan alamat</span>';
            });
    }

    // Add touch improvements for mobile
    document.addEventListener('touchstart', function() {}, { passive: true });
</script>
@endsection