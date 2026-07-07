<x-app-layout>
@php $isGrid = ($meta['layout'] === 'grid'); @endphp
<div class="max-w-5xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- ── 페이지 헤더 ── --}}
  <div class="flex items-end justify-between mb-8">
    <div>
      <p class="font-display text-[10px] tracking-[4px] uppercase text-pac-yellow-500 mb-2">Community</p>
      <h1 class="font-display text-4xl lg:text-5xl uppercase tracking-tight text-white leading-none">
        {{ $meta['label'] }}
      </h1>
      <p class="font-body text-sm text-pac-black-400 mt-2">{{ $meta['desc'] }}</p>
    </div>
    <a href="{{ route('boards.create', $type) }}"
       class="pac-btn shrink-0">
      + 글쓰기
    </a>
  </div>

  {{-- ── 게시물 없음 ── --}}
  @if($posts->isEmpty())
  <div class="border border-pac-black-100 bg-pac-black-900 py-20 text-center">
    <p class="font-display text-4xl font-black text-pac-black-700 uppercase tracking-widest mb-2">EMPTY</p>
    <p class="font-body text-sm text-pac-black-600">첫 번째 게시글을 작성해보세요.</p>
  </div>

  {{-- ── 포토게시판: 그리드 ── --}}
  @elseif($isGrid)
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
    @foreach($posts as $post)
    <a href="{{ route('boards.show', [$type, $post]) }}"
       class="group relative overflow-hidden bg-pac-black-900 border border-pac-black-100 hover:border-pac-yellow-500 transition-colors duration-200"
       style="aspect-ratio:1;">

      {{-- 썸네일 --}}
      @if($post->thumbnail_url)
        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}"
             class="absolute inset-0 w-full h-full object-cover">
      @else
        <div class="absolute inset-0 flex items-center justify-center"
             style="background:linear-gradient(135deg,#2d1a00,#0d0d0d);">
          <span class="font-display text-3xl tracking-widest text-pac-yellow-500 opacity-20">PAC</span>
        </div>
      @endif

      {{-- 오버레이 --}}
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent
                  opacity-0 group-hover:opacity-100 transition-opacity duration-200">
        <div class="absolute bottom-0 left-0 right-0 p-3">
          <p class="font-body text-xs font-semibold text-white line-clamp-2 leading-tight">
            {{ $post->title }}
          </p>
          <p class="font-display text-[9px] tracking-wider uppercase text-white/50 mt-1">
            {{ $post->author->nickname ?? '?' }} · {{ $post->created_at->format('m.d') }}
          </p>
        </div>
      </div>

      {{-- 고정 배지 --}}
      @if($post->is_pinned)
        <div class="absolute top-2 left-2">
          <span class="font-display text-[8px] tracking-wider uppercase bg-pac-pink-500 text-white px-1.5 py-0.5">고정</span>
        </div>
      @endif
    </a>
    @endforeach
  </div>

  {{-- ── 리스트형 (자유/문의) ── --}}
  @else
  <div class="border border-pac-black-100 bg-pac-black-900 overflow-hidden">
    {{-- 테이블 헤더 --}}
    <div class="hidden sm:grid grid-cols-[auto_1fr_auto_auto_auto] gap-x-4 px-5 py-2.5
                border-b border-pac-black-100 bg-pac-black-800">
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600 w-10 text-center">번호</span>
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600">제목</span>
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600 w-16 text-center">작성자</span>
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600 w-14 text-center">날짜</span>
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600 w-10 text-center">조회</span>
    </div>

    @foreach($posts as $idx => $post)
    <a href="{{ route('boards.show', [$type, $post]) }}"
       class="group flex sm:grid sm:grid-cols-[auto_1fr_auto_auto_auto] sm:gap-x-4
              items-start sm:items-center px-5 py-3.5
              border-b border-pac-black-100/50 last:border-0
              hover:bg-white/[0.025] transition-colors duration-150
              {{ $post->is_pinned ? 'border-l-2 border-l-pac-yellow-500' : '' }}"
       style="text-decoration:none;">

      {{-- 번호 --}}
      <span class="hidden sm:block font-display text-sm text-pac-black-600 w-10 text-center shrink-0">
        @if($post->is_pinned)
          <span class="font-display text-[9px] tracking-wider uppercase bg-pac-yellow-500 text-pac-black px-1.5 py-0.5">공지</span>
        @else
          {{ $posts->total() - ($posts->currentPage() - 1) * $posts->perPage() - $idx }}
        @endif
      </span>

      {{-- 제목 --}}
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 min-w-0">
          @if($post->is_secret)
            <span class="text-pac-black-500 flex-shrink-0 text-xs" title="비밀글">🔒</span>
          @endif
          <p class="font-body text-sm font-semibold text-pac-black-200 leading-snug
                    group-hover:text-pac-yellow-400 transition-colors duration-150 truncate">
            {{ $post->title }}
          </p>
          @if(isset($post->comments_count) && $post->comments_count > 0)
            <span class="flex-shrink-0 font-display text-[9px] text-pac-yellow-500">[{{ $post->comments_count }}]</span>
          @endif
        </div>
        {{-- 모바일 서브정보 --}}
        <div class="sm:hidden flex items-center gap-2 mt-1">
          <span class="font-display text-[9px] tracking-wider text-pac-black-500">
            {{ $post->author->nickname ?? '?' }}
          </span>
          <span class="text-pac-black-700">·</span>
          <span class="font-display text-[9px] tracking-wider text-pac-black-500">
            {{ $post->created_at->format('Y.m.d') }}
          </span>
        </div>
      </div>

      {{-- 작성자 --}}
      <span class="hidden sm:block font-body text-xs text-pac-black-500 w-16 text-center truncate shrink-0">
        {{ $post->author->nickname ?? '?' }}
      </span>

      {{-- 날짜 --}}
      <span class="hidden sm:block font-display text-[10px] text-pac-black-600 w-14 text-center shrink-0">
        {{ $post->created_at->format('Y.m.d') }}
      </span>

      {{-- 조회수 --}}
      <span class="hidden sm:block font-display text-[10px] text-pac-black-600 w-10 text-center shrink-0">
        {{ number_format($post->view_count) }}
      </span>
    </a>
    @endforeach
  </div>
  @endif

  {{-- ── 페이지네이션 ── --}}
  @if($posts->hasPages())
  <div class="flex justify-center mt-6">
    {{ $posts->links() }}
  </div>
  @endif

</div>
</x-app-layout>
