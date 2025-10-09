@extends('layouts.admin')

@section('title', 'Manajemen Laporan Kehadiran')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
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
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-8 shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-2xl font-bold text-white">Manajemen Laporan Kehadiran</h1>
                <p class="text-indigo-100 mt-1">Kelola dan pantau laporan kehadiran pegawai</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl p-6 shadow-md mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filter Laporan</h2>
        <form id="formFilter" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label class="text-sm font-medium block mb-2 text-gray-700">Pilih Pegawai</label>
                <select name="user_id" id="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 text-sm">
                    <option value="">Semua Pegawai</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->nip }})</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="text-sm font-medium block mb-2 text-gray-700">Bulan</label>
                <input type="month" name="bulan" id="bulan" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 text-sm" value="{{ now()->format('Y-m') }}">
            </div>
            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition-colors flex items-center justify-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    Tampilkan Laporan
                </button>
                <a href="#" id="btnPdf" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 transition-colors flex items-center justify-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    PDF
                </a>
                <a href="#" id="btnExcel" class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700 transition-colors flex items-center justify-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Laporan -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pulang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Keterlambatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pulang Cepat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Jam Kerja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Waktu Kurang</th>
                    </tr>
                </thead>
                <tbody id="laporanBody" class="bg-white divide-y divide-gray-200 text-xs">
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">Pilih filter dan klik "Tampilkan Laporan"</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Notification -->
<div id="notification" class="notification"></div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = `notification ${type} show`;

        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk form filter
        document.getElementById('formFilter').addEventListener('submit', function(e) {
            e.preventDefault();

            const userId = document.getElementById('user_id').value;
            const bulan = document.getElementById('bulan').value;

            // Tampilkan loading state
            const tbody = document.getElementById('laporanBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="loading mb-3"></div>
                            <p class="text-sm font-medium">Memuat data laporan...</p>
                        </div>
                    </td>
                </tr>
            `;

            fetch("{{ route('admin.laporan.data') }}?user_id=" + userId + "&bulan=" + bulan)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('laporanBody');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm font-medium">Data tidak ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba ubah filter pencarian</p>
                                </div>
                            </td>
                        </tr>
                    `;
                        return;
                    }

                    data.forEach(item => {
                        item.rows.forEach(row => {
                            // Format jam kerja
                            let jamKerja = row.jam_kerja;
                            if (!isNaN(jamKerja)) {
                                if (row.is_weekend) {
                                    const jam = Math.floor(jamKerja / 60);
                                    jamKerja = `${jam} jam (lembur)`;
                                } else {
                                    const jam = Math.floor(jamKerja / 60);
                                    const menit = jamKerja % 60;
                                    jamKerja = `${jam > 0 ? jam+' jam ' : ''}${menit > 0 ? menit+' menit' : (jam > 0 ? '' : '0 menit')}`;
                                }
                            }

                            // Format status dengan warna
                            const keterlambatanClass = row.keterlambatan > 0 ? 'text-red-600 font-medium' : 'text-gray-600';
                            const pulangCepatClass = row.pulang_cepat > 0 ? 'text-orange-600 font-medium' : 'text-gray-600';
                            const waktuKurangClass = row.waktu_kurang > 0 ? 'text-red-600 font-medium' : 'text-gray-600';

                            tbody.innerHTML += `
                            <tr class="transition-colors ${row.is_weekend ? 'bg-gray-100' : 'hover:bg-gray-50'}">

                                <td class="px-4 py-3">
                                    <div class="font-medium text-xs text-gray-900">${item.user.name}</div>
                                    <div class="text-gray-500 text-xs mt-1">${item.user.nip || 'Tidak ada NIP'}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-900">${row.tanggal}</td>
                                <td class="px-4 py-3 text-xs text-gray-900">${row.masuk || '-'}</td>
                                <td class="px-4 py-3 text-xs text-gray-900">${row.pulang || '-'}</td>
                                <td class="px-4 py-3 text-xs ${keterlambatanClass}">
    ${row.keterlambatan && row.keterlambatan != '-' && row.keterlambatan != 0 ? row.keterlambatan + ' menit' : '-'}
</td>
<td class="px-4 py-3 text-xs ${pulangCepatClass}">
    ${row.pulang_cepat && row.pulang_cepat != '-' && row.pulang_cepat != 0 ? row.pulang_cepat + ' menit' : '-'}
</td>
<td class="px-4 py-3 text-xs text-gray-900">${jamKerja}</td>
<td class="px-4 py-3 text-xs ${waktuKurangClass}">
    ${row.waktu_kurang && row.waktu_kurang != '-' && row.waktu_kurang != 0 ? row.waktu_kurang + ' menit' : '-'}
</td>

                            </tr>
                        `;
                        });
                    });

                    showNotification('Data laporan berhasil dimuat', 'success');
                })
                .catch(error => {
                    const tbody = document.getElementById('laporanBody');
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-sm font-medium text-red-600">Terjadi kesalahan</p>
                                <p class="text-xs text-gray-400 mt-1">Gagal memuat data laporan</p>
                            </div>
                        </td>
                    </tr>
                `;
                    showNotification('Gagal memuat data laporan', 'error');
                });
        });

        // Export PDF
        document.getElementById('btnPdf').addEventListener('click', function(e) {
            e.preventDefault();
            const userId = document.getElementById('user_id').value;
            const bulan = document.getElementById('bulan').value;
            const mode = userId ? 'single' : 'all';

            if (!bulan) {
                showNotification('Pilih bulan terlebih dahulu', 'error');
                return;
            }

            window.open(`{{ route('admin.laporan.pdf') }}?user_id=${userId}&bulan=${bulan}&mode=${mode}`, '_blank');
            showNotification('Membuat laporan PDF...', 'success');
        });

        // Export Excel
        document.getElementById('btnExcel').addEventListener('click', function(e) {
            e.preventDefault();
            const userId = document.getElementById('user_id').value;
            const bulan = document.getElementById('bulan').value;

            if (!bulan) {
                showNotification('Pilih bulan terlebih dahulu', 'error');
                return;
            }

            window.location.href = `{{ route('admin.laporan.excel') }}?user_id=${userId}&bulan=${bulan}`;
            showNotification('Membuat laporan Excel...', 'success');
        });
    });

    // CSS untuk loading spinner
    const style = document.createElement('style');
    style.textContent = `
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
    // Contoh penggunaan untuk download laporan
    document.addEventListener('DOMContentLoaded', function() {
        // Untuk link download biasa
        document.querySelectorAll('.download-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                handleDownload(this, url, 'GET');
            });
        });

        // Untuk form download dengan filter
        document.querySelectorAll('.download-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const url = this.action;
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                handleDownload(this.querySelector('button[type="submit"]'), url, 'POST', data);
            });
        });

        // Untuk button download dengan AJAX
        document.querySelectorAll('.download-btn').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.dataset.url;
                const method = this.dataset.method || 'GET';
                const data = this.dataset.params ? JSON.parse(this.dataset.params) : null;
                handleDownload(this, url, method, data);
            });
        });
    });
</script>

@endpush