@if ($paginator->hasPages())
<div style="padding:14px 18px; border-top:1px solid var(--dm-border,#e2e8f0); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
    <span style="font-size:12px; color:var(--dm-muted,#64748b); white-space:nowrap;">
        {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
    </span>
    <div style="display:flex; gap:4px; flex-wrap:wrap;">
        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span style="width:30px; height:30px; border:1px solid var(--dm-border,#e2e8f0); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--dm-muted,#94a3b8); opacity:0.4; background:var(--dm-card,#fff);">
                <i class="fas fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="width:30px; height:30px; border:1px solid var(--dm-border,#e2e8f0); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--dm-muted,#64748b); text-decoration:none; background:var(--dm-card,#fff); transition:all .15s;" onmouseover="this.style.background='var(--dm-bg,#f1f5f9)'" onmouseout="this.style.background='var(--dm-card,#fff)'">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pages --}}
        @php
            $start = max($paginator->currentPage() - 2, 1);
            $end = min($start + 4, $paginator->lastPage());
            $start = max($end - 4, 1);
        @endphp

        @if($start > 1)
            <a href="{{ $paginator->url(1) }}" style="min-width:30px; height:30px; border:1px solid var(--dm-border,#e2e8f0); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; color:var(--dm-muted,#64748b); text-decoration:none; background:var(--dm-card,#fff); padding:0 6px;">1</a>
            @if($start > 2)
                <span style="min-width:30px; height:30px; display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--dm-muted,#94a3b8);">...</span>
            @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $paginator->currentPage())
                <span style="min-width:30px; height:30px; border:1px solid #2E97D4; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#fff; background:#2E97D4; padding:0 6px; box-shadow:0 2px 8px rgba(46,151,212,0.3);">{{ $i }}</span>
            @else
                <a href="{{ $paginator->url($i) }}" style="min-width:30px; height:30px; border:1px solid var(--dm-border,#e2e8f0); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; color:var(--dm-muted,#64748b); text-decoration:none; background:var(--dm-card,#fff); padding:0 6px; transition:all .15s;" onmouseover="this.style.background='var(--dm-bg,#f1f5f9)'" onmouseout="this.style.background='var(--dm-card,#fff)'">{{ $i }}</a>
            @endif
        @endfor

        @if($end < $paginator->lastPage())
            @if($end < $paginator->lastPage() - 1)
                <span style="min-width:30px; height:30px; display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--dm-muted,#94a3b8);">...</span>
            @endif
            <a href="{{ $paginator->url($paginator->lastPage()) }}" style="min-width:30px; height:30px; border:1px solid var(--dm-border,#e2e8f0); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; color:var(--dm-muted,#64748b); text-decoration:none; background:var(--dm-card,#fff); padding:0 6px;">{{ $paginator->lastPage() }}</a>
        @endif

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="width:30px; height:30px; border:1px solid var(--dm-border,#e2e8f0); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--dm-muted,#64748b); text-decoration:none; background:var(--dm-card,#fff); transition:all .15s;" onmouseover="this.style.background='var(--dm-bg,#f1f5f9)'" onmouseout="this.style.background='var(--dm-card,#fff)'">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span style="width:30px; height:30px; border:1px solid var(--dm-border,#e2e8f0); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--dm-muted,#94a3b8); opacity:0.4; background:var(--dm-card,#fff);">
                <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </div>
</div>
@endif
