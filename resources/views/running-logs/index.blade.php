<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-6 md:px-6 lg:px-8 space-y-5">

  {{-- 페이지 헤더 --}}
  <div class="flex items-start justify-between gap-4">
    <div>
      <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-3">MY RUNNING</p>
      <h1 class="font-display text-[clamp(40px,6vw,72px)] leading-none tracking-wide text-white uppercase">
        기록 목록
      </h1>
      <div class="w-16 h-0.5 bg-pac-yellow-500 mt-4 mb-1"></div>
    </div>
    <a href="{{ route('running-logs.create') }}"
       class="shrink-0 mt-1 inline-flex items-center gap-2 px-5 py-2.5
              bg-pac-yellow-500 hover:bg-pac-yellow-400
              text-pac-black-900 font-display font-black text-sm uppercase tracking-wider
              transition-colors duration-150">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
      </svg>
      기록 추가
    </a>
  </div>

  {{-- 플래시 --}}
  @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-pac-green-500/10 border border-pac-green-500/30 text-pac-green-500 font-body text-sm">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      {{ session('success') }}
    </div>
  @endif

  {{-- 검토 대기 배너 --}}
  @if($pendingCount > 0)
    <div class="flex items-center gap-3 px-4 py-3.5
                bg-pac-yellow-500/10 border-l-2 border-pac-yellow-400">
      <svg class="w-4 h-4 text-pac-yellow-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
      </svg>
      <p class="font-body text-sm text-pac-black-300 flex-1">
        AI 파싱 후 <span class="font-bold text-pac-yellow-400">검토 대기 {{ $pendingCount }}건</span>이 있습니다. 수정 후 확정해주세요.
      </p>
    </div>
  @endif

  {{-- 이달 통계 --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div class="bg-pac-black-900 border border-white/[0.05] border-t-2 border-t-pac-yellow-500 px-5 py-4">
      <p class="font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-[0.2em] mb-2">이번 달 거리</p>
      <p class="font-display text-3xl font-black text-pac-yellow-400 leading-none">
        {{ number_format($monthlyStats['total_km'], 1) }}
        <span class="font-body text-sm font-normal text-pac-black-500">km</span>
      </p>
    </div>
    <div class="bg-pac-black-900 border border-white/[0.05] border-t-2 border-t-white/10 px-5 py-4">
      <p class="font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-[0.2em] mb-2">확정 횟수</p>
      <p class="font-display text-3xl font-black text-white leading-none">
        {{ $monthlyStats['total_count'] }}
        <span class="font-body text-sm font-normal text-pac-black-500">회</span>
      </p>
    </div>
    <div class="bg-pac-black-900 border border-white/[0.05] border-t-2 border-t-pac-pink-500 px-5 py-4">
      <p class="font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-[0.2em] mb-2">평균 페이스</p>
      @php
        $pace = $monthlyStats['avg_pace'];
        $paceStr = $pace ? sprintf("%d'%02d\"", intdiv($pace, 60), $pace % 60) : '—';
      @endphp
      <p class="font-display text-3xl font-black text-white leading-none">{{ $paceStr }}</p>
    </div>
  </div>

  {{-- 기록 목록 --}}
  <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/[0.06]">
      <span class="font-display text-xs font-black text-white uppercase tracking-[0.15em]">전체 기록</span>
      @if($pendingCount > 0)
        <span class="font-display text-[10px] font-black uppercase tracking-wider
                     bg-pac-yellow-500 text-pac-black-900 px-2 py-0.5">
          검토 대기 {{ $pendingCount }}
        </span>
      @endif
    </div>

    @forelse($logs as $log)
      @if($log->is_confirmed)
        <a href="{{ route('running-logs.show', $log) }}"
           class="flex items-center gap-4 px-5 py-4 border-b border-white/[0.04] last:border-0
                  hover:bg-white/[0.025] transition-colors group">
      @else
        <a href="{{ route('running-logs.edit', $log) }}"
           class="flex items-center gap-4 px-5 py-4 border-b border-white/[0.04] last:border-0
                  hover:bg-pac-yellow-500/5 border-l-2 border-l-pac-yellow-400 transition-colors group">
      @endif

          {{-- 날짜 블록 --}}
          <div class="shrink-0 w-12 h-12 bg-pac-black-800 border border-white/[0.06]
                      flex flex-col items-center justify-center
                      group-hover:border-pac-yellow-500/30 transition-colors">
            <span class="font-display text-lg font-black text-pac-yellow-400 leading-none">
              {{ $log->run_date->format('d') }}
            </span>
            <span class="font-display text-[8px] text-pac-black-500 uppercase tracking-wider">
              {{ $log->run_date->format('M') }}
            </span>
          </div>

          {{-- 거리 + 페이스 --}}
          <div class="flex-1 min-w-0">
            <div class="flex items-baseline gap-2">
              <span class="font-display text-2xl font-black text-white leading-none
                           group-hover:text-pac-yellow-400 transition-colors">
                {{ number_format($log->distance_km, 2) }}
              </span>
              <span class="font-body text-xs text-pac-black-500">km</span>
              @if(!$log->is_confirmed)
                <span class="font-display text-[9px] font-black uppercase tracking-wider
                             border border-pac-yellow-500/50 text-pac-yellow-500 px-1.5 py-0.5 ml-1">
                  검토 대기
                </span>
              @endif
            </div>
            <p class="font-body text-xs text-pac-black-500 mt-0.5">
              {{ $log->run_date->format('Y.m.d') }}
              @if($log->avg_pace_formatted) · {{ $log->avg_pace_formatted }}/km @endif
              @if($log->duration_formatted)  · {{ $log->duration_formatted }} @endif
            </p>
          </div>

          {{-- 태그 + 거리 그래프 바 --}}
          <div class="hidden sm:flex shrink-0 flex-col items-end gap-2">
            <span class="font-display text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5
                         {{ $log->is_indoor
                             ? 'bg-pac-black-700 text-pac-black-400'
                             : 'bg-pac-yellow-500/10 text-pac-yellow-500 border border-pac-yellow-500/20' }}">
              {{ $log->is_indoor ? '실내' : '야외' }}
            </span>
            @if($log->calories)
              <span class="font-display text-[9px] text-pac-black-600 uppercase tracking-wider">
                {{ $log->calories }}kcal
              </span>
            @endif
          </div>

          {{-- 삭제 폼 --}}
          <form method="POST" action="{{ route('running-logs.destroy', $log) }}"
                onsubmit="return confirm('기록을 삭제하시겠습니까?')"
                class="shrink-0">
            @csrf @method('DELETE')
            <button type="submit"
                    class="p-2 text-pac-black-700 hover:text-pac-pink-400 transition-colors opacity-0 group-hover:opacity-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </form>
        </a>

    @empty
      <div class="px-5 py-20 text-center">
        <p class="font-display text-5xl font-black text-pac-black-800 uppercase tracking-widest mb-3">0 KM</p>
        <p class="font-body text-sm text-pac-black-600 mb-6">아직 러닝 기록이 없어요</p>
        <a href="{{ route('running-logs.create') }}"
           class="inline-flex items-center gap-2 px-6 py-2.5
                  bg-pac-yellow-500 hover:bg-pac-yellow-400
                  text-pac-black-900 font-display font-black text-sm uppercase tracking-wider
                  transition-colors duration-150">
          첫 기록 추가하기
        </a>
      </div>
    @endforelse
  </div>

  {{-- 페이지네이션 --}}
  @if($logs->hasPages())
    <div class="flex justify-center">
      {{ $logs->links() }}
    </div>
  @endif

</div>
</x-app-layout>
