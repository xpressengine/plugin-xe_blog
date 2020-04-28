@if ($paginator->hasPages())
    <section class="section-xepage-paging">
        <a href="{{ $paginator->previousPageUrl() }}" class="xepage-paging-item xepage-paging-item--prev" title="이전">
            <i class="xi-angle-left-thin"></i>
        </a>

        <div class="xepage-paging__box xepage-paging__box--normal">
            @foreach ($elements as $element)
                @if (is_string($element) === true)

                @elseif (is_array($element) === true)
                    @foreach($element as $page => $url)
                        <a href="{{ $url }}" class="xepage-paging-item @if ($page === $paginator->currentPage()) xepage-paging-item--active @endif">{{ sprintf('%02d', $page) }}</a>
                    @endforeach
                @endif
            @endforeach
        </div>

        <div class="xepage-paging__box xepage-paging__box--simple">
            <span class="xepage-paging__box-items">
                <strong class="xepage-paging-item xepage-paging-item--active">{{ $paginator->currentPage() }}</strong> / <span class="xepage-paging-item">{{ $paginator->lastPage() }}</span>
            </span>
        </div>

        <a href="{{ $paginator->nextPageUrl() }}" class="xepage-paging-item xepage-paging-item--next" title="다음">
            <i class="xi-angle-right-thin"></i>
        </a>
    </section>
@endif
