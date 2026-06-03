<x-app-layout>
@php
  $user    = auth()->user();
  $nick    = $user->nickname ?? $user->name ?? '?';
  $initial = mb_strtoupper(mb_substr($nick, 0, 1));
  $roleMap = [
    'super_admin'  => ['label' => 'SUPER ADMIN',   'color' => 'text-pac-pink-400',   'bg' => 'bg-pac-pink-500'],
    'region_admin' => ['label' => 'REGION ADMIN',  'color' => 'text-pac-yellow-400', 'bg' => 'bg-pac-yellow-500'],
    'operator'     => ['label' => 'OPERATOR',       'color' => 'text-pac-green-500',  'bg' => 'bg-pac-green-500'],
    'member'       => ['label' => 'MEMBER',         'color' => 'text-pac-black-400',  'bg' => 'bg-pac-black-600'],
  ];
  $roleInfo = $roleMap[$user->role] ?? $roleMap['member'];
@endphp

<div class="max-w-7xl mx-auto px-4 py-6 md:px-6 lg:px-8 space-y-5">

  {{-- ══════════════════════════════════════════════
       HERO: 마이페이지 프로필 헤더
       ══════════════════════════════════════════════ --}}
  <div class="relative bg-pac-black-900 overflow-hidden">

    {{-- 배경 장식 --}}
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-0 right-0 w-96 h-full"
           style="background:linear-gradient(to left, rgba(229,173,22,0.04) 0%, transparent 100%);"></div>
      <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-pac-yellow-500/40 via-transparent to-transparent"></div>
      {{-- 배경 텍스트 장식 --}}
      <div class="absolute right-6 top-1/2 -translate-y-1/2 font-display text-[100px] font-black text-white/[0.025]
                  uppercase tracking-tight leading-none select-none hidden lg:block">
        PAC RUN
      </div>
    </div>

    <div class="relative z-10 px-6 py-6 lg:px-8 lg:py-8">
      <div class="flex flex-col sm:flex-row sm:items-center gap-5">

        {{-- 아바타 --}}
        <div class="relative shrink-0">
          <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-full {{ $roleInfo['bg'] }}
                      flex items-center justify-center ring-2 ring-white/10">
            <span class="font-display text-3xl lg:text-4xl font-black text-white leading-none">
              {{ $initial }}
            </span>
          </div>
          <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-pac-green-500 rounded-full border-2 border-pac-black-900"></span>
        </div>

        {{-- 사용자 정보 --}}
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            <span class="font-display text-[9px] font-bold uppercase tracking-[0.25em] {{ $roleInfo['color'] }}">
              {{ $roleInfo['label'] }}
            </span>
            <span class="text-pac-black-700">·</span>
            <span class="font-display text-[9px] font-bold uppercase tracking-[0.25em] text-pac-black-500">
              {{ now()->format('Y.m.d') }}
            </span>
          </div>
          <h1 class="font-display text-3xl lg:text-4xl font-black text-white uppercase tracking-tight leading-none mb-2">
            {{ mb_strtoupper($nick) }}
          </h1>
          @if($stats['monthly_percent'] !== null)
            @if($stats['monthly_percent'] >= 100)
              <p class="font-body text-sm text-pac-green-500 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-pac-green-500 inline-block animate-pulse"></span>
                이번 달 목표를 달성했습니다!
              </p>
            @else
              <p class="font-body text-sm text-pac-black-400">
                이번 달 목표까지
                <span class="text-pac-yellow-400 font-bold">{{ $stats['monthly_percent'] }}%</span> 달성
              </p>
            @endif
          @else
            <p class="font-body text-sm text-pac-black-500">오늘도 함께 달려요!</p>
          @endif
        </div>

        {{-- CTA 버튼 --}}
        <div class="flex gap-2 shrink-0">
          <a href="{{ route('running-logs.create') }}"
             class="inline-flex items-center gap-2 px-5 py-2.5
                    bg-pac-yellow-500 hover:bg-pac-yellow-400
                    text-pac-black-900 font-display font-black text-sm uppercase tracking-wider
                    transition-colors duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            기록 추가
          </a>
          <a href="{{ route('profile.edit') }}"
             class="inline-flex items-center px-4 py-2.5
                    border border-white/10 hover:border-pac-yellow-500/50
                    text-pac-black-400 hover:text-pac-yellow-400
                    font-display font-bold text-sm uppercase tracking-wider
                    transition-colors duration-150">
            프로필
          </a>
        </div>
      </div>

      {{-- 수치 카드 4개 (하단 스트립) --}}
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-0 mt-6 border-t border-white/[0.06]">

        <div class="pt-4 pr-6 lg:border-r border-white/[0.06]">
          <p class="font-display text-[9px] font-bold uppercase tracking-[0.2em] text-pac-black-500 mb-1">
            이번 달 거리
          </p>
          <p class="font-display text-3xl lg:text-4xl font-black text-pac-yellow-400 leading-none">
            {{ number_format($stats['monthly_km'], 1) }}
            <span class="font-body text-sm font-normal text-pac-black-500">km</span>
          </p>
        </div>

        <div class="pt-4 pr-6 lg:pl-6 lg:border-r border-white/[0.06]">
          <p class="font-display text-[9px] font-bold uppercase tracking-[0.2em] text-pac-black-500 mb-1">
            누적 거리
          </p>
          <p class="font-display text-3xl lg:text-4xl font-black text-white leading-none">
            {{ number_format($stats['total_km'], 0) }}
            <span class="font-body text-sm font-normal text-pac-black-500">km</span>
          </p>
        </div>

        <div class="pt-4 pr-6 lg:pl-6 lg:border-r border-white/[0.06]">
          <p class="font-display text-[9px] font-bold uppercase tracking-[0.2em] text-pac-black-500 mb-1">
            이벤트 점수
          </p>
          @if($stats['has_active_event'])
            <p class="font-display text-3xl lg:text-4xl font-black text-pac-pink-400 leading-none">
              {{ $stats['event_score'] }}
              <span class="font-body text-sm font-normal text-pac-black-500">pts</span>
            </p>
          @else
            <p class="font-display text-3xl lg:text-4xl font-black text-pac-black-700 leading-none">—</p>
          @endif
        </div>

        <div class="pt-4 lg:pl-6">
          <p class="font-display text-[9px] font-bold uppercase tracking-[0.2em] text-pac-black-500 mb-1">
            조 순위
          </p>
          @if($stats['group_rank'])
            <p class="font-display text-3xl lg:text-4xl font-black text-white leading-none">
              {{ $stats['group_rank'] }}
              <span class="font-body text-sm font-normal text-pac-black-500">위</span>
            </p>
          @else
            <p class="font-display text-3xl lg:text-4xl font-black text-pac-black-700 leading-none">—</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════
       빠른 이동
       ══════════════════════════════════════════════ --}}
  <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
    @php
      $quickLinks = [
        ['href' => route('running-logs.index'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => '기록'],
        ['href' => route('events.index'),       'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => '이벤트'],
        ['href' => route('photos.index'),       'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => '포토'],
        ['href' => route('ranking.index'),      'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => '순위'],
        ['href' => route('notices.index'),      'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z', 'label' => '공지'],
        ['href' => route('bug-reports.index'),  'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'label' => '제보'],
      ];
    @endphp
    @foreach($quickLinks as $ql)
      <a href="{{ $ql['href'] }}"
         class="flex flex-col items-center gap-2 py-4 bg-pac-black-900 border border-white/[0.05]
                hover:border-pac-yellow-500/30 hover:bg-pac-black-800
                transition-all duration-150 group">
        <svg class="w-5 h-5 text-pac-black-500 group-hover:text-pac-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $ql['icon'] }}"/>
        </svg>
        <span class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-black-500 group-hover:text-pac-yellow-400 transition-colors">
          {{ $ql['label'] }}
        </span>
      </a>
    @endforeach
  </div>

  {{-- ══════════════════════════════════════════════
       공지 + 이벤트 / 마일리지 (2열)
       ══════════════════════════════════════════════ --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    {{-- 공지사항 --}}
    <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/[0.06]">
        <span class="font-display text-xs font-black text-white uppercase tracking-[0.15em]">공지사항</span>
        <a href="{{ route('notices.index') }}"
           class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-yellow-500 hover:text-pac-yellow-300 transition-colors">
          전체 →
        </a>
      </div>
      @forelse($notices as $notice)
        <div class="flex items-start gap-3 px-5 py-3.5 border-b border-white/[0.04] last:border-0
                    hover:bg-white/[0.02] transition-colors cursor-pointer group">
          @if($notice->is_pinned)
            <span class="mt-0.5 shrink-0 font-display text-[9px] font-black uppercase tracking-wider
                         bg-pac-pink-500 text-white px-1.5 py-0.5">고정</span>
          @else
            <span class="w-1 h-1 rounded-full bg-pac-yellow-500 shrink-0 mt-2"></span>
          @endif
          <p class="flex-1 font-body text-sm text-pac-black-300 group-hover:text-white transition-colors truncate">
            {{ $notice->title }}
          </p>
          <span class="font-display text-[9px] text-pac-black-600 shrink-0 tracking-wider">
            {{ $notice->created_at->format('m.d') }}
          </span>
        </div>
      @empty
        <div class="px-5 py-12 text-center">
          <p class="font-display text-2xl font-black text-pac-black-800 uppercase tracking-widest mb-1">NO NOTICE</p>
          <p class="font-body text-xs text-pac-black-600">공지사항이 없습니다</p>
        </div>
      @endforelse
    </div>

    {{-- 이벤트 / 마일리지 --}}
    <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/[0.06]">
        <span class="font-display text-xs font-black text-white uppercase tracking-[0.15em]">이벤트</span>
        <a href="{{ route('events.index') }}"
           class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-yellow-500 hover:text-pac-yellow-300 transition-colors">
          전체 →
        </a>
      </div>

      {{-- 마일리지 진행바 --}}
      @if($mileage)
        <div class="px-5 py-4 border-b border-white/[0.06]">
          <div class="flex items-center justify-between mb-2.5">
            <span class="font-body text-sm font-semibold text-pac-black-300 truncate max-w-[60%]">
              {{ $mileage['name'] }}
            </span>
            <span class="font-display text-sm font-black text-pac-yellow-400">
              {{ number_format($mileage['achieved'], 1) }}
              <span class="font-body text-xs font-normal text-pac-black-500">/ {{ $mileage['target'] }}km</span>
            </span>
          </div>
          <div class="h-2 bg-pac-black-700 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700"
                 style="width:{{ min(100, $mileage['percent']) }}%;
                        background:linear-gradient(90deg, #E5AD16, #E80043 {{ min(100, $mileage['percent']) > 80 ? '100%' : '300%' }});">
            </div>
          </div>
          <div class="flex items-center justify-between mt-2">
            @if($mileage['done'])
              <span class="font-display text-[9px] font-black uppercase tracking-wider text-pac-green-500">달성 완료!</span>
            @elseif($mileage['percent'] >= 80)
              <span class="font-display text-[9px] font-black uppercase tracking-wider text-pac-pink-400">거의 다 왔어요!</span>
            @else
              <span class="font-display text-[9px] text-pac-black-500 uppercase tracking-wider">D-{{ $mileage['days_left'] }}</span>
            @endif
            <span class="font-display text-sm font-black text-pac-yellow-400">{{ $mileage['percent'] }}%</span>
          </div>
        </div>
      @endif

      {{-- 이벤트 목록 --}}
      @forelse($events as $event)
        <a href="{{ route('events.show', $event) }}"
           class="flex items-center gap-3 px-5 py-3.5 border-b border-white/[0.04] last:border-0
                  hover:bg-white/[0.02] transition-colors group">
          <div class="flex-1 min-w-0">
            <p class="font-body text-sm font-semibold text-pac-black-300 group-hover:text-white transition-colors truncate">
              {{ $event->name }}
            </p>
            <p class="font-display text-[9px] text-pac-black-600 uppercase tracking-wider mt-0.5">
              ~ {{ \Carbon\Carbon::parse($event->end_date)->format('Y.m.d') }}
            </p>
          </div>
          @if($event->status === 'active')
            <span class="font-display text-[9px] font-black uppercase tracking-wider shrink-0
                         bg-pac-pink-500 text-white px-2 py-0.5 flex items-center gap-1">
              <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>LIVE
            </span>
          @else
            <span class="font-display text-[9px] font-black uppercase tracking-wider shrink-0
                         border border-pac-yellow-500/40 text-pac-yellow-600 px-2 py-0.5">예정</span>
          @endif
        </a>
      @empty
        <div class="px-5 py-12 text-center">
          <p class="font-display text-2xl font-black text-pac-black-800 uppercase tracking-widest mb-1">NO EVENT</p>
          <p class="font-body text-xs text-pac-black-600">진행 중인 이벤트가 없습니다</p>
        </div>
      @endforelse
    </div>
  </div>

  {{-- ══════════════════════════════════════════════
       최근 러닝 기록 타임라인
       ══════════════════════════════════════════════ --}}
  <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/[0.06]">
      <span class="font-display text-xs font-black text-white uppercase tracking-[0.15em]">최근 러닝 기록</span>
      <a href="{{ route('running-logs.index') }}"
         class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-yellow-500 hover:text-pac-yellow-300 transition-colors">
        전체 →
      </a>
    </div>

    @forelse($recentLogs as $log)
    <a href="{{ $log->is_confirmed ? route('running-logs.show', $log) : route('running-logs.edit', $log) }}"
       class="flex items-center gap-4 px-5 py-4 border-b border-white/[0.04] last:border-0
              hover:bg-white/[0.02] transition-colors group
              {{ !$log->is_confirmed ? 'border-l-2 border-l-pac-yellow-500/60' : '' }}">

      {{-- 날짜 블록 --}}
      <div class="shrink-0 w-12 h-12 bg-pac-black-800 border border-white/[0.06]
                  flex flex-col items-center justify-center group-hover:border-pac-yellow-500/30 transition-colors">
        <span class="font-display text-lg font-black text-pac-yellow-400 leading-none">
          {{ $log->run_date->format('d') }}
        </span>
        <span class="font-display text-[8px] text-pac-black-500 uppercase tracking-wider">
          {{ $log->run_date->format('M') }}
        </span>
      </div>

      {{-- 메인 데이터 --}}
      <div class="flex-1 min-w-0">
        <div class="flex items-baseline gap-2">
          <span class="font-display text-2xl font-black text-white leading-none group-hover:text-pac-yellow-400 transition-colors">
            {{ number_format($log->distance_km, 2) }}
          </span>
          <span class="font-body text-xs text-pac-black-500">km</span>
        </div>
        <p class="font-body text-xs text-pac-black-500 mt-0.5">
          {{ $log->avg_pace_formatted ? $log->avg_pace_formatted . '/km' : '' }}
          {{ $log->avg_pace_formatted && $log->duration_formatted ? ' · ' : '' }}
          {{ $log->duration_formatted ?? '' }}
        </p>
      </div>

      {{-- 태그 --}}
      <div class="shrink-0 flex flex-col items-end gap-1.5">
        @if(!$log->is_confirmed)
          <span class="font-display text-[9px] font-black uppercase tracking-wider
                       border border-pac-yellow-500/50 text-pac-yellow-500 px-1.5 py-0.5">
            검토 대기
          </span>
        @endif
        <span class="font-display text-[9px] font-black uppercase tracking-wider
                     {{ $log->is_indoor
                         ? 'bg-pac-black-700 text-pac-black-400'
                         : 'bg-pac-yellow-500/10 text-pac-yellow-500 border border-pac-yellow-500/20' }}
                     px-1.5 py-0.5">
          {{ $log->is_indoor ? '실내' : '야외' }}
        </span>
      </div>
    </a>
    @empty
    <div class="px-5 py-16 text-center">
      <p class="font-display text-4xl font-black text-pac-black-800 uppercase tracking-widest mb-2">NO RECORDS</p>
      <p class="font-body text-sm text-pac-black-600 mb-5">아직 러닝 기록이 없어요</p>
      <a href="{{ route('running-logs.create') }}"
         class="inline-flex items-center gap-2 px-6 py-2.5
                bg-pac-yellow-500 hover:bg-pac-yellow-400
                text-pac-black-900 font-display font-black text-sm uppercase tracking-wider
                transition-colors duration-150">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        첫 기록 추가하기
      </a>
    </div>
    @endforelse
  </div>

</div>
</x-app-layout>
