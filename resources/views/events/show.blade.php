<x-app-layout>
@php
  $user  = auth()->user();
  $thumb = $event->thumbnail_url
    ? (str_starts_with($event->thumbnail_url, 'http')
        ? $event->thumbnail_url
        : \Storage::disk('s3')->url($event->thumbnail_url))
    : null;

  $now = now();

  // 날짜 기반 세분화 상태 (스케줄러 lag 방어 — status 보조 판별만)
  $isBeforeRecruit  = $event->recruit_start_at && $now->lt($event->recruit_start_at);
  $isAfterRecruit   = $event->recruit_end_at   && $now->gt($event->recruit_end_at);
  $daysUntilOpen    = $isBeforeRecruit ? (int) ceil($now->floatDiffInHours($event->recruit_start_at) / 24) : 0;
  $hoursUntilOpen   = $isBeforeRecruit ? (int) ceil($now->floatDiffInHours($event->recruit_start_at))      : 0;
  $daysLeftToClose  = ($event->recruit_end_at && !$isAfterRecruit)
                        ? (int) floor($now->floatDiffInHours($event->recruit_end_at) / 24)
                        : null;
@endphp

<div class="max-w-3xl mx-auto px-4 py-5 md:px-6 lg:px-8 space-y-5">

  {{-- 썸네일 히어로 --}}
  @if($thumb)
  <div class="relative rounded-2xl overflow-hidden h-56 md:h-72">
    <img src="{{ $thumb }}" alt="{{ $event->name }}" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
    <div class="absolute bottom-0 left-0 p-6">
      <span class="inline-flex items-center gap-1.5 mb-2
        {{ $event->status === 'active' ? 'bg-pac-pink-500 text-white' : ($event->status === 'upcoming' ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-white/20 text-white') }}
        text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full">
        @if($event->status === 'active') <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> LIVE
        @elseif($event->status === 'upcoming') 모집 예정
        @else 종료 @endif
      </span>
      <h1 class="font-display text-2xl md:text-3xl font-bold text-white uppercase tracking-tight leading-tight">
        {{ $event->name }}
      </h1>
    </div>
  </div>
  @else
  {{-- 썸네일 없는 헤더 --}}
  <div>
    <div class="flex items-center gap-2 mb-2">
      <span class="font-display text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full
        {{ $event->status === 'active' ? 'bg-pac-pink-500 text-white' : ($event->status === 'upcoming' ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-pac-black-100 text-pac-black-500') }}">
        {{ $event->status === 'active' ? 'LIVE' : ($event->status === 'upcoming' ? '예정' : '종료') }}
      </span>
      <span class="font-display text-[10px] font-bold text-pac-yellow-500 uppercase tracking-widest">B TYPE</span>
    </div>
    <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">{{ $event->name }}</h1>
  </div>
  @endif

  {{-- 플래시 --}}
  @foreach(['success' => 'green', 'error' => 'red'] as $key => $color)
  @if(session($key))
  <div class="flex items-center gap-3 px-4 py-3 bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-xl text-sm text-{{ $color }}-700">
    {{ session($key) }}
  </div>
  @endif
  @endforeach

  {{-- 이벤트 정보 카드 --}}
  <div class="bg-white rounded-2xl shadow-sm divide-y divide-pac-black-100">
    <div class="px-6 py-4 grid grid-cols-2 gap-4">

      @if($event->location)
      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">장소</p>
        <p class="font-body text-sm text-pac-black-900">{{ $event->location }}</p>
      </div>
      @endif

      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">이벤트 기간</p>
        <p class="font-body text-sm text-pac-black-900">
          {{ $event->start_date->format('Y.m.d') }}
          @if($event->start_date->ne($event->end_date)) ~ {{ $event->end_date->format('Y.m.d') }} @endif
        </p>
      </div>

      @if($event->recruit_start_at || $event->recruit_end_at)
      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">모집 기간</p>
        <p class="font-body text-sm text-pac-black-900">
          {{ $event->recruit_start_at?->format('Y.m.d') ?? '—' }}
          ~ {{ $event->recruit_end_at?->format('Y.m.d') ?? '—' }}
        </p>
      </div>
      @endif

      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">신청 현황</p>
        <p class="font-body text-sm text-pac-black-900">
          {{ $participants->count() }}명
          @if($event->max_participants) / 최대 {{ $event->max_participants }}명 @endif
        </p>
      </div>

      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">참여 대상</p>
        <p class="font-body text-sm text-pac-black-900">
          {{ $event->target_scope === 'all' ? '전체 회원' : ($event->target_scope === 'generation' ? $event->generation.'기' : '지부') }}
        </p>
      </div>

    </div>
  </div>

  {{-- 이벤트 설명 --}}
  @if($event->description)
  <div class="bg-white rounded-2xl shadow-sm p-6">
    <h2 class="font-display text-sm font-bold text-pac-black-900 uppercase tracking-widest mb-3">이벤트 소개</h2>
    <div class="prose prose-sm max-w-none text-pac-black-800">
      {!! $event->description !!}
    </div>
  </div>
  @endif

  {{-- 참가 신청 영역 --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 bg-pac-black-900 flex items-center justify-between">
      <h2 class="font-display text-sm font-bold text-white uppercase tracking-widest">참가 신청</h2>
      {{-- 모집 중일 때만 마감 임박 배지 표시 --}}
      @if($daysLeftToClose !== null && $daysLeftToClose <= 3 && !$isBeforeRecruit)
        <span class="font-display text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full
          {{ $daysLeftToClose === 0 ? 'bg-pac-pink-500 text-white animate-pulse' : 'bg-pac-yellow-500 text-pac-black-900' }}">
          {{ $daysLeftToClose === 0 ? '오늘 마감' : "D-{$daysLeftToClose}" }}
        </span>
      @endif
    </div>

    @if($alreadyRegistered)
      {{-- ① 이미 신청 완료 --}}
      <div class="p-6 text-center space-y-4">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mx-auto">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <p class="font-body text-sm font-semibold text-pac-black-900">참가 신청이 완료되었습니다.</p>
        <p class="font-body text-xs text-pac-black-400">취소는 대시보드 > 내 이벤트에서 가능합니다.</p>
        @if($event->status !== 'ended')
        <form method="POST" action="{{ route('events.cancel', $event) }}" class="inline-block"
              onsubmit="return confirm('정말 신청을 취소하시겠습니까?')">
          @csrf
          <button type="submit"
                  class="font-body text-xs text-pac-black-400 hover:text-pac-pink-500 underline transition-colors">
            신청 취소
          </button>
        </form>
        @endif
      </div>

    @elseif(!auth()->check())
      {{-- ② 비로그인 --}}
      <div class="p-6 text-center space-y-3">
        <p class="font-body text-sm text-pac-black-600">참가 신청은 로그인 후 이용하실 수 있습니다.</p>
        <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}"
           class="inline-flex items-center gap-2 bg-pac-yellow-500 hover:bg-pac-yellow-400
                  text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                  px-6 py-2.5 rounded-xl transition-colors">
          로그인 후 신청하기
        </a>
      </div>

    @elseif(!$eligible)
      {{-- ③ 참여 대상 아님 --}}
      <div class="p-6 text-center">
        <p class="font-body text-sm text-pac-black-500">이 이벤트는 참여 대상이 아닙니다.</p>
      </div>

    @elseif($event->status === 'ended' || ($event->end_date && $now->gt($event->end_date->endOfDay())))
      {{-- ④ 이벤트 종료 --}}
      <div class="p-6 text-center space-y-1">
        <p class="font-body text-sm font-semibold text-pac-black-400">이미 종료된 이벤트입니다.</p>
        @if($event->end_date)
        <p class="font-body text-xs text-pac-black-300">종료일: {{ $event->end_date->format('Y년 m월 d일') }}</p>
        @endif
      </div>

    @elseif($isBeforeRecruit)
      {{-- ⑤ 모집 시작 전 --}}
      <div class="p-6 text-center space-y-4">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pac-yellow-100 mx-auto">
          <svg class="w-5 h-5 text-pac-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="font-body text-sm font-semibold text-pac-black-800">아직 모집이 시작되지 않았습니다.</p>
          <p class="font-body text-sm text-pac-black-500 mt-1.5">
            <span class="font-semibold text-pac-yellow-700">
              {{ $event->recruit_start_at->format('Y년 m월 d일 H:i') }}
            </span>
            부터 신청 가능합니다.
          </p>
        </div>
        <div class="inline-flex items-center gap-2 bg-pac-yellow-50 border border-pac-yellow-200 rounded-xl px-5 py-2.5">
          @if($hoursUntilOpen < 24)
            <span class="font-display text-xl font-bold text-pac-yellow-700">{{ $hoursUntilOpen }}시간 후</span>
            <span class="font-body text-xs text-pac-yellow-600">오픈 예정</span>
          @elseif($daysUntilOpen === 1)
            <span class="font-display text-xl font-bold text-pac-yellow-700">내일 오픈</span>
          @else
            <span class="font-display text-3xl font-bold text-pac-yellow-700">D-{{ $daysUntilOpen }}</span>
          @endif
        </div>
        @if($event->recruit_end_at)
        <p class="font-body text-xs text-pac-black-400">
          모집 기간: {{ $event->recruit_start_at->format('m.d') }} ~ {{ $event->recruit_end_at->format('m.d') }}
        </p>
        @endif
      </div>

    @elseif(!$event->is_registration_open)
      {{-- ⑥ 관리자 수동 비활성화 --}}
      <div class="p-6 text-center space-y-1">
        <p class="font-body text-sm font-semibold text-pac-black-500">현재 신청이 비활성화되어 있습니다.</p>
        <p class="font-body text-xs text-pac-black-400">이벤트 담당자에게 문의해 주세요.</p>
      </div>

    @elseif($isAfterRecruit)
      {{-- ⑦ 모집 기간 종료 --}}
      <div class="p-6 text-center space-y-1">
        <p class="font-body text-sm font-semibold text-pac-black-500">신청이 마감되었습니다.</p>
        @if($event->recruit_end_at)
        <p class="font-body text-xs text-pac-black-400">
          마감일: {{ $event->recruit_end_at->format('Y년 m월 d일 H:i') }}
        </p>
        @endif
      </div>

    @elseif($event->isCapacityFull())
      {{-- ⑧ 정원 초과 --}}
      <div class="p-6 text-center space-y-1">
        <p class="font-body text-sm font-semibold text-pac-black-500">정원이 초과되었습니다.</p>
        <p class="font-body text-xs text-pac-black-400">현재 {{ $participants->count() }}명 / 최대 {{ $event->max_participants }}명</p>
      </div>

    @else
      {{-- ⑨ 신청 가능 — 폼 표시 --}}
      <form method="POST" action="{{ route('events.register', $event) }}"
            class="p-6 space-y-4" enctype="multipart/form-data">
        @csrf

        {{-- 마감 임박 경고 --}}
        @if($daysLeftToClose !== null && $daysLeftToClose <= 3)
        <div class="flex items-center gap-2 px-4 py-2.5 bg-pac-pink-50 border border-pac-pink-200 rounded-xl text-xs font-body text-pac-pink-700">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          </svg>
          {{ $daysLeftToClose === 0 ? '오늘이 신청 마감일입니다.' : "신청 마감 D-{$daysLeftToClose} — {$event->recruit_end_at->format('m월 d일 H:i')} 마감" }}
        </div>
        @endif

        @foreach($event->form_schema ?? [] as $field)
        @php
          $key      = $field['data']['key']      ?? $field['key']      ?? null;
          $label    = $field['data']['label']    ?? $field['label']    ?? $key;
          $type     = $field['type']             ?? 'text';
          $required = $field['data']['required'] ?? $field['required'] ?? false;
          $options  = $field['data']['options']  ?? [];
          $name     = "field_{$key}";
        @endphp
        @if(!$key) @continue @endif

        <div>
          <label class="block font-display text-xs font-bold text-pac-black-700 uppercase tracking-wide mb-1.5">
            {{ $label }}
            @if($required) <span class="text-pac-pink-500 ml-0.5">*</span> @endif
          </label>

          @if($type === 'textarea')
            <textarea name="{{ $name }}" rows="3" {{ $required ? 'required' : '' }}
                      class="w-full rounded-xl border border-pac-black-200 px-3 py-2 text-sm font-body focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">{{ old($name) }}</textarea>

          @elseif($type === 'radio')
            <div class="space-y-2">
              @foreach($options as $opt)
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="{{ $name }}" value="{{ $opt['value'] ?? $opt }}"
                       {{ $required ? 'required' : '' }} class="text-pac-yellow-500 focus:ring-pac-yellow-400">
                <span class="font-body text-sm text-pac-black-800">{{ $opt['value'] ?? $opt }}</span>
              </label>
              @endforeach
            </div>

          @elseif($type === 'checkbox')
            <div class="space-y-2">
              @foreach($options as $opt)
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $opt['value'] ?? $opt }}"
                       class="text-pac-yellow-500 focus:ring-pac-yellow-400 rounded">
                <span class="font-body text-sm text-pac-black-800">{{ $opt['value'] ?? $opt }}</span>
              </label>
              @endforeach
            </div>

          @elseif($type === 'image')
            <input type="file" name="{{ $name }}" accept="image/*" {{ $required ? 'required' : '' }}
                   class="w-full text-sm font-body text-pac-black-600 file:mr-3 file:py-2 file:px-4
                          file:rounded-lg file:border-0 file:bg-pac-yellow-500 file:text-pac-black-900
                          file:font-bold file:text-xs file:uppercase file:cursor-pointer">

          @else
            <input type="text" name="{{ $name }}" value="{{ old($name) }}"
                   {{ $required ? 'required' : '' }}
                   @if($key === 'phone') inputmode="tel" placeholder="010-0000-0000" @endif
                   class="w-full rounded-xl border border-pac-black-200 px-3 py-2 text-sm font-body
                          focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">
          @endif

          @error($name)
          <p class="mt-1 text-xs text-pac-pink-500 font-body">{{ $message }}</p>
          @enderror
        </div>
        @endforeach

        <button type="submit"
                class="w-full py-3 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900
                       font-display font-bold text-sm uppercase tracking-wide rounded-xl
                       transition-colors duration-200">
          참가 신청하기
        </button>
      </form>
    @endif
  </div>

  {{-- 참여자 명단 (마스킹) --}}
  @if($participants->isNotEmpty())
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 bg-pac-black-900 flex items-center justify-between">
      <h2 class="font-display text-sm font-bold text-white uppercase tracking-widest">참여자 명단</h2>
      <span class="font-body text-xs text-pac-black-400">{{ $participants->count() }}명</span>
    </div>
    <div class="p-5">
      <div class="flex flex-wrap gap-2">
        @foreach($participants as $name)
        <span class="inline-block bg-pac-black-50 border border-pac-black-100 text-pac-black-600
                     font-body text-xs px-3 py-1.5 rounded-lg">
          {{ $name }}
        </span>
        @endforeach
      </div>
    </div>
  </div>
  @endif

</div>
</x-app-layout>
