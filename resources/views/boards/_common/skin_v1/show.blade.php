<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 브레드크럼 --}}
  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('boards.index', $type) }}"
       class="font-display text-[10px] tracking-[3px] uppercase text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
      {{ $meta['label'] }}
    </a>
    <span class="text-pac-black-700">›</span>
    <span class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600">상세</span>
  </div>

  {{-- 성공 메시지 --}}
  @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-emerald-900/40 border border-emerald-700/50 text-emerald-400 font-body text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- 게시글 --}}
  <article class="bg-pac-black-900 border border-pac-black-100">

    {{-- 헤더 --}}
    <div class="px-6 pt-6 pb-5 border-b border-pac-black-100">
      @if($board->is_secret)
        <span class="inline-block font-display text-[9px] tracking-wider uppercase
                     bg-pac-black-700 text-pac-black-400 px-1.5 py-0.5 mb-3">🔒 비밀글</span>
      @endif
      <h1 class="font-body text-xl font-bold text-white leading-snug mb-4">{{ $board->title }}</h1>

      <div class="flex items-center justify-between flex-wrap gap-3">
        {{-- 작성자 아바타 --}}
        <div class="flex items-center gap-2.5">
          @php
            $nick = $board->author->nickname ?? '?';
            $role = $board->author->role ?? 'member';
            $bg   = match($role) { 'super_admin' => '#E80043', 'region_admin' => '#E5AD16', 'operator' => '#10b981', default => '#2D2020' };
            $tc   = ($role === 'region_admin') ? '#1A1212' : '#fff';
          @endphp
          <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
               style="background:{{ $bg }};">
            <span class="font-display text-sm leading-none" style="color:{{ $tc }};">
              {{ mb_strtoupper(mb_substr($nick, 0, 1)) }}
            </span>
          </div>
          <div>
            <p class="font-body text-sm font-semibold text-pac-black-300">{{ $nick }}</p>
            <p class="font-display text-[9px] tracking-wider uppercase text-pac-black-600">
              {{ $board->created_at->format('Y.m.d H:i') }}
            </p>
          </div>
        </div>

        {{-- 메타 + 수정/삭제 --}}
        <div class="flex items-center gap-4">
          <span class="font-display text-[9px] tracking-wider uppercase text-pac-black-600">
            조회 {{ number_format($board->view_count) }}
          </span>
          @can('update', $board)
            <a href="{{ route('boards.edit', [$type, $board]) }}"
               class="font-display text-[10px] tracking-wider uppercase text-pac-black-500 hover:text-pac-yellow-500 transition-colors">
              수정
            </a>
          @endcan
          @can('delete', $board)
            <form method="POST" action="{{ route('boards.destroy', [$type, $board]) }}"
                  onsubmit="return confirm('게시글을 삭제하시겠습니까?');">
              @csrf @method('DELETE')
              <button type="submit"
                      class="font-display text-[10px] tracking-wider uppercase text-pac-black-600 hover:text-pac-pink-500 transition-colors">
                삭제
              </button>
            </form>
          @endcan
        </div>
      </div>
    </div>

    {{-- 본문 --}}
    <div class="px-6 py-6 min-h-[200px]">
      <div class="tiptap-output font-body text-sm text-pac-black-300 leading-relaxed">
        {!! $board->content !!}
      </div>
    </div>

  </article>

  {{-- 댓글 --}}
  @include("boards._common.skin_v1.comment-section")

  {{-- 하단 네비 --}}
  <div class="flex items-center justify-between mt-8">
    <a href="{{ route('boards.index', $type) }}" class="pac-btn-ghost">← 목록으로</a>
    <a href="{{ route('boards.create', $type) }}" class="pac-btn">+ 글쓰기</a>
  </div>

</div>
</x-app-layout>
