@if ($paginator->hasPages())
<nav class="flex items-center justify-between gap-4" role="navigation">

  {{-- 결과 정보 --}}
  <p class="font-body text-xs text-pac-black-500">
    전체 <span class="text-pac-black-300">{{ $paginator->total() }}</span>건
    · {{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}페이지
  </p>

  {{-- 페이지 번호 --}}
  <div class="flex items-center gap-0.5">

    {{-- 이전 --}}
    @if ($paginator->onFirstPage())
      <span class="inline-flex items-center justify-center w-8 h-8 font-display text-[11px] tracking-wider
                   text-pac-black-700 cursor-not-allowed">‹</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
         class="inline-flex items-center justify-center w-8 h-8 font-display text-[11px] tracking-wider
                text-pac-black-400 hover:text-pac-yellow-500 transition-colors">‹</a>
    @endif

    {{-- 번호 목록 --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="inline-flex items-center justify-center w-8 h-8 font-display text-xs text-pac-black-600">…</span>
      @endif
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="inline-flex items-center justify-center w-8 h-8 font-display text-xs
                         bg-pac-yellow-500 text-pac-black-900 font-bold">{{ $page }}</span>
          @else
            <a href="{{ $url }}"
               class="inline-flex items-center justify-center w-8 h-8 font-display text-xs
                      text-pac-black-400 border border-transparent
                      hover:border-pac-black-100 hover:text-pac-black-200 transition-colors">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- 다음 --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" rel="next"
         class="inline-flex items-center justify-center w-8 h-8 font-display text-[11px] tracking-wider
                text-pac-black-400 hover:text-pac-yellow-500 transition-colors">›</a>
    @else
      <span class="inline-flex items-center justify-center w-8 h-8 font-display text-[11px] tracking-wider
                   text-pac-black-700 cursor-not-allowed">›</span>
    @endif

  </div>
</nav>
@endif
