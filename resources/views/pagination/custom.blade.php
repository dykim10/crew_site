@if ($paginator->hasPages())
<nav class="flex items-center justify-center gap-1 font-body text-sm" aria-label="페이지 탐색">

    {{-- 첫 페이지 --}}
    @if ($paginator->onFirstPage())
        <span class="px-2 py-1.5 text-pac-black-300 cursor-not-allowed select-none">«</span>
        <span class="px-2 py-1.5 text-pac-black-300 cursor-not-allowed select-none">‹</span>
    @else
        <a href="{{ $paginator->url(1) }}"
           class="px-2 py-1.5 text-pac-black-500 hover:text-pac-yellow-500 transition-colors duration-150"
           aria-label="첫 페이지">«</a>
        <a href="{{ $paginator->previousPageUrl() }}"
           class="px-2 py-1.5 text-pac-black-500 hover:text-pac-yellow-500 transition-colors duration-150"
           aria-label="이전 페이지">‹</a>
    @endif

    {{-- 페이지 번호 --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 py-1.5 text-pac-black-300 select-none">...</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 bg-pac-yellow-500 text-pac-black-900 font-bold rounded-lg min-w-[32px] text-center">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       class="px-3 py-1.5 text-pac-black-500 hover:text-pac-yellow-500 hover:bg-pac-black-50 rounded-lg min-w-[32px] text-center transition-colors duration-150">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- 마지막 페이지 --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="px-2 py-1.5 text-pac-black-500 hover:text-pac-yellow-500 transition-colors duration-150"
           aria-label="다음 페이지">›</a>
        <a href="{{ $paginator->url($paginator->lastPage()) }}"
           class="px-2 py-1.5 text-pac-black-500 hover:text-pac-yellow-500 transition-colors duration-150"
           aria-label="마지막 페이지">»</a>
    @else
        <span class="px-2 py-1.5 text-pac-black-300 cursor-not-allowed select-none">›</span>
        <span class="px-2 py-1.5 text-pac-black-300 cursor-not-allowed select-none">»</span>
    @endif

</nav>
@endif
