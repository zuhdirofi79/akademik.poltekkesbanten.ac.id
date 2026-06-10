@if ($paginator->hasPages())
    <nav style="margin-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
        @if ($paginator->onFirstPage())
            <span class="btn-sm btn-gray" style="opacity:0.5;">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn-sm btn-gray">Sebelumnya</a>
        @endif

        <span style="padding:0.4rem 0.75rem;">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn-sm btn-teal">Selanjutnya</a>
        @else
            <span class="btn-sm btn-teal" style="opacity:0.5;">Selanjutnya</span>
        @endif
    </nav>
@endif
