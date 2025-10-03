@extends('layouts.admin')

@section('title', 'Manajemen Jam Kerja')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-5 shadow-lg flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Jam Kerja</h1>
            <p class="text-blue-100 mt-1">Kelola jadwal jam kerja pegawai</p>
        </div>
        <button onclick="openModal('modalAddJam')"
            class="w-full sm:w-auto bg-white hover:bg-gray-100 text-blue-600 px-5 py-3 rounded-xl flex items-center justify-center transition-all duration-300 transform hover:scale-105 shadow-md">
            <i class="fas fa-plus mr-2"></i>
            <span class="font-medium">Tambah Jam Kerja</span>
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Hari</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Jam Pulang</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($jamKerja as $i => $jam)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $i + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $jam->hari }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $jam->jam_masuk }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $jam->jam_pulang }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <button onclick="openEditModal({{ $jam->id }})"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteJam({{ $jam->id }})"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div id="modalAddJam" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto">
        <button onclick="closeModal('modalAddJam')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Tambah Jam Kerja</h2>
        <p class="text-gray-500 mb-4">Tambahkan jadwal jam kerja baru</p>
        <div id="addErrors" class="mb-4 text-red-500 text-sm hidden"></div>
        <form id="formAddJam" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                <input type="text" name="hari" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" placeholder="Senin" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Masuk</label>
                    <input type="time" name="jam_masuk" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Pulang</label>
                    <input type="time" name="jam_pulang" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" required>
                </div>
            </div>
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalAddJam')" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-sm flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEditJam" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative mx-4 transform transition-all duration-300 scale-95 my-auto">
        <button onclick="closeModal('modalEditJam')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Edit Jam Kerja</h2>
        <p class="text-gray-500 mb-4">Perbarui jam masuk dan pulang</p>
        <div id="editErrors" class="mb-4 text-red-500 text-sm hidden"></div>
        <form id="formEditJam" class="space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editJamId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                <input type="text" name="hari" id="editHari" class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Masuk</label>
                    <input type="time" name="jam_masuk" id="editJamMasuk" class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Pulang</label>
                    <input type="time" name="jam_pulang" id="editJamPulang" class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none" required>
                </div>
            </div>
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeModal('modalEditJam')" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i> Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('div');
    modal.classList.remove('hidden');
    setTimeout(() => content.classList.remove('scale-95'), 10);
}

function closeModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('div');
    content.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

// Tambah Jam Kerja
document.getElementById('formAddJam').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const errorsDiv = document.getElementById('addErrors');
    errorsDiv.classList.add('hidden');
    fetch(`{{ route('admin.jamkerja.store') }}`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: formData
    })
    .then(async res => {
        if(res.ok) return res.json();
        const err = await res.json();
        throw err;
    })
    .then(data => { if(data.success) location.reload(); })
    .catch(err => {
        errorsDiv.innerHTML = Object.values(err.errors || {}).flat().join('<br>');
        errorsDiv.classList.remove('hidden');
    });
});

// Edit Jam Kerja
function openEditModal(id){
    const errorsDiv = document.getElementById('editErrors');
    errorsDiv.classList.add('hidden');
    fetch(`{{ url('admin/jamkerja') }}/${id}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('editJamId').value = data.id;
        document.getElementById('editHari').value = data.hari;
        document.getElementById('editJamMasuk').value = data.jam_masuk.slice(0,5);
        document.getElementById('editJamPulang').value = data.jam_pulang.slice(0,5);
        openModal('modalEditJam');
    })
    .catch(()=> alert('Gagal mengambil data jam kerja.'));
}

document.getElementById('formEditJam').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('editJamId').value;
    const hari = document.getElementById('editHari').value.trim();
    const jamMasuk = document.getElementById('editJamMasuk').value.trim().slice(0,5);
    const jamPulang = document.getElementById('editJamPulang').value.trim().slice(0,5);
    const errorsDiv = document.getElementById('editErrors');
    errorsDiv.classList.add('hidden');

    fetch(`{{ url('admin/jamkerja') }}/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ hari, jam_masuk: jamMasuk, jam_pulang: jamPulang })
    })
    .then(async res => {
        if(res.ok) return res.json();
        const err = await res.json();
        throw err;
    })
    .then(data => { if(data.success) location.reload(); })
    .catch(err => {
        errorsDiv.innerHTML = Object.values(err.errors || {}).flat().join('<br>');
        errorsDiv.classList.remove('hidden');
    });
});

// Hapus Jam Kerja
function deleteJam(id){
    if(!confirm('Yakin ingin menghapus jam kerja ini?')) return;
    fetch(`{{ url('admin/jamkerja') }}/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    })
    .then(res => res.json())
    .then(data => { if(data.success) location.reload(); else alert('Gagal menghapus jam kerja.'); })
    .catch(()=> alert('Terjadi kesalahan.'));
}
</script>
@endpush
