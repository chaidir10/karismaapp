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
</style>

@section('content')

<!-- Card Absensi -->
<div class="attendance-card">
    <div class="attendance-date">
        <i class="far fa-calendar-alt date-icon"></i>
        <span>{{ now()->translatedFormat('d M Y') }}</span>
    </div>
    <div class="attendance-time">
        <i class="far fa-clock time-icon"></i>
        <span>Jam Kerja (07:30 - 16:00)</span>
    </div>
    <div class="attendance-actions">
        <button class="attendance-btn {{ $sudahPresensiMasuk ? 'btn-disabled' : '' }}"
            id="clock-in-btn"
            data-bs-toggle="modal"
            data-bs-target="#presensiModal"
            onclick="setJenis('masuk')"
            {{ $sudahPresensiMasuk ? 'disabled' : '' }}>
            <i class="fas fa-sign-in-alt attendance-icon"></i>
            {{ $sudahPresensiMasuk ? 'Sudah Masuk' : 'Masuk' }}
        </button>
        <button class="attendance-btn {{ !$sudahPresensiMasuk || $sudahPresensiPulang ? 'btn-disabled' : '' }}"
            id="clock-out-btn"
            data-bs-toggle="modal"
            data-bs-target="#presensiModal"
            onclick="handlePulangClick()"
            {{ !$sudahPresensiMasuk || $sudahPresensiPulang ? 'disabled' : '' }}>
            <i class="fas fa-sign-out-alt attendance-icon"></i>
            {{ $sudahPresensiPulang ? 'Sudah Pulang' : 'Pulang' }}
        </button>
    </div>
</div>

