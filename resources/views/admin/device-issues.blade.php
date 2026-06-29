@extends(Auth::user()->role === 'operator' ? 'layouts.operator' : 'layouts.admin')
@section('title', 'Kendala Perangkat')

@section('content')
<style>
    .di-page { max-width:900px; }
    .di-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
    .di-title { font-size:20px; font-weight:700; color:var(--dm-text,#1e293b); margin:0; }
    .di-subtitle { font-size:13px; color:var(--dm-muted,#64748b); margin-top:2px; }
    .di-empty { text-align:center; padding:60px 20px; color:var(--dm-muted,#64748b); }
    .di-empty i { font-size:40px; opacity:0.2; display:block; margin-bottom:12px; }
    .di-user-card { background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; margin-bottom:12px; overflow:hidden; }
    .di-user-header { display:flex; align-items:center; gap:12px; padding:14px 16px; cursor:pointer; }
    .di-user-header:hover { background:rgba(0,0,0,0.02); }
    [data-theme="dark"] .di-user-header:hover { background:rgba(255,255,255,0.02); }
    .di-avatar { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; color:#fff; flex-shrink:0; }
    .di-user-info { flex:1; min-width:0; }
    .di-user-name { font-size:14px; font-weight:600; color:var(--dm-text,#1e293b); }
    .di-user-nip { font-size:11px; color:var(--dm-muted,#64748b); }
    .di-badge-count { background:#ef4444; color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:8px; }
    .di-issues { padding:0 16px 14px; display:flex; flex-direction:column; gap:8px; }
    .di-issue { display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--dm-bg,#f8fafc); border-radius:10px; border:1px solid var(--dm-border,#e2e8f0); }
    .di-issue-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; }
    .di-issue-body { flex:1; min-width:0; }
    .di-issue-type { font-size:13px; font-weight:600; color:var(--dm-text,#1e293b); }
    .di-issue-time { font-size:10px; color:var(--dm-muted,#64748b); }
    .di-issue-ua { font-size:9px; color:var(--dm-muted,#94a3b8); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .di-resolve-btn { padding:6px 12px; border-radius:8px; border:none; background:rgba(16,185,129,0.1); color:#10b981; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap; }
    .di-resolve-btn:hover { background:rgba(16,185,129,0.2); }
    .di-resolve-all { padding:6px 14px; border-radius:8px; border:none; background:#10b981; color:#fff; font-size:11px; font-weight:600; cursor:pointer; }
    .di-resolve-all:hover { background:#059669; }
    .di-tabs { display:flex; gap:6px; margin-bottom:16px; background:rgba(0,0,0,0.03); border-radius:12px; padding:4px; border:1px solid var(--dm-border,#e2e8f0); }
    .di-tab { flex:1; padding:9px 14px; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; background:transparent; color:var(--dm-muted,#64748b); }
    .di-tab.active { background:var(--dm-card,#fff); color:var(--dm-text,#1e293b); box-shadow:0 1px 3px rgba(0,0,0,0.08); }
    .di-resolved { opacity:0.6; }
    .di-resolved .di-issue { background:transparent; border-style:dashed; }
</style>

<div class="di-page">
    <div class="di-header">
        <div>
            <h1 class="di-title">Kendala Perangkat</h1>
            <p class="di-subtitle">Deteksi otomatis masalah kamera & lokasi pegawai</p>
        </div>
    </div>

    <div class="di-tabs">
        <button class="di-tab active" onclick="switchDiTab('active',this)">
            <i class="fas fa-circle-exclamation" style="margin-right:4px; color:#ef4444;"></i> Aktif ({{ $activeIssues->count() }})
        </button>
        <button class="di-tab" onclick="switchDiTab('resolved',this)">
            <i class="fas fa-circle-check" style="margin-right:4px; color:#10b981;"></i> Riwayat ({{ $resolvedIssues->count() }})
        </button>
    </div>

    <div id="tabActive">
        @if($activeIssues->isEmpty())
        <div class="di-empty">
            <i class="fas fa-mobile-screen-button"></i>
            <p style="font-size:14px; font-weight:500;">Tidak ada kendala perangkat</p>
            <p style="font-size:12px; margin-top:4px;">Semua pegawai dapat mengakses kamera & lokasi dengan normal</p>
        </div>
        @else
            @foreach($activeIssues as $userId => $issues)
            @php
                $u = $issues->first()->user;
                $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#84cc16'];
                $avatarColor = $colors[$userId % count($colors)];
                $initials = collect(explode(' ', $u->name ?? 'U'))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
            @endphp
            <div class="di-user-card">
                <div class="di-user-header">
                    <div class="di-avatar" style="background:{{ $avatarColor }};">{{ $initials }}</div>
                    <div class="di-user-info">
                        <div class="di-user-name">{{ $u->name ?? 'Unknown' }}</div>
                        <div class="di-user-nip">{{ $u->nip ?? '-' }}</div>
                    </div>
                    <span class="di-badge-count">{{ $issues->count() }}</span>
                    <button class="di-resolve-all" onclick="resolveUser({{ $userId }}, this)"><i class="fas fa-check" style="margin-right:3px;"></i> Selesai Semua</button>
                </div>
                <div class="di-issues">
                    @foreach($issues as $issue)
                    @php $ic = \App\Models\DeviceIssue::typeIcon($issue->issue_type); @endphp
                    <div class="di-issue" id="issue{{ $issue->id }}">
                        <div class="di-issue-icon" style="background:{{ $ic['bg'] }}; color:{{ $ic['color'] }};"><i class="fas {{ $ic['icon'] }}"></i></div>
                        <div class="di-issue-body">
                            <div class="di-issue-type">{{ \App\Models\DeviceIssue::typeLabel($issue->issue_type) }}</div>
                            <div class="di-issue-time">{{ $issue->reported_at->diffForHumans() }}</div>
                            @if($issue->user_agent)
                            <div class="di-issue-ua" title="{{ $issue->user_agent }}">{{ Str::limit($issue->user_agent, 60) }}</div>
                            @endif
                        </div>
                        <button class="di-resolve-btn" onclick="resolveIssue({{ $issue->id }}, this)"><i class="fas fa-check"></i></button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <div id="tabResolved" style="display:none;">
        @if($resolvedIssues->isEmpty())
        <div class="di-empty">
            <i class="fas fa-clock-rotate-left"></i>
            <p>Belum ada riwayat kendala</p>
        </div>
        @else
        <div class="di-resolved">
            @foreach($resolvedIssues as $issue)
            @php $ic = \App\Models\DeviceIssue::typeIcon($issue->issue_type); @endphp
            <div class="di-issue" style="margin-bottom:8px;">
                <div class="di-issue-icon" style="background:{{ $ic['bg'] }}; color:{{ $ic['color'] }};"><i class="fas {{ $ic['icon'] }}"></i></div>
                <div class="di-issue-body">
                    <div class="di-issue-type">{{ $issue->user->name ?? 'Unknown' }} — {{ \App\Models\DeviceIssue::typeLabel($issue->issue_type) }}</div>
                    <div class="di-issue-time">Dilaporkan {{ $issue->reported_at->diffForHumans() }} · Diselesaikan {{ $issue->resolved_at->diffForHumans() }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
    function switchDiTab(tab, btn) {
        document.getElementById('tabActive').style.display = tab === 'active' ? '' : 'none';
        document.getElementById('tabResolved').style.display = tab === 'resolved' ? '' : 'none';
        document.querySelectorAll('.di-tab').forEach(function(t) { t.classList.remove('active'); });
        btn.classList.add('active');
    }

    function resolveIssue(id, btn) {
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        fetch('/admin/kendala-perangkat/' + id + '/resolve', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(r) { return r.json(); }).then(function() {
            var el = document.getElementById('issue' + id);
            if (el) { el.style.opacity = '0.3'; setTimeout(function() { el.remove(); }, 300); }
        });
    }

    function resolveUser(userId, btn) {
        btn.disabled = true; btn.textContent = 'Memproses...';
        fetch('/admin/kendala-perangkat/user/' + userId + '/resolve', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(r) { return r.json(); }).then(function() {
            var card = btn.closest('.di-user-card');
            if (card) { card.style.opacity = '0.3'; setTimeout(function() { card.remove(); }, 300); }
        });
    }
</script>
@endsection
