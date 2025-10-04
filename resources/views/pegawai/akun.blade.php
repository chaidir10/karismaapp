@extends('layouts.pegawai')

@section('title', 'Akun Saya')

@section('content')
<div class="pt-6">
    {{-- Notifikasi sukses --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg m-4 flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Notifikasi error --}}
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg m-4 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        Terjadi kesalahan. Silakan periksa form di bawah.
    </div>
    @endif

    {{-- Modal Konfirmasi Logout --}}
    <div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 relative">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-out-alt text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Logout</h3>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin keluar dari aplikasi?</p>

                <div class="flex gap-3">
                    <button type="button" id="logoutCancelBtn"
                        class="flex-1 px-4 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition-all duration-300 font-medium text-sm">
                        Batal
                    </button>
                    <button type="button" id="logoutConfirmBtn"
                        class="flex-1 px-4 py-3 rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all duration-300 font-medium text-sm">
                        Ya, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Card --}}
    <div class="bg-white mx-4 mt-4 rounded-2xl shadow-xl p-6 border border-gray-100">
        {{-- Foto Profil --}}
        <div class="flex justify-center mb-4">
            <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden flex items-center justify-center bg-blue-100 text-white text-2xl font-bold">
                @if($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil))
                <img src="{{ asset('public/storage/foto_profil/' . $user->foto_profil) }}"
                    alt="Foto Profil {{ $user->name }}"
                    class="w-full h-full object-cover"
                    onerror="this.style.display='none'; this.parentNode.innerHTML='{{ collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->join('') }}'">
                @else
                {{ collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->join('') }}
                @endif
            </div>
        </div>



        {{-- Nama dan Jabatan --}}
        <h3 class="text-xl font-bold text-center text-gray-800 mb-2">{{ $user->name ?? 'Nama Pengguna' }}</h3>
        <div class="text-center mb-6">
            <span class="bg-blue-50 text-blue-600 text-xs font-medium px-4 py-1 rounded-full">
                {{ $user->jabatan ?? 'Pegawai' }}
            </span>
        </div>

        {{-- Informasi Profil --}}
        <div class="space-y-4 mb-6">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 text-blue-600">
                    <i class="fas fa-phone text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700">No HP</p>
                    <p class="text-sm text-gray-600">{{ $user->no_hp ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 text-blue-600">
                    <i class="fas fa-envelope text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700">Email</p>
                    <p class="text-sm text-gray-600">{{ $user->email ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 text-blue-600">
                    <i class="fas fa-map-marker-alt text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700">Alamat</p>
                    <p class="text-sm text-gray-600">{{ $user->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Tombol Edit --}}
        <button onclick="openModal('editModal')" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-2 shadow-lg">
            <i class="fas fa-edit"></i>
            Edit Profil
        </button>
    </div>

    {{-- Menu Section --}}
    <div class="mx-4 mb-20 mt-4">
        <div class="space-y-3">
            {{-- Logout --}}
            <form action="{{ route('pegawai.akun.logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="button" onclick="openLogoutModal()" class="w-full flex items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-md text-left">
                    <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center mr-3 text-red-500">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-sm">Keluar</div>
                        <div class="text-xs text-gray-500">Logout dari aplikasi</div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Modal Edit Akun --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 relative max-h-[90vh] overflow-y-auto">
            <button type="button"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors duration-200"
                onclick="closeModal('editModal')">
                <i class="fas fa-times text-lg"></i>
            </button>

            <h3 class="text-xl font-bold text-gray-800 mb-6">
                Edit Profil
            </h3>

            {{-- Sesuaikan dengan route yang ada --}}
            <form action="{{ route('pegawai.akun.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('name')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- No HP --}}
                <div>
                    <label for="no_hp" class="block text-gray-700 text-sm font-medium mb-2">No HP</label>
                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('no_hp')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <label for="alamat" class="block text-gray-700 text-sm font-medium mb-2">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('alamat', $user->alamat) }}</textarea>
                    @error('alamat')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password Baru</label>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin diubah"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Foto Profil --}}
                <div>
                    <label for="foto_profil" class="block text-gray-700 text-sm font-medium mb-2">Foto Profil</label>
                    <input type="file" name="foto_profil" id="foto_profil" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('foto_profil')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    @if($user->foto_profil)
                    <p class="text-xs text-gray-500 mt-1">Foto profil saat ini: {{ $user->foto_profil }}</p>
                    @endif
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3 pt-4">
                    <button type="button"
                        class="flex-1 px-4 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition-all duration-300 font-medium text-sm"
                        onclick="closeModal('editModal')">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-blue-500 text-white hover:bg-blue-600 transition-all duration-300 font-medium text-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openLogoutModal() {
        openModal('logoutModal');
    }

    function closeLogoutModal() {
        closeModal('logoutModal');
    }

    // Initialize logout modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        const logoutModal = document.getElementById('logoutModal');
        const logoutCancelBtn = document.getElementById('logoutCancelBtn');
        const logoutConfirmBtn = document.getElementById('logoutConfirmBtn');
        const logoutForm = document.getElementById('logoutForm');

        // Cancel logout
        logoutCancelBtn.addEventListener('click', closeLogoutModal);

        // Confirm logout
        logoutConfirmBtn.addEventListener('click', function() {
            logoutForm.submit();
        });

        // Close modal when clicking outside
        logoutModal.addEventListener('click', function(e) {
            if (e.target === logoutModal) {
                closeLogoutModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!logoutModal.classList.contains('hidden')) {
                    closeLogoutModal();
                }
                if (!document.getElementById('editModal').classList.contains('hidden')) {
                    closeModal('editModal');
                }
            }
        });
    });

    // Close modal when clicking outside for edit modal
    document.addEventListener('click', (e) => {
        if (e.target.id === 'editModal') {
            closeModal('editModal');
        }
    });

    // Prevent modal close when clicking inside modal content
    document.querySelectorAll('#editModal > div').forEach(modalContent => {
        modalContent.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Prevent modal close when clicking inside logout modal content
    document.querySelectorAll('#logoutModal > div').forEach(modalContent => {
        modalContent.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Handle image loading errors
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            img.addEventListener('error', function() {
                this.src = 'https://avatar.iran.liara.run/public/48';
            });
        });
    });
</script>
@endsection