@props([
    'name'        => 'content',
    'value'       => '',
    'placeholder' => '내용을 입력하세요...',
    'maxImages'   => 5,
])

{{--
  TipTap 에디터 공통 컴포넌트 (자유게시판 / 문의게시판 공용)
  ※ 툴바 버튼 @mousedown.prevent 필수 —
    @click 사용 시 에디터 포커스 소실 → ProseMirror
    "Applying a mismatched transaction" 오류 발생
--}}
<div x-data="tiptap({
    content:     @js($value),
    placeholder: @js($placeholder),
    maxImages:   {{ (int)$maxImages }},
    uploadUrl:   @js(route('boards.images.upload')),
})">

  {{-- ── 툴바 ──────────────────────────────────────────── --}}
  <div class="flex flex-wrap items-center gap-0.5 px-2 py-1.5
              bg-pac-black-800 border-b border-pac-black-100/50">

    {{-- 서식 --}}
    <div class="flex items-center">
      <button type="button" @mousedown.prevent="toggleBold()"
              :class="formats.bold ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="w-8 h-8 flex items-center justify-center font-display font-bold text-sm rounded transition-colors"
              title="굵게">B</button>
      <button type="button" @mousedown.prevent="toggleItalic()"
              :class="formats.italic ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="w-8 h-8 flex items-center justify-center font-display italic text-sm rounded transition-colors"
              title="기울임">I</button>
      <button type="button" @mousedown.prevent="toggleUnderline()"
              :class="formats.underline ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="w-8 h-8 flex items-center justify-center font-display underline text-sm rounded transition-colors"
              title="밑줄">U</button>
      <button type="button" @mousedown.prevent="toggleStrike()"
              :class="formats.strike ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="w-8 h-8 flex items-center justify-center font-display line-through text-sm rounded transition-colors"
              title="취소선">S</button>
    </div>

    <span class="w-px h-4 self-center bg-pac-black-700 mx-1.5 flex-shrink-0"></span>

    {{-- 제목 --}}
    <div class="flex items-center">
      <button type="button" @mousedown.prevent="toggleH2()"
              :class="formats.h2 ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="h-8 px-2 flex items-center justify-center font-display font-bold text-[11px] tracking-wider rounded transition-colors"
              title="제목2">H2</button>
      <button type="button" @mousedown.prevent="toggleH3()"
              :class="formats.h3 ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="h-8 px-2 flex items-center justify-center font-display font-bold text-[11px] tracking-wider rounded transition-colors"
              title="제목3">H3</button>
    </div>

    <span class="w-px h-4 self-center bg-pac-black-700 mx-1.5 flex-shrink-0"></span>

    {{-- 목록 · 인용 · 구분선 --}}
    <div class="flex items-center">
      <button type="button" @mousedown.prevent="toggleBulletList()"
              :class="formats.bulletList ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="w-8 h-8 flex items-center justify-center rounded transition-colors" title="목록">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <button type="button" @mousedown.prevent="toggleOrderedList()"
              :class="formats.orderedList ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="w-8 h-8 flex items-center justify-center rounded transition-colors" title="번호목록">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h13M7 12h13M7 16h13M3 8h.01M3 12h.01M3 16h.01"/>
        </svg>
      </button>
      <button type="button" @mousedown.prevent="toggleBlockquote()"
              :class="formats.blockquote ? 'bg-pac-black-700 text-pac-yellow-400' : 'text-pac-black-500 hover:text-pac-black-200 hover:bg-pac-black-700'"
              class="w-8 h-8 flex items-center justify-center rounded transition-colors" title="인용">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
        </svg>
      </button>
      <button type="button" @mousedown.prevent="insertHR()"
              class="w-8 h-8 flex items-center justify-center text-pac-black-500
                     hover:text-pac-black-200 hover:bg-pac-black-700 rounded transition-colors"
              title="구분선">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
        </svg>
      </button>
    </div>

    {{-- 이미지 첨부 --}}
    @if($maxImages > 0)
    <span class="w-px h-4 self-center bg-pac-black-700 mx-1.5 flex-shrink-0 hidden sm:block"></span>
    <button type="button"
            @mousedown.prevent="$refs.fileInput.click()"
            class="h-8 px-3 flex items-center gap-1.5 font-display text-[10px] tracking-[1px] uppercase
                   text-pac-black-500 border border-pac-black-100/60 rounded transition-colors
                   hover:border-pac-yellow-500 hover:text-pac-yellow-500">
      <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      <span>이미지</span>
    </button>
    @endif

  </div>

  {{-- ── 에디터 본체 ──────────────────────────────────────── --}}
  <div x-ref="editorEl"
       class="tiptap-editor min-h-[420px] bg-pac-black-800 px-5 py-4
              font-body text-sm text-pac-black-200 focus-within:ring-1
              focus-within:ring-pac-yellow-500/30 transition-all">
  </div>

  {{-- 폼 제출용 숨김 textarea --}}
  <textarea name="{{ $name }}" class="hidden" x-model="content"></textarea>

  {{-- ── 이미지 첨부 슬롯 ───────────────────────────────── --}}
  @if($maxImages > 0)
  <div class="border-t border-pac-black-100/50 bg-pac-black-900 px-4 py-3">
    <div class="flex items-center gap-2 flex-wrap min-h-[3.25rem]">

      <span class="font-display text-[9px] tracking-[2px] uppercase text-pac-yellow-600 flex-shrink-0 mr-1">
        이미지 첨부
      </span>

      {{-- 업로드된 슬롯 --}}
      <template x-for="(img, i) in images" :key="i">
        <div class="relative group flex-shrink-0 border border-pac-yellow-500/40"
             style="width:52px;height:52px;">
          <img :src="img.previewUrl" class="w-full h-full object-cover block">
          {{-- 업로딩 스피너 --}}
          <div x-show="img.uploading"
               class="absolute inset-0 bg-black/70 flex items-center justify-center">
            <svg class="w-4 h-4 animate-spin text-pac-yellow-400" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
          </div>
          {{-- 호버 액션 --}}
          <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity
                      flex flex-col items-center justify-center gap-1">
            <button type="button" @click="insertImageToEditor(i)"
                    :disabled="img.uploading"
                    class="font-display text-[9px] tracking-[1px] uppercase text-pac-yellow-400
                           hover:text-white transition-colors leading-none disabled:opacity-50">
              본문삽입
            </button>
            <button type="button" @click="removeImage(i)"
                    class="font-display text-[9px] tracking-[1px] uppercase text-pac-black-500
                           hover:text-red-400 transition-colors leading-none">
              삭제
            </button>
          </div>
        </div>
      </template>

      {{-- 추가 버튼 --}}
      <button type="button"
              x-show="images.length < maxImages"
              @click="$refs.fileInput.click()"
              style="width:52px;height:52px;"
              class="flex-shrink-0 flex flex-col items-center justify-center
                     border border-dashed border-pac-black-700 text-pac-black-600
                     hover:border-pac-yellow-500 hover:text-pac-yellow-500 transition-colors">
        <span class="text-xl leading-none font-light">+</span>
        <span class="font-display text-[8px] tracking-[1px] uppercase mt-0.5">추가</span>
      </button>

      <span class="ml-auto font-body text-[10px] text-pac-black-600 flex-shrink-0">
        <span x-text="images.length"></span>/{{ $maxImages }}장
      </span>
    </div>

    <input type="file" x-ref="fileInput" class="hidden"
           accept="image/jpeg,image/png,image/webp,image/gif"
           multiple
           @change="addImages($event.target.files); $event.target.value = ''">
  </div>
  @endif

  {{-- ── 글자수 ─────────────────────────────────────────── --}}
  <div class="border-t border-pac-black-100/30 px-4 py-1.5 text-right bg-pac-black-900">
    <span class="font-body text-[10px] text-pac-black-700">
      본문 <span x-text="charCount"></span>자
    </span>
  </div>

  @if($errors->has($name))
    <p class="font-body text-xs text-pac-pink-500 mt-1 px-1">{{ $errors->first($name) }}</p>
  @endif

</div>
