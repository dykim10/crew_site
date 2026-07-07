<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 헤더 --}}
  <div class="flex items-end justify-between mb-8">
    <div>
      <p class="font-display text-[10px] tracking-[4px] uppercase text-pac-yellow-500 mb-2">Community</p>
      <h1 class="font-display text-4xl lg:text-5xl uppercase tracking-tight text-white leading-none">
        {{ $meta['label'] }}
      </h1>
      <p class="font-body text-sm text-pac-black-500 mt-2">{{ $meta['desc'] }}</p>
    </div>
    <a href="{{ route('boards.create', $type) }}" class="pac-btn shrink-0">+ 글쓰기</a>
  </div>

  {{-- 성공/오류 메시지 --}}
  @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-emerald-900/40 border border-emerald-700/50 text-emerald-400 font-body text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- 글 없음 --}}
  @if($posts->isEmpty())
  <div class="border border-pac-black-100 bg-pac-black-900 py-24 text-center">
    <p class="font-display text-4xl font-black text-pac-black-700 uppercase tracking-widest mb-3">EMPTY</p>
    <p class="font-body text-sm text-pac-black-600">첫 번째 게시글을 작성해보세요.</p>
  </div>

  @else
  <div class="border border-pac-black-100 bg-pac-black-900 overflow-hidden">

    {{-- 테이블 헤더 --}}
    <div class="hidden sm:grid grid-cols-[60px_1fr_120px_100px_60px] gap-x-3 px-5 py-2.5
                border-b border-pac-black-100 bg-pac-black-800">
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600 text-center">번호</span>
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600">제목</span>
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600 text-center">작성자</span>
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600 text-center">날짜</span>
      <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-600 text-center">조회</span>
    </div>

    @foreach($posts as $idx => $post)
    <a href="{{ route('boards.show', [$type, $post]) }}"
       class="group flex sm:grid sm:grid-cols-[60px_1fr_120px_100px_60px] sm:gap-x-3
              items-start sm:items-center px-5 py-3.5
              border-b border-pac-black-100/40 last:border-0
              hover:bg-white/[0.025] transition-colors duration-150
              {{ $post->is_pinned ?? false ? 'border-l-2 border-l-pac-yellow-500' : '' }}"
       style="text-decoration:none;">

      {{-- 번호 --}}
      <span class="hidden sm:block font-display text-xs text-pac-black-600 text-center shrink-0">
        @if($post->is_pinned ?? false)
          <span class="font-display text-[9px] tracking-wider uppercase bg-pac-yellow-500 text-pac-black px-1.5 py-0.5">공지</span>
        @else
          {{ $posts->total() - ($posts->currentPage() - 1) * $posts->perPage() - $idx }}
        @endif
      </span>

      {{-- 제목 --}}
      <div class="flex-1 min-w-0">
        <p class="font-body text-sm font-semibold text-pac-black-200 leading-snug
                  group-hover:text-pac-yellow-400 transition-colors truncate">
          @if($post->is_secret)
            <span class="text-pac-black-600 mr-1">🔒</span>
          @endif
          {{ $post->title }}
        </p>
        {{-- 모바일 서브 --}}
        <div class="sm:hidden flex items-center gap-2 mt-1">
          <span class="font-display text-[9px] tracking-wider text-pac-black-600">
            {{ $post->author->nickname ?? '?' }}
          </span>
          <span class="text-pac-black-700">·</span>
          <span class="font-display text-[9px] tracking-wider text-pac-black-600">
            {{ $post->created_at->format('m.d') }}
          </span>
        </div>
      </div>

      {{-- 작성자 --}}
      <span class="hidden sm:block font-body text-xs text-pac-black-500 text-center truncate shrink-0">
        {{ $post->author->nickname ?? '?' }}
      </span>

      {{-- 날짜 --}}
      <span class="hidden sm:block font-display text-[10px] text-pac-black-600 text-center shrink-0">
        {{ $post->created_at->format('Y.m.d') }}
      </span>

      {{-- 조회 --}}
      <span class="hidden sm:block font-display text-[10px] text-pac-black-600 text-center shrink-0">
        {{ number_format($post->view_count) }}
      </span>
    </a>
    @endforeach
  </div>
  @endif

  {{-- 페이지네이션 --}}
  @if(isset($posts) && $posts->hasPages())
  <div class="flex justify-center mt-6">{{ $posts->links() }}</div>
  @endif

</div>
</x-app-layout>
