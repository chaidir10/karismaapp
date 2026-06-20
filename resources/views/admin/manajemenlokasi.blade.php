@extends('layouts.admin')

@section('title', 'Manajemen Lokasi Presensi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/a2e0e6ad65.js" crossorigin="anonymous"></script>
<style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    .leaflet-container {
        border-radius: 0.75rem;
    }

    .radius-control {
        background: white;
        padding: 10px;
        border-radius: 4px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    }

    .radius-control input {
        width: 100%;
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 10px;
        color: white;
        z-index: 1000;
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 0.3s, transform 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .notification.show {
        opacity: 1;
        transform: translateY(0);
    }

    .notification.success {
        background: linear-gradient(to right, #4ade80, #22c55e);
    }

    .notification.error {
        background: linear-gradient(to right, #f87171, #ef4444);
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #d1d5db;
    }

    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, .3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .address-container {
        margin-top: 10px;
        padding: 10px;
        background-color: #f8fafc;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
    }

    /* Action Buttons - Konsisten dengan Dashboard */
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
    }

    .btn-detail,
    .btn-edit,
    .btn-delete {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12px;
    }

    .btn-detail {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .btn-detail:hover {
        background: #10b981;
        color: white;
    }

    .btn-edit {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .btn-edit:hover {
        background: #3b82f6;
        color: white;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: white;
    }

    /* Button Styles untuk Header */
    .btn-primary {
        background: white;
        color: #3b82f6;
        padding: 12px 20px;
        border-radius: 12px;
        border: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .radius-slider-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .radius-slider {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 5px;
        background: linear-gradient(to right, #6366f1, #a855f7);
        outline: none;
        cursor: pointer;
    }

    .radius-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #4f46e5;
        border: 2px solid white;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
        cursor: grab;
    }

    .radius-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #4f46e5;
        border: 2px solid white;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
        cursor: grab;
    }

    .radius-input {
        width: 80px;
    }

    /* Animasi untuk modal - PERBAIKAN */
    .modal-container {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal-container.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-backdrop {
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modal-container.active .modal-backdrop {
        opacity: 1;
    }

    .modal-content {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 90vw;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        transform: scale(0.9) translateY(10px);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .modal-container.active .modal-content {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    /* Animasi untuk modal konfirmasi khusus */
    .modal-confirm .modal-content {
        max-width: 28rem;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-8 shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-2xl font-bold text-white">Manajemen Titik Presensi</h1>
                <p class="text-indigo-100 mt-1">Kelola titik lokasi presensi dengan mudah</p>
            </div>
            <button id="btnTambah"
                class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                <span class="font-medium">Tambah Titik</span>
            </button>
        </div>
    </div>

    <!-- Main Content: Map and Table -->
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6 relative">
        <!-- Map Container (6/10) -->
        <div class="lg:col-span-5 bg-white rounded-xl p-4 shadow-md relative">
            <h2 class="text-lg font-semibold mb-4">Sebaran Titik Presensi</h2>
            <div id="mainMap" class="h-[500px] w-full rounded-xl border relative z-0"></div>
        </div>

        <!-- Table Container (4/10) -->
        <div class="lg:col-span-5 bg-white rounded-xl p-4 shadow-md relative z-10">
            <!-- Search and Filter -->
            <div class="mb-4 flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Cari titik presensi..."
                        class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-1 focus:outline-none text-sm shadow-sm" />
                </div>
                <button id="btnSort"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 flex items-center text-sm">
                    <i class="fas fa-sort mr-2"></i>
                    Urutkan
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama
                                    Titik</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Radius
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody id="titikTableBody" class="bg-white divide-y divide-gray-200 text-xs">
                            @forelse($lokasi as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-xs">{{ $item->nama }}</div>
                                    <div class="text-gray-500 text-xs mt-1">{{ $item->alamat ?? 'Tidak ada alamat' }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $item->radius }} m</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="action-buttons">
                                        <button onclick="showDetail({{ $item->id }})" 
                                                class="btn-detail" 
                                                title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editLokasi({{ $item->id }})" 
                                                class="btn-edit" 
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteLokasi({{ $item->id }}, '{{ $item->nama }}')" 
                                                class="btn-delete" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">
                                    <div id="emptyState" class="empty-state p-6 text-center">
                                        <i class="fas fa-map-marker-alt text-gray-300 mb-3" style="font-size: 3rem;"></i>
                                        <h3 class="font-medium text-sm mb-2">Belum ada titik presensi</h3>
                                        <p class="max-w-md text-xs mx-auto">Tambahkan titik presensi pertama Anda dengan menekan tombol "Tambah
                                            Titik" di atas.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form (Tambah/Edit) -->
<div id="modalForm" class="modal-container">
    <div class="modal-backdrop" onclick="closeModal('modalForm')"></div>
    <div class="modal-content w-full max-w-5xl p-6 mx-4 max-h-[90vh] overflow-y-auto">
        <button class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors"
            onclick="closeModal('modalForm')">
            <i class="fas fa-times"></i>
        </button>
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Tambah Titik Presensi</h2>
        <form id="formTitik" method="POST" class="grid grid-cols-12 gap-4">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            <input type="hidden" id="lokasiId" name="id">

            <!-- Map 6 -->
            <div class="col-span-12 md:col-span-7">
                <div id="mapForm" class="h-80 md:h-96 w-full rounded-xl border"></div>
                <div class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Klik pada peta untuk menandai lokasi
                </div>
            </div>
            <!-- Detail 4 -->
            <div class="col-span-12 md:col-span-5 space-y-4">
                <div>
                    <label class="text-sm font-medium block mb-1">Nama Titik <span class="text-red-500">*</span></label>
                    <input type="text" id="nama" name="nama" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 text-sm required">
                    <div id="namaError" class="text-red-500 text-xs mt-1 hidden">Nama titik harus diisi</div>
                </div>
                <div>
                    <label class="text-sm font-medium block mb-1">Alamat</label>
                    <textarea id="alamat" name="alamat"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 text-sm"
                        rows="3"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium mb-1">Latitude <span class="text-red-500">*</span></label>
                        <input type="text" id="latitude" name="latitude"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 text-sm"
                            required>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1">Longitude <span class="text-red-500">*</span></label>
                        <input type="text" id="longitude" name="longitude"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 text-sm"
                            required>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium block mb-1">Radius (meter) <span class="text-red-500">*</span></label>
                    <div class="radius-slider-container">
                        <input type="range" id="radiusSlider" min="0" max="500" value="0" class="radius-slider">
                        <input type="number" id="radius" name="radius" min="0" max="500" value="0"
                            class="radius-input px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 text-sm">
                    </div>
                    <div id="radiusError" class="text-red-500 text-xs mt-1 hidden">Radius minimal 10 meter</div>
                    <div class="text-xs text-gray-500 mt-1">Jarak maksimal untuk dapat melakukan presensi</div>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 mt-2">
                    <!-- Tombol Batal -->
                    <button type="button"
                        class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors text-sm"
                        onclick="confirmCancel()">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>

                    <!-- Tombol Simpan -->
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors flex items-center text-sm">
                        <i class="fas fa-check mr-2"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail -->
<div id="modalDetail" class="modal-container">
    <div class="modal-backdrop" onclick="closeModal('modalDetail')"></div>
    <div class="modal-content w-full max-w-5xl p-6 mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Close Button -->
        <button class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-lg transition-colors"
            onclick="closeModal('modalDetail')">
            <i class="fas fa-times"></i>
        </button>

        <h2 class="text-lg font-bold mb-4">Detail Titik Presensi</h2>
        <div class="grid grid-cols-12 gap-4">
            <!-- Map -->
            <div class="col-span-12 md:col-span-7">
                <div id="mapDetail" class="h-80 md:h-96 w-full rounded-xl border"></div>
                <div class="mt-2 text-[10px] text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Lokasi titik presensi
                </div>
            </div>

            <!-- Detail Info -->
            <div class="col-span-12 md:col-span-5 space-y-4">
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="font-semibold text-gray-700 text-xs">Nama Titik:</p>
                    <p id="detailNama" class="mt-1 text-sm"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="font-semibold text-gray-700 text-xs">Alamat:</p>
                    <p id="detailAlamat" class="mt-1 text-sm"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="font-semibold text-gray-700 text-xs">Latitude:</p>
                        <p id="detailLat" class="mt-1 text-sm"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="font-semibold text-gray-700 text-xs">Longitude:</p>
                        <p id="detailLon" class="mt-1 text-sm"></p>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="font-semibold text-gray-700 text-xs">Radius:</p>
                    <p id="detailRadius" class="mt-1 text-sm"></p>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 mt-2">
                    <button
                        class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors text-xs"
                        onclick="closeModal('modalDetail')">
                        <i class="fas fa-times mr-2"></i>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div id="modalDelete" class="modal-container modal-confirm">
    <div class="modal-backdrop" onclick="closeModal('modalDelete')"></div>
    <div class="modal-content p-6">
        <button class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors"
            onclick="closeModal('modalDelete')">
            <i class="fas fa-times"></i>
        </button>
        <div class="mb-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
        </div>
        <h2 class="text-xl font-bold mb-2">Hapus Titik Presensi</h2>
        <p class="text-gray-600">Apakah Anda yakin ingin menghapus <span id="deleteNama"
                class="font-semibold text-gray-800"></span>?</p>
        <p class="text-xs text-gray-500 mt-2">Data yang dihapus tidak dapat dikembalikan</p>
        <div class="flex justify-center gap-3 mt-6">
            <button
                class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors text-sm"
                onclick="closeModal('modalDelete')">
                <i class="fas fa-times mr-2"></i>
                Batal
            </button>
            <button id="confirmDelete"
                class="px-5 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors flex items-center text-sm">
                <i class="fas fa-trash mr-2"></i>
                Hapus
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batal -->
<div id="modalConfirmCancel" class="modal-container modal-confirm">
    <div class="modal-backdrop" onclick="closeModal('modalConfirmCancel')"></div>
    <div class="modal-content p-6">
        <button class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors"
            onclick="closeModal('modalConfirmCancel')">
            <i class="fas fa-times"></i>
        </button>
        <div class="mb-4">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
            </div>
        </div>
        <h2 class="text-xl font-bold mb-2">Batalkan Perubahan</h2>
        <p class="text-gray-600">Apakah Anda yakin ingin membatalkan perubahan yang telah dibuat?</p>
        <p class="text-xs text-gray-500 mt-2">Semua perubahan yang belum disimpan akan hilang</p>
        <div class="flex justify-center gap-3 mt-6">
            <button
                class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors text-sm"
                onclick="closeModal('modalConfirmCancel')">
                <i class="fas fa-edit mr-2"></i>
                Lanjutkan Edit
            </button>
            <button
                class="px-5 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors flex items-center text-sm"
                onclick="closeModal('modalForm'); closeModal('modalConfirmCancel');">
                <i class="fas fa-times mr-2"></i>
                Batalkan
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Simpan -->
<div id="modalConfirmSave" class="modal-container modal-confirm">
    <div class="modal-backdrop" onclick="closeModal('modalConfirmSave')"></div>
    <div class="modal-content p-6">
        <button class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors"
            onclick="closeModal('modalConfirmSave')">
            <i class="fas fa-times"></i>
        </button>
        <div class="mb-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-check text-green-500 text-xl"></i>
            </div>
        </div>
        <h2 class="text-xl font-bold mb-2">Berhasil</h2>
        <p class="text-gray-600">Data titik presensi berhasil disimpan</p>
        <div class="flex justify-center mt-6">
            <button
                class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors flex items-center text-sm"
                onclick="closeModal('modalConfirmSave'); closeModal('modalForm'); location.reload();">
                <i class="fas fa-check mr-2"></i>
                Oke
            </button>
        </div>
    </div>
</div>

<!-- Notification -->
<div id="notification" class="notification"></div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Data lokasi dari server
    let lokasiData = @json($lokasi);
    let map, mapForm, mapDetail;
    let currentMarker, currentCircle;

    // Inisialisasi peta utama
    function initMap() {
        map = L.map('mainMap').setView([-2, 118], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Tambahkan marker untuk setiap lokasi
        lokasiData.forEach(l => {
            if (l.latitude && l.longitude) {
                let marker = L.marker([l.latitude, l.longitude]).addTo(map);
                marker.bindPopup(`<b>${l.nama}</b><br>Radius: ${l.radius} m`);
                L.circle([l.latitude, l.longitude], {
                    radius: l.radius,
                    color: '#4f46e5',
                    fillOpacity: 0.1
                }).addTo(map);
            }
        });
    }

    // Inisialisasi peta untuk form
    function initMapForm() {
        mapForm = L.map('mapForm').setView([-2, 118], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(mapForm);

        // Event klik pada peta untuk menambahkan marker
        mapForm.on('click', function(e) {
            // Hapus marker sebelumnya jika ada
            if (currentMarker) {
                mapForm.removeLayer(currentMarker);
            }
            if (currentCircle) {
                mapForm.removeLayer(currentCircle);
            }

            // Tambahkan marker baru
            currentMarker = L.marker(e.latlng).addTo(mapForm);
            currentCircle = L.circle(e.latlng, {
                radius: document.getElementById('radius').value,
                color: '#4f46e5',
                fillOpacity: 0.1
            }).addTo(mapForm);

            // Update form dengan koordinat baru
            document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
        });
    }

    // Inisialisasi peta untuk detail
    function initMapDetail(lat, lng, radius, nama) {
        // Hapus map lama kalau ada
        if (mapDetail) {
            mapDetail.remove();
        }

        // Buat ulang map baru
        mapDetail = L.map('mapDetail').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(mapDetail);

        // Tambahkan marker dan circle untuk lokasi
        L.marker([lat, lng]).addTo(mapDetail)
            .bindPopup(`<b>${nama}</b><br>Radius: ${radius} m`)
            .openPopup();
        L.circle([lat, lng], {
            radius: radius,
            color: '#4f46e5',
            fillOpacity: 0.1
        }).addTo(mapDetail);

        // Pastikan ukuran map valid setelah modal aktif
        setTimeout(() => {
            mapDetail.invalidateSize();
        }, 300);
    }

    // Fungsi untuk membuka modal
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Inisialisasi peta form jika modal form dibuka
        if (id === 'modalForm') {
            setTimeout(() => {
                if (!mapForm) {
                    initMapForm();
                } else {
                    mapForm.invalidateSize();
                }
            }, 300);
        }
    }

    // Fungsi untuk menutup modal
    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Fungsi untuk menampilkan detail lokasi
    function showDetail(id) {
        let l = lokasiData.find(x => x.id == id);
        if (l) {
            document.getElementById('detailNama').textContent = l.nama;
            document.getElementById('detailAlamat').textContent = l.alamat || 'Tidak ada alamat';
            document.getElementById('detailLat').textContent = l.latitude;
            document.getElementById('detailLon').textContent = l.longitude;
            document.getElementById('detailRadius').textContent = l.radius + ' meter';

            // Inisialisasi peta detail
            setTimeout(() => {
                initMapDetail(l.latitude, l.longitude, l.radius, l.nama);
            }, 300);

            openModal('modalDetail');
        }
    }

    // Fungsi untuk mengedit lokasi - PERBAIKAN
    function editLokasi(id) {
        let l = lokasiData.find(x => x.id == id);
        if (l) {
            const form = document.getElementById('formTitik');
            const methodInput = document.getElementById('formMethod');

            form.action = `/admin/lokasi/${l.id}`;
            methodInput.value = "PUT";

            document.getElementById('modalTitle').textContent = 'Edit Titik Presensi';
            document.getElementById('lokasiId').value = l.id;
            document.getElementById('nama').value = l.nama;
            document.getElementById('alamat').value = l.alamat || '';
            document.getElementById('latitude').value = l.latitude;
            document.getElementById('longitude').value = l.longitude;
            document.getElementById('radius').value = l.radius;
            document.getElementById('radiusSlider').value = l.radius;

            // Buka modal terlebih dahulu
            openModal('modalForm');

            // Set marker pada peta form SETELAH modal terbuka
            setTimeout(() => {
                // Pastikan peta form sudah terinisialisasi
                if (!mapForm) {
                    initMapForm();
                } else {
                    mapForm.invalidateSize(); // Pastikan ukuran peta benar
                }

                // Hapus marker sebelumnya jika ada
                if (currentMarker) {
                    mapForm.removeLayer(currentMarker);
                }
                if (currentCircle) {
                    mapForm.removeLayer(currentCircle);
                }

                // Tambahkan marker untuk lokasi yang diedit
                const latLng = [parseFloat(l.latitude), parseFloat(l.longitude)];
                currentMarker = L.marker(latLng).addTo(mapForm);
                currentCircle = L.circle(latLng, {
                    radius: l.radius,
                    color: '#4f46e5',
                    fillOpacity: 0.1
                }).addTo(mapForm);

                // Fokus ke lokasi marker
                mapForm.setView(latLng, 16);
            }, 300); // Delay untuk memastikan modal sudah terbuka sempurna
        }
    }

    // Fungsi untuk menghapus lokasi
    function deleteLokasi(id, nama) {
        document.getElementById('deleteNama').textContent = nama;
        document.getElementById('confirmDelete').onclick = function() {
            fetch(`/admin/lokasi/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    closeModal('modalDelete');
                    showNotification('Titik presensi berhasil dihapus', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Gagal menghapus titik presensi', 'error');
                }
            }).catch(error => {
                showNotification('Terjadi kesalahan saat menghapus', 'error');
            });
        };
        openModal('modalDelete');
    }

    // Fungsi untuk konfirmasi pembatalan
    function confirmCancel() {
        openModal('modalConfirmCancel');
    }

    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = `notification ${type} show`;

        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }

    // Event listener saat dokumen dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi peta utama
        initMap();

        // Event listener untuk tombol tambah - PERBAIKAN
        document.getElementById('btnTambah').addEventListener('click', function() {
            const form = document.getElementById('formTitik');
            const methodInput = document.getElementById('formMethod');

            form.action = "{{ route('admin.lokasi.store') }}";
            methodInput.value = "POST";
            document.getElementById('modalTitle').textContent = "Tambah Titik Presensi";
            document.getElementById('lokasiId').value = '';
            form.reset();
            document.getElementById('radius').value = 50;
            document.getElementById('radiusSlider').value = 50;

            // Buka modal terlebih dahulu
            openModal('modalForm');

            // Reset peta form SETELAH modal terbuka
            setTimeout(() => {
                // Pastikan peta form sudah terinisialisasi
                if (!mapForm) {
                    initMapForm();
                } else {
                    mapForm.invalidateSize();
                }

                // Hapus marker sebelumnya jika ada
                if (currentMarker) {
                    mapForm.removeLayer(currentMarker);
                }
                if (currentCircle) {
                    mapForm.removeLayer(currentCircle);
                }
                
                // Reset view ke default
                mapForm.setView([-2, 118], 5);
            }, 300);
        });

        // Event listener untuk slider radius
        document.getElementById('radiusSlider').addEventListener('input', function() {
            const radiusValue = this.value;
            document.getElementById('radius').value = radiusValue;

            // Update circle radius jika ada
            if (currentCircle && currentMarker) {
                const latLng = currentMarker.getLatLng();
                mapForm.removeLayer(currentCircle);
                currentCircle = L.circle(latLng, {
                    radius: radiusValue,
                    color: '#4f46e5',
                    fillOpacity: 0.1
                }).addTo(mapForm);
            }
        });

        // Event listener untuk input radius
        document.getElementById('radius').addEventListener('input', function() {
            let radiusValue = parseInt(this.value);
            if (radiusValue < 0) radiusValue = 0;
            if (radiusValue > 500) radiusValue = 500;

            this.value = radiusValue;
            document.getElementById('radiusSlider').value = radiusValue;

            // Update circle radius jika ada
            if (currentCircle && currentMarker) {
                const latLng = currentMarker.getLatLng();
                mapForm.removeLayer(currentCircle);
                currentCircle = L.circle(latLng, {
                    radius: radiusValue,
                    color: '#4f46e5',
                    fillOpacity: 0.1
                }).addTo(mapForm);
            }
        });

        // Event listener untuk form submit
        document.getElementById('formTitik').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validasi form
            const nama = document.getElementById('nama').value.trim();
            const latitude = document.getElementById('latitude').value.trim();
            const longitude = document.getElementById('longitude').value.trim();
            const radius = document.getElementById('radius').value;

            let isValid = true;

            // Validasi nama
            if (!nama) {
                document.getElementById('namaError').classList.remove('hidden');
                isValid = false;
            } else {
                document.getElementById('namaError').classList.add('hidden');
            }

            // Validasi radius
            if (radius < 0) {
                document.getElementById('radiusError').classList.remove('hidden');
                isValid = false;
            } else {
                document.getElementById('radiusError').classList.add('hidden');
            }

            // Validasi koordinat
            if (!latitude || !longitude) {
                showNotification('Silakan pilih lokasi pada peta', 'error');
                isValid = false;
            }

            if (!isValid) return;

            // Submit form
            const formData = new FormData(this);
            const url = this.action;
            const method = document.getElementById('formMethod').value;

            fetch(url, {
                    method: method === 'PUT' ? 'POST' : 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal('modalForm');
                        openModal('modalConfirmSave');
                    } else {
                        showNotification(data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Terjadi kesalahan saat menyimpan data', 'error');
                });
        });

        // Event listener untuk pencarian
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#titikTableBody tr');

            rows.forEach(row => {
                const nama = row.querySelector('td:first-child .font-medium').textContent.toLowerCase();
                const alamat = row.querySelector('td:first-child .text-gray-500').textContent.toLowerCase();

                if (nama.includes(searchTerm) || alamat.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        })
    });
</script>
@endpush