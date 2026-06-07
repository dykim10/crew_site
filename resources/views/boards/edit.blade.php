<x-app-layout>
<div class="max-w-4xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 페이지 헤더 --}}
  <div class="mb-6">
    <div class="flex items-center gap-2 mb-2">
      <a href="{{ route('boards.index', $type) }}"
         class="font-display text-[10px] tracking-[3px] uppercase text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
        {{ $meta['label'] }}
      </a>
      <span class="text-pac-black-600">›</span>
      <a href="{{ route('boards.show', [$type, $board]) }}"
         class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600 hover:text-pac-black-400 transition-colors truncate max-w-[180px]">
        {{ Str::limit($board->title, 20) }}
      </a>
      <span class="text-pac-black-600">›</span>
      <span class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600">수정</span>
    </div>
    <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wide text-pac-black-900 leading-none">
      게시글 수정
    </h1>
  </div>

  <form method="POST" action="{{ route('boards.update', [$type, $board]) }}">
    @csrf
    @method('PUT')

    {{-- 에디터 카드 --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

      {{-- 제목 입력 --}}
      <div class="px-6 pt-5 border-b-2 border-pac-black-900">
        <input type="text" name="title"
               value="{{ old('title', $board->title) }}"
               placeholder="제목을 입력하세요"
               required
               maxlength="200"
               class="w-full font-body text-xl font-semibold text-gray-900 bg-transparent
                      outline-none pb-4 placeholder:text-gray-300 placeholder:font-normal
                      transition-colors">
        @error('title')
          <p class="font-body text-xs text-pac-pink-500 pb-2 -mt-3">{{ $message }}</p>
        @enderror
      </div>

      {{-- 문의게시판: 비밀글 옵션 --}}
      @if($type === 'qna')
      <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center gap-3">
        <input type="checkbox" name="is_secret" id="is_secret" value="1"
               {{ old('is_secret', $board->is_secret) ? 'checked' : '' }}
               class="w-4 h-4 accent-pac-yellow-500 cursor-pointer">
        <label for="is_secret" class="font-body text-sm text-gray-500 cursor-pointer select-none">
          🔒 비밀글 — 작성자와 관리자만 열람 가능
        </label>
      </div>
      @endif

      {{-- TipTap 에디터 (공통 컴포넌트) --}}
      <x-tiptap-editor :value="old('content', $board->content ?? '')" />

    </div>

    {{-- 안내 팁 --}}
    <div class="mt-3 flex gap-2 items-start text-xs text-gray-500 rounded px-4 py-3
                bg-amber-50 border border-amber-200/80">
      <span class="text-amber-500 mt-0.5 flex-shrink-0 text-sm">💡</span>
      <span>이미지는 장당 최대 <strong>10MB</strong>, 게시물당 최대 <strong>5장</strong>까지 첨부 가능합니다.
            하단 슬롯에 이미지를 추가한 후 <strong>[본문삽입]</strong>을 클릭하면 커서 위치에 이미지가 삽입됩니다.</span>
    </div>

    {{-- 액션 바 --}}
    <div class="flex items-center justify-between mt-5">
      <a href="{{ route('boards.show', [$type, $board]) }}"
         class="font-body text-sm text-gray-500 border border-gray-300 rounded
                px-5 py-2.5 hover:bg-gray-100 transition-colors">
        취소
      </a>
      <button type="submit"
              class="font-display text-base uppercase tracking-widest
                     bg-pac-black-900 text-pac-yellow-500 rounded
                     px-7 py-2.5 hover:bg-[#2a2020] transition-colors">
        수정 완료
      </button>
    </div>

  </form>

</div>
</x-app-layout>
