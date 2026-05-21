<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('photos.index') }}"
           class="p-2 text-pac-black-400 hover:text-pac-black-700 transition-colors duration-150">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <p class="font-display text-[10px] font-bold text-pac-yellow-500 uppercase tracking-widest mb-0.5">
                {{ $gallery->session_number }}회차
            </p>
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight leading-tight">
                {{ $gallery->title }}
            </h1>
            <p class="font-body text-sm text-pac-black-400 mt-0.5">
                {{ $gallery->taken_at->format('Y년 m월 d일') }}
            </p>
        </div>
    </div>

    {{-- 설명 --}}
    @if($gallery->description)
        <div class="bg-white rounded-2xl shadow-sm px-6 py-4">
            <p class="font-body text-sm text-pac-black-700 whitespace-pre-wrap leading-relaxed">{{ $gallery->description }}</p>
        </div>
    @endif

    {{-- 외부 앨범 링크 --}}
    @if($gallery->album_url)
        <a href="{{ $gallery->album_url }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-2 px-5 py-3 bg-pac-yellow-500 hover:bg-pac-yellow-400
                  text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                  rounded-xl transition-colors duration-150 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            앨범 바로가기
        </a>
    @endif

    {{-- 사진 그리드 --}}
    @if(count($gallery->photo_urls ?? []) > 0)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-pac-black-900 flex items-center justify-between">
                <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">사진</h3>
                <span class="font-body text-xs text-pac-black-400">{{ count($gallery->photo_urls) }}장</span>
            </div>
            <div class="p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach($gallery->photo_urls as $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                       class="group aspect-square rounded-xl overflow-hidden bg-pac-black-100 block">
                        <img src="{{ $url }}"
                             alt="사진"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             onerror="this.parentElement.classList.add('flex','items-center','justify-center'); this.remove();">
                    </a>
                @endforeach
            </div>
        </div>
    @elseif(!$gallery->album_url)
        <div class="bg-white rounded-2xl shadow-sm px-5 py-12 text-center">
            <p class="font-body text-sm text-pac-black-400">등록된 사진이 없습니다.</p>
        </div>
    @endif

</div>
</x-app-layout>
