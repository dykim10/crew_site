<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-6">

    {{-- 헤더 --}}
    <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">EVENTS</p>
        <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">이벤트</h1>
    </div>

    {{-- 진행 중 --}}
    @if($active->isNotEmpty())
    <section>
        <h2 class="font-display text-xs font-bold text-pac-yellow-500 uppercase tracking-widest mb-3">진행 중</h2>
        <div class="space-y-3">
            @foreach($active as $event)
            <a href="{{ $event->isTypeB() ? route('events.show', $event) : '#' }}"
               class="flex items-center gap-4 bg-white rounded-2xl px-5 py-4 shadow-sm
                      hover:shadow-md transition-shadow duration-200 group">
                <div class="w-10 h-10 rounded-xl bg-pac-yellow-500/15 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="font-display text-[10px] font-bold uppercase tracking-widest
                                     {{ $event->event_type === 'A' ? 'text-pac-pink-500' : 'text-pac-yellow-500' }}">
                            {{ $event->event_type === 'A' ? 'A타입' : 'B타입' }}
                        </span>
                        <span class="font-display text-[10px] font-bold text-green-500 uppercase tracking-widest">LIVE</span>
                    </div>
                    <p class="font-body text-sm font-semibold text-pac-black-900 truncate group-hover:text-pac-yellow-600 transition-colors duration-150">
                        {{ $event->name }}
                    </p>
                    <p class="font-body text-xs text-pac-black-400 mt-0.5">
                        {{ $event->start_date->format('Y.m.d') }} – {{ $event->end_date->format('Y.m.d') }}
                    </p>
                </div>
                <svg class="w-4 h-4 text-pac-black-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 예정 --}}
    @if($upcoming->isNotEmpty())
    <section>
        <h2 class="font-display text-xs font-bold text-pac-black-400 uppercase tracking-widest mb-3">예정</h2>
        <div class="space-y-3">
            @foreach($upcoming as $event)
            <div class="flex items-center gap-4 bg-white rounded-2xl px-5 py-4 shadow-sm opacity-80">
                <div class="w-10 h-10 rounded-xl bg-pac-black-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-pac-black-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest">예정</span>
                    </div>
                    <p class="font-body text-sm font-semibold text-pac-black-700 truncate">{{ $event->name }}</p>
                    <p class="font-body text-xs text-pac-black-400 mt-0.5">
                        {{ $event->start_date->format('Y.m.d') }} – {{ $event->end_date->format('Y.m.d') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 종료 --}}
    @if($ended->isNotEmpty())
    <section>
        <h2 class="font-display text-xs font-bold text-pac-black-300 uppercase tracking-widest mb-3">종료</h2>
        <div class="space-y-2">
            @foreach($ended as $event)
            <div class="flex items-center gap-4 bg-pac-black-50 rounded-xl px-5 py-3 opacity-60">
                <div class="flex-1 min-w-0">
                    <p class="font-body text-sm text-pac-black-600 truncate">{{ $event->name }}</p>
                    <p class="font-body text-xs text-pac-black-400 mt-0.5">
                        {{ $event->start_date->format('Y.m.d') }} – {{ $event->end_date->format('Y.m.d') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    @if($active->isEmpty() && $upcoming->isEmpty() && $ended->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm px-5 py-16 text-center">
        <p class="font-display text-3xl font-bold text-pac-black-100 uppercase tracking-widest mb-3">NO EVENTS</p>
        <p class="font-body text-sm text-pac-black-400">등록된 이벤트가 없습니다.</p>
    </div>
    @endif

</div>
</x-app-layout>
