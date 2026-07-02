@extends('layouts.operator')
@section('title', 'Pengaturan')

@section('content')
<div class="page-header-glass">
    <h1>Pengaturan Aplikasi</h1>
    <p>Kelola logo, data instansi, dan konfigurasi sistem</p>
</div>

<!-- Instansi -->
<div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
    <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
        <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);"><i class="fas fa-building" style="color:#2E97D4; margin-right:6px;"></i> Data Instansi</h2>
    </div>
    <div class="px-6 py-4">
        <form method="POST" action="{{ route('operator.pengaturan.update-instansi') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:var(--dm-text,#374151); display:block; margin-bottom:4px;">Nama Instansi <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama" value="{{ $instansi->nama ?? '' }}" required
                        style="width:100%; padding:9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:10px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text);">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:var(--dm-text,#374151); display:block; margin-bottom:4px;">Kode Instansi <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="kode_instansi" value="{{ $instansi->kode_instansi ?? '' }}" required
                        style="width:100%; padding:9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:10px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text);">
                </div>
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px; font-weight:600; color:var(--dm-text,#374151); display:block; margin-bottom:4px;">Alamat</label>
                <textarea name="alamat" rows="2" style="width:100%; padding:9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:10px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text); resize:vertical;">{{ $instansi->alamat ?? '' }}</textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:var(--dm-text,#374151); display:block; margin-bottom:4px;">Email</label>
                    <input type="email" name="email" value="{{ $instansi->email ?? '' }}"
                        style="width:100%; padding:9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:10px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text);">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:var(--dm-text,#374151); display:block; margin-bottom:4px;">No. HP</label>
                    <input type="text" name="no_hp" value="{{ $instansi->no_hp ?? '' }}"
                        style="width:100%; padding:9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:10px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text);">
                </div>
            </div>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Simpan Instansi</button>
        </form>
    </div>
</div>

<!-- Logo -->
<div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
    <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
        <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);"><i class="fas fa-image" style="color:#2E97D4; margin-right:6px;"></i> Logo Aplikasi</h2>
    </div>
    <div class="px-6 py-4">
        <div class="text-xs mb-3" style="color:var(--dm-muted,#64748b);">Logo tampil di sidebar, tab browser, halaman login dan registrasi. Rekomendasi: persegi, minimal 512x512px, format PNG.</div>
        <form method="POST" action="{{ route('operator.pengaturan.upload-logo') }}" enctype="multipart/form-data" id="logoForm">
            @csrf
            <div style="display:flex; align-items:center; gap:16px;">
                <div id="logoPreviewBox" style="width:64px; height:64px; border-radius:14px; border:2px dashed var(--dm-border,#d1d5db); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; background:var(--dm-bg,#f9fafb);">
                    @if($appLogoUrl)
                        <img src="{{ $appLogoUrl }}" alt="Logo" style="width:100%; height:100%; object-fit:contain;">
                    @else
                        <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,#5AB6EA,#2E97D4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:16px;">K</div>
                    @endif
                </div>
                <div style="flex:1; min-width:0;">
                    <input type="file" name="app_logo" id="logoInput" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="display:none;">
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <button type="button" onclick="document.getElementById('logoInput').click()" class="btn-primary" style="padding:7px 14px; font-size:12px;"><i class="fas fa-upload"></i> Pilih Logo</button>
                        @if($appLogoUrl)
                        <button type="button" onclick="removeLogo()" class="btn-danger" style="padding:7px 14px; font-size:12px;"><i class="fas fa-trash"></i> Hapus</button>
                        @endif
                    </div>
                    <div id="logoFileName" style="font-size:11px; color:var(--dm-muted,#94a3b8); margin-top:6px;"></div>
                </div>
            </div>
        </form>
        <form method="POST" action="{{ route('operator.pengaturan.remove-logo') }}" id="removeLogoForm" style="display:none;">@csrf @method('DELETE')</form>
    </div>
</div>

<!-- Pengaturan Fitur -->
<form method="POST" action="{{ route('operator.pengaturan.update-settings') }}">
    @csrf

    <div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);"><i class="fas fa-sliders" style="color:#2E97D4; margin-right:6px;"></i> Konfigurasi Presensi</h2>
        </div>
        <div class="px-6 py-4">
            <label class="flex items-center justify-between cursor-pointer">
                <div>
                    <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Nonaktifkan presensi reguler di hari libur</div>
                    <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Tombol masuk & pulang reguler di-disable saat hari libur/weekend. Lembur tetap bisa.</div>
                </div>
                <div class="relative ml-4 flex-shrink-0">
                    <input type="checkbox" name="disable_presensi_hari_libur" value="1" class="sr-only peer" {{ ($settings['disable_presensi_hari_libur'] ?? '1') === '1' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                </div>
            </label>
        </div>
    </div>

    <div class="overflow-hidden mb-6" style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px;">
        <div class="px-6 py-4" style="border-bottom:1px solid var(--dm-border,#e2e8f0);">
            <h2 class="text-base font-semibold" style="color:var(--dm-text,#1e293b);"><i class="fas fa-shield-halved" style="color:#2E97D4; margin-right:6px;"></i> Keamanan & Fitur</h2>
        </div>
        <div class="px-6 py-4 space-y-4">
            <label class="flex items-center justify-between cursor-pointer">
                <div>
                    <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Aktifkan Face Detection</div>
                    <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Wajah harus terdeteksi sebelum absen.</div>
                </div>
                <div class="relative ml-4 flex-shrink-0">
                    <input type="checkbox" name="enable_face_detection" value="1" class="sr-only peer" id="toggleFace" {{ ($settings['enable_face_detection'] ?? '1') === '1' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                </div>
            </label>

            @php
                $faceMode = $settings['face_detection_mode'] ?? 'all';
                $faceExceptIds = json_decode($settings['face_detection_users_except'] ?? '[]', true) ?: [];
                $faceOnlyIds = json_decode($settings['face_detection_users_only'] ?? '[]', true) ?: [];
            @endphp
            <input type="hidden" name="face_detection_mode" id="faceDetectMode" value="{{ $faceMode }}">
            <div id="faceSection" style="{{ ($settings['enable_face_detection'] ?? '1') !== '1' ? 'display:none;' : '' }}">
                <div class="setting-tabs" style="margin-bottom:8px;">
                    <button type="button" class="setting-tab {{ $faceMode === 'all' ? 'active' : '' }}" onclick="event.preventDefault();setMode('face',this,'all')"><i class="fas fa-users"></i> Semua</button>
                    <button type="button" class="setting-tab {{ $faceMode === 'except' ? 'active' : '' }}" onclick="event.preventDefault();setMode('face',this,'except')"><i class="fas fa-user-minus"></i> Kecuali</button>
                    <button type="button" class="setting-tab {{ $faceMode === 'only' ? 'active' : '' }}" onclick="event.preventDefault();setMode('face',this,'only')"><i class="fas fa-user-check"></i> Hanya</button>
                </div>
            </div>

            <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Wajib masuk sebelum pulang</div>
                        <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Pulang hanya aktif jika sudah presensi masuk.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="require_masuk_before_pulang" value="1" class="sr-only peer" {{ ($settings['require_masuk_before_pulang'] ?? '1') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>
            </div>

            <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Timer Jam Kerja</div>
                        <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Tampilkan timer di dashboard pegawai.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="enable_work_timer" value="1" class="sr-only peer" {{ ($settings['enable_work_timer'] ?? '1') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>
            </div>

            <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Banner Aktifkan Notifikasi</div>
                        <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Tampilkan banner di dashboard pegawai untuk mengaktifkan push notification HP.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="show_notif_banner" value="1" class="sr-only peer" {{ ($settings['show_notif_banner'] ?? '1') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>
            </div>

            <div class="pt-4" style="border-top:1px solid var(--dm-border,#e2e8f0);">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="font-medium text-sm" style="color:var(--dm-text,#1e293b);">Aktifkan Absen Darurat</div>
                        <div class="text-xs mt-1" style="color:var(--dm-muted,#64748b);">Halaman absen alternatif tanpa cache & face detection.</div>
                    </div>
                    <div class="relative ml-4 flex-shrink-0">
                        <input type="checkbox" name="enable_absen_darurat" value="1" class="sr-only peer" id="toggleDarurat" {{ ($settings['enable_absen_darurat'] ?? '0') === '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    </div>
                </label>

                @php
                    $daruratMode = $settings['absen_darurat_mode'] ?? 'all';
                    $daruratExceptIds = json_decode($settings['absen_darurat_users_except'] ?? '[]', true) ?: [];
                    $daruratOnlyIds = json_decode($settings['absen_darurat_users_only'] ?? '[]', true) ?: [];
                @endphp
                <input type="hidden" name="absen_darurat_mode" id="daruratMode" value="{{ $daruratMode }}">
                <div id="daruratSection" style="margin-top:12px; {{ ($settings['enable_absen_darurat'] ?? '0') !== '1' ? 'display:none;' : '' }}">
                    <div class="setting-tabs" style="margin-bottom:8px;">
                        <button type="button" class="setting-tab {{ $daruratMode === 'all' ? 'active-red' : '' }}" onclick="event.preventDefault();setMode('darurat',this,'all')"><i class="fas fa-users"></i> Semua</button>
                        <button type="button" class="setting-tab {{ $daruratMode === 'except' ? 'active-red' : '' }}" onclick="event.preventDefault();setMode('darurat',this,'except')"><i class="fas fa-user-minus"></i> Kecuali</button>
                        <button type="button" class="setting-tab {{ $daruratMode === 'only' ? 'active-red' : '' }}" onclick="event.preventDefault();setMode('darurat',this,'only')"><i class="fas fa-user-check"></i> Hanya</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="faceExceptInputs">@foreach($faceExceptIds as $uid)<input type="hidden" name="face_detection_users_except[]" value="{{ $uid }}">@endforeach</div>
    <div id="faceOnlyInputs">@foreach($faceOnlyIds as $uid)<input type="hidden" name="face_detection_users_only[]" value="{{ $uid }}">@endforeach</div>
    <div id="daruratExceptInputs">@foreach($daruratExceptIds as $uid)<input type="hidden" name="absen_darurat_users_except[]" value="{{ $uid }}">@endforeach</div>
    <div id="daruratOnlyInputs">@foreach($daruratOnlyIds as $uid)<input type="hidden" name="absen_darurat_users_only[]" value="{{ $uid }}">@endforeach</div>

    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Simpan Pengaturan</button>
</form>

@push('styles')
<style>
    .setting-tabs { display:flex; gap:4px; padding:3px; background:var(--dm-bg,#f1f5f9); border-radius:10px; border:1px solid var(--dm-border,#e2e8f0); }
    .setting-tab { flex:1; padding:7px 6px; border:none; border-radius:8px; font-size:12px; font-weight:600; background:transparent; color:var(--dm-muted,#94a3b8); cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px; transition:all 0.2s; }
    .setting-tab:hover { color:var(--dm-text,#475569); }
    .setting-tab.active { background:#5AB6EA; color:#fff; box-shadow:0 2px 6px rgba(90,182,234,0.25); }
    .setting-tab.active-red { background:#ef4444; color:#fff; box-shadow:0 2px 6px rgba(239,68,68,0.25); }
    [data-theme="dark"] .setting-tabs { background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.06); }
    [data-theme="dark"] .setting-tab { color:rgba(255,255,255,0.4); }
</style>
@endpush
@push('scripts')
<script>
    document.getElementById('toggleFace').addEventListener('change',function(){
        document.getElementById('faceSection').style.display=this.checked?'':'none';
    });
    document.getElementById('toggleDarurat').addEventListener('change',function(){
        document.getElementById('daruratSection').style.display=this.checked?'':'none';
    });
    function setMode(target,btn,val){
        var tabs=btn.parentElement.querySelectorAll('.setting-tab');
        var cls=target==='darurat'?'active-red':'active';
        tabs.forEach(function(t){t.classList.remove('active','active-red');});
        btn.classList.add(cls);
        document.getElementById(target==='face'?'faceDetectMode':'daruratMode').value=val;
    }
    document.getElementById('logoInput').addEventListener('change',function(){
        var file=this.files[0]; if(!file)return;
        if(file.size>2*1024*1024){showError('Ukuran file maksimal 2MB');this.value='';return;}
        document.getElementById('logoFileName').textContent=file.name;
        var reader=new FileReader();
        reader.onload=function(e){document.getElementById('logoPreviewBox').innerHTML='<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:contain;">';};
        reader.readAsDataURL(file);
        document.getElementById('logoForm').submit();
    });
    function removeLogo(){
        showConfirm({type:'danger',title:'Hapus Logo?',message:'Logo akan dikembalikan ke default.',onConfirm:function(){document.getElementById('removeLogoForm').submit();}});
    }
</script>
@endpush
@endsection
