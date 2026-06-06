<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 헤더 --}}
  <div class="flex items-end justify-between mb-8">
    <div>
      <p class="font-display text-[10px] tracking-[4px] uppercase text-pac-pink-500 mb-2">Community</p>
      <h1 class="font-display text-4xl lg:text-5xl uppercase tracking-tight text-white leading-none">
        {{ $meta['label'] }}
      </h1>
      <p class="font-body text-sm text-[#aaaaaa] mt-2">{{ $meta['desc'] }}</p>
    </div>
    <a href="{{ route('boards.create', $type) }}"
       class="shrink-0 inline-flex items-center px-5 py-2.5 bg-pac-pink-500 text-white
              font-display text-xs tracking-[2px] uppercase hover:bg-pac-pink-600 transition-colors">
      + 글쓰기
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-emerald-900/40 border border-emerald-700/50 text-emerald-400 font-body text-sm">
      {{ session('success') }}
    </div>
  @endif

  @if($posts->isEmpty())
  <div class="border border-[#2a2a2a] bg-[#141414] py-24 text-center">
    <p class="font-display text-4xl font-black text-[#333] uppercase tracking-widest mb-3">EMPTY</p>
    <p class="font-body text-sm text-[#666]">첫 번째 게시글을 작성해보세요.</p>
  </div>

  @else
  <div class="border border-[#2a2a2a] bg-[#141414] overflow-hidden">

    <div class="hidden sm:grid grid-cols-[60px_1fr_120px_100px_60px] gap-x-3 px-5 py-2.5
                border-b border-[#2a2a2a] bg-[#1a1a1a]">
      <span class="font-display text-[9px] tracking-[3px] uppercase text-[#555] text-center">번호</span>
      <span class="font-display text-[9px] tracking-[3px] uppercase text-[#555]">제목</span>
      <span class="font-display text-[9px] tracking-[3px] uppercase text-[#555] text-center">작성자</span>
      <span class="font-display text-[9px] tracking-[3px] uppercase text-[#555] text-center">날짜</span>
      <span class="font-display text-[9px] tracking-[3px] uppercase text-[#555] text-center">조회</span>
    </div>

    @foreach($posts as $idx => $post)
    <a href="{{ route('boards.show', [$type, $post]) }}"
       class="group flex sm:grid sm:grid-cols-[60px_1fr_120px_100px_60px] sm:gap-x-3
              items-start sm:items-center px-5 py-3.5
              border-b border-[#2a2a2a]/60 last:border-0
              hover:bg-white/[0.03] transition-colors duration-150
              {{ $post->is_pinned ?? false ? 'border-l-2 border-l-pac-pink-500' : '' }}"
       style="text-decoration:none;">

      <span class="hidden sm:block font-display text-xs text-[#555] text-center shrink-0">
        @if($post->is_pinned ?? false)
          <span class="font-display text-[9px] tracking-wider uppercase bg-pac-pink-500 text-white px-1.5 py-0.5">공지</span>
        @else
          {{ $posts->total() - ($posts->currentPage() - 1) * $posts->perPage() - $idx }}
        @endif
      </span>

      <div class="flex-1 min-w-0">
        <p class="font-body text-sm font-semibold text-white leading-snug
                  group-hover:text-pac-pink-400 transition-colors truncate">
          @if($post->is_secret)<span class="text-[#555] mr-1">🔒</span>@endif
          {{ $post->title }}
        </p>
        <div class="sm:hidden flex items-center gap-2 mt-1">
          <span class="font-display text-[9px] tracking-wider text-[#555]">
            {{ $post->author->nickname ?? '?' }}
          </span>
          <span class="text-[#333]">·</span>
          <span class="font-display text-[9px] tracking-wider text-[#555]">
            {{ $post->created_at->format('m.d') }}
          </span>
        </div>
      </div>

      <span class="hidden sm:block font-body text-xs text-[#666] text-center truncate shrink-0">
        {{ $post->author->nickname ?? '?' }}
      </span>
      <span class="hidden sm:block font-display text-[10px] text-[#555] text-center shrink-0">
        {{ $post->created_at->format('Y.m.d') }}
      </span>
      <span class="hidden sm:block font-display text-[10px] text-[#555] text-center shrink-0">
        {{ number_format($post->view_count) }}
      </span>
    </a>
    @endforeach
  </div>
  @endif

  @if(isset($posts) && $posts->hasPages())
  <div class="flex justify-center mt-6">{{ $posts->links() }}</div>
  @endif

</div>
</x-app-layout>
