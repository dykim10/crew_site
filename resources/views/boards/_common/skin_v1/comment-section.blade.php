@php
  $commentBase = 'boards.' . $board->board_type . '.comments';
  $actionBtn = 'font-body text-xs px-2.5 py-0.5 rounded border border-white/20 bg-transparent
                text-pac-black-300 hover:text-pac-yellow-500 hover:border-pac-yellow-500/50
                transition-colors cursor-pointer leading-none';
  $actionBtnDanger = 'font-body text-xs px-2.5 py-0.5 rounded border border-white/20 bg-transparent
                      text-pac-black-300 hover:text-pac-pink-400 hover:border-pac-pink-500/50
                      transition-colors cursor-pointer leading-none';
@endphp

<section class="mt-8 border-t border-white/10 pt-8">

  <h3 class="font-body text-sm font-semibold text-pac-black-200 mb-5">
    댓글 <span class="text-pac-yellow-500">{{ $board->comments->count() }}</span>
  </h3>

  {{-- 댓글 목록 --}}
  @forelse($board->comments as $comment)
  <div class="mb-3" id="comment-{{ $comment->id }}">

    {{-- 최상위 댓글 --}}
    <div class="bg-pac-black-800/60 border border-white/8 rounded p-4"
         x-data="{ editing: false, reply: false, replyContent: '' }">

      {{-- 작성자 행 --}}
      <div class="flex items-center justify-between gap-3">
        {{-- 아바타 + 닉네임 + 날짜 --}}
        <div class="flex items-center gap-2.5 min-w-0">
          @php
            $nick  = $comment->author->nickname ?? '?';
            $role  = $comment->author->role ?? 'member';
            $bg    = match($role) { 'super_admin' => '#E80043', 'region_admin' => '#E5AD16', 'operator' => '#10b981', default => '#2D2020' };
            $tc    = ($role === 'region_admin') ? '#1A1212' : '#fff';
          @endphp
          <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 text-xs font-display"
               style="background:{{ $bg }};color:{{ $tc }};">
            {{ mb_strtoupper(mb_substr($nick, 0, 1)) }}
          </div>
          <div class="flex items-baseline gap-2 min-w-0 truncate">
            <span class="font-body text-sm font-semibold text-white shrink-0">{{ $nick }}</span>
            <span class="font-body text-xs text-pac-black-400 shrink-0">{{ $comment->created_at->format('Y.m.d H:i') }}</span>
          </div>
        </div>

        {{-- 액션 버튼 --}}
        <div class="flex items-center gap-1.5 shrink-0">
          @can('update', $comment)
            <button type="button" @click="editing = !editing" class="{{ $actionBtn }}">수정</button>
          @endcan
          @can('delete', $comment)
            <form method="POST" action="{{ route($commentBase . '.destroy', [$board, $comment]) }}"
                  onsubmit="return confirm('댓글을 삭제하시겠습니까?');" class="inline-flex">
              @csrf @method('DELETE')
              <button type="submit" class="{{ $actionBtnDanger }}">삭제</button>
            </form>
          @endcan
          @auth
            <button type="button" @click="reply = !reply" class="{{ $actionBtn }}">답글</button>
          @endauth
        </div>
      </div>

      {{-- 댓글 본문 --}}
      <div x-show="!editing" class="mt-3 font-body text-sm text-pac-black-300 leading-relaxed whitespace-pre-wrap">{{ $comment->content }}</div>

      {{-- 인라인 수정 폼 --}}
      <form x-show="editing" method="POST"
            action="{{ route($commentBase . '.update', [$board, $comment]) }}"
            class="mt-3" x-cloak>
        @csrf @method('PUT')
        <textarea name="content" rows="3"
                  class="w-full bg-pac-black-900 border border-white/10 focus:border-pac-yellow-500
                         text-pac-black-200 placeholder:text-pac-black-600
                         font-body text-sm px-3 py-2 outline-none resize-none transition-colors rounded">{{ $comment->content }}</textarea>
        <div class="flex gap-2 mt-2">
          <button type="submit" class="pac-btn text-xs px-3 py-1.5">저장</button>
          <button type="button" @click="editing = false"
                  class="pac-btn-ghost text-xs px-3 py-1.5">취소</button>
        </div>
      </form>

      {{-- 답글 작성 폼 --}}
      <form x-show="reply" method="POST"
            action="{{ route($commentBase . '.store', $board) }}"
            class="mt-3 pl-4 border-l-2 border-pac-yellow-500/30" x-cloak>
        @csrf
        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
        <textarea name="content" rows="2" x-model="replyContent"
                  placeholder="답글을 입력하세요..."
                  class="w-full bg-pac-black-900 border border-white/10 focus:border-pac-yellow-500
                         text-pac-black-200 placeholder:text-pac-black-600
                         font-body text-sm px-3 py-2 outline-none resize-none transition-colors rounded"></textarea>
        <div class="flex gap-2 mt-2">
          <button type="submit" class="pac-btn text-xs px-3 py-1.5">등록</button>
          <button type="button" @click="reply = false; replyContent = ''"
                  class="pac-btn-ghost text-xs px-3 py-1.5">취소</button>
        </div>
      </form>
    </div>

    {{-- 대댓글 --}}
    @foreach($comment->replies as $reply)
    <div class="ml-6 mt-1 bg-pac-black-700/40 border border-white/6 border-l-2 border-l-pac-yellow-500/25 rounded-r p-4"
         x-data="{ editing: false }">
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
          @php
            $rNick = $reply->author->nickname ?? '?';
            $rRole = $reply->author->role ?? 'member';
            $rBg   = match($rRole) { 'super_admin' => '#E80043', 'region_admin' => '#E5AD16', 'operator' => '#10b981', default => '#2D2020' };
            $rTc   = ($rRole === 'region_admin') ? '#1A1212' : '#fff';
          @endphp
          <span class="text-pac-black-400 text-xs shrink-0">↳</span>
          <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 text-[11px] font-display"
               style="background:{{ $rBg }};color:{{ $rTc }};">
            {{ mb_strtoupper(mb_substr($rNick, 0, 1)) }}
          </div>
          <div class="flex items-baseline gap-2 min-w-0 truncate">
            <span class="font-body text-sm font-semibold text-white shrink-0">{{ $rNick }}</span>
            <span class="font-body text-xs text-pac-black-400 shrink-0">{{ $reply->created_at->format('Y.m.d H:i') }}</span>
          </div>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
          @can('update', $reply)
            <button type="button" @click="editing = !editing" class="{{ $actionBtn }}">수정</button>
          @endcan
          @can('delete', $reply)
            <form method="POST" action="{{ route($commentBase . '.destroy', [$board, $reply]) }}"
                  onsubmit="return confirm('댓글을 삭제하시겠습니까?');" class="inline-flex">
              @csrf @method('DELETE')
              <button type="submit" class="{{ $actionBtnDanger }}">삭제</button>
            </form>
          @endcan
        </div>
      </div>

      <div x-show="!editing" class="mt-2 ml-8 font-body text-sm text-pac-black-300 leading-relaxed whitespace-pre-wrap">{{ $reply->content }}</div>

      <form x-show="editing" method="POST"
            action="{{ route($commentBase . '.update', [$board, $reply]) }}"
            class="mt-2 ml-8" x-cloak>
        @csrf @method('PUT')
        <textarea name="content" rows="2"
                  class="w-full bg-pac-black-900 border border-white/10 focus:border-pac-yellow-500
                         text-pac-black-200 placeholder:text-pac-black-600
                         font-body text-sm px-3 py-2 outline-none resize-none transition-colors rounded">{{ $reply->content }}</textarea>
        <div class="flex gap-2 mt-2">
          <button type="submit" class="pac-btn text-xs px-3 py-1.5">저장</button>
          <button type="button" @click="editing = false" class="pac-btn-ghost text-xs px-3 py-1.5">취소</button>
        </div>
      </form>
    </div>
    @endforeach

  </div>
  @empty
  <p class="font-body text-sm text-pac-black-500 mb-6">첫 번째 댓글을 남겨보세요.</p>
  @endforelse

  {{-- 댓글 작성 폼 --}}
  @auth
  <div class="mt-6 pt-6 border-t border-white/8">
    <form method="POST" action="{{ route($commentBase . '.store', $board) }}">
      @csrf
      @error('content')
        <p class="font-body text-xs text-pac-pink-400 mb-2">{{ $message }}</p>
      @enderror
      <textarea name="content" rows="3"
                placeholder="댓글을 입력하세요 (최대 2,000자)"
                class="w-full bg-pac-black-800 border border-white/10 focus:border-pac-yellow-500
                       text-pac-black-200 placeholder:text-pac-black-600
                       font-body text-sm px-4 py-3 outline-none resize-none transition-colors rounded">{{ old('content') }}</textarea>
      <div class="flex justify-end mt-2">
        <button type="submit" class="pac-btn">댓글 등록</button>
      </div>
    </form>
  </div>
  @endauth

</section>