<!-- Riwayat Hari Ini -->
<div class="attendance-history">
    <div class="history-header">
        <h5 class="history-title">Riwayat Hari Ini</h5>
    </div>
    <div>
        @forelse($riwayatHariIni as $p)
        <div class="history-item">
            <div>
                <span class="history-time">{{ $p->jam }}</span>
                <div class="history-type">{{ ucfirst($p->jenis) }}</div>
            </div>
            <i class="fas fa-chevron-right text-info cursor-pointer" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}" title="Lihat Detail"></i>
        </div>
        @empty
        <div class="empty-state">Belum ada riwayat absensi hari ini</div>
        @endforelse
    </div>
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
                    <div class="detail-type-badge fw-bold">{{ ucfirst($p->jenis) }} - {{ $p->jam }}</div>
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

                    <div class="camera-container-full">
                        <video id="video" autoplay playsinline></video>
                        <canvas id="canvas" class="d-none"></canvas>
                    </div>

                    <!-- <div class="mini-map-container">
                        <div id="mini-map" class="mini-map"></div>
                        <div class="location-info-mini mt-1">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="location-address-mini">Mendeteksi lokasi...</span>
                            <div id="locationRadiusInfo" class="text-sm mt-1">Memeriksa radius wilayah kerja...</div>
                        </div>
                    </div> -->

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
        <div class="modal-content rounded-2xl">
            <div class="modal-body p-0">
                <div class="text-center p-0">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Presensi</h3>
                    <p class="text-gray-600 mb-4">Anda berada di luar radius wilayah kerja. Yakin ingin melanjutkan presensi?</p>

                    <div class="confirmation-details bg-gray-50 rounded-xl p-0 mb-4">
                        <div class="grid grid-cols-2 gap-4 text-left">
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Jenis Presensi</div>
                                <div id="confirmationJenis" class="font-semibold text-gray-800"></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Waktu</div>
                                <div id="confirmationWaktu" class="font-semibold text-gray-800"></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-xs text-gray-500 mb-1">Lokasi Saat Ini</div>
                            <div id="confirmationLokasi" class="font-semibold text-gray-800 text-sm"></div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-yellow-500 mt-0.5 mr-2"></i>
                                <div class="text-xs text-yellow-700">
                                    <strong>Perhatian:</strong> Presensi di luar radius memerlukan persetujuan admin dan dapat mempengaruhi status kehadiran Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex border-t border-gray-200">
                    <button type="button" class="flex-1 py-4 text-gray-600 font-medium hover:bg-gray-50 rounded-bl-2xl transition-colors" data-bs-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="button" class="flex-1 py-4 bg-red-500 text-white font-medium hover:bg-red-600 rounded-br-2xl transition-colors" onclick="prosesPresensi()" id="confirmPresensiBtn">
                        <i class="fas fa-check mr-2"></i>Ya, Presensi
                    </button>
                </div>
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

    .confirmation-details {
        border-left: 4px solid #ffc107;
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
</style>
@endpush

@push('scripts')
<script>
    let videoStream = null;
    let mapInstance = null;
    let currentPosition = null;
    let isOutsideRadius = false;
    let capturedPhotoData = null;
    let autoCloseTimer = null;

    // Variabel status presensi dari controller
    const sudahPresensiMasuk = @json($sudahPresensiMasuk);
    const sudahPresensiPulang = @json($sudahPresensiPulang);

    function setJenis(jenis) {
        const el = document.getElementById('jenisPresensi');
        if (el) el.value = jenis;
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
        if (videoStream) {
            videoStream.getTracks().forEach(t => t.stop());
            videoStream = null;
        }

        // reset mapInstance agar tidak double-init
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

        // penting untuk iOS: muted agar autoplay bisa jalan
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
        .catch(err => {
            console.error(err);
            showError("Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan.");
        });
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

    function updateLocationInfo(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        const lokasiInput = document.getElementById('lokasiInput');
        if (lokasiInput) lokasiInput.value = `${lat},${lng}`;

        const wilayahLat = parseFloat("{{ Auth::user()->wilayahKerja->latitude ?? 0 }}");
        const wilayahLng = parseFloat("{{ Auth::user()->wilayahKerja->longitude ?? 0 }}");
        const wilayahAlamat = @json(Auth::user()->wilayahKerja->alamat ?? '');
        const radius = parseFloat("{{ Auth::user()->wilayahKerja->radius ?? 100 }}");

        const distance = haversineDistance(lat, lng, wilayahLat, wilayahLng);

        const infoEl = document.getElementById('locationRadiusInfo');
        const submitBtn = document.querySelector('.submit-btn-large');
        const addrEl = document.getElementById('location-address-mini');

        if (infoEl) infoEl.style.fontSize = "10px";

        if (distance <= radius) {
            if (infoEl) {
                infoEl.innerHTML = '<span class="badge bg-success">✔ Anda berada di dalam wilayah kerja</span>';
                infoEl.classList.remove('text-danger', 'text-warning');
                infoEl.classList.add('text-success');
            }
            if (submitBtn) submitBtn.disabled = false;
            if (addrEl) addrEl.textContent = wilayahAlamat || 'Di dalam wilayah kerja';
            isOutsideRadius = false;
        } else {
            if (infoEl) {
                infoEl.innerHTML = '<span class="badge bg-warning">⚠ Anda berada di luar radius wilayah kerja</span>';
                infoEl.classList.remove('text-success');
                infoEl.classList.add('text-warning');
            }
            if (submitBtn) submitBtn.disabled = false;
            isOutsideRadius = true;

            // ambil alamat real
            getAddressFromCoordinates(lat, lng, 'location-address-mini');
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

   
    function initializeDetailModals() {
        if (!window.L) return;

        @foreach($riwayatHariIni as $p)
            @if($p->lokasi)
                const modal{{ $p->id }} = document.getElementById('detailModal{{ $p->id }}');
                if (modal{{ $p->id }}) {
                    modal{{ $p->id }}.addEventListener('shown.bs.modal', function () {
                        const coords = @json($p->lokasi).split(',');
                        const lat = parseFloat(coords[0]);
                        const lng = parseFloat(coords[1]);

                        const map = L.map('mapDetail{{ $p->id }}').setView([lat, lng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                        L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Presensi').openPopup();

                        getAddressFromCoordinates(lat, lng, 'locationAddress{{ $p->id }}');
                        this._map = map;

                        setTimeout(() => {
                            try { map.invalidateSize(); } catch (e) {}
                        }, 300);
                    });

                    modal{{ $p->id }}.addEventListener('hidden.bs.modal', function () {
                        if (this._map) {
                            try { this._map.remove(); } catch (e) {}
                            this._map = null;
                        }
                    });
                }
            @endif
        @endforeach
    }

    function getAddressFromCoordinates(lat, lng, elementId) {
        const el = document.getElementById(elementId);
        if (!el) return;

        el.innerHTML = '<div class="loading-address"><i class="fas fa-spinner fa-spin me-2"></i>Mendeteksi alamat...</div>';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            el.innerHTML = (data && data.display_name) ? data.display_name : '<span class="text-warning">Alamat tidak dapat ditemukan</span>';
        })
        .catch(e => {
            console.error(e);
            el.innerHTML = '<span class="text-danger">Gagal mendapatkan alamat</span>';
        });
    }

    function captureAndProcess() {
        if (!videoStream || !currentPosition) {
            showError("Kamera atau lokasi belum siap. Pastikan izin kamera & lokasi diizinkan.");
            return;
        }

        const jenis = document.getElementById('jenisPresensi')?.value || '';
        if (jenis === 'pulang' && !sudahPresensiMasuk) {
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