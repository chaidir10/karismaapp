@extends('layouts.operator')
@section('title', 'Akun Operator')

@section('content')
<div class="page-header-glass">
    <h1><i class="fas fa-user-shield" style="margin-right:8px;"></i> Akun Operator</h1>
    <p>Kelola email dan password akun operator Anda</p>
</div>

<div class="akun-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; max-width:900px;">
    {{-- Ubah Email --}}
    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid var(--dm-border,#e2e8f0); display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; border-radius:10px; background:rgba(90,182,234,0.12); color:#2E97D4; display:flex; align-items:center; justify-content:center; font-size:14px;">
                <i class="fas fa-envelope"></i>
            </div>
            <div>
                <div style="font-size:14px; font-weight:700; color:var(--dm-text,#1e293b);">Ubah Email</div>
                <div style="font-size:11px; color:var(--dm-muted,#64748b);">Email digunakan untuk login ke sistem</div>
            </div>
        </div>
        <form method="POST" action="{{ route('operator.akun.update-email') }}" style="padding:20px;">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-size:12px; font-weight:600; color:var(--dm-muted,#64748b); display:block; margin-bottom:5px;">Email Saat Ini</label>
                <div style="padding:9px 12px; border:1px solid var(--dm-border,#e2e8f0); border-radius:8px; font-size:13px; color:var(--dm-muted,#94a3b8); background:var(--dm-bg,#f9fafb);">
                    {{ Auth::user()->email }}
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:var(--dm-text,#1e293b); display:block; margin-bottom:5px;">Email Baru</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                    style="width:100%; padding:9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text); outline:none; box-sizing:border-box;"
                    placeholder="email@contoh.com">
                @error('email')
                <div style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">
                <i class="fas fa-save"></i> Simpan Email
            </button>
        </form>
    </div>

    {{-- Ubah Password --}}
    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid var(--dm-border,#e2e8f0); display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; border-radius:10px; background:rgba(245,158,11,0.12); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:14px;">
                <i class="fas fa-lock"></i>
            </div>
            <div>
                <div style="font-size:14px; font-weight:700; color:var(--dm-text,#1e293b);">Ubah Password</div>
                <div style="font-size:11px; color:var(--dm-muted,#64748b);">Gunakan password yang kuat dan unik</div>
            </div>
        </div>
        <form method="POST" action="{{ route('operator.akun.update-password') }}" style="padding:20px;">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-size:12px; font-weight:600; color:var(--dm-text,#1e293b); display:block; margin-bottom:5px;">Password Lama</label>
                <div style="position:relative;">
                    <input type="password" name="current_password" required id="currentPw"
                        style="width:100%; padding:9px 36px 9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text); outline:none; box-sizing:border-box;"
                        placeholder="Masukkan password lama">
                    <button type="button" onclick="togglePw('currentPw',this)" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--dm-muted,#94a3b8); cursor:pointer; font-size:13px; padding:4px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('current_password')
                <div style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px; font-weight:600; color:var(--dm-text,#1e293b); display:block; margin-bottom:5px;">Password Baru</label>
                <div style="position:relative;">
                    <input type="password" name="password" required id="newPw" minlength="6"
                        style="width:100%; padding:9px 36px 9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text); outline:none; box-sizing:border-box;"
                        placeholder="Minimal 6 karakter">
                    <button type="button" onclick="togglePw('newPw',this)" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--dm-muted,#94a3b8); cursor:pointer; font-size:13px; padding:4px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                <div style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:var(--dm-text,#1e293b); display:block; margin-bottom:5px;">Konfirmasi Password Baru</label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" required id="confirmPw" minlength="6"
                        style="width:100%; padding:9px 36px 9px 12px; border:1px solid var(--dm-input-border,#d1d5db); border-radius:8px; font-size:13px; background:var(--dm-input,#fff); color:var(--dm-text); outline:none; box-sizing:border-box;"
                        placeholder="Ulangi password baru">
                    <button type="button" onclick="togglePw('confirmPw',this)" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--dm-muted,#94a3b8); cursor:pointer; font-size:13px; padding:4px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">
                <i class="fas fa-shield-halved"></i> Ubah Password
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
    @media (max-width:768px) {
        .akun-grid { grid-template-columns:1fr !important; }
    }
</style>
@endpush

@push('scripts')
<script>
function togglePw(id, btn) {
    var el = document.getElementById(id);
    var icon = btn.querySelector('i');
    if (el.type === 'password') {
        el.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        el.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection
