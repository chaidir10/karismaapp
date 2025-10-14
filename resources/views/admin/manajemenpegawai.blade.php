@extends('layouts.admin')

@section('title', 'Manajemen Pegawai')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section with Gradient -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-5 shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-2xl font-bold text-white">Manajemen Pegawai</h1>
                <p class="text-blue-100 mt-1">Kelola data seluruh pegawai</p>
            </div>
            <button onclick="openModal('modalAdd')" class="w-full sm:w-auto bg-white hover:bg-gray-100 text-blue-600 px-5 py-3 rounded-xl flex items-center justify-center transition-all duration-300 transform hover:scale-105 shadow-md">
                <i class="fas fa-user-plus mr-2"></i>
                <span class="font-medium">Tambah Pegawai</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl shadow-md p-5 mb-6 border border-gray-100 text-sm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pegawai</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari nama atau NIP..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Unit</label>
                <select id="filterUnit" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                    <option value="">Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Jenis Pegawai</label>
                <select id="filterJenis" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                    <option value="">Semua Jenis</option>
                    <option value="asn">ASN</option>
                    <option value="non_asn">Non ASN</option>
                    <option value="outsourcing">Outsourcing</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button id="resetFilter" class="w-full h-[42px] bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm shadow-sm transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-sync-alt mr-2"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Employee Table Card -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th colspan="8" class="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                            Total Pegawai: <span id="totalPegawai">{{ $users->count() }}</span>
                        </th>
                    </tr>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Foto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pegawaiTableBody" class="bg-white divide-y divide-gray-200">
                    @foreach($users as $i => $user)
                    <tr class="pegawai-row hover:bg-gray-50 transition-colors duration-150"
                        data-name="{{ strtolower($user->name) }}"
                        data-nip="{{ $user->nip }}"
                        data-unit="{{ $user->unit_id ?? '' }}"
                        data-jenis="{{ $user->jenis_pegawai }}">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $i+1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden border-2 border-white shadow-sm">
                                    <img
                                        src="{{ $user->foto_profil ? asset('public/storage/foto_profil/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0a9396&color=fff' }}"
                                        class="h-full w-full object-cover"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0a9396&color=fff'">
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $user->name ?? '-' }} <br>
                            <small class="font-semibold text-blue-500">
                                {{ $user->nip ?? 'N/A' }}
                            </small>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->jabatan ?? '-' }} <br>
                            <small class="font-semibold text-blue-500">{{ ucwords(str_replace('_', ' ', $user->jenis_pegawai)) }}</small>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->wilayahKerja->nama ?? '-' }}</td>


                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <!-- All Action Buttons Visible -->
                                <button onclick="openDetailModal({{ $user->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" title="Detail">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
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

<!-- Add Employee Modal -->
<div id="modalAdd" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-300 overflow-y-auto py-8">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto" id="modalAddContent">
        <button onclick="closeModal('modalAdd')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors duration-200 z-10">
            <i class="fas fa-times"></i>
        </button>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Tambah Pegawai Baru</h2>
            <p class="text-gray-500 mt-1">Isi formulir untuk menambahkan pegawai baru</p>
        </div>
        <form id="formAdd" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                    <input type="number" name="nip" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <input type="text" name="jabatan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <select name="unit_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pegawai</label>
                    <select name="jenis_pegawai" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                        <option value="asn">ASN</option>
                        <option value="non_asn">Non ASN</option>
                        <option value="outsourcing">Outsourcing</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No HP</label>
                    <input type="text" name="no_hp" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bisa Shift?</label>
                    <select name="can_shift" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <textarea name="alamat" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" rows="3"></textarea>
            </div>


            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalAdd')" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition-colors duration-200 flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Detail Employee Modal - Improved Layout -->
<div id="modalDetail" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-300 overflow-y-auto py-8">
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto" id="modalDetailContent">
        <button onclick="closeModal('modalDetail')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors duration-200">
            <i class="fas fa-times"></i>
        </button>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Detail Data Pegawai</h2>
            <p class="text-gray-500 mt-1">Informasi lengkap pegawai</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Photo & Basic Info -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    <div class="flex justify-center mb-4">
                        <img id="detailFoto" class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg mx-auto">
                    </div>
                    <h3 id="detailNama" class="text-xl font-bold text-gray-800 mb-2"></h3>
                    <p id="detailJabatan" class="text-gray-600 mb-1"></p>
                    <p id="detailNIP" class="text-sm text-gray-500 mb-3"></p>
                    <div id="detailJenisPegawai" class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium"></div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 space-y-3">
                    <button onclick="openEditModalFromDetail()" class="w-full px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i> Edit Data
                    </button>
                    <button onclick="showResetPasswordConfirmation()" class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-key mr-2"></i> Reset Password
                    </button>
                    <button onclick="showDeleteConfirmation()" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus Pegawai
                    </button>
                </div>
            </div>

            <!-- Detailed Information -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Kontak & Lainnya</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                                <p id="detailEmail" class="text-gray-800 font-medium"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">No. Telepon</label>
                                <p id="detailNoHP" class="text-gray-800 font-medium"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Unit Kerja</label>
                                <p id="detailUnit" class="text-gray-800 font-medium"></p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Pegawai</label>
                                <p id="detailJenis" class="text-gray-800 font-medium"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-500">
                                    <i class="fas fa-circle text-xs mr-1"></i> Aktif
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Bergabung</label>
                                <p id="detailTanggal" class="text-gray-800 font-medium">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Alamat</label>
                        <div id="detailAlamat" class="bg-gray-50 rounded-lg p-4 text-gray-800 border border-gray-200"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 pt-6 mt-6 border-t border-gray-200">
            <button onclick="closeModal('modalDetail')" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-300 overflow-y-auto py-8">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto" id="modalEditContent">
        <button onclick="closeModal('modalEdit')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl transition-colors duration-200">
            <i class="fas fa-times"></i>
        </button>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Data Pegawai</h2>
            <p class="text-gray-500 mt-1">Perbarui informasi pegawai</p>
        </div>
        <form id="formEdit" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                    <input type="number" name="nip" id="edit_nip" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <input type="text" name="jabatan" id="edit_jabatan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <select name="unit_id" id="edit_unit" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pegawai</label>
                    <select name="jenis_pegawai" id="edit_jenis_pegawai" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                        <option value="asn">ASN</option>
                        <option value="non_asn">Non ASN</option>
                        <option value="outsourcing">Outsourcing</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No HP</label>
                    <input type="text" name="no_hp" id="edit_no_hp" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                </div>
                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bisa Shift?</label>
                        <select name="can_shift" id="edit_can_shift"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <textarea name="alamat" id="edit_alamat" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none" rows="3"></textarea>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalEdit')" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition-colors duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i> Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="modalDeleteConfirmation" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Hapus</h3>
            <p class="text-gray-500 mb-6">Apakah Anda yakin ingin menghapus data pegawai ini? Tindakan ini tidak dapat dibatalkan.</p>

            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeModal('modalDeleteConfirmation')"
                    class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200 font-medium">
                    Batal
                </button>
                <button type="button" onclick="confirmDelete()"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow-sm transition-colors duration-200 font-medium flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Confirmation Modal -->
<div id="modalResetPasswordConfirmation" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <i class="fas fa-key text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Reset Password</h3>
            <p class="text-gray-500 mb-6">Password akan direset ke NIP pegawai. Apakah Anda yakin?</p>

            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeModal('modalResetPasswordConfirmation')"
                    class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200 font-medium">
                    Batal
                </button>
                <button type="button" onclick="confirmResetPassword()"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-sm transition-colors duration-200 font-medium flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i> Reset Password
                </button>
            </div>
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

@push('scripts')
<script>
    // Store current user ID for detail modal actions
    let currentDetailUserId = null;

    // Enhanced Filter Function
    function filterPegawai() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const unitValue = document.getElementById('filterUnit').value;
        const jenisValue = document.getElementById('filterJenis').value;
        const rows = document.querySelectorAll('.pegawai-row');

        let visibleCount = 0;

        rows.forEach(row => {
            const nameMatch = row.dataset.name.includes(searchValue);
            const nipMatch = row.dataset.nip.includes(searchValue);
            const unitMatch = unitValue === '' || row.dataset.unit === unitValue;
            const jenisMatch = jenisValue === '' || row.dataset.jenis === jenisValue;

            if ((nameMatch || nipMatch) && unitMatch && jenisMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update total count
        document.getElementById('totalPegawai').textContent = visibleCount;
    }

    // Event listeners for live filtering
    document.getElementById('searchInput').addEventListener('input', filterPegawai);
    document.getElementById('filterUnit').addEventListener('change', filterPegawai);
    document.getElementById('filterJenis').addEventListener('change', filterPegawai);

    // Reset filter
    document.getElementById('resetFilter').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterUnit').value = '';
        document.getElementById('filterJenis').value = '';
        filterPegawai();
    });

    // Modal functions with animations
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
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const modals = ['modalAdd', 'modalDetail', 'modalEdit', 'modalDeleteConfirmation', 'modalResetPasswordConfirmation'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    });

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

    // Confirmation Modal Functions
    function showDeleteConfirmation() {
        if (currentDetailUserId) {
            openModal('modalDeleteConfirmation');
        }
    }

    function showResetPasswordConfirmation() {
        if (currentDetailUserId) {
            openModal('modalResetPasswordConfirmation');
        }
    }

    function confirmDelete() {
        if (currentDetailUserId) {
            closeModal('modalDeleteConfirmation');
            deleteUser(currentDetailUserId);
        }
    }

    function confirmResetPassword() {
        if (currentDetailUserId) {
            closeModal('modalResetPasswordConfirmation');
            resetPassword(currentDetailUserId);
        }
    }

    // Tambah Pegawai
    document.getElementById('formAdd').addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();

        let formData = new FormData(this);
        fetch("{{ route('admin.manajemenpegawai.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessNotification('Sukses!', data.message);
                    closeModal('modalAdd');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorNotification('Error!', data.message || 'Terjadi kesalahan!');
                }
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat mengirim data!');
            });
    });

    // Detail Pegawai
    function openDetailModal(id) {
        currentDetailUserId = id;
        showLoading();

        fetch(`/admin/manajemen-pegawai/${id}`)
            .then(res => res.json())
            .then(user => {
                hideLoading();
                let foto = user.foto_profil ?
                    `{{ asset('public/storage/foto_profil/') }}/${user.foto_profil}` :
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0a9396&color=fff`;

                // Set photo
                document.getElementById('detailFoto').src = foto;
                document.getElementById('detailFoto').onerror = function() {
                    this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0a9396&color=fff`;
                };

                // Set basic info
                document.getElementById('detailNama').textContent = user.name;
                document.getElementById('detailJabatan').textContent = user.jabatan || '-';
                document.getElementById('detailNIP').textContent = `NIP: ${user.nip}`;
                document.getElementById('detailJenisPegawai').textContent =
                    user.jenis_pegawai.replace(/_/g, ' ').toUpperCase();


                // Set contact info
                document.getElementById('detailEmail').textContent = user.email || '-';
                document.getElementById('detailNoHP').textContent = user.no_hp || '-';
                document.getElementById('detailUnit').textContent = user.wilayah_kerja?.nama || '-';
                document.getElementById('detailJenis').textContent = user.jenis_pegawai.replace(/_/g, ' ').toUpperCase();
                document.getElementById('detailAlamat').textContent = user.alamat || '-';

                openModal('modalDetail');
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat mengambil data!');
            });
    }

    // Action functions from detail modal
    function openEditModalFromDetail() {
        if (currentDetailUserId) {
            closeModal('modalDetail');
            openEditModal(currentDetailUserId);
        }
    }

    // Edit Pegawai
    function openEditModal(id) {
        showLoading();

        fetch(`/admin/manajemen-pegawai/${id}`)
            .then(res => res.json())
            .then(user => {
                hideLoading();
                document.getElementById('edit_id').value = user.id;
                document.getElementById('edit_nip').value = user.nip;
                document.getElementById('edit_name').value = user.name;
                document.getElementById('edit_jabatan').value = user.jabatan ?? '';
                document.getElementById('edit_unit').value = user.unit_id ?? '';
                document.getElementById('edit_jenis_pegawai').value = user.jenis_pegawai;
                document.getElementById('edit_email').value = user.email ?? '';
                document.getElementById('edit_no_hp').value = user.no_hp ?? '';
                document.getElementById('edit_can_shift').value = user.can_shift ? '1' : '0';
                document.getElementById('edit_alamat').value = user.alamat ?? '';

                openModal('modalEdit');
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat mengambil data!');
            });
    }

    // Submit Edit
    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();

        let id = document.getElementById('edit_id').value;
        let formData = new FormData(this);
        formData.append('_method', 'PUT');

        fetch(`/admin/manajemen-pegawai/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessNotification('Sukses!', data.message);
                    closeModal('modalEdit');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorNotification('Error!', data.message || 'Terjadi kesalahan!');
                }
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat mengirim data!');
            });
    });

    // Reset Password
    function resetPassword(id) {
        showLoading();

        fetch(`/admin/manajemen-pegawai/${id}/reset-password`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessNotification('Sukses!', data.message);
                } else {
                    showErrorNotification('Error!', data.message || 'Terjadi kesalahan!');
                }
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat reset password!');
            });
    }

    // Hapus Pegawai
    function deleteUser(id) {
        showLoading();

        fetch(`/admin/manajemen-pegawai/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessNotification('Sukses!', data.message);
                    closeModal('modalDetail');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorNotification('Error!', data.message || 'Terjadi kesalahan!');
                }
            })
            .catch(error => {
                hideLoading();
                showErrorNotification('Error!', 'Terjadi kesalahan saat menghapus data!');
            });
    }
</script>
@endpush