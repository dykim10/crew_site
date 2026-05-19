<x-app-layout>
<div class="max-w-7xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4 lg:space-y-5">

    {{-- ① 인사 배너 --}}
    <div class="relative bg-pac-black-900 rounded-2xl overflow-hidden">
        {{-- 장식 원 --}}
        <span class="absolute top-0 right-16 w-32 h-32 bg-pac-yellow-500 opacity-[0.07] rounded-full -translate-y-1/2"></span>
        <span class="absolute top-4 right-4 w-16 h-16 bg-pac-pink-500 opacity-[0.12] rounded-full"></span>

        <div class="relative z-10 flex items-center justify-between gap-4 p-5 lg:p-6">
            <div>
                <h2 class="font-display text-xl lg:text-2xl font-bold text-white uppercase tracking-tight">
                    안녕하세요,
                    <span class="text-pac-yellow-400">{{ auth()->user()->nickname ?? auth()->user()->name }}</span> 님!
                </h2>
                @if($stats['monthly_percent'] !== null)
                    @if($stats['monthly_percent'] >= 100)
                        <p class="font-body text-sm text-pac-green-500 mt-1">이번 달 목표를 달성했습니다! 🎉</p>
                    @else
                        <p class="font-body text-sm text-pac-black-300 mt-1">
                            이번 달 목표까지
                            <span class="text-pac-yellow-400 font-semibold">{{ $stats['monthly_percent'] }}%</span>
                            달성했습니다
                        </p>
                    @endif
                @else
                    <p class="font-body text-sm text-pac-black-400 mt-1">오늘도 함께 달려요!</p>
                @endif
            </div>
            <a href="{{ route('running-logs.create') }}"
               class="shrink-0 inline-flex items-center gap-2 px-4 py-3 bg-pac-yellow-500 hover:bg-pac-yellow-600
                      text-pac-black-900 font-body font-bold text-sm rounded-xl
                      transition-colors duration-200 min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                기록 추가
            </a>
        </div>
    </div>

    {{-- ② 수치 카드 4개 --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">

        {{-- 이번 달 거리 --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 lg:p-5 border-l-4 border-pac-yellow-500
                    hover:shadow-md transition-shadow duration-200">
            <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide mb-2">이번 달 거리</p>
            <p class="font-display text-3xl lg:text-4xl font-bold text-pac-black-900 leading-none">
                {{ number_format($stats['monthly_km'], 1) }}
                <span class="font-body text-sm font-normal text-pac-black-400">km</span>
            </p>
            @if($stats['monthly_percent'] !== null)
                <p class="font-body text-xs text-pac-green-500 mt-2">↑ 목표 {{ $stats['monthly_percent'] }}%</p>
            @else
                <p class="font-body text-xs text-pac-black-300 mt-2">{{ $stats['total_count'] }}회 누적</p>
            @endif
        </div>

        {{-- 누적 거리 --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 lg:p-5 border-l-4 border-pac-black-400
                    hover:shadow-md transition-shadow duration-200">
            <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide mb-2">누적 거리</p>
            <p class="font-display text-3xl lg:text-4xl font-bold text-pac-black-900 leading-none">
                {{ number_format($stats['total_km'], 0) }}
                <span class="font-body text-sm font-normal text-pac-black-400">km</span>
            </p>
            <p class="font-body text-xs text-pac-black-400 mt-2">총 {{ $stats['total_count'] }}회 러닝</p>
        </div>

        {{-- 이벤트 점수 --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 lg:p-5 border-l-4 border-pac-pink-500
                    hover:shadow-md transition-shadow duration-200">
            <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide mb-2">이벤트 점수</p>
            @if($stats['has_active_event'])
                <p class="font-display text-3xl lg:text-4xl font-bold text-pac-pink-500 leading-none">
                    {{ $stats['event_score'] }}
                    <span class="font-body text-sm font-normal text-pac-black-400">pts</span>
                </p>
                <p class="font-body text-xs text-pac-black-400 mt-2">진행 중 이벤트</p>
            @else
                <p class="font-display text-3xl lg:text-4xl font-bold text-pac-black-200 leading-none">—</p>
                <p class="font-body text-xs text-pac-black-300 mt-2">진행 중 이벤트 없음</p>
            @endif
        </div>

        {{-- 조 순위 --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 lg:p-5 border-l-4 border-pac-green-500
                    hover:shadow-md transition-shadow duration-200">
            <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide mb-2">조 순위</p>
            @if($stats['group_rank'])
                <p class="font-display text-3xl lg:text-4xl font-bold text-pac-black-900 leading-none">
                    {{ $stats['group_rank'] }}
                    <span class="font-body text-sm font-normal text-pac-black-400">위</span>
                </p>
                <p class="font-body text-xs text-pac-black-400 mt-2">{{ $stats['group_total'] }}명 중</p>
            @else
                <p class="font-display text-3xl lg:text-4xl font-bold text-pac-black-200 leading-none">—</p>
                <p class="font-body text-xs text-pac-black-300 mt-2">조 미배정</p>
            @endif
        </div>

    </div>

    {{-- ③ 크루 공지사항 --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-pac-black-100">
            <h3 class="font-body text-base font-bold text-pac-black-900">크루 공지사항</h3>
            <a href="#" class="font-body text-xs text-pac-yellow-600 hover:text-pac-yellow-700 transition-colors duration-200">
                전체보기 →
            </a>
        </div>

        @forelse($notices as $notice)
            <div class="flex items-start gap-3 px-5 py-3.5 border-b border-pac-black-50 last:border-0
                        hover:bg-pac-black-50 transition-colors duration-200 cursor-pointer">
                @if($notice->is_pinned)
                    <span class="font-display text-xs font-bold uppercase tracking-widest
                                 bg-pac-pink-100 text-pac-pink-700 px-2 py-1 rounded-full shrink-0 mt-0.5">
                        고정
                    </span>
                @else
                    <span class="w-1.5 h-1.5 rounded-full bg-pac-yellow-500 shrink-0 mt-2"></span>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-body text-sm font-semibold text-pac-black-900 hover:text-pac-yellow-600
                               truncate transition-colors duration-200">
                        {{ $notice->title }}
                    </p>
                    <p class="font-body text-xs text-pac-black-400 mt-0.5">
                        {{ $notice->created_at->format('Y.m.d') }} ·
                        {{ $notice->target_type === 'all' ? '전체' : '지역 공지' }}
                    </p>
                </div>
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <p class="font-body text-sm text-pac-black-300">등록된 공지사항이 없습니다.</p>
            </div>
        @endforelse
    </div>

    {{-- ④ 마일리지 진행바 + 이벤트 / ⑤ 최근 기록 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5">

        {{-- ④ 마일리지 진행바 + 이벤트 목록 --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <h3 class="font-body text-base font-bold text-pac-black-900 mb-4">이벤트</h3>

            @if($mileage)
                {{-- 진행바 --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-body text-sm font-semibold text-pac-black-700">
                            {{ $mileage['name'] }}
                        </span>
                        <span class="font-display text-sm font-bold text-pac-yellow-600">
                            {{ number_format($mileage['achieved'], 1) }} / {{ number_format($mileage['target'], 0) }} km
                        </span>
                    </div>
                    <div class="bg-pac-black-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-pac-yellow-400 to-pac-yellow-600
                                    transition-all duration-500"
                             style="width: {{ $mileage['percent'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        @if($mileage['done'])
                            <span class="font-display text-xs font-bold uppercase tracking-widest text-pac-green-500">
                                달성 완료! 🎉
                            </span>
                        @elseif($mileage['percent'] >= 90)
                            <span class="font-display text-xs font-bold uppercase tracking-widest text-pac-pink-500">
                                거의 다 왔어요!
                            </span>
                        @else
                            <span class="font-body text-xs text-pac-black-400">
                                D-{{ $mileage['days_left'] }}
                            </span>
                        @endif
                        <span class="font-display text-xs font-bold text-pac-yellow-600">
                            {{ $mileage['percent'] }}%
                        </span>
                    </div>
                </div>
            @endif

            {{-- 이벤트 목록 --}}
            @forelse($events as $event)
                <div class="flex items-center gap-3 py-3 border-t border-pac-black-50 first:border-0">
                    <span class="w-2 h-2 rounded-full shrink-0
                        {{ now()->between($event->start_date, $event->end_date) ? 'bg-pac-pink-500' : 'bg-pac-yellow-400' }}">
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-body text-sm font-semibold text-pac-black-800 truncate">
                            {{ $event->name }}
                        </p>
                        <p class="font-body text-xs text-pac-black-400 mt-0.5">
                            ~ {{ \Carbon\Carbon::parse($event->end_date)->format('Y.m.d') }}
                        </p>
                    </div>
                    @if(now()->between($event->start_date, $event->end_date))
                        <span class="font-display text-xs font-bold uppercase tracking-widest
                                     bg-pac-pink-100 text-pac-pink-700 px-2.5 py-1 rounded-full shrink-0">
                            LIVE
                        </span>
                    @else
                        <span class="font-display text-xs font-bold uppercase tracking-widest
                                     bg-pac-yellow-100 text-pac-yellow-700 px-2.5 py-1 rounded-full shrink-0">
                            예정
                        </span>
                    @endif
                </div>
            @empty
                <div class="py-8 text-center border-t border-pac-black-50">
                    <p class="font-body text-sm text-pac-black-300">진행 중인 이벤트가 없습니다.</p>
                </div>
            @endforelse
        </div>

        {{-- ⑤ 최근 러닝 기록 --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-body text-base font-bold text-pac-black-900">최근 기록</h3>
                <a href="{{ route('running-logs.index') }}"
                   class="font-body text-xs text-pac-yellow-600 hover:text-pac-yellow-700 transition-colors duration-200">
                    전체 →
                </a>
            </div>

            @forelse($recentLogs as $log)
                <a href="{{ route('running-logs.show', $log) }}"
                   class="flex items-center gap-3 py-3 border-t border-pac-black-50 first:border-0
                          hover:bg-pac-black-50 -mx-5 px-5 transition-colors duration-200">
                    {{-- 날짜 --}}
                    <div class="text-center shrink-0 w-10">
                        <p class="font-display text-xl font-bold text-pac-yellow-500 leading-none">
                            {{ $log->run_date->format('d') }}
                        </p>
                        <p class="font-body text-[9px] text-pac-black-400 uppercase mt-0.5">
                            {{ $log->run_date->format('M') }}
                        </p>
                    </div>
                    {{-- 정보 --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-display text-lg font-bold text-pac-black-900 leading-none">
                            {{ number_format($log->distance_km, 2) }}
                            <span class="font-body text-xs font-normal text-pac-black-400">km</span>
                        </p>
                        <p class="font-body text-xs text-pac-black-400 mt-0.5 truncate">
                            {{ $log->avg_pace_formatted ? $log->avg_pace_formatted . '/km · ' : '' }}{{ $log->duration_formatted }}
                        </p>
                    </div>
                    {{-- 태그 --}}
                    <span class="font-display text-[10px] font-bold uppercase tracking-widest
                                 bg-pac-black-100 text-pac-black-400 px-2.5 py-1 rounded-full shrink-0">
                        {{ $log->is_indoor ? '실내' : '야외' }}
                    </span>
                </a>
            @empty
                <div class="py-8 text-center border-t border-pac-black-50">
                    <p class="font-body text-sm text-pac-black-400 mb-4">아직 기록이 없어요.</p>
                    <a href="{{ route('running-logs.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5
                              bg-pac-yellow-500 hover:bg-pac-yellow-600
                              text-pac-black-900 font-body font-bold text-sm
                              rounded-xl transition-colors duration-200">
                        첫 기록 추가하기
                    </a>
                </div>
            @endforelse
        </div>

    </div>

</div>
</x-app-layout>
