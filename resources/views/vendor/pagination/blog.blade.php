@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-ink">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</p>
        <ul class="flex flex-wrap gap-2">
            @if ($paginator->onFirstPage())
                <li><span class="btn border border-line bg-subtle text-muted" aria-disabled="true">« Anterior</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-secondary">« Anterior</a></li>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                <li>
                    @if ($page == $paginator->currentPage())
                        <span class="btn btn-primary" aria-current="page"><span class="sr-only">Página </span>{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="btn btn-secondary"><span class="sr-only">Ir a la página </span>{{ $page }}</a>
                    @endif
                </li>
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-secondary">Siguiente »</a></li>
            @else
                <li><span class="btn border border-line bg-subtle text-muted" aria-disabled="true">Siguiente »</span></li>
            @endif
        </ul>
    </nav>
@endif
