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
       class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600 hover:text-pac-black-400 transition-colors truncate max-w-[180px]">
      {{ Str::limit($board->title, 20) }}
    </a>
    <span class="text-pac-black-700">›</span>
    <span class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600">수정</span>
  </div>

  <div class="bg-pac-black-900 border border-pac-black-100">

    {{-- 카드 헤더 --}}
    <div class="px-6 py-4 border-b border-pac-black-100">
      <h1 class="font-display text-xl uppercase tracking-wider text-white">
        게시글 수정 <span class="text-pac-yellow-500">— {{ $meta['label'] }}</span>
      </h1>
    </div>

    <form method="POST" action="{{ route('boards.update', [$type, $board]) }}" class="p-6 space-y-5">
      @csrf
      @method('PUT')

      {{-- 제목 --}}
      <div>
        <label class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-500 mb-2 block">
          제목 <span class="text-pac-pink-500">*</span>
        </label>
        <input type="text" name="title"
               value="{{ old('title', $board->title) }}"
               placeholder="제목을 입력하세요"
               required maxlength="200"
               class="w-full bg-pac-black-800 border border-pac-black-100 focus:border-pac-yellow-500
                      text-white placeholder:text-pac-black-600
                      font-body text-sm px-4 py-3 outline-none transition-colors">
        @error('title')
          <p class="font-body text-xs text-pac-pink-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- 문의게시판: 비밀글 --}}
      @if($type === 'qna')
      <div class="flex items-center gap-3 py-2 px-3 bg-pac-black-800 border border-pac-black-100/60">
        <input type="checkbox" name="is_secret" id="is_secret" value="1"
               {{ old('is_secret', $board->is_secret) ? 'checked' : '' }}
               class="w-4 h-4 accent-pac-yellow-500 cursor-pointer">
        <label for="is_secret" class="font-body text-sm text-pac-black-400 cursor-pointer select-none">
          🔒 비밀글 — 작성자와 관리자만 열람 가능
        </label>
      </div>
      @endif

      {{-- TipTap 에디터 --}}
      <div>
        <label class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-500 mb-2 block">
          내용 <span class="text-pac-pink-500">*</span>
        </label>
        <x-tiptap-editor :value="old('content', $board->content ?? '')" />
      </div>

      {{-- 이미지 안내 --}}
      <div class="flex gap-2 items-start px-3 py-2.5 bg-pac-yellow-500/5 border border-pac-yellow-500/20">
        <span class="text-pac-yellow-600 flex-shrink-0 text-xs mt-0.5">💡</span>
        <p class="font-body text-xs text-pac-black-500">
          이미지는 장당 최대 <span class="text-pac-black-300">10MB</span>,
          게시물당 최대 <span class="text-pac-black-300">5장</span>까지 첨부 가능합니다.
          에디터 하단 슬롯에서 이미지 추가 후
          <span class="text-pac-yellow-500">[본문삽입]</span>을 클릭하면 커서 위치에 삽입됩니다.
        </p>
      </div>

      {{-- 액션 --}}
      <div class="flex items-center justify-between pt-2 border-t border-pac-black-100">
        <a href="{{ route('boards.show', [$type, $board]) }}" class="pac-btn-ghost">취소</a>
        <button type="submit" class="pac-btn">수정 완료</button>
      </div>

    </form>
  </div>

</div>
</x-app-layout>
