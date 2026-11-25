@if ($paginator->hasPages())
    <nav class="post-pagination" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="post-pagination__icon" aria-disabled="true" aria-label="Previous page"><i class="fas fa-long-arrow-alt-left" aria-hidden="true"></i></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="post-pagination__icon" rel="prev" aria-label="Previous page"><i class="fas fa-long-arrow-alt-left" aria-hidden="true"></i></a>
        @endif
        @php $window = Illuminate\Pagination\UrlWindow::make($paginator->onEachSide(1)); @endphp
        @foreach ($window as $pages)
            @if (is_array($pages))
                @foreach ($pages as $page => $url)
                    <a href="{{ $url }}" class="post-pagination__btn {{ $page === $paginator->currentPage() ? 'active' : '' }}" aria-label="Page {{ $page }}" @if ($page === $paginator->currentPage()) aria-current="page" @endif>{{ str_pad((string) $page, 2, '0', STR_PAD_LEFT) }}</a>
                @endforeach
            @elseif ($pages === null)
                <span aria-hidden="true">&hellip;</span>
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="post-pagination__icon" rel="next" aria-label="Next page"><i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i></a>
        @else
            <span class="post-pagination__icon" aria-disabled="true" aria-label="Next page"><i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i></span>
        @endif
    </nav>
@endif
