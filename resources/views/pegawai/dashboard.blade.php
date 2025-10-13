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
        <button class="attendance-btn" id="clock-in-btn" data-bs-toggle="modal" data-bs-target="#presensiModal" onclick="setJenis('masuk')">
            <i class="fas fa-sign-in-alt attendance-icon"></i> Masuk
        </button>
        <button class="attendance-btn" id="clock-out-btn" data-bs-toggle="modal" data-bs-target="#presensiModal" onclick="setJenis('pulang')">
            <i class="fas fa-sign-out-alt attendance-icon"></i> Pulang
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

                    <div class="mini-map-container">
                        <div id="mini-map" class="mini-map"></div>
                        <div class="location-info-mini mt-1">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="location-address-mini">Mendeteksi lokasi...</span>
                            <div id="locationRadiusInfo" class="text-sm mt-1">Memeriksa radius wilayah kerja...</div>
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
<div class="modal fade" id="successConfirmationModal" tabindex="-1" aria-hidden="true">
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

    function setJenis(jenis) {
        document.getElementById('jenisPresensi').value = jenis;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const presensiModal = document.getElementById('presensiModal');
        if (presensiModal) {
            presensiModal.addEventListener('shown.bs.modal', initializePresensiModal);
            presensiModal.addEventListener('hidden.bs.modal', cleanupPresensiModal);
        }
        initializeDetailModals();

        // Inisialisasi toast
        const toastElList = [].slice.call(document.querySelectorAll('.toast'))
        const toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            })
        });

        // Handle response dari server
        @if(session('success'))
        showSuccess("{{ session('success') }}");
        @endif

        @if(session('error'))
        showError("{{ session('error') }}");
        @endif

        @if(session('warning'))
        showWarning("{{ session('warning') }}");
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
        const submitBtn = document.querySelector('.submit-btn-large');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Ambil Foto & Absen';
            submitBtn.disabled = false;
        }
        capturedPhotoData = null;

        // Clear auto close timer
        if (autoCloseTimer) {
            clearTimeout(autoCloseTimer);
            autoCloseTimer = null;
        }
    }

    function initializeCamera() {
        const video = document.getElementById('video');
        if (!navigator.mediaDevices?.getUserMedia) {
            showError("Browser tidak mendukung akses kamera.");
            return;
        }
        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: {
                        ideal: 1920
                    },
                    height: {
                        ideal: 1080
                    }
                }
            })
            .then(stream => {
                videoStream = stream;
                video.srcObject = stream;
            })
            .catch(err => {
                console.error(err);
                showError("Tidak dapat mengakses kamera.");
            });
    }

    function initializeLocation() {
        if (!navigator.geolocation) {
            document.getElementById('location-address-mini').textContent = "Browser tidak mendukung geolokasi";
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
                document.getElementById('location-address-mini').textContent = "Gagal mendapatkan lokasi";
                initializeMiniMapWithDefault();
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    }

    function updateLocationInfo(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        const lokasiInput = document.getElementById('lokasiInput');

        const wilayahLat = parseFloat("{{ Auth::user()->wilayahKerja->latitude ?? 0 }}");
        const wilayahLng = parseFloat("{{ Auth::user()->wilayahKerja->longitude ?? 0 }}");
        const wilayahAlamat = "{{ Auth::user()->wilayahKerja->alamat ?? '' }}";
        const radius = parseFloat("{{ Auth::user()->wilayahKerja->radius ?? 100 }}");
        const distance = haversineDistance(lat, lng, wilayahLat, wilayahLng);

        const infoEl = document.getElementById('locationRadiusInfo');
        const submitBtn = document.querySelector('.submit-btn-large');

        lokasiInput.value = `${lat},${lng}`;

        if (distance <= radius) {
            // infoEl.textContent="✔ Anda berada di dalam radius wilayah kerja"; 
            infoEl.innerHTML = '<span class="badge badge-success">✔ Anda berada di dalam wilayah kerja</span>';
            infoEl.style.fontSize = "10 px";
            infoEl.classList.remove('text-danger', 'text-warning');
            infoEl.classList.add('text-success');
            submitBtn.disabled = false;
            document.getElementById('location-address-mini').textContent = wilayahAlamat;
            isOutsideRadius = false;
        } else {
            infoEl.textContent = '<span class="badge badge-warning">⚠ Anda berada di luar radius wilayah kerja</span>';
            infoEl.style.fontSize = "10 px";
            infoEl.classList.remove('text-success');
            infoEl.classList.add('text-warning');
            submitBtn.disabled = false;
            getAddressFromCoordinates(lat, lng, 'location-address-mini');
            isOutsideRadius = true;
        }
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = x => x * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;
        return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function initializeMiniMap(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        const mapEl = document.getElementById('mini-map');
        if (!mapInstance) {
            mapInstance = L.map(mapEl, {
                zoomControl: false,
                attributionControl: false
            }).setView([lat, lng], 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapInstance);
            L.marker([lat, lng]).addTo(mapInstance);
            ['dragging', 'touchZoom', 'doubleClickZoom', 'scrollWheelZoom', 'boxZoom', 'keyboard'].forEach(f => mapInstance[f].disable());
        } else {
            mapInstance.setView([lat, lng], 17);
        }
    }

    function initializeMiniMapWithDefault() {
        const mapEl = document.getElementById('mini-map');
        if (!mapInstance) {
            mapInstance = L.map(mapEl, {
                zoomControl: false,
                attributionControl: false
            }).setView([-6.2088, 106.8456], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapInstance);
        }
    }

    function initializeDetailModals() {
        @foreach($riwayatHariIni as $p)
        @if($p - > lokasi)
        const modal {
            {
                $p - > id
            }
        } = document.getElementById('detailModal{{ $p->id }}');
        if (modal {
                {
                    $p - > id
                }
            }) {
            modal {
                {
                    $p - > id
                }
            }.addEventListener('shown.bs.modal', function() {
                setTimeout(() => {
                    const coords = "{{ $p->lokasi }}".split(',');
                    const lat = parseFloat(coords[0]);
                    const lng = parseFloat(coords[1]);
                    const map = L.map('mapDetail{{ $p->id }}').setView([lat, lng], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                    L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Presensi').openPopup();
                    getAddressFromCoordinates(lat, lng, 'locationAddress{{ $p->id }}');
                    this._map = map;
                }, 400); // ⏱ delay 400ms
            });
            modal {
                {
                    $p - > id
                }
            }.addEventListener('hidden.bs.modal', function() {
                if (this._map) {
                    this._map.remove();
                    this._map = null;
                }
            });
        }
        @endif
        @endforeach
    }

    function getAddressFromCoordinates(lat, lng, elementId) {
        const el = document.getElementById(elementId);
        el.innerHTML = '<div class="loading-address"><i class="fas fa-spinner fa-spin me-2"></i>Mendeteksi alamat...</div>';
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
            .then(r => r.json())
            .then(data => {
                el.innerHTML = data?.display_name ?? '<span class="text-warning">Alamat tidak dapat ditemukan</span>';
            })
            .catch(e => {
                console.error(e);
                el.innerHTML = '<span class="text-danger">Gagal mendapatkan alamat</span>';
            });
    }

    function captureAndProcess() {
        if (!videoStream || !currentPosition) {
            showError("Kamera atau lokasi belum siap.");
            return;
        }

        // Ambil foto terlebih dahulu
        capturePhoto().then(photoData => {
            capturedPhotoData = photoData;

            // Tampilkan modal konfirmasi sesuai radius
            if (isOutsideRadius) {
                showConfirmationModal();
            } else {
                langsungProsesPresensi();
            }
        }).catch(error => {
            console.error('Error capturing photo:', error);
            showError("Gagal mengambil foto.");
        });
    }

    function capturePhoto() {
        return new Promise((resolve, reject) => {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            const photoData = canvas.toDataURL('image/jpeg', 0.8);
            resolve(photoData);
        });
    }

    function showConfirmationModal() {
        const jenis = document.getElementById('jenisPresensi').value;
        const waktu = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        // Update modal konfirmasi luar radius
        document.getElementById('confirmationJenis').textContent = jenis.toUpperCase();
        document.getElementById('confirmationWaktu').textContent = waktu;
        document.getElementById('confirmationLokasi').textContent = document.getElementById('location-address-mini').textContent;

        // Reset tombol konfirmasi
        const confirmBtn = document.getElementById('confirmPresensiBtn');
        confirmBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Ya, Presensi';
        confirmBtn.disabled = false;

        // Tampilkan modal konfirmasi
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        confirmationModal.show();
    }

    function langsungProsesPresensi() {
        if (!capturedPhotoData) {
            showError("Foto belum diambil.");
            return;
        }

        // Set foto ke input hidden
        document.getElementById('fotoInput').value = capturedPhotoData;

        // Submit form
        document.getElementById('formPresensi').submit();

        // Tutup modal presensi
        const presensiModal = bootstrap.Modal.getInstance(document.getElementById('presensiModal'));
        if (presensiModal) {
            presensiModal.hide();
        }

        // Tampilkan modal sukses yang auto close
        showSuccessConfirmationModal();
    }

    function showSuccessConfirmationModal() {
        const jenis = document.getElementById('jenisPresensi').value;
        const waktu = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        // Update modal konfirmasi dalam radius
        document.getElementById('successConfirmationJenis').textContent = jenis.toUpperCase();
        document.getElementById('successConfirmationWaktu').textContent = waktu;
        document.getElementById('successConfirmationLokasi').textContent = document.getElementById('location-address-mini').textContent;

        // Tampilkan modal konfirmasi
        const confirmationModal = new bootstrap.Modal(document.getElementById('successConfirmationModal'));
        confirmationModal.show();

        // Auto close setelah 3 detik
        autoCloseTimer = setTimeout(() => {
            if (confirmationModal) {
                confirmationModal.hide();
            }
        }, 3000);
    }

    function prosesPresensi(isInRadius = false) {
        if (!capturedPhotoData) {
            showError("Foto belum diambil.");
            return;
        }

        const confirmBtn = document.getElementById('confirmPresensiBtn');
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        confirmBtn.disabled = true;

        // Set foto ke input hidden
        document.getElementById('fotoInput').value = capturedPhotoData;

        // Submit form
        setTimeout(() => {
            document.getElementById('formPresensi').submit();

            // Tutup modal konfirmasi
            const confirmationModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            if (confirmationModal) {
                confirmationModal.hide();
            }

            // Tutup modal presensi
            const presensiModal = bootstrap.Modal.getInstance(document.getElementById('presensiModal'));
            if (presensiModal) {
                presensiModal.hide();
            }
        }, 1500);
    }

    // Fungsi Notifikasi
    function showSuccess(message) {
        const toast = document.getElementById('successToast');
        const messageEl = document.getElementById('successToastMessage');
        if (message && messageEl) {
            messageEl.textContent = message;
        }
        bootstrap.Toast.getOrCreateInstance(toast).show();
    }

    function showError(message) {
        const toast = document.getElementById('errorToast');
        const messageEl = document.getElementById('errorToastMessage');
        if (message && messageEl) {
            messageEl.textContent = message;
        }
        bootstrap.Toast.getOrCreateInstance(toast).show();
    }

    function showWarning(message) {
        const toast = document.getElementById('warningToast');
        const messageEl = document.getElementById('warningToastMessage');
        if (message && messageEl) {
            messageEl.textContent = message;
        }
        bootstrap.Toast.getOrCreateInstance(toast).show();
    }
</script>
@endpush