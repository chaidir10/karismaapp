@extends('layouts.pegawai')
@section('title', 'Pengajuan')

@section('content')
<style>
    /* CSS Variables - Sama seperti di pegawai */
    :root {
        --primary: #5AB6EA;
        --primary-light: #87CEEB;
        --primary-dark: #2E97D4;
        --primary-soft: #E6F4F9;
        --accent: #FEAA2B;
        --accent-light: #FFE4BC;
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

    /* Pengajuan Section - Mirip dengan employee-section */
    .pengajuan-section {
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

    .pengajuan-section:hover {
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

    .pengajuan-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    /* Pengajuan Item - Mirip dengan employee-item */
    .pengajuan-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background-color: var(--light);
        border-radius: 16px;
        transition: all 0.2s ease;
        border: 1px solid var(--gray-light);
        cursor: pointer;
    }

    .pengajuan-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(109, 40, 217, 0.1);
    }

    /* Icon pengganti avatar */
    .pengajuan-icon {
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
        font-size: 20px;
    }

    .pengajuan-icon-container {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-soft);
        font-size: 32px;
    }

    .pengajuan-info {
        flex-grow: 1;
        min-width: 0;
    }

    .pengajuan-jenis {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 2px;
        color: var(--dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pengajuan-date {
        font-size: 12px;
        color: var(--gray);
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pengajuan-alasan .badge {
        font-size: 10px;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    .pengajuan-status .badge {
        font-size: 11px;
        padding: 5px 10px;
        font-weight: 500;
        border-radius: 6px;
        white-space: nowrap;
    }

    /* Status Colors */
    .status-pending { 
        background-color: var(--warning-light); 
        color: var(--warning);
    }
    .status-approved { 
        background-color: var(--success-light); 
        color: var(--success);
    }
    .status-rejected { 
        background-color: var(--danger-light); 
        color: var(--danger);
    }

    /* Badge Color Variants */
    .bg-primary-light {
        background-color: rgba(90, 182, 234, 0.1);
        color: var(--primary);
    }

    .bg-secondary-light {
        background-color: rgba(100, 116, 139, 0.1);
        color: var(--gray-dark);
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

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.35rem 0.75rem;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 4px 8px rgba(90, 182, 234, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(90, 182, 234, 0.4);
    }

    /* Modal Styles */
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

    .pengajuan-section {
        animation: fadeIn 0.2s 0.1s ease-out forwards;
        opacity: 0;
    }

    .pengajuan-item {
        animation: fadeIn 0.2s ease-out forwards;
        opacity: 0;
    }

    .pengajuan-item:nth-child(1) { animation-delay: 0.15s; }
    .pengajuan-item:nth-child(2) { animation-delay: 0.2s; }
    .pengajuan-item:nth-child(3) { animation-delay: 0.25s; }
    .pengajuan-item:nth-child(4) { animation-delay: 0.3s; }
    .pengajuan-item:nth-child(5) { animation-delay: 0.35s; }

    /* MODAL BUAT PENGAJUAN (Existing Modal) */
    .modal {
        display: none;
        position: fixed;
        z-index: 100;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.4);
        overflow: auto;
        padding-top: 50px;
        animation: fadeIn 0.3s ease-out;
    }
    .modal-content {
        background: var(--white);
        margin: auto;
        padding: 25px;
        border-radius: 16px;
        width: 90%;
        max-width: 450px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        border: none;
        animation: slideIn 0.3s ease-out;
    }
    .close {
        float: right;
        font-size: 24px;
        cursor: pointer;
        color: var(--gray);
        transition: color 0.2s ease;
    }
    .close:hover {
        color: var(--dark);
    }
    .modal-content h3 {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 20px;
        text-align: center;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
        color: var(--dark);
        font-size: 14px;
    }
    .form-group input, 
    .form-group select, 
    .form-group textarea {
        width: 100%;
        border: 1px solid var(--gray-light);
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: var(--white);
    }
    .form-group input:focus, 
    .form-group select:focus, 
    .form-group textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(90, 182, 234, 0.15);
        outline: none;
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 25px;
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

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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

        .pengajuan-icon-container {
            width: 100px;
            height: 100px;
            font-size: 40px;
        }
    }

    @media (max-width: 400px) {
        .pengajuan-item {
            padding: 12px;
        }

        .pengajuan-icon {
            width: 45px;
            height: 45px;
            margin-right: 12px;
            font-size: 18px;
        }

        .pengajuan-icon-container {
            width: 70px;
            height: 70px;
            font-size: 28px;
        }

        .pengajuan-jenis {
            font-size: 13px;
        }

        .pengajuan-date {
            font-size: 11px;
        }

        .pengajuan-status .badge {
            font-size: 10px;
            padding: 4px 8px;
        }
    }
</style>

<div class="container">
    <!-- Pengajuan Section -->
    <div class="pengajuan-section">
        <div class="section-header">
            <h3 class="section-title">Pengajuan Presensi</h3>
            <button class="btn btn-sm btn-primary btn-scale" onclick="openModal()">
                <i class="fas fa-plus"></i> Buat 
            </button>
        </div>

        {{-- Riwayat Pengajuan --}}
        <div class="pengajuan-list">
            @forelse($pengajuan as $p)
            <div class="pengajuan-item"
                 data-pengajuan-id="{{ $p->id }}"
                 data-pengajuan-jenis="{{ $p->jenis }}"
                 data-pengajuan-tanggal="{{ $p->tanggal }}"
                 data-pengajuan-alasan="{{ $p->alasan }}"
                 data-pengajuan-bukti="{{ $p->bukti ? Storage::url($p->bukti) : '' }}"
                 data-pengajuan-status="{{ $p->status }}">

                <div class="pengajuan-icon">
                    @if($p->jenis == 'masuk')
                    <i class="fas fa-sign-in-alt text-primary"></i>
                    @elseif($p->jenis == 'pulang')
                    <i class="fas fa-sign-out-alt text-warning"></i>
                    @else
                    <i class="fas fa-exchange-alt text-info"></i>
                    @endif
                </div>

                <div class="pengajuan-info">
                    <h5 class="pengajuan-jenis">{{ ucfirst($p->jenis) }}</h5>
                    <p class="pengajuan-date">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</p>
                    <div class="pengajuan-alasan">
                        <span class="badge bg-secondary-light">{{ Str::limit($p->alasan, 30) }}</span>
                    </div>
                </div>

                <div class="pengajuan-status">
                    <span class="badge status-{{ $p->status }}">
                        <i class="fas fa-circle small me-1" style="font-size: 6px;"></i> 
                        {{ ucfirst($p->status) }}
                    </span>
                </div>
            </div>

            @empty
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada pengajuan.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Detail Pengajuan -->
<div class="modal fade" id="pengajuanDetailModal" tabindex="-1" aria-labelledby="pengajuanDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-body p-0 pt-2">
                <div class="text-center mb-3">
                    <div id="modalPengajuanIconContainer" class="pengajuan-icon-container">
                        <!-- Icon akan dimuat di sini melalui JavaScript -->
                    </div>
                </div>

                <div class="pengajuan-detail-section">
                    <h5 class="text-center" id="modalPengajuanJenis">Jenis Pengajuan</h5>
                    <div class="text-center mb-4">
                        <span class="badge bg-primary-light" id="modalPengajuanStatus">-</span>
                    </div>

                    <div class="detail-grid full-width">
                        <div class="detail-item">
                            <div class="detail-label">Tanggal</div>
                            <div class="detail-value" id="modalPengajuanTanggal">-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Jenis</div>
                            <div class="detail-value" id="modalPengajuanJenisDetail">-</div>
                        </div>
                        <div class="detail-item full-width">
                            <div class="detail-label">Alasan</div>
                            <div class="detail-value" id="modalPengajuanAlasan">-</div>
                        </div>
                        <div class="detail-item full-width">
                            <div class="detail-label">Bukti</div>
                            <div class="detail-value" id="modalPengajuanBukti">
                                <span class="text-muted">Tidak ada bukti</span>
                            </div>
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

{{-- Modal Buat Pengajuan --}}
<div id="pengajuanModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Buat Pengajuan Presensi</h3>

        <form action="{{ route('pegawai.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="jenis">Jenis Pengajuan</label>
                <select name="jenis" id="jenis" required onchange="toggleJamFields()">
                    <option value="">-- Pilih --</option>
                    <option value="masuk">Masuk</option>
                    <option value="pulang">Pulang</option>
                    <option value="keduanya">Keduanya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required>
            </div>

            {{-- Jam Masuk --}}
            <div class="form-group" id="group_jam_masuk" style="display:none;">
                <label for="jam_masuk">Jam Masuk</label>
                <input type="time" name="jam_masuk" id="jam_masuk">
            </div>

            {{-- Jam Pulang --}}
            <div class="form-group" id="group_jam_pulang" style="display:none;">
                <label for="jam_pulang">Jam Pulang</label>
                <input type="time" name="jam_pulang" id="jam_pulang">
            </div>

            <div class="form-group">
                <label for="alasan">Alasan</label>
                <textarea name="alasan" id="alasan" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="bukti">Upload Bukti (jpg/png/pdf, max 2MB)</label>
                <input type="file" name="bukti" id="bukti" accept=".jpg,.jpeg,.png,.pdf">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Kirim Pengajuan</button>
                <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pengajuanItems = document.querySelectorAll('.pengajuan-item');
        const pengajuanModal = new bootstrap.Modal(document.getElementById('pengajuanDetailModal'));
        const modalIconContainer = document.getElementById('modalPengajuanIconContainer');

        // Fungsi untuk membuat icon berdasarkan jenis pengajuan
        function createPengajuanIcon(jenis) {
            let iconClass, iconColor;
            
            switch(jenis) {
                case 'masuk':
                    iconClass = 'fas fa-sign-in-alt';
                    iconColor = 'var(--primary)';
                    break;
                case 'pulang':
                    iconClass = 'fas fa-sign-out-alt';
                    iconColor = 'var(--accent)';
                    break;
                case 'keduanya':
                    iconClass = 'fas fa-exchange-alt';
                    iconColor = 'var(--info)';
                    break;
                default:
                    iconClass = 'fas fa-clock';
                    iconColor = 'var(--gray)';
            }
            
            return `<i class="${iconClass}" style="color: ${iconColor}"></i>`;
        }

        // Fungsi untuk mengisi data modal
        function fillModalData(pengajuanItem) {
            const pengajuanJenis = pengajuanItem.getAttribute('data-pengajuan-jenis');
            const pengajuanTanggal = pengajuanItem.getAttribute('data-pengajuan-tanggal');
            const pengajuanAlasan = pengajuanItem.getAttribute('data-pengajuan-alasan');
            const pengajuanBukti = pengajuanItem.getAttribute('data-pengajuan-bukti');
            const pengajuanStatus = pengajuanItem.getAttribute('data-pengajuan-status');

            // Set icon di modal
            modalIconContainer.innerHTML = createPengajuanIcon(pengajuanJenis);

            // Set data lainnya
            document.getElementById('modalPengajuanJenis').textContent = `Pengajuan ${pengajuanJenis}`;
            document.getElementById('modalPengajuanStatus').textContent = pengajuanStatus;
            document.getElementById('modalPengajuanTanggal').textContent = new Date(pengajuanTanggal).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('modalPengajuanJenisDetail').textContent = pengajuanJenis;
            document.getElementById('modalPengajuanAlasan').textContent = pengajuanAlasan;

            // Set bukti
            const buktiElement = document.getElementById('modalPengajuanBukti');
            if (pengajuanBukti && pengajuanBukti.trim() !== '') {
                buktiElement.innerHTML = `<a href="${pengajuanBukti}" target="_blank" class="text-primary">Lihat Bukti</a>`;
            } else {
                buktiElement.innerHTML = '<span class="text-muted">Tidak ada bukti</span>';
            }
        }

        // Tambahkan event listener untuk setiap item pengajuan
        pengajuanItems.forEach((item) => {
            item.addEventListener('click', function() {
                fillModalData(this);
                pengajuanModal.show();
            });
        });

        // Reset modal ketika ditutup
        document.getElementById('pengajuanDetailModal').addEventListener('hidden.bs.modal', function () {
            modalIconContainer.innerHTML = '';
        });
    });

    // Fungsi untuk modal buat pengajuan (existing)
    function openModal() {
        document.getElementById("pengajuanModal").style.display = "block";
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal').value = today;
    }

    function closeModal() {
        document.getElementById("pengajuanModal").style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("pengajuanModal");
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection