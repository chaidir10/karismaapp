@extends('layouts.pegawai')
@section('title', 'Daftar Pegawai')

@section('content')

<style>
    /* Gaya untuk halaman pegawai */
    .employee-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }

    .search-box {
        margin-bottom: 20px;
    }

    .search-box .input-group-text {
        background: #f8f9fa;
        border-right: none;
    }

    .search-box .form-control {
        border-left: none;
    }

    .employee-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .employee-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s, box-shadow 0.2s;
    }

    .employee-item:hover {
        background: #f9f9f9;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .employee-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 15px;
        flex-shrink: 0;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .employee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        font-size: 1.5rem;
        color: #6c757d;
    }

    .employee-info {
        flex-grow: 1;
    }

    .employee-name {
        font-size: 1.2rem;
        font-weight: bold;
        margin
<div class="container">
    <!-- Employee List Section -->
    <div class="employee-section">
        <div class="section-header">
            <h3 class="section-title">Daftar Pegawai</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary btn-scale">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>

        <div class="search-box">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari pegawai..." id="searchInput">
            </div>
        </div>

        <div class="employee-list">
            @forelse($pegawai as $p)
            <!-- Bagian employee-item -->
            <div class="employee-item"
                data-employee-id="{{ $p->id }}"
                data-employee-nip="{{ $p->nip }}"
                data-employee-email="{{ $p->email }}"
                data-employee-phone="{{ $p->no_hp ?? '-' }}"
                data-employee-address="{{ $p->alamat ?? '-' }}"
                data-employee-status="{{ $p->status ?? 'Aktif' }}"
                data-employee-avatar="{{ $p->foto_profil ? asset('public/storage/foto_profil/' . $p->foto_profil) : '' }}">

                <div class="employee-avatar">
                    @if($p->foto_profil && Storage::disk('public')->exists('foto_profil/' . $p->foto_profil))
                    <img src="{{ asset('public/storage/foto_profil/' . $p->foto_profil) }}" alt="{{ $p->name }}"
                        onerror="handleAvatarError(this, '{{ $p->name }}')">
                    @else
                    <div class="avatar-placeholder">{{ collect(explode(' ', $p->name))->map(fn($n)=>substr($n,0,1))->join('') }}</div>
                    @endif
                </div>

                <div class="employee-info">
                    <h5 class="employee-name">{{ $p->name }}</h5>
                    <p class="employee-position">{{ $p->jabatan ?? '-' }}</p>
                    <div class="employee-department">
                        <span class="badge bg-primary-light">{{ $p->wilayahKerja->nama ?? 'Belum ditetapkan' }}</span>
                    </div>
                </div>

                <div class="employee-status">
                    @php
                    $statusClass = 'bg-success-light';
                    $statusText = 'Aktif';
                    if(isset($p->status)) {
                    if($p->status == 'Cuti') {
                    $statusClass = 'bg-warning-light';
                    $statusText = 'Cuti';
                    } elseif($p->status == 'Tidak Aktif') {
                    $statusClass = 'bg-danger-light';
                    $statusText = 'Tidak Aktif';
                    }
                    }
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        <i class="fas fa-circle small me-1" style="font-size: 6px;"></i> {{ $statusText }}
                    </span>
                </div>
            </div>

            @empty
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada data pegawai.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Detail Pegawai -->
<div class="modal fade" id="employeeDetailModal" tabindex="-1" aria-labelledby="employeeDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-body p-0 pt-2">
                <div class="text-center mb-2">
                    <div id="modalEmployeeAvatarContainer" class="employee-avatar-container">
                        <!-- Avatar akan dimuat di sini melalui JavaScript -->
                    </div>
                </div>

                <div class="employee-detail-section">
                    <h5 class="text-center" id="modalEmployeeName">Nama Pegawai</h5>
                    <div class="text-center mb-4">
                        <span class="badge bg-primary-light" id="modalEmployeePosition">-</span>
                    </div>

                    <div class="detail-grid full-width">
                        <div class="detail-item">
                            <div class="detail-label">Unit Kerja</div>
                            <div class="detail-value" id="modalEmployeeDepartment">-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value" id="modalEmployeeStatus">-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">NIP</div>
                            <div class="detail-value" id="modalEmployeeNip">-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">No. Telepon</div>
                            <div class="detail-value d-flex align-items-center justify-content-between">
                                <span id="modalEmployeePhone">-</span>
                                <a href="#" id="whatsappLink" target="_blank" style="text-decoration: none;">
                                    <i class="fab fa-whatsapp" style="color: #25D366; font-size: 18px;"></i>
                                </a>
                            </div>
                        </div>

                        <div class="detail-item full-width">
                            <div class="detail-label">Email</div>
                            <div class="detail-value" id="modalEmployeeEmail">-</div>
                        </div>

                        <div class="detail-item full-width">
                            <div class="detail-label">Alamat</div>
                            <div class="detail-value" id="modalEmployeeAddress">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-0 pt-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeItems = document.querySelectorAll('.employee-item');
        const employeeModal = new bootstrap.Modal(document.getElementById('employeeDetailModal'));
        const searchInput = document.getElementById('searchInput');
        const modalAvatarContainer = document.getElementById('modalEmployeeAvatarContainer');

        // Fungsi untuk membuat placeholder avatar berdasarkan nama
        function createAvatarPlaceholder(name) {
            const initials = name.split(' ').map(word => word[0]).join('').toUpperCase();
            return `<div class="employee-avatar-placeholder">${initials}</div>`;
        }

        // Fungsi untuk membuat avatar dengan gambar
        function createAvatarImage(avatarUrl, name) {
            return `<img src="${avatarUrl}" class="employee-avatar-large" alt="${name}" onerror="handleModalAvatarError(this, '${name}')">`;
        }

        // Fungsi untuk menangani error gambar di list
        function handleAvatarError(imgElement, name) {
            imgElement.style.display = 'none';
            imgElement.parentElement.innerHTML = `<div class="avatar-placeholder">${name.split(' ').map(word => word[0]).join('').toUpperCase()}</div>`;
        }

        // Fungsi untuk menangani error gambar di modal
        function handleModalAvatarError(imgElement, name) {
            imgElement.style.display = 'none';
            modalAvatarContainer.innerHTML = createAvatarPlaceholder(name);
        }

        // Fungsi untuk mengisi data modal
        function fillModalData(employeeItem) {
            const employeeName = employeeItem.querySelector('.employee-name').textContent;
            const employeePosition = employeeItem.querySelector('.employee-position').textContent;
            const employeeDepartment = employeeItem.querySelector('.employee-department .badge').textContent;
            const employeeAvatar = employeeItem.getAttribute('data-employee-avatar');
            const employeeNip = employeeItem.getAttribute('data-employee-nip');
            const employeeEmail = employeeItem.getAttribute('data-employee-email');
            const employeePhone = employeeItem.getAttribute('data-employee-phone');
            const employeeAddress = employeeItem.getAttribute('data-employee-address');
            const employeeStatus = employeeItem.getAttribute('data-employee-status');

            // Set avatar di modal
            if (employeeAvatar && employeeAvatar.trim() !== '') {
                modalAvatarContainer.innerHTML = createAvatarImage(employeeAvatar, employeeName);
            } else {
                modalAvatarContainer.innerHTML = createAvatarPlaceholder(employeeName);
            }

            // Set data lainnya
            document.getElementById('modalEmployeeName').textContent = employeeName;
            document.getElementById('modalEmployeePosition').textContent = employeePosition;
            document.getElementById('modalEmployeeDepartment').textContent = employeeDepartment;
            document.getElementById('modalEmployeeStatus').textContent = employeeStatus;
            document.getElementById('modalEmployeeNip').textContent = employeeNip || '-';
            document.getElementById('modalEmployeeEmail').textContent = employeeEmail || '-';
            document.getElementById('modalEmployeePhone').textContent = employeePhone || '-';
            document.getElementById('modalEmployeeAddress').textContent = employeeAddress || '-';

            // Set WhatsApp link
            const whatsappLink = document.getElementById('whatsappLink');
            if (employeePhone && employeePhone !== '-') {
                let phoneNumber = employeePhone.replace(/[^0-9]/g, '');
                if (phoneNumber.startsWith('0')) {
                    phoneNumber = '62' + phoneNumber.substring(1);
                }
                whatsappLink.href = `https://wa.me/${phoneNumber}`;
                whatsappLink.style.pointerEvents = 'auto';
                whatsappLink.style.opacity = '1';
            } else {
                whatsappLink.href = '#';
                whatsappLink.style.pointerEvents = 'none';
                whatsappLink.style.opacity = '0.5';
            }
        }

        // Tambahkan event listener untuk setiap item pegawai
        employeeItems.forEach((item) => {
            // Setup error handling untuk avatar di list
            const avatarImg = item.querySelector('.employee-avatar img');
            const employeeName = item.querySelector('.employee-name').textContent;

            if (avatarImg) {
                avatarImg.addEventListener('error', function() {
                    handleAvatarError(this, employeeName);
                });
            }

            // Click handler untuk modal - PERBAIKAN: Gunakan event delegation yang lebih baik
            item.addEventListener('click', function() {
                fillModalData(this);
                employeeModal.show();
            });
        });

        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const employees = document.querySelectorAll('.employee-item');

                employees.forEach(employee => {
                    const name = employee.querySelector('.employee-name').textContent.toLowerCase();
                    const position = employee.querySelector('.employee-position').textContent.toLowerCase();
                    const department = employee.querySelector('.employee-department .badge').textContent.toLowerCase();

                    if (name.includes(searchTerm) || position.includes(searchTerm) || department.includes(searchTerm)) {
                        employee.style.display = 'flex';
                    } else {
                        employee.style.display = 'none';
                    }
                });
            });
        }

        // PERBAIKAN PENTING: Reset modal ketika ditutup
        document.getElementById('employeeDetailModal').addEventListener('hidden.bs.modal', function () {
            // Kosongkan container avatar untuk memastikan tidak ada konflik
            modalAvatarContainer.innerHTML = '';
        });
    });

    // Export fungsi ke global scope untuk digunakan di inline event handlers
    window.handleAvatarError = function(imgElement, name) {
        imgElement.style.display = 'none';
        imgElement.parentElement.innerHTML = `<div class="avatar-placeholder">${name.split(' ').map(word => word[0]).join('').toUpperCase()}</div>`;
    };

    window.handleModalAvatarError = function(imgElement, name) {
        imgElement.style.display = 'none';
        document.getElementById('modalEmployeeAvatarContainer').innerHTML = `<div class="employee-avatar-placeholder">${name.split(' ').map(word => word[0]).join('').toUpperCase()}</div>`;
    };
</script>
@endsection