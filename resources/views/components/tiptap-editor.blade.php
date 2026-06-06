@props([
    'name'        => 'content',
    'value'       => '',
    'placeholder' => '내용을 입력하세요...',
    'theme'       => 'v1',     {{-- v1 = pac-yellow editorial | v2 = pac-pink energy --}}
    'maxImages'   => 5,
])
@php
$v2  = ($theme === 'v2');
$act = $v2 ? 'text-pac-pink-400 bg-[#1a1a1a]'           : 'text-pac-yellow-400 bg-pac-black-700';
$ina = $v2 ? 'text-[#666] hover:text-white'              : 'text-pac-black-500 hover:text-pac-black-200';
$tBg = $v2 ? 'bg-[#0d0d0d] border-[#2a2a2a]'            : 'bg-pac-black-800 border-pac-black-100';
$eBg = $v2 ? 'bg-[#0d0d0d] border-[#2a2a2a] text-white' : 'bg-pac-black-800 border-pac-black-100 text-pac-black-200';
$fBd = $v2 ? 'focus-within:border-pac-pink-500'          : 'focus-within:border-pac-yellow-500';
$div = $v2 ? 'bg-[#2a2a2a]'                              : 'bg-pac-black-700';
$acc = $v2 ? 'text-pac-pink-400'                         : 'text-pac-yellow-500';
$cnt = $v2 ? 'text-[#444]'                               : 'text-pac-black-600';
$slt = $v2 ? 'border-[#333] text-[#555] hover:border-pac-pink-500 hover:text-pac-pink-400'
           : 'border-pac-black-700 text-pac-black-600 hover:border-pac-yellow-500 hover:text-pac-yellow-500';
@endphp

