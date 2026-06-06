<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('boards.index', $type) }}"
       class="font-display text-[10px] tracking-[3px] uppercase text-pac-pink-500 hover:text-pac-pink-400 transition-colors">
      {{ $meta['label'] }}
    </a>
    <span class="text-[#444]">›</span>
    <span class="font-display text-[10px] tracking-[3px] uppercase text-[#555]">
      {{ isset($board) ? '수정' : '글쓰기' }}
    </span>
  </div>

  <div class="bg-[#141414] border border-[#2a2a2a] p-6">
    <h2 class="font-display text-xl uppercase tracking-tight text-white mb-6">
      {{ isset($board) ? '게시글 수정' : '새 게시글' }}
    </h2>

    @php
      $isEdit = isset($board);
      $action = $isEdit
        ? route('boards.update', [$type, $board])
        : route('boards.store', $type);
    @endphp

    <form method="POST" action="{{ $action }}"
          x-data="tiptap({ content: @json(old('content', $board->content ?? '')) })"
          x-init="init()"
          @destroy.window="destroy()">
      @csrf
      @if($isEdit) @method('PUT') @endif

      <div class="space-y-5">

        <div>
          <label class="font-display text-[10px] tracking-[3px] uppercase text-[#666] mb-2 block">
            제목 <span class="text-pac-pink-500">*</span>
          </label>
          <input type="text" name="title"
                 value="{{ old('title', $board->title ?? '') }}"
                 placeholder="제목을 입력하세요"
                 class="w-full bg-[#0d0d0d] border border-[#2a2a2a] focus:border-pac-pink-500
                        text-white placeholder:text-[#444] font-body text-sm px-4 py-3 outline-none transition-colors">
          @error('title')<p class="font-body text-xs text-pac-pink-500 mt-1">{{ $message }}</p>@enderror
        </div>

        @if($type === 'qna')
        <div class="flex items-center gap-3">
          <input type="checkbox" name="is_secret" id="is_secret" value="1"
                 {{ old('is_secret', $board->is_secret ?? false) ? 'checked' : '' }}
                 class="w-4 h-4 accent-pac-pink-500">
          <label for="is_secret" class="font-body text-sm text-[#aaa] cursor-pointer">
            비밀글 (작성자와 관리자만 열람 가능)
          </label>
        </div>
        @endif

        <div>
          <label class="font-display text-[10px] tracking-[3px] uppercase text-[#666] mb-2 block">
            내용 <span class="text-pac-pink-500">*</span>
          </label>

          {{-- 툴바 --}}
          <div class="flex flex-wrap gap-1 px-3 py-2 bg-[#0d0d0d] border border-[#2a2a2a] border-b-0">
            <button type="button" @click="toggleBold()"
                    :class="formats.bold ? 'text-pac-pink-400 bg-[#1a1a1a]' : 'text-[#666] hover:text-white'"
                    class="w-7 h-7 flex items-center justify-center font-display font-bold text-sm transition-colors rounded">B</button>
            <button type="button" @click="toggleItalic()"
                    :class="formats.italic ? 'text-pac-pink-400 bg-[#1a1a1a]' : 'text-[#666] hover:text-white'"
                    class="w-7 h-7 flex items-center justify-center font-display italic text-sm transition-colors rounded">I</button>
            <button type="button" @click="toggleStrike()"
                    :class="formats.strike ? 'text-pac-pink-400 bg-[#1a1a1a]' : 'text-[#666] hover:text-white'"
                    class="w-7 h-7 flex items-center justify-center font-display text-sm line-through transition-colors rounded">S</button>
            <span class="w-px bg-[#2a2a2a] mx-1"></span>
            <button type="button" @click="toggleBulletList()"
                    :class="formats.bulletList ? 'text-pac-pink-400 bg-[#1a1a1a]' : 'text-[#666] hover:text-white'"
                    class="w-7 h-7 flex items-center justify-center transition-colors rounded">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
            <button type="button" @click="toggleOrderedList()"
                    :class="formats.orderedList ? 'text-pac-pink-400 bg-[#1a1a1a]' : 'text-[#666] hover:text-white'"
                    class="w-7 h-7 flex items-center justify-center transition-colors rounded">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h13M7 12h13M7 16h13M3 8h.01M3 12h.01M3 16h.01"/>
              </svg>
            </button>
            <button type="button" @click="toggleBlockquote()"
                    :class="formats.blockquote ? 'text-pac-pink-400 bg-[#1a1a1a]' : 'text-[#666] hover:text-white'"
                    class="w-7 h-7 flex items-center justify-center transition-colors rounded">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
              </svg>
            </button>
          </div>

          <div x-ref="editorEl"
               class="tiptap-editor min-h-[240px] bg-[#0d0d0d] border border-[#2a2a2a]
                      focus-within:border-pac-pink-500 px-4 py-3 transition-colors
                      text-white font-body text-sm">
          </div>

          <textarea name="content" class="hidden" x-model="content"></textarea>
          @error('content')<p class="font-body text-xs text-pac-pink-500 mt-1">{{ $message }}</p>@enderror
        </div>

      </div>

      <div class="flex items-center justify-between pt-6 mt-6 border-t border-[#2a2a2a]">
        <a href="{{ $isEdit ? route('boards.show', [$type, $board]) : route('boards.index', $type) }}"
           class="inline-flex items-center px-4 py-2 border border-[#2a2a2a] text-[#aaa]
                  font-display text-xs tracking-[2px] uppercase hover:border-[#555] transition-colors">
          취소
        </a>
        <button type="submit"
                class="inline-flex items-center px-6 py-2.5 bg-pac-pink-500 text-white
                       font-display text-xs tracking-[2px] uppercase hover:bg-pac-pink-600 transition-colors">
          {{ $isEdit ? '수정 완료' : '등록하기' }}
        </button>
      </div>
    </form>
  </div>

</div>
</x-app-layout>
