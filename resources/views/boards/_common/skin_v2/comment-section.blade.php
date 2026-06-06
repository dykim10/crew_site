@php $commentBase = 'boards.' . $board->board_type . '.comments'; @endphp

<section class="mt-8 border-t border-[#2a2a2a] pt-8">

  <h3 class="font-display text-[10px] tracking-[3px] uppercase text-[#666] mb-6">
    댓글 <span class="text-pac-pink-500">{{ $board->comments->count() }}</span>
  </h3>

  @forelse($board->comments as $comment)
  <div class="mb-4" id="comment-{{ $comment->id }}">
    <div class="bg-[#1a1a1a] border border-[#2a2a2a] p-4"
         x-data="{ editing: false, reply: false }">

      <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-2.5">
          @php
            $nick = $comment->author->nickname ?? '?';
            $role = $comment->author->role ?? 'member';
            $bg   = match($role) { 'super_admin' => '#E80043', 'region_admin' => '#E5AD16', 'operator' => '#10b981', default => '#2a2a2a' };
            $tc   = ($role === 'region_admin') ? '#1A1212' : '#fff';
          @endphp
          <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 text-[10px] font-display"
               style="background:{{ $bg }};color:{{ $tc }};">
            {{ mb_strtoupper(mb_substr($nick, 0, 1)) }}
          </div>
          <span class="font-body text-xs font-semibold text-white">{{ $nick }}</span>
          <span class="font-display text-[9px] tracking-wider text-[#555]">
            {{ $comment->created_at->format('Y.m.d H:i') }}
          </span>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          @can('update', $comment)
            <button type="button" @click="editing = !editing"
                    class="font-display text-[9px] tracking-wider uppercase text-[#555] hover:text-pac-pink-400 transition-colors">수정</button>
          @endcan
          @can('delete', $comment)
            <form method="POST" action="{{ route($commentBase . '.destroy', [$board, $comment]) }}"
                  onsubmit="return confirm('댓글을 삭제하시겠습니까?');">
              @csrf @method('DELETE')
              <button type="submit"
                      class="font-display text-[9px] tracking-wider uppercase text-[#555] hover:text-pac-pink-500 transition-colors">삭제</button>
            </form>
          @endcan
          @auth
            <button type="button" @click="reply = !reply"
                    class="font-display text-[9px] tracking-wider uppercase text-[#555] hover:text-pac-pink-400 transition-colors">답글</button>
          @endauth
        </div>
      </div>

      <div x-show="!editing" class="mt-3 font-body text-sm text-[#aaa] leading-relaxed whitespace-pre-wrap">{{ $comment->content }}</div>

      <form x-show="editing" method="POST"
            action="{{ route($commentBase . '.update', [$board, $comment]) }}"
            class="mt-3" x-cloak>
        @csrf @method('PUT')
        <textarea name="content" rows="3"
                  class="w-full bg-[#0d0d0d] border border-[#2a2a2a] focus:border-pac-pink-500
                         text-white placeholder:text-[#555] font-body text-sm px-3 py-2 outline-none resize-none">{{ $comment->content }}</textarea>
        <div class="flex gap-2 mt-2">
          <button type="submit"
                  class="px-3 py-1.5 bg-pac-pink-500 text-white font-display text-xs tracking-[2px] uppercase hover:bg-pac-pink-600 transition-colors">저장</button>
          <button type="button" @click="editing = false"
                  class="px-3 py-1.5 border border-[#2a2a2a] text-[#aaa] font-display text-xs tracking-[2px] uppercase hover:border-[#555] transition-colors">취소</button>
        </div>
      </form>

      <form x-show="reply" method="POST"
            action="{{ route($commentBase . '.store', $board) }}"
            class="mt-3 pl-4 border-l-2 border-pac-pink-500/30" x-cloak>
        @csrf
        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
        <textarea name="content" rows="2" placeholder="답글을 입력하세요..."
                  class="w-full bg-[#0d0d0d] border border-[#2a2a2a] focus:border-pac-pink-500
                         text-white placeholder:text-[#555] font-body text-sm px-3 py-2 outline-none resize-none"></textarea>
        <div class="flex gap-2 mt-2">
          <button type="submit"
                  class="px-3 py-1.5 bg-pac-pink-500 text-white font-display text-xs tracking-[2px] uppercase">등록</button>
          <button type="button" @click="reply = false"
                  class="px-3 py-1.5 border border-[#2a2a2a] text-[#aaa] font-display text-xs tracking-[2px] uppercase">취소</button>
        </div>
      </form>
    </div>

    {{-- 대댓글 --}}
    @foreach($comment->replies as $reply)
    <div class="ml-6 mt-1 bg-[#141414] border border-[#2a2a2a] border-l-2 border-l-pac-pink-500/20 p-4"
         x-data="{ editing: false }">
      <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-2.5">
          @php
            $rNick = $reply->author->nickname ?? '?';
            $rRole = $reply->author->role ?? 'member';
            $rBg   = match($rRole) { 'super_admin' => '#E80043', 'region_admin' => '#E5AD16', 'operator' => '#10b981', default => '#2a2a2a' };
            $rTc   = ($rRole === 'region_admin') ? '#1A1212' : '#fff';
          @endphp
          <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 text-[9px] font-display"
               style="background:{{ $rBg }};color:{{ $rTc }};">
            {{ mb_strtoupper(mb_substr($rNick, 0, 1)) }}
          </div>
          <span class="font-body text-xs font-semibold text-[#ccc]">{{ $rNick }}</span>
          <span class="font-display text-[9px] tracking-wider text-[#555]">{{ $reply->created_at->format('Y.m.d H:i') }}</span>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          @can('update', $reply)
            <button type="button" @click="editing = !editing"
                    class="font-display text-[9px] tracking-wider uppercase text-[#555] hover:text-pac-pink-400 transition-colors">수정</button>
          @endcan
          @can('delete', $reply)
            <form method="POST" action="{{ route($commentBase . '.destroy', [$board, $reply]) }}"
                  onsubmit="return confirm('댓글을 삭제하시겠습니까?');">
              @csrf @method('DELETE')
              <button type="submit"
                      class="font-display text-[9px] tracking-wider uppercase text-[#555] hover:text-pac-pink-500 transition-colors">삭제</button>
            </form>
          @endcan
        </div>
      </div>
      <div x-show="!editing" class="mt-2 font-body text-sm text-[#888] leading-relaxed whitespace-pre-wrap">{{ $reply->content }}</div>
      <form x-show="editing" method="POST"
            action="{{ route($commentBase . '.update', [$board, $reply]) }}" class="mt-2" x-cloak>
        @csrf @method('PUT')
        <textarea name="content" rows="2"
                  class="w-full bg-[#0d0d0d] border border-[#2a2a2a] focus:border-pac-pink-500
                         text-white font-body text-sm px-3 py-2 outline-none resize-none">{{ $reply->content }}</textarea>
        <div class="flex gap-2 mt-2">
          <button type="submit" class="px-3 py-1.5 bg-pac-pink-500 text-white font-display text-xs tracking-[2px] uppercase">저장</button>
          <button type="button" @click="editing = false" class="px-3 py-1.5 border border-[#2a2a2a] text-[#aaa] font-display text-xs tracking-[2px] uppercase">취소</button>
        </div>
      </form>
    </div>
    @endforeach
  </div>
  @empty
  <p class="font-body text-sm text-[#555] mb-6">첫 번째 댓글을 남겨보세요.</p>
  @endforelse

  @auth
  <div class="mt-6 pt-6 border-t border-[#2a2a2a]">
    <form method="POST" action="{{ route($commentBase . '.store', $board) }}">
      @csrf
      @error('content')<p class="font-body text-xs text-pac-pink-500 mb-2">{{ $message }}</p>@enderror
      <textarea name="content" rows="3" placeholder="댓글을 입력하세요 (최대 2,000자)"
                class="w-full bg-[#1a1a1a] border border-[#2a2a2a] focus:border-pac-pink-500
                       text-white placeholder:text-[#555] font-body text-sm px-4 py-3 outline-none resize-none">{{ old('content') }}</textarea>
      <div class="flex justify-end mt-2">
        <button type="submit"
                class="px-5 py-2.5 bg-pac-pink-500 text-white font-display text-xs tracking-[2px] uppercase hover:bg-pac-pink-600 transition-colors">
          댓글 등록
        </button>
      </div>
    </form>
  </div>
  @endauth

</section>
