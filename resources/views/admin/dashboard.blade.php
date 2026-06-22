@extends('layouts.admin')

@section('title', 'Dashboard ')

@section('content')
<style>
    :root, [data-theme="light"] {
        --primary: #3b82f6;
        --primary-light: #60a5fa;
        --primary-dark: #2563eb;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --light: #f8fafc;
        --dark: #1e293b;
        --white: #ffffff;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
    }
    [data-theme="dark"] {
        --primary: #60a5fa;
        --primary-light: #93c5fd;
        --primary-dark: #3b82f6;
        --light: #0f1626;
        --dark: #e2e8f0;
        --white: #141b2d;
        --gray-50: #0f1626;
        --gray-100: #141b2d;
        --gray-200: #1e293b;
        --gray-300: #334155;
        --gray-400: #64748b;
        --gray-500: #94a3b8;
        --gray-600: #94a3b8;
        --gray-700: #cbd5e1;
    }

    .admin-dashboard {
        padding: 0;
        background: transparent;
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
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 500px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--white);
        padding: 20px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid var(--gray-200);
        -webkit-tap-highlight-color: transparent;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s;
    }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform:translateY(-1px); }
    .stat-card:active { transform:scale(0.97); }
    [data-theme="dark"] .stat-card {
        background: var(--dm-card, rgba(20,27,45,0.8));
        border: 1px solid rgba(255,255,255,0.06);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    [data-theme="dark"] .stat-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.3); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--dark);
        margin: 0 0 2px 0;
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
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
        border: 1px solid var(--gray-200);
        transition: box-shadow 0.2s;
    }
    .content-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    [data-theme="dark"] .content-card {
        background: var(--dm-card, rgba(20,27,45,0.8));
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.06);
    }
    [data-theme="dark"] .content-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.3); }

    .card-search {
        position:relative; padding:12px 16px;
    }
    .card-search input {
        width:100%; padding:8px 12px 8px 34px; border:none;
        border-radius:8px; font-size:12px; color:var(--dark); outline:none;
        background:var(--gray-100);
    }
    .card-search input::placeholder { color:var(--gray-400); }
    .card-search input:focus { background:var(--gray-200); }
    .card-search i {
        position:absolute; left:28px; top:50%; transform:translateY(-50%);
        color:var(--gray-400); font-size:11px; pointer-events:none;
    }
    [data-theme="dark"] .card-search input {
        background:rgba(255,255,255,0.06); color:var(--dm-text);
    }
    [data-theme="dark"] .card-search input:focus { background:rgba(255,255,255,0.1); }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--gray-200);
    }

    .card-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-badge {
        padding: 4px 12px;
        background: rgba(90,182,234,0.1);
        color: #2E97D4;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        border: 1px solid rgba(90,182,234,0.15);
    }
    [data-theme="dark"] .card-badge {
        background: rgba(90,182,234,0.12);
        color: #7dd3fc;
        border-color: rgba(90,182,234,0.2);
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

    .data-table th[data-sort] {
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
    }

    .data-table th[data-sort]:hover {
        color: var(--primary);
    }

    .data-table th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        font-size: 10px;
        opacity: 0.3;
        vertical-align: middle;
    }

    .data-table th.sort-asc .sort-icon,
    .data-table th.sort-desc .sort-icon {
        opacity: 1;
        color: var(--primary);
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

    .user-name {
        font-size: 12px;
        font-weight: 500;
        color: var(--dark);
    }

    /* Pagination */
    .table-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        border-top: 1px solid var(--gray-200);
        font-size: 12px;
        color: var(--gray-500);
    }

    .pagination-info {
        white-space: nowrap;
    }

    .pagination-buttons {
        display: flex;
        gap: 4px;
    }

    .pagination-buttons button {
        width: 30px;
        height: 30px;
        border: 1px solid var(--gray-200);
        background: var(--white);
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        color: var(--gray-600);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .pagination-buttons button:hover:not(:disabled) {
        background: var(--gray-100);
        border-color: var(--gray-300);
    }

    .pagination-buttons button.active {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary);
    }

    .pagination-buttons button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Badges */
    .status-badge { padding:4px 10px; border-radius:8px; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:4px; }
    .jenis-badge { background:rgba(90,182,234,0.12); color:#2E97D4; border:1px solid rgba(90,182,234,0.2); padding:4px 10px; border-radius:8px; font-size:11px; font-weight:600; }
    .jenis-pulang { background:rgba(254,170,43,0.12); color:#d97706; border:1px solid rgba(254,170,43,0.2); }
    .status-badge.on-time { background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.15); }
    .status-badge.late { background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.15); }
    .status-badge.neutral { background:rgba(100,116,139,0.08); color:var(--gray-500); border:1px solid rgba(100,116,139,0.12); }
    .status-badge.pending { background:rgba(245,158,11,0.1); color:#d97706; border:1px solid rgba(245,158,11,0.15); }
    [data-theme="dark"] .status-badge.on-time { background:rgba(16,185,129,0.12); color:#6ee7b7; border-color:rgba(16,185,129,0.3); }
    [data-theme="dark"] .status-badge.late { background:rgba(239,68,68,0.12); color:#fca5a5; border-color:rgba(239,68,68,0.3); }
    [data-theme="dark"] .status-badge.pending { background:rgba(245,158,11,0.12); color:#fde68a; border-color:rgba(245,158,11,0.3); }
    [data-theme="dark"] .status-badge.neutral { background:rgba(100,116,139,0.12); color:#94a3b8; border-color:rgba(100,116,139,0.2); }
    [data-theme="dark"] .jenis-badge { background:rgba(90,182,234,0.12); color:#7dd3fc; border-color:rgba(90,182,234,0.3); }
    [data-theme="dark"] .jenis-pulang { background:rgba(254,170,43,0.12); color:#fde68a; border-color:rgba(254,170,43,0.3); }

    .date-cell {
        font-size: 13px;
        color: var(--gray-600);
    }

    .time-cell {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: var(--dark);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    /* btn-success, btn-danger inherited from layout */

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        height: 220px;
        vertical-align: middle;
    }

    .empty-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: var(--gray-400);
    }

    .empty-content .empty-icon {
        width: 56px; height: 56px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        background: rgba(148,163,184,0.08);
        color: var(--gray-300);
        margin-bottom: 4px;
    }
    [data-theme="dark"] .empty-content .empty-icon {
        background: rgba(148,163,184,0.06);
        color: var(--gray-500);
    }

    .empty-content p {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .modal-overlay.show { opacity: 1; }
    .modal-overlay .modal-container {
        transform: translateY(12px);
        opacity: 0;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .modal-overlay.show .modal-container {
        transform: translateY(0);
        opacity: 1;
    }

    .modal-container {
        background: var(--white);
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        border: 1px solid var(--gray-200);
        z-index: 10000;
        position: relative;
    }
    [data-theme="dark"] .modal-container {
        background: var(--dm-card);
        border-color: rgba(255,255,255,0.06);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }

    .modal-large { max-width: 900px; }
    .modal-wide { max-width: 1100px; }
    .modal-3col {
        display:grid; grid-template-columns:1fr 1fr 1.2fr; min-height:480px;
    }
    .modal-col {
        padding:16px; display:flex; flex-direction:column;
        border-right:1px solid var(--gray-200);
    }
    .modal-col:last-child { border-right:none; }
    [data-theme="dark"] .modal-col { border-color:rgba(255,255,255,0.06); }
    .modal-col-label {
        font-size:9px; font-weight:600; color:var(--gray-500); text-transform:uppercase;
        letter-spacing:0.3px; margin-bottom:8px;
    }
    .modal-col-content {
        flex:1; border-radius:10px; overflow:hidden; display:flex;
        align-items:center; justify-content:center; background:var(--gray-100);
        position:relative;
    }
    .modal-col-content.foto-wrapper > img { width:100%; height:100%; object-fit:cover; display:block; position:absolute; inset:0; }
    [data-theme="dark"] .modal-col-content { background:rgba(255,255,255,0.03); }
    .modal-info-col {
        padding:16px; display:flex; flex-direction:column;
    }
    .modal-info-col .info-grid {
        display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;
    }
    .modal-info-col .info-item {
        background:var(--gray-100); border-radius:8px; padding:8px 10px;
    }
    [data-theme="dark"] .modal-info-col .info-item { background:rgba(255,255,255,0.04); }
    .modal-info-col .info-item label {
        font-size:9px; font-weight:600; color:var(--gray-500); text-transform:uppercase;
        letter-spacing:0.3px; display:block; margin:0 0 2px;
    }
    .modal-info-col .info-item label::after { content:''; }
    .modal-info-col .info-item span {
        font-size:13px; font-weight:500; color:var(--dark); word-break:break-word;
    }
    .modal-info-col .info-item span.badge { color:inherit; font-size:11px; }
    .modal-info-col .info-item.full { grid-column:1/-1; }
    @media (max-width:768px) {
        .modal-3col { grid-template-columns:1fr; }
        .modal-col { border-right:none; border-bottom:1px solid var(--gray-200); }
        .modal-col-content { min-height:180px; }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px;
        border-bottom: 1px solid var(--gray-200);
    }

    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .modal-close {
        width: 32px; height: 32px;
        background: var(--gray-100);
        border: none;
        font-size: 14px;
        color: var(--gray-500);
        cursor: pointer;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.15s;
    }
    .modal-close:hover { background: rgba(239,68,68,0.1); color: #ef4444; }
    [data-theme="dark"] .modal-close { background: rgba(255,255,255,0.06); }

    .modal-content {
        padding: 16px 20px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }

    .detail-item {
        background: var(--gray-100);
        border-radius: 10px;
        padding: 10px 12px;
    }
    [data-theme="dark"] .detail-item { background: rgba(255,255,255,0.04); }

    .detail-item.full-width { grid-column: 1 / -1; }

    .detail-item label {
        font-size: 10px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: block;
        margin: 0 0 3px;
    }

    .detail-item label::after { content: ''; }

    .detail-item span {
        font-size: 13px;
        font-weight: 500;
        color: var(--dark);
        word-break: break-word;
    }
    .detail-item span.badge, .info-item span.badge { color:inherit !important; font-size:11px; }

    .modal-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: wrap;
        border-top: 1px solid var(--gray-200);
        padding: 16px 20px;
    }

    .btn-secondary {
        padding: 8px 16px;
        background: var(--gray-300);
        color: var(--gray-700);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-secondary:hover {
        background: var(--gray-400);
    }

    .text-muted {
        color: var(--gray-400);
        font-style: italic;
    }

    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-row:hover {
        background: var(--gray-50) !important;
    }

    .bukti-image,
    .foto-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 6px;
        border: 1px solid var(--gray-200);
        object-fit: cover;
    }

    /* Foto + Map Side by Side */
    .media-row {
        display: flex !important;
        flex-direction: row !important;
        gap: 15px;
        margin-bottom: 12px;
        width: 100%;
        align-items: stretch;
    }

    .media-col {
        flex: 1 1 0%;
        min-width: 0;
        width: 50%;
        display: flex;
        flex-direction: column;
    }

    .media-col label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        margin-bottom: 6px;
        flex-shrink: 0;
    }

    .media-col label::after {
        content: '' !important;
    }

    .foto-wrapper {
        flex: 1;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
        background: var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .foto-wrapper .foto-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border: none;
        border-radius: 0;
    }

    .foto-wrapper .text-muted {
        font-size: 13px;
        padding: 40px 0;
    }

    /* Map Styles */
    .map-container {
        position: relative;
        flex: 1;
        min-height: 250px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
        background: var(--gray-100);
    }

    #presensiMap,
    #hariIniMap,
    #lemburMap {
        width: 100%;
        height: 100%;
    }

    .map-loading,
    .map-error {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        pointer-events: none;
    }

    .map-loading {
        background: rgba(255, 255, 255, 0.9);
        color: var(--gray-500);
        z-index: 1000;
    }

    .map-error {
        background: var(--gray-100);
        color: var(--gray-500);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .admin-dashboard { padding: 0; }
        .stats-grid { grid-template-columns: 1fr; gap: 15px; }
        .content-grid { gap: 15px; }
        .data-table th,
        .data-table td { padding: 10px 12px; font-size: 12px; }
        .stat-card { padding: 15px; }
        .stat-value { font-size: 24px; }
        .stat-icon { width: 40px; height: 40px; font-size: 16px; }
        .modal-container { width: 95%; margin: 20px; }
        .modal-large { max-width: 95%; }
        .detail-grid { grid-template-columns: 1fr; gap: 12px; }
        .modal-actions { flex-direction: column; }
        .modal-actions .inline-form,
        .modal-actions button { width: 100%; }
        .map-container { height: 220px; }
        .media-row { flex-direction: column !important; }
        .media-col { width: 100%; }
        .map-container { min-height: 220px; }
    }
</style>

<div class="admin-dashboard text-sm">
    <!-- Header -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard Admin</h1>
        <p class="dashboard-subtitle">Ringkasan aktivitas dan statistik sistem</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <a href="#presensiHariIniSection" class="stat-card" onclick="event.preventDefault(); var el=document.getElementById('presensiHariIniSection'); var top=el.getBoundingClientRect().top+window.scrollY-70; window.scrollTo({top:top,behavior:'smooth'})">
            <div class="stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahHadir ?? 0 }}</h3>
                <p class="stat-label">Hadir Hari Ini</p>
            </div>
        </a>
        <a href="{{ route('admin.manajemenpegawai.index') }}" class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,0.1); color:#3b82f6;">
                <i class="fas fa-user-group"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPegawai ?? 0 }}</h3>
                <p class="stat-label">Total Pegawai</p>
            </div>
        </a>
        <a href="#pengajuanPendingSection" class="stat-card" onclick="event.preventDefault(); var el=document.getElementById('pengajuanPendingSection'); var top=el.getBoundingClientRect().top+window.scrollY-70; window.scrollTo({top:top,behavior:'smooth'})">
            <div class="stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $jumlahPengajuan ?? 0 }}</h3>
                <p class="stat-label">Pengajuan Pending</p>
            </div>
        </a>
        <a href="#lemburHariIniSection" class="stat-card" onclick="event.preventDefault(); var el=document.getElementById('lemburHariIniSection'); var top=el.getBoundingClientRect().top+window.scrollY-70; window.scrollTo({top:top,behavior:'smooth'})">
            <div class="stat-icon" style="background:rgba(139,92,246,0.1); color:#8b5cf6;">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $lemburHariIni->where('jenis','masuk')->count() }}</h3>
                <p class="stat-label">Lembur Hari Ini</p>
            </div>
        </a>
    </div>

    {{-- Grafik + Performa --}}
    <div class="chart-performa-grid" style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:20px;">
        <div class="content-card" style="margin:0;">
            <div class="card-header">
                <h2 class="card-title">Tren Kehadiran 7 Hari</h2>
            </div>
            <div class="card-content" style="padding:10px 15px 0;">
                <div style="position:relative; height:280px;">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="content-card" style="margin:0;">
            <div class="card-header">
                <h2 class="card-title">Top 10 Performa Bulan Ini</h2>
                <a href="{{ route('admin.performa.index') }}" style="font-size:12px; font-weight:600; color:var(--primary); text-decoration:none;">Lihat Semua <i class="fas fa-chevron-right" style="font-size:10px;"></i></a>
            </div>
            <div class="card-content" style="padding:0; max-height:310px; overflow-y:auto;">
                @foreach($performaList as $idx => $pf)
                <div style="display:flex; align-items:center; gap:10px; padding:8px 14px; border-bottom:1px solid var(--gray-200); {{ $idx < 3 ? 'background:rgba(16,185,129,0.03);' : '' }}">
                    <div style="width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0;
                        {{ $idx === 0 ? 'background:#fbbf24;color:#fff;' : ($idx === 1 ? 'background:#94a3b8;color:#fff;' : ($idx === 2 ? 'background:#cd7f32;color:#fff;' : 'background:var(--gray-200);color:var(--gray-600);')) }}">
                        {{ $idx + 1 }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:12px; font-weight:600; color:var(--dark); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $pf['user']->name }}</div>
                        <div style="font-size:10px; color:var(--gray-500);">{{ $pf['hadir'] }} hadir · {{ $pf['telat'] }} telat</div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div style="font-size:14px; font-weight:700; color:{{ $pf['persen'] >= 90 ? '#10b981' : ($pf['persen'] >= 75 ? '#2563eb' : ($pf['persen'] >= 50 ? '#f59e0b' : '#ef4444')) }};">{{ $pf['persen'] }}%</div>
                    </div>
                </div>
                @endforeach
                @if(count($performaList) === 0)
                <div style="padding:20px; text-align:center; color:var(--gray-400); font-size:12px;">Belum ada data</div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            .chart-performa-grid { grid-template-columns: 1fr !important; }
        }
    </style>

    {{-- Content Grid --}}
    <div class="content-grid">

        {{-- Presensi Diluar Radius --}}
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Presensi Diluar Radius</h2>
                <span class="card-badge">{{ count($presensiPending ?? []) }} menunggu</span>
            </div>
            <div class="card-content">
                <div class="card-search" onclick="event.stopPropagation()"><i class="fas fa-magnifying-glass"></i><input type="text" placeholder="Cari pegawai..." onkeyup="searchTable(this,'presensiPendingTable')" onkeydown="if(event.key==='Enter')event.preventDefault()"></div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th data-sort="text">Pegawai <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="date">Tanggal <i class="fas fa-sort sort-icon"></i></th>
                                <th data-sort="text">Jenis <i class="fas fa-sort sort-icon"></i></th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="presensiPendingTable" data-paginate="5">
                            @forelse($presensiPending ?? [] as $index => $p)
                            <tr class="clickable-row"
                                data-user-name="{{ $p->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $p->jenis ?? '' }}"
                                data-jam="{{ $p->jam ?? '-' }}"
                                data-lokasi="{{ $p->lokasi ?? '' }}"
                                data-foto-url="{{ $p->foto ? asset('public/storage/' . $p->foto) : '' }}"
                                data-approve-url="/admin/presensi/{{ $p->id }}/approve"
                                data-reject-url="/admin/presensi/{{ $p->id }}/reject">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge jenis-badge">{{ ucfirst($p->jenis ?? '') }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons" onclick="event.stopPropagation()">
                                        <button type="button" class="btn-success" onclick="ajaxAction('/admin/presensi/{{ $p->id }}/approve', this)"><i class="fas fa-check"></i> Setuju</button>
                                        <button type="button" class="btn-danger" onclick="ajaxAction('/admin/presensi/{{ $p->id }}/reject', this)"><i class="fas fa-times"></i> Tolak</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-content">
                                        <div class="empty-icon"><i class="fas fa-shield-check"></i></div>
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
        <div class="content-card" id="pengajuanPendingSection">
            <div class="card-header">
                <h2 class="card-title">Pengajuan Pending</h2>
                <span class="card-badge">{{ count($pengajuanPending ?? []) + count($cutiPending ?? []) }} menunggu</span>
            </div>
            <div style="display:flex; gap:6px; margin:14px 16px; padding:4px; background:rgba(0,0,0,0.03); border-radius:12px; border:1px solid var(--gray-200);">
                <button type="button" class="admin-pend-tab active" data-pend="presensi" onclick="switchAdminPendTab('presensi')" style="flex:1; padding:10px 14px; border:none; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; box-shadow:0 2px 8px rgba(90,182,234,0.25), inset 0 1px 1px rgba(255,255,255,0.2); -webkit-tap-highlight-color:transparent;">
                    <i class="fas fa-clock"></i> Presensi ({{ count($pengajuanPending ?? []) }})
                </button>
                <button type="button" class="admin-pend-tab" data-pend="cuti" onclick="switchAdminPendTab('cuti')" style="flex:1; padding:10px 14px; border:none; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; background:transparent; color:var(--dm-muted,#64748b); box-shadow:none; -webkit-tap-highlight-color:transparent;">
                    <i class="fas fa-calendar-minus"></i> Cuti/DL ({{ count($cutiPending ?? []) }})
                </button>
            </div>
            {{-- Tab Presensi --}}
            <div class="card-content" id="adminTabPresensi">
                <div class="card-search" onclick="event.stopPropagation()"><i class="fas fa-magnifying-glass"></i><input type="text" placeholder="Cari pegawai..." onkeyup="searchTable(this,'pengajuanPendingTable')" onkeydown="if(event.key==='Enter')event.preventDefault()"></div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th data-sort="text">Pegawai</th>
                                <th data-sort="date">Tanggal</th>
                                <th data-sort="text">Jenis</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengajuanPendingTable" data-paginate="5">
                            @forelse($pengajuanPending ?? [] as $index => $peng)
                            <tr class="clickable-row"
                                data-user-name="{{ $peng->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $peng->jenis ?? '' }}"
                                data-alasan="{{ $peng->alasan ?? 'Tidak ada alasan' }}"
                                data-bukti-url="{{ $peng->bukti ? asset('public/storage/' . $peng->bukti) : '' }}"
                                data-approve-url="/admin/pengajuan/{{ $peng->id }}/approve"
                                data-reject-url="/admin/pengajuan/{{ $peng->id }}/reject">
                                <td class="text-center text-xs">{{ $index + 1 }}</td>
                                <td class="user-name">{{ $peng->user->name ?? 'N/A' }}</td>
                                <td class="date-cell">{{ \Carbon\Carbon::parse($peng->tanggal ?? now())->translatedFormat('d M Y') }}</td>
                                <td><span class="badge jenis-badge">{{ ucfirst($peng->jenis ?? '') }}</span></td>
                                <td>
                                    <div class="action-buttons" onclick="event.stopPropagation()">
                                        <button type="button" class="btn-success" onclick="ajaxAction('/admin/pengajuan/{{ $peng->id }}/approve', this)"><i class="fas fa-check"></i> Setuju</button>
                                        <button type="button" class="btn-danger" onclick="ajaxAction('/admin/pengajuan/{{ $peng->id }}/reject', this)"><i class="fas fa-times"></i> Tolak</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="empty-state"><div class="empty-content"><div class="empty-icon"><i class="fas fa-paper-plane"></i></div><p>Tidak ada pengajuan presensi pending</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Tab Cuti/DL --}}
            <div class="card-content" id="adminTabCuti" style="display:none;">
                <div class="card-search" onclick="event.stopPropagation()"><i class="fas fa-magnifying-glass"></i><input type="text" placeholder="Cari pegawai..." onkeyup="searchTable(this,'cutiPendingTable')" onkeydown="if(event.key==='Enter')event.preventDefault()"></div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Pegawai</th>
                                <th>Jenis</th>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cutiPendingTable">
                            @forelse($cutiPending ?? [] as $idx => $cp)
                            <tr style="cursor:pointer;" onclick="openCutiModal({{ $cp->id }})" id="cutiRow{{ $cp->id }}"
                                data-cuti-id="{{ $cp->id }}"
                                data-cuti-user="{{ $cp->user->name ?? 'N/A' }}"
                                data-cuti-jenis="{{ $cp->jenis === 'dinas_luar' ? 'Dinas Luar' : \App\Models\Cuti::jenisOptions()[$cp->jenis] ?? $cp->jenis }}"
                                data-cuti-mulai="{{ $cp->tanggal_mulai->format('d M Y') }}"
                                data-cuti-selesai="{{ $cp->tanggal_selesai->format('d M Y') }}"
                                data-cuti-hari="{{ $cp->tanggal_mulai->diffInDays($cp->tanggal_selesai) + 1 }}"
                                data-cuti-keterangan="{{ $cp->keterangan ?? '-' }}"
                                data-cuti-bukti="{{ $cp->bukti_surat ? asset('public/storage/' . $cp->bukti_surat) : '' }}"
                                data-cuti-approve="/admin/cuti/{{ $cp->id }}/approve"
                                data-cuti-reject="/admin/cuti/{{ $cp->id }}/reject">
                                <td class="text-center text-xs">{{ $idx + 1 }}</td>
                                <td class="user-name">{{ $cp->user->name ?? 'N/A' }}</td>
                                <td><span class="badge" style="background:rgba(139,92,246,0.1);color:#7c3aed;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600;">{{ $cp->jenis === 'dinas_luar' ? 'Dinas Luar' : \App\Models\Cuti::jenisOptions()[$cp->jenis] ?? $cp->jenis }}</span></td>
                                <td class="date-cell">{{ $cp->tanggal_mulai->format('d M') }}@if($cp->tanggal_mulai != $cp->tanggal_selesai) - {{ $cp->tanggal_selesai->format('d M') }}@endif</td>
                                <td class="text-center">{{ $cp->tanggal_mulai->diffInDays($cp->tanggal_selesai) + 1 }}</td>
                                <td>
                                    <div class="action-buttons" onclick="event.stopPropagation()">
                                        <button type="button" class="btn-success" onclick="ajaxAction('/admin/cuti/{{ $cp->id }}/approve', this)"><i class="fas fa-check"></i> Setuju</button>
                                        <button type="button" class="btn-danger" onclick="ajaxAction('/admin/cuti/{{ $cp->id }}/reject', this)"><i class="fas fa-times"></i> Tolak</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="empty-state"><div class="empty-content"><div class="empty-icon"><i class="fas fa-calendar-xmark"></i></div><p>Tidak ada pengajuan cuti/DL pending</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Detail Cuti/DL --}}
        <div id="modalCutiDetail" class="modal-overlay" style="display:none;">
            <div class="modal-container" style="max-width:1100px;">
                <div class="modal-header">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;color:#7c3aed;font-size:14px;">
                            <i class="fas fa-calendar-minus"></i>
                        </div>
                        <h3 class="modal-title" style="margin:0;" id="cutiModalTitle">Detail Cuti/DL</h3>
                    </div>
                    <button class="modal-close" onclick="closeModal('modalCutiDetail')"><i class="fas fa-times"></i></button>
                </div>
                <div style="display:grid; grid-template-columns:3fr 2fr; min-height:520px;">
                    <div class="modal-col">
                        <div class="modal-col-label">Bukti Surat</div>
                        <div class="modal-col-content" id="cutiModalBukti" style="flex-direction:column;">
                            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                                <div style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-file-pdf" style="font-size:18px;color:var(--gray-400);"></i>
                                </div>
                                <span style="font-size:12px;color:var(--gray-400);">Tidak ada bukti</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-info-col">
                        <div class="info-grid">
                            <div class="info-item"><label>Pegawai</label><span id="cutiModalUser">-</span></div>
                            <div class="info-item"><label>Jenis</label><span id="cutiModalJenis">-</span></div>
                            <div class="info-item"><label>Periode</label><span id="cutiModalPeriode">-</span></div>
                            <div class="info-item"><label>Durasi</label><span id="cutiModalHari">-</span></div>
                            <div class="info-item full"><label>Keterangan</label><span id="cutiModalKeterangan">-</span></div>
                        </div>
                        <div style="margin-top:auto; display:flex; gap:8px;">
                            <button type="button" class="btn-success" style="flex:1;padding:10px;" id="cutiModalApprove"><i class="fas fa-check"></i> Setuju</button>
                            <button type="button" class="btn-danger" style="flex:1;padding:10px;" id="cutiModalReject"><i class="fas fa-times"></i> Tolak</button>
                            <button type="button" class="btn-secondary" style="padding:10px 16px;" onclick="closeModal('modalCutiDetail')">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Presensi Hari Ini --}}
        @php
            $presensiMasuk = collect($presensiHariIni ?? [])->where('jenis','masuk');
            $presensiPulangHI = collect($presensiHariIni ?? [])->where('jenis','pulang');
        @endphp
        <div class="content-card" id="presensiHariIniSection">
            <div class="card-header">
                <h2 class="card-title">Daftar Presensi Hari Ini</h2>
                <span class="card-badge">{{ count($presensiHariIni ?? []) }} aktivitas</span>
            </div>
            <div style="display:flex; gap:6px; margin:14px 16px; padding:4px; background:rgba(0,0,0,0.03); border-radius:12px; border:1px solid var(--gray-200);">
                <button type="button" class="hi-tab active" data-hitab="masuk" onclick="switchHiTab('masuk')" style="flex:1; padding:10px 14px; border:none; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; box-shadow:0 2px 8px rgba(90,182,234,0.25), inset 0 1px 1px rgba(255,255,255,0.2); -webkit-tap-highlight-color:transparent;">
                    <i class="fas fa-arrow-right-to-bracket"></i> Masuk ({{ $presensiMasuk->count() }})
                </button>
                <button type="button" class="hi-tab" data-hitab="pulang" onclick="switchHiTab('pulang')" style="flex:1; padding:10px 14px; border:none; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; background:transparent; color:var(--dm-muted,#64748b); box-shadow:none; -webkit-tap-highlight-color:transparent;">
                    <i class="fas fa-arrow-right-from-bracket"></i> Pulang ({{ $presensiPulangHI->count() }})
                </button>
            </div>
            {{-- Tab Masuk --}}
            <div class="card-content" id="hiTabMasuk">
                <div class="card-search" onclick="event.stopPropagation()"><i class="fas fa-magnifying-glass"></i><input type="text" placeholder="Cari pegawai..." onkeyup="searchTable(this,'presensiMasukTable')" onkeydown="if(event.key==='Enter')event.preventDefault()"></div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th data-sort="text">Nama Pegawai</th>
                                <th data-sort="text">Jam</th>
                                <th data-sort="text">Status</th>
                            </tr>
                        </thead>
                        <tbody id="presensiMasukTable" data-paginate="5">
                            @forelse($presensiMasuk as $index => $p)
                            <tr class="clickable-row"
                                data-user-name="{{ $p->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $p->jenis ?? '' }}"
                                data-jam="{{ $p->jam ?? '-' }}"
                                data-lokasi="{{ $p->lokasi ?? '' }}"
                                data-foto-url="{{ $p->foto ? asset('public/storage/' . $p->foto) : '' }}"
                                data-status="{{ $p->status ?? '' }}"
                                data-status-label="{{ $p->terlambat ? 'Terlambat' : 'Tepat Waktu' }}"
                                data-approve-url="/admin/presensi/{{ $p->id }}/approve"
                                data-reject-url="/admin/presensi/{{ $p->id }}/reject">
                                <td class="text-center text-xs">{{ $loop->iteration }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td class="time-cell">{{ $p->jam ?? '-' }}</td>
                                <td>
                                    @if($p->terlambat)
                                        <span class="badge badge-danger"><i class="fas fa-clock" style="font-size:9px;"></i> Terlambat</span>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-check" style="font-size:9px;"></i> Tepat Waktu</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="empty-state"><div class="empty-content"><div class="empty-icon"><i class="fas fa-fingerprint"></i></div><p>Belum ada presensi masuk</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Tab Pulang --}}
            <div class="card-content" id="hiTabPulang" style="display:none;">
                <div class="card-search" onclick="event.stopPropagation()"><i class="fas fa-magnifying-glass"></i><input type="text" placeholder="Cari pegawai..." onkeyup="searchTable(this,'presensiPulangTable')" onkeydown="if(event.key==='Enter')event.preventDefault()"></div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th data-sort="text">Nama Pegawai</th>
                                <th data-sort="text">Jam</th>
                                <th data-sort="text">Status</th>
                            </tr>
                        </thead>
                        <tbody id="presensiPulangTable" data-paginate="5">
                            @forelse($presensiPulangHI as $index => $p)
                            <tr class="clickable-row"
                                data-user-name="{{ $p->user->name ?? 'N/A' }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal ?? now())->translatedFormat('d M Y') }}"
                                data-jenis="{{ $p->jenis ?? '' }}"
                                data-jam="{{ $p->jam ?? '-' }}"
                                data-lokasi="{{ $p->lokasi ?? '' }}"
                                data-foto-url="{{ $p->foto ? asset('public/storage/' . $p->foto) : '' }}"
                                data-status="{{ $p->status ?? '' }}"
                                data-status-label="{{ ($p->waktu_kurang_menit ?? 0) > 0 ? 'Pulang Cepat' : 'Tepat Waktu' }}"
                                data-approve-url="/admin/presensi/{{ $p->id }}/approve"
                                data-reject-url="/admin/presensi/{{ $p->id }}/reject">
                                <td class="text-center text-xs">{{ $loop->iteration }}</td>
                                <td class="user-name">{{ $p->user->name ?? 'N/A' }}</td>
                                <td class="time-cell">{{ $p->jam ?? '-' }}</td>
                                <td>
                                    @if(($p->waktu_kurang_menit ?? 0) > 0)
                                        <span class="badge badge-warning"><i class="fas fa-clock" style="font-size:9px;"></i> Pulang Cepat</span>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-check" style="font-size:9px;"></i> Tepat Waktu</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="empty-state"><div class="empty-content"><div class="empty-icon"><i class="fas fa-arrow-right-from-bracket"></i></div><p>Belum ada presensi pulang</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Lembur Hari Ini --}}
        @php
            $lemburMasukHI = $lemburHariIni->where('jenis','masuk');
            $lemburPulangHI = $lemburHariIni->where('jenis','pulang');
        @endphp
        <div class="content-card" id="lemburHariIniSection">
            <div class="card-header">
                <h2 class="card-title">Lembur Hari Ini</h2>
                <span class="card-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">{{ $lemburMasukHI->count() }} pegawai</span>
            </div>
            <div style="display:flex; gap:6px; margin:14px 16px; padding:4px; background:rgba(0,0,0,0.03); border-radius:12px; border:1px solid var(--gray-200);">
                <button type="button" class="lb-tab active" data-lbtab="masuk" onclick="switchLbTab('masuk')" style="flex:1; padding:10px 14px; border:none; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; box-shadow:0 2px 8px rgba(90,182,234,0.25), inset 0 1px 1px rgba(255,255,255,0.2); -webkit-tap-highlight-color:transparent;">
                    <i class="fas fa-arrow-right-to-bracket"></i> Masuk ({{ $lemburMasukHI->count() }})
                </button>
                <button type="button" class="lb-tab" data-lbtab="pulang" onclick="switchLbTab('pulang')" style="flex:1; padding:10px 14px; border:none; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; background:transparent; color:var(--dm-muted,#64748b); box-shadow:none; -webkit-tap-highlight-color:transparent;">
                    <i class="fas fa-arrow-right-from-bracket"></i> Pulang ({{ $lemburPulangHI->count() }})
                </button>
            </div>
            {{-- Tab Lembur Masuk --}}
            <div class="card-content" id="lbTabMasuk">
                <div class="card-search" onclick="event.stopPropagation()"><i class="fas fa-magnifying-glass"></i><input type="text" placeholder="Cari pegawai..." onkeyup="searchTable(this,'lemburMasukTable')" onkeydown="if(event.key==='Enter')event.preventDefault()"></div>
                <div class="table-container">
                    <table class="data-table">
                        <thead><tr><th class="text-center">No</th><th>Pegawai</th><th>Jam</th></tr></thead>
                        <tbody id="lemburMasukTable" data-paginate="5">
                            @forelse($lemburMasukHI as $l)
                            <tr class="clickable-row" data-user-name="{{ $l->user->name ?? 'N/A' }}" data-tanggal="{{ \Carbon\Carbon::parse($l->tanggal ?? now())->translatedFormat('d M Y') }}" data-jenis="Lembur Masuk" data-jam="{{ $l->jam ?? '-' }}" data-lokasi="{{ $l->lokasi ?? '' }}" data-foto-url="{{ $l->foto ? asset('public/storage/' . $l->foto) : '' }}" data-status="{{ $l->status ?? '' }}">
                                <td class="text-center text-xs">{{ $loop->iteration }}</td>
                                <td class="user-name">{{ $l->user->name ?? 'N/A' }}</td>
                                <td class="time-cell">{{ $l->jam ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="empty-state"><div class="empty-content"><div class="empty-icon"><i class="fas fa-bolt-lightning"></i></div><p>Belum ada lembur masuk</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Tab Lembur Pulang --}}
            <div class="card-content" id="lbTabPulang" style="display:none;">
                <div class="card-search" onclick="event.stopPropagation()"><i class="fas fa-magnifying-glass"></i><input type="text" placeholder="Cari pegawai..." onkeyup="searchTable(this,'lemburPulangTable')" onkeydown="if(event.key==='Enter')event.preventDefault()"></div>
                <div class="table-container">
                    <table class="data-table">
                        <thead><tr><th class="text-center">No</th><th>Pegawai</th><th>Jam</th></tr></thead>
                        <tbody id="lemburPulangTable" data-paginate="5">
                            @forelse($lemburPulangHI as $l)
                            <tr class="clickable-row" data-user-name="{{ $l->user->name ?? 'N/A' }}" data-tanggal="{{ \Carbon\Carbon::parse($l->tanggal ?? now())->translatedFormat('d M Y') }}" data-jenis="Lembur Pulang" data-jam="{{ $l->jam ?? '-' }}" data-lokasi="{{ $l->lokasi ?? '' }}" data-foto-url="{{ $l->foto ? asset('public/storage/' . $l->foto) : '' }}" data-status="{{ $l->status ?? '' }}">
                                <td class="text-center text-xs">{{ $loop->iteration }}</td>
                                <td class="user-name">{{ $l->user->name ?? 'N/A' }}</td>
                                <td class="time-cell">{{ $l->jam ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="empty-state"><div class="empty-content"><div class="empty-icon"><i class="fas fa-bolt-lightning"></i></div><p>Belum ada lembur pulang</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- end .content-grid --}}

</div>{{-- end .admin-dashboard --}}


{{-- ========== MODAL PRESENSI PENDING ========== --}}
<div id="modalPresensiPending" class="modal-overlay">
    <div class="modal-container modal-wide">
        <div class="modal-header">
            <h3 class="modal-title">Detail Presensi Pending</h3>
            <button class="modal-close" onclick="closeModal('modalPresensiPending')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-3col">
            <div class="modal-col">
                <div class="modal-col-label">Peta Lokasi</div>
                <div class="modal-col-content map-container">
                    <div id="presensiMap" style="width:100%;height:100%;"></div>
                    <div id="mapLoading" class="map-loading"><i class="fas fa-spinner fa-spin"></i><span>Memuat peta...</span></div>
                    <div id="mapError" class="map-error" style="display:none;"><i class="fas fa-exclamation-triangle"></i><span>Koordinat tidak tersedia</span></div>
                </div>
            </div>
            <div class="modal-col">
                <div class="modal-col-label">Foto</div>
                <div class="modal-col-content foto-wrapper" id="detailFotoPresensi">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;"><div style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;"><i class="fas fa-camera" style="font-size:18px;color:var(--gray-400);"></i></div><span style="font-size:12px;color:var(--gray-400);">Tidak ada foto</span></div>
                </div>
            </div>
            <div class="modal-info-col">
                <div class="info-grid">
                    <div class="info-item"><label>Pegawai</label><span id="detailPegawaiPresensi">-</span></div>
                    <div class="info-item"><label>Tanggal</label><span id="detailTanggalPresensi">-</span></div>
                    <div class="info-item"><label>Jenis</label><span id="detailJenisPresensi">-</span></div>
                    <div class="info-item"><label>Jam</label><span id="detailJamPresensi">-</span></div>
                    <div class="info-item full"><label>Lokasi</label><span id="detailLokasiPresensi">-</span></div>
                    <div class="info-item"><label>Status</label><span class="badge badge-warning">Pending</span></div>
                </div>
                <div style="margin-top:auto; display:flex; gap:8px;">
                    <button type="button" class="btn-success" id="modalBtnApprovePresensi" style="flex:1;padding:10px;"><i class="fas fa-check"></i> Setuju</button>
                    <button type="button" class="btn-danger" id="modalBtnRejectPresensi" style="flex:1;padding:10px;"><i class="fas fa-times"></i> Tolak</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('modalPresensiPending')" style="padding:10px 16px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ========== MODAL PENGAJUAN PENDING ========== --}}
