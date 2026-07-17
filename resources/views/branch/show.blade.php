<x-app-layout>
@php
  $image = $branch->public_image_url;
@endphp

<div class="max-w-3xl mx-auto px-4 py-5 md:px-6 lg:px-8 space-y-5">

  {{-- 뒤로가기 --}}
  <a href="{{ route('branch') }}"
     class="inline-flex items-center gap-1.5 font-body text-xs text-pac-black-400 hover:text-pac-yellow-500 transition-colors">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    지부 목록
  </a>

  {{-- 대표 이미지 히어로 --}}
  @if($image)
  <x-event-thumbnail :url="$image" :alt="$branch->name" class="rounded-2xl h-56 md:h-72">
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent pointer-events-none z-[2]"></div>
    <div class="absolute bottom-0 left-0 p-6 z-[3]">
      <span class="inline-flex items-center gap-1.5 mb-2 bg-pac-yellow-500 text-pac-black-900 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full">
        BRANCH
      </span>
      <h1 class="font-display text-2xl md:text-3xl font-bold text-white uppercase tracking-tight leading-tight">
        {{ $branch->name }}
      </h1>
    </div>
  </x-event-thumbnail>
  @else
  <div>
    <div class="flex items-center gap-2 mb-2">
      <span class="font-display text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full bg-pac-yellow-500 text-pac-black-900">
        BRANCH
      </span>
      <span class="font-display text-[10px] font-bold text-pac-yellow-500 uppercase tracking-widest">활성</span>
    </div>
    <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">{{ $branch->name }}</h1>
  </div>
  @endif

  {{-- 기본 정보 --}}
  <div class="bg-white rounded-2xl shadow-sm divide-y divide-pac-black-100">
    <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">지부/지역명</p>
        <p class="font-body text-sm text-pac-black-900">{{ $branch->name }}</p>
      </div>

      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">상태</p>
        <p class="font-body text-sm text-pac-black-900">활성</p>
      </div>

      @if($branch->admin)
      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">지부 관리자</p>
        <p class="font-body text-sm text-pac-black-900">{{ $branch->admin->nickname ?? $branch->admin->name }}</p>
      </div>
      @endif

      @if($branch->operator)
      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">지부 운영자</p>
        <p class="font-body text-sm text-pac-black-900">{{ $branch->operator->nickname ?? $branch->operator->name }}</p>
      </div>
      @endif

      <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">등록일</p>
        <p class="font-body text-sm text-pac-black-900">{{ $branch->created_at->format('Y.m.d') }}</p>
      </div>
    </div>
  </div>

  {{-- 지부 소개 --}}
  <div class="bg-white rounded-2xl shadow-sm p-6">
    <h2 class="font-display text-sm font-bold text-pac-black-900 uppercase tracking-widest mb-3">지부 소개</h2>
    @if($branch->branch_desc)
      <p class="font-body text-sm text-pac-black-800 leading-relaxed whitespace-pre-line">{{ $branch->branch_desc }}</p>
    @else
      <p class="font-body text-sm text-pac-black-500 italic">소개 준비 중입니다.</p>
    @endif
  </div>

  {{-- 기수 구성원 (기수별 활동 지부 스냅샷) --}}
  <div class="bg-white rounded-2xl shadow-sm p-6">
    <div class="flex flex-wrap items-baseline justify-between gap-3 mb-3">
      <h2 class="font-display text-sm font-bold text-pac-black-900 uppercase tracking-widest">구성원</h2>
      <p class="font-body text-xs text-pac-black-500">{{ $memberCount }}명</p>
    </div>

    <form method="GET" action="{{ route('branch.show', $branch) }}" class="mb-4">
      <label for="generation" class="block font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1.5">기수</label>
      <div class="flex flex-wrap gap-2 items-center">
        <select id="generation" name="generation"
                class="font-body text-sm border border-pac-black-100 rounded-lg px-3 py-2 bg-white min-w-[12rem]"
                onchange="this.form.submit()">
          <option value="" @selected(!$usingPastFilter)>
            현재
            @if($visibleGenerations->isNotEmpty())
              ({{ $visibleGenerations->map(fn ($g) => $g->alias ? "{$g->number}기" : "{$g->number}기")->implode(', ') }})
            @else
              (모집·운영 기수 없음)
            @endif
          </option>
          @foreach($pastGenerations as $g)
            <option value="{{ $g->id }}" @selected($usingPastFilter && $selectedGeneration?->id === $g->id)>
              {{ $g->alias ? "{$g->number}기 — {$g->alias}" : "{$g->number}기" }}
              @if($g->status === 'ended') (종료) @endif
            </option>
          @endforeach
        </select>
        @if($usingPastFilter)
          <a href="{{ route('branch.show', $branch) }}" class="font-body text-xs text-pac-black-500 hover:text-pac-yellow-600 underline">현재로</a>
        @endif
      </div>
      @if($usingPastFilter && $selectedGeneration)
        <p class="font-body text-xs text-pac-black-500 mt-2">
          {{ $selectedGeneration->alias ? "{$selectedGeneration->number}기 — {$selectedGeneration->alias}" : "{$selectedGeneration->number}기" }}
          당시 이 지부 활동 인원입니다.
        </p>
      @endif
    </form>

    @if($memberCount === 0)
      <p class="font-body text-sm text-pac-black-500 italic">
        @if($usingPastFilter)
          선택한 기수에 이 지부로 기록된 구성원이 없습니다.
        @else
          현재 모집·운영 기수에 이 지부로 편성된 신청자가 없습니다.
          <span class="block mt-2 text-pac-black-400 not-italic">
            관리자: 신청 내역에서「기수 입단 이관」으로 기수·지부를 지정하면 여기에 표시됩니다.
            (회원가입·회원 연결은 필수가 아닙니다.)
          </span>
        @endif
      </p>
    @elseif(!$showRoster)
      <p class="font-body text-sm text-pac-black-600 leading-relaxed">
        구성원 닉네임은 로그인 후 확인할 수 있습니다.
      </p>
      <a href="{{ route('login') }}" class="inline-block mt-4 font-body text-sm text-pac-yellow-600 hover:underline">로그인하기</a>
    @else
      <ul class="flex flex-wrap gap-2">
        @foreach($nicknames as $nickname)
          <li class="font-body text-sm text-pac-black-800 px-2.5 py-1 rounded-lg bg-pac-black-50">{{ $nickname }}</li>
        @endforeach
      </ul>
    @endif
  </div>

  {{-- 하단 CTA --}}
  <div class="flex flex-col sm:flex-row items-start gap-3 pt-2">
    <a href="{{ route('apply') }}" class="pac-btn">크루 합류하기 →</a>
    <a href="{{ route('branch') }}" class="pac-btn-ghost">다른 지부 보기</a>
  </div>

</div>
</x-app-layout>
