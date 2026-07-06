<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-6 md:px-6 lg:px-8 space-y-5"
     x-data="trainingCalendar(@js([
         'year' => $year,
         'month' => $month,
         'currentWeekStart' => $currentWeekStart,
         'reportUrl' => route('training-notes.reports'),
         'reportDestroyUrl' => route('training-notes.reports.destroy'),
         'scheduleUrl' => route('training-notes.schedules'),
         'scheduleDestroyUrl' => route('training-notes.schedules.destroy'),
         'csrf' => csrf_token(),
         'hasReport' => (bool) $currentReport,
         'hasSchedule' => (bool) $currentSchedule,
         'reportData' => $currentReport?->report,
         'scheduleData' => $currentSchedule ? [
             'point_workout' => $currentSchedule->point_workout,
             'weekly_volume' => $currentSchedule->weekly_volume,
             'rationale' => $currentSchedule->rationale,
             'demo_mode' => (bool) ($currentSchedule->point_workout['demo_mode'] ?? false),
         ] : null,
         'profileEmpty' => $profileEmpty ?? false,
         'trainingGoal' => $trainingGoal ?? null,
         'hasGoal' => (bool) ($trainingGoal ?? null),
         'goalUrl' => route('training-notes.goal'),
     ]))">

  {{-- 헤더 --}}
  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
      <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-3">TRAINING NOTES</p>
      <h1 class="font-display text-[clamp(36px,6vw,64px)] leading-none tracking-wide text-white uppercase">
        훈련노트
      </h1>
      <div class="w-16 h-0.5 bg-pac-yellow-500 mt-4"></div>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('training-notes.goal') }}"
         class="px-4 py-2 border border-white/10 text-white/70 font-display text-xs uppercase tracking-wider hover:border-pac-yellow-500/40 transition-colors">
        목표 대회
      </a>
      <a href="{{ route('training-notes.records') }}"
         class="px-4 py-2 border border-white/10 text-white/70 font-display text-xs uppercase tracking-wider hover:border-pac-yellow-500/40 transition-colors">
        PB 관리
      </a>
      <a href="{{ route('training-notes.body') }}"
         class="px-4 py-2 border border-white/10 text-white/70 font-display text-xs uppercase tracking-wider hover:border-pac-yellow-500/40 transition-colors">
        체성분
      </a>
    </div>
  </div>

  {{-- 목표 대회 --}}
  @if($trainingGoal ?? null)
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3
                bg-pac-black-900 border border-pac-yellow-500/20 border-l-2 border-l-pac-yellow-500">
      <div>
        <p class="font-display text-[10px] tracking-[3px] uppercase text-pac-yellow-500/80 mb-1">Race Target</p>
        <p class="font-body text-sm text-white">
          <span class="text-pac-yellow-400 font-medium">{{ $trainingGoal['race_name'] }}</span>
          · {{ \App\Services\TrainingNoteService::COURSE_TYPES[$trainingGoal['course_type']] ?? $trainingGoal['course_type'] }}
          · {{ \Carbon\Carbon::parse($trainingGoal['race_date'])->format('Y.m.d') }}
          @if(!empty($trainingGoal['goal_time']))
            <span class="text-white/45">· 목표 {{ $trainingGoal['goal_time'] }}</span>
          @endif
        </p>
      </div>
      <a href="{{ route('training-notes.goal') }}"
         class="shrink-0 px-3 py-1.5 border border-white/15 text-white/60 hover:text-white font-display text-xs uppercase tracking-wider">
        변경
      </a>
    </div>
  @else
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3
                border border-dashed border-pac-yellow-500/30 bg-pac-yellow-500/[0.03]">
      <p class="font-body text-sm text-white/55">
        <span class="text-pac-yellow-400">목표 대회·코스</span>를 설정하면 AI 스케줄이 그 완주를 기준으로 생성됩니다.
      </p>
      <a href="{{ route('training-notes.goal') }}"
         class="shrink-0 px-4 py-2 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900 font-display text-xs font-black uppercase tracking-wider">
        목표 설정
      </a>
    </div>
  @endif

  @if(session('success'))
    <div class="px-4 py-3 bg-pac-green-500/10 border border-pac-green-500/30 text-pac-green-500 text-sm">{{ session('success') }}</div>
  @endif

  {{-- 이번 주 액션 --}}
  <div class="flex flex-wrap gap-3">
    <div class="inline-flex items-stretch gap-1.5">
      <button type="button" @click="requestSchedule()"
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400
                     text-pac-black-900 font-display font-black text-sm uppercase tracking-wider transition-colors">
        <template x-if="loadingSchedule">
          <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
        </template>
        <span x-text="hasSchedule ? '이번 주 스케줄 보기' : '이번 주 스케줄 생성'"></span>
      </button>
      <button type="button" x-show="hasSchedule" x-cloak @click="resetSchedule()"
              :disabled="loadingSchedule || resettingSchedule"
              class="px-3 py-2.5 border border-white/10 hover:border-white/25 text-white/45 hover:text-white/75
                     disabled:opacity-40 font-display text-xs uppercase tracking-wider transition-colors">
        초기화
      </button>
    </div>
    <div class="inline-flex items-stretch gap-1.5">
      <button type="button" @click="requestReport()"
              class="inline-flex items-center gap-2 px-5 py-2.5 border border-white/15 hover:border-pac-yellow-500/50
                     text-white font-display text-sm uppercase tracking-wider transition-colors">
        <template x-if="loadingReport">
          <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
        </template>
        <span x-text="hasReport ? '주간 리포트 보기' : '주간 리포트 생성'"></span>
      </button>
      <button type="button" x-show="hasReport" x-cloak @click="resetReport()"
              :disabled="loadingReport || resettingReport"
              class="px-3 py-2.5 border border-white/10 hover:border-white/25 text-white/45 hover:text-white/75
                     disabled:opacity-40 font-display text-xs uppercase tracking-wider transition-colors">
        초기화
      </button>
    </div>
  </div>

  @if($profileEmpty ?? false)
    <div class="flex gap-3 items-start px-4 py-3 border border-dashed border-pac-yellow-500/25 bg-pac-yellow-500/[0.04]">
      <span class="font-display text-[10px] tracking-[2px] text-pac-black-900 bg-pac-yellow-500 px-1.5 py-0.5 shrink-0">TIP</span>
      <p class="font-body text-sm text-white/50 leading-relaxed">
        PB·러닝·체성분 데이터가 없으면 <span class="text-pac-yellow-400/90">샘플 프로필 데모</span>로 실행됩니다.
        기록 등록 후 맞춤 코칭으로 전환됩니다.
      </p>
    </div>
  @endif

  {{-- 월 네비 --}}
  <div class="flex items-center justify-between">
    @php
      $prev = \Carbon\Carbon::create($year, $month, 1)->subMonth();
      $next = \Carbon\Carbon::create($year, $month, 1)->addMonth();
    @endphp
    <a href="{{ route('training-notes.index', ['year' => $prev->year, 'month' => $prev->month]) }}"
       class="text-white/50 hover:text-pac-yellow-400 font-display text-sm">&larr; {{ $prev->month }}월</a>
    <span class="font-display text-xl text-white tracking-widest">{{ $year }}.{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}</span>
    <a href="{{ route('training-notes.index', ['year' => $next->year, 'month' => $next->month]) }}"
       class="text-white/50 hover:text-pac-yellow-400 font-display text-sm">{{ $next->month }}월 &rarr;</a>
  </div>

  {{-- 캘린더 그리드 --}}
  <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
    <div class="grid grid-cols-7 border-b border-white/[0.06]">
      @foreach(['월','화','수','목','금','토','일'] as $dow)
        <div class="py-2 text-center font-display text-[10px] tracking-widest text-pac-black-500 uppercase">{{ $dow }}</div>
      @endforeach
    </div>

    @php
      $firstDay = \Carbon\Carbon::create($year, $month, 1);
      $startPad = ($firstDay->dayOfWeekIso - 1);
      $daysInMonth = $firstDay->daysInMonth;
      $cells = [];
      for ($i = 0; $i < $startPad; $i++) $cells[] = null;
      for ($d = 1; $d <= $daysInMonth; $d++) $cells[] = $d;
      while (count($cells) % 7 !== 0) $cells[] = null;
      $weekRows = array_chunk($cells, 7);
    @endphp

    @foreach($weekRows as $week)
      @php
        $firstCellDay = collect($week)->first(fn($d) => $d !== null);
        $weekStartKey = $firstCellDay
          ? \Carbon\Carbon::create($year, $month, $firstCellDay)->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d')
          : null;
        $weekSchedule = $weekStartKey ? $schedules->get($weekStartKey) : null;
      @endphp

      @if($weekSchedule)
        <div class="px-3 py-2.5 bg-gradient-to-r from-pac-yellow-500/[0.07] to-transparent border-b border-white/[0.04] flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
          <span class="font-display text-[9px] tracking-[4px] text-pac-yellow-500 uppercase shrink-0">Point · Week</span>
          <span class="font-body text-sm text-white/85 font-medium">{{ $weekSchedule->point_workout['title'] ?? '훈련' }}</span>
          @if(isset($weekSchedule->point_workout['target_pace_sec_per_km']))
            @php $tp = $weekSchedule->point_workout['target_pace_sec_per_km']; @endphp
            <span class="font-mono text-xs text-pac-yellow-500/70 sm:ml-auto">{{ intdiv($tp,60) }}'{{ str_pad($tp%60,2,'0',STR_PAD_LEFT) }}" /km</span>
          @endif
        </div>
      @endif

      <div class="grid grid-cols-7">
        @foreach($week as $day)
          @if($day === null)
            <div class="min-h-[72px] border-b border-r border-white/[0.03] bg-pac-black-950/50"></div>
          @else
            @php
              $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
              $dayLogs = $logsByDate->get($dateKey, collect());
              $isToday = $dateKey === now()->toDateString();
            @endphp
            <div class="min-h-[72px] p-1.5 border-b border-r border-white/[0.04] {{ $isToday ? 'bg-pac-yellow-500/5' : '' }}">
              <div class="font-display text-xs {{ $isToday ? 'text-pac-yellow-400' : 'text-white/40' }}">{{ $day }}</div>
              @foreach($dayLogs->take(2) as $log)
                <a href="{{ route('training-notes.logs.show', $log) }}"
                   class="block mt-1 px-1 py-0.5 bg-white/[0.04] hover:bg-pac-yellow-500/10 rounded-sm group">
                  <div class="flex items-center justify-between gap-1">
                    <span class="font-body text-[10px] text-pac-yellow-400/90">{{ number_format($log->distance_km, 1) }}km</span>
                    @if($log->feedback_at)
                      <svg class="w-3 h-3 text-pac-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    @endif
                  </div>
                  @if($log->avg_pace_seconds)
                    @php $p = $log->avg_pace_seconds; @endphp
                    <span class="font-mono text-[9px] text-white/30">{{ intdiv($p,60) }}'{{ str_pad($p%60,2,'0',STR_PAD_LEFT) }}"</span>
                  @endif
                </a>
              @endforeach
              @if($dayLogs->count() > 2)
                <span class="font-body text-[9px] text-white/25">+{{ $dayLogs->count() - 2 }}</span>
              @endif
            </div>
          @endif
        @endforeach
      </div>
    @endforeach
  </div>

  {{-- 결과 모달 --}}
  <div x-show="modalOpen" x-cloak
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-3 sm:p-6 bg-black/80 backdrop-blur-sm"
       @keydown.escape.window="modalOpen = false">
    <div @click.outside="modalOpen = false"
         x-show="modalOpen"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-2 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         class="tn-modal w-full max-w-xl max-h-[88vh] overflow-hidden flex flex-col bg-[#141414] border border-white/[0.08] shadow-2xl shadow-black/50">

      {{-- 헤더 --}}
      <div class="relative shrink-0 px-5 pt-5 pb-4 border-b border-white/[0.06]">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-pac-yellow-500 to-transparent"></div>
        <div class="flex items-start justify-between gap-4">
          <div>
            <p class="font-display text-[13px] tracking-[4px] uppercase text-pac-yellow-500 mb-1" x-text="modalSubtitle"></p>
            <h2 class="font-display text-[23px] sm:text-[27px] text-white uppercase tracking-wide leading-tight" x-text="modalTitle"></h2>
          </div>
          <button type="button" @click="modalOpen = false"
                  class="shrink-0 w-9 h-9 flex items-center justify-center border border-white/10 text-white/50 hover:text-white hover:border-white/25 transition-colors">
            &times;
          </button>
        </div>
      </div>

      <div class="overflow-y-auto flex-1 px-5 py-5 space-y-4">

        {{-- 데모 배너 --}}
        <div x-show="modalDemo" class="tn-demo-banner">
          <span class="tn-demo-badge">DEMO</span>
          <p class="font-body text-xs text-white/70 leading-relaxed" x-text="modalDemoNote"></p>
        </div>

        {{-- 스케줄 --}}
        <template x-if="modalType === 'schedule' && modalData">
          <div class="space-y-4">
            <div class="tn-hero-card">
              <p class="font-display text-[13px] tracking-[3px] uppercase text-pac-yellow-500/80 mb-2">Point Workout</p>
              <h3 class="font-display text-[27px] sm:text-[29.4px] text-white leading-snug uppercase tracking-wide" x-text="modalData.title"></h3>
              <div class="flex flex-wrap gap-2 mt-4">
                <span x-show="modalData.paceSec" class="tn-chip tn-chip--pace">
                  <span class="text-[9px] uppercase tracking-widest opacity-60">Target</span>
                  <span x-text="formatPace(modalData.paceSec)"></span>
                </span>
                <span x-show="modalData.type" class="tn-chip" x-text="formatWorkoutType(modalData.type)"></span>
              </div>
            </div>

            <div class="tn-section">
              <p class="tn-section-label">훈련 구성</p>
              <p class="font-body text-sm text-white/85 leading-relaxed whitespace-pre-line" x-text="modalData.structure"></p>
            </div>

            <div x-show="modalData.caution" class="tn-caution">
              <p class="font-display text-[12px] tracking-[3px] uppercase text-pac-yellow-500 mb-1.5">Caution</p>
              <p class="font-body text-xs text-white/65 leading-relaxed" x-text="modalData.caution"></p>
            </div>

            <div class="grid grid-cols-3 gap-2">
              <div class="tn-stat">
                <p class="tn-stat-label">이지런</p>
                <p class="tn-stat-value text-base" x-text="modalData.easyRuns || '—'"></p>
              </div>
              <div class="tn-stat col-span-2">
                <p class="tn-stat-label">권장 거리</p>
                <p class="tn-stat-value">
                  <span x-text="modalData.kmMin ?? '—'"></span><span class="text-white/40 mx-1">~</span><span x-text="modalData.kmMax ?? '—'"></span><span class="text-sm text-white/40 ml-1">km</span>
                </p>
              </div>
            </div>

            <div x-show="modalData.rationale" class="tn-coach-note">
              <p class="tn-section-label mb-2">코치 메모</p>
              <p class="font-body text-sm text-white/60 leading-relaxed" x-text="modalData.rationale"></p>
            </div>
          </div>
        </template>

        {{-- 주간 리포트 --}}
        <template x-if="modalType === 'report' && modalData">
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-2">
              <div class="tn-stat tn-stat--accent">
                <p class="tn-stat-label">주간 거리</p>
                <p class="tn-stat-value text-3xl"><span x-text="modalData.totalKm"></span><span class="text-base text-white/40 ml-1">km</span></p>
              </div>
              <div class="tn-stat tn-stat--accent">
                <p class="tn-stat-label">러닝 횟수</p>
                <p class="tn-stat-value text-3xl"><span x-text="modalData.runs"></span><span class="text-base text-white/40 ml-1">회</span></p>
              </div>
            </div>

            <div class="tn-section">
              <p class="tn-section-label">주간 총평</p>
              <p class="font-body text-[15px] text-white/90 leading-relaxed" x-text="modalData.weekSummary"></p>
            </div>

            <div class="tn-section">
              <div class="flex items-center justify-between gap-2 mb-2">
                <p class="tn-section-label mb-0">스케줄 대비</p>
                <span x-show="modalData.pointWorkoutDone === true" class="tn-badge tn-badge--ok">포인트 훈련 완료</span>
                <span x-show="modalData.pointWorkoutDone === false" class="tn-badge tn-badge--pending">미완료</span>
              </div>
              <p class="font-body text-sm text-white/75 leading-relaxed" x-text="modalData.scheduleComment"></p>
              <p x-show="modalData.paceDeviation != null" class="font-mono text-xs text-pac-yellow-500/80 mt-2">
                페이스 편차: <span x-text="formatPaceDeviation(modalData.paceDeviation)"></span>
              </p>
            </div>

            <div class="grid sm:grid-cols-2 gap-3">
              <div class="tn-section h-full">
                <p class="tn-section-label">추세</p>
                <p class="font-body text-sm text-white/70 leading-relaxed" x-text="modalData.trend"></p>
              </div>
              <div class="tn-section tn-section--focus h-full">
                <p class="tn-section-label text-pac-yellow-500">다음 주 포커스</p>
                <p class="font-body text-sm text-white/85 leading-relaxed" x-text="modalData.nextWeekFocus"></p>
              </div>
            </div>
          </div>
        </template>

      </div>

      {{-- 푸터 --}}
      <div class="shrink-0 px-5 py-4 border-t border-white/[0.06] flex items-center justify-between gap-3">
        <p x-show="(modalType === 'schedule' && hasSchedule) || (modalType === 'report' && hasReport)"
           class="font-body text-xs text-white/35 hidden sm:block">
          저장된 내용을 지우고 새로 만들 수 있습니다.
        </p>
        <div class="flex items-center gap-2 ml-auto">
          <button type="button" @click="modalOpen = false"
                  class="px-4 py-2 border border-white/10 text-white/50 hover:text-white/80 font-display text-xs uppercase tracking-wider">
            닫기
          </button>
          <button type="button"
                  x-show="(modalType === 'schedule' && hasSchedule) || (modalType === 'report' && hasReport)"
                  @click="regenerateModal()"
                  :disabled="loadingSchedule || loadingReport || resettingSchedule || resettingReport"
                  class="px-4 py-2 border border-pac-yellow-500/40 text-pac-yellow-400 hover:bg-pac-yellow-500/10
                         disabled:opacity-40 font-display text-xs uppercase tracking-wider">
            재생성
          </button>
        </div>
      </div>
    </div>
  </div>

  <div x-show="toast" x-cloak
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 translate-y-2"
       x-transition:enter-end="opacity-100 translate-y-0"
       class="fixed bottom-6 left-1/2 -translate-x-1/2 px-4 py-2.5 bg-[#1a1a1a] border border-red-500/30 text-red-200 text-sm font-body z-50 max-w-sm text-center"
       x-text="toast"></div>
