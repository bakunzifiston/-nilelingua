@if ($paginator->hasPages() || $paginator->total() > 0)
    <div class="a-pagination">
        <span>Showing {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}</span>
        @if ($paginator->hasPages())
            <nav aria-label="Pages">
                @if ($paginator->onFirstPage())
                    <span class="a-btn a-btn-sm" aria-disabled="true">← Previous</span>
                @else
                    <a class="a-btn a-btn-sm" href="{{ $paginator->previousPageUrl() }}" rel="prev">← Previous</a>
                @endif
                <span class="a-btn a-btn-sm a-btn-quiet" aria-current="page">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
                @if ($paginator->hasMorePages())
                    <a class="a-btn a-btn-sm" href="{{ $paginator->nextPageUrl() }}" rel="next">Next →</a>
                @else
                    <span class="a-btn a-btn-sm" aria-disabled="true">Next →</span>
                @endif
            </nav>
        @endif
    </div>
@endif
