<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 브레드크럼 --}}
  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('boards.index', $type) }}"
       class="font-display text-[10px] tracking-[3px] uppercase text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
      {{ $meta['label'] }}
    </a>
    <span class="text-pac-black-700">›</span>
    <a href="{{ route('boards.show', [$type, $board]) }}"
       class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600 hover:text-pac-black-400 transition-colors truncate max-w-[200px]">
      {{ Str::limit($board->title, 20) }}
    </a>
    <span class="text-pac-black-700">›</span>
    <span class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600">수정</span>
  </div>

  <div class="bg-pac-black-900 border border-pac-black-100 p-6">
    <h1 class="font-display text-2xl uppercase tracking-wider text-white mb-6">
      게시글 수정 <span class="text-pac-yellow-500 text-lg">— {{ $meta['label'] }}</span>
    </h1>

    <form method="POST" action="{{ route('boards.update', [$type, $board]) }}"
          x-data="tiptap({ content: @json(old('content', $board->content ?? '')) })">
      @csrf
      @method('PUT')

      <div class="space-y-5">

        {{-- 제목 --}}
        <div>
          <label class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-500 mb-2 block">
            제목 <span class="text-pac-pink-500">*</span>
          </label>
          <input type="text" name="title"
                 value="{{ old('title', $board->title) }}"
                 placeholder="제목을 입력하세요"
                 class="w-full bg-pac-black-800 border border-pac-black-100 focus:border-pac-yellow-500
                        text-white placeholder:text-pac-black-600
                        font-body text-sm px-4 py-3 outline-none transition-colors">
          @error('title')
            <p class="font-body text-xs text-pac-pink-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- 문의게시판: 비밀글 --}}
        @if($type === 'qna')
        <div class="flex items-center gap-3">
          <input type="checkbox" name="is_secret" id="is_secret" value="1"
                 {{ old('is_secret', $board->is_secret) ? 'checked' : '' }}
                 class="w-4 h-4 accent-pac-yellow-500">
          <label for="is_secret" class="font-body text-sm text-pac-black-400 cursor-pointer">
            비밀글 (작성자와 관리자만 열람 가능)
          </label>
        </div>
        @endif

        {{-- TipTap 에디터 --}}
        <div>
          <label class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-500 mb-2 block">
            내용 <span class="text-pac-pink-500">*</span>
          </label>

          {{-- 툴바 --}}
          <div class="flex flex-wrap gap-1 px-3 py-2 bg-pac-black-800 border border-pac-black-100 border-b-0">
            <button type="button" @click="toggleBold()"
                    :class="formats.bold ? 'text-pac-yellow-400 bg-pac-black-700' : 'text-pac-black-500 hover:text-pac-black-200'"
                    class="w-7 h-7 flex items-center justify-center font-display font-bold text-sm transition-colors rounded">B</button>
            <button type="button" @click="toggleItalic()"
                    :class="formats.italic ? 'text-pac-yellow-400 bg-pac-black-700' : 'text-pac-black-500 hover:text-pac-black-200'"
                    class="w-7 h-7 flex items-center justify-center font-display italic text-sm transition-colors rounded">I</button>
            <button type="button" @click="toggleStrike()"
                    :class="formats.strike ? 'text-pac-yellow-400 bg-pac-black-700' : 'text-pac-black-500 hover:text-pac-black-200'"
                    class="w-7 h-7 flex items-center justify-center font-display text-sm line-through transition-colors rounded">S</button>
            <span class="w-px bg-pac-black-700 mx-1"></span>
            <button type="button" @click="toggleBulletList()"
                    :class="formats.bulletList ? 'text-pac-yellow-400 bg-pac-black-700' : 'text-pac-black-500 hover:text-pac-black-200'"
                    class="w-7 h-7 flex items-center justify-center text-sm transition-colors rounded" title="목록">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
            <button type="button" @click="toggleOrderedList()"
                    :class="formats.orderedList ? 'text-pac-yellow-400 bg-pac-black-700' : 'text-pac-black-500 hover:text-pac-black-200'"
                    class="w-7 h-7 flex items-center justify-center text-sm transition-colors rounded" title="번호목록">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h13M7 12h13M7 16h13M3 8h.01M3 12h.01M3 16h.01"/>
              </svg>
            </button>
            <button type="button" @click="toggleBlockquote()"
                    :class="formats.blockquote ? 'text-pac-yellow-400 bg-pac-black-700' : 'text-pac-black-500 hover:text-pac-black-200'"
                    class="w-7 h-7 flex items-center justify-center text-sm transition-colors rounded" title="인용">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
              </svg>
            </button>
          </div>

          {{-- 에디터 본체 --}}
          <div x-ref="editorEl"
               class="tiptap-editor min-h-[240px] bg-pac-black-800 border border-pac-black-100
                      focus-within:border-pac-yellow-500 px-4 py-3 transition-colors
                      text-pac-black-200 font-body text-sm">
          </div>

          {{-- 숨김 textarea — 폼 제출용 --}}
          <textarea name="content" class="hidden" x-model="content"></textarea>
          @error('content')
            <p class="font-body text-xs text-pac-pink-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

      </div>

      {{-- 버튼 --}}
      <div class="flex items-center justify-between pt-6 mt-6 border-t border-pac-black-100">
        <a href="{{ route('boards.show', [$type, $board]) }}" class="pac-btn-ghost">취소</a>
        <button type="submit" class="pac-btn">수정 완료</button>
      </div>
    </form>
  </div>

</div>
</x-app-layout>
