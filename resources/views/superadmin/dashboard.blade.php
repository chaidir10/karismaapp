@extends('layouts.superadmin')

@section('title', 'Dashboard Superadmin')
@push('styles')
<style>
    /* Custom animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .hover-lift {
        transition: all 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50/30 pb-8">


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Total Admin Card -->
            <div class="bg-white rounded-2xl shadow-lg hover-lift border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Admin</p>
                            <h3 class="text-3xl font-bold text-gray-900">{{ $totalAdmin ?? 0 }}</h3>
                            <p class="text-xs text-green-600 mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Active
                            </p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-xl">
                            <i class="fas fa-user-shield text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                    <p class="text-xs text-gray-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Administrator sistem
                    </p>
                </div>
            </div>

            <!-- Total Pegawai Card -->
            <div class="bg-white rounded-2xl shadow-lg hover-lift border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Pegawai</p>
                            <h3 class="text-3xl font-bold text-gray-900">{{ $totalPegawai ?? 0 }}</h3>
                            <p class="text-xs text-blue-600 mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Registered
                            </p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-xl">
                            <i class="fas fa-users text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                    <p class="text-xs text-gray-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Seluruh pegawai aktif
                    </p>
                </div>
            </div>

            <!-- Pengajuan Pending Card -->
            <div class="bg-white rounded-2xl shadow-lg hover-lift border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Pengajuan Pending</p>
                            <h3 class="text-3xl font-bold text-gray-900">{{ $pengajuanPending ?? 0 }}</h3>
                            <p class="text-xs text-orange-600 mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Menunggu review
                            </p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-xl">
                            <i class="fas fa-file-alt text-2xl text-orange-600"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                    <p class="text-xs text-gray-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Perlu persetujuan
                    </p>
                </div>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Daftar Admin --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-10 to-blue-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-user-shield text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Daftar Admin</h2>
                                <p class="text-sm text-gray-600">Administrator sistem</p>
                            </div>
                        </div>
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ count($admins ?? []) }} admin
                        </span>
                    </div>
                </div>
                
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($admins ?? [] as $index => $admin)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ substr($admin->name, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ $admin->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $admin->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Active
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-user-shield text-4xl mb-3"></i>
                                            <p class="text-lg font-medium text-gray-500">Tidak ada admin</p>
                                            <p class="text-sm text-gray-400 mt-1">Belum ada administrator terdaftar</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if(count($admins ?? []) > 0)
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Menampilkan {{ count($admins ?? []) }} administrator</span>
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                            Lihat semua
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @endif
            </div>

            {{-- Pengajuan Pending --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: 0.5s">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-orange-100 rounded-lg">
                                <i class="fas fa-file-alt text-orange-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Pengajuan Pending</h2>
                                <p class="text-sm text-gray-600">Menunggu persetujuan</p>
                            </div>
                        </div>
                        <span class="bg-orange-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ count($pengajuanList ?? []) }} menunggu
                        </span>
                    </div>
                </div>
                
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($pengajuanList ?? [] as $index => $peng)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ substr($peng->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ $peng->user->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $jenisColor = [
                                                'masuk' => 'bg-blue-100 text-blue-800',
                                                'pulang' => 'bg-green-100 text-green-800',
                                                'keduanya' => 'bg-purple-100 text-purple-800'
                                            ][$peng->jenis ?? 'masuk'] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jenisColor }}">
                                            {{ ucfirst($peng->jenis ?? '-') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-inbox text-4xl mb-3"></i>
                                            <p class="text-lg font-medium text-gray-500">Tidak ada pengajuan</p>
                                            <p class="text-sm text-gray-400 mt-1">Semua pengajuan telah diproses</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if(count($pengajuanList ?? []) > 0)
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Menampilkan {{ count($pengajuanList ?? []) }} pengajuan</span>
                        <a href="#" class="text-orange-600 hover:text-orange-800 font-medium flex items-center">
                            Lihat semua
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Kelola Admin</h3>
                        <p class="text-blue-100 text-sm">Tambah atau edit administrator</p>
                    </div>
                    <i class="fas fa-user-cog text-2xl opacity-80"></i>
                </div>
                <button class="mt-4 bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                    Kelola Sekarang
                </button>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Review Pengajuan</h3>
                        <p class="text-green-100 text-sm">Proses pengajuan presensi</p>
                    </div>
                    <i class="fas fa-clipboard-check text-2xl opacity-80"></i>
                </div>
                <button class="mt-4 bg-white text-green-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-50 transition-colors">
                    Review Sekarang
                </button>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Laporan Sistem</h3>
                        <p class="text-orange-100 text-sm">Lihat statistik lengkap</p>
                    </div>
                    <i class="fas fa-chart-bar text-2xl opacity-80"></i>
                </div>
                <button class="mt-4 bg-white text-orange-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-50 transition-colors">
                    Lihat Laporan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection