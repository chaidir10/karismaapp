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
    .s-dot-lembur { background:var(--accent); }
    .s-dot-warning { background:#f59e0b; }
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
            data-employee-status="{{ isset($kehadiranHariIni[$p->id]) ? $kehadiranHariIni[$p->id]['text'] : 'Belum Masuk' }}"
            data-employee-status-type="{{ isset($kehadiranHariIni[$p->id]) ? $kehadiranHariIni[$p->id]['status'] : 'belum' }}"
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

            @if(in_array(strtolower($userRole), ['admin', 'superadmin']))
            <div class="e-status">
                @if(!empty($kehadiranHariIni) && isset($kehadiranHariIni[$p->id]))
                    @php $kh = $kehadiranHariIni[$p->id]; @endphp
                    <span class="s-dot s-dot-{{ $kh['status'] }}"></span>
                    <span class="s-text">{{ $kh['text'] }}</span>
                @else
                    <span class="s-dot s-dot-belum"></span>
                    <span class="s-text">Belum Masuk</span>
                @endif
            </div>
            @endif
        </div>
        @empty
        <div class="empty-box">
            <i class="fas fa-user-group"></i>
            <p>Belum ada data pegawai</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Detail Modal — Fullscreen -->
<div id="employeeDetailModal" style="display:none; position:fixed; inset:0; z-index:100; background:var(--card-bg);">
    <div style="display:flex; flex-direction:column; height:100%;">
        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--card-border); flex-shrink:0;">
            <button onclick="closeEmployeeModal()" style="background:none; border:none; color:var(--gray); font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px; font-weight:500; -webkit-tap-highlight-color:transparent;">
                <i class="fas fa-chevron-left"></i> Kembali
            </button>
            <span style="font-size:15px; font-weight:700; color:var(--dark);">Detail Pegawai</span>
            <div style="width:70px;"></div>
        </div>
        <!-- Body -->
        <div style="flex:1; overflow-y:auto; padding:16px;">
            <!-- Profile Card -->
            <div style="text-align:center; margin-bottom:16px;">
                <div id="modalEmployeeAvatarContainer" style="width:72px; height:72px; border-radius:50%; overflow:hidden; margin:0 auto 12px; border:3px solid var(--primary); background:var(--primary-soft); display:flex; align-items:center; justify-content:center;"></div>
                <div id="modalEmployeeName" style="font-size:18px; font-weight:700; color:var(--dark); margin-bottom:4px;">-</div>
                <div id="modalEmployeePosition" style="font-size:12px; color:var(--gray); margin-bottom:8px;">-</div>
                <span id="modalEmployeeDeptBadge" style="display:inline-block; padding:4px 14px; border-radius:8px; font-size:11px; font-weight:600; background:var(--primary-soft); color:var(--primary-dark);">-</span>
            </div>

            <!-- Info Cards -->
            <div style="background:var(--light); border-radius:14px; padding:14px 16px; border:1px solid var(--card-border); margin-bottom:16px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div style="background:var(--card-bg); border-radius:10px; padding:10px 12px; border:1px solid var(--card-border);">
                        <div style="font-size:9px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">NIP</div>
                        <div style="font-size:13px; font-weight:600; color:var(--dark);" id="modalEmployeeNip">-</div>
                    </div>
                    <div style="background:var(--card-bg); border-radius:10px; padding:10px 12px; border:1px solid var(--card-border);">
                        <div style="font-size:9px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Status</div>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="width:8px; height:8px; border-radius:50%; background:#10b981;"></span>
                            <span style="font-size:13px; font-weight:600; color:var(--dark);" id="modalEmployeeStatus">Aktif</span>
                        </div>
                    </div>
                </div>

                <div style="background:var(--card-bg); border-radius:10px; padding:10px 12px; border:1px solid var(--card-border); margin-top:10px;">
                    <div style="font-size:9px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">No. Telepon</div>
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:13px; font-weight:600; color:var(--dark);" id="modalEmployeePhone">-</span>
                        <a href="#" id="whatsappLink" target="_blank" style="text-decoration:none; width:32px; height:32px; border-radius:8px; background:#dcfce7; display:flex; align-items:center; justify-content:center;">
                            <i class="fab fa-whatsapp" style="color:#25D366; font-size:16px;"></i>
                        </a>
                    </div>
                </div>

                <div style="background:var(--card-bg); border-radius:10px; padding:10px 12px; border:1px solid var(--card-border); margin-top:10px;">
                    <div style="font-size:9px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Email</div>
                    <div style="font-size:13px; font-weight:600; color:var(--dark);" id="modalEmployeeEmail">-</div>
                </div>

                <div style="background:var(--card-bg); border-radius:10px; padding:10px 12px; border:1px solid var(--card-border); margin-top:10px;">
                    <div style="font-size:9px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:2px;">Alamat</div>
                    <div style="font-size:12px; color:var(--dark); line-height:1.4;" id="modalEmployeeAddress">-</div>
                </div>
            </div>

            <!-- Riwayat Presensi Hari Ini -->
            @if(!empty($riwayatHariIni))
            <div id="modalRiwayatSection" style="display:none;">
                <div style="font-size:10px; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin-bottom:8px;">Presensi Hari Ini</div>
                <div id="modalRiwayatList"></div>
                <div id="modalRiwayatEmpty" style="text-align:center; padding:20px; color:var(--gray); font-size:12px; display:none;">
                    Belum ada presensi hari ini
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    var _riwayatData = @json($riwayatHariIni ?? []);

    function initials(name) {
        return name.split(' ').map(function(w) { return w[0]; }).join('').toUpperCase();
    }
    function closeEmployeeModal() { document.getElementById('employeeDetailModal').style.display = 'none'; }

    function renderRiwayat(employeeId) {
        var section = document.getElementById('modalRiwayatSection');
        var list = document.getElementById('modalRiwayatList');
        var empty = document.getElementById('modalRiwayatEmpty');
        if (!section || !list) return;

        var records = _riwayatData[employeeId] || [];
        list.innerHTML = '';

        if (records.length === 0) {
            section.style.display = 'block';
            empty.style.display = 'block';
            return;
        }

        section.style.display = 'block';
        empty.style.display = 'none';

        records.forEach(function(r) {
            var isMasuk = r.jenis === 'masuk';
            var isLembur = r.is_lembur;
            var iconBg = isMasuk ? 'var(--primary-soft)' : 'var(--accent-light)';
            var iconColor = isMasuk ? 'var(--primary-dark)' : 'var(--accent)';
            var iconName = isLembur ? 'fa-bolt' : (isMasuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket');
            var label = (isLembur ? 'Lembur ' : '') + (isMasuk ? 'Masuk' : 'Pulang');
            var jam = r.jam ? r.jam.substring(0, 5) : '-';

            list.innerHTML += '<div style="display:flex; align-items:center; gap:12px; padding:10px 14px; background:var(--card-bg); border-radius:12px; border:1px solid var(--card-border); margin-bottom:8px;">' +
                '<div style="width:36px; height:36px; border-radius:10px; background:' + iconBg + '; color:' + iconColor + '; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0;"><i class="fas ' + iconName + '"></i></div>' +
                '<div style="flex:1;"><div style="font-size:13px; font-weight:600; color:var(--dark);">' + label + '</div><div style="font-size:11px; color:var(--gray);">' + jam + '</div></div>' +
                '</div>';
        });
    }

    function initPegawai() {
        var items = document.querySelectorAll('.e-card');
        var avatarBox = document.getElementById('modalEmployeeAvatarContainer');
        var searchInput = document.getElementById('searchInput');

        items.forEach(function(item) {
            item.onclick = function() {
                var name = this.querySelector('.e-name').textContent;
                var position = this.querySelector('.e-position').textContent;
                var avatar = this.dataset.employeeAvatar;

                if (avatar && avatar.trim()) {
                    avatarBox.innerHTML = '<img src="' + avatar + '" style="width:100%;height:100%;object-fit:cover;display:block;">';
                } else {
                    avatarBox.innerHTML = '<div class="e-avatar-placeholder" style="width:100%;height:100%;font-size:22px;">' + initials(name) + '</div>';
                }

                document.getElementById('modalEmployeeName').textContent = name;
                document.getElementById('modalEmployeePosition').textContent = position;
                document.getElementById('modalEmployeeDeptBadge').textContent = this.dataset.employeeDept;
                document.getElementById('modalEmployeeStatus').textContent = this.dataset.employeeStatus;
                var statusDot = document.getElementById('modalEmployeeStatus').previousElementSibling;
                var sType = this.dataset.employeeStatusType || 'belum';
                var dotColors = { tepat:'#10b981', telat:'#ef4444', belum:'#94a3b8', lembur:'#f59e0b', warning:'#f59e0b' };
                if (statusDot) statusDot.style.background = dotColors[sType] || '#94a3b8';
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

                renderRiwayat(this.dataset.employeeId);
                document.getElementById('employeeDetailModal').style.display = 'block';
            };
        });

        if (searchInput) {
            searchInput.oninput = function() {
                var term = this.value.toLowerCase().trim();
                items.forEach(function(el) {
                    var name = el.querySelector('.e-name').textContent.toLowerCase();
                    var pos = el.querySelector('.e-position').textContent.toLowerCase();
                    el.style.display = (name.includes(term) || pos.includes(term)) ? 'flex' : 'none';
                });
            };
        }
    }

    document.addEventListener('turbo:load', initPegawai);
    initPegawai();

    window.handleAvatarError = function(img, name) {
        img.style.display = 'none';
        img.parentElement.innerHTML = '<div class="e-avatar-placeholder">' + name.split(' ').map(function(w){return w[0]}).join('').toUpperCase() + '</div>';
    };
</script>
@endsection
