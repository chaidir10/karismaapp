@extends('layouts.superadmin')

@section('title', 'Manajemen Admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section with Gradient -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-5 shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-2xl font-bold text-white">Manajemen Admin</h1>
                <p class="text-blue-100 mt-1">Kelola data seluruh admin</p>
            </div>
            <button onclick="openModal('modalTambah')" class="w-full sm:w-auto bg-white hover:bg-gray-100 text-blue-600 px-5 py-3 rounded-xl flex items-center justify-center transition-all duration-300 transform hover:scale-105 shadow-md">
                <i class="fas fa-user-plus mr-2"></i>
                <span class="font-medium">Tambah Admin</span>
            </button>
        </div>
    </div>

   
    <!-- Admin Table Card -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th colspan="5" class="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                            Total Admin: <span id="totalAdmin">{{ $admins->count() }}</span>
                        </th>
                    </tr>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Bisa Approve</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($admins as $i => $admin)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $i + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $admin->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $admin->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $admin->can_approve_pengajuan ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $admin->can_approve_pengajuan ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <!-- Toggle Approve -->
                                <form action="{{ route('superadmin.manajemenadmin.update', $admin->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="can_approve_pengajuan" value="{{ $admin->can_approve_pengajuan ? 0 : 1 }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200 {{ $admin->can_approve_pengajuan ? 'bg-gray-400 hover:bg-gray-500 text-white' : 'bg-yellow-400 hover:bg-yellow-500 text-black' }}">
                                        {{ $admin->can_approve_pengajuan ? 'Non-Approve' : 'Approve' }}
                                    </button>
                                </form>

                                <!-- Reset Password -->
                                <button type="button" onclick="showConfirmResetPassword({{ $admin->id }}, '{{ $admin->name }}')" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition-colors duration-200">
                                    Reset Password
                                </button>

                                <!-- Hapus -->
                                <button type="button" onclick="showConfirmDelete({{ $admin->id }}, '{{ $admin->name }}')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium transition-colors duration-200">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-300 overflow-y-auto py-8">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto" id="modalTambahContent">
        <button onclick="closeModal('modalTambah')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors duration-200 z-10">
            <i class="fas fa-times"></i>
        </button>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Tambah Admin Baru</h2>
            <p class="text-gray-500 mt-1">Pilih user untuk dijadikan admin</p>
        </div>
        <form action="{{ route('superadmin.manajemenadmin.store') }}" method="POST" id="formTambahAdmin">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih User</label>
                <div class="relative">
                    <div class="flex items-center border border-gray-300 rounded-xl focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-all duration-200 bg-white">
                        <div class="flex-shrink-0 pl-3 pr-2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" id="userSearch" placeholder="Cari nama atau email user..."
                            class="w-full py-3 pr-3 border-none focus:ring-0 outline-none bg-transparent">
                        <div class="flex-shrink-0 pr-3 pl-2">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Dropdown dengan semua data -->
                    <div id="userDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                        @foreach($users as $user)
                            <div class="user-option px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150"
                                 data-value="{{ $user->id }}"
                                 data-name="{{ $user->name }}"
                                 data-email="{{ $user->email }}">
                                <div class="font-medium text-gray-800">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Hidden input untuk form submission -->
                    <input type="hidden" name="user_id" id="selectedUserId" required>
                </div>
                <div id="selectedUser" class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200 hidden">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-medium text-blue-800" id="selectedUserName"></div>
                            <div class="text-sm text-blue-600" id="selectedUserEmail"></div>
                        </div>
                        <button type="button" onclick="clearSelection()" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalTambah')" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition-colors duration-200 flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Reset Password -->
<div id="modalConfirmResetPassword" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95" id="modalConfirmResetPasswordContent">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                <i class="fas fa-key text-yellow-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900" id="confirmResetPasswordTitle">Reset Password Admin</h3>
            <p class="text-gray-500 mt-2" id="confirmResetPasswordMessage">Password akan direset menjadi "password123"</p>
        </div>
        <form id="formResetPassword" method="POST" class="hidden">
            @csrf
            @method('PUT')
        </form>
        <div class="flex justify-center space-x-4 pt-4">
            <button type="button" onclick="closeModal('modalConfirmResetPassword')" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                Batal
            </button>
            <button type="button" onclick="submitResetPassword()" class="px-6 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-xl shadow-sm transition-colors duration-200 flex items-center">
                <i class="fas fa-redo-alt mr-2"></i> Reset Password
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="modalConfirmDelete" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95" id="modalConfirmDeleteContent">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900" id="confirmDeleteTitle">Hapus Admin</h3>
            <p class="text-gray-500 mt-2" id="confirmDeleteMessage">Data admin akan dihapus secara permanen</p>
        </div>
        <form id="formDelete" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        <div class="flex justify-center space-x-4 pt-4">
            <button type="button" onclick="closeModal('modalConfirmDelete')" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                Batal
            </button>
            <button type="button" onclick="submitDelete()" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow-sm transition-colors duration-200 flex items-center">
                <i class="fas fa-trash mr-2"></i> Hapus
            </button>
        </div>
    </div>
</div>

<!-- Success Notification -->
<div id="successNotification" class="hidden fixed top-4 right-4 z-50">
    <div class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between transform transition-all duration-300 ease-in-out translate-x-full max-w-sm">
        <div class="flex items-center">
            <div class="mr-3">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <div class="font-semibold" id="successTitle">Sukses!</div>
                <div class="text-sm" id="successMessage">Operasi berhasil dilakukan.</div>
            </div>
        </div>
        <button onclick="hideSuccessNotification()" class="ml-4 text-green-100 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Error Notification -->
<div id="errorNotification" class="hidden fixed top-4 right-4 z-50">
    <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between transform transition-all duration-300 ease-in-out translate-x-full max-w-sm">
        <div class="flex items-center">
            <div class="mr-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
            </div>
            <div>
                <div class="font-semibold" id="errorTitle">Error!</div>
                <div class="text-sm" id="errorMessage">Terjadi kesalahan saat memproses permintaan.</div>
            </div>
        </div>
        <button onclick="hideErrorNotification()" class="ml-4 text-red-100 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="text-gray-700 font-medium">Memproses...</span>
    </div>
</div>

@endsection

@push('styles')
<style>
    .user-option:hover {
        background-color: #f0f4ff;
    }
    
    #userDropdown::-webkit-scrollbar {
        width: 6px;
    }
    
    #userDropdown::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 0 0 12px 12px;
    }
    
    #userDropdown::-webkit-scrollbar-thumb {
        background: #c5c5c5;
        border-radius: 3px;
    }
    
    #userDropdown::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush

