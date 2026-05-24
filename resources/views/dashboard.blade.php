<x-app-layout>
<div class="max-w-7xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4 lg:space-y-5">

    {{-- ① 인사 배너 --}}
    <div class="relative bg-pac-black-900 rounded-2xl overflow-hidden">
        <div class="absolute inset-y-0 left-0 w-1.5 bg-pac-yellow-500"></div>
        <div class="absolute inset-y-0 right-0 w-64 bg-gradient-to-l from-pac-yellow-500 opacity-5"></div>
        <span class="absolute -bottom-8 -right-8 w-40 h-40 bg-pac-pink-500 opacity-10 rounded-full"></span>
        <span class="absolute top-3 right-24 w-8 h-8 bg-pac-yellow-500 opacity-10 rounded-full"></span>

        <div class="relative z-10 flex items-center justify-between gap-4 px-7 py-5 lg:px-8 lg:py-6">
            <div>
                <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">
                    {{ now()->format('Y.m.d') }} · PAC-RUN CREW
                </p>
                <h2 class="font-display text-2xl lg:text-3xl font-bold text-white uppercase tracking-tight leading-tight">
                    WELCOME BACK,
                    <span class="text-pac-yellow-400">{{ auth()->user()->nickname ?? auth()->user()->name }}</span>
                </h2>
                @if($stats['monthly_percent'] !== null)
                    @if($stats['monthly_percent'] >= 100)
                        <p class="font-body text-sm text-pac-green-500 mt-1.5 flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-pac-green-500"></span>
                            이번 달 목표를 달성했습니다!
                        </p>
                    @else
                        <p class="font-body text-sm text-pac-black-400 mt-1.5">
                            이번 달 목표까지
                            <span class="text-pac-yellow-400 font-bold">{{ $stats['monthly_percent'] }}%</span>
                            달성
                        </p>
                    @endif
                @else
                    <p class="font-body text-sm text-pac-black-400 mt-1.5">오늘도 함께 달려요!</p>
                @endif
            </div>
            <a href="{{ route('running-logs.create') }}"
               class="shrink-0 inline-flex items-center gap-2 px-5 py-3
                      bg-pac-yellow-500 hover:bg-pac-yellow-400
                      text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                      rounded-xl transition-colors duration-200 min-h-[48px]">
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
        <div class="bg-pac-black-900 rounded-2xl p-4 lg:p-5 border-t-2 border-pac-yellow-500
                    hover:bg-pac-black-800 transition-colors duration-200">
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-3">
                이번 달 거리
            </p>
            <p class="font-display text-4xl lg:text-5xl font-bold text-white leading-none">
                {{ number_format($stats['monthly_km'], 1) }}
                <span class="font-body text-base font-normal text-pac-black-400">km</span>
            </p>
            @if($stats['monthly_percent'] !== null)
                <p class="font-body text-xs text-pac-yellow-500 mt-3 font-semibold">
                    목표 {{ $stats['monthly_percent'] }}% 달성
                </p>
            @else
                <p class="font-body text-xs text-pac-black-500 mt-3">{{ $stats['total_count'] }}회 누적</p>
            @endif
        </div>

        {{-- 누적 거리 --}}
        <div class="bg-pac-black-900 rounded-2xl p-4 lg:p-5 border-t-2 border-pac-black-600
                    hover:bg-pac-black-800 transition-colors duration-200">
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-3">
                누적 거리
            </p>
            <p class="font-display text-4xl lg:text-5xl font-bold text-white leading-none">
                {{ number_format($stats['total_km'], 0) }}
                <span class="font-body text-base font-normal text-pac-black-400">km</span>
            </p>
            <p class="font-body text-xs text-pac-black-500 mt-3">총 {{ $stats['total_count'] }}회 러닝</p>
        </div>

        {{-- 이벤트 점수 --}}
        <div class="bg-pac-black-900 rounded-2xl p-4 lg:p-5 border-t-2 border-pac-pink-500
                    hover:bg-pac-black-800 transition-colors duration-200">
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-3">
                이벤트 점수
            </p>
            @if($stats['has_active_event'])
                <p class="font-display text-4xl lg:text-5xl font-bold text-pac-pink-500 leading-none">
                    {{ $stats['event_score'] }}
                    <span class="font-body text-base font-normal text-pac-black-400">pts</span>
                </p>
                <p class="font-body text-xs text-pac-black-500 mt-3">진행 중 이벤트</p>
            @else
                <p class="font-display text-4xl lg:text-5xl font-bold text-pac-black-700 leading-none">—</p>
                <p class="font-body text-xs text-pac-black-600 mt-3">이벤트 없음</p>
            @endif
        </div>

        {{-- 조 순위 --}}
        <div class="bg-pac-black-900 rounded-2xl p-4 lg:p-5 border-t-2 border-pac-green-500
                    hover:bg-pac-black-800 transition-colors duration-200">
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-3">
                조 순위
            </p>
            @if($stats['group_rank'])
                <p class="font-display text-4xl lg:text-5xl font-bold text-white leading-none">
                    {{ $stats['group_rank'] }}
                    <span class="font-body text-base font-normal text-pac-black-400">위</span>
                </p>
                <p class="font-body text-xs text-pac-black-500 mt-3">{{ $stats['group_total'] }}명 중</p>
            @else
                <p class="font-display text-4xl lg:text-5xl font-bold text-pac-black-700 leading-none">—</p>
                <p class="font-body text-xs text-pac-black-600 mt-3">조 미배정</p>
            @endif
        </div>

    </div>

    {{-- ③ 크루 공지사항 --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
        <div class="flex items-center justify-between px-5 py-3.5 bg-pac-black-900">
            <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">공지사항</h3>
            <a href="#" class="font-display text-xs font-bold text-pac-yellow-400 hover:text-pac-yellow-300 uppercase tracking-widest transition-colors duration-200">
                전체보기 →
            </a>
        </div>

        @forelse($notices as $notice)
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-pac-black-50 last:border-0
                        hover:bg-pac-black-50 transition-colors duration-200 cursor-pointer">
                @if($notice->is_pinned)
                    <span class="font-display text-[10px] font-bold uppercase tracking-widest shrink-0
                                 bg-pac-pink-500 text-white px-2 py-0.5 rounded">
                        고정
                    </span>
                @else
                    <span class="w-1.5 h-1.5 rounded-full bg-pac-yellow-500 shrink-0"></span>
                @endif
                <p class="font-body text-sm font-semibold text-pac-black-900 flex-1 min-w-0 truncate
                           hover:text-pac-yellow-600 transition-colors duration-200">
                    {{ $notice->title }}
                </p>
                <p class="font-body text-xs text-pac-black-400 shrink-0">
                    {{ $notice->created_at->format('m.d') }} ·
                    {{ $notice->target_type === 'all' ? '전체' : '지역' }}
                </p>
            </div>
        @empty
            <div class="px-5 py-10 text-center">
                <p class="font-body text-sm text-pac-black-300">등록된 공지사항이 없습니다.</p>
            </div>
        @endforelse
    </div>

    {{-- ④ 이벤트 / ⑤ 최근 기록 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5">

        {{-- ④ 마일리지 진행바 + 이벤트 목록 --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-pac-black-900">
                <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">이벤트</h3>
            </div>
            <div class="p-5">
                @if($mileage)
                    <div class="mb-5 pb-5 border-b border-pac-black-50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-body text-sm font-semibold text-pac-black-700">
                                {{ $mileage['name'] }}
                            </span>
                            <span class="font-display text-sm font-bold text-pac-yellow-600">
                                {{ number_format($mileage['achieved'], 1) }}
                                <span class="font-body text-xs font-normal text-pac-black-400">/ {{ number_format($mileage['target'], 0) }}km</span>
                            </span>
                        </div>
                        <div class="bg-pac-black-100 rounded-full h-3 overflow-hidden">
                            <div class="h-full rounded-full bg-pac-yellow-500 transition-all duration-700"
                                 style="width: {{ $mileage['percent'] }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            @if($mileage['done'])
                                <span class="font-display text-xs font-bold uppercase tracking-widest text-pac-green-500">
                                    달성 완료!
                                </span>
                            @elseif($mileage['percent'] >= 90)
                                <span class="font-display text-xs font-bold uppercase tracking-widest text-pac-pink-500">
                                    거의 다 왔어요!
                                </span>
                            @else
                                <span class="font-body text-xs text-pac-black-400">D-{{ $mileage['days_left'] }}</span>
                            @endif
                            <span class="font-display text-xs font-bold text-pac-yellow-600">{{ $mileage['percent'] }}%</span>
                        </div>
                    </div>
                @endif

                @forelse($events as $event)
                    <div class="flex items-center gap-3 py-3 border-b border-pac-black-50 last:border-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-body text-sm font-semibold text-pac-black-800 truncate">
                                {{ $event->name }}
                            </p>
                            <p class="font-body text-xs text-pac-black-400 mt-0.5">
                                ~ {{ \Carbon\Carbon::parse($event->end_date)->format('Y.m.d') }}
                            </p>
                        </div>
                        @if(now()->between($event->start_date, $event->end_date))
                            <span class="font-display text-[10px] font-bold uppercase tracking-widest shrink-0
                                         bg-pac-pink-500 text-white px-2.5 py-1 rounded">
                                LIVE
                            </span>
                        @else
                            <span class="font-display text-[10px] font-bold uppercase tracking-widest shrink-0
                                         bg-pac-yellow-100 text-pac-yellow-700 px-2.5 py-1 rounded">
                                예정
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="font-body text-sm text-pac-black-300">진행 중인 이벤트가 없습니다.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ⑤ 최근 러닝 기록 --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 bg-pac-black-900">
                <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">최근 기록</h3>
                <a href="{{ route('running-logs.index') }}"
                   class="font-display text-xs font-bold text-pac-yellow-400 hover:text-pac-yellow-300 uppercase tracking-widest transition-colors duration-200">
                    전체 →
                </a>
            </div>
            <div class="p-5">
                @forelse($recentLogs as $log)
                    @if($log->is_confirmed)
                        {{-- 확정 기록 --}}
                        <a href="{{ route('running-logs.show', $log) }}"
                           class="flex items-center gap-4 py-3 border-b border-pac-black-50 last:border-0
                                  hover:bg-pac-black-50 -mx-5 px-5 transition-colors duration-200">
                            <div class="shrink-0 w-11 h-11 rounded-lg bg-pac-black-900
                                        flex flex-col items-center justify-center">
                                <p class="font-display text-base font-bold text-pac-yellow-400 leading-none">
                                    {{ $log->run_date->format('d') }}
                                </p>
                                <p class="font-body text-[8px] text-pac-black-500 uppercase mt-0.5">
                                    {{ $log->run_date->format('M') }}
                                </p>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-display text-xl font-bold text-pac-black-900 leading-none">
                                    {{ number_format($log->distance_km, 2) }}
                                    <span class="font-body text-xs font-normal text-pac-black-400">km</span>
                                </p>
                                <p class="font-body text-xs text-pac-black-400 mt-0.5 truncate">
                                    {{ $log->avg_pace_formatted ? $log->avg_pace_formatted . '/km · ' : '' }}{{ $log->duration_formatted }}
                                </p>
                            </div>
                            <span class="font-display text-[9px] font-bold uppercase tracking-widest shrink-0 px-2.5 py-1 rounded
                                         {{ $log->is_indoor ? 'bg-pac-black-100 text-pac-black-500' : 'bg-pac-yellow-100 text-pac-yellow-700' }}">
                                {{ $log->is_indoor ? '실내' : '야외' }}
                            </span>
                        </a>
                    @else
                        {{-- 미확정(검토 대기) 기록 --}}
                        <a href="{{ route('running-logs.edit', $log) }}"
                           class="flex items-center gap-4 py-3 border-b border-pac-yellow-100 last:border-0
                                  border-l-4 border-l-pac-yellow-400 bg-pac-yellow-50 hover:bg-pac-yellow-100
                                  -mx-5 px-5 transition-colors duration-200">
                            <div class="shrink-0 w-11 h-11 rounded-lg bg-pac-black-700
                                        flex flex-col items-center justify-center">
                                <p class="font-display text-base font-bold text-pac-yellow-300 leading-none">
                                    {{ $log->run_date->format('d') }}
                                </p>
                                <p class="font-body text-[8px] text-pac-black-400 uppercase mt-0.5">
                                    {{ $log->run_date->format('M') }}
                                </p>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-display text-xl font-bold text-pac-black-700 leading-none">
                                    {{ number_format($log->distance_km, 2) }}
                                    <span class="font-body text-xs font-normal text-pac-black-400">km</span>
                                </p>
                                <p class="font-body text-xs text-pac-black-400 mt-0.5 truncate">
                                    AI 파싱 완료 · 검토 후 확정 필요
                                </p>
                            </div>
                            <span class="font-display text-[9px] font-bold uppercase tracking-widest shrink-0 px-2.5 py-1 rounded
                                         bg-pac-yellow-200 text-pac-yellow-700 border border-pac-yellow-300">
                                검토 대기
                            </span>
                        </a>
                    @endif
                @empty
                    <div class="py-10 text-center">
                        <p class="font-body text-sm text-pac-black-400 mb-4">아직 기록이 없어요.</p>
                        <a href="{{ route('running-logs.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5
                                  bg-pac-yellow-500 hover:bg-pac-yellow-400
                                  text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                                  rounded-xl transition-colors duration-200">
                            첫 기록 추가하기
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
</x-app-layout>
