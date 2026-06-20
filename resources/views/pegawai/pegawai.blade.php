@extends('layouts.pegawai')
@section('title', 'Daftar Pegawai')

@section('content')
<style>
    .pegawai-page { padding: 20px; padding-bottom: 100px; }

    .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .page-title { font-size:17px; font-weight:700; color:var(--dark); margin:0; }

    /* Search */
    .search-bar {
        position:relative; margin-bottom:16px;
    }
    .search-bar input {
        width:100%; padding:12px 14px 12px 42px; border:1px solid var(--card-border);
        border-radius:14px; font-size:14px; background:var(--card-bg); color:var(--dark); outline:none;
    }
    .search-bar input:focus { border-color:var(--primary); }
    .search-bar i {
        position:absolute; left:14px; top:50%; transform:translateY(-50%);
        color:var(--gray); font-size:14px;
    }

    .employee-list { display:flex; flex-direction:column; gap:10px; }

    .e-card {
        background:var(--card-bg); border-radius:14px; padding:14px 16px;
        display:flex; align-items:center; gap:14px;
        box-shadow:0 1px 6px rgba(0,0,0,0.04); border:1px solid var(--card-border);
        cursor:pointer; -webkit-tap-highlight-color:transparent;
    }
    .e-card:active { opacity:0.85; }

    .e-avatar {
        width:44px; height:44px; border-radius:50%; flex-shrink:0; overflow:hidden;
        background:var(--primary-soft); display:flex; align-items:center; justify-content:center;
        border:2px solid var(--card-border);
    }
    .e-avatar img { width:100%; height:100%; object-fit:cover; display:block; }
    .e-avatar-placeholder {
        width:100%; height:100%; display:flex; align-items:center; justify-content:center;
        background:linear-gradient(135deg,var(--primary),var(--primary-light));
        color:#fff; font-weight:700; font-size:14px;
    }

    .e-body { flex:1; min-width:0; }
    .e-name { font-size:14px; font-weight:600; color:var(--dark); margin-bottom:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .e-position { font-size:12px; color:var(--gray); margin-bottom:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .e-dept { font-size:10px; font-weight:600; padding:2px 8px; border-radius:6px; background:var(--primary-soft); color:var(--primary-dark); display:inline-block; }

    .e-status { flex-shrink:0; text-align:right; }
    .s-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:4px; }
    .s-dot-tepat { background:#10b981; }
    .s-dot-telat { background:#ef4444; }
    .s-dot-belum { background:var(--gray); }
    .s-dot-aktif { background:#10b981; }
    .s-text { font-size:11px; font-weight:500; color:var(--gray); }

    .empty-box { text-align:center; padding:60px 20px; color:var(--gray); background:var(--card-bg); border-radius:16px; }
    .empty-box i { font-size:40px; margin-bottom:12px; opacity:0.3; display:block; }
    .empty-box p { font-size:14px; margin:0; }

    /* Detail Modal */
    .detail-modal .modal-content { background:var(--card-bg); border-radius:20px; border:none; box-shadow:0 10px 30px rgba(0,0,0,0.2); }
    .detail-modal .modal-body { padding:24px; }
    .modal-avatar {
        width:72px; height:72px; border-radius:50%; margin:0 auto 12px; overflow:hidden;
        border:3px solid var(--primary); background:var(--primary-soft);
        display:flex; align-items:center; justify-content:center;
    }
    .modal-avatar img { width:100%; height:100%; object-fit:cover; display:block; }
    .modal-avatar-placeholder {
        width:100%; height:100%; display:flex; align-items:center; justify-content:center;
        background:linear-gradient(135deg,var(--primary),var(--primary-light));
        color:#fff; font-weight:700; font-size:22px;
    }
    .modal-name { font-size:16px; font-weight:700; color:var(--dark); text-align:center; margin-bottom:4px; }
    .modal-position { text-align:center; margin-bottom:16px; }
    .modal-position span { display:inline-block; padding:4px 12px; border-radius:8px; font-size:11px; font-weight:600; background:var(--primary-soft); color:var(--primary-dark); }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .detail-grid .full { grid-column:1/-1; }
    .detail-label { font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px; }
    .detail-value { font-size:14px; font-weight:500; color:var(--dark); padding:6px 0; border-bottom:1px solid var(--card-border); display:flex; align-items:center; justify-content:space-between; }
    .detail-close-btn { width:100%; margin-top:16px; padding:12px; background:var(--gray-light); color:var(--dark); border:none; border-radius:12px; font-weight:600; font-size:14px; cursor:pointer; }
</style>

<div class="pegawai-page">
    <div class="search-bar">
        <i class="fas fa-magnifying-glass"></i>
        <input type="text" placeholder="Cari nama, jabatan..." id="searchInput">
    </div>

    <div class="employee-list">
        @forelse($pegawai as $p)
        <div class="e-card"
            data-employee-id="{{ $p->id }}"
            data-employee-nip="{{ $p->nip }}"
            data-employee-email="{{ $p->email }}"
            data-employee-phone="{{ $p->no_hp ?? '-' }}"
            data-employee-address="{{ $p->alamat ?? '-' }}"
            data-employee-status="{{ $p->status ?? 'Aktif' }}"
            data-employee-dept="{{ $p->wilayahKerja->nama ?? 'Belum ditetapkan' }}"
            data-employee-avatar="{{ $p->foto_profil ? asset('public/storage/foto_profil/' . $p->foto_profil) : '' }}">

            <div class="e-avatar">
                @if($p->foto_profil && Storage::disk('public')->exists('foto_profil/' . $p->foto_profil))
                <img src="{{ asset('public/storage/foto_profil/' . $p->foto_profil) }}" alt="{{ $p->name }}" onerror="handleAvatarError(this, '{{ $p->name }}')">
                @else
                <div class="e-avatar-placeholder">{{ collect(explode(' ', $p->name))->map(fn($n)=>substr($n,0,1))->take(2)->join('') }}</div>
                @endif
            </div>

            <div class="e-body">
                <div class="e-name">{{ $p->name }}</div>
                <div class="e-position">{{ $p->jabatan ?? '-' }}</div>
                <span class="e-dept">{{ $p->wilayahKerja->nama ?? 'Belum ditetapkan' }}</span>
            </div>

            <div class="e-status">
                @if(!empty($kehadiranHariIni) && isset($kehadiranHariIni[$p->id]))
                    @php $kh = $kehadiranHariIni[$p->id]; @endphp
                    <span class="s-dot s-dot-{{ $kh['status'] }}"></span>
                    <span class="s-text">{{ $kh['text'] }}</span>
                @else
                    <span class="s-dot s-dot-aktif"></span>
                    <span class="s-text">Aktif</span>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-box">
            <i class="fas fa-user-group"></i>
            <p>Belum ada data pegawai</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade detail-modal" id="employeeDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-avatar" id="modalEmployeeAvatarContainer"></div>
                <div class="modal-name" id="modalEmployeeName">-</div>
                <div class="modal-position"><span id="modalEmployeePosition">-</span></div>

                <div class="detail-grid">
                    <div>
                        <div class="detail-label">Unit Kerja</div>
                        <div class="detail-value" id="modalEmployeeDepartment">-</div>
                    </div>
                    <div>
                        <div class="detail-label">Status</div>
                        <div class="detail-value" id="modalEmployeeStatus">-</div>
                    </div>
                    <div>
                        <div class="detail-label">NIP</div>
                        <div class="detail-value" id="modalEmployeeNip">-</div>
                    </div>
                    <div>
                        <div class="detail-label">No. Telepon</div>
                        <div class="detail-value">
                            <span id="modalEmployeePhone">-</span>
                            <a href="#" id="whatsappLink" target="_blank" style="text-decoration:none;">
                                <i class="fab fa-whatsapp" style="color:#25D366; font-size:18px;"></i>
                            </a>
                        </div>
                    </div>
                    <div class="full">
                        <div class="detail-label">Email</div>
                        <div class="detail-value" id="modalEmployeeEmail">-</div>
                    </div>
                    <div class="full">
                        <div class="detail-label">Alamat</div>
                        <div class="detail-value" id="modalEmployeeAddress">-</div>
                    </div>
                </div>
                <button type="button" class="detail-close-btn" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('turbo:load', function() {
        var items = document.querySelectorAll('.e-card');
        var modal = new bootstrap.Modal(document.getElementById('employeeDetailModal'));
        var avatarBox = document.getElementById('modalEmployeeAvatarContainer');
        var searchInput = document.getElementById('searchInput');

        function initials(name) {
            return name.split(' ').map(function(w) { return w[0]; }).join('').toUpperCase();
        }

        items.forEach(function(item) {
            item.addEventListener('click', function() {
                var name = this.querySelector('.e-name').textContent;
                var position = this.querySelector('.e-position').textContent;
                var avatar = this.dataset.employeeAvatar;

                if (avatar && avatar.trim()) {
                    avatarBox.innerHTML = '<img src="' + avatar + '" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.parentElement.innerHTML=\'<div class=modal-avatar-placeholder>' + initials(name) + '</div>\'">';
                } else {
                    avatarBox.innerHTML = '<div class="modal-avatar-placeholder">' + initials(name) + '</div>';
                }

                document.getElementById('modalEmployeeName').textContent = name;
                document.getElementById('modalEmployeePosition').textContent = position;
                document.getElementById('modalEmployeeDepartment').textContent = this.dataset.employeeDept;
                document.getElementById('modalEmployeeStatus').textContent = this.dataset.employeeStatus;
                document.getElementById('modalEmployeeNip').textContent = this.dataset.employeeNip || '-';
                document.getElementById('modalEmployeeEmail').textContent = this.dataset.employeeEmail || '-';
                document.getElementById('modalEmployeePhone').textContent = this.dataset.employeePhone || '-';
                document.getElementById('modalEmployeeAddress').textContent = this.dataset.employeeAddress || '-';

                var phone = this.dataset.employeePhone;
                var waLink = document.getElementById('whatsappLink');
                if (phone && phone !== '-') {
                    var num = phone.replace(/[^0-9]/g, '');
                    if (num.startsWith('0')) num = '62' + num.substring(1);
                    waLink.href = 'https://wa.me/' + num;
                    waLink.style.pointerEvents = 'auto';
                    waLink.style.opacity = '1';
                } else {
                    waLink.href = '#';
                    waLink.style.pointerEvents = 'none';
                    waLink.style.opacity = '0.5';
                }

                modal.show();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var term = this.value.toLowerCase().trim();
                items.forEach(function(el) {
                    var name = el.querySelector('.e-name').textContent.toLowerCase();
                    var pos = el.querySelector('.e-position').textContent.toLowerCase();
                    el.style.display = (name.includes(term) || pos.includes(term)) ? 'flex' : 'none';
                });
            });
        }

        document.getElementById('employeeDetailModal').addEventListener('hidden.bs.modal', function() {
            avatarBox.innerHTML = '';
        });
    });

    window.handleAvatarError = function(img, name) {
        img.style.display = 'none';
        img.parentElement.innerHTML = '<div class="e-avatar-placeholder">' + name.split(' ').map(function(w){return w[0]}).join('').toUpperCase() + '</div>';
    };
</script>
@endsection
