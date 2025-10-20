@extends('layouts.admin')

@section('title', 'Dashboard ')

@section('content')
<div class="admin-dashboard text-sm">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">Dashboard Admin</h1>
            <p class="dashboard-subtitle">Ringkasan aktivitas dan statistik sistem</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahHadir ?? 0 }}</h3>
                <p class="stat-label">Hadir Hari Ini</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPegawai ?? 0 }}</h3>
                <p class="stat-label">Total Pegawai</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPengajuan ?? 0 }}</h3>
                <p class="stat-label">Pengajuan Pending</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="content-grid">

        {{-- Presensi Diluar Radius --}}
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Presensi Diluar Radius</h2>
                <span class="card-badge">{{ count($presensiPending ?? []) }} menunggu</span>
            </div>
            <div class="card-content">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Pegawai</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presensiPending ?? [] as $index => $p)
                            <tr>
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge jenis-badge {{ $p->jenis ?? '' }}">
                                        {{ ucfirst($p->jenis ?? '') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if(Auth::user()->can_approve_pengajuan)
                                        <form action="{{ route('admin.presensi.approve', $p->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-success" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.presensi.reject', $p->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-danger" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-gray-400 text-xs">Tidak memiliki hak akses</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-inbox"></i>
                                        <p>Tidak ada presensi pending</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        {{-- Pengajuan Pending --}}
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Pengajuan Pending</h2>
                <span class="card-badge">{{ count($pengajuanPending ?? []) }} menunggu</span>
            </div>
            <div class="card-content">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Pegawai</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuanPending ?? [] as $index => $peng)
                            <tr>
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $peng->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge jenis-badge {{ $peng->jenis ?? '' }}">
                                        {{ ucfirst($peng->jenis ?? '') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if(Auth::user()->can_approve_pengajuan)
                                        <form action="{{ route('admin.pengajuan.approve', $peng->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-success" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.pengajuan.reject', $peng->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-danger" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-gray-400 text-xs">Tidak memiliki hak akses</span>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-inbox"></i>
                                        <p>Tidak ada pengajuan pending</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Presensi Hari Ini --}}
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Daftar Presensi Hari Ini</h2>
                <span class="card-badge">{{ count($presensiHariIni ?? []) }} aktivitas</span>
            </div>
            <div class="card-content">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Pegawai</th>
                                <th>Jenis</th>
                                <th>Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presensiHariIni ?? [] as $index => $p)
                            <tr>
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge jenis-badge {{ $p->jenis ?? '' }}">
                                        {{ ucfirst($p->jenis ?? '') }}
                                    </span>
                                </td>
                                <td class="time-cell">{{ $p->jam ?? '-' }}</td>
                                <td>
                                    @if(($p->jenis ?? '') === 'masuk')
                                    @if($p->terlambat)
                                    <span class="status-badge late">Terlambat</span>
                                    @else
                                    <span class="status-badge on-time">Tepat Waktu</span>
                                    @endif
                                    @elseif(($p->jenis ?? '') === 'pulang')
                                    @if($p->waktu_kurang_menit > 0)
                                    <span class="status-badge late">Waktu Kurang</span>
                                    @else
                                    <span class="status-badge on-time">Tepat Waktu</span>
                                    @endif
                                    @else
                                    <span class="status-badge neutral">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-clipboard-list"></i>
                                        <p>Belum ada presensi hari ini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<style>
    :root {
        --primary: #3b82f6;
        --primary-light: #60a5fa;
        --primary-dark: #2563eb;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --light: #f8fafc;
        --dark: #1e293b;
        --white: #ffffff;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
    }

    .admin-dashboard {
        padding: 20px;
        background: var(--light);
        min-height: 100vh;
    }

    .dashboard-header {
        margin-bottom: 30px;
    }

    .dashboard-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 5px 0;
    }

    .dashboard-subtitle {
        color: var(--gray-500);
        margin: 0;
        font-size: 14px;
    }

    /* Statistics Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--white);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 4px solid var(--primary);
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 5px 0;
        line-height: 1;
    }

    .stat-label {
        color: var(--gray-500);
        font-size: 14px;
        margin: 0;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        background: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 20px;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    .content-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid var(--gray-200);

    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-badge {
        padding: 4px 10px;
        background: var(--gray-300);
        color: var(--gray-700);
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .card-content {
        padding: 0;
    }

    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: var(--gray-100);
    }

    .data-table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-600);
        font-size: 12px;
        text-transform: uppercase;
        border-bottom: 1px solid var(--gray-200);
    }

    .data-table th.text-center {
        text-align: center;
    }

    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .data-table td.text-center {
        text-align: center;
    }

    .data-table tbody tr:hover {
        background: var(--gray-100);
    }

    /* User Name */
    .user-name {
        font-size: 12px;
        font-weight: 500;
        color: var(--dark);
    }

    /* Badges */
    .badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .jenis-badge {
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary);
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
    }

    .status-badge.on-time {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-badge.late {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .status-badge.neutral {
        background: rgba(100, 116, 139, 0.1);
        color: var(--gray-500);
    }

    /* Date Cell */
    .date-cell {
        font-size: 13px;
        color: var(--gray-600);
    }

    /* Time Cell */
    .time-cell {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: var(--dark);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .inline-form {
        display: inline;
    }

    .btn-success,
    .btn-danger {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12px;
    }

    .btn-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .btn-success:hover {
        background: var(--success);
        color: var(--white);
    }

    .btn-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .btn-danger:hover {
        background: var(--danger);
        color: var(--white);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 30px 20px;
    }

    .empty-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        color: var(--gray-400);
    }

    .empty-content i {
        font-size: 32px;
    }

    .empty-content p {
        margin: 0;
        font-size: 13px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .admin-dashboard {
            padding: 15px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .content-grid {
            gap: 15px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 12px;
            font-size: 12px;
        }

        .stat-card {
            padding: 15px;
        }

        .stat-value {
            font-size: 24px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dashboardContainer = document.querySelector('.admin-dashboard');

    // Fungsi untuk memperbarui isi dashboard
    function refreshDashboard() {
        fetch('{{ route("admin.dashboard") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('.admin-dashboard');
            if (newContent) {
                dashboardContainer.innerHTML = newContent.innerHTML;
                console.log("✅ Dashboard diperbarui otomatis");
            }
        })
        .catch(err => console.error('❌ Gagal memperbarui dashboard:', err));
    }

    // Jalankan setiap 30 detik (30000 ms)
    setInterval(refreshDashboard, 1000);
});
</script>


@endsection