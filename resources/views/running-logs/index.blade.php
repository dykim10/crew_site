<x-app-layout>
<div class="max-w-4xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 페이지 헤더 --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-0.5">MY RUNNING</p>
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">기록 목록</h1>
        </div>
        <a href="{{ route('running-logs.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5
                  bg-pac-yellow-500 hover:bg-pac-yellow-400
                  text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                  rounded-xl transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            기록 추가
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl font-body text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- 검토 대기 배너 --}}
    @if($pendingCount > 0)
        <div class="flex items-center gap-3 bg-pac-yellow-50 border border-pac-yellow-200 rounded-xl px-4 py-3">
            <svg class="w-4 h-4 text-pac-yellow-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            <p class="font-body text-sm text-pac-black-700 flex-1">
                AI 파싱 후 <span class="font-bold text-pac-yellow-700">검토 대기 {{ $pendingCount }}건</span>이 있습니다. 수정 후 확정해주세요.
            </p>
        </div>
    @endif

    {{-- 이달 통계 (확정 기록 기준) --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-pac-black-900 rounded-2xl p-4 border-t-2 border-pac-yellow-500 text-center">
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">이번 달 거리</p>
            <p class="font-display text-3xl font-bold text-white leading-none">
                {{ number_format($monthlyStats['total_km'], 1) }}
                <span class="font-body text-sm font-normal text-pac-black-400">km</span>
            </p>
        </div>
        <div class="bg-pac-black-900 rounded-2xl p-4 border-t-2 border-pac-black-600 text-center">
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">확정 횟수</p>
            <p class="font-display text-3xl font-bold text-white leading-none">
                {{ $monthlyStats['total_count'] }}
                <span class="font-body text-sm font-normal text-pac-black-400">회</span>
            </p>
        </div>
        <div class="bg-pac-black-900 rounded-2xl p-4 border-t-2 border-pac-pink-500 text-center">
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">평균 페이스</p>
            @php
                $pace = $monthlyStats['avg_pace'];
                $paceStr = $pace ? sprintf("%d'%02d\"", intdiv($pace, 60), $pace % 60) : '—';
            @endphp
            <p class="font-display text-3xl font-bold text-white leading-none">{{ $paceStr }}</p>
        </div>
    </div>

    {{-- 기록 목록 --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 bg-pac-black-900">
            <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">전체 기록</h3>
            @if($pendingCount > 0)
                <span class="font-display text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded bg-pac-yellow-500 text-pac-black-900">
                    검토 대기 {{ $pendingCount }}
                </span>
            @endif
        </div>

        @forelse($logs as $log)
            @if($log->is_confirmed)
                {{-- 확정 기록 --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-pac-black-100 last:border-0
                            hover:bg-pac-black-50 transition-colors duration-200">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-pac-black-900
                                    flex flex-col items-center justify-center">
                            <p class="font-display text-lg font-bold text-pac-yellow-400 leading-none">
                                {{ $log->run_date->format('d') }}
                            </p>
                            <p class="font-body text-[9px] text-pac-black-500 uppercase">
                                {{ $log->run_date->format('M') }}
                            </p>
                        </div>
                        @if($log->image_url)
                            <img src="{{ $log->image_url }}" class="shrink-0 w-12 h-12 rounded-xl object-cover" alt="">
                        @endif
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-display text-xl font-bold text-pac-black-900 leading-none">
                                    {{ number_format($log->distance_km, 2) }}
                                    <span class="font-body text-sm font-normal text-pac-black-400">km</span>
                                </p>
                                <span class="font-display text-[9px] font-bold uppercase tracking-widest px-1.5 py-0.5 rounded
                                             bg-green-100 text-green-700 border border-green-200">
                                    확정
                                </span>
                            </div>
                            <p class="font-body text-xs text-pac-black-400 mt-0.5">
                                {{ $log->duration_formatted }}
                                @if($log->avg_pace_formatted)
                                    · {{ $log->avg_pace_formatted }}/km
                                @endif
                                · <span class="{{ $log->is_indoor ? 'text-pac-black-400' : 'text-pac-yellow-600' }}">
                                    {{ $log->is_indoor ? '실내' : '야외' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('running-logs.show', $log) }}"
                           class="font-display text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg
                                  border border-pac-yellow-400 text-pac-yellow-600 hover:bg-pac-yellow-50 transition-colors">
                            상세
                        </a>
                        <a href="{{ route('running-logs.edit', $log) }}"
                           class="font-display text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg
                                  border border-pac-black-200 text-pac-black-500 hover:bg-pac-black-50 transition-colors">
                            수정
                        </a>
                        <form method="POST" action="{{ route('running-logs.destroy', $log) }}"
                              onsubmit="return confirm('이 기록을 삭제하시겠습니까?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="font-display text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg
                                           border border-pac-pink-300 text-pac-pink-500 hover:bg-pink-50 transition-colors">
                                삭제
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- 미확정(검토 대기) 기록 --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-yellow-100 last:border-0
                            border-l-4 border-l-yellow-400 bg-yellow-50 hover:bg-yellow-100
                            transition-colors duration-200">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-pac-black-700
                                    flex flex-col items-center justify-center">
                            <p class="font-display text-lg font-bold text-pac-yellow-300 leading-none">
                                {{ $log->run_date->format('d') }}
                            </p>
                            <p class="font-body text-[9px] text-pac-black-400 uppercase">
                                {{ $log->run_date->format('M') }}
                            </p>
                        </div>
                        @if($log->image_url)
                            <img src="{{ $log->image_url }}" class="shrink-0 w-12 h-12 rounded-xl object-cover opacity-70" alt="">
                        @endif
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-display text-xl font-bold text-pac-black-700 leading-none">
                                    {{ number_format($log->distance_km, 2) }}
                                    <span class="font-body text-sm font-normal text-pac-black-400">km</span>
                                </p>
                                <span class="font-display text-[9px] font-bold uppercase tracking-widest px-1.5 py-0.5 rounded
                                             bg-yellow-100 text-yellow-700 border border-yellow-300">
                                    검토 대기
                                </span>
                            </div>
                            <p class="font-body text-xs text-pac-black-400 mt-0.5">
                                {{ $log->duration_formatted ?? '시간 미입력' }}
                                @if($log->avg_pace_formatted)
                                    · {{ $log->avg_pace_formatted }}/km
                                @endif
                                · AI 파싱 완료 · 내용을 확인 후 수정 버튼으로 확정하세요
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('running-logs.edit', $log) }}"
                           class="font-display text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg
                                  border border-yellow-400 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                            검토·수정
                        </a>
                        <form method="POST" action="{{ route('running-logs.destroy', $log) }}"
                              onsubmit="return confirm('이 기록을 삭제하시겠습니까?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="font-display text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg
                                           border border-pac-pink-300 text-pac-pink-500 hover:bg-pink-50 transition-colors">
                                삭제
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @empty
            <div class="py-16 text-center">
                <p class="font-body text-sm text-pac-black-400 mb-5">아직 러닝 기록이 없습니다.</p>
                <a href="{{ route('running-logs.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3
                          bg-pac-yellow-500 hover:bg-pac-yellow-400
                          text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                          rounded-xl transition-colors duration-200">
                    첫 기록 추가하기
                </a>
            </div>
        @endforelse
    </div>

    <div>{{ $logs->links() }}</div>

</div>
</x-app-layout>
