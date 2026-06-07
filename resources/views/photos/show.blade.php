<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 브레드크럼 --}}
  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('photos.index') }}"
       class="font-body text-xs text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
      Photo
    </a>
    <span class="text-pac-black-600 text-xs">›</span>
    <span class="font-body text-xs text-pac-black-500 truncate max-w-[200px]">{{ $gallery->title }}</span>
  </div>

  <article class="bg-pac-black-900 border border-pac-black-100">

    {{-- 대표 이미지 --}}
    @if($gallery->image_url)
      <div class="border-b border-pac-black-100 bg-pac-black-800">
        <img src="{{ $gallery->image_url }}"
             alt="{{ $gallery->title }}"
             class="w-full max-h-[560px] object-contain">
      </div>
    @endif

    {{-- 헤더 --}}
    <div class="px-6 pt-5 pb-4 border-b border-pac-black-100">
      <h1 class="font-body text-xl font-bold text-white leading-snug mb-3">
        {{ $gallery->title }}
      </h1>
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-4">
          @if($gallery->taken_at)
            <span class="font-body text-xs text-pac-black-500">
              {{ $gallery->taken_at->format('Y년 m월 d일') }}
            </span>
          @endif
          @if($gallery->branch)
            <span class="font-display text-[9px] tracking-wider uppercase
                         border border-pac-yellow-500/30 text-pac-yellow-600 px-2 py-0.5">
              {{ $gallery->branch->name }}
            </span>
          @endif
        </div>
        <span class="font-body text-xs text-pac-black-600">
          조회 {{ number_format($gallery->view_count) }}
        </span>
      </div>
    </div>

    {{-- 설명 --}}
    @if($gallery->description)
      <div class="px-6 py-5 border-b border-pac-black-100">
        <p class="font-body text-sm text-pac-black-400 leading-relaxed whitespace-pre-wrap">
          {{ $gallery->description }}
        </p>
      </div>
    @endif

    {{-- Google Photos 앨범 링크 --}}
    @if($gallery->google_photos_url)
      <div class="px-6 py-5">
        <a href="{{ $gallery->google_photos_url }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-2 px-5 py-2.5
                  bg-pac-yellow-500 hover:bg-pac-yellow-400
                  text-pac-black-900 font-display font-black text-sm uppercase tracking-wider
                  transition-colors duration-150">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
          </svg>
          Google 앨범 전체 보기
        </a>
      </div>
    @endif

  </article>

  {{-- 하단 네비 --}}
  <div class="flex items-center justify-between mt-6">
    <a href="{{ route('photos.index') }}" class="pac-btn-ghost text-sm">← 목록으로</a>
  </div>

</div>
</x-app-layout>
