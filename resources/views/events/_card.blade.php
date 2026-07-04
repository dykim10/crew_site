@php
  $thumb   = $event->thumbnail_url
    ? (str_starts_with($event->thumbnail_url, 'http')
        ? $event->thumbnail_url
        : \Storage::disk('s3')->url($event->thumbnail_url))
    : null;
  $isEnded = $event->status === 'ended';
@endphp

<a href="{{ route('events.show', $event) }}"
   class="group flex flex-col bg-white rounded-2xl overflow-hidden shadow-sm border border-pac-black-100
          hover:shadow-md hover:-translate-y-1 transition-all duration-200 {{ $isEnded ? 'opacity-70' : '' }}">

  {{-- 썸네일 --}}
  <x-event-thumbnail :url="$thumb" :alt="$event->name" class="h-44 flex-shrink-0">
    @unless($thumb)
      {{-- 플레이스홀더: 그라디언트 + 이벤트명 --}}
      <div class="absolute inset-0 flex items-end p-4 z-0"
           style="background: linear-gradient(135deg,#2d1a00 0%,#1a1212 100%);">
        <span class="font-display text-white text-lg font-bold uppercase leading-tight opacity-30 tracking-widest">
          EVENT
        </span>
      </div>
    @endunless

    {{-- 상태 배지 --}}
    <div class="absolute top-3 left-3 z-[2]">
      @if($event->status === 'active')
        <span class="inline-flex items-center gap-1.5 bg-pac-pink-500 text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full">
          <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> LIVE
        </span>
      @elseif($event->status === 'upcoming')
        <span class="bg-pac-yellow-500 text-pac-black-900 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full">
          예정
        </span>
      @else
        <span class="bg-black/50 text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full backdrop-blur-sm">
          종료
        </span>
      @endif
    </div>

    {{-- 신청자 수 --}}
    <div class="absolute bottom-3 right-3 z-[2]">
      <span class="bg-black/60 text-white text-[10px] font-bold px-2 py-1 rounded-full backdrop-blur-sm">
        {{ $event->registrations_count ?? $event->registrations()->count() }}명 참가
      </span>
    </div>
  </x-event-thumbnail>

  {{-- 정보 --}}
  <div class="flex-1 flex flex-col p-4 gap-2">
    <div>
      <p class="font-display text-[10px] font-bold text-pac-yellow-500 uppercase tracking-widest mb-1">
        B TYPE · {{ $event->target_scope === 'all' ? '전체 대상' : ($event->target_scope === 'generation' ? $event->generation.'기' : '지부') }}
      </p>
      <h3 class="font-body text-sm font-semibold text-pac-black-900 leading-snug line-clamp-2
                 group-hover:text-pac-yellow-600 transition-colors duration-150">
        {{ $event->name }}
      </h3>
    </div>

    <div class="mt-auto space-y-1 pt-2 border-t border-pac-black-100">
      @if($event->location)
      <p class="font-body text-xs text-pac-black-400 flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ $event->location }}
      </p>
      @endif
      <p class="font-body text-xs text-pac-black-400 flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ $event->start_date->format('Y.m.d') }}
        @if($event->start_date->ne($event->end_date)) ~ {{ $event->end_date->format('Y.m.d') }} @endif
      </p>
    </div>
  </div>
</a>
