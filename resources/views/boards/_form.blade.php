{{-- 게시판 공통 폼 파셜 — create / edit 에서 @include --}}
@php
  $isEdit  = isset($board);
  $isPhoto = ($type === 'photo');
  $action  = $isEdit
    ? route('boards.update',  [$type, $board])
    : route('boards.store',   $type);
@endphp

<form method="POST" action="{{ $action }}"
      enctype="multipart/form-data"
      class="space-y-5">
  @csrf
  @if($isEdit) @method('PUT') @endif

  {{-- 제목 --}}
  <div>
    <label class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-500 mb-2 block">
      제목 <span class="text-pac-pink-500">*</span>
    </label>
    <input type="text"
           name="title"
           value="{{ old('title', $board->title ?? '') }}"
           placeholder="제목을 입력하세요"
           class="w-full bg-pac-black-800 border border-pac-black-100 focus:border-pac-yellow-500
                  text-pac-black-200 placeholder:text-pac-black-700
                  font-body text-sm px-4 py-3 outline-none transition-colors">
    @error('title')
      <p class="font-body text-xs text-pac-pink-500 mt-1">{{ $message }}</p>
    @enderror
  </div>

  {{-- 포토게시판: 이미지 업로드 --}}
  @if($isPhoto)
  <div>
    <label class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-500 mb-2 block">
      대표 이미지 (JPG/PNG, 최대 5MB)
    </label>

    {{-- 현재 이미지 미리보기 (수정 시) --}}
    @if($isEdit && $board->thumbnail_url)
    <div class="mb-3">
      <img src="{{ $board->thumbnail_url }}" alt="현재 이미지"
           class="h-32 object-cover border border-pac-black-100">
      <p class="font-body text-xs text-pac-black-600 mt-1">현재 이미지 — 새 파일 선택 시 교체됩니다</p>
    </div>
    @endif

    <label class="flex flex-col items-center justify-center w-full h-32 cursor-pointer
                  border-2 border-dashed border-pac-black-100 hover:border-pac-yellow-500
                  bg-pac-black-900 transition-colors group">
      <svg class="w-8 h-8 text-pac-black-600 group-hover:text-pac-yellow-500 transition-colors mb-2"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      <span class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600
                   group-hover:text-pac-yellow-500 transition-colors" id="file-label">
        클릭하여 이미지 선택
      </span>
      <input type="file" name="thumbnail" accept="image/*" class="hidden"
             onchange="document.getElementById('file-label').textContent = this.files[0]?.name ?? '클릭하여 이미지 선택'">
    </label>
    @error('thumbnail')
      <p class="font-body text-xs text-pac-pink-500 mt-1">{{ $message }}</p>
    @enderror
  </div>
  @endif

  {{-- 본문 --}}
  <div>
    <label class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-500 mb-2 block">
      내용 @if($type !== 'photo') <span class="text-pac-pink-500">*</span> @endif
    </label>
    <textarea name="content"
              rows="{{ $isPhoto ? 5 : 14 }}"
              placeholder="{{ $isPhoto ? '사진 설명을 입력하세요 (선택)' : '내용을 입력하세요' }}"
              class="w-full bg-pac-black-800 border border-pac-black-100 focus:border-pac-yellow-500
                     text-pac-black-200 placeholder:text-pac-black-700
                     font-body text-sm px-4 py-3 outline-none resize-y transition-colors">{{ old('content', $board->content ?? '') }}</textarea>
    @error('content')
      <p class="font-body text-xs text-pac-pink-500 mt-1">{{ $message }}</p>
    @enderror
  </div>

  {{-- 버튼 --}}
  <div class="flex items-center justify-between pt-2">
    <a href="{{ $isEdit ? route('boards.show', [$type, $board]) : route('boards.index', $type) }}"
       class="pac-btn-ghost">취소</a>
    <button type="submit" class="pac-btn">
      {{ $isEdit ? '수정 완료' : '등록하기' }}
    </button>
  </div>

</form>