<div id="modalPengajuanPending" class="modal-overlay">
    <div class="modal-container modal-wide">
        <div class="modal-header">
            <h3 class="modal-title">Detail Pengajuan Pending</h3>
            <button class="modal-close" onclick="closeModal('modalPengajuanPending')"><i class="fas fa-times"></i></button>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; height:420px;">
            <div style="position:relative; overflow:hidden; background:var(--gray-100); display:flex; align-items:center; justify-content:center;" id="detailBuktiPengajuan">
                <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                    <div style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;"><i class="fas fa-image" style="font-size:18px;color:var(--gray-400);"></i></div>
                    <span style="font-size:12px;color:var(--gray-400);">Tidak ada bukti</span>
                </div>
            </div>
            <div class="modal-info-col">
                <div class="info-grid">
                    <div class="info-item"><label>Pegawai</label><span id="detailPegawaiPengajuan">-</span></div>
                    <div class="info-item"><label>Tanggal</label><span id="detailTanggalPengajuan">-</span></div>
                    <div class="info-item"><label>Jenis</label><span id="detailJenisPengajuan">-</span></div>
                    <div class="info-item"><label>Status</label><span class="badge badge-warning">Pending</span></div>
                    <div class="info-item full"><label>Alasan</label><span id="detailAlasanPengajuan">-</span></div>
                </div>
                <div style="margin-top:auto; display:flex; gap:8px;">
                    <button type="button" class="btn-success" id="modalBtnApprove" style="flex:1;padding:10px;"><i class="fas fa-check"></i> Setuju</button>
                    <button type="button" class="btn-danger" id="modalBtnReject" style="flex:1;padding:10px;"><i class="fas fa-times"></i> Tolak</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('modalPengajuanPending')" style="padding:10px 16px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ========== MODAL DETAIL PRESENSI HARI INI ========== --}}
