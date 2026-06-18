@extends('layouts.pegawai')
@section('title', 'Daftar Pegawai')

@section('content')
<style>
    /* CSS Variables */
    :root {
        --primary: #5AB6EA;
        --primary-light: #87CEEB;
        --primary-dark: #2E97D4;
        --primary-soft: #E6F4F9;
        --accent: #FEAA2B;
        --light: #f8fafc;
        --gray-light: #f1f5f9;
        --gray: #94a3b8;
        --gray-dark: #64748b;
        --dark: #1e293b;
        --white: #ffffff;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --success: #10b981;
        --success-light: #d1fae5;
        --warning: #f59e0b;
        --warning-light: #fef3c7;
    }

    /* Employee Section */
    .employee-section {
        background-color: var(--white);
        margin: 20px;
        position: relative;
        z-index: 2;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(109, 40, 217, 0.1);
        border: 1px solid rgba(109, 40, 217, 0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
        margin-bottom: 100px;
    }

    .employee-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(109, 40, 217, 0.2);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .section-title {
        font-weight: 700;
        font-size: 17px;
        color: var(--dark);
        margin: 0;
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
        background-color: var(--light);
        border-radius: 16px;
        transition: all 0.2s ease;
        border: 1px solid var(--gray-light);
        cursor: pointer;
    }

    .employee-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(109, 40, 217, 0.1);
    }

    /* PERBAIKAN UTAMA: Styling untuk avatar */
    .employee-avatar {
        width: 50px;
        height: 50px;
        margin-right: 15px;
        flex-shrink: 0;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--gray-light);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-soft);
    }

    .employee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .btn-secondary {
        background: var(--gray-light);
        color: var(--dark);
        border: none;
        border-radius: 10px;
        padding: 12px 15px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        flex: 1;
        transition: all 0.2s ease;
    }
    .btn-secondary:hover {
        background: var(--gray);
        color: var(--white);
    }

    /* Container untuk avatar modal - PERBAIKAN PENTING */
    .employee-avatar-container {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-soft);
    }

    /* Avatar besar untuk modal */
    .employee-avatar-large {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .employee-avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 24px;
        border-radius: 50%;
    }

    .employee-info {
        flex-grow: 1;
        min-width: 0;
    }

    .employee-name {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 2px;
        color: var(--dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .employee-position {
        font-size: 12px;
        color: var(--gray);
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .employee-department .badge {
        font-size: 10px;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .employee-status .badge {
        font-size: 11px;
        padding: 5px 10px;
        font-weight: 500;
        border-radius: 6px;
        white-space: nowrap;
    }

    /* Badge Color Variants */
    .bg-primary-light {
        background-color: rgba(90, 182, 234, 0.1);
        color: var(--primary);
    }

    .bg-warning-light {
        background-color: rgba(254, 170, 43, 0.1);
        color: var(--accent);
    }

    .bg-danger-light {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .bg-info-light {
        background-color: rgba(6, 182, 212, 0.1);
        color: #06b6d4;
    }

    .bg-success-light {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .bg-secondary-light {
        background-color: rgba(100, 116, 139, 0.1);
        color: var(--gray-dark);
    }

    /* Search Box */
    .search-box {
        margin-bottom: 20px;
    }

    .search-box .input-group {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .search-box .input-group-text {
        border: none;
        background-color: var(--white);
        padding-left: 15px;
    }

    .search-box .form-control {
        border: none;
        font-size: 14px;
        padding: 12px 15px;
        background-color: var(--white);
    }

    .search-box .form-control:focus {
        box-shadow: none;
    }

    /* Button Styles */
    .btn-scale {
        transition: transform 0.2s ease;
    }

    .btn-scale:active {
        transform: scale(0.96);
    }

    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 10px;
    }

    .btn-outline-primary {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary);
        color: white;
    }

    /* Modal Styles - Improved for Mobile */
    .modal-dialog.modal-md {
        max-width: 100%;
        margin: 1rem auto;
    }

    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(109, 40, 217, 0.2);
        overflow-y: auto;
    }

    .modal-header {
        border-bottom: 1px solid var(--gray-light);
        padding: 15px 20px;
    }

    .modal-footer {
        border-top: 1px solid var(--gray-light);
        padding: 15px 20px;
    }

    .modal-title {
        font-weight: 600;
        color: var(--dark);
        font-size: 18px;
    }

    .btn-close {
        font-size: 12px;
    }

    /* Detail Grid Layout */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-label {
        font-size: 12px;
        color: var(--gray);
        font-weight: 500;
        margin-bottom: 4px;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--dark);
        padding: 6px 0;
        border-bottom: 1px solid var(--gray-light);
        word-break: break-word;
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .employee-section {
        animation: fadeIn 0.2s 0.1s ease-out forwards;
        opacity: 0;
    }

    .employee-item {
        animation: fadeIn 0.2s ease-out forwards;
        opacity: 0;
    }

    .employee-item:nth-child(1) {
        animation-delay: 0.15s;
    }

    .employee-item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .employee-item:nth-child(3) {
        animation-delay: 0.25s;
    }

    .employee-item:nth-child(4) {
        animation-delay: 0.3s;
    }

    .employee-item:nth-child(5) {
        animation-delay: 0.35s;
    }

    .search-box {
        animation: fadeIn 0.2s 0.05s ease-out forwards;
        opacity: 0;
    }

    /* Responsive adjustments */
    @media (min-width: 576px) {
        .detail-grid {
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .modal-dialog.modal-md {
            max-width: 500px;
        }

        .employee-avatar-container {
            width: 120px;
            height: 120px;
        }
    }

    @media (max-width: 400px) {
        .employee-item {
            padding: 12px;
        }

        .employee-avatar {
            width: 45px;
            height: 45px;
            margin-right: 12px;
        }

        .employee-avatar-container {
            width: 80px;
            height: 80px;
        }

        .employee-name {
            font-size: 13px;
        }

        .employee-position {
            font-size: 11px;
        }

        .employee-status .badge {
            font-size: 10px;
            padding: 4px 8px;
        }
    }

    /* Fix untuk gambar yang loading atau error */
    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        border-radius: 50%;
    }
</style>

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