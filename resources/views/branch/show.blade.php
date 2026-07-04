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

  {{-- 하단 CTA --}}
  <div class="flex flex-col sm:flex-row items-start gap-3 pt-2">
    <a href="{{ route('apply') }}" class="pac-btn">크루 합류하기 →</a>
    <a href="{{ route('branch') }}" class="pac-btn-ghost">다른 지부 보기</a>
  </div>

</div>
</x-app-layout>