<div id="modalDetailHariIni" class="modal-overlay">
    <div class="modal-container modal-wide">
        <div class="modal-header">
            <h3 class="modal-title">Detail Presensi</h3>
            <button class="modal-close" onclick="closeModal('modalDetailHariIni')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-3col">
            <div class="modal-col">
                <div class="modal-col-label">Peta Lokasi</div>
                <div class="modal-col-content map-container">
                    <div id="hariIniMap" style="width:100%;height:100%;"></div>
                    <div id="hariIniMapLoading" class="map-loading"><i class="fas fa-spinner fa-spin"></i><span>Memuat peta...</span></div>
                    <div id="hariIniMapError" class="map-error" style="display:none;"><i class="fas fa-exclamation-triangle"></i><span>Koordinat tidak tersedia</span></div>
                </div>
            </div>
            <div class="modal-col">
                <div class="modal-col-label">Foto</div>
                <div class="modal-col-content foto-wrapper" id="detailFotoHariIni">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;"><div style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;"><i class="fas fa-camera" style="font-size:18px;color:var(--gray-400);"></i></div><span style="font-size:12px;color:var(--gray-400);">Tidak ada foto</span></div>
                </div>
            </div>
            <div class="modal-info-col">
                <div class="info-grid">
                    <div class="info-item"><label>Pegawai</label><span id="detailNamaHariIni">-</span></div>
                    <div class="info-item"><label>Tanggal</label><span id="detailTanggalHariIni">-</span></div>
                    <div class="info-item"><label>Jenis</label><span id="detailJenisHariIni">-</span></div>
                    <div class="info-item"><label>Jam</label><span id="detailJamHariIni">-</span></div>
                    <div class="info-item"><label>Kehadiran</label><span id="detailStatusHariIni">-</span></div>
                    <div class="info-item"><label>Verifikasi</label><span id="detailVerifikasiHariIni">-</span></div>
                    <div class="info-item full"><label>Lokasi</label><span id="detailLokasiHariIni">-</span></div>
                </div>
                <div style="margin-top:auto; display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDetailHariIni')" style="padding:10px 20px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Lembur --}}
