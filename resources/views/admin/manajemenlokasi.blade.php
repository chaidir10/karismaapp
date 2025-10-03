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

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-icon:hover {
        transform: scale(1.05);
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
                class="w-full sm:w-auto bg-white hover:bg-gray-50 text-indigo-600 px-5 py-3 rounded-xl flex items-center justify-center transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Cari titik presensi..."
                        class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-1 focus:outline-none text-sm shadow-sm" />
                </div>
                <button id="btnSort"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 flex items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M5 12a1 1 0 110-2h10a1 1 0 110 2H5zM5 8a1 1 0 110-2h6a1 1 0 110 2H5zM5 16a1 1 0 110-2h4a1 1 0 110 2H5z" />
                    </svg>
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
                                    <div class="flex justify-end space-x-1">
                                        <button onclick="showDetail({{ $item->id }})" class="btn-icon bg-green-100 text-green-700 hover:bg-green-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button onclick="editLokasi({{ $item->id }})" class="btn-icon bg-blue-100 text-blue-700 hover:bg-blue-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.380-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button onclick="deleteLokasi({{ $item->id }}, '{{ $item->nama }}')" class="btn-icon bg-red-100 text-red-700 hover:bg-red-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">
                                    <div id="emptyState" class="empty-state p-6 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3 mx-auto" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
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
                        onclick="confirmCancel()">Batal</button>

                    <!-- Tombol Simpan -->
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-lg font-bold mb-4">Detail Titik Presensi</h2>
        <div class="grid grid-cols-12 gap-4">
            <!-- Map -->
            <div class="col-span-12 md:col-span-7">
                <div id="mapDetail" class="h-80 md:h-96 w-full rounded-xl border"></div>
                <div class="mt-2 text-[10px] text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="mb-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <h2 class="text-xl font-bold mb-2">Hapus Titik Presensi</h2>
        <p class="text-gray-600">Apakah Anda yakin ingin menghapus <span id="deleteNama"
                class="font-semibold text-gray-800"></span>?</p>
        <p class="text-xs text-gray-500 mt-2">Data yang dihapus tidak dapat dikembalikan</p>
        <div class="flex justify-center gap-3 mt-6">
            <button
                class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors text-sm"
                onclick="closeModal('modalDelete')">Batal</button>
            <button id="confirmDelete"
                class="px-5 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="mb-4">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <h2 class="text-xl font-bold mb-2">Batalkan Perubahan</h2>
        <p class="text-gray-600">Apakah Anda yakin ingin membatalkan perubahan yang telah dibuat?</p>
        <p class="text-xs text-gray-500 mt-2">Semua perubahan yang belum disimpan akan hilang</p>
        <div class="flex justify-center gap-3 mt-6">
            <button
                class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors text-sm"
                onclick="closeModal('modalConfirmCancel')">Lanjutkan Edit</button>
            <button
                class="px-5 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors flex items-center text-sm"
                onclick="closeModal('modalForm'); closeModal('modalConfirmCancel');">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="mb-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        <h2 class="text-xl font-bold mb-2">Berhasil</h2>
        <p class="text-gray-600">Data titik presensi berhasil disimpan</p>
        <div class="flex justify-center mt-6">
            <button
                class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors flex items-center text-sm"
                onclick="closeModal('modalConfirmSave'); closeModal('modalForm'); location.reload();">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
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
        });
    });
</script>
@endpush