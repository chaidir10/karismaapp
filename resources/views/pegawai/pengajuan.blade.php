@extends('layouts.pegawai')
@section('title', 'Pengajuan')

@section('content')

<style>
    .pengajuan-page { padding: 20px; padding-bottom: 100px; }

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

    .btn-buat {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }
    .btn-buat:active { opacity: 0.85; }

    .pengajuan-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* Card Items */
    .pengajuan-item {
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
    .pengajuan-item:active { opacity: 0.85; }

    /* Icon rounded-square */
    .pengajuan-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .icon-masuk { background: #d1fae5; color: #059669; }
    .icon-pulang { background: #fef3c7; color: #d97706; }
    .icon-keduanya { background: #cffafe; color: #0891b2; }

    .pengajuan-info {
        flex: 1;
        min-width: 0;
    }

    .pengajuan-jenis {
        font-size: 14px;
        font-weight: 600;
        color: var(--dark);
        margin: 0 0 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pengajuan-date {
        font-size: 12px;
        color: var(--gray);
        margin: 0 0 4px;
    }

    .pengajuan-alasan {
        font-size: 11px;
        color: var(--gray);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Status dot on right */
    .pengajuan-status {
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
    .dot-pending { background: #f59e0b; }
    .dot-approved { background: #10b981; }
    .dot-rejected { background: #ef4444; }
    .status-text {
        font-size: 11px;
        font-weight: 500;
        color: var(--gray);
    }

    /* Modal icon container for detail */
    .pengajuan-icon-container {
        width: 72px;
        height: 72px;
        margin: 0 auto;
        border-radius: 18px;
        overflow: hidden;
        border: 3px solid var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-soft);
        font-size: 28px;
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

    /* MODAL BUAT PENGAJUAN (custom overlay) */
    .modal-overlay {
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
    .modal-create {
        background: var(--card-bg);
        margin: auto;
        padding: 25px;
        border-radius: 16px;
        width: 90%;
        max-width: 450px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        border: 1px solid var(--card-border);
        animation: slideIn 0.3s ease-out;
    }
    .close {
        float: right;
        font-size: 24px;
        cursor: pointer;
        color: var(--gray);
        -webkit-tap-highlight-color: transparent;
    }
    .close:hover {
        color: var(--dark);
    }
    .modal-create h3 {
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
        border: 1px solid var(--card-border);
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 14px;
        background-color: var(--card-bg);
        color: var(--dark);
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
    .btn-submit {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 15px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        flex: 1;
        -webkit-tap-highlight-color: transparent;
    }
    .btn-submit:active { opacity: 0.85; }
    .btn-cancel {
        background: var(--gray-light);
        color: var(--dark);
        border: none;
        border-radius: 10px;
        padding: 12px 15px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        flex: 1;
        -webkit-tap-highlight-color: transparent;
    }
    .btn-cancel:active { opacity: 0.85; }

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

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (min-width: 576px) {
        .detail-grid {
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .modal-dialog.modal-md {
            max-width: 500px;
        }
        .pengajuan-icon-container {
            width: 88px;
            height: 88px;
            font-size: 36px;
        }
    }

    @media (max-width: 400px) {
        .pengajuan-item { padding: 12px; gap: 10px; }
        .pengajuan-icon { width: 40px; height: 40px; font-size: 16px; border-radius: 10px; }
        .pengajuan-jenis { font-size: 13px; }
        .pengajuan-date { font-size: 11px; }
    }
</style>

<div class="pengajuan-page">
    <!-- Section Header -->
    <div class="section-header">
        <h3 class="section-title">Pengajuan Presensi</h3>
        <button class="btn-buat" onclick="openModal()">
            <i class="fas fa-plus"></i> Buat
        </button>
    </div>

    <!-- Pengajuan List -->
    <div class="pengajuan-list">
        @forelse($pengajuan as $p)
        <div class="pengajuan-item"
             data-pengajuan-id="{{ $p->id }}"
             data-pengajuan-jenis="{{ $p->jenis }}"
             data-pengajuan-tanggal="{{ $p->tanggal }}"
             data-pengajuan-alasan="{{ $p->alasan }}"
             data-pengajuan-bukti="{{ $p->bukti ? asset('public/storage/' . str_replace('public/', '', $p->bukti)) : '' }}"
             data-pengajuan-status="{{ $p->status }}">

            <div class="pengajuan-icon {{ $p->jenis == 'masuk' ? 'icon-masuk' : ($p->jenis == 'pulang' ? 'icon-pulang' : 'icon-keduanya') }}">
                @if($p->jenis == 'masuk')
                <i class="fas fa-sign-in-alt"></i>
                @elseif($p->jenis == 'pulang')
                <i class="fas fa-sign-out-alt"></i>
                @else
                <i class="fas fa-exchange-alt"></i>
                @endif
            </div>

            <div class="pengajuan-info">
                <h5 class="pengajuan-jenis">{{ ucfirst($p->jenis) }}</h5>
                <p class="pengajuan-date">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</p>
                <div class="pengajuan-alasan">{{ Str::limit($p->alasan, 35) }}</div>
            </div>

            <div class="pengajuan-status">
                <span class="status-dot dot-{{ $p->status }}"></span>
                <span class="status-text">{{ ucfirst($p->status) }}</span>
            </div>
        </div>

        @empty
        <div class="empty-box">
            <i class="fas fa-inbox"></i>
            <p>Belum ada pengajuan.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Detail Pengajuan -->
<div class="modal fade" id="pengajuanDetailModal" tabindex="-1" aria-labelledby="pengajuanDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content modal-content-detail">
            <div class="modal-body p-0 pt-2">
                <div class="text-center mb-3">
                    <div id="modalPengajuanIconContainer" class="pengajuan-icon-container">
                        <!-- Icon akan dimuat di sini melalui JavaScript -->
                    </div>
                </div>

                <div class="pengajuan-detail-section">
                    <h5 class="text-center" id="modalPengajuanJenis" style="color:var(--dark);font-weight:600;">Jenis Pengajuan</h5>
                    <div class="text-center mb-4">
                        <span class="badge bg-primary-light" id="modalPengajuanStatus">-</span>
                    </div>

                    <div class="detail-grid full-width" style="padding:0 20px;">
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
                                <span style="color:var(--gray);">Tidak ada bukti</span>
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
<div id="pengajuanModal" class="modal-overlay">
    <div class="modal-create">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Buat Pengajuan Presensi</h3>

        <form action="{{ route('pegawai.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="jenis">Jenis Pengajuan</label>
                <select name="jenis" id="jenis" required>
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

            <div class="form-group">
                <label for="alasan">Alasan</label>
                <textarea name="alasan" id="alasan" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="bukti">Upload Bukti (jpg/png/pdf, max 2MB)</label>
                <input type="file" name="bukti" id="bukti" accept=".jpg,.jpeg,.png,.pdf,.heic,.heif">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Kirim Pengajuan</button>
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
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
                buktiElement.innerHTML = `<a href="${pengajuanBukti}" target="_blank" style="color:var(--primary);">Lihat Bukti</a>`;
            } else {
                buktiElement.innerHTML = '<span style="color:var(--gray);">Tidak ada bukti</span>';
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