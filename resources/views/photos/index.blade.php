<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 페이지 헤더 --}}
  <div class="flex items-end justify-between mb-8">
    <div>
      <p class="font-display text-[10px] tracking-[4px] uppercase text-pac-yellow-500 mb-2">Community</p>
      <h1 class="font-display text-4xl lg:text-5xl uppercase tracking-tight text-white leading-none">
        Photo
      </h1>
      <p class="font-body text-sm text-pac-black-400 mt-2">PAC-RUN 활동 사진 모음</p>
    </div>
  </div>

  @if($galleries->isEmpty())
    <div class="border border-pac-black-100 bg-pac-black-900 py-24 text-center">
      <p class="font-display text-4xl font-black text-pac-black-700 uppercase tracking-widest mb-3">NO PHOTOS</p>
      <p class="font-body text-sm text-pac-black-600">등록된 사진이 없습니다.</p>
    </div>
  @else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
      @foreach($galleries as $gallery)
        <a href="{{ route('photos.show', $gallery) }}"
           class="group relative overflow-hidden bg-pac-black-900 border border-pac-black-100
                  hover:border-pac-yellow-500/50 transition-colors duration-200"
           style="aspect-ratio:1;">

          {{-- 썸네일 --}}
          @if($gallery->thumbnail_url ?? $gallery->image_url)
            <img src="{{ $gallery->thumbnail_url ?? $gallery->image_url }}"
                 alt="{{ $gallery->title }}"
                 loading="lazy"
                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          @else
            <div class="absolute inset-0 flex items-center justify-center"
                 style="background:linear-gradient(135deg,#2d1a00,#0d0d0d);">
              <svg class="w-10 h-10 text-pac-yellow-500 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          @endif

          {{-- 호버 오버레이 --}}
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent
                      opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <div class="absolute bottom-0 left-0 right-0 p-3">
              <p class="font-body text-xs font-semibold text-white line-clamp-2 leading-tight">
                {{ $gallery->title }}
              </p>
              @if($gallery->taken_at)
                <p class="font-display text-[9px] tracking-wider uppercase text-white/50 mt-1">
                  {{ $gallery->taken_at->format('Y.m.d') }}
                </p>
              @endif
            </div>
          </div>

          {{-- Google Photos 배지 --}}
          @if($gallery->google_photos_url)
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <span class="font-display text-[8px] tracking-wider uppercase bg-pac-yellow-500 text-pac-black-900 px-1.5 py-0.5">
                앨범
              </span>
            </div>
          @endif

        </a>
      @endforeach
    </div>

    {{-- 페이지네이션 --}}
    @if($galleries->hasPages())
      <div class="flex justify-center mt-8">{{ $galleries->links() }}</div>
    @endif
  @endif

</div>
</x-app-layout>
