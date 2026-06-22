@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div>
    <div class="page-header-glass">
        <h1>Pengaturan</h1>
        <p>Konfigurasi sistem aplikasi</p>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2" style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); color:var(--dm-text,#15803d);">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.pengaturan.update') }}">
        @csrf

        <div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
            <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
                <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);">Presensi</h2>
            </div>
            <div class="px-6 py-4">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Nonaktifkan presensi reguler di hari libur</div>
                        <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Tombol masuk & pulang reguler akan di-disable saat hari libur/weekend. Lembur tetap bisa digunakan.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="disable_presensi_hari_libur" value="1" class="sr-only peer" {{ ($settings['disable_presensi_hari_libur'] ?? '1') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Face Detection -->
        <div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
            <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
                <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);">Keamanan</h2>
            </div>
            <div class="px-6 py-4 space-y-4">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Aktifkan Face Detection</div>
                        <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Wajah harus terdeteksi sebelum bisa absen. Jika dinonaktifkan, pegawai bisa langsung absen tanpa verifikasi wajah.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="enable_face_detection" value="1" class="sr-only peer" id="toggleFaceDetect" {{ ($settings['enable_face_detection'] ?? '1') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>

                @php $faceUserIds = json_decode($settings['face_detection_users'] ?? '[]', true) ?: []; @endphp
                <div id="faceDetectUserSection" style="margin-top:12px; {{ ($settings['enable_face_detection'] ?? '1') !== '1' ? 'display:none;' : '' }}">
                    <div class="text-xs mb-2" style="color:var(--dm-muted,#64748b);">Pilih pegawai yang wajib face detection. <strong>Kosongkan</strong> = semua pegawai wajib.</div>
                    <div style="display:flex; flex-wrap:wrap; gap:6px; padding:10px; border:1px solid var(--dm-border,#d1d5db); border-radius:10px; max-height:160px; overflow-y:auto;">
                        @foreach(\App\Models\User::where('role','!=','superadmin')->orderBy('name')->get() as $pg)
                        <label style="display:flex; align-items:center; gap:5px; cursor:pointer; font-size:12px; color:var(--dm-text,#374151); padding:4px 10px; border-radius:8px; background:var(--dm-bg,#f9fafb);">
                            <input type="checkbox" name="face_detection_users[]" value="{{ $pg->id }}" {{ in_array($pg->id, $faceUserIds) ? 'checked' : '' }}>
                            {{ $pg->name }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Wajib masuk sebelum pulang</div>
                            <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Tombol pulang reguler hanya aktif jika pegawai sudah presensi masuk reguler di hari tersebut.</div>
                        </div>
                        <div class="relative ml-4 flex-shrink-0">
                            <input type="checkbox" name="require_masuk_before_pulang" value="1" class="sr-only peer" {{ ($settings['require_masuk_before_pulang'] ?? '1') === '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                        </div>
                    </label>
                <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Aktifkan Absen Darurat</div>
                            <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Halaman absen alternatif tanpa cache dan face detection. Aktifkan jika sistem utama mengalami gangguan.</div>
                        </div>
                        <div class="relative ml-4 flex-shrink-0">
                            <input type="checkbox" name="enable_absen_darurat" value="1" class="sr-only peer" id="toggleDarurat" {{ ($settings['enable_absen_darurat'] ?? '0') === '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                        </div>
                    </label>

                    @php $allowedIds = json_decode($settings['absen_darurat_users'] ?? '[]', true) ?: []; @endphp
                    <div id="daruratUserSection" style="margin-top:12px; {{ ($settings['enable_absen_darurat'] ?? '0') !== '1' ? 'display:none;' : '' }}">
                        <div class="text-xs mb-2" style="color:var(--dm-muted,#64748b);">Pilih pegawai yang boleh akses absen darurat. <strong>Kosongkan</strong> = semua pegawai boleh.</div>
                        <div style="display:flex; flex-wrap:wrap; gap:6px; padding:10px; border:1px solid var(--dm-border,#d1d5db); border-radius:10px; max-height:160px; overflow-y:auto;">
                            @foreach(\App\Models\User::where('role','!=','superadmin')->orderBy('name')->get() as $pg)
                            <label style="display:flex; align-items:center; gap:5px; cursor:pointer; font-size:12px; color:var(--dm-text,#374151); padding:4px 10px; border-radius:8px; background:var(--dm-bg,#f9fafb);">
                                <input type="checkbox" name="absen_darurat_users[]" value="{{ $pg->id }}" {{ in_array($pg->id, $allowedIds) ? 'checked' : '' }}>
                                {{ $pg->name }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Simpan Pengaturan
        </button>
    </form>
</div>
@push('scripts')
<script>
    document.getElementById('toggleDarurat').addEventListener('change', function() {
        document.getElementById('daruratUserSection').style.display = this.checked ? '' : 'none';
    });
    document.getElementById('toggleFaceDetect').addEventListener('change', function() {
        document.getElementById('faceDetectUserSection').style.display = this.checked ? '' : 'none';
    });
</script>
@endpush
@endsection