@push('scripts')
<script>
    // Autocomplete functionality dengan dropdown semua data
    document.addEventListener('DOMContentLoaded', function() {
        const userSearch = document.getElementById('userSearch');
        const userDropdown = document.getElementById('userDropdown');
        const selectedUserId = document.getElementById('selectedUserId');
        const selectedUser = document.getElementById('selectedUser');
        const selectedUserName = document.getElementById('selectedUserName');
        const selectedUserEmail = document.getElementById('selectedUserEmail');
        const userOptions = document.querySelectorAll('.user-option');
        let allUsers = [];

        // Simpan semua data user
        userOptions.forEach(option => {
            allUsers.push({
                id: option.dataset.value,
                name: option.dataset.name,
                email: option.dataset.email,
                element: option
            });
        });

        // Tampilkan dropdown saat input difokus
        userSearch.addEventListener('focus', function() {
            userDropdown.classList.remove('hidden');
            filterUsers(userSearch.value);
        });

        // Filter users saat mengetik
        userSearch.addEventListener('input', function() {
            filterUsers(userSearch.value);
        });

        // Fungsi filter users
        function filterUsers(searchTerm) {
            const search = searchTerm.toLowerCase();
            
            userOptions.forEach(option => {
                const name = option.dataset.name.toLowerCase();
                const email = option.dataset.email.toLowerCase();
                
                if (name.includes(search) || email.includes(search)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        // Pilih user dari dropdown
        userOptions.forEach(option => {
            option.addEventListener('click', function() {
                const userId = this.dataset.value;
                const userName = this.dataset.name;
                const userEmail = this.dataset.email;
                
                // Set nilai yang dipilih
                selectedUserId.value = userId;
                selectedUserName.textContent = userName;
                selectedUserEmail.textContent = userEmail;
                
                // Tampilkan user yang dipilih
                selectedUser.classList.remove('hidden');
                
                // Sembunyikan dropdown
                userDropdown.classList.add('hidden');
                
                // Kosongkan pencarian
                userSearch.value = '';
            });
        });

        // Sembunyikan dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            if (!userSearch.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });

        // Tampilkan semua data saat dropdown dibuka
        userSearch.addEventListener('click', function() {
            if (userDropdown.classList.contains('hidden')) {
                userDropdown.classList.remove('hidden');
                filterUsers(userSearch.value);
            }
        });
    });

    // Fungsi untuk menghapus pilihan
    function clearSelection() {
        document.getElementById('selectedUserId').value = '';
        document.getElementById('selectedUser').classList.add('hidden');
        document.getElementById('userSearch').value = '';
        document.getElementById('userSearch').focus();
    }

    // Modal functions dengan animasi
    function openModal(id) {
        const modal = document.getElementById(id);
        const modalContent = document.getElementById(id + 'Content') || modal.querySelector('div');

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            if (modalContent) {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const modalContent = document.getElementById(id + 'Content') || modal.querySelector('div');

        if (modalContent) {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
        }
        
        // Reset form saat modal ditutup
        if (id === 'modalTambah') {
            setTimeout(() => {
                clearSelection();
                document.getElementById('userSearch').value = '';
                document.getElementById('userDropdown').classList.add('hidden');
            }, 300);
        }
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    // Close modals ketika klik di luar
    window.addEventListener('click', function(event) {
        const modals = ['modalTambah', 'modalConfirmResetPassword', 'modalConfirmDelete'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    });

    // Konfirmasi Reset Password
    function showConfirmResetPassword(adminId, adminName) {
        document.getElementById('confirmResetPasswordTitle').textContent = `Reset Password ${adminName}`;
        document.getElementById('confirmResetPasswordMessage').textContent = `Anda yakin ingin mereset password untuk admin ${adminName}? Password akan direset menjadi "password123".`;
        
        const form = document.getElementById('formResetPassword');
        form.action = `{{ url('superadmin/manajemen-admin') }}/${adminId}/reset-password`;
        
        openModal('modalConfirmResetPassword');
    }

    function submitResetPassword() {
        showLoading();
        document.getElementById('formResetPassword').submit();
    }

    // Konfirmasi Hapus
    function showConfirmDelete(adminId, adminName) {
        document.getElementById('confirmDeleteTitle').textContent = `Hapus Admin ${adminName}`;
        document.getElementById('confirmDeleteMessage').textContent = `Anda yakin ingin menghapus admin ${adminName}? Tindakan ini tidak dapat dibatalkan.`;
        
        const form = document.getElementById('formDelete');
        form.action = `{{ url('superadmin/manajemen-admin') }}/${adminId}`;
        
        openModal('modalConfirmDelete');
    }

    function submitDelete() {
        showLoading();
        document.getElementById('formDelete').submit();
    }

    // Loading functions
    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
    }

    // Notification functions
    function showSuccessNotification(title, message) {
        document.getElementById('successTitle').textContent = title;
        document.getElementById('successMessage').textContent = message;

        const successNotification = document.getElementById('successNotification');
        successNotification.classList.remove('hidden');
        setTimeout(() => {
            successNotification.querySelector('div').classList.remove('translate-x-full');
        }, 10);

        setTimeout(() => {
            hideSuccessNotification();
        }, 5000);
    }

    function hideSuccessNotification() {
        const successNotification = document.getElementById('successNotification');
        successNotification.querySelector('div').classList.add('translate-x-full');
        setTimeout(() => {
            successNotification.classList.add('hidden');
        }, 300);
    }

    function showErrorNotification(title, message) {
        document.getElementById('errorTitle').textContent = title;
        document.getElementById('errorMessage').textContent = message;

        const errorNotification = document.getElementById('errorNotification');
        errorNotification.classList.remove('hidden');
        setTimeout(() => {
            errorNotification.querySelector('div').classList.remove('translate-x-full');
        }, 10);

        setTimeout(() => {
            hideErrorNotification();
        }, 5000);
    }

    function hideErrorNotification() {
        const errorNotification = document.getElementById('errorNotification');
        errorNotification.querySelector('div').classList.add('translate-x-full');
        setTimeout(() => {
            errorNotification.classList.add('hidden');
        }, 300);
    }

    // Form submission dengan loading
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                showLoading();
            });
        });
    });

    // Tampilkan notifikasi sukses jika ada
    @if(session('success'))
        showSuccessNotification('Sukses!', '{{ session('success') }}');
    @endif

    // Tampilkan notifikasi error jika ada
    @if($errors->any())
        showErrorNotification('Error!', '{{ $errors->first() }}');
    @endif
</script>
@endpush