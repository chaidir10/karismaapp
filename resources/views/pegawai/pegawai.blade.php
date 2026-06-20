@extends('layouts.pegawai')
@section('title', 'Daftar Pegawai')

@section('content')
<style>
    .pegawai-page { padding: 20px; padding-bottom: 100px; }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .section-title {
        font-weight: 700;
        font-size: 17px;
        color: var(--dark);
        margin: 0;
    }

    /* Search bar standalone */
    .search-bar {
        position: relative;
        margin-bottom: 16px;
    }
    .search-bar .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
        font-size: 14px;
        pointer-events: none;
    }
    .search-bar input {
        width: 100%;
        padding: 12px 14px 12px 40px;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        font-size: 14px;
        background: var(--card-bg);
        color: var(--dark);
        outline: none;
    }
    .search-bar input:focus {
        border-color: var(--primary);
    }

    .employee-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* Card Items */
    .employee-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        background: var(--card-bg);
        border-radius: 14px;
        border: 1px solid var(--card-border);
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }
    .employee-item:active { opacity: 0.85; }

    /* Avatar rounded-square */
    .employee-avatar {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        overflow: hidden;
        flex-shrink: 0;
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
    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 15px;
    }

    .employee-info {
        flex: 1;
        min-width: 0;
    }

    .employee-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--dark);
        margin: 0 0 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .employee-position {
        font-size: 12px;
        color: var(--gray);
        margin: 0 0 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .employee-department .badge {
        font-size: 10px;
        font-weight: 500;
        padding: 3px 8px;
        border-radius: 6px;
        white-space: nowrap;
        background-color: rgba(90, 182, 234, 0.1);
        color: var(--primary);
    }

    /* Status dot on right */
    .employee-status {
        flex-shrink: 0;
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-aktif { background: #10b981; }
    .dot-cuti { background: #f59e0b; }
    .dot-tidak-aktif { background: #ef4444; }
    .status-text {
        font-size: 11px;
        font-weight: 500;
        color: var(--gray);
    }

    /* Modal avatar container */
    .employee-avatar-container {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        border-radius: 18px;
        overflow: hidden;
        border: 3px solid var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-soft);
    }
    .employee-avatar-large {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .employee-avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 24px;
    }

    /* Detail Grid */
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
        border-bottom: 1px solid var(--card-border);
        word-break: break-word;
    }

    /* Badge */
    .bg-primary-light {
        background-color: rgba(90, 182, 234, 0.1);
        color: var(--primary);
    }

    /* Bootstrap modal overrides */
    .modal-dialog.modal-md {
        max-width: 100%;
        margin: 1rem auto;
    }
    .modal-content-detail {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        overflow-y: auto;
        background: var(--card-bg);
    }
    .modal-header {
        border-bottom: 1px solid var(--card-border);
        padding: 15px 20px;
    }
    .modal-footer {
        border-top: 1px solid var(--card-border);
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
    .btn-secondary {
        background: var(--gray-light);
        color: var(--dark);
        border: none;
        border-radius: 10px;
        padding: 8px 15px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        -webkit-tap-highlight-color: transparent;
    }

    /* Empty state */
    .empty-box {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray);
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--card-border);
    }
    .empty-box i { font-size: 40px; margin-bottom: 12px; opacity: 0.3; display: block; }
    .empty-box p { font-size: 14px; margin: 0; }

    @media (min-width: 576px) {
        .detail-grid {
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .modal-dialog.modal-md {
            max-width: 500px;
        }
        .employee-avatar-container {
            width: 100px;
            height: 100px;
        }
    }

    @media (max-width: 400px) {
        .employee-item { padding: 12px; gap: 10px; }
        .employee-avatar { width: 40px; height: 40px; border-radius: 12px; }
        .employee-name { font-size: 13px; }
        .employee-position { font-size: 11px; }
    }
</style>

<div class="pegawai-page">
    <!-- Section Header -->
    <div class="section-header">
        <h3 class="section-title">Daftar Pegawai</h3>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="Cari pegawai..." id="searchInput">
    </div>

    <!-- Employee List -->
    <div class="employee-list">
        @forelse($pegawai as $p)
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
                    <span class="badge">{{ $p->wilayahKerja->nama ?? 'Belum ditetapkan' }}</span>
                </div>
            </div>

            <div class="employee-status">
                @php
                $statusText = 'Aktif';
                $dotClass = 'dot-aktif';
                if(isset($p->status)) {
                    if($p->status == 'Cuti') {
                        $dotClass = 'dot-cuti';
                        $statusText = 'Cuti';
                    } elseif($p->status == 'Tidak Aktif') {
                        $dotClass = 'dot-tidak-aktif';
                        $statusText = 'Tidak Aktif';
                    }
                }
                @endphp
                <span class="status-dot {{ $dotClass }}"></span>
                <span class="status-text">{{ $statusText }}</span>
            </div>
        </div>

        @empty
        <div class="empty-box">
            <i class="fas fa-users"></i>
            <p>Belum ada data pegawai.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Detail Pegawai -->
<div class="modal fade" id="employeeDetailModal" tabindex="-1" aria-labelledby="employeeDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content modal-content-detail">
            <div class="modal-body p-0 pt-2">
                <div class="text-center mb-2">
                    <div id="modalEmployeeAvatarContainer" class="employee-avatar-container">
                        <!-- Avatar akan dimuat di sini melalui JavaScript -->
                    </div>
                </div>

                <div class="employee-detail-section">
                    <h5 class="text-center" id="modalEmployeeName" style="color:var(--dark);font-weight:600;">Nama Pegawai</h5>
                    <div class="text-center mb-4">
                        <span class="badge bg-primary-light" id="modalEmployeePosition">-</span>
                    </div>

                    <div class="detail-grid full-width" style="padding:0 20px;">
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

            // Click handler untuk modal
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

        // Reset modal ketika ditutup
        document.getElementById('employeeDetailModal').addEventListener('hidden.bs.modal', function () {
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