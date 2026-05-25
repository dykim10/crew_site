<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="font-display text-[10px] font-bold text-pac-pink-500 uppercase tracking-widest">A타입 · 기수 경쟁</span>
        </div>
        <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight leading-tight">
            {{ $event->name }}
        </h1>
        <div class="flex items-center gap-3 mt-2">
            <span class="font-display text-xs font-bold uppercase tracking-widest px-2.5 py-1 rounded
                         {{ $event->isActive() ? 'bg-pac-pink-500 text-white' : 'bg-pac-black-100 text-pac-black-500' }}">
                {{ $event->isActive() ? 'LIVE' : ($event->status === 'upcoming' ? '예정' : '종료') }}
            </span>
            <span class="font-body text-sm text-pac-black-400">
                {{ $event->start_date->format('Y.m.d') }} ~ {{ $event->end_date->format('Y.m.d') }}
            </span>
        </div>
    </div>

    {{-- 이벤트 설명 --}}
    @if($event->description)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="prose prose-sm max-w-none text-pac-black-800">
                {!! $event->description !!}
            </div>
        </div>
    @endif

    {{-- 기수 자격 미충족 --}}
    @if(!$eligible)
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center py-10">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pac-black-100 mb-3">
                <svg class="w-6 h-6 text-pac-black-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>
                </svg>
            </div>
            <p class="font-display text-base font-bold text-pac-black-700 uppercase tracking-widest">참여 대상 아님</p>
            <p class="font-body text-sm text-pac-black-400 mt-1">이 이벤트는 특정 기수 대상으로 운영됩니다.</p>
        </div>

    {{-- 내 마일리지 현황 --}}
    @elseif($grade && $targetKm > 0)
        @php
            $pct      = $targetKm > 0 ? min(100, round($achievedKm / $targetKm * 100)) : 0;
            $achieved = $achievedKm >= $targetKm;
        @endphp
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-pac-black-900 flex items-center justify-between">
                <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">내 마일리지 현황</h3>
                <span class="font-display text-xs font-bold px-2 py-0.5 rounded
                             {{ $achieved ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-pac-black-700 text-pac-black-300' }}">
                    {{ $grade }}등급
                </span>
            </div>
            <div class="p-6 space-y-4">
                {{-- 달성 배지 --}}
                @if($achieved)
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-green-50 border border-green-200 rounded-xl">
                        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="font-body text-sm font-semibold text-green-700">목표 달성! 관리자 검수 후 점수가 부여됩니다.</p>
                    </div>
                @endif

                {{-- 프로그레스 바 --}}
                <div>
                    <div class="flex justify-between items-baseline mb-1.5">
                        <span class="font-body text-xs text-pac-black-500">달성 거리</span>
                        <span class="font-display text-xs font-bold text-pac-black-700">
                            {{ number_format($achievedKm, 1) }} / {{ number_format($targetKm, 0) }} km
                        </span>
                    </div>
                    <div class="h-3 bg-pac-black-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500
                                    {{ $achieved ? 'bg-pac-yellow-500' : 'bg-pac-pink-500' }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="font-body text-xs text-pac-black-400 mt-1 text-right">{{ $pct }}% 달성</p>
                </div>

                {{-- 이벤트 점수 --}}
                @if($eventScore)
                    <div class="flex items-center justify-between px-4 py-3 bg-pac-black-50 rounded-xl">
                        <span class="font-body text-sm text-pac-black-600">부여된 점수</span>
                        <span class="font-display text-sm font-bold text-pac-black-900">
                            {{ number_format($eventScore->score, 1) }}점
                        </span>
                    </div>
                @endif

                <p class="font-body text-xs text-pac-black-400">
                    · 확정된 러닝 기록만 집계됩니다 (관리자 검수 완료 기준)<br>
                    · 이벤트 기간 내 기록: {{ $event->start_date->format('Y.m.d') }} ~ {{ $event->end_date->format('Y.m.d') }}
                </p>
            </div>
        </div>

    {{-- 등급 미설정 안내 --}}
    @else
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-pac-black-900">
                <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">내 마일리지 현황</h3>
            </div>
            <div class="p-6 text-center py-10 space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pac-black-100 mb-1">
                    <svg class="w-6 h-6 text-pac-black-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="font-display text-base font-bold text-pac-black-700 uppercase tracking-widest">등급 미배정</p>
                <p class="font-body text-sm text-pac-black-400">관리자에게 등급 배정을 요청해 주세요.</p>
                <p class="font-body text-xs text-pac-black-300 mt-1">
                    관리자: 구성원 관리 → 해당 구성원 수정 → 등급 설정
                </p>
            </div>
        </div>
    @endif

    {{-- 등급별 목표 (전체 공개) --}}
    @if(!empty($event->score_config))
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-pac-black-50 border-b border-pac-black-100">
                <h3 class="font-display text-xs font-bold text-pac-black-600 uppercase tracking-widest">등급별 목표 거리</h3>
            </div>
            <div class="divide-y divide-pac-black-50">
                @foreach($event->score_config as $row)
                    <div class="flex items-center justify-between px-5 py-3
                                {{ ($grade && $row['grade'] === $grade) ? 'bg-pac-yellow-500/8' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="font-display text-sm font-bold
                                         {{ ($grade && $row['grade'] === $grade) ? 'text-pac-yellow-600' : 'text-pac-black-700' }}">
                                {{ $row['grade'] }}등급
                            </span>
                            @if($grade && $row['grade'] === $grade)
                                <span class="font-display text-[9px] font-bold bg-pac-yellow-500 text-pac-black-900 px-1.5 py-0.5 rounded uppercase tracking-wide">내 등급</span>
                            @endif
                        </div>
                        <span class="font-body text-sm text-pac-black-700">{{ number_format($row['target_km'], 0) }} km</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 뒤로 --}}
    <div class="pt-2">
        <a href="{{ route('events.index') }}"
           class="font-display text-xs font-bold text-pac-black-400 uppercase tracking-widest hover:text-pac-black-700 transition-colors">
            ← 이벤트 목록
        </a>
    </div>

</div>
</x-app-layout>
