@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-gradient-to-r from-gray-600 to-gray-800 rounded-xl p-6 mb-8 shadow-lg">
        <h1 class="text-2xl font-bold text-white">Pengaturan</h1>
        <p class="text-gray-300 mt-1">Konfigurasi sistem aplikasi</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.pengaturan.update') }}">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Presensi</h2>
            </div>
            <div class="px-6 py-4">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm text-gray-800">Nonaktifkan presensi reguler di hari libur</div>
                        <div class="text-xs text-gray-500 mt-1">Tombol masuk & pulang reguler akan di-disable saat hari libur/weekend. Lembur tetap bisa digunakan.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="disable_presensi_hari_libur" value="1" class="sr-only peer" {{ ($settings['disable_presensi_hari_libur'] ?? '1') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-blue-700 transition-colors flex items-center gap-2">
            <i class="fas fa-save"></i> Simpan Pengaturan
        </button>
    </form>
</div>
@endsection
