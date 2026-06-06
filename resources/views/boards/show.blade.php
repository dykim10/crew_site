<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- ── 브레드크럼 ── --}}
  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('boards.index', $type) }}"
       class="font-display text-[10px] tracking-[3px] uppercase text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
      {{ $meta['label'] }}
    </a>
    <span class="text-pac-black-700">›</span>
    <span class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600">상세</span>
  </div>

  {{-- ── 포스트 ── --}}
  <article class="bg-pac-black-900 border border-pac-black-100">

    {{-- 헤더 --}}
    <div class="px-6 pt-6 pb-5 border-b border-pac-black-100">
      @if($board->is_pinned)
        <span class="inline-block font-display text-[9px] tracking-wider uppercase
                     bg-pac-yellow-500 text-pac-black px-1.5 py-0.5 mb-3">공지</span>
      @endif
      <h1 class="font-body text-xl font-bold text-pac-black-900 leading-snug mb-4">
        {{ $board->title }}
      </h1>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          {{-- 아바타 --}}
          @php
            $nick  = $board->author->nickname ?? '?';
            $init  = mb_strtoupper(mb_substr($nick, 0, 1));
            $role  = $board->author->role ?? 'member';
            $bg    = match($role) { 'super_admin' => '#E80043', 'region_admin' => '#E5AD16', 'operator' => '#10b981', default => '#2D2020' };
            $txt   = ($role === 'region_admin') ? '#1A1212' : '#fff';
          @endphp
          <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0"
                 style="background:{{ $bg }};">
              <span class="font-display text-xs leading-none" style="color:{{ $txt }};">{{ $init }}</span>
            </div>
            <div>
              <p class="font-body text-sm font-semibold text-pac-black-300">{{ $nick }}</p>
              <p class="font-display text-[9px] tracking-wider uppercase text-pac-black-600">
                {{ $board->created_at->format('Y.m.d H:i') }}
              </p>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <span class="font-display text-[9px] tracking-wider uppercase text-pac-black-600">
            조회 {{ number_format($board->view_count) }}
          </span>
          @if($board->isWrittenBy(auth()->id()) || auth()->user()?->isAdmin())
            <a href="{{ route('boards.edit', [$type, $board]) }}"
               class="font-display text-[10px] tracking-wider uppercase text-pac-black-500 hover:text-pac-yellow-500 transition-colors">
              수정
            </a>
            <form method="POST" action="{{ route('boards.destroy', [$type, $board]) }}"
                  onsubmit="return confirm('삭제하시겠습니까?');" class="inline">
              @csrf @method('DELETE')
              <button type="submit"
                      class="font-display text-[10px] tracking-wider uppercase text-pac-black-600 hover:text-pac-pink-500 transition-colors">
                삭제
              </button>
            </form>
          @endif
        </div>
      </div>
    </div>

    {{-- 포토: 대표 이미지 --}}
    @if($board->thumbnail_url)
    <div class="border-b border-pac-black-100">
      <img src="{{ $board->thumbnail_url }}" alt="{{ $board->title }}"
           class="w-full max-h-[520px] object-cover">
    </div>
    @endif

    {{-- 본문 (purifier 통과한 TipTap HTML) --}}
    <div class="px-6 py-6 min-h-[200px]">
      @if($board->content)
        <div class="tiptap-output font-body text-sm text-pac-black-400 leading-relaxed">
          {!! $board->content !!}
        </div>
      @else
        <p class="font-body text-sm text-pac-black-700 italic">내용 없음</p>
      @endif
    </div>

  </article>

  {{-- ── 댓글 섹션 (자유/문의 게시판만) ── --}}
  @if(in_array($type, ['free', 'qna']))
    @include('boards._common.skin_v1.comment-section')
  @endif

  {{-- ── 하단 네비 ── --}}
  <div class="flex items-center justify-between mt-6">
    <a href="{{ route('boards.index', $type) }}"
       class="pac-btn-ghost text-sm">← 목록으로</a>
    <a href="{{ route('boards.create', $type) }}"
       class="pac-btn text-sm">+ 글쓰기</a>
  </div>

</div>
</x-app-layout>
