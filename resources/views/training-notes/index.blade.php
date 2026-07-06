<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-6 md:px-6 lg:px-8 space-y-5"
     x-data="trainingCalendar(@js([
         'year' => $year,
         'month' => $month,
         'currentWeekStart' => $currentWeekStart,
         'reportUrl' => route('training-notes.reports'),
         'scheduleUrl' => route('training-notes.schedules'),
         'csrf' => csrf_token(),
         'hasReport' => (bool) $currentReport,
         'hasSchedule' => (bool) $currentSchedule,
         'reportData' => $currentReport?->report,
         'scheduleData' => $currentSchedule ? [
             'point_workout' => $currentSchedule->point_workout,
             'weekly_volume' => $currentSchedule->weekly_volume,
             'rationale' => $currentSchedule->rationale,
         ] : null,
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

  {{-- 이번 주 액션 --}}
  <div class="flex flex-wrap gap-3">
    <button type="button" @click="requestSchedule()"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400
                   text-pac-black-900 font-display font-black text-sm uppercase tracking-wider transition-colors">
      <template x-if="loadingSchedule">
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
      </template>
      <span x-text="hasSchedule ? '이번 주 스케줄 보기' : '이번 주 스케줄 생성'"></span>
    </button>
    <button type="button" @click="requestReport()"
            class="inline-flex items-center gap-2 px-5 py-2.5 border border-white/15 hover:border-pac-yellow-500/50
                   text-white font-display text-sm uppercase tracking-wider transition-colors">
      <template x-if="loadingReport">
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
      </template>
      <span x-text="hasReport ? '주간 리포트 보기' : '주간 리포트 생성'"></span>
    </button>
  </div>

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
        <div class="px-3 py-2 bg-pac-yellow-500/5 border-b border-white/[0.04] flex flex-col sm:flex-row sm:items-center gap-2">
          <span class="font-display text-[9px] tracking-widest text-pac-yellow-500 uppercase shrink-0">포인트 훈련</span>
          <span class="font-body text-sm text-white/80">{{ $weekSchedule->point_workout['title'] ?? '훈련' }}</span>
          @if(isset($weekSchedule->point_workout['target_pace_sec_per_km']))
            @php $tp = $weekSchedule->point_workout['target_pace_sec_per_km']; @endphp
            <span class="font-mono text-xs text-pac-black-400">{{ intdiv($tp,60) }}'{{ str_pad($tp%60,2,'0',STR_PAD_LEFT) }}" /km</span>
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
       class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/70"
       @keydown.escape.window="modalOpen = false">
    <div @click.outside="modalOpen = false"
         class="w-full max-w-lg max-h-[80vh] overflow-y-auto bg-pac-black-900 border border-white/10 p-6 space-y-4">
      <div class="flex items-start justify-between gap-4">
        <h2 class="font-display text-lg text-white uppercase tracking-wider" x-text="modalTitle"></h2>
        <button type="button" @click="modalOpen = false" class="text-white/40 hover:text-white">&times;</button>
      </div>
      <div class="font-body text-sm text-white/80 space-y-3" x-html="modalBody"></div>
    </div>
  </div>

  <div x-show="toast" x-cloak x-transition
       class="fixed bottom-6 left-1/2 -translate-x-1/2 px-4 py-2 bg-pac-black-800 border border-red-500/40 text-red-300 text-sm font-body z-50"
       x-text="toast"></div>
</div>

<script>
function trainingCalendar(config) {
  return {
    ...config,
    loadingReport: false,
    loadingSchedule: false,
    modalOpen: false,
    modalTitle: '',
    modalBody: '',
    toast: '',

    async requestReport() {
      if (this.hasReport && this.reportData) {
        this.showReport(this.reportData);
        return;
      }
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
        this.showReport(data.report);
      } catch (e) {
        this.toast = e.message || '주간 리포트 생성에 실패했습니다.';
        setTimeout(() => this.toast = '', 4000);
      } finally {
        this.loadingReport = false;
      }
    },

    async requestSchedule() {
      if (this.hasSchedule && this.scheduleData) {
        this.showSchedule(this.scheduleData);
        return;
      }
      this.loadingSchedule = true;
      try {
        const res = await fetch(this.scheduleUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
          body: JSON.stringify({ week_start: this.currentWeekStart }),
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || '실패');
        this.hasSchedule = true;
        this.scheduleData = data.schedule;
        this.showSchedule(data.schedule);
      } catch (e) {
        this.toast = e.message || '스케줄 생성에 실패했습니다.';
        setTimeout(() => this.toast = '', 4000);
      } finally {
        this.loadingSchedule = false;
      }
    },

    showReport(r) {
      this.modalTitle = '주간 리포트';
      const sr = r.schedule_review || {};
      this.modalBody = `
        <p><strong class="text-pac-yellow-400">총평</strong><br>${r.week_summary || ''}</p>
        <p><strong class="text-pac-yellow-400">볼륨</strong><br>${(r.volume?.total_km ?? 0)}km / ${(r.volume?.runs ?? 0)}회</p>
        <p><strong class="text-pac-yellow-400">스케줄</strong><br>${sr.comment || '—'}</p>
        <p><strong class="text-pac-yellow-400">추세</strong><br>${r.trend || ''}</p>
        <p><strong class="text-pac-yellow-400">다음 주</strong><br>${r.next_week_focus || ''}</p>`;
      this.modalOpen = true;
    },

    showSchedule(s) {
      const pw = s.point_workout || {};
      const vol = s.weekly_volume || {};
      this.modalTitle = '이번 주 스케줄';
      this.modalBody = `
        <p class="font-display text-pac-yellow-400">${pw.title || ''}</p>
        <p>${pw.structure || ''}</p>
        <p class="text-white/50 text-xs">${pw.caution || ''}</p>
        <p><strong>볼륨</strong> ${vol.easy_runs || ''} · ${vol.total_km_min ?? ''}~${vol.total_km_max ?? ''}km</p>
        <p class="text-white/60">${s.rationale || ''}</p>`;
      this.modalOpen = true;
    },
  };
}
</script>
</x-app-layout>