</div>

<style>
  .tn-modal { border-radius: 2px; }
  .tn-hero-card {
    padding: 1.25rem 1.35rem;
    background: linear-gradient(135deg, rgba(229,173,22,0.08) 0%, rgba(20,20,20,0) 55%);
    border: 1px solid rgba(229,173,22,0.18);
  }
  .tn-section {
    padding: 1rem 1.15rem;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.06);
  }
  .tn-section--focus {
    border-color: rgba(229,173,22,0.22);
    background: rgba(229,173,22,0.04);
  }
  .tn-section-label {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 13px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
    margin-bottom: 0.5rem;
  }
  .tn-stat {
    padding: 0.85rem 1rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
  }
  .tn-stat--accent {
    border-color: rgba(229,173,22,0.15);
    background: rgba(229,173,22,0.05);
  }
  .tn-stat-label {
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
    margin-bottom: 0.35rem;
  }
  .tn-stat-value {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.35rem;
    line-height: 1;
    color: #fff;
    letter-spacing: 0.02em;
  }
  .tn-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.65rem;
    font-family: ui-monospace, monospace;
    font-size: 12px;
    color: rgba(255,255,255,0.85);
    background: rgba(0,0,0,0.35);
    border: 1px solid rgba(255,255,255,0.1);
  }
  .tn-chip--pace { border-color: rgba(229,173,22,0.35); color: #E5AD16; }
  .tn-caution {
    padding: 0.85rem 1rem;
    border-left: 3px solid #E5AD16;
    background: rgba(229,173,22,0.06);
  }
  .tn-coach-note {
    padding: 1rem 1rem 1rem 1.15rem;
    border-left: 2px solid rgba(255,255,255,0.12);
  }
  .tn-demo-banner {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    padding: 0.85rem 1rem;
    background: rgba(229,173,22,0.07);
    border: 1px dashed rgba(229,173,22,0.35);
  }
  .tn-demo-badge {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 11px;
    letter-spacing: 2px;
    color: #1A1212;
    background: #E5AD16;
    padding: 0.15rem 0.45rem;
    flex-shrink: 0;
  }
  .tn-badge {
    font-size: 9px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 0.2rem 0.5rem;
    border: 1px solid rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.5);
  }
  .tn-badge--ok { border-color: rgba(16,185,129,0.4); color: #34d399; }
  .tn-badge--pending { border-color: rgba(255,255,255,0.12); color: rgba(255,255,255,0.4); }
</style>

<script>
function trainingCalendar(config) {
  return {
    ...config,
    loadingReport: false,
    loadingSchedule: false,
    resettingReport: false,
    resettingSchedule: false,
    modalOpen: false,
    modalType: null,
    modalTitle: '',
    modalSubtitle: '',
    modalData: null,
    modalDemo: false,
    modalDemoNote: '',
    toast: '',

    formatPace(sec) {
      if (!sec) return '—';
      const m = Math.floor(sec / 60);
      const s = String(sec % 60).padStart(2, '0');
      return `${m}'${s}"/km`;
    },

    normalizePaceText(text) {
      if (!text) return text;
      return String(text).replace(/(\d{2,4})초\/km/g, (_, sec) => this.formatPace(Number(sec)));
    },

    formatPaceDeviation(sec) {
      if (sec == null) return '';
      const sign = sec > 0 ? '+' : '';
      return `${sign}${sec}초/km`;
    },

    formatWorkoutType(type) {
      const map = {
        sub_threshold_interval: '서브-T',
        easy_run: '이지런',
        long_run: '롱런',
        tempo: '템포',
      };
      return map[type] || type;
    },

    openModal(type, title, subtitle, data, demo = false, demoNote = '') {
      this.modalType = type;
      this.modalTitle = title;
      this.modalSubtitle = subtitle;
      this.modalData = data;
      this.modalDemo = demo;
      this.modalDemoNote = demoNote || '실제 훈련 데이터가 없어 샘플 프로필로 생성된 데모 결과입니다.';
      this.modalOpen = true;
    },

    async requestReport() {
      if (this.hasReport && this.reportData) {
        this.showReport(this.reportData, this.reportData.demo_mode);
        return;
      }
      await this.generateReport();
    },

    async generateReport() {
      this.loadingReport = true;
      try {
        const res = await fetch(this.reportUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
          body: JSON.stringify({ week_start: this.currentWeekStart }),
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || '실패');
        this.hasReport = true;
        this.reportData = data.report;
        this.showReport(data.report, data.demo_mode);
        if (data.demo_mode) {
          this.toast = '샘플 데이터로 데모 리포트를 생성했습니다.';
          setTimeout(() => this.toast = '', 4000);
        }
      } catch (e) {
        this.toast = e.message || '주간 리포트 생성에 실패했습니다.';
        setTimeout(() => this.toast = '', 4000);
      } finally {
        this.loadingReport = false;
      }
    },

    async requestSchedule() {
      if (!this.hasGoal && !this.hasSchedule) {
        this.toast = '스케줄 생성 전 목표 대회·코스를 설정해 주세요.';
        setTimeout(() => this.toast = '', 4500);
        return;
      }
      if (this.hasSchedule && this.scheduleData) {
        this.showSchedule(this.scheduleData, this.scheduleData.demo_mode);
        return;
      }
      await this.generateSchedule();
    },

    async generateSchedule() {
      this.loadingSchedule = true;
      try {
        const res = await fetch(this.scheduleUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
          body: JSON.stringify({ week_start: this.currentWeekStart }),
        });
        const data = await res.json();
        if (!data.success) {
          if (data.code === 'goal_required') {
            this.toast = data.message || '목표 대회를 먼저 설정해 주세요.';
            setTimeout(() => { window.location.href = this.goalUrl; }, 1200);
            return;
          }
          throw new Error(data.message || '실패');
        }
        this.hasSchedule = true;
        this.scheduleData = data.schedule;
        this.showSchedule(data.schedule, data.demo_mode);
        if (data.demo_mode) {
          this.toast = '샘플 데이터로 데모 스케줄을 생성했습니다.';
          setTimeout(() => this.toast = '', 4000);
        }
      } catch (e) {
        this.toast = e.message || '스케줄 생성에 실패했습니다.';
        setTimeout(() => this.toast = '', 4000);
      } finally {
        this.loadingSchedule = false;
      }
    },

    async resetReport(skipConfirm = false) {
      if (!this.hasReport) return;
      if (!skipConfirm && !confirm('저장된 주간 리포트를 삭제합니다. 초기화할까요?')) return;
      this.resettingReport = true;
      try {
        const res = await fetch(this.reportDestroyUrl, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
          body: JSON.stringify({ week_start: this.currentWeekStart }),
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || '실패');
        this.hasReport = false;
        this.reportData = null;
        if (this.modalType === 'report') this.modalOpen = false;
        if (!skipConfirm) window.location.reload();
      } catch (e) {
        this.toast = e.message || '리포트 초기화에 실패했습니다.';
        setTimeout(() => this.toast = '', 4000);
      } finally {
        this.resettingReport = false;
      }
    },

    async resetSchedule(skipConfirm = false) {
      if (!this.hasSchedule) return;
      if (!skipConfirm && !confirm('저장된 스케줄을 삭제합니다. 초기화할까요?')) return;
      this.resettingSchedule = true;
      try {
        const res = await fetch(this.scheduleDestroyUrl, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
          body: JSON.stringify({ week_start: this.currentWeekStart }),
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || '실패');
        this.hasSchedule = false;
        this.scheduleData = null;
        if (this.modalType === 'schedule') this.modalOpen = false;
        if (!skipConfirm) window.location.reload();
      } catch (e) {
        this.toast = e.message || '스케줄 초기화에 실패했습니다.';
        setTimeout(() => this.toast = '', 4000);
      } finally {
        this.resettingSchedule = false;
      }
    },

    async regenerateModal() {
      if (!confirm('저장된 내용을 삭제하고 새로 생성합니다. 계속할까요?')) return;
      const type = this.modalType;
      this.modalOpen = false;
      if (type === 'schedule') {
        await this.resetSchedule(true);
        await this.generateSchedule();
      } else if (type === 'report') {
        await this.resetReport(true);
        await this.generateReport();
      }
    },

    showReport(r, demo = false) {
      const sr = r.schedule_review || {};
      this.openModal('report', '주간 리포트', demo ? 'Weekly Report · Demo' : 'Weekly Report · AI', {
        weekSummary: this.normalizePaceText(r.week_summary || ''),
        totalKm: r.volume?.total_km ?? 0,
        runs: r.volume?.runs ?? 0,
        scheduleComment: this.normalizePaceText(sr.comment || '스케줄 대비 데이터가 없습니다.'),
        pointWorkoutDone: sr.point_workout_done,
        paceDeviation: sr.pace_deviation_sec ?? null,
        trend: this.normalizePaceText(r.trend || ''),
        nextWeekFocus: this.normalizePaceText(r.next_week_focus || ''),
      }, demo, r.demo_note);
    },

    showSchedule(s, demo = false) {
      const pw = s.point_workout || {};
      const vol = s.weekly_volume || {};
      this.openModal('schedule', '이번 주 스케줄', demo ? 'Training Plan · Demo' : 'Training Plan · AI', {
        title: pw.title || '포인트 훈련',
        structure: pw.structure || '',
        caution: pw.caution || '',
        paceSec: pw.target_pace_sec_per_km || null,
        type: pw.type || '',
        easyRuns: vol.easy_runs || '',
        kmMin: vol.total_km_min,
        kmMax: vol.total_km_max,
        rationale: s.rationale || '',
      }, demo, s.demo_note);
    },
  };
}
</script>
</x-app-layout>