<div id="modalDetailLembur" class="modal-overlay">
    <div class="modal-container modal-wide">
        <div class="modal-header">
            <h3 class="modal-title">Detail Lembur</h3>
            <button class="modal-close" onclick="closeModal('modalDetailLembur')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-3col">
            <div class="modal-col">
                <div class="modal-col-label">Peta Lokasi</div>
                <div class="modal-col-content map-container">
                    <div id="lemburMap" style="width:100%;height:100%;"></div>
                    <div id="lemburMapLoading" class="map-loading"><i class="fas fa-spinner fa-spin"></i><span>Memuat peta...</span></div>
                    <div id="lemburMapError" class="map-error" style="display:none;"><i class="fas fa-exclamation-triangle"></i><span>Koordinat tidak tersedia</span></div>
                </div>
            </div>
            <div class="modal-col">
                <div class="modal-col-label">Foto</div>
                <div class="modal-col-content foto-wrapper" id="detailFotoLembur">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;"><div style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;"><i class="fas fa-camera" style="font-size:18px;color:var(--gray-400);"></i></div><span style="font-size:12px;color:var(--gray-400);">Tidak ada foto</span></div>
                </div>
            </div>
            <div class="modal-info-col">
                <div class="info-grid">
                    <div class="info-item"><label>Pegawai</label><span id="detailNamaLembur">-</span></div>
                    <div class="info-item"><label>Tanggal</label><span id="detailTanggalLembur">-</span></div>
                    <div class="info-item"><label>Jenis</label><span id="detailJenisLembur">-</span></div>
                    <div class="info-item"><label>Jam</label><span id="detailJamLembur">-</span></div>
                    <div class="info-item"><label>Verifikasi</label><span id="detailVerifikasiLembur">-</span></div>
                    <div class="info-item full"><label>Lokasi</label><span id="detailLokasiLembur">-</span></div>
                </div>
                <div style="margin-top:auto; display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDetailLembur')" style="padding:10px 20px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // ─── AJAX Approve/Reject (no page reload) ──────────────────────────────
    // Tab switch pengajuan pending
    function switchAdminPendTab(tab) {
        document.getElementById('adminTabPresensi').style.display = tab === 'presensi' ? '' : 'none';
        document.getElementById('adminTabCuti').style.display = tab === 'cuti' ? '' : 'none';
        document.querySelectorAll('.admin-pend-tab').forEach(function(btn) {
            if (btn.dataset.pend === tab) {
                btn.style.background = 'linear-gradient(135deg,#5AB6EA,#2E97D4)';
                btn.style.color = '#fff';
                btn.style.boxShadow = '0 3px 10px rgba(90,182,234,0.3), inset 0 1px 1px rgba(255,255,255,0.2)';
            } else {
                btn.style.background = 'rgba(255,255,255,0.06)';
                btn.style.color = 'var(--dm-muted,#64748b)';
                btn.style.boxShadow = 'inset 0 1px 2px rgba(0,0,0,0.04)';
            }
        });
    }

    // Cuti detail modal
    function openCutiModal(id) {
        var row = document.getElementById('cutiRow' + id);
        if (!row) return;
        document.getElementById('cutiModalUser').textContent = row.dataset.cutiUser;
        document.getElementById('cutiModalJenis').textContent = row.dataset.cutiJenis;
        document.getElementById('cutiModalHari').textContent = row.dataset.cutiHari + ' hari';
        document.getElementById('cutiModalKeterangan').textContent = row.dataset.cutiKeterangan || '-';
        var mulai = row.dataset.cutiMulai, selesai = row.dataset.cutiSelesai;
        document.getElementById('cutiModalPeriode').textContent = mulai === selesai ? mulai : mulai + ' - ' + selesai;

        var buktiEl = document.getElementById('cutiModalBukti');
        var buktiUrl = row.dataset.cutiBukti;
        var emptyIcon = '<div style="display:flex;flex-direction:column;align-items:center;gap:6px;"><div style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;"><i class="fas fa-file-pdf" style="font-size:18px;color:var(--gray-400);"></i></div><span style="font-size:12px;color:var(--gray-400);">Tidak ada bukti</span></div>';
        if (buktiUrl && buktiUrl.match(/\.pdf$/i)) {
            buktiEl.style.cssText = 'flex:1;display:flex;border-radius:10px;overflow:hidden;background:#fff;';
            buktiEl.innerHTML = '<iframe src="' + buktiUrl + '#toolbar=0&navpanes=0&view=FitH" style="width:100%;height:100%;border:none;" frameborder="0"></iframe>';
        } else if (buktiUrl) {
            buktiEl.style.cssText = 'flex:1;display:flex;align-items:center;justify-content:center;background:var(--gray-100);border-radius:10px;overflow:hidden;';
            buktiEl.innerHTML = '<img src="' + buktiUrl + '" style="width:100%;height:100%;object-fit:cover;display:block;">';
        } else {
            buktiEl.style.cssText = 'flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;background:var(--gray-100);border-radius:10px;overflow:hidden;';
            buktiEl.innerHTML = emptyIcon;
        }

        var approveUrl = row.dataset.cutiApprove;
        var rejectUrl = row.dataset.cutiReject;
        document.getElementById('cutiModalApprove').onclick = function() {
            ajaxAction(approveUrl, row);
            closeModal('modalCutiDetail');
        };
        document.getElementById('cutiModalReject').onclick = function() {
            ajaxAction(rejectUrl, row);
            closeModal('modalCutiDetail');
        };

        openModal('modalCutiDetail');
    }

    // Tab switch — Presensi Hari Ini (Masuk/Pulang)
    function switchHiTab(tab) {
        document.getElementById('hiTabMasuk').style.display = tab === 'masuk' ? '' : 'none';
        document.getElementById('hiTabPulang').style.display = tab === 'pulang' ? '' : 'none';
        document.querySelectorAll('.hi-tab').forEach(function(btn) {
            if (btn.dataset.hitab === tab) {
                btn.style.background = 'linear-gradient(135deg,#5AB6EA,#2E97D4)'; btn.style.color = '#fff';
                btn.style.boxShadow = '0 2px 8px rgba(90,182,234,0.25), inset 0 1px 1px rgba(255,255,255,0.2)';
            } else {
                btn.style.background = 'transparent'; btn.style.color = 'var(--dm-muted,#64748b)'; btn.style.boxShadow = 'none';
            }
        });
    }

    // Tab switch — Lembur Hari Ini (Masuk/Pulang)
    function switchLbTab(tab) {
        document.getElementById('lbTabMasuk').style.display = tab === 'masuk' ? '' : 'none';
        document.getElementById('lbTabPulang').style.display = tab === 'pulang' ? '' : 'none';
        document.querySelectorAll('.lb-tab').forEach(function(btn) {
            if (btn.dataset.lbtab === tab) {
                btn.style.background = 'linear-gradient(135deg,#5AB6EA,#2E97D4)'; btn.style.color = '#fff';
                btn.style.boxShadow = '0 2px 8px rgba(90,182,234,0.25), inset 0 1px 1px rgba(255,255,255,0.2)';
            } else {
                btn.style.background = 'transparent'; btn.style.color = 'var(--dm-muted,#64748b)'; btn.style.boxShadow = 'none';
            }
        });
    }

    // Table instances (must be before searchTable)
    var tableInstances = {};

    // Prevent scroll when clicking search (deferred to DOMContentLoaded)
    function initSearchScroll() {
        document.querySelectorAll('.card-search input').forEach(function(inp) {
            inp.addEventListener('focus', function() {
                var y = window.scrollY;
                requestAnimationFrame(function() { window.scrollTo(0, y); });
            });
        });
    }

    function searchTable(input, tbodyId) {
        var query = input.value.toLowerCase().trim();
        var tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        var inst = tableInstances[tbodyId];
        if (!inst) return;
        var perPage = parseInt(tbody.getAttribute('data-paginate')) || 5;

        // Filter rows by query
        var filtered = inst.allRows.filter(function(row) {
            if (row.querySelector('.empty-state')) return false;
            return row.textContent.toLowerCase().indexOf(query) !== -1;
        });

        inst.rows = filtered;
        inst.currentPage = 1;
        inst._searchQuery = query;

        // Render filtered with pagination
        renderTable(inst, tbody, perPage);
    }

    function renderTable(inst, tbody, perPage) {
        // Hide all original rows first
        inst.allRows.forEach(function(r) { r.style.display = 'none'; });

        var totalRows = inst.rows.length;
        var totalPages = Math.max(1, Math.ceil(totalRows / perPage));
        if (inst.currentPage > totalPages) inst.currentPage = totalPages;

        var start = (inst.currentPage - 1) * perPage;
        var end = Math.min(start + perPage, totalRows);

        inst.rows.forEach(function(row, i) {
            row.style.display = (i >= start && i < end) ? '' : 'none';
            var noCell = row.querySelector('td:first-child');
            if (noCell) noCell.textContent = i + 1;
        });

        // Update pagination
        var pg = inst._paginationDiv;
        if (!pg) return;
        if (totalRows <= perPage) { pg.style.display = 'none'; return; }
        pg.style.display = '';
        var s = start + 1, e = end;
        var html = '<span class="pagination-info">' + s + '-' + e + ' dari ' + totalRows + '</span><div class="pagination-buttons">';
        html += '<button data-page="prev" ' + (inst.currentPage <= 1 ? 'disabled' : '') + '><i class="fas fa-chevron-left"></i></button>';
        var sp = Math.max(1, inst.currentPage - 2);
        var ep = Math.min(totalPages, sp + 4);
        if (ep - sp < 4) sp = Math.max(1, ep - 4);
        for (var p = sp; p <= ep; p++) {
            html += '<button data-page="' + p + '" class="' + (p === inst.currentPage ? 'active' : '') + '">' + p + '</button>';
        }
        html += '<button data-page="next" ' + (inst.currentPage >= totalPages ? 'disabled' : '') + '><i class="fas fa-chevron-right"></i></button></div>';
        pg.innerHTML = html;
    }

    function refreshTablePagination(tbodyId) {
        var tbody = document.getElementById(tbodyId);
        if (!tbody || !tableInstances[tbodyId]) return;
        var inst = tableInstances[tbodyId];
        var perPage = parseInt(tbody.getAttribute('data-paginate')) || 5;

        // Rebuild allRows from current DOM
        inst.allRows = Array.from(tbody.querySelectorAll('tr')).filter(function(r) { return !r.querySelector('.empty-state'); });
        inst.rows = inst.allRows.slice();
        inst._searchQuery = '';

        if (inst.rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="empty-state"><div class="empty-content"><div class="empty-icon"><i class="fas fa-circle-check"></i></div><p>Semua pengajuan telah diproses</p></div></td></tr>';
            if (inst._paginationDiv) inst._paginationDiv.style.display = 'none';
            return;
        }

        renderTable(inst, tbody, perPage);
    }

    function ajaxAction(url, btnEl) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var row = btnEl ? btnEl.closest('tr') : null;
                var tbodyId = row ? (row.closest('tbody') ? row.closest('tbody').id : null) : null;
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(function() {
                        row.remove();
                        if (tbodyId) refreshTablePagination(tbodyId);
                    }, 300);
                }
            }
        })
        .catch(function(e) { console.error(e); location.reload(); });
    }

    // ─── State ───────────────────────────────────────────────────────────────
    let presensiMap    = null;
    let currentMarker  = null;
    let pendingCoords  = null;

    let hariIniMap     = null;
    let hariIniMarker  = null;
    let hariIniCoords  = null;

    // ─── DOM Ready ───────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {

        // Klik baris presensi pending
        document.querySelectorAll('#presensiPendingTable .clickable-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                // Abaikan klik pada tombol aksi
                if (e.target.closest('.action-buttons')) return;

                openPresensiModal({
                    user_name         : this.dataset.userName,
                    tanggal           : this.dataset.tanggal,
                    jenis             : this.dataset.jenis,
                    jam               : this.dataset.jam,
                    lokasi            : this.dataset.lokasi,
                    foto_url          : this.dataset.fotoUrl,
                    approve_url       : this.dataset.approveUrl,
                    reject_url        : this.dataset.rejectUrl
                });
            });
        });

        // Klik baris pengajuan pending
        document.querySelectorAll('#pengajuanPendingTable .clickable-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.closest('.action-buttons')) return;

                openPengajuanModal({
                    user_name         : this.dataset.userName,
                    tanggal           : this.dataset.tanggal,
                    jenis             : this.dataset.jenis,
                    alasan            : this.dataset.alasan,
                    bukti_url         : this.dataset.buktiUrl,
                    approve_url       : this.dataset.approveUrl,
                    reject_url        : this.dataset.rejectUrl
                });
            });
        });

        // Klik baris presensi hari ini
        document.querySelectorAll('#presensiMasukTable .clickable-row, #presensiPulangTable .clickable-row').forEach(function (row) {
            row.addEventListener('click', function () {
                openHariIniModal({
                    user_name    : this.dataset.userName,
                    tanggal      : this.dataset.tanggal,
                    jenis        : this.dataset.jenis,
                    jam          : this.dataset.jam,
                    lokasi       : this.dataset.lokasi,
                    foto_url     : this.dataset.fotoUrl,
                    status       : this.dataset.status,
                    status_label : this.dataset.statusLabel
                });
            });
        });

        // Klik baris lembur hari ini
        document.querySelectorAll('#lemburMasukTable .clickable-row, #lemburPulangTable .clickable-row').forEach(function (row) {
            row.addEventListener('click', function () {
                openLemburModal({
                    user_name : this.dataset.userName,
                    tanggal   : this.dataset.tanggal,
                    jenis     : this.dataset.jenis,
                    jam       : this.dataset.jam,
                    lokasi    : this.dataset.lokasi,
                    foto_url  : this.dataset.fotoUrl,
                    status    : this.dataset.status
                });
            });
        });

        // Tutup modal klik di luar container
        document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        // Tutup modal dengan ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay').forEach(function (m) {
                    closeModal(m.id);
                });
            }
        });
    });

    // ─── Modal Presensi ───────────────────────────────────────────────────────
    function openPresensiModal(data) {
        document.getElementById('detailPegawaiPresensi').textContent = data.user_name || 'N/A';
        document.getElementById('detailTanggalPresensi').textContent  = data.tanggal  || '-';
        document.getElementById('detailJenisPresensi').textContent    = capitalize(data.jenis);
        document.getElementById('detailJamPresensi').textContent      = data.jam      || '-';
        resolveAddress(data.lokasi, document.getElementById('detailLokasiPresensi'));

        setZoomableFoto(document.getElementById('detailFotoPresensi'), data.foto_url, emptyFotoHtml);

        // Wire modal buttons
        document.getElementById('modalBtnApprovePresensi').onclick = function() { ajaxAction(data.approve_url, null); closeModal('modalPresensiPending'); };
        document.getElementById('modalBtnRejectPresensi').onclick = function() { ajaxAction(data.reject_url, null); closeModal('modalPresensiPending'); };

        // Parse koordinat dari string lokasi format "lat,lng"
        var lat = NaN;
        var lng = NaN;

        if (data.lokasi) {
            var parts = data.lokasi.trim().split(',');
            if (parts.length === 2) {
                lat = parseFloat(parts[0].trim());
                lng = parseFloat(parts[1].trim());
            }
        }

        pendingCoords = {
            lat   : lat,
            lng   : lng,
            lokasi: data.lokasi || 'Lokasi tidak tersedia'
        };

        openModal('modalPresensiPending');
    }

    // ─── Modal Pengajuan ──────────────────────────────────────────────────────
    function openPengajuanModal(data) {
        document.getElementById('detailPegawaiPengajuan').textContent = data.user_name || 'N/A';
        document.getElementById('detailTanggalPengajuan').textContent  = data.tanggal  || '-';
        document.getElementById('detailJenisPengajuan').textContent    = capitalize(data.jenis);
        document.getElementById('detailAlasanPengajuan').textContent   = data.alasan   || 'Tidak ada alasan';

        var buktiEl = document.getElementById('detailBuktiPengajuan');
        var emptyBukti = '<div style="display:flex;flex-direction:column;align-items:center;gap:6px"><div style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);display:flex;align-items:center;justify-content:center"><i class="fas fa-image" style="font-size:18px;color:var(--gray-400)"></i></div><span style="font-size:12px;color:var(--gray-400)">Tidak ada bukti</span></div>';
        var baseStyle = 'position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;';
        if (data.bukti_url && data.bukti_url.match(/\.pdf$/i)) {
            buktiEl.style.cssText = baseStyle + 'background:#fff;';
            buktiEl.innerHTML = '<iframe src="' + data.bukti_url + '#toolbar=0&navpanes=0&view=FitH" style="position:absolute;inset:0;width:100%;height:100%;border:none;" frameborder="0"></iframe>';
        } else if (data.bukti_url) {
            buktiEl.style.cssText = baseStyle + 'background:#000;cursor:zoom-in;';
            var img = document.createElement('img');
            img.src = data.bukti_url;
            img.style.cssText = 'width:100%;height:100%;object-fit:contain;display:block;position:absolute;inset:0;transition:transform 0.15s ease;transform-origin:center center;';
            img.onerror = function() { buktiEl.innerHTML = emptyBukti; buktiEl.style.cssText = baseStyle + 'background:var(--gray-100);'; };
            buktiEl.innerHTML = '';
            buktiEl.appendChild(img);
            var zoom = 1;
            buktiEl.onwheel = function(e) {
                e.preventDefault();
                var rect = buktiEl.getBoundingClientRect();
                var ox = ((e.clientX - rect.left) / rect.width) * 100;
                var oy = ((e.clientY - rect.top) / rect.height) * 100;
                zoom += e.deltaY < 0 ? 0.2 : -0.2;
                zoom = Math.max(1, Math.min(5, zoom));
                img.style.transformOrigin = ox + '% ' + oy + '%';
                img.style.transform = 'scale(' + zoom + ')';
                buktiEl.style.cursor = zoom > 1 ? 'zoom-out' : 'zoom-in';
            };
            buktiEl.ondblclick = function() { zoom = 1; img.style.transform = 'scale(1)'; buktiEl.style.cursor = 'zoom-in'; };
        } else {
            buktiEl.style.cssText = baseStyle + 'background:var(--gray-100);flex-direction:column;';
            buktiEl.innerHTML = emptyBukti;
        }

        document.getElementById('modalBtnApprove').onclick = function() { ajaxAction(data.approve_url, null); closeModal('modalPengajuanPending'); };
        document.getElementById('modalBtnReject').onclick = function() { ajaxAction(data.reject_url, null); closeModal('modalPengajuanPending'); };

        openModal('modalPengajuanPending');
    }

    // ─── Modal Detail Hari Ini ───────────────────────────────────────────────
    function openHariIniModal(data) {
        document.getElementById('detailNamaHariIni').textContent    = data.user_name || 'N/A';
        document.getElementById('detailTanggalHariIni').textContent = data.tanggal   || '-';
        document.getElementById('detailJenisHariIni').textContent   = capitalize(data.jenis);
        document.getElementById('detailJamHariIni').textContent     = data.jam       || '-';
        resolveAddress(data.lokasi, document.getElementById('detailLokasiHariIni'));

        // Status kehadiran
        var statusEl = document.getElementById('detailStatusHariIni');
        var label = (data.status_label || '-').trim();
        var cls = 'badge-neutral';
        if (label === 'Tepat Waktu') cls = 'badge-success';
        else if (label === 'Terlambat' || label === 'Waktu Kurang' || label === 'Pulang Cepat') cls = 'badge-danger';
        statusEl.innerHTML = '<span class="badge ' + cls + '">' + label + '</span>';

        // Status verifikasi
        var verifEl = document.getElementById('detailVerifikasiHariIni');
        var st = (data.status || '').toLowerCase();
        var verifCls = st === 'approved' ? 'badge-success' : st === 'rejected' ? 'badge-danger' : 'badge-warning';
        var verifLabel = st === 'approved' ? 'Disetujui' : st === 'rejected' ? 'Ditolak' : 'Menunggu';
        verifEl.innerHTML = '<span class="badge ' + verifCls + '">' + verifLabel + '</span>';

        setZoomableFoto(document.getElementById('detailFotoHariIni'), data.foto_url, emptyFotoHtml);

        // Koordinat
        var lat = NaN, lng = NaN;
        if (data.lokasi) {
            var parts = data.lokasi.trim().split(',');
            if (parts.length === 2) {
                lat = parseFloat(parts[0].trim());
                lng = parseFloat(parts[1].trim());
            }
        }

        hariIniCoords = { lat: lat, lng: lng, lokasi: data.lokasi || 'Lokasi tidak tersedia' };
        openModal('modalDetailHariIni');
    }

    // ─── Modal Detail Lembur ─────────────────────────────────────────────────
    var lemburCoords = { lat: NaN, lng: NaN, lokasi: '' };

    function openLemburModal(data) {
        document.getElementById('detailNamaLembur').textContent    = data.user_name || 'N/A';
        document.getElementById('detailTanggalLembur').textContent = data.tanggal   || '-';
        document.getElementById('detailJenisLembur').textContent   = data.jenis     || '-';
        document.getElementById('detailJamLembur').textContent     = data.jam       || '-';
        resolveAddress(data.lokasi, document.getElementById('detailLokasiLembur'));

        var st = (data.status || '').toLowerCase();
        var verifCls = st === 'approved' ? 'on-time' : st === 'rejected' ? 'late' : 'pending';
        document.getElementById('detailVerifikasiLembur').innerHTML = '<span class="status-badge ' + verifCls + '">' + capitalize(st) + '</span>';

        setZoomableFoto(document.getElementById('detailFotoLembur'), data.foto_url, emptyFotoHtml);

        var lat = NaN, lng = NaN;
        if (data.lokasi) {
            var parts = data.lokasi.trim().split(',');
            if (parts.length === 2) { lat = parseFloat(parts[0].trim()); lng = parseFloat(parts[1].trim()); }
        }
        lemburCoords = { lat: lat, lng: lng, lokasi: data.lokasi || 'Lokasi tidak tersedia' };
        openModal('modalDetailLembur');
    }

    // ─── Modal Helpers ────────────────────────────────────────────────────────
    function openModal(id) {
        var modal = document.getElementById(id);
        if (!modal) return;

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(function() { modal.classList.add('show'); });

        if (id === 'modalPresensiPending') renderMap('presensiMap', 'mapLoading', 'mapError', pendingCoords, 'pending');
        if (id === 'modalDetailHariIni')   renderMap('hariIniMap', 'hariIniMapLoading', 'hariIniMapError', hariIniCoords, 'hariIni');
        if (id === 'modalDetailLembur')    renderMap('lemburMap', 'lemburMapLoading', 'lemburMapError', lemburCoords, 'lembur');
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        if (!modal) return;

        modal.classList.remove('show');
        setTimeout(function() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 200);

        if (id === 'modalPresensiPending') destroyMap('pending');
        if (id === 'modalDetailHariIni')   destroyMap('hariIni');
        if (id === 'modalDetailLembur')    destroyMap('lembur');
    }

    // ─── Map ──────────────────────────────────────────────────────────────────
    function renderMap(mapElId, loadingId, errorId, coords, type) {
        var mapLoading = document.getElementById(loadingId);
        var mapError   = document.getElementById(errorId);
        var mapEl      = document.getElementById(mapElId);

        mapLoading.style.display = 'flex';
        mapError.style.display   = 'none';

        destroyMap(type);

        if (!coords || isNaN(coords.lat) || isNaN(coords.lng)) {
            mapLoading.style.display = 'none';
            mapError.style.display   = 'flex';
            return;
        }

        var lat    = coords.lat;
        var lng    = coords.lng;
        var lokasi = coords.lokasi;

        setTimeout(function () {
            if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
                mapLoading.style.display = 'none';
                mapError.style.display   = 'flex';
                return;
            }

            try {
                var map = L.map(mapElId, { zoomControl: true }).setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap', maxZoom: 19
                }).addTo(map);

                var marker = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup('<b>Lokasi Presensi</b><br>' + lokasi)
                    .openPopup();

                map.invalidateSize();
                mapLoading.style.display = 'none';

                if (type === 'pending') { presensiMap = map; currentMarker = marker; }
                else if (type === 'lembur') { lemburMapInstance = map; }
                else { hariIniMap = map; hariIniMarker = marker; }
            } catch (err) {
                console.error('Map error:', err);
                mapLoading.style.display = 'none';
                mapError.style.display   = 'flex';
            }
        }, 200);
    }

    var lemburMapInstance = null;

    function destroyMap(type) {
        if (type === 'pending' && presensiMap) {
            presensiMap.remove();
            presensiMap = null;
            currentMarker = null;
        }
        if (type === 'hariIni' && hariIniMap) {
            hariIniMap.remove();
            hariIniMap = null;
            hariIniMarker = null;
        }
        if (type === 'lembur' && lemburMapInstance) {
            lemburMapInstance.remove();
            lemburMapInstance = null;
        }
    }

    // ─── Utilities ────────────────────────────────────────────────────────────
    function setZoomableFoto(container, url, emptyHtml) {
        if (!url) { container.innerHTML = emptyHtml; container.onwheel = null; return; }
        container.style.cursor = 'zoom-in';
        container.style.overflow = 'hidden';
        var img = document.createElement('img');
        img.src = url;
        img.style.cssText = 'width:100%;height:100%;object-fit:contain;display:block;transition:transform 0.15s ease;';
        img.onerror = function() { container.innerHTML = emptyHtml; container.onwheel = null; };
        container.innerHTML = '';
        container.appendChild(img);
        var z = 1;
        container.onwheel = function(e) {
            e.preventDefault();
            var r = container.getBoundingClientRect();
            var ox = ((e.clientX - r.left) / r.width) * 100;
            var oy = ((e.clientY - r.top) / r.height) * 100;
            z += e.deltaY < 0 ? 0.25 : -0.25;
            z = Math.max(1, Math.min(5, z));
            img.style.transformOrigin = ox + '% ' + oy + '%';
            img.style.transform = 'scale(' + z + ')';
            container.style.cursor = z > 1 ? 'zoom-out' : 'zoom-in';
        };
        container.ondblclick = function() { z = 1; img.style.transform = 'scale(1)'; container.style.cursor = 'zoom-in'; };
    }

    var emptyFotoHtml = '<div style="display:flex;flex-direction:column;align-items:center;gap:6px;"><div style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;"><i class="fas fa-camera" style="font-size:18px;color:var(--gray-400);"></i></div><span style="font-size:12px;color:var(--gray-400);">Tidak ada foto</span></div>';

    function resolveAddress(lokasi, el) {
        if (!lokasi || !el) { el.textContent = 'Tidak ada lokasi'; return; }
        var parts = lokasi.split(',');
        if (parts.length < 2) { el.textContent = lokasi; return; }
        var lat = parts[0].trim(), lng = parts[1].trim();
        el.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size:10px;margin-right:4px;"></i> Mendeteksi...';
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && data.display_name) {
                    el.textContent = data.display_name;
                } else {
                    el.textContent = lat + ', ' + lng;
                }
            })
            .catch(function() { el.textContent = lat + ', ' + lng; });
    }

    function capitalize(str) {
        if (!str) return '-';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function setFormAction(formId, url) {
        var form = document.getElementById(formId);
        if (form && url) form.action = url;
    }

    // ===== TABLE SORT + PAGINATION =====
    function initTable(tbodyId) {
        var tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        var perPage = parseInt(tbody.getAttribute('data-paginate')) || 5;
        var allRows = Array.from(tbody.querySelectorAll('tr'));
        if (allRows.length === 0 || (allRows.length === 1 && allRows[0].querySelector('.empty-state'))) return;

        var table = tbody.closest('table');
        var container = table.parentElement;

        var paginationDiv = document.createElement('div');
        paginationDiv.className = 'table-pagination';
        container.appendChild(paginationDiv);

        var instance = { allRows: allRows, rows: allRows.slice(), currentPage: 1, _paginationDiv: paginationDiv, _searchQuery: '' };
        tableInstances[tbodyId] = instance;

        function render() {
            renderTable(instance, tbody, perPage);
        }

        paginationDiv.addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (!btn || btn.disabled) return;
            var page = btn.getAttribute('data-page');
            if (page === 'prev') instance.currentPage--;
            else if (page === 'next') instance.currentPage++;
            else instance.currentPage = parseInt(page);
            render();
        });

        // Sorting
        var ths = table.querySelectorAll('th[data-sort]');
        var monthMap = {Jan:1,Feb:2,Mar:3,Apr:4,Mei:5,Jun:6,Jul:7,Agu:8,Sep:9,Okt:10,Nov:11,Des:12};

        function applySort(colIndex, dir, sortType, save) {
            ths.forEach(function(h) {
                h.classList.remove('sort-asc', 'sort-desc');
                var ic = h.querySelector('.sort-icon');
                if (ic) ic.className = 'fas fa-sort sort-icon';
            });
            var th = ths[colIndex] || table.querySelectorAll('th')[colIndex];
            if (!th) return;
            th.classList.add('sort-' + dir);
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.className = 'fas fa-sort-' + (dir === 'asc' ? 'up' : 'down') + ' sort-icon';

            var actualColIndex = Array.from(th.parentElement.children).indexOf(th);

            instance.rows.sort(function(a, b) {
                var aCell = a.children[actualColIndex];
                var bCell = b.children[actualColIndex];
                if (!aCell || !bCell) return 0;
                var aVal = aCell.textContent.trim();
                var bVal = bCell.textContent.trim();
                if (sortType === 'date') {
                    var ap = aVal.split(' '), bp = bVal.split(' ');
                    var aD = new Date(parseInt(ap[2]), (monthMap[ap[1]] || 1) - 1, parseInt(ap[0]));
                    var bD = new Date(parseInt(bp[2]), (monthMap[bp[1]] || 1) - 1, parseInt(bp[0]));
                    return dir === 'asc' ? aD - bD : bD - aD;
                }
                return dir === 'asc' ? aVal.localeCompare(bVal, 'id') : bVal.localeCompare(aVal, 'id');
            });
            instance.allRows = instance.rows.slice();
            instance.rows.forEach(function(row) { tbody.appendChild(row); });
            instance.currentPage = 1;
            render();
            // Re-apply search if active
            if (instance._searchQuery) {
                var q = instance._searchQuery;
                instance.rows = instance.allRows.filter(function(r) { return r.textContent.toLowerCase().indexOf(q) !== -1; });
                instance.currentPage = 1;
                render();
            }

            if (save) {
                localStorage.setItem('sort_' + tbodyId, JSON.stringify({ col: colIndex, dir: dir, type: sortType }));
            }
        }

        ths.forEach(function(th, thIdx) {
            th.addEventListener('click', function() {
                var sortType = th.getAttribute('data-sort');
                var dir = th.classList.contains('sort-asc') ? 'desc' : 'asc';
                applySort(thIdx, dir, sortType, true);
            });
        });

        // Restore saved sort
        var saved = localStorage.getItem('sort_' + tbodyId);
        if (saved) {
            try {
                var s = JSON.parse(saved);
                applySort(s.col, s.dir, s.type, false);
            } catch(e) {}
        } else {
            render();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        try { initSearchScroll(); } catch(e) { console.error('initSearchScroll:', e); }
        initTable('presensiPendingTable');
        initTable('pengajuanPendingTable');
        initTable('cutiPendingTable');
        initTable('presensiMasukTable');
        initTable('presensiPulangTable');
        initTable('lemburMasukTable');
        initTable('lemburPulangTable');

        // AJAX approve/reject — no page reload
        document.querySelectorAll('.inline-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = form.querySelector('button[type="submit"]');
                var originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                })
                .then(function(r) { return r.json().catch(function() { return {}; }); })
                .then(function() {
                    // Remove the row from table
                    var row = form.closest('tr');
                    if (row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(function() { row.remove(); }, 300);
                    }
                    // Close modal if open
                    document.querySelectorAll('.modal-overlay').forEach(function(m) {
                        if (m.style.display === 'flex') closeModal(m.id);
                    });
                })
                .catch(function() {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    alert('Gagal memproses. Coba lagi.');
                });
            });
        });

        // Also handle modal approve/reject forms
        ['formApprovePresensi','formRejectPresensi'].forEach(function(fid) {
            var form = document.getElementById(fid);
            if (!form) return;
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = form.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                })
                .then(function() {
                    closeModal('modalPresensiPending');
                    // Remove matching row from table
                    document.querySelectorAll('#presensiPendingTable tr').forEach(function(row) {
                        var approveUrl = row.dataset ? row.dataset.approveUrl : '';
                        if (approveUrl === form.action || form.action.includes(approveUrl)) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(function() { row.remove(); }, 300);
                        }
                    });
                })
                .catch(function() {
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    btn.disabled = false;
                });
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('attendanceChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Hadir',
                        data: @json($chartHadir),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Terlambat',
                        data: @json($chartTelat),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Lembur',
                        data: @json($chartLembur),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, boxHeight: 10, borderRadius: 5, useBorderRadius: true, padding: 20, font: { size: 12, weight: '500' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30,41,59,0.9)',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: true,
                        boxWidth: 8, boxHeight: 8, boxPadding: 4
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 }, color: '#94a3b8' },
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        border: { display: false }
                    },
                    x: {
                        ticks: { font: { size: 10 }, color: '#94a3b8' },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    });
</script>

@endsection