<div x-data="tiptap({
    content:     @js($value),
    placeholder: @js($placeholder),
    maxImages:   {{ (int)$maxImages }},
    uploadUrl:   @js(route('boards.images.upload')),
})">

  {{-- ── 툴바 ───────────────────────────────────────────────────── --}}
  <div class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 {{ $tBg }} border border-b-0">

    {{-- 텍스트 서식 --}}
    <button type="button" @click="toggleBold()"
            :class="formats.bold ? '{{ $act }}' : '{{ $ina }}'"
            class="w-7 h-7 flex items-center justify-center font-display font-bold text-sm transition-colors rounded" title="굵게">B</button>
    <button type="button" @click="toggleItalic()"
            :class="formats.italic ? '{{ $act }}' : '{{ $ina }}'"
            class="w-7 h-7 flex items-center justify-center font-display italic text-sm transition-colors rounded" title="기울임">I</button>
    <button type="button" @click="toggleUnderline()"
            :class="formats.underline ? '{{ $act }}' : '{{ $ina }}'"
            class="w-7 h-7 flex items-center justify-center font-display underline text-sm transition-colors rounded" title="밑줄">U</button>
    <button type="button" @click="toggleStrike()"
            :class="formats.strike ? '{{ $act }}' : '{{ $ina }}'"
            class="w-7 h-7 flex items-center justify-center font-display line-through text-sm transition-colors rounded" title="취소선">S</button>

    <span class="w-px h-4 self-center {{ $div }} mx-1"></span>

    {{-- 제목 --}}
    <button type="button" @click="toggleH2()"
            :class="formats.h2 ? '{{ $act }}' : '{{ $ina }}'"
            class="h-7 px-1.5 flex items-center justify-center font-display font-bold text-[11px] tracking-wider transition-colors rounded" title="제목2">H2</button>
    <button type="button" @click="toggleH3()"
            :class="formats.h3 ? '{{ $act }}' : '{{ $ina }}'"
            class="h-7 px-1.5 flex items-center justify-center font-display font-bold text-[11px] tracking-wider transition-colors rounded" title="제목3">H3</button>

    <span class="w-px h-4 self-center {{ $div }} mx-1"></span>

    {{-- 목록 · 인용 · 구분선 --}}
    <button type="button" @click="toggleBulletList()"
            :class="formats.bulletList ? '{{ $act }}' : '{{ $ina }}'"
            class="w-7 h-7 flex items-center justify-center transition-colors rounded" title="목록">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
    <button type="button" @click="toggleOrderedList()"
            :class="formats.orderedList ? '{{ $act }}' : '{{ $ina }}'"
            class="w-7 h-7 flex items-center justify-center transition-colors rounded" title="번호목록">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h13M7 12h13M7 16h13M3 8h.01M3 12h.01M3 16h.01"/>
      </svg>
    </button>
    <button type="button" @click="toggleBlockquote()"
            :class="formats.blockquote ? '{{ $act }}' : '{{ $ina }}'"
            class="w-7 h-7 flex items-center justify-center transition-colors rounded" title="인용">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
      </svg>
    </button>
    <button type="button" @click="insertHR()"
            class="w-7 h-7 flex items-center justify-center {{ $ina }} transition-colors rounded" title="구분선">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
      </svg>
    </button>

  </div>

  {{-- ── 에디터 본체 ─────────────────────────────────────────────── --}}
  <div x-ref="editorEl"
       class="tiptap-editor min-h-[240px] {{ $eBg }} border {{ $fBd }} px-4 py-3 transition-colors font-body text-sm">
  </div>

  {{-- 폼 제출용 숨김 textarea --}}
  <textarea name="{{ $name }}" class="hidden" x-model="content"></textarea>

  {{-- ── 이미지 첨부 슬롯 ──────────────────────────────────────── --}}
  @if($maxImages > 0)
  <div class="{{ $tBg }} border border-t-0 px-3 py-2">
    <div class="flex items-center gap-2 flex-wrap min-h-[3rem]">

      <span class="font-display text-[9px] tracking-[2px] uppercase {{ $acc }} flex-shrink-0 mr-1">이미지 첨부</span>

      {{-- 업로드된 슬롯 --}}
      <template x-for="(img, i) in images" :key="i">
        <div class="relative group w-11 h-11 flex-shrink-0">
          <img :src="img.previewUrl" class="w-11 h-11 object-cover block">
          {{-- 업로딩 스피너 --}}
          <div x-show="img.uploading"
               class="absolute inset-0 bg-black/70 flex items-center justify-center">
            <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
          </div>
          {{-- 호버 액션 --}}
          <div class="absolute inset-0 bg-black/55 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-0.5">
            <button type="button" @click="insertImageToEditor(i)"
                    :disabled="img.uploading"
                    class="text-[8px] {{ $acc }} font-display tracking-[1px] uppercase hover:text-white transition-colors leading-tight disabled:opacity-50">
              본문삽입
            </button>
            <button type="button" @click="removeImage(i)"
                    class="text-[8px] text-red-400 font-display tracking-[1px] uppercase hover:text-red-200 transition-colors leading-tight">
              삭제
            </button>
          </div>
        </div>
      </template>

      {{-- 추가 버튼 --}}
      <button type="button"
              x-show="images.length < maxImages"
              @click="$refs.fileInput.click()"
              class="w-11 h-11 flex-shrink-0 flex flex-col items-center justify-center border border-dashed {{ $slt }} transition-colors">
        <span class="text-base leading-none">+</span>
        <span class="text-[8px] font-display tracking-[1px]">추가</span>
      </button>

      <span class="ml-auto font-body text-[10px] {{ $cnt }} flex-shrink-0">
        <span x-text="images.length"></span>/{{ $maxImages }}장
      </span>
    </div>

    <input type="file" x-ref="fileInput" class="hidden"
           accept="image/jpeg,image/png,image/webp,image/gif"
           multiple
           @change="addImages($event.target.files); $event.target.value = ''">
  </div>
  @endif

  {{-- ── 글자수 --}}
  <div class="flex justify-end px-3 py-1 {{ $tBg }} border border-t-0">
    <span class="font-body text-[10px] {{ $cnt }}"><span x-text="charCount"></span>자</span>
  </div>

  @if($errors->has($name))
    <p class="font-body text-xs text-pac-pink-500 mt-1">{{ $errors->first($name) }}</p>
  @endif

</div>
