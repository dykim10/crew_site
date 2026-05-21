<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">GALLERY</p>
        <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">포토 갤러리</h1>
    </div>

    {{-- 그리드 --}}
    @if($galleries->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm px-5 py-16 text-center">
            <p class="font-display text-3xl font-bold text-pac-black-100 uppercase tracking-widest mb-3">NO PHOTOS</p>
            <p class="font-body text-sm text-pac-black-400">등록된 사진이 없습니다.</p>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($galleries as $gallery)
                <a href="{{ route('photos.show', $gallery) }}"
                   class="group bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">

                    {{-- 썸네일 --}}
                    <div class="aspect-square bg-pac-black-100 overflow-hidden">
                        @if($gallery->cover_image_url)
                            <img src="{{ $gallery->cover_image_url }}"
                                 alt="{{ $gallery->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-pac-black-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- 정보 --}}
                    <div class="p-3">
                        <p class="font-display text-[10px] font-bold text-pac-yellow-500 uppercase tracking-widest mb-0.5">
                            {{ $gallery->session_number }}회차
                        </p>
                        <p class="font-body text-sm font-semibold text-pac-black-900 truncate group-hover:text-pac-yellow-600 transition-colors duration-150">
                            {{ $gallery->title }}
                        </p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="font-body text-xs text-pac-black-400">
                                {{ $gallery->taken_at->format('Y.m.d') }}
                            </span>
                            @if(count($gallery->photo_urls ?? []) > 0)
                                <span class="font-body text-xs text-pac-black-400">
                                    {{ count($gallery->photo_urls) }}장
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($galleries->hasPages())
            <div>{{ $galleries->links() }}</div>
        @endif
    @endif

</div>
</x-app-layout>
