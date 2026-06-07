<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 브레드크럼 --}}
  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('boards.index', $type) }}"
       class="font-body text-xs text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
      {{ $meta['label'] }}
    </a>
    <span class="text-pac-black-600 text-xs">›</span>
    <a href="{{ route('boards.show', [$type, $board]) }}"
       class="font-body text-xs text-pac-black-500 hover:text-pac-black-300 transition-colors truncate max-w-[180px]">
      {{ Str::limit($board->title, 20) }}
    </a>
    <span class="text-pac-black-600 text-xs">›</span>
    <span class="font-body text-xs text-pac-black-500">수정</span>
  </div>

  <div class="bg-pac-black-900 border border-white/8 relative overflow-hidden"
       x-data="{ loading: false }">

    {{-- 로딩 바 --}}
    <div x-show="loading" x-cloak
         class="absolute top-0 left-0 right-0 h-0.5 bg-pac-black-700 overflow-hidden z-10">
      <div class="absolute inset-y-0 pac-loading-bar bg-pac-yellow-500"></div>
    </div>

    {{-- 카드 헤더 --}}
    <div class="px-6 py-4 border-b border-white/8">
      <h1 class="font-display text-xl uppercase tracking-wider text-white">
        게시글 수정 <span class="text-pac-yellow-500">— {{ $meta['label'] }}</span>
      </h1>
    </div>

    <form method="POST" action="{{ route('boards.update', [$type, $board]) }}" class="p-6 space-y-5"
          @submit="loading = true">
      @csrf
      @method('PUT')

      {{-- 제목 --}}
      <div>
        <label class="font-body text-xs text-pac-black-400 mb-2 block">
          제목 <span class="text-pac-pink-400">*</span>
        </label>
        <input type="text" name="title"
               value="{{ old('title', $board->title) }}"
               placeholder="제목을 입력하세요"
               required maxlength="200"
               class="w-full bg-pac-black-800 border border-white/10 focus:border-pac-yellow-500
                      text-white placeholder:text-pac-black-600
                      font-body text-sm px-4 py-3 outline-none transition-colors">
        @error('title')
          <p class="font-body text-xs text-pac-pink-400 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- 문의게시판: 비밀글 --}}
      @if($type === 'qna')
      <div class="flex items-center gap-3 py-2 px-3 bg-pac-black-800 border border-white/8">
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
        <label class="font-body text-xs text-pac-black-400 mb-2 block">
          내용 <span class="text-pac-pink-400">*</span>
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
      <div class="flex items-center justify-between pt-2 border-t border-white/8">
        <a href="{{ route('boards.show', [$type, $board]) }}" class="pac-btn-ghost">취소</a>
        <button type="submit" class="pac-btn"
                :disabled="loading"
                :class="loading ? 'opacity-70 cursor-not-allowed' : ''">
          <span x-show="!loading">수정 완료</span>
          <span x-show="loading" x-cloak class="flex items-center gap-2">
            <svg class="animate-spin h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10"
                      stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            저장 중...
          </span>
        </button>
      </div>

    </form>
  </div>

</div>

<style>
@keyframes pac-slide {
  0%   { left: -60%; width: 60%; }
  100% { left: 120%; width: 60%; }
}
.pac-loading-bar {
  animation: pac-slide 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}
</style>

</x-app-layout>